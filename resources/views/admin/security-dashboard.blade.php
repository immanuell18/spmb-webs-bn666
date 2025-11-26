@extends('layouts.admin')

@section('title', 'Security Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">🛡️ Security Dashboard</h1>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($metrics['total_activities']) }}</h4>
                    <small>Total Activities</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($metrics['suspicious_activities']) }}</h4>
                    <small>Suspicious Activities</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($metrics['failed_logins_today']) }}</h4>
                    <small>Failed Logins Today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($metrics['unique_users_today']) }}</h4>
                    <small>Active Users Today</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>📊 Activity by Hour (Last 24 Hours)</h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>🎯 Top Actions Today</h5>
                </div>
                <div class="card-body">
                    @foreach($topActions as $action)
                    <div class="d-flex justify-content-between mb-2">
                        <small><code>{{ $action->action }}</code></small>
                        <span class="badge bg-primary">{{ $action->count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Suspicious Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">⚠️ Recent Suspicious Activities</h5>
                </div>
                <div class="card-body">
                    @if($suspiciousActivities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                        <th>Severity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suspiciousActivities as $activity)
                                    <tr>
                                        <td>
                                            <small>{{ $activity->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $activity->user->name ?? 'Unknown' }}</strong>
                                        </td>
                                        <td>
                                            <code>{{ $activity->action }}</code>
                                        </td>
                                        <td>
                                            {{ Str::limit($activity->description, 60) }}
                                        </td>
                                        <td>
                                            <small>{{ $activity->ip_address }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $activity->severity == 'high' ? 'danger' : 'warning' }}">
                                                {{ strtoupper($activity->severity) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-shield-alt fa-3x mb-3"></i>
                            <p>No suspicious activities detected recently</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Hourly Activity Chart
const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
new Chart(hourlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($hourlyActivity->pluck('hour')->map(fn($h) => $h . ':00')) !!},
        datasets: [{
            label: 'Activities',
            data: {!! json_encode($hourlyActivity->pluck('count')) !!},
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
@endsection