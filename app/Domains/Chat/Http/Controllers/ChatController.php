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
     * [HALAMAN APLIKASI] UI Chat 3 Kolom (Unified Dashboard)
     * URL: /app/chat/ui
     */
    public function showChatUI()
    {
        $csUser = Auth::user();
        
        // Ambil semua room untuk sidebar kiri
        $rooms = \App\Domains\Chat\Models\ChatRoom::with(['customer', 'latestMessage'])
                    // [MODIFIKASI] Tampilkan SEMUA room untuk sementara agar CS bisa melihat semua chat
                    // Idealnya nanti ada filter "My Chats" vs "All Chats"
                    /*
                    ->where(function($q) use ($csUser) {
                        $q->where('cs_user_id', $csUser->id)
                          ->orWhereNull('cs_user_id');
                    })
                    */
                    // Order by latest message created_at, fallback to updated_at
                    ->orderByDesc(
                        \App\Domains\Chat\Models\ChatMessage::select('created_at')
                            ->whereColumn('chat_room_id', 'chat_rooms.id')
                            ->latest()
                            ->limit(1)
                    )
                    ->orderBy('updated_at', 'desc')
                    ->get();

        // Cek Status WA (untuk indikator di UI)
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
            // Ignore error for UI check
        }

        return view('pages.chat.ui', compact('rooms', 'isConnected'));
    }

    public function getRooms(Request $request)
    {
        $clientLastUpdated = $request->query('last_updated_at', 0);
        $clientLastGlobalMsgId = $request->query('last_global_msg_id', 0);
        
        $startTime = time();

        // [OPTIMISASI] Tutup sesi agar tidak memblokir request lain (AJAX send message, dll)
        session_write_close();
        
        // 1. Cek timestamp terbaru di DB
        $latestRoom = \App\Domains\Chat\Models\ChatRoom::latest('updated_at')->first();
        $serverLastUpdated = $latestRoom ? $latestRoom->updated_at->timestamp : 0;

        // 1b. Cek ID pesan terakhir secara global (lebih akurat daripada timestamp detik)
        $serverLastGlobalMsgId = \App\Domains\Chat\Models\ChatMessage::max('id') ?? 0;

        // 2. Jika ada update baru (timestamp berubah ATAU ada pesan baru)
        if ($serverLastUpdated > $clientLastUpdated || $serverLastGlobalMsgId > $clientLastGlobalMsgId) {
            $rooms = \App\Domains\Chat\Models\ChatRoom::with(['customer', 'latestMessage'])
                        ->orderByDesc(
                            \App\Domains\Chat\Models\ChatMessage::select('created_at')
                                ->whereColumn('chat_room_id', 'chat_rooms.id')
                                ->latest()
                                ->limit(1)
                        )
                        ->orderBy('updated_at', 'desc')
                        ->get()
                        ->map(function($room) {
                            return [
                                'id' => $room->id,
                                'updated_at' => $room->updated_at,
                                'updated_ts' => $room->updated_at->timestamp, // Kirim timestamp
                                'status' => $room->status,
                                'customer' => [
                                    'name' => $room->customer->name ?? 'Guest',
                                    'phone' => $room->customer->phone,
                                ],
                                'latest_message' => $room->latestMessage ? [
                                    'message_content' => $room->latestMessage->message_content,
                                    'created_at' => $room->latestMessage->created_at,
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

        $room = \App\Domains\Chat\Models\ChatRoom::with('customer')->find($roomId);
        if (!$room) return response()->json(['error' => 'Room not found'], 404);

        // Cek pesan terakhir
        $lastMsg = $this->chatMessageRepo->getMessagesForRoom($roomId)->last();
        $serverLastMsgId = $lastMsg ? $lastMsg->id : 0;

        // Jika ada pesan baru atau bukan polling (load awal)
        if (!$isPolling || $serverLastMsgId > $clientLastMsgId) {
            
            // Ambil pesan
            $messages = $this->chatMessageRepo->getMessagesForRoom($roomId);
            
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