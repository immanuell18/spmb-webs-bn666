<?php

namespace App\Services;

use App\Models\Pendaftar;
use App\Models\PaymentTransaction;
use App\Models\Jurusan;
use App\Models\Gelombang;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    public function getKpiData()
    {
        return Cache::remember('dashboard_kpi', 300, function () {
            $totalPendaftar = Pendaftar::count();
            $targetKuota = 500; // Set target
            
            return [
                'total_pendaftar' => $totalPendaftar,
                'target_kuota' => $targetKuota,
                'progress_percentage' => $targetKuota > 0 ? number_format(($totalPendaftar / $targetKuota) * 100, 1) : '0.0',
                'sudah_verifikasi' => Pendaftar::where('status', 'ADM_PASS')->count(),
                'sudah_bayar' => Pendaftar::where('status', 'PAID')->count(),
                'menunggu_verifikasi' => Pendaftar::where('status', 'SUBMIT')->count(),
                'ditolak' => Pendaftar::where('status', 'ADM_REJECT')->count(),
                'conversion_rate' => $totalPendaftar > 0 ? number_format((Pendaftar::where('status', 'PAID')->count() / $totalPendaftar) * 100, 1) : '0.0',
                'total_revenue' => Pendaftar::where('status', 'PAID')->sum('biaya_pendaftaran') ?: 
                                 Pendaftar::join('gelombang', 'pendaftar.gelombang_id', '=', 'gelombang.id')
                                         ->sum('gelombang.biaya_daftar'),
            ];
        });
    }

    public function getRegistrationTrend($days = 30)
    {
        return Cache::remember("registration_trend_{$days}", 300, function () use ($days) {
            $startDate = Carbon::now()->subDays($days);
            
            $data = Pendaftar::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            $labels = [];
            $values = [];
            
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::parse($date)->format('d/m');
                
                $dayData = $data->firstWhere('date', $date);
                $values[] = $dayData ? $dayData->count : 0;
            }

            return [
                'labels' => $labels,
                'data' => $values,
                'total' => array_sum($values)
            ];
        });
    }

    public function getJurusanDistribution()
    {
        return Cache::remember('jurusan_distribution', 300, function () {
            $data = Jurusan::withCount('pendaftar')
                          ->having('pendaftar_count', '>', 0)
                          ->get();

            return [
                'labels' => $data->pluck('nama')->toArray(),
                'data' => $data->pluck('pendaftar_count')->toArray(),
                'colors' => $this->generateColors($data->count())
            ];
        });
    }

    public function getPaymentAnalytics()
    {
        return Cache::remember('payment_analytics', 300, function () {
            $totalPendaftar = Pendaftar::whereIn('status', ['ADM_PASS', 'PAID'])->count();
            $paidTransactions = Pendaftar::where('status', 'PAID')->count();
            $pendingTransactions = Pendaftar::where('status', 'ADM_PASS')->count();
            $failedTransactions = Pendaftar::where('status', 'ADM_REJECT')->count();

            return [
                'total_transactions' => $totalPendaftar,
                'paid_transactions' => $paidTransactions,
                'pending_transactions' => $pendingTransactions,
                'failed_transactions' => $failedTransactions,
                'success_rate' => $totalPendaftar > 0 ? number_format(($paidTransactions / $totalPendaftar) * 100, 1) : '0.0',
                'revenue_trend' => $this->getRevenueTrend(),
                'payment_methods' => $this->getPaymentMethodDistribution()
            ];
        });
    }

    public function getGeographicDistribution()
    {
        return Cache::remember('geographic_distribution', 300, function () {
            // Get distribution by jurusan since geographic data might not be available
            $data = Jurusan::withCount('pendaftar')
                          ->having('pendaftar_count', '>', 0)
                          ->orderBy('pendaftar_count', 'desc')
                          ->limit(10)
                          ->get();

            return [
                'labels' => $data->pluck('nama')->toArray(),
                'data' => $data->pluck('pendaftar_count')->toArray(),
                'coordinates' => [] // No coordinates available
            ];
        });
    }

    public function getPerformanceMetrics()
    {
        return Cache::remember('performance_metrics', 600, function () {
            $avgProcessingTime = $this->calculateAverageProcessingTime();
            $systemUsage = $this->getSystemUsageStats();
            
            return [
                'avg_processing_time' => $avgProcessingTime,
                'system_usage' => $systemUsage,
                'active_users_today' => $this->getActiveUsersToday(),
                'error_rate' => $this->getErrorRate(),
                'database_size' => $this->getDatabaseSize()
            ];
        });
    }

    public function getExecutiveSummary()
    {
        return Cache::remember('executive_summary', 300, function () {
            $kpi = $this->getKpiData();
            $trend = $this->getRegistrationTrend(7);
            $payment = $this->getPaymentAnalytics();
            
            return [
                'kpi' => $kpi,
                'weekly_trend' => $trend,
                'payment_summary' => [
                    'total_revenue' => $payment['revenue_trend']['total'] ?? 0,
                    'success_rate' => $payment['success_rate'],
                    'pending_amount' => Pendaftar::where('status', 'ADM_PASS')->sum('biaya_pendaftaran')
                ],
                'top_jurusan' => Jurusan::withCount('pendaftar')->orderBy('pendaftar_count', 'desc')->limit(3)->get(),
                'alerts' => $this->generateAlerts($kpi, $payment)
            ];
        });
    }

    private function getRevenueTrend($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        // Try to get revenue from paid registrations first
        $data = Pendaftar::select(
            DB::raw('DATE(tanggal_pembayaran) as date'),
            DB::raw('SUM(biaya_pendaftaran) as revenue')
        )
        ->where('status', 'PAID')
        ->whereNotNull('tanggal_pembayaran')
        ->where('tanggal_pembayaran', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        // If no payment data, use registration data with gelombang biaya_daftar
        if ($data->isEmpty()) {
            $data = Pendaftar::join('gelombang', 'pendaftar.gelombang_id', '=', 'gelombang.id')
                ->select(
                    DB::raw('DATE(pendaftar.created_at) as date'),
                    DB::raw('SUM(gelombang.biaya_daftar) as revenue')
                )
                ->where('pendaftar.created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        $labels = [];
        $values = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d/m');
            
            $dayData = $data->firstWhere('date', $date);
            $values[] = $dayData ? (int)$dayData->revenue : 0;
        }

        return [
            'labels' => $labels,
            'data' => $values,
            'total' => array_sum($values)
        ];
    }

    private function getPaymentMethodDistribution()
    {
        $data = Pendaftar::join('gelombang', 'pendaftar.gelombang_id', '=', 'gelombang.id')
                        ->select('gelombang.nama as method', DB::raw('COUNT(*) as count'))
                        ->where('pendaftar.status', 'PAID')
                        ->groupBy('gelombang.nama')
                        ->get();

        return [
            'labels' => $data->pluck('method')->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }

    private function calculateAverageProcessingTime()
    {
        // Calculate real processing time based on recent registrations
        $recentRegistrations = Pendaftar::where('created_at', '>=', Carbon::now()->subHours(24))
                                      ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_time')
                                      ->first();
        
        $avgSeconds = $recentRegistrations->avg_time ?? 0;
        return $avgSeconds > 0 ? round($avgSeconds * 1000) . 'ms' : '0ms';
    }

    private function getSystemUsageStats()
    {
        return [
            'cpu_usage' => 'N/A',
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 1) . ' MB',
            'disk_usage' => $this->getDiskUsage()
        ];
    }

    private function getActiveUsersToday()
    {
        // Count users who logged in today or have recent activity
        return DB::table('users')
                ->whereDate('updated_at', Carbon::today())
                ->count();
    }

    private function getErrorRate()
    {
        // Calculate error rate based on rejected applications
        $totalSubmissions = Pendaftar::count();
        $rejectedSubmissions = Pendaftar::where('status', 'ADM_REJECT')->count();
        
        if ($totalSubmissions === 0) return '0%';
        
        return number_format(($rejectedSubmissions / $totalSubmissions) * 100, 1) . '%';
    }

    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'size_mb' FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            return ($result[0]->size_mb ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function generateAlerts($kpi, $payment)
    {
        $alerts = [];
        
        if ($kpi['progress_percentage'] > 90) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Kuota pendaftaran hampir penuh (' . $kpi['progress_percentage'] . '%)'
            ];
        }
        
        if ($payment['success_rate'] < 80) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Success rate pembayaran rendah (' . $payment['success_rate'] . '%)'
            ];
        }
        
        if ($kpi['menunggu_verifikasi'] > 50) {
            $alerts[] = [
                'type' => 'info',
                'message' => $kpi['menunggu_verifikasi'] . ' pendaftar menunggu verifikasi'
            ];
        }

        return $alerts;
    }

    private function generateColors($count)
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
        ];
        
        return array_slice($colors, 0, $count);
    }

    private function getDiskUsage()
    {
        try {
            $bytes = disk_free_space('/');
            $total = disk_total_space('/');
            if ($bytes !== false && $total !== false) {
                $used = $total - $bytes;
                $percentage = round(($used / $total) * 100, 1);
                return $percentage . '%';
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    public function clearCache()
    {
        $keys = [
            'dashboard_kpi', 'registration_trend_30', 'registration_trend_7',
            'jurusan_distribution', 'payment_analytics', 'geographic_distribution',
            'performance_metrics', 'executive_summary'
        ];
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}