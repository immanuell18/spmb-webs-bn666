<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = [
        'email',
        'otp_code',
        'expires_at',
        'is_verified'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public static function generateOtp($email)
    {
        // Hapus OTP lama untuk email ini
        self::where('email', $email)->delete();
        
        // Generate OTP 6 digit
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        return self::create([
            'email' => $email,
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(5),
            'is_verified' => false
        ]);
    }

    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    public static function verifyOtp($email, $otpCode)
    {
        $otp = self::where('email', $email)
                   ->where('otp_code', $otpCode)
                   ->where('is_verified', false)
                   ->first();

        if (!$otp || $otp->isExpired()) {
            return false;
        }

        $otp->update(['is_verified' => true]);
        return true;
    }
}