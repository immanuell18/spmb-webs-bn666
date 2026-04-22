@extends('layouts.admin')

@section('title', 'Kelola Gelombang Pendaftaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 fw-bold">Kelola Gelombang Pendaftaran</h1>
        <a href="{{ route('admin.gelombang.create') }}" class="btn btn-primary shadow-sm">
            <i class="ti ti-plus pe-1"></i> Tambah Gelombang
        </a>
    </div>

    <x-flash-message />

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h6 class="m-0 fw-bold text-muted">Daftar Gelombang Pendaftaran</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Gelombang</th>
                            <th>Tahun</th>
                            <th>Periode</th>
                            <th>Biaya Daftar</th>
                            <th>Status</th>
                            <th>Pendaftar</th>
                            <th class="pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gelombang as $index => $g)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $g->nama }}</td>
                            <td>{{ $g->tahun }}</td>
                            <td>
                                {{ $g->tgl_mulai->format('d/m/Y') }} <span class="text-muted mx-1">-</span> 
                                {{ $g->tgl_selesai->format('d/m/Y') }}
                            </td>
                            <td><strong>Rp {{ number_format($g->biaya_daftar, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($g->status === 'aktif')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">{{ $g->pendaftar->count() }} orang</span>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.gelombang.edit', $g) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.gelombang.toggle-status', $g) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-{{ $g->status === 'aktif' ? 'secondary' : 'success' }} btn-sm" title="{{ $g->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="ti ti-{{ $g->status === 'aktif' ? 'player-pause' : 'player-play' }}"></i>
                                        </button>
                                    </form>
                                    @if($g->pendaftar->count() === 0)
                                    <form action="{{ route('admin.gelombang.destroy', $g) }}" method="POST" onsubmit="return confirm('Yakin hapus gelombang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="ti ti-folder-off fs-1 d-block mb-2"></i>
                                Belum ada gelombang pendaftaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection