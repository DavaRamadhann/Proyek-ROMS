<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Domains\Chat\Models\ChatRoom;
use App\Domains\Chat\Models\ChatMessage;
use App\Domains\Customer\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CSDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Hitung obrolan belum dibaca (status = 'unread')
        $unreadChatsCount = ChatRoom::where('status', 'unread')->count();
        
        // Total obrolan hari ini
        $todayChatsCount = ChatRoom::whereDate('created_at', today())->count();
        
        // Pesanan perlu diproses - untuk sementara kita skip karena model Order masih kosong
        $pendingOrdersCount = 0; // Nanti bisa diupdate ketika Order model sudah ada
        
        // Total pelanggan aktif (yang punya chat room)
        $activeCustomersCount = Customer::whereHas('chatRooms')->count();
        
        // Ambil 5 obrolan terbaru dengan pesan terakhir
        $recentChats = ChatRoom::with(['customer', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->map(function($room) {
                $lastMessage = $room->messages->first();
                return [
                    'room_id' => $room->id,
                    'customer_name' => $room->customer->name ?? 'Unknown',
                    'customer_phone' => $room->customer->phone ?? '-',
                    'last_message' => $lastMessage ? 
                        (strlen($lastMessage->message_content) > 50 ? 
                            substr($lastMessage->message_content, 0, 50) . '...' : 
                            $lastMessage->message_content) : 
                        'Tidak ada pesan',
                    'last_message_time' => $room->updated_at,
                    'status' => $room->status,
                ];
            });
        
        return view('cs.dashboard', compact(
            'unreadChatsCount',
            'todayChatsCount',
            'pendingOrdersCount',
            'activeCustomersCount',
            'recentChats'
        ));
    }
}
