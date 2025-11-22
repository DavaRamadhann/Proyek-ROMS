<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthControllers extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Generate kode verifikasi 6 digit
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan data pendaftaran di session (belum buat user)
        session([
            'register_data' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'code' => $code,
                'expires_at' => now()->addMinutes(15)->timestamp, // Expiration 15 menit
            ],
        ]);

        // Kirim email verifikasi
        try {
            Mail::to($validated['email'])->send(new VerificationCodeMail($code, $validated['name']));
        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email verifikasi. Silakan coba lagi.']);
        }

        return redirect()->route('verification.notice')->with('status', 'Kode verifikasi telah dikirim ke email Anda. Silakan cek inbox atau spam folder.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek apakah credentials valid
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Cek apakah email sudah diverifikasi
            if (!$user->email_verified_at) {
                Auth::logout(); // Logout user yang belum terverifikasi
                return back()->withErrors([
                    'email' => 'Email Anda belum terverifikasi. Silakan verifikasi email Anda terlebih dahulu.',
                ])->onlyInput('email');
            }
            
            $request->session()->regenerate();
            return redirect()->intended('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda telah logout.');
    }

    // =================== EMAIL VERIFICATION METHODS (FOR REGISTER) ===================

    /**
     * Tampilkan halaman verifikasi email setelah register
     */
    public function showVerifyForm()
    {
        $registerData = session('register_data');
        
        if (!$registerData) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi verifikasi tidak ditemukan. Silakan daftar kembali.']);
        }

        $email = $registerData['email'];
        $verificationType = 'register';

        return view('auth.verify-token', compact('email', 'verificationType'));
    }

    /**
     * Proses verifikasi kode untuk registrasi
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.required' => 'Kode verifikasi harus diisi.',
            'code.size' => 'Kode verifikasi harus 6 digit.',
            'code.regex' => 'Kode verifikasi harus berupa angka.',
        ]);

        $registerData = session('register_data');
        
        if (!$registerData) {
            return redirect()->route('register')->withErrors(['email' => 'Sesi verifikasi tidak ditemukan.']);
        }

        // Cek expiration
        if (isset($registerData['expires_at']) && $registerData['expires_at'] < now()->timestamp) {
            session()->forget('register_data');
            return redirect()->route('register')->withErrors(['email' => 'Kode verifikasi telah kadaluarsa. Silakan daftar kembali.']);
        }

        // Verifikasi kode dari session
        if ($registerData['code'] !== $request->code) {
            return back()->withErrors(['code' => 'Kode verifikasi salah. Silakan coba lagi.']);
        }

        // Verifikasi berhasil - buat user baru (BARU DIBUAT DI SINI!)
        $user = User::create([
            'name' => $registerData['name'],
            'email' => $registerData['email'],
            'password' => Hash::make($registerData['password']),
            'role' => 'admin', // Default role
            'email_verified_at' => now(), // Langsung terverifikasi
        ]);
        
        // Hapus session register data
        session()->forget('register_data');

        // Login user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '🎉 Email berhasil diverifikasi! Selamat datang di ROMS.');
    }

    /**
     * Kirim ulang kode verifikasi untuk registrasi
     */
    public function resend(Request $request)
    {
        $registerData = session('register_data');
        
        if (!$registerData) {
            return redirect()->route('register')->withErrors(['email' => 'Sesi verifikasi tidak ditemukan.']);
        }

        $email = $registerData['email'];
        $name = $registerData['name'];

        // Generate kode baru
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update session dengan kode baru
        session([
            'register_data' => [
                'name' => $name,
                'email' => $email,
                'password' => $registerData['password'],
                'code' => $code,
                'expires_at' => now()->addMinutes(15)->timestamp, // Expiration baru
            ],
        ]);

        // Kirim email
        try {
            Mail::to($email)->send(new VerificationCodeMail($code, $name));
            return back()->with('status', '✉️ Kode verifikasi baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            Log::error('Resend verification failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi.']);
        }
    }

    // --- [METODE BARU UNTUK GOOGLE AUTH] ---

    /**
     * Arahkan pengguna ke halaman autentikasi Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Dapatkan informasi pengguna dari Google dan tangani login/register.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. Cari pengguna berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // User sudah ada - cek apakah email sudah terverifikasi
                if (!$user->email_verified_at) {
                    // Belum terverifikasi - kirim OTP
                    return $this->sendGoogleVerificationCode($googleUser, $user);
                }
                
                // Sudah terverifikasi - update google_id jika belum ada
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
                
                // Login
                Auth::login($user);
                return redirect()->intended('dashboard')->with('success', 'Login berhasil!');
            }

            // User baru - kirim OTP untuk verifikasi
            return $this->sendGoogleVerificationCode($googleUser, null);

        } catch (\Exception $e) {
            // Jika ada error, kembali ke login
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan Google: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Kirim kode verifikasi untuk Google OAuth
     */
    private function sendGoogleVerificationCode($googleUser, $existingUser = null)
    {
        // Generate kode verifikasi 6 digit
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan data Google di session
        session([
            'google_user_data' => [
                'google_id' => $googleUser->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'existing_user_id' => $existingUser ? $existingUser->id : null,
                'code' => $code,
                'expires_at' => now()->addMinutes(15)->timestamp, // Expiration 15 menit
            ],
        ]);

        // Kirim email verifikasi
        try {
            Mail::to($googleUser->email)->send(new VerificationCodeMail($code, $googleUser->name));
        } catch (\Exception $e) {
            Log::error('Google verification email failed: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Gagal mengirim email verifikasi. Silakan coba lagi.']);
        }

        return redirect()->route('verification.google.notice')->with('status', 'Kode verifikasi telah dikirim ke email Anda. Silakan cek inbox atau spam folder.');
    }

    /**
     * Tampilkan halaman verifikasi untuk Google OAuth
     */
    public function showGoogleVerifyForm()
    {
        $googleData = session('google_user_data');
        
        if (!$googleData) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi verifikasi tidak ditemukan. Silakan login dengan Google lagi.']);
        }

        $email = $googleData['email'];
        $verificationType = 'google';

        return view('auth.verify-token', compact('email', 'verificationType'));
    }

    /**
     * Proses verifikasi kode untuk Google OAuth
     */
    public function verifyGoogle(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.required' => 'Kode verifikasi harus diisi.',
            'code.size' => 'Kode verifikasi harus 6 digit.',
            'code.regex' => 'Kode verifikasi harus berupa angka.',
        ]);

        $googleData = session('google_user_data');
        
        if (!$googleData) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi verifikasi tidak ditemukan.']);
        }

        // Cek expiration
        if (isset($googleData['expires_at']) && $googleData['expires_at'] < now()->timestamp) {
            session()->forget('google_user_data');
            return redirect()->route('login')->withErrors(['email' => 'Kode verifikasi telah kadaluarsa. Silakan login dengan Google lagi.']);
        }

        // Verifikasi kode dari session
        if ($googleData['code'] !== $request->code) {
            return back()->withErrors(['code' => 'Kode verifikasi salah. Silakan coba lagi.']);
        }

        // Verifikasi berhasil
        if ($googleData['existing_user_id']) {
            // Update user yang sudah ada
            $user = User::find($googleData['existing_user_id']);
            $user->google_id = $googleData['google_id'];
            $user->email_verified_at = now();
            $user->save();
        } else {
            // Buat user baru (BARU DIBUAT DI SINI!)
            $user = User::create([
                'google_id' => $googleData['google_id'],
                'name' => $googleData['name'],
                'email' => $googleData['email'],
                'password' => Hash::make(Str::random(16)),
                'role' => 'admin',
                'email_verified_at' => now(), // Langsung terverifikasi
            ]);
        }
        
        // Hapus session
        session()->forget('google_user_data');

        // Login user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '🎉 Email berhasil diverifikasi! Selamat datang di ROMS.');
    }

    /**
     * Kirim ulang kode verifikasi untuk Google OAuth
     */
    public function resendGoogle(Request $request)
    {
        $googleData = session('google_user_data');
        
        if (!$googleData) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi verifikasi tidak ditemukan.']);
        }

        $email = $googleData['email'];
        $name = $googleData['name'];

        // Generate kode baru
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update session dengan kode baru
        session([
            'google_user_data' => [
                'google_id' => $googleData['google_id'],
                'name' => $name,
                'email' => $email,
                'existing_user_id' => $googleData['existing_user_id'],
                'code' => $code,
                'expires_at' => now()->addMinutes(15)->timestamp, // Expiration baru
            ],
        ]);

        // Kirim email
        try {
            Mail::to($email)->send(new VerificationCodeMail($code, $name));
            return back()->with('status', '✉️ Kode verifikasi baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            Log::error('Resend Google verification failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi.']);
        }
    }

    // =================== PASSWORD RESET METHODS ===================

    /**
     * Tampilkan halaman forgot password (input email)
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses pengiriman kode reset password ke email
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar di sistem kami.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate kode verifikasi 6 digit
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update user dengan kode reset
        $user->reset_code = $code;
        $user->reset_code_expires_at = now()->addMinutes(15);
        $user->save();

        // Kirim email reset password
        try {
            Mail::to($request->email)->send(new PasswordResetMail($code, $user->name));
        } catch (\Exception $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi.']);
        }

        // Simpan email di session untuk proses verifikasi
        session(['reset_password_email' => $request->email]);

        return redirect()->route('password.verify.form')->with('status', 'Kode verifikasi telah dikirim ke email Anda. Silakan cek inbox atau spam folder.');
    }

    /**
     * Tampilkan halaman verifikasi kode reset password
     */
    public function showVerifyResetCodeForm()
    {
        $email = session('reset_password_email');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi reset password tidak ditemukan. Silakan mulai lagi.']);
        }

        return view('auth.verify-token', [
            'email' => $email,
            'verificationType' => 'reset_password'
        ]);
    }

    /**
     * Verifikasi kode reset password
     */
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.required' => 'Kode verifikasi harus diisi.',
            'code.size' => 'Kode verifikasi harus 6 digit.',
            'code.regex' => 'Kode verifikasi harus berupa angka.',
        ]);

        $email = session('reset_password_email');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi reset password tidak ditemukan.']);
        }

        // Cari user
        $user = User::where('email', $email)->first();

        if (!$user || $user->reset_code !== $request->code) {
            return back()->withErrors(['code' => 'Kode verifikasi salah. Silakan coba lagi.']);
        }

        // Cek apakah kode sudah kadaluarsa
        if ($user->isResetCodeExpired()) {
            return back()->withErrors(['code' => 'Kode verifikasi telah kadaluarsa. Silakan kirim ulang kode baru.']);
        }

        // Verifikasi berhasil - simpan status di session
        session(['reset_code_verified' => true]);

        return redirect()->route('password.reset.form')->with('status', 'Verifikasi berhasil! Silakan masukkan password baru Anda.');
    }

    /**
     * Tampilkan halaman reset password (input password baru)
     */
    public function showResetPasswordForm()
    {
        $email = session('reset_password_email');
        $verified = session('reset_code_verified');
        
        if (!$email || !$verified) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi reset password tidak valid. Silakan mulai lagi.']);
        }

        return view('auth.reset-password', compact('email'));
    }

    /**
     * Proses reset password (simpan password baru)
     */
    public function resetPassword(Request $request)
    {
        $email = session('reset_password_email');
        $verified = session('reset_code_verified');
        
        if (!$email || !$verified) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi reset password tidak valid. Silakan mulai lagi.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'Password baru harus diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // Update password user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'User tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        
        // Pastikan email_verified_at tetap ada atau diset jika belum ada
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }
        
        // Clear reset code
        $user->reset_code = null;
        $user->reset_code_expires_at = null;
        
        $user->save();

        // Hapus session
        session()->forget(['reset_password_email', 'reset_code_verified']);

        return redirect()->route('login')->with('success', '🎉 Password berhasil direset! Silakan login dengan password baru Anda.');
    }

    /**
     * Kirim ulang kode reset password
     */
    public function resendResetCode(Request $request)
    {
        $email = session('reset_password_email');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi reset password tidak ditemukan.']);
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Generate kode baru
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update user dengan kode baru
        $user->reset_code = $code;
        $user->reset_code_expires_at = now()->addMinutes(15);
        $user->save();

        // Kirim email
        try {
            Mail::to($email)->send(new PasswordResetMail($code, $user->name));
            return back()->with('status', '✉️ Kode verifikasi baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            Log::error('Resend reset code failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi.']);
        }
    }
}