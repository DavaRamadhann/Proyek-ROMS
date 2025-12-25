<?php

namespace App\Services;

use App\Domains\Order\Models\Order;
use App\Domains\Chat\Models\ChatRoom;
use App\Domains\Chat\Models\ChatMessage;
use App\Domains\Customer\Models\Customer;
use App\Domains\Product\Models\Product;
use App\Domains\Broadcast\Models\Broadcast;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get comprehensive dashboard statistics
     */
    protected ?User $user = null;

    /**
     * Get comprehensive dashboard statistics
     */
    public function getStatistics(?User $user = null): array
    {
        $this->user = $user;

        return [
            // Basic Stats
            'active_chats' => $this->getActiveChatsToday(),
            'active_chats_trend' => $this->getActiveChatsYesterday(),
            'total_customers' => $this->applyCustomerScope(Customer::query())->count(),
            'total_customers_trend' => $this->getCustomersLastMonth(),
            'total_products' => Product::count(),
            'orders_today' => $this->getOrdersToday(),
            'orders_yesterday' => $this->getOrdersYesterday(),
            
            // Revenue Metrics
            'revenue_today' => $this->getRevenueToday(),
            'revenue_yesterday' => $this->getRevenueYesterday(),
            'revenue_this_month' => $this->getRevenueThisMonth(),
            'revenue_last_month' => $this->getRevenueLastMonth(),
            
            // Performance Metrics
            'conversion_rate' => $this->getConversionRate(),
            'average_order_value' => $this->getAverageOrderValue(),
            'average_response_time' => $this->getAverageResponseTime(),
            
            // Alerts
            'unanswered_chats' => $this->getUnansweredChats(),
            'pending_orders' => $this->getPendingOrders(),
            
            // Charts Data
            'sales_trend' => $this->getSalesTrendLast7Days(),
            'revenue_trend' => $this->getRevenueTrendLast6Months(),
            'top_products' => $this->getTopProducts(),
            'customer_growth' => $this->getCustomerGrowthLast6Months(),
            'peak_hours' => $this->getPeakHours(),
            
            // Recent Activity
            'recent_activities' => $this->getRecentActivities(),
            
            // CS Performance (for admin)
            'cs_performance' => $this->getCSPerformance(),
        ];
    }

    /** ============= BASIC STATISTICS ============= */

    private function applyChatScope($query)
    {
        // Apply filtering for ALL users (Admin and CS)
        // Admin sees all data
        if ($this->user && $this->user->isAdmin()) {
            return $query;
        }

        return $query->when($this->user, function($q) {
            $q->where('cs_user_id', $this->user->id);
        });
    }

    private function applyOrderScope($query)
    {
        // Apply filtering for ALL users (Admin and CS)
        // Admin sees all data
        if ($this->user && $this->user->isAdmin()) {
            return $query;
        }

        return $query->when($this->user, function($q) {
            $q->whereHas('customer.chatRooms', function($sq) {
                $sq->where('cs_user_id', $this->user->id);
            });
        });
    }

    private function applyCustomerScope($query)
    {
        // Apply filtering for ALL users (Admin and CS)
        // Admin sees all data
        if ($this->user && $this->user->isAdmin()) {
            return $query;
        }

        return $query->when($this->user, function($q) {
            $q->whereHas('chatRooms', function($sq) {
                $sq->where('cs_user_id', $this->user->id);
            });
        });
    }

    private function getActiveChatsToday(): int
    {
        return $this->applyChatScope(ChatRoom::query())
            ->whereDate('updated_at', today())
            ->count();
    }

    private function getActiveChatsYesterday(): int
    {
        return $this->applyChatScope(ChatRoom::query())
            ->whereDate('updated_at', today()->subDay())
            ->count();
    }

    private function getOrdersToday(): int
    {
        return $this->applyOrderScope(Order::query())
            ->whereDate('created_at', today())
            ->count();
    }

    private function getOrdersYesterday(): int
    {
        return $this->applyOrderScope(Order::query())
            ->whereDate('created_at', today()->subDay())
            ->count();
    }

    private function getCustomersLastMonth(): int
    {
        return $this->applyCustomerScope(Customer::query())
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
    }

    /** ============= REVENUE METRICS ============= */

    private function getRevenueToday(): float
    {
        return (float) $this->applyOrderScope(Order::query())
            ->whereDate('created_at', today())
            ->sum('total_amount');
    }

    private function getRevenueYesterday(): float
    {
        return (float) $this->applyOrderScope(Order::query())
            ->whereDate('created_at', today()->subDay())
            ->sum('total_amount');
    }

    private function getRevenueThisMonth(): float
    {
        return (float) $this->applyOrderScope(Order::query())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
    }

    private function getRevenueLastMonth(): float
    {
        return (float) $this->applyOrderScope(Order::query())
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');
    }

    /** ============= PERFORMANCE METRICS ============= */

    private function getConversionRate(): float
    {
        $chatsToday = $this->getActiveChatsToday();
        $ordersToday = $this->getOrdersToday();
        
        return $chatsToday > 0 ? round(($ordersToday / $chatsToday) * 100, 2) : 0;
    }

    private function getAverageOrderValue(): float
    {
        $totalRevenue = $this->applyOrderScope(Order::query())->sum('total_amount');
        $totalOrders = $this->applyOrderScope(Order::query())->count();
        
        return $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;
    }

    private function getAverageResponseTime(): int
    {
        // Calculate average time between customer message and CS response
        // Return in minutes
        $chatRooms = $this->applyChatScope(ChatRoom::with('messages'))->get();
        $totalResponseTime = 0;
        $responseCount = 0;

        foreach ($chatRooms as $room) {
            $messages = $room->messages->sortBy('created_at');
            $lastCustomerMessage = null;

            foreach ($messages as $message) {
                if ($message->sender_type === 'customer') {
                    $lastCustomerMessage = $message;
                } elseif ($message->sender_type === 'cs' && $lastCustomerMessage) {
                    $responseTime = $message->created_at->diffInMinutes($lastCustomerMessage->created_at);
                    $totalResponseTime += $responseTime;
                    $responseCount++;
                    $lastCustomerMessage = null;
                }
            }
        }

        return $responseCount > 0 ? (int) round($totalResponseTime / $responseCount) : 0;
    }

    /** ============= ALERTS ============= */

    private function getUnansweredChats(): int
    {
        // Chats that haven't received a CS reply in the last 30 minutes
        return $this->applyChatScope(ChatRoom::query())
        ->whereHas('messages', function ($query) {
            $query->where('sender_type', 'customer')
                ->where('created_at', '>', now()->subMinutes(30));
        })
        ->whereDoesntHave('messages', function ($query) {
            $query->where('sender_type', 'cs')
                ->where('created_at', '>', now()->subMinutes(30));
        })
        ->count();
    }

    private function getPendingOrders(): int
    {
        // Orders that are pending for more than 24 hours
        return $this->applyOrderScope(Order::query())
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subDay())
            ->count();
    }

    /** ============= CHART DATA ============= */

    private function getSalesTrendLast7Days(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $labels[] = $date->format('D, M j'); // e.g., "Mon, Dec 2"
            $data[] = $this->applyOrderScope(Order::query())->whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getRevenueTrendLast6Months(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y'); // e.g., "Dec 2024"
            $data[] = (float) $this->applyOrderScope(Order::query())
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount');
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getTopProducts(): array
    {
        try {
            $query = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id'); // Join orders to filter

            if ($this->user && !$this->user->isAdmin()) {
                $query->join('customers', 'orders.customer_id', '=', 'customers.id')
                      ->join('chat_rooms', 'customers.id', '=', 'chat_rooms.customer_id')
                      ->where('chat_rooms.cs_user_id', $this->user->id);
            }

            $topProducts = $query->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();

            return [
                'labels' => $topProducts->pluck('name')->toArray(),
                'data' => $topProducts->pluck('total_sold')->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }
    }

    private function getCustomerGrowthLast6Months(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');
            $data[] = $this->applyCustomerScope(Customer::query())
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getPeakHours(): array
    {
        // Get message count by hour (0-23)
        $hours = range(0, 23);
        $data = [];

        foreach ($hours as $hour) {
            $query = ChatMessage::query();
            
            if ($this->user && !$this->user->isAdmin()) {
                $query->whereHas('chatRoom', function($q) {
                    $q->where('cs_user_id', $this->user->id);
                });
            }

            $count = $query->whereRaw('EXTRACT(HOUR FROM created_at) = ?', [$hour])
                ->count();
            $data[] = $count;
        }

        return [
            'labels' => array_map(fn($h) => sprintf('%02d:00', $h), $hours),
            'data' => $data,
        ];
    }

    /** ============= RECENT ACTIVITY ============= */

    private function getRecentActivities(): array
    {
        try {
            $activities = [];

            // Recent Orders (last 5)
            try {
                $recentOrders = $this->applyOrderScope(Order::with('customer'))
                    ->latest()
                    ->limit(5)
                    ->get();

                foreach ($recentOrders as $order) {
                    $customerName = $order->customer ? $order->customer->name : 'Unknown Customer';
                    $activities[] = [
                        'type' => 'order',
                        'icon' => 'bi-cart-check-fill',
                        'color' => 'text-[#B45253]',
                        'bg_color' => 'bg-[#B45253]/10',
                        'title' => 'Pesanan Baru',
                        'description' => $customerName . ' - Rp ' . number_format($order->total_amount ?? 0, 0, ',', '.'),
                        'time' => $order->created_at,
                        'action_url' => route('orders.show', $order->id),
                        'action_text' => 'Lihat',
                    ];
                }
            } catch (\Exception $e) {
                // Skip orders if there's an error
            }

            // Recent Chats (last 5)
            try {
                $recentChats = $this->applyChatScope(ChatRoom::with(['customer', 'latestMessage']))
                    ->latest('updated_at')
                    ->limit(5)
                    ->get();

                foreach ($recentChats as $chat) {
                    $customerName = $chat->customer ? $chat->customer->name : 'Unknown Customer';
                    $messageContent = $chat->latestMessage ? ($chat->latestMessage->message_content ?? 'No message') : 'No message';
                    
                    $activities[] = [
                        'type' => 'chat',
                        'icon' => 'bi-chat-dots-fill',
                        'color' => 'text-blue-600',
                        'bg_color' => 'bg-blue-600/10',
                        'title' => 'Chat Baru',
                        'description' => $customerName . ' - ' . $messageContent,
                        'time' => $chat->updated_at,
                        'action_url' => route('chat.ui') . '?room=' . $chat->id,
                        'action_text' => 'Balas',
                    ];
                }
            } catch (\Exception $e) {
                // Skip chats if there's an error
            }

            // Recent Broadcasts (last 5)
            try {
                $recentBroadcasts = Broadcast::latest()
                    ->limit(5)
                    ->get();

                foreach ($recentBroadcasts as $broadcast) {
                    $activities[] = [
                        'type' => 'broadcast',
                        'icon' => 'bi-megaphone-fill',
                        'color' => 'text-purple-600',
                        'bg_color' => 'bg-purple-600/10',
                        'title' => 'Broadcast Terkirim',
                        'description' => ($broadcast->title ?? 'No title') . ' - ' . ($broadcast->recipient_count ?? 0) . ' penerima',
                        'time' => $broadcast->created_at,
                        'action_url' => route('broadcast.index'),
                        'action_text' => 'Detail',
                    ];
                }
            } catch (\Exception $e) {
                // Skip broadcasts if there's an error
            }

            // Sort by time and limit to 10
            if (!empty($activities)) {
                usort($activities, function ($a, $b) {
                    return $b['time'] <=> $a['time'];
                });
            }

            return array_slice($activities, 0, 10);
        } catch (\Exception $e) {
            return [];
        }
    }

    /** ============= CS PERFORMANCE ============= */

    private function getCSPerformance(): array
    {
        try {
            $query = User::where('role', 'cs');
            
            // Show only own performance for all users (except Admin)
            if ($this->user && !$this->user->isAdmin()) {
                $query->where('id', $this->user->id);
            }
            
            $csUsers = $query->get();
            $performance = [];

            foreach ($csUsers as $cs) {
                try {
                    // Check if assignedChats relationship exists
                    if (!method_exists($cs, 'assignedChats')) {
                        // Fallback: Get chats assigned to this CS via assigned_cs_id
                        $assignedChats = ChatRoom::where('assigned_cs_id', $cs->id)->count();
                        $resolvedChats = ChatRoom::where('assigned_cs_id', $cs->id)
                            ->where('status', 'resolved')
                            ->count();
                        $chatRooms = ChatRoom::where('assigned_cs_id', $cs->id)
                            ->with('messages')
                            ->get();
                    } else {
                        $assignedChats = $cs->assignedChats()->count();
                        $resolvedChats = $cs->assignedChats()->where('status', 'resolved')->count();
                        $chatRooms = $cs->assignedChats()->with('messages')->get();
                    }
                    
                    // Calculate average response time for this CS
                    $totalResponseTime = 0;
                    $responseCount = 0;

                    foreach ($chatRooms as $room) {
                        if (!$room->messages || $room->messages->isEmpty()) {
                            continue;
                        }
                        
                        $messages = $room->messages->sortBy('created_at');
                        $lastCustomerMessage = null;

                        foreach ($messages as $message) {
                            if ($message->sender_type === 'customer') {
                                $lastCustomerMessage = $message;
                            } elseif ($message->sender_type === 'cs' && $lastCustomerMessage) {
                                $responseTime = $message->created_at->diffInMinutes($lastCustomerMessage->created_at);
                                $totalResponseTime += $responseTime;
                                $responseCount++;
                                $lastCustomerMessage = null;
                            }
                        }
                    }

                    $avgResponseTime = $responseCount > 0 ? (int) round($totalResponseTime / $responseCount) : 0;

                    // Performance badge
                    $badge = 'Needs Improvement';
                    $badgeColor = 'bg-red-100 text-red-600';
                    
                    if ($avgResponseTime < 10 && $resolvedChats > 5) {
                        $badge = 'Excellent';
                        $badgeColor = 'bg-green-100 text-green-600';
                    } elseif ($avgResponseTime < 20 && $resolvedChats > 2) {
                        $badge = 'Good';
                        $badgeColor = 'bg-yellow-100 text-yellow-600';
                    }

                    $performance[] = [
                        'name' => $cs->name,
                        'assigned_chats' => $assignedChats,
                        'resolved_chats' => $resolvedChats,
                        'avg_response_time' => $avgResponseTime,
                        'is_online' => $cs->is_online ?? false,
                        'badge' => $badge,
                        'badge_color' => $badgeColor,
                    ];
                } catch (\Exception $e) {
                    // Skip this CS if there's an error
                    continue;
                }
            }

            return $performance;
        } catch (\Exception $e) {
            // Return empty array if there's a general error
            return [];
        }
    }
}
