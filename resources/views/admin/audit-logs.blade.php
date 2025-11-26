@extends('layouts.admin')

@section('title', 'Audit Log - SPMB Admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Audit Log Sistem</h5>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Tabel</th>
                            <th>Record ID</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                            <td>{{ $log->user_name ?? '-' }}</td>
                            <td>
                                @if($log->action == 'CREATE')
                                    <span class="badge bg-success">{{ $log->action }}</span>
                                @elseif($log->action == 'UPDATE')
                                    <span class="badge bg-warning">{{ $log->action }}</span>
                                @elseif($log->action == 'DELETE')
                                    <span class="badge bg-danger">{{ $log->action }}</span>
                                @else
                                    <span class="badge bg-info">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td>{{ $log->table_name ?? '-' }}</td>
                            <td>{{ $log->record_id ?? '-' }}</td>
                            <td>{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada log audit</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($logs, 'links'))
                {{ $logs->links() }}
            @endif
        </div>
    </div>
</div>
@endsection