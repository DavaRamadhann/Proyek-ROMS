<?php

namespace App\Domains\Chat\Http\Controllers;

use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Interfaces\ChatMessageRepositoryInterface;
use App\Domains\Chat\Services\ChatService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $chatService;
    protected $chatRoomRepo;
    protected $chatMessageRepo;
    protected $contactRepo;

    public function __construct(
        ChatService $chatService,
        ChatRoomRepositoryInterface $chatRoomRepo,
        ChatMessageRepositoryInterface $chatMessageRepo,
        \App\Domains\Chat\Interfaces\ChatContactRepositoryInterface $contactRepo
    ) {
        $this->chatService = $chatService;
        $this->chatRoomRepo = $chatRoomRepo;
        $this->chatMessageRepo = $chatMessageRepo;
        $this->contactRepo = $contactRepo;
    }

    /**
     * Dashboard Statistik CS
     * URL: /app/cs/dashboard
     */
    public function index()
    {
        $csUser = Auth::user();
        $rooms = $this->chatRoomRepo->getRoomsForCs($csUser->id);

        // Menggunakan view baru dari folder tampilan
        return view('pages.chat.dashboard', compact('rooms'));
    }

    /**
     * [HALAMAN KONEKSI] Menampilkan QR Code
     * URL: /app/chat/connect
     */
    public function showConnectionPage()
    {
        // Menggunakan view lama yang sudah dimodifikasi isinya menjadi khusus QR
        return view('pages.chat.whatsapp'); 
    }

    /**
     * [HALAMAN APLIKASI] UI Chat 3 Kolom (Unified Dashboard)
     * URL: /app/chat/ui
     */
    public function showChatUI(Request $request)
    {
        $csUser = Auth::user();
        
        // Cek Status WA (untuk keamanan - redirect jika belum terkoneksi)
        $isConnected = false;
        try {
            $waUrl = rtrim(config('services.whatsapp.url'), '/');
            $apiKey = config('services.whatsapp.api_key');
            $clientId = 'official_business';

            if ($waUrl && $apiKey) {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'Accept' => 'application/json',
                ])->timeout(2)->get("{$waUrl}/accounts");

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    $account = collect($data)->firstWhere('clientId', $clientId);
                    if ($account && in_array(($account['status'] ?? ''), ['CONNECTED', 'READY'])) {
                        $isConnected = true;
                    }
                }
            }
        } catch (\Exception $e) {
            // Connection check failed
        }

        // REDIRECT jika WhatsApp belum terkoneksi
        if (!$isConnected) {
            return redirect()->route('whatsapp.scan')
                ->with('warning', 'WhatsApp belum terhubung. Silakan scan QR code terlebih dahulu.');
        }
        
        // Ambil room untuk sidebar kiri - filter berdasarkan role
        // Include CS user info for display
        $query = \App\Domains\Chat\Models\ChatRoom::with(['customer', 'chatContact', 'latestMessage', 'csUser']);
        
        // Jika bukan admin, hanya tampilkan room yang di-assign ke CS ini
        if ($csUser->role !== 'admin') {
            $query->where('cs_user_id', $csUser->id);
        }

        // [FILTER] Hanya tampilkan nomor Indonesia (62...)
        // Ini otomatis memfilter grup WA (biasanya id panjang/beda) atau nomor luar
        $query->where(function($q) {
            $q->whereHas('customer', function($sq) {
                $sq->where('phone', 'like', '62%');
            })->orWhereHas('chatContact', function($sq) {
                $sq->where('phone', 'like', '62%');
            });
        });
        
        $rooms = $query->orderByDesc(
                        \App\Domains\Chat\Models\ChatMessage::select('created_at')
                            ->whereColumn('chat_room_id', 'chat_rooms.id')
                            ->latest()
                            ->limit(1)
                    )
                    ->orderBy('updated_at', 'desc')
                    ->get();

        // Auto Open Room Logic
        $autoOpenRoomId = null;
        if ($request->has('room')) {
            $autoOpenRoomId = $request->room;
        } elseif ($request->has('customer_id')) {
            $customerRoom = $rooms->firstWhere('customer_id', $request->customer_id);
            if ($customerRoom) {
                $autoOpenRoomId = $customerRoom->id;
            } else {
                // If room doesn't exist in the list (maybe new customer or pagination limit?), try to find or create it
                // For now, let's assume it should be in the list or we just ignore it if not found to avoid complexity
                // Alternatively, we can use findOrCreateRoomForCustomer here if we want to be robust
                try {
                     $room = $this->chatRoomRepo->findOrCreateRoomForCustomer($request->customer_id);
                     $autoOpenRoomId = $room->id;
                     // If it wasn't in $rooms, we might need to re-fetch or push it, but for now let's rely on it being there or just opening if ID is known
                } catch (\Exception $e) {
                    // Ignore
                }
            }
        }

        // Get all CS users for reassign modal (admin only)
        $allCsUsers = [];
        if ($csUser->role === 'admin') {
            $allCsUsers = \App\Models\User::where('role', 'cs')
                ->orderBy('name')
                ->get();
        }

        return view('pages.chat.ui', compact('rooms', 'isConnected', 'autoOpenRoomId', 'allCsUsers'));
    }

    public function getRooms(Request $request)
    {
        $clientLastUpdated = $request->query('last_updated_at', 0);
        $clientLastGlobalMsgId = $request->query('last_global_msg_id', 0);
        
        $startTime = time();
        $currentUser = Auth::user();

        // [OPTIMISASI] Tutup sesi agar tidak memblokir request lain (AJAX send message, dll)
        session_write_close();
        
        // 1. Cek timestamp terbaru di DB (per user untuk CS, global untuk admin)
        $latestRoomQuery = \App\Domains\Chat\Models\ChatRoom::query();
        if ($currentUser->role !== 'admin') {
            $latestRoomQuery->where('cs_user_id', $currentUser->id);
        }
        $latestRoom = $latestRoomQuery->latest('updated_at')->first();
        $serverLastUpdated = $latestRoom ? $latestRoom->updated_at->timestamp : 0;

        // 1b. Cek ID pesan terakhir secara global (lebih akurat daripada timestamp detik)
        $serverLastGlobalMsgId = \App\Domains\Chat\Models\ChatMessage::max('id') ?? 0;

        // 2. Jika ada update baru (timestamp berubah ATAU ada pesan baru)
        if ($serverLastUpdated > $clientLastUpdated || $serverLastGlobalMsgId > $clientLastGlobalMsgId) {
            $query = \App\Domains\Chat\Models\ChatRoom::with(['customer', 'chatContact', 'latestMessage', 'csUser']);
            
            // Filter berdasarkan role
            if ($currentUser->role !== 'admin') {
                $query->where('cs_user_id', $currentUser->id);
            }

            // [FILTER] Hanya tampilkan nomor Indonesia (62...)
            $query->where(function($q) {
                $q->whereHas('customer', function($sq) {
                    $sq->where('phone', 'like', '62%');
                })->orWhereHas('chatContact', function($sq) {
                    $sq->where('phone', 'like', '62%');
                });
            });
            
            $rooms = $query->orderByDesc(
                            \App\Domains\Chat\Models\ChatMessage::select('created_at')
                                ->whereColumn('chat_room_id', 'chat_rooms.id')
                                ->latest()
                                ->limit(1)
                        )
                        ->orderBy('updated_at', 'desc')
                        ->get()
                        ->map(function($room) {
                            // Hitung pesan belum dibaca
                            $unreadCount = $room->messages()
                                ->where('sender_type', 'customer')
                                ->where('status', '!=', 'read')
                                ->count();

                            return [
                                'id' => $room->id,
                                'updated_at' => $room->updated_at,
                                'updated_ts' => $room->updated_at->timestamp, // Kirim timestamp
                                'status' => $room->status,
                                'unread_count' => $unreadCount, // Tambahkan ini
                                'customer' => $room->customer ? [
                                    'id' => $room->customer->id,
                                    'name' => $room->customer->name,
                                    'phone' => $room->customer->phone,
                                    'is_contact' => false
                                ] : ($room->chatContact ? [
                                    'id' => $room->chatContact->id,
                                    'name' => $room->chatContact->name ?? $room->chatContact->phone,
                                    'phone' => $room->chatContact->phone,
                                    'is_contact' => true
                                ] : null),
                                'latest_message' => $room->latestMessage ? [
                                    'message_content' => $room->latestMessage->message_content,
                                    'created_at' => $room->latestMessage->created_at,
                                ] : null,
                                'cs_user' => $room->csUser ? [
                                    'id' => $room->csUser->id,
                                    'name' => $room->csUser->name,
                                    'email' => $room->csUser->email,
                                ] : null,
                            ];
                        });

            return response()->json([
                'rooms' => $rooms,
                'last_updated_at' => $serverLastUpdated,
                'last_global_msg_id' => $serverLastGlobalMsgId
            ]);
        }

        // 3. Jika tidak ada update, return kosong (Short Polling)
        return response()->json(['status' => 'no_update']);
    }

    public function getRoomData(Request $request, int $roomId)
    {
        $clientLastMsgId = $request->query('last_message_id', 0);
        $startTime = time();
        
        // Jika request biasa (bukan polling), tidak perlu loop
        $isPolling = $request->has('polling');



        // [OPTIMISASI] Tutup sesi agar tidak memblokir request lain
        session_write_close();

        $room = \App\Domains\Chat\Models\ChatRoom::with(['customer', 'chatContact'])->find($roomId);
        if (!$room) return response()->json(['error' => 'Room not found'], 404);

        // Cek pesan terakhir
        $lastMsg = $this->chatMessageRepo->getMessagesForRoom($roomId)->last();
        $serverLastMsgId = $lastMsg ? $lastMsg->id : 0;

        // Jika ada pesan baru atau bukan polling (load awal)
        if (!$isPolling || $serverLastMsgId > $clientLastMsgId) {
            
            // Ambil pesan
            $messages = $this->chatMessageRepo->getMessagesForRoom($roomId);
            
            // [UPDATE] Selalu tandai pesan sebagai terbaca saat data diambil (baik load awal maupun polling)
            // Karena jika user memanggil endpoint ini, berarti mereka sedang melihat chat room tersebut
            \App\Domains\Chat\Models\ChatMessage::where('chat_room_id', $roomId)
                ->where('sender_type', 'customer')
                ->where('status', '!=', 'read')
                ->update(['status' => 'read']);
            
            // Ambil Order History (Limit 5 terakhir)
            $orders = [];
            if ($room->customer) {
                $orders = \App\Domains\Order\Models\Order::where('customer_id', $room->customer->id)
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get()
                            ->map(function($order) {
                                return [
                                    'id' => $order->id,
                                    'order_number' => $order->order_number ?? ('#' . $order->id),
                                    'total_amount' => number_format($order->total_amount, 0, ',', '.'),
                                    'status' => $order->status,
                                    'date' => $order->created_at->format('d M Y'),
                                    'items_count' => $order->items()->count()
                                ];
                            });
            }

            // Prepare customer data for response
            $customerData = null;
            if ($room->customer) {
                $customerData = $room->customer;
                $customerData->is_contact = false;
            } elseif ($room->chatContact) {
                // Map contact to customer structure
                $customerData = new \stdClass();
                $customerData->id = $room->chatContact->id;
                $customerData->name = $room->chatContact->name ?? $room->chatContact->phone;
                $customerData->phone = $room->chatContact->phone;
                $customerData->email = null;
                $customerData->address = null;
                $customerData->city = null;
                $customerData->is_contact = true;
            } else {
                // [SELF-HEALING] Jika Room Orphan (tidak punya customer/contact), coba cari dari pesan
                $firstContactMsg = $room->messages()->where('sender_type', 'contact')->first();
                if ($firstContactMsg) {
                    $contact = \App\Domains\Chat\Models\ChatContact::find($firstContactMsg->sender_id);
                    if ($contact) {
                        // Fix Room Relation
                        $room->update(['chat_contact_id' => $contact->id]);
                        $room->load('chatContact'); // Reload relation

                        // Populate Data
                        $customerData = new \stdClass();
                        $customerData->id = $contact->id;
                        $customerData->name = $contact->name ?? $contact->phone;
                        $customerData->phone = $contact->phone;
                        $customerData->email = null;
                        $customerData->address = null;
                        $customerData->city = null;
                        $customerData->is_contact = true;
                    }
                }
            }

            // Inject customer data into room object for frontend compatibility
            $room->customer = $customerData;

            return response()->json([
                'room' => $room,
                'messages' => $messages,
                'orders' => $orders,
                'last_message_id' => $serverLastMsgId
            ]);
        }

        // Short Polling: Return no update immediately
        return response()->json(['status' => 'no_update']);
    }

    /**
     * [AJAX] Get Unread Notifications
     */
    public function getNotifications()
    {
        $user = Auth::user();
        
        // Query dasar: pesan dari customer yang statusnya 'new' (belum dibaca)
        // Asumsi: status 'new' atau 'unread' menandakan pesan belum dibaca
        // Sesuaikan 'new' dengan status actual di database Anda (misal: 'sent', 'delivered' adalah status kirim, 'read' status baca)
        // Jika pesan masuk (from customer), biasanya statusnya 'received' atau 'new' sampai dibaca.
        // Mari kita asumsikan pesan masuk yang belum dibaca user memiliki status tertentu atau kita cek read_at (jika ada).
        // Berdasarkan ChatMessage model, ada kolom 'status'.
        
        $query = \App\Domains\Chat\Models\ChatMessage::with(['chatRoom.customer', 'chatRoom.chatContact'])
            ->whereIn('sender_type', ['customer', 'contact'])
            ->where('status', '!=', 'read'); // Ambil semua yang belum 'read'
            
        // [FILTER] Hanya notifikasi dari nomor 62...
        $query->whereHas('chatRoom', function($q) {
             $q->where(function($sq) {
                 $sq->whereHas('customer', function($ssq) {
                     $ssq->where('phone', 'like', '62%');
                 })->orWhereHas('chatContact', function($ssq) {
                     $ssq->where('phone', 'like', '62%');
                 });
             });
        });

        // Jika bukan admin, filter berdasarkan room yang di-assign ke CS ini
        if ($user->role !== 'admin') {
            $query->whereHas('chatRoom', function($q) use ($user) {
                $q->where('cs_user_id', $user->id);
            });
        }
        
        $unreadCount = $query->count();
        
        // Ambil 5 pesan terbaru
        $latestMessages = $query->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($msg) {
                $customerName = $msg->chatRoom->customer->name ?? ($msg->chatRoom->chatContact->name ?? ($msg->chatRoom->chatContact->phone ?? 'Unknown'));
                return [
                    'id' => $msg->id,
                    'sender_name' => $customerName,
                    'message' => \Illuminate\Support\Str::limit($msg->message_content, 40),
                    'time' => $msg->created_at->diffForHumans(),
                    'room_id' => $msg->chat_room_id,
                    'initial' => strtoupper(substr($customerName, 0, 1))
                ];
            });
            
        return response()->json([
            'count' => $unreadCount,
            'messages' => $latestMessages
        ]);
    }

    /**
     * [AJAX] Update Customer Data from Chat
     */
    public function updateCustomer(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'is_contact' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_contact')) {
            // PROMOTION LOGIC: Contact -> Customer
            $contact = \App\Domains\Chat\Models\ChatContact::find($id);
            if (!$contact) {
                return response()->json(['success' => false, 'message' => 'Contact not found'], 404);
            }

            // Check if customer exists
            $existingCustomer = \App\Domains\Customer\Models\Customer::where('phone', $contact->phone)->first();
            $message = 'Kontak berhasil dijadikan Pelanggan';

            if ($existingCustomer) {
                $customer = $existingCustomer;
                // Update existing customer with new data
                $customer->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'city' => $request->city,
                    'is_manual_name' => '1',
                ]);
                $message = 'Pelanggan sudah terdata. Chat berhasil ditautkan ke pelanggan tersebut.';
            } else {
                // Create Customer
                $customer = \App\Domains\Customer\Models\Customer::create([
                    'name' => $request->name,
                    'phone' => $contact->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'city' => $request->city,
                    'is_manual_name' => '1',
                ]);
            }

            // Update Room
            $room = \App\Domains\Chat\Models\ChatRoom::where('chat_contact_id', $id)->first();
            if ($room) {
                $room->update([
                    'customer_id' => $customer->id,
                    'chat_contact_id' => null
                ]);
            }

            // Delete Contact (Optional, but recommended to avoid duplicates)
            $contact->delete();

            return response()->json(['success' => true, 'message' => $message, 'customer' => $customer]);

        } else {
            // EXISTING LOGIC: Update Customer
            $customer = \App\Domains\Customer\Models\Customer::find($id);
            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
            }

            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'is_manual_name' => '1', 
            ]);

            return response()->json(['success' => true, 'message' => 'Data pelanggan berhasil diperbarui', 'customer' => $customer]);
        }
    }

    /**
     * [AJAX] Kirim pesan
     */
    public function storeAjaxMessage(Request $request, int $roomId)
    {
        $request->validate([
            'message_body' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx', // Max 10MB
        ]);

        // Pastikan ada pesan atau attachment
        if (!$request->message_body && !$request->hasFile('attachment')) {
            return response()->json(['error' => 'Pesan atau lampiran harus diisi'], 422);
        }

        $csUser = Auth::user();

        if (!$csUser instanceof \App\Models\User) {
            return response()->json(['error' => 'Unauthorized or invalid user type'], 401);
        }

        try {
            $attachment = $request->file('attachment');
            $message = $this->chatService->sendOutboundMessage($csUser, $roomId, $request->message_body ?? '', $attachment);
            
            return response()->json([
                'success' => true,
                'message' => $message 
            ]);

        } catch (\Exception $e) {
            Log::error('Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menampilkan halaman percakapan untuk room tertentu
     */
    public function show(int $roomId)
    {
        $room = \App\Domains\Chat\Models\ChatRoom::with('customer')->find($roomId);
        
        if (!$room) {
            return redirect()->route('chat.ui')->with('error', 'Chat room tidak ditemukan');
        }

        $messages = $this->chatMessageRepo->getMessagesForRoom($roomId);

        return view('pages.chat.show', compact('room', 'messages'));
    }

    /**
     * Menyimpan pesan baru dari form (non-AJAX)
     */
    public function storeMessage(Request $request, int $roomId)
    {
        $request->validate(['message_body' => 'required|string']);
        $csUser = Auth::user();

        try {
            $this->chatService->sendOutboundMessage($csUser, $roomId, $request->message_body);
            
            return redirect()->route('chat.show', $roomId)->with('success', 'Pesan berhasil dikirim');

        } catch (\Exception $e) {
            Log::error('Chat Error: ' . $e->getMessage());
            return redirect()->route('chat.show', $roomId)->with('error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }
    /**
     * Memulai chat baru dengan nomor HP (AJAX)
     */
    public function startChat(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'nullable|string',
        ]);

        try {
            // 1. Bersihkan nomor telepon
            $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
            if (str_starts_with($cleanPhone, '08')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }

            // 2. Cari atau Buat Customer
            $customerRepo = app(\App\Domains\Customer\Interfaces\CustomerRepositoryInterface::class);
            $customer = $customerRepo->findByPhone($cleanPhone);

            if (!$customer) {
                $customer = $customerRepo->create([
                    'phone' => $cleanPhone,
                    'name' => $request->name ?: $cleanPhone, // Gunakan nama atau nomor HP
                    'is_manual_name' => !empty($request->name) ? '1' : '0' // Tandai jika nama diinput manual
                ]);
            } else if ($request->name) {
                // Update nama jika diinput manual
                $customerRepo->update($customer->id, [
                    'name' => $request->name,
                    'is_manual_name' => '1'
                ]);
            }

            // 3. Cari atau Buat Chat Room
            $room = $this->chatRoomRepo->findOrCreateRoomForCustomer($customer->id);

            // 4. Assign CS jika belum ada (Round Robin)
            if (!$room->cs_user_id) {
                $csId = $this->chatService->assignCsToNewRoom();
                if ($csId) {
                    $this->chatRoomRepo->assignCsToRoom($room->id, $csId);
                }
            }

            // 5. Return Room ID
            return response()->json([
                'success' => true,
                'room_id' => $room->id
            ]);

        } catch (\Exception $e) {
            Log::error('Start Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create customer for a room (Ghost Room Fix)
     */
    public function createCustomerForRoom(Request $request, $roomId)
    {
        // Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        $room = \App\Domains\Chat\Models\ChatRoom::find($roomId);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Room not found'], 404);
        }

        // Check if customer exists
        $customer = \App\Domains\Customer\Models\Customer::where('phone', $request->phone)->first();
        $message = 'Pelanggan berhasil dibuat';

        if (!$customer) {
            // Create Customer
            $customer = \App\Domains\Customer\Models\Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'is_manual_name' => '1',
            ]);
        } else {
            $message = 'Pelanggan sudah terdata. Chat berhasil ditautkan.';
            // Update existing
            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'is_manual_name' => '1',
            ]);
        }

        // Update Room
        $room->update([
            'customer_id' => $customer->id,
            'chat_contact_id' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'customer' => $customer
        ]);
    }

    /**
     * Reassign CS to a chat room (Admin only)
     */
    public function reassignCs(Request $request, $roomId)
    {
        $request->validate([
            'cs_user_id' => 'required|exists:users,id'
        ]);

        $room = \App\Domains\Chat\Models\ChatRoom::find($roomId);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Room not found'], 404);
        }

        // Verify the CS user is actually a CS role
        $csUser = \App\Models\User::where('id', $request->cs_user_id)
            ->where('role', 'cs')
            ->first();

        if (!$csUser) {
            return response()->json(['success' => false, 'message' => 'User is not a CS'], 400);
        }

        $room->update(['cs_user_id' => $request->cs_user_id]);

        return response()->json([
            'success' => true,
            'message' => 'Chat berhasil di-assign ke ' . $csUser->name,
            'cs_user' => [
                'id' => $csUser->id,
                'name' => $csUser->name,
                'email' => $csUser->email,
            ]
        ]);
    }
}