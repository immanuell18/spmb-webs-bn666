<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Otp;
use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check for too many failed attempts
        $failedAttempts = LoginAttempt::getFailedAttempts($credentials['email']);
        if ($failedAttempts >= 5) {
            LoginAttempt::logAttempt($credentials['email'], false, 'Too many failed attempts');
            return back()->withErrors([
                'email' => 'Terlalu banyak percobaan login gagal. Coba lagi dalam 15 menit.',
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Log successful login
            LoginAttempt::logAttempt($credentials['email'], true);
            AuditLog::logActivity([
                'action' => 'login',
                'description' => "User {$user->name} ({$user->email}) berhasil login",
                'severity' => 'medium'
            ]);
            
            // Redirect berdasarkan role
            switch ($user->role) {
                case User::ROLE_ADMIN:
                    return redirect()->route('admin.dashboard');
                case User::ROLE_KEUANGAN:
                    return redirect()->route('keuangan.dashboard');
                case User::ROLE_KEPSEK:
                    return redirect()->route('kepsek.dashboard');
                case User::ROLE_VERIFIKATOR_ADM:
                    return redirect()->route('verifikator.dashboard');
                case User::ROLE_PENDAFTAR:
                    return redirect()->route('siswa.dashboard');
                default:
                    return redirect()->route('siswa.dashboard');
            }
        }

        // Log failed login
        LoginAttempt::logAttempt($credentials['email'], false, 'Invalid credentials');
        AuditLog::logActivity([
            'action' => 'login_failed',
            'description' => "Failed login attempt for email: {$credentials['email']}",
            'severity' => 'high',
            'is_suspicious' => $failedAttempts >= 3
        ]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Simpan data sementara di session
        session([
            'temp_user' => [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]
        ]);

        // Generate dan kirim OTP
        $otp = Otp::generateOtp($request->email);
        
        try {
            Mail::to($request->email)->send(new OtpMail($otp->otp_code, $request->name));
        } catch (\Exception $e) {
            \Log::error('Email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email OTP. Silakan coba lagi.']);
        }
        
        return redirect()->route('otp.verify')->with('email', $request->email);
    }

    public function showOtpVerify()
    {
        if (!session('temp_user')) {
            return redirect()->route('register')->with('error', 'Session expired. Silakan daftar ulang.');
        }
        return view('auth.verify-otp-register');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6'
        ], [
            'otp_code.required' => 'Kode OTP harus diisi.',
            'otp_code.size' => 'Kode OTP harus 6 digit.'
        ]);

        $tempUser = session('temp_user');
        if (!$tempUser) {
            return redirect()->route('register')->with('error', 'Session expired. Silakan daftar ulang.');
        }

        // Verifikasi OTP
        if (Otp::verifyOtp($tempUser['email'], $request->otp_code)) {
            // Buat user setelah OTP terverifikasi
            $user = User::create([
                'name' => $tempUser['name'],
                'email' => $tempUser['email'],
                'password' => $tempUser['password'],
                'role' => User::ROLE_PENDAFTAR,
                'email_verified_at' => now()
            ]);

            // Hapus data sementara
            session()->forget('temp_user');
            
            Auth::login($user);
            return redirect()->route('siswa.dashboard')->with('success', 'Akun berhasil dibuat dan diverifikasi!');
        }

        // OTP salah atau kadaluarsa
        return back()->with('error', 'Kode OTP salah atau sudah kadaluarsa. Silakan periksa kembali kode yang Anda masukkan.');
    }

    public function resendOtp(Request $request)
    {
        $tempUser = session('temp_user');
        if (!$tempUser) {
            return response()->json(['error' => 'Session expired'], 400);
        }

        $otp = Otp::generateOtp($tempUser['email']);
        Mail::to($tempUser['email'])->send(new OtpMail($otp->otp_code, $tempUser['name']));
        
        return response()->json(['success' => 'OTP baru telah dikirim']);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout
        if ($user) {
            AuditLog::logActivity([
                'action' => 'logout',
                'description' => "User {$user->name} ({$user->email}) logout",
                'severity' => 'low'
            ]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}