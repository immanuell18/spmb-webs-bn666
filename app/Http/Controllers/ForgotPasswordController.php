<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordResetOtp;
use App\Mail\OtpMail;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.'
        ]);

        // Check if user is pendaftar (not admin/staff)
        $user = User::where('email', $request->email)->first();
        if ($user->role !== 'pendaftar') {
            return back()->with('error', 'Fitur lupa password hanya tersedia untuk pendaftar.');
        }

        // Generate and send OTP
        $otpRecord = PasswordResetOtp::generateOtp($request->email);
        
        try {
            Mail::to($request->email)->send(new OtpMail($otpRecord->otp, $user->name));
            
            // Store email in session
            session(['email' => $request->email]);
            
            return redirect()->route('password.verify-otp')
                           ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox atau spam folder.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim OTP. Silakan coba lagi.');
        }
    }

    public function showVerifyOtpForm()
    {
        if (!session('email')) {
            return redirect()->route('password.request')
                           ->with('error', 'Session expired. Silakan mulai ulang proses reset password.');
        }
        
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ], [
            'otp.required' => 'Kode OTP harus diisi.',
            'otp.size' => 'Kode OTP harus 6 digit.'
        ]);

        $email = session('email');
        \Log::info('Verifying OTP', ['email' => $email, 'otp' => $request->otp]);
        
        if (!$email) {
            \Log::error('No email in session for OTP verification');
            return redirect()->route('password.request')
                           ->with('error', 'Session expired. Silakan mulai ulang proses reset password.');
        }

        // Verifikasi OTP
        if (PasswordResetOtp::verifyOtp($email, $request->otp)) {
            // Store email in session for reset password form
            session(['verified_email' => $email]);
            \Log::info('OTP verification successful, redirecting to reset password');
            
            return redirect()->route('password.reset')
                           ->with('success', 'OTP berhasil diverifikasi. Silakan buat password baru.');
        }

        // OTP salah atau kadaluarsa
        \Log::error('OTP verification failed', ['email' => $email, 'otp' => $request->otp]);
        return back()->with('error', 'Kode OTP salah atau sudah kadaluarsa. Silakan periksa kembali kode yang Anda masukkan.');
    }

    public function showResetForm()
    {
        if (!session('verified_email')) {
            return redirect()->route('password.request')
                           ->with('error', 'Akses tidak valid. Silakan mulai ulang proses reset password.');
        }
        
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $email = session('verified_email');
        if (!$email) {
            return redirect()->route('password.request')
                           ->with('error', 'Session expired. Silakan mulai ulang proses reset password.');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')
                           ->with('error', 'User tidak ditemukan.');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Clear all sessions
        session()->forget(['email', 'verified_email']);

        return redirect()->route('login')
                       ->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}