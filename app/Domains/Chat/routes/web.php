<?php

use App\Http\Controllers\Auth\AuthControllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domains\Chat\Http\Controllers\ChatController;
use App\Domains\Chat\Http\Controllers\WhatsAppConnectionController;

/*
|--------------------------------------------------------------------------
| Web Routes (FINAL FIXED)
|--------------------------------------------------------------------------
*/

// --- 1. ROOT REDIRECT ---
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// --- 2. GRUP OTENTIKASI (Login, Register, dll) ---
Route::controller(AuthControllers::class)->group(function () {
    
    Route::middleware('guest')->group(function () {
        // Login & Register
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', 'login');
        Route::get('register', 'showRegisterForm')->name('register');
        Route::post('register', 'register');
        
        // Google OAuth
        Route::get('auth/google/redirect', 'redirectToGoogle')->name('google.redirect');
        Route::get('auth/google/callback', 'handleGoogleCallback')->name('google.callback');
        
        // Password Reset & Verify Email (Standard Laravel)
        Route::get('forgot-password', 'showForgotPasswordForm')->name('password.request');
        Route::post('forgot-password', 'sendResetCode')->name('password.email');
        Route::get('verify-reset-code', 'showVerifyResetCodeForm')->name('password.verify.form');
        Route::post('verify-reset-code', 'verifyResetCode')->name('password.verify');
        Route::get('reset-password', 'showResetPasswordForm')->name('password.reset.form');
        Route::post('reset-password', 'resetPassword')->name('password.update');
        
        Route::get('verify-email', 'showVerifyForm')->name('verification.notice');
        Route::post('verify-email', 'verify')->name('verification.verify');
        Route::post('verify-email/resend', 'resend')->name('verification.resend');
        
        Route::get('verify-google', 'showGoogleVerifyForm')->name('verification.google.notice');
        Route::post('verify-google', 'verifyGoogle')->name('verification.google.verify');
        Route::post('verify-google/resend', 'resendGoogle')->name('verification.google.resend');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', 'logout')->name('logout');
    });
});

// --- 3. APLIKASI UTAMA (Harus Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Umum User
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');

    // ======================================================
    // DOMAIN CHAT & CS (PREFIX: /app)
    // ======================================================
    Route::prefix('app')->middleware(['role:admin,cs'])->group(function () {

        // 1. Dashboard Statistik CS
        Route::get('/cs/dashboard', [ChatController::class, 'index'])
            ->name('chat.dashboard');

        // 2. Pintu Masuk: Halaman Koneksi & QR
        Route::get('/chat/connect', [ChatController::class, 'showConnectionPage'])
            ->name('chat.connect');

        // 3. Halaman Utama: UI Chat (Aplikasi Chat 3 Kolom)
        Route::get('/chat/ui', [ChatController::class, 'showChatUI'])
            ->name('chat.ui');

        // 4. AJAX Endpoints (Data & Kirim Pesan)
        Route::get('/chat/room/{roomId}/data', [ChatController::class, 'getRoomData'])
            ->name('chat.room.data');
        
        Route::post('/chat/room/{roomId}/send-ajax', [ChatController::class, 'storeAjaxMessage'])
            ->name('chat.room.send-ajax');

    });

    // ======================================================
    // API INTERNAL WHATSAPP (Untuk Cek Status JS)
    // ======================================================
    Route::prefix('admin/whatsapp/api')
         ->middleware(['role:admin,cs'])
         ->name('admin.whatsapp.api.')
         ->group(function () {
            Route::get('/status', [WhatsAppConnectionController::class, 'getStatus'])->name('status');
            Route::get('/qr', [WhatsAppConnectionController::class, 'getQrCode'])->name('qr');
            Route::post('/reconnect', [WhatsAppConnectionController::class, 'requestReconnect'])->name('reconnect');
    });

    // Test Database (Opsional)
    Route::get('/test-supabase', function () {
        return response()->json(['status' => 'ok', 'db' => DB::connection()->getDatabaseName()]);
    });

});