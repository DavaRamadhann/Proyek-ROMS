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
        return view('pages.chat.whatsapp', compact('rooms')); // [MODIFIKASI] Di-uncomment dari kode asli
        
        // Untuk testing sementara:
        // return response()->json($rooms);
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
        return view('pages.chat.show', compact('room', 'messages')); // [MODIFIKASI] Di-uncomment dari kode asli

        // Untuk testing sementara:
        // return response()->json([
        //     'room' => $room,
        //     'messages' => $messages
        // ]);
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

    // ====================================================================
    // [METHOD BARU]
    // ====================================================================

    /**
     * Menampilkan halaman UI Chat 3-kolom (ala WhatsApp).
     * Halaman ini akan di-handle oleh JS untuk interaktivitas.
     */
    /**
     * Menampilkan halaman UI Chat 3-kolom (ala WhatsApp).
     * Halaman ini akan di-handle oleh JS untuk interaktivitas.
     */
    public function showWhatsAppUI()
    {
        // [TES DEBUG]
        // Kita tidak akan pakai Auth::user() dulu.
        // Kita paksa ambil SEMUA room.
        $allRooms = \App\Domains\Chat\Models\ChatRoom::with('customer')
                                                   ->orderBy('id', 'desc')
                                                   ->get();

        // Kita log datanya untuk memastikan
        \Illuminate\Support\Facades\Log::info('TES DEBUG - Data Rooms:', $allRooms->toArray());
        
        // Kita juga log siapa yang sedang login
        \Illuminate\Support\Facades\Log::info('TES DEBUG - User Login:', [
            'id' => \Illuminate\Support\Facades\Auth::id(), 
            'email' => \Illuminate\Support\Facades\Auth::user()->email
        ]);

        // Kirim variabel $rooms ke view, yang berisi SEMUA room
        $rooms = $allRooms;
        
        return view('pages.chat.whatsapp', compact('rooms'));
    }

    // ====================================================================
    // [METHOD BARU UNTUK AJAX]
    // ====================================================================

    /**
     * [BARU] Mengambil data room spesifik untuk AJAX call.
     * Ini akan dipanggil oleh JavaScript saat CS mengklik chat di kolom kiri.
     */
    public function getRoomData(int $roomId)
    {
        // TODO: Validasi apakah CS ini berhak mengakses room $roomId
        
        // [PERBAIKAN]
        // Kita tidak pakai repository, tapi panggil Model langsung
        // dan pakai ->with('customer') untuk Eager Loading.
        $room = \App\Domains\Chat\Models\ChatRoom::with('customer')
                                                ->find($roomId);

        $messages = $this->chatMessageRepo->getMessagesForRoom($roomId);

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }
        
        // Pastikan customer ter-load
        if (!$room->customer) {
             return response()->json(['error' => 'Customer data not found for this room.'], 404);
        }

        // Kirim balik data sebagai JSON
        return response()->json([
            'room' => $room, // Ini SEKARANG akan berisi data customer
            'messages' => $messages
        ]);
    }

    /**
     * [BARU] Menerima kiriman balasan dari CS (via AJAX).
     * Berbeda dari storeMessage, ini mengembalikan JSON, bukan redirect.
     */
    public function storeAjaxMessage(Request $request, int $roomId)
    {
        $request->validate(['message_body' => 'required|string']);
        
        $csUser = Auth::user();
        $messageBody = $request->input('message_body');

        try {
            // Panggil "otak" kita untuk mengirim pesan keluar
            // $this->chatService->sendOutboundMessage($csUser, $roomId, $messageBody);
            
            // Untuk testing UI, kita simulasi pesan baru
            // Hapus/ganti ini dengan $this->chatService->... yang asli
            $newMessage = $this->chatMessageRepo->createMessage(
                $roomId,
                $csUser->id,
                'user', // Tipe pengirim adalah 'user' (CS)
                $messageBody
            );

            return response()->json([
                'success' => true,
                'message' => $newMessage // Kirim balik pesan yg baru dibuat
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ], 500);
        }
    }
}