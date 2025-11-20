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
     * Dashboard Statistik CS
     * URL: /app/cs/dashboard
     */
    public function index()
    {
        $csUser = Auth::user();
        $rooms = $this->chatRoomRepo->getRoomsForCs($csUser->id);

        // Pastikan view 'dashboard_cs' ada (hasil rename dari dashboard.cs.clean.blade.php)
        return view('pages.chat.dashboard_cs', compact('rooms'));
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
     * [HALAMAN APLIKASI] UI Chat 3 Kolom
     * URL: /app/chat/ui
     */
    public function showChatUI()
    {
        // Ambil semua room + pesan terakhir + data customer
        $rooms = \App\Domains\Chat\Models\ChatRoom::with(['customer', 'messages' => function($q) {
                        $q->latest()->limit(1);
                    }])
                    ->orderBy('updated_at', 'desc')
                    ->get();

        // Pastikan view 'obrolan_cs' ada (hasil rename dari obrolan.cs.clean.blade.php)
        return view('pages.chat.obrolan_cs', compact('rooms'));
    }

    /**
     * [AJAX] Ambil data chat room spesifik
     */
    public function getRoomData(int $roomId)
    {
        $room = \App\Domains\Chat\Models\ChatRoom::with('customer')->find($roomId);

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $messages = $this->chatMessageRepo->getMessagesForRoom($roomId);

        return response()->json([
            'room' => $room,
            'messages' => $messages
        ]);
    }

    /**
     * [AJAX] Kirim pesan
     */
    public function storeAjaxMessage(Request $request, int $roomId)
    {
        $request->validate(['message_body' => 'required|string']);
        $csUser = Auth::user();

        try {
            $message = $this->chatService->sendOutboundMessage($csUser, $roomId, $request->message_body);
            
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
     * Fallback method untuk mencegah error jika ada route lama yang terlewat
     */
    public function show(int $roomId)
    {
        return redirect()->route('chat.ui');
    }
}