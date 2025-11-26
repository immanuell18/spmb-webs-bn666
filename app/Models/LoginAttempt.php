<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'successful',
        'failure_reason',
        'user_agent',
        'attempted_at'
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime'
    ];

    public static function logAttempt($email, $successful, $failureReason = null)
    {
        return self::create([
            'email' => $email,
            'ip_address' => request()->ip(),
            'successful' => $successful,
            'failure_reason' => $failureReason,
            'user_agent' => request()->userAgent(),
            'attempted_at' => now()
        ]);
    }

    public static function getFailedAttempts($email, $minutes = 15)
    {
        return self::where('email', $email)
                  ->where('successful', false)
                  ->where('attempted_at', '>=', now()->subMinutes($minutes))
                  ->count();
    }

    public static function getRecentFailedAttempts($ip, $minutes = 15)
    {
        return self::where('ip_address', $ip)
                  ->where('successful', false)
                  ->where('attempted_at', '>=', now()->subMinutes($minutes))
                  ->count();
    }
}