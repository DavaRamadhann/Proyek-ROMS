<?php

use App\Http\Controllers\Auth\AuthControllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\RedirectIfAuthenticated;

// Root redirect
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::controller(AuthControllers::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('register', 'showRegisterForm')->name('register');
        Route::get('register', 'showRegisterForm')->name('register');
        Route::post('register', 'register');
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

    Route::middleware('auth')->group(function () {
        Route::post('logout', 'logout')->name('logout');
    });
});

// Dashboard - only for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');
});

// Test Supabase Connection (optional - bisa dihapus jika tidak perlu)
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