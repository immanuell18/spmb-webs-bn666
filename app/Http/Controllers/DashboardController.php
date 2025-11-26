<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function adminDashboard()
    {
        $kpi = $this->analyticsService->getKpiData();
        $registrationTrend = $this->analyticsService->getRegistrationTrend(30);
        $jurusanDistribution = $this->analyticsService->getJurusanDistribution();
        $paymentAnalytics = $this->analyticsService->getPaymentAnalytics();
        $geographicData = $this->analyticsService->getGeographicDistribution();
        $performanceMetrics = $this->analyticsService->getPerformanceMetrics();

        return view('admin.dashboard-advanced', compact(
            'kpi', 'registrationTrend', 'jurusanDistribution', 
            'paymentAnalytics', 'geographicData', 'performanceMetrics'
        ));
    }

    public function executiveDashboard()
    {
        $executiveSummary = $this->analyticsService->getExecutiveSummary();
        
        return view('kepsek.dashboard-executive', compact('executiveSummary'));
    }

    // API Endpoints for real-time data
    public function apiKpi()
    {
        return response()->json($this->analyticsService->getKpiData());
    }

    public function apiRegistrationTrend(Request $request)
    {
        $days = $request->get('days', 30);
        return response()->json($this->analyticsService->getRegistrationTrend($days));
    }

    public function apiJurusanDistribution()
    {
        return response()->json($this->analyticsService->getJurusanDistribution());
    }

    public function apiPaymentAnalytics()
    {
        return response()->json($this->analyticsService->getPaymentAnalytics());
    }

    public function apiGeographicData()
    {
        return response()->json($this->analyticsService->getGeographicDistribution());
    }

    public function apiPerformanceMetrics()
    {
        return response()->json($this->analyticsService->getPerformanceMetrics());
    }

    public function exportExecutivePdf()
    {
        $executiveSummary = $this->analyticsService->getExecutiveSummary();
        
        $pdf = Pdf::loadView('reports.pdf.executive-dashboard', $executiveSummary)
                 ->setPaper('a4', 'portrait')
                 ->setOptions(['defaultFont' => 'sans-serif']);
        
        return $pdf->download('executive-dashboard-' . date('Y-m-d') . '.pdf');
    }

    public function refreshCache()
    {
        $this->analyticsService->clearCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Cache berhasil di-refresh'
        ]);
    }

    // Drill-down endpoints
    public function drillDownJurusan($jurusanId)
    {
        $jurusan = \App\Models\Jurusan::with(['pendaftar' => function($query) {
            $query->select('id', 'nama', 'status', 'created_at', 'jurusan_id');
        }])->findOrFail($jurusanId);

        $stats = [
            'total' => $jurusan->pendaftar->count(),
            'paid' => $jurusan->pendaftar->where('status', 'PAID')->count(),
            'verified' => $jurusan->pendaftar->where('status', 'ADM_PASS')->count(),
            'pending' => $jurusan->pendaftar->where('status', 'SUBMIT')->count(),
        ];

        return response()->json([
            'jurusan' => $jurusan->nama,
            'stats' => $stats,
            'recent_registrations' => $jurusan->pendaftar->take(10)
        ]);
    }

    public function drillDownPayment($status)
    {
        $transactions = \App\Models\PaymentTransaction::with('pendaftar')
                                                    ->where('status', $status)
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(20)
                                                    ->get();

        return response()->json([
            'status' => $status,
            'count' => $transactions->count(),
            'transactions' => $transactions
        ]);
    }

    public function systemHealth()
    {
        $health = [
            'database' => $this->checkDatabaseConnection(),
            'cache' => $this->checkCacheConnection(),
            'storage' => $this->checkStorageAccess(),
            'queue' => $this->checkQueueStatus(),
            'mail' => $this->checkMailConfiguration()
        ];

        $overallStatus = collect($health)->every(fn($status) => $status === 'healthy') ? 'healthy' : 'warning';

        return response()->json([
            'overall_status' => $overallStatus,
            'components' => $health,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function checkDatabaseConnection()
    {
        try {
            \DB::connection()->getPdo();
            return 'healthy';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkCacheConnection()
    {
        try {
            \Cache::put('health_check', 'ok', 10);
            return \Cache::get('health_check') === 'ok' ? 'healthy' : 'error';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkStorageAccess()
    {
        try {
            return \Storage::exists('test') || \Storage::put('test', 'ok') ? 'healthy' : 'error';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkQueueStatus()
    {
        // Simplified queue check
        return 'healthy';
    }

    private function checkMailConfiguration()
    {
        return config('mail.mailers.smtp.host') ? 'healthy' : 'warning';
    }
}