<?php

namespace App\Domains\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Order\Models\Order;
use App\Domains\Order\Models\OrderItem;
use App\Domains\Customer\Models\Customer;
use App\Domains\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Domains\Chat\Services\ChatService;
use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Reminder\Services\ReminderService;
use App\Domains\Message\Models\MessageTemplate;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $chatService;
    protected $chatRoomRepo;
    protected $reminderService;

    public function __construct(
        ChatService $chatService,
        ChatRoomRepositoryInterface $chatRoomRepo,
        ReminderService $reminderService
    ) {
        $this->chatService = $chatService;
        $this->chatRoomRepo = $chatRoomRepo;
        $this->reminderService = $reminderService;
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::with('customer')->latest();

        // [CS SCOPE] Filter orders by assigned customers
        if ($user->role !== 'admin') {
            $query->whereHas('customer.chatRooms', function($q) use ($user) {
                $q->where('cs_user_id', $user->id);
            });
        }

        // Filter by Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        // Statistics (Scoped)
        $statsQuery = Order::query();
        if ($user->role !== 'admin') {
            $statsQuery->whereHas('customer.chatRooms', function($q) use ($user) {
                $q->where('cs_user_id', $user->id);
            });
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'shipped' => (clone $statsQuery)->where('status', 'shipped')->count(),
            'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'revenue' => (clone $statsQuery)->where('status', 'completed')->sum('total_amount'),
        ];

        return view('pages.order.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // [CS SCOPE] Only show assigned customers
        if ($user->role !== 'admin') {
            $customers = Customer::whereHas('chatRooms', function($q) use ($user) {
                $q->where('cs_user_id', $user->id);
            })->get();
        } else {
            $customers = Customer::all();
        }

        $products = Product::all();
        return view('pages.order.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'custom_message' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total amount and prepare items data
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                // Double check if product exists (though validation covers it)
                if (!$product) {
                    throw new \Exception("Product not found for ID: " . $item['product_id']);
                }

                // Check Stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok tidak cukup untuk produk: " . $product->name . " (Sisa: " . $product->stock . ")");
                }

                $price = $product->price;
                $subtotal = $item['quantity'] * $price;
                $totalAmount += $subtotal;

                // Decrement Stock
                $product->decrement('stock', $item['quantity']);

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            }

            // Create Order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_id' => $request->customer_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create Order Items
            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'product_name' => $itemData['product_name'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $itemData['subtotal'],
                ]);
            }

            DB::commit();

            // --- AUTOMATION: Send WhatsApp Notification ---
            try {
                // 1. Cari atau buat room untuk customer ini
                $room = $this->chatRoomRepo->findOrCreateRoomForCustomer($order->customer_id);
                
                // 2. Siapkan pesan
                $formattedTotal = number_format($order->total_amount, 0, ',', '.');
                
                // Build Item List String
                $itemsList = "";
                foreach ($orderItemsData as $item) {
                    $itemsList .= "- " . $item['product_name'] . " (x" . $item['quantity'] . ")\n";
                }

                if ($request->filled('custom_message')) {
                    $message = $request->custom_message;
                    // Optional: Replace placeholders if user wants to use them in custom message
                    $message = str_replace('[Name]', $order->customer->name, $message);
                    $message = str_replace('[Order No]', $order->order_number, $message);
                    $message = str_replace('[Total]', $formattedTotal, $message);
                    $message = str_replace('[Items]', $itemsList, $message); // Add support for [Items] placeholder
                } else {
                    // Marketing Template
                    $message = "Halo Kak {$order->customer->name}! 👋\n\nTerima kasih banyak sudah memesan di Someah. Pesanan kakak *{$order->order_number}* sudah kami terima dan siap diproses.\n\n*Rincian Pesanan:*\n{$itemsList}\nTotal: Rp {$formattedTotal}\n\nDitunggu ya, kami pastikan pesanan kakak sampai dengan aman! 💖";
                }

                // 3. Kirim pesan (gunakan user yang login sebagai pengirim, atau system user jika perlu)
                // Karena ini aksi dari admin/CS yang login, kita pakai Auth::user()
                $currentUser = Auth::user();
                if ($currentUser) {
                    $this->chatService->sendOutboundMessage($currentUser, $room->id, $message);
                }
            } catch (\Exception $e) {
                // Jangan gagalkan order cuma karena notifikasi gagal
                \Illuminate\Support\Facades\Log::error('Gagal kirim notifikasi order: ' . $e->getMessage());
            }
            // ----------------------------------------------

            return redirect()->route('orders.index')->with('success', 'Order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items']);
        return view('pages.order.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,shipped,completed,cancelled',
            'custom_message' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;

        if ($request->status == 'shipped' && !$order->shipped_at) {
            $order->shipped_at = now();
        }
        
        if ($request->status == 'completed' && !$order->delivered_at) {
             $order->delivered_at = now();
        }

        $order->save();

        // --- AUTOMATION: Send WhatsApp Notification on Status Change ---
        if ($oldStatus != $request->status) {
            try {
                $room = $this->chatRoomRepo->findOrCreateRoomForCustomer($order->customer_id);
                
                $statusLabel = ucfirst($request->status);
                $message = '';

                if ($request->filled('custom_message')) {
                    $message = $request->custom_message;
                    $message = str_replace('[Name]', $order->customer->name, $message);
                    $message = str_replace('[Order No]', $order->order_number, $message);
                    $message = str_replace('[Status]', $statusLabel, $message);
                } else {
                    // Try to find a template for this specific status
                    // Naming convention: 'order_status_{status}' e.g., 'order_status_shipped'
                    $templateName = 'order_status_' . $request->status;
                    $template = MessageTemplate::where('type', 'order_notification')
                        ->where('name', $templateName)
                        ->first();

                    if ($template) {
                        // Prepare data for template
                        $data = [
                            'customer_name' => $order->customer->name,
                            'order_number' => $order->order_number,
                            'status' => $statusLabel,
                            'total_amount' => number_format($order->total_amount, 0, ',', '.'),
                            'order_date' => $order->created_at->format('d M Y'),
                        ];
                        
                        // Add recommendations if completed
                        if ($request->status == 'completed') {
                            $recommendations = collect();
                            foreach ($order->items as $item) {
                                if ($item->product && $item->product->recommendation_text) {
                                    $recommendations->push($item->product->recommendation_text);
                                }
                            }
                            $data['recommendation'] = $recommendations->unique()->implode("\n");
                        }

                        $message = $this->reminderService->processTemplate($template->content, $data);

                    } else {
                        // Fallback to Hardcoded Marketing Template if no template found
                        $message = "Halo Kak {$order->customer->name}! ✨\n\nUpdate status pesanan *{$order->order_number}* kakak: sekarang statusnya *{$statusLabel}* ya.";
                        
                        if ($request->status == 'shipped') {
                            $message .= "\n\nPaket sedang dalam perjalanan menuju alamat kakak. Mohon ditunggu ya! 🚚";
                        } elseif ($request->status == 'completed') {
                            $message .= "\n\nTerima kasih sudah berbelanja! Semoga kakak suka dengan produknya. 🥰";
                        } elseif ($request->status == 'cancelled') {
                            $message .= "\n\nMohon maaf atas ketidaknyamanannya. Jika ada pertanyaan, silakan hubungi kami ya. 🙏";
                        }

                        // Jika status completed, tambahkan rekomendasi (manual logic fallback)
                        if ($request->status == 'completed') {
                            $recommendations = collect();
                            foreach ($order->items as $item) {
                                if ($item->product && $item->product->recommendation_text) {
                                    $recommendations->push($item->product->recommendation_text);
                                }
                            }
                            
                            if ($recommendations->isNotEmpty()) {
                                $message .= "\n\n*Rekomendasi untuk Anda:*\n" . $recommendations->unique()->implode("\n");
                            }
                        }
                    }
                }

                $currentUser = Auth::user();
                if ($currentUser && !empty($message)) {
                    $this->chatService->sendOutboundMessage($currentUser, $room->id, $message);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal kirim notifikasi status: ' . $e->getMessage());
            }
        }
        // ---------------------------------------------------------------

        // Trigger Reminder Creation if status changed to completed
        if ($request->status == 'completed' && $oldStatus != 'completed') {
            $this->reminderService->generateRemindersForOrder($order);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();
            
            // Items will be deleted automatically if cascade is set, 
            // but let's be safe and delete them explicitly or rely on DB constraint.
            // Assuming cascade on delete in migration, but if not:
            // $order->items()->delete();
            
            $order->delete();
            
            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }
    }


    // ... (Existing Methods)

    // ==========================================
    // IMPORT FUNCTIONALITY
    // ==========================================
    public function importForm()
    {
        return view('pages.order.import');
    }

    public function import(Request $request, \App\Domains\Order\Services\OrderImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $importService->import($file->getRealPath());

            return redirect()->route('orders.index')->with('success', 'Import pesanan berhasil! Stok produk telah diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        // 1. Clear Output Buffer to prevent whitespace/HTML injection
        if (ob_get_level()) {
            ob_end_clean();
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="template_import_order.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['order_number', 'customer_name', 'customer_phone', 'product_name', 'quantity', 'payment_method', 'notes'];
        
        // Ambil contoh data produk nyata dari DB agar user tidak bingung
        $exampleProducts = Product::limit(2)->pluck('name')->toArray();
        $prod1 = $exampleProducts[0] ?? 'Contoh Produk A';
        $prod2 = $exampleProducts[1] ?? 'Contoh Produk B';

        $callback = function() use ($columns, $prod1, $prod2) {
            $file = fopen('php://output', 'w');
            
            // 2. Add BOM for Excel utf-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, $columns);
            
            // Contoh Data 1 (Order Baru Single Item)
            fputcsv($file, ['', 'Budi Santoso', '081234567890', $prod1, '2', 'transfer', 'Jangan pakai gula']);
            
            // Contoh Data 2 (Order Grouping - 2 Item dalam 1 Order)
            // order_number harus sama untuk menggabungkan item
            fputcsv($file, ['ORD-EXAMPLE-001', 'Siti Aminah', '08987654321', $prod1, '1', 'cod', '']);
            fputcsv($file, ['ORD-EXAMPLE-001', 'Siti Aminah', '08987654321', $prod2, '3', 'cod', '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
