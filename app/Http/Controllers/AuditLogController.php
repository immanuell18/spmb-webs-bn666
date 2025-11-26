<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Exports\AuditLogExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('suspicious')) {
            $query->where('is_suspicious', true);
        }

        $logs = $query->paginate(50);
        $users = User::select('id', 'name')->get();

        // Statistics
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'suspicious_logs' => AuditLog::where('is_suspicious', true)->count(),
            'high_severity' => AuditLog::where('severity', 'high')->count(),
        ];

        return view('admin.audit-logs.index', compact('logs', 'users', 'stats'));
    }

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        return view('admin.audit-logs.show', compact('log'));
    }

    public function loginAttempts(Request $request)
    {
        $query = AuditLog::where('action', 'login_attempt')
            ->orWhere('action', 'login_failed')
            ->with('user')
            ->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $attempts = $query->paginate(30);

        return view('admin.audit-logs.login-attempts', compact('attempts'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['user_id', 'action', 'severity', 'date_from', 'date_to']);
        
        return Excel::download(new AuditLogExport($filters), 'audit_logs_' . date('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(1000)->get(); // Limit for PDF performance

        $pdf = Pdf::loadView('admin.audit-logs.pdf', compact('logs'));
        return $pdf->download('audit_logs_' . date('Y-m-d') . '.pdf');
    }

    public function dashboard()
    {
        // Security dashboard with key metrics
        $metrics = [
            'total_activities' => AuditLog::count(),
            'suspicious_activities' => AuditLog::where('is_suspicious', true)->count(),
            'failed_logins_today' => AuditLog::where('action', 'login_failed')
                ->whereDate('created_at', today())->count(),
            'unique_users_today' => AuditLog::whereDate('created_at', today())
                ->distinct('user_id')->count('user_id'),
        ];

        // Recent suspicious activities
        $suspiciousActivities = AuditLog::where('is_suspicious', true)
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Activity by hour (last 24 hours)
        $hourlyActivity = AuditLog::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Top actions
        $topActions = AuditLog::selectRaw('action, COUNT(*) as count')
            ->whereDate('created_at', today())
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('admin.security-dashboard', compact(
            'metrics', 'suspiciousActivities', 'hourlyActivity', 'topActions'
        ));
    }
}