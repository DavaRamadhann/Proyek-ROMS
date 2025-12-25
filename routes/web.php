<?php

use App\Http\Controllers\Auth\AuthControllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domains\Chat\Http\Controllers\ChatController;
use App\Domains\Chat\Http\Controllers\WhatsAppConnectionController;

// Root redirect
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::controller(AuthControllers::class)->group(function () {
    
    // Rute untuk Tamu (Guest)
    Route::middleware('guest')->group(function () {
        // Register
        Route::get('register', 'showRegisterForm')->name('register');
        Route::post('register', 'register');
        
        // Login
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', 'login');
        
        // Google OAuth
        Route::get('auth/google/redirect', 'redirectToGoogle')->name('google.redirect');
        Route::get('auth/google/callback', 'handleGoogleCallback')->name('google.callback');
        
        // Email Verification for Register
        Route::get('verify-email', 'showVerifyForm')->name('verification.notice');
        Route::post('verify-email', 'verify')->name('verification.verify');
        Route::post('verify-email/resend', 'resend')->name('verification.resend');
        
        // Email Verification for Google
        Route::get('verify-google', 'showGoogleVerifyForm')->name('verification.google.notice');
        Route::post('verify-google', 'verifyGoogle')->name('verification.google.verify');
        Route::post('verify-google/resend', 'resendGoogle')->name('verification.google.resend');
        
        // Password Reset Routes
        Route::get('forgot-password', 'showForgotPasswordForm')->name('password.request');
        Route::post('forgot-password', 'sendResetCode')->name('password.email');
        Route::get('verify-reset-code', 'showVerifyResetCodeForm')->name('password.verify.form');
        Route::post('verify-reset-code', 'verifyResetCode')->name('password.verify');
        Route::get('reset-password', 'showResetPasswordForm')->name('password.reset.form');
        Route::post('reset-password', 'resetPassword')->name('password.update');
        Route::post('verify-reset-code/resend', 'resendResetCode')->name('password.resend');
    });

    // Rute Logout (Hanya untuk yang sudah login)
    Route::middleware('auth')->group(function () {
        Route::post('logout', 'logout')->name('logout');
    });
});


// --- GRUP UNTUK SEMUA HALAMAN YANG SUDAH LOGIN ---
Route::middleware(['auth'])->group(function () {
    
    // Rute Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        // Use DashboardService for comprehensive statistics
        $dashboardService = new \App\Services\DashboardService();
        $stats = $dashboardService->getStatistics($user);
        
        // Tambahkan data untuk view Tailwind baru
        // Map stats ke format yang diharapkan oleh view baru
        $stats['omset'] = $stats['revenue_this_month'] ?? 0;
        $stats['total_order'] = \App\Domains\Order\Models\Order::count();
        $stats['pesanan_hari_ini'] = $stats['orders_today'] ?? 0;
        $stats['pelanggan_aktif'] = $stats['total_customers'] ?? 0;
        
        // Get recent orders (5 terbaru)
        $recentOrders = \App\Domains\Order\Models\Order::with('customer')
            ->latest()
            ->limit(5)
            ->get();
        
        // Get WhatsApp Connection Status
        $waService = new \App\Services\WaService();
        $waStatus = $waService->getConnectionStatus();
        
        return view('dashboard', compact('user', 'stats', 'recentOrders', 'waStatus'));
    })->name('dashboard');

    // Global Search
    Route::get('/search', function (Illuminate\Http\Request $request) {
        $query = $request->input('q');
        
        if (empty($query)) {
            return back()->with('info', 'Silakan masukkan kata kunci pencarian.');
        }
        
        // Search across multiple models
        $customers = \App\Domains\Customer\Models\Customer::where('name', 'ILIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();
            
        $products = \App\Domains\Product\Models\Product::where('name', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get();
            
        $orders = \App\Domains\Order\Models\Order::where('order_number', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();
        
        // For now, redirect to customers if found, products if found, else back
        if ($customers->isNotEmpty()) {
            return redirect()->route('customers.index')->with('search_results', $customers)->with('query', $query);
        } elseif ($products->isNotEmpty()) {
            return redirect()->route('product.index')->with('search_results', $products)->with('query', $query);
        } elseif ($orders->isNotEmpty()) {
            return redirect()->route('orders.index')->with('search_results', $orders)->with('query', $query);
        }
        
        return back()->with('warning', "Tidak ada hasil untuk '{$query}'");
    })->name('search.global');

    // ======================================================
    // ADMIN CS MANAGEMENT ROUTES
    // ======================================================
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::resource('cs', \App\Http\Controllers\Admin\CSController::class)->names([
            'index' => 'admin.cs.index',
            'create' => 'admin.cs.create',
            'store' => 'admin.cs.store',
            'edit' => 'admin.cs.edit',
            'update' => 'admin.cs.update',
            'destroy' => 'admin.cs.destroy',
        ]);
        // Existing WhatsApp API routes
        Route::get('/whatsapp/api/status', [WhatsAppConnectionController::class, 'getStatus'])
            ->name('admin.whatsapp.api.status');
        Route::get('/whatsapp/api/qr', [WhatsAppConnectionController::class, 'getQrCode'])
            ->name('admin.whatsapp.api.qr');
        Route::post('/whatsapp/api/reconnect', [WhatsAppConnectionController::class, 'requestReconnect'])
            ->name('admin.whatsapp.api.reconnect');

        // **New route for the QR‑code page (admin UI)**
        Route::get('/whatsapp/scan', [WhatsAppConnectionController::class, 'index'])
            ->name('whatsapp.scan');

        // Laporan Bisnis (Epik 1.5)
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
            ->name('admin.reports.index');
        Route::get('/reports/export-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPDF'])
            ->name('admin.reports.export-pdf');

        // Integrasi API (Epik 1.4 Docs)
        Route::get('/api-integration', [\App\Http\Controllers\Admin\ApiIntegrationController::class, 'index'])
            ->name('admin.api.index');
        Route::post('/api-integration/generate-key', [\App\Http\Controllers\Admin\ApiIntegrationController::class, 'generateApiKey'])
            ->name('admin.api.generate-key');

        // Manajemen Template Pesan (Epik 1.2)
        Route::resource('templates', \App\Http\Controllers\Admin\MessageTemplateController::class)
            ->names('admin.templates');
    });

    // ======================================================
    // CS DASHBOARD ROUTE
    // ======================================================
    Route::middleware(['role:cs'])->group(function () {
        Route::get('/cs/dashboard', [\App\Http\Controllers\CS\CSDashboardController::class, 'index'])->name('cs.dashboard');
        
        // Toggle Status
        Route::post('/cs/status/toggle', [\App\Http\Controllers\CS\CsStatusController::class, 'toggle'])->name('cs.status.toggle');
    });

    // ======================================================
    // BLOK KODE DOMAIN CHAT (DIGABUNGKAN KE SINI)
    // ======================================================
    Route::prefix('app')->middleware([
        // [PERBAIKAN]
        // Mengubah 'check.role' menjadi 'role'
        // Ini untuk menyamakan dengan alias di bootstrap/app.php
        'role:admin,cs' 
    ])->group(function () {
        
        // Muat Rute Domain Chat
        // Baris inilah yang mendefinisikan 'chat.index'
        require __DIR__.'/../app/Domains/Chat/routes/web.php';

        // Muat Rute Domain Customer
        require __DIR__.'/../app/Domains/Customer/routes/web.php';

        // Muat Rute Domain Order
        require __DIR__.'/../app/Domains/Order/routes/web.php';

        // Muat Rute Domain Product
        require __DIR__.'/../app/Domains/Product/routes/web.php';

        // Muat Rute Domain Reminder
        require __DIR__.'/../app/Domains/Reminder/routes/web.php';

        // Muat Rute Domain Automation (Otomasi)
        require __DIR__.'/../app/Domains/Automation/routes/web.php';



        // Muat Rute Domain Broadcast (Otomasi Pesan)
        require __DIR__.'/../app/Domains/Broadcast/routes/web.php';

    });
    // --- AKHIR DARI BLOK KODE BARU ---

    
    // Test Supabase Connection (opsional)
    Route::get('/test-supabase', function () {
        try {
            $pdo = DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            
            return response()->json([
                'status' => 'success',
                'message' => '✅ Koneksi Supabase berhasil!',
                'database' => $dbName,
                'tables' => collect($tables)->pluck('table_name'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ Error koneksi: ' . $e->getMessage(),
            ], 500);
        }
    });

}); // <-- Ini adalah penutup penting untuk grup Route::middleware(['auth'])

Route::middleware(['web'])->group(function () {
    
    // 2. AJAX Endpoints untuk Chatting (PENTING!)
    Route::get('/chat/rooms', [ChatController::class, 'getRooms']); // Polling List
    Route::get('/room/{id}/data', [ChatController::class, 'getRoomData']); // Load pesan
    Route::post('/room/{id}/send-ajax', [ChatController::class, 'storeAjaxMessage']); // Kirim pesan
    Route::post('/start-chat', [ChatController::class, 'startChat'])->name('chat.start'); // New Route
    Route::get('/chat/notifications', [ChatController::class, 'getNotifications']); // Notifikasi Pesan Baru
    Route::post('/chat/customer/{id}/update', [ChatController::class, 'updateCustomer']); // Update Customer dari Chat

    // 3. API Koneksi WhatsApp (Status & QR)
    // Pastikan URL ini bisa diakses (tanpa middleware admin dulu untuk testing)
    Route::get('/admin/whatsapp/api/status', [WhatsAppConnectionController::class, 'getStatus'])->name('admin.whatsapp.api.status');
    Route::get('/admin/whatsapp/api/qr', [WhatsAppConnectionController::class, 'getQrCode'])->name('admin.whatsapp.api.qr');
    Route::post('/admin/whatsapp/api/reconnect', [WhatsAppConnectionController::class, 'requestReconnect'])->name('admin.whatsapp.api.reconnect');
    Route::post('/admin/whatsapp/api/start', [WhatsAppConnectionController::class, 'start'])->name('admin.whatsapp.api.start');
    Route::post('/admin/whatsapp/api/disconnect', [WhatsAppConnectionController::class, 'disconnect'])->name('admin.whatsapp.api.disconnect');
    Route::post('/admin/whatsapp/api/clear-chats', [WhatsAppConnectionController::class, 'clearAllChats'])->name('admin.whatsapp.api.clear-chats');

});