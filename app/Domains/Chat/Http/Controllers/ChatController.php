<?php
// app/Domains/Chat/Http/Controllers/ChatController.php

namespace App\Domains\Chat\Http\Controllers;

use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Interfaces\ChatMessageRepositoryInterface;
use App\Domains\Chat\Services\ChatService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $chatService;
    protected $chatRoomRepo;
    protected $chatMessageRepo;

    public function __construct(
        ChatService $chatService,
        ChatRoomRepositoryInterface $chatRoomRepo,
        ChatMessageRepositoryInterface $chatMessageRepo
    ) {
        $this->chatService = $chatService;
        $this->chatRoomRepo = $chatRoomRepo;
        $this->chatMessageRepo = $chatMessageRepo;
    }

    /**
     * Menampilkan halaman Inbox utama CS.
     * Daftar semua room yang di-assign ke dia.
     */
    public function index()
    {
        $csUser = Auth::user();
        $rooms = $this->chatRoomRepo->getRoomsForCs($csUser->id);

        // Nanti kita akan buat view 'pages.chat.index'
        // return view('pages.chat.index', compact('rooms'));
        
        // Untuk testing sementara:
        return response()->json($rooms);
    }

    /**
     * Menampilkan satu ruang obrolan spesifik.
     */
    public function show(int $roomId)
    {
        $csUser = Auth::user();
        
        // TODO: Validasi apakah CS ini berhak mengakses room $roomId
        
        $room = $this.chatRoomRepo->findRoomById($roomId); // Asumsi method ini ada
        $messages = $this->chatMessageRepo->getMessagesForRoom($roomId);

        // Nanti kita akan buat view 'pages.chat.show'
        // return view('pages.chat.show', compact('room', 'messages'));

        // Untuk testing sementara:
        return response()->json([
            'room' => $room,
            'messages' => $messages
        ]);
    }

    /**
     * Menerima kiriman balasan dari CS (via form).
     */
    public function storeMessage(Request $request, int $roomId)
    {
        $request->validate(['message_body' => 'required|string']);
        
        $csUser = Auth::user();
        $messageBody = $request->input('message_body');

        try {
            // Panggil "otak" kita untuk mengirim pesan keluar
            $this->chatService->sendOutboundMessage($csUser, $roomId, $messageBody);
            
            return redirect()->route('chat.show', $roomId)
                             ->with('success', 'Pesan terkirim!');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }
}