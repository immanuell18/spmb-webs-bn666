@extends('layouts.admin')

@section('title', 'Kelola Akun')

@push('styles')
<style>
.avatar {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}
.dropdown-item {
    padding: 8px 16px;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Kelola Akun Pengguna</h3>
                            <p class="text-muted mb-0">Total: {{ $users->count() }} akun | Aktif: {{ $users->where('status', true)->count() }} | Nonaktif: {{ $users->where('status', false)->count() }}</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus"></i> Tambah Akun
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="20%">Nama</th>
                                    <th width="25%">Email</th>
                                    <th width="15%" class="text-center">Role</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="15%" class="text-center">Dibuat</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm rounded-circle bg-primary text-white me-2">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        @php
                                            $roleColors = [
                                                'admin' => 'danger',
                                                'keuangan' => 'success', 
                                                'verifikator' => 'info',
                                                'kepsek' => 'warning'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($user->status)
                                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>Aktif</span>
                                        @else
                                            <span class="badge bg-danger"><i class="ti ti-x me-1"></i>Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small>{{ $user->created_at->format('d/m/Y') }}</small><br>
                                        <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i> Aksi
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                                        <i class="ti ti-eye me-2"></i>Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                                        <i class="ti ti-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item text-{{ $user->status ? 'warning' : 'success' }}" 
                                                                onclick="return confirm('Yakin ingin {{ $user->status ? 'menonaktifkan' : 'mengaktifkan' }} akun ini?')">
                                                            <i class="ti ti-{{ $user->status ? 'ban' : 'check' }} me-2"></i>{{ $user->status ? 'Nonaktifkan' : 'Aktifkan' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Yakin ingin menghapus akun ini? Data yang dihapus tidak dapat dikembalikan!')">
                                                            <i class="ti ti-trash me-2"></i>Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data akun</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection