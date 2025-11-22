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
     * [HALAMAN APLIKASI] UI Chat 3 Kolom
     * URL: /app/chat/ui
     */
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

        // Cek Status WA
        $isConnected = false;
        try {
            $waUrl = rtrim(config('services.whatsapp.url'), '/');
            $apiKey = config('services.whatsapp.api_key');
            $clientId = 'official_business';

            if ($waUrl && $apiKey) {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'Accept' => 'application/json',
                ])->timeout(5)->get("{$waUrl}/accounts");

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    $account = collect($data)->firstWhere('clientId', $clientId);
                    // Status dari WA Service biasanya 'READY' saat terhubung
                    if ($account && in_array(($account['status'] ?? ''), ['CONNECTED', 'READY'])) {
                        $isConnected = true;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal cek status WA di ChatController: ' . $e->getMessage());
        }

        return view('pages.chat.index', compact('rooms', 'isConnected'));
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
     * Menampilkan halaman percakapan untuk room tertentu
     */
    public function show(int $roomId)
    {
        $room = \App\Domains\Chat\Models\ChatRoom::with('customer')->find($roomId);
        
        if (!$room) {
            return redirect()->route('chat.index')->with('error', 'Chat room tidak ditemukan');
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
}