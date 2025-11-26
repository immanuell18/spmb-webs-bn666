<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log authenticated requests
        if (auth()->check()) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    private function logRequest($request, $response)
    {
        // Skip logging for certain routes
        $skipRoutes = [
            'api/map/data',
            'api/dashboard',
            'csrf-token',
            'refresh-token'
        ];

        foreach ($skipRoutes as $route) {
            if (str_contains($request->path(), $route)) {
                return;
            }
        }

        // Determine action based on method and route
        $action = $this->determineAction($request);
        
        // Detect suspicious activity
        $isSuspicious = $this->detectSuspiciousActivity($request);

        // Log the activity
        AuditLog::logActivity([
            'action' => $action,
            'description' => $this->generateDescription($request, $action),
            'request_data' => $this->sanitizeRequestData($request),
            'severity' => $this->determineSeverity($request, $action),
            'is_suspicious' => $isSuspicious
        ]);
    }

    private function determineAction($request)
    {
        $method = $request->method();
        $path = $request->path();

        if (str_contains($path, 'login')) return 'login_attempt';
        if (str_contains($path, 'logout')) return 'logout';
        if (str_contains($path, 'upload') || $request->hasFile('file')) return 'file_upload';
        if (str_contains($path, 'download') || str_contains($path, 'export')) return 'file_download';
        if (str_contains($path, 'verifikasi')) return 'verification_action';
        if (str_contains($path, 'pembayaran')) return 'payment_action';

        return match($method) {
            'POST' => 'create_action',
            'PUT', 'PATCH' => 'update_action',
            'DELETE' => 'delete_action',
            default => 'view_action'
        };
    }

    private function detectSuspiciousActivity($request)
    {
        // Multiple rapid requests
        $recentLogs = AuditLog::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subMinutes(1))
            ->count();

        if ($recentLogs > 20) return true;

        // Failed login attempts
        if (str_contains($request->path(), 'login') && $request->method() === 'POST') {
            $failedAttempts = AuditLog::where('user_id', auth()->id())
                ->where('action', 'login_attempt')
                ->where('created_at', '>=', now()->subHour())
                ->count();

            if ($failedAttempts > 5) return true;
        }

        // Unusual IP address
        $userIps = AuditLog::where('user_id', auth()->id())
            ->distinct()
            ->pluck('ip_address')
            ->toArray();

        if (count($userIps) > 3 && !in_array($request->ip(), $userIps)) {
            return true;
        }

        return false;
    }

    private function sanitizeRequestData($request)
    {
        $data = $request->all();
        
        // Remove sensitive data
        $sensitiveFields = ['password', 'password_confirmation', 'token', '_token'];
        foreach ($sensitiveFields as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    private function generateDescription($request, $action)
    {
        $user = auth()->user();
        $path = $request->path();

        return "User {$user->name} melakukan {$action} pada {$path}";
    }

    private function determineSeverity($request, $action)
    {
        $highSeverityActions = ['delete_action', 'verification_action', 'payment_action'];
        $mediumSeverityActions = ['create_action', 'update_action', 'file_upload'];

        if (in_array($action, $highSeverityActions)) return 'high';
        if (in_array($action, $mediumSeverityActions)) return 'medium';

        return 'low';
    }
}