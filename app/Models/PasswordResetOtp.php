<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetOtp extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    public static function generateOtp($email)
    {
        // Delete old OTPs for this email
        self::where('email', $email)->delete();
        
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        return self::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10), // 10 minutes expiry
            'is_used' => false
        ]);
    }

    public function isExpired()
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }

    public static function verifyOtp($email, $otp)
    {
        $otpRecord = self::where('email', $email)
                        ->where('otp', $otp)
                        ->where('is_used', false)
                        ->first();

        if (!$otpRecord) {
            \Log::info('OTP not found', ['email' => $email, 'otp' => $otp]);
            return false;
        }

        if ($otpRecord->isExpired()) {
            \Log::info('OTP expired', ['email' => $email, 'otp' => $otp, 'expires_at' => $otpRecord->expires_at]);
            return false;
        }

        $otpRecord->update(['is_used' => true]);
        \Log::info('OTP verified successfully', ['email' => $email, 'otp' => $otp]);
        return true;
    }
}