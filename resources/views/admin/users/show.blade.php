@extends('layouts.admin')

@section('title', 'Detail Akun')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Akun: {{ $user->name }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama Lengkap</th>
                                    <td>: {{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>: {{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>: <span class="badge bg-info">{{ ucfirst($user->role) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>: 
                                        @if($user->status)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Dibuat</th>
                                    <td>: {{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Diupdate</th>
                                    <td>: {{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Email Verified</th>
                                    <td>: 
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Verified</span>
                                            <br><small>{{ $user->email_verified_at->format('d/m/Y H:i:s') }}</small>
                                        @else
                                            <span class="badge bg-warning">Not Verified</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn {{ $user->status ? 'btn-secondary' : 'btn-success' }}" 
                                onclick="return confirm('Yakin ingin {{ $user->status ? 'menonaktifkan' : 'mengaktifkan' }} akun ini?')">
                            <i class="fas fa-{{ $user->status ? 'ban' : 'check' }}"></i> 
                            {{ $user->status ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection