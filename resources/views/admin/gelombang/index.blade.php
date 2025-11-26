@extends('layouts.admin')

@section('title', 'Kelola Gelombang Pendaftaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Gelombang Pendaftaran</h1>
        <a href="{{ route('admin.gelombang.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Gelombang
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Gelombang Pendaftaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Gelombang</th>
                            <th>Tahun</th>
                            <th>Periode</th>
                            <th>Biaya Daftar</th>
                            <th>Status</th>
                            <th>Pendaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gelombang as $index => $g)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $g->nama }}</td>
                            <td>{{ $g->tahun }}</td>
                            <td>
                                {{ $g->tgl_mulai->format('d/m/Y') }} - 
                                {{ $g->tgl_selesai->format('d/m/Y') }}
                            </td>
                            <td>Rp {{ number_format($g->biaya_daftar, 0, ',', '.') }}</td>
                            <td>
                                @if($g->status === 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $g->pendaftar->count() }} orang</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.gelombang.edit', $g) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.gelombang.toggle-status', $g) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-{{ $g->status === 'aktif' ? 'secondary' : 'success' }} btn-sm">
                                            <i class="fas fa-{{ $g->status === 'aktif' ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    @if($g->pendaftar->count() === 0)
                                    <form action="{{ route('admin.gelombang.destroy', $g) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus gelombang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada gelombang pendaftaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection