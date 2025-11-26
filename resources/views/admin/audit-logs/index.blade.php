@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">🔍 Audit Logs</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($stats['total_logs']) }}</h4>
                    <small>Total Logs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($stats['today_logs']) }}</h4>
                    <small>Today's Logs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($stats['suspicious_logs']) }}</h4>
                    <small>Suspicious Activities</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($stats['high_severity']) }}</h4>
                    <small>High Severity</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-2">
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="action" class="form-control" placeholder="Action" value="{{ request('action') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="severity" class="form-select">
                                <option value="">All Severity</option>
                                <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.audit-logs.export.excel', request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('admin.audit-logs.export.pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <label class="form-check ms-3">
                    <input type="checkbox" class="form-check-input" onchange="toggleSuspicious(this)">
                    <span class="form-check-label">Show Suspicious Only</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr class="{{ $log->is_suspicious ? 'table-warning' : '' }}">
                                    <td>
                                        <small>{{ $log->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $log->user->name ?? 'System' }}</strong>
                                    </td>
                                    <td>
                                        <code>{{ $log->action }}</code>
                                    </td>
                                    <td>
                                        {{ Str::limit($log->description, 50) }}
                                    </td>
                                    <td>
                                        <small>{{ $log->ip_address }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->severity == 'high' ? 'danger' : ($log->severity == 'medium' ? 'warning' : 'info') }}">
                                            {{ strtoupper($log->severity) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->is_suspicious)
                                            <span class="badge bg-danger">Suspicious</span>
                                        @else
                                            <span class="badge bg-success">Normal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No logs found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleSuspicious(checkbox) {
    const url = new URL(window.location);
    if (checkbox.checked) {
        url.searchParams.set('suspicious', '1');
    } else {
        url.searchParams.delete('suspicious');
    }
    window.location.href = url.toString();
}
</script>
@endsection