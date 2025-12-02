

document.addEventListener('DOMContentLoaded', function () {
    const roomIdInput = document.getElementById('room-id');
    if (!roomIdInput) return; // Not on chat page

    const roomId = roomIdInput.value;
    const messagesBox = document.getElementById('messages-box');
    const inputMessage = document.getElementById('input-message');
    const emojiTrigger = document.querySelector('.bi-emoji-smile');
    const fileTrigger = document.querySelector('.bi-paperclip');
    const sendBtn = document.querySelector('.btn-primary.rounded-circle');

    // --- EMOJI PICKER ---
    if (window.EmojiButton && emojiTrigger) {
        const picker = new EmojiButton({
            position: 'top-start',
            zIndex: 1000
        });

        picker.on('emoji', selection => {
            inputMessage.value += selection.emoji;
            inputMessage.focus();
            autoResizeInput(inputMessage);
        });

        emojiTrigger.addEventListener('click', () => picker.togglePicker(emojiTrigger));
    }

    // --- FILE ATTACHMENT ---
    if (fileTrigger) {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.style.display = 'none';
        document.body.appendChild(fileInput);

        fileTrigger.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            if (!confirm(`Kirim file "${file.name}"?`)) {
                fileInput.value = '';
                return;
            }

            alert('Fitur upload file sedang dalam pengembangan di sisi backend.');
            fileInput.value = '';
        });
    }

    // --- SCROLL TO BOTTOM ---
    function scrollToBottom() {
        if (messagesBox) {
            messagesBox.scrollTop = messagesBox.scrollHeight;
        }
    }
    scrollToBottom();

    // --- REALTIME LISTENER (ECHO) ---
    if (window.Echo) {
        console.log(`Subscribing to chat-room.${roomId}`);
        window.Echo.private(`chat-room.${roomId}`)
            .listen('.new-message', (e) => {
                console.log('New message received:', e);
                appendMessage(e.message);
            });
    } else {
        // console.log('Laravel Echo is not initialized (Long Polling Mode).');
    }

    function appendMessage(msg) {
        const isMe = msg.sender_type !== 'customer';
        const align = isMe ? 'outgoing' : 'incoming';
        const bubble = isMe ? 'out' : 'in';

        // Format time
        const date = new Date(msg.created_at);
        const time = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const check = isMe ? `<i class="bi bi-check2-all ${msg.status === 'read' ? 'text-primary' : 'text-secondary'} ms-1"></i>` : '';

        const html = `
            <div class="msg-row ${align}">
                <div class="msg-bubble ${bubble}">
                    ${msg.message_content.replace(/\n/g, '<br>')}
                    <div class="msg-time">${time} ${check}</div>
                </div>
            </div>
        `;

        messagesBox.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // --- SEND MESSAGE ---
    async function sendMessage() {
        const text = inputMessage.value.trim();
        if (!text) return;

        // Optimistic UI
        const tempId = Date.now();
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const tempHtml = `
            <div class="msg-row outgoing" id="msg-${tempId}">
                <div class="msg-bubble out opacity-75">
                    ${text.replace(/\n/g, '<br>')}
                    <div class="msg-time">${time} <i class="bi bi-clock ms-1"></i></div>
                </div>
            </div>
        `;
        messagesBox.insertAdjacentHTML('beforeend', tempHtml);
        scrollToBottom();

        inputMessage.value = '';
        inputMessage.style.height = 'auto';

        try {
            const response = await window.axios.post(`/chat/room/${roomId}/send-ajax`, {
                message_body: text
            });

            if (response.data.success) {
                // Remove temp message and append real one (or just update status)
                // For simplicity, we remove temp and let Echo or manual append handle it
                // But Echo might be faster or slower. 
                // If we rely on Echo, we don't need to do anything else if the backend broadcasts the message we just sent.
                // Usually backend broadcasts ALL messages including ours.
                // If so, we might get a duplicate if we don't handle it.
                // But for now, let's just remove the temp message when we get the real one via Echo?
                // Or just keep the temp one and update it?

                // Let's remove the temp one to avoid duplication when Echo event arrives
                document.getElementById(`msg-${tempId}`).remove();

                // If Echo is working, the message will appear via event. 
                // If not, we might want to manually append it from response.data.message
                if (!window.Echo) {
                    appendMessage(response.data.message);
                }
            }
        } catch (error) {
            console.error(error);
            alert('Gagal mengirim pesan');
            const tempMsg = document.getElementById(`msg-${tempId}`);
            if (tempMsg) tempMsg.remove();
            inputMessage.value = text;
        }
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }

    // --- AUTO RESIZE & ENTER TO SEND ---
    if (inputMessage) {
        inputMessage.addEventListener('input', function () {
            autoResizeInput(this);
        });

        inputMessage.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    function autoResizeInput(element) {
        element.style.height = 'auto';
        element.style.height = (element.scrollHeight) + 'px';
    }
});
