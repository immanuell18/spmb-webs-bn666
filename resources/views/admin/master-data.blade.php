@extends('layouts.admin')

@section('title', 'Master Data - SPMB Admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Master Data SPMB</h5>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="masterDataTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="jurusan-tab" data-bs-toggle="tab" data-bs-target="#jurusan" type="button" role="tab">Jurusan</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="gelombang-tab" data-bs-toggle="tab" data-bs-target="#gelombang" type="button" role="tab">Gelombang</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="wilayah-tab" data-bs-toggle="tab" data-bs-target="#wilayah" type="button" role="tab">Wilayah</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="biaya-tab" data-bs-toggle="tab" data-bs-target="#biaya" type="button" role="tab">Persyaratan</button>
                </li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content mt-4" id="masterDataTabContent">
                <!-- Jurusan Tab -->
                <div class="tab-pane fade show active" id="jurusan" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Data Jurusan</h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJurusanModal">
                            <i class="ti ti-plus"></i> Tambah Jurusan
                        </button>
                    </div>
                    

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Jurusan</th>
                                    <th>Kuota</th>
                                    <th>Pendaftar</th>
                                    <th>Sisa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jurusan ?? [] as $j)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $j->kode }}</span></td>
                                    <td>{{ $j->nama }}</td>
                                    <td>{{ $j->kuota }}</td>
                                    <td>{{ $j->jumlah_pendaftar }}</td>
                                    <td>
                                        <span class="badge bg-{{ $j->is_kuota_penuh ? 'danger' : 'success' }}">
                                            {{ $j->sisa_kuota }}
                                            {{ $j->is_kuota_penuh ? ' (PENUH)' : '' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editJurusanModal{{ $j->id }}">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.jurusan.delete', $j->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data jurusan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Gelombang Tab -->
                <div class="tab-pane fade" id="gelombang" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Data Gelombang Pendaftaran</h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGelombangModal">
                            <i class="ti ti-plus"></i> Tambah Gelombang
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tahun</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Biaya Daftar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gelombang ?? [] as $g)
                                <tr>
                                    <td>{{ $g->nama }}</td>
                                    <td>{{ $g->tahun }}</td>
                                    <td>{{ $g->tgl_mulai->format('d M Y') }}</td>
                                    <td>{{ $g->tgl_selesai->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($g->biaya_daftar, 0, ',', '.') }}</td>
                                    <td>
                                        @if($g->status === 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editGelombangModal{{ $g->id }}">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.gelombang.toggle-status', $g->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-{{ $g->status === 'aktif' ? 'secondary' : 'success' }}" title="{{ $g->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="ti ti-{{ $g->status === 'aktif' ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        @if($g->pendaftar->count() === 0)
                                        <form action="{{ route('admin.gelombang.delete', $g->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data gelombang</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Wilayah Tab -->
                <div class="tab-pane fade" id="wilayah" role="tabpanel">
                    <!-- Nav tabs untuk wilayah -->
                    <ul class="nav nav-pills mb-3" id="wilayahTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="provinces-tab" data-bs-toggle="pill" data-bs-target="#provinces" type="button" role="tab">Provinsi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="regencies-tab" data-bs-toggle="pill" data-bs-target="#regencies" type="button" role="tab">Kabupaten/Kota</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="districts-tab" data-bs-toggle="pill" data-bs-target="#districts" type="button" role="tab">Kecamatan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="villages-tab" data-bs-toggle="pill" data-bs-target="#villages" type="button" role="tab">Kelurahan</button>
                        </li>
                    </ul>
                    
                    <!-- Tab content -->
                    <div class="tab-content" id="wilayahTabContent">
                        <!-- Provinsi -->
                        <div class="tab-pane fade show active" id="provinces" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Data Provinsi</h6>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control" id="searchProvinsi" placeholder="Cari provinsi..." style="width: 200px;">
                                    <button type="button" class="btn btn-outline-primary" onclick="filterProvinsi()">
                                        <i class="ti ti-search"></i> Cari
                                    </button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProvinsiModal">
                                        <i class="ti ti-plus"></i> Tambah Provinsi
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="provinsiTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Provinsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($provinces ?? [] as $p)
                                        <tr>
                                            <td>{{ $p->id }}</td>
                                            <td>{{ $p->name }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProvinsiModal{{ $p->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <a href="/admin/wilayah/{{ $p->id }}/delete?type=province" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus provinsi ini?')">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada data provinsi</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Kabupaten -->
                        <div class="tab-pane fade" id="regencies" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Data Kabupaten/Kota</h6>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control" id="searchKabupaten" placeholder="Cari kabupaten..." style="width: 200px;">
                                    <select class="form-select" id="filterProvinsiKab" style="width: 200px;">
                                        <option value="">Semua Provinsi</option>
                                        @foreach($provinces ?? [] as $province)
                                            <option value="{{ $province->name }}">{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" onclick="filterKabupaten()">
                                        <i class="ti ti-search"></i> Cari
                                    </button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKabupatenModal">
                                        <i class="ti ti-plus"></i> Tambah Kabupaten/Kota
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="kabupatenTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Kabupaten/Kota</th>
                                            <th>Provinsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($regencies ?? [] as $r)
                                        <tr>
                                            <td>{{ $r->id }}</td>
                                            <td>{{ $r->name }}</td>
                                            <td>{{ $r->province->name ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRegencyModal{{ $r->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <a href="/admin/wilayah/{{ $r->id }}/delete?type=regency" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kabupaten/kota ini?')">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada data kabupaten/kota</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Kecamatan -->
                        <div class="tab-pane fade" id="districts" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Data Kecamatan</h6>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control" id="searchKecamatan" placeholder="Cari kecamatan..." style="width: 200px;">
                                    <select class="form-select" id="filterKabupatenKec" style="width: 200px;">
                                        <option value="">Semua Kabupaten</option>
                                        @foreach($regencies ?? [] as $regency)
                                            <option value="{{ $regency->name }}">{{ $regency->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" onclick="filterKecamatan()">
                                        <i class="ti ti-search"></i> Cari
                                    </button>
                                    <button type="button" class="btn btn-primary">
                                        <i class="ti ti-plus"></i> Tambah Kecamatan
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="kecamatanTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Kecamatan</th>
                                            <th>Kabupaten/Kota</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($districts ?? [] as $d)
                                        <tr>
                                            <td>{{ $d->id }}</td>
                                            <td>{{ $d->name }}</td>
                                            <td>{{ $d->regency->name ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editDistrictModal{{ $d->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <a href="/admin/wilayah/{{ $d->id }}/delete?type=district" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kecamatan ini?')">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada data kecamatan</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Kelurahan -->
                        <div class="tab-pane fade" id="villages" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Data Kelurahan</h6>
                                <button type="button" class="btn btn-primary">
                                    <i class="ti ti-plus"></i> Tambah Kelurahan
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Kelurahan</th>
                                            <th>Kecamatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($villages ?? [] as $v)
                                        <tr>
                                            <td>{{ $v->id }}</td>
                                            <td>{{ $v->name }}</td>
                                            <td>{{ $v->district->name ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editVillageModal{{ $v->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <a href="/admin/wilayah/{{ $v->id }}/delete?type=village" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kelurahan ini?')">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada data kelurahan</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Persyaratan Tab -->
                <div class="tab-pane fade" id="biaya" role="tabpanel">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Persyaratan Berkas Pendaftaran</h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="mb-3">Daftar Persyaratan</h6>
                                    <div class="list-group" id="persyaratanList">
                                        @forelse($persyaratan ?? [] as $p)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $p->nama }}</strong>
                                                @if($p->deskripsi)
                                                <br><small class="text-muted">{{ $p->deskripsi }}</small>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-{{ $p->wajib ? 'primary' : 'secondary' }}">{{ $p->wajib ? 'Wajib' : 'Opsional' }}</span>
                                                <span class="badge bg-info">{{ ucfirst($p->jenis) }}</span>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="list-group-item text-center">
                                            Belum ada persyaratan yang ditambahkan
                                        </div>
                                        @endforelse
                                    </div>
                                    <div class="alert alert-info mt-3">
                                        <i class="ti ti-info-circle me-2"></i>
                                        <strong>Catatan:</strong> Biaya pendaftaran diatur per gelombang di tab Gelombang. Persyaratan ini akan muncul di halaman pendaftaran dan beranda.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jurusan -->
<div class="modal fade" id="addJurusanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.jurusan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan</label>
                        <input type="text" class="form-control" name="kode" placeholder="Contoh: PPLG, AKT, ANM" required maxlength="10">
                        <small class="form-text text-muted">Masukkan kode unik untuk jurusan (maksimal 10 karakter)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang jurusan ini"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kuota</label>
                        <input type="number" class="form-control" name="kuota" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Jurusan -->
@foreach($jurusan ?? [] as $j)
<div class="modal fade" id="editJurusanModal{{ $j->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.jurusan.update', $j->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan</label>
                        <input type="text" class="form-control" name="kode" value="{{ $j->kode }}" required maxlength="10">
                        <small class="form-text text-muted">Kode unik untuk jurusan (maksimal 10 karakter)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" class="form-control" name="nama" value="{{ $j->nama }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3">{{ $j->deskripsi }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kuota</label>
                        <input type="number" class="form-control" name="kuota" value="{{ $j->kuota }}" required min="1">
                    </div>
                    <div class="alert alert-info">
                        <strong>Info Kuota:</strong><br>
                        Pendaftar saat ini: {{ $j->jumlah_pendaftar }}<br>
                        Sisa kuota: {{ $j->sisa_kuota }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Tambah Gelombang -->
<div class="modal fade" id="addGelombangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.gelombang.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Gelombang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Gelombang</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" name="tahun" value="2024" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tgl_mulai" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tgl_selesai" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Pendaftaran</label>
                        <input type="number" class="form-control" name="biaya_daftar" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="aktif">Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Gelombang -->
@foreach($gelombang ?? [] as $g)
<div class="modal fade" id="editGelombangModal{{ $g->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.gelombang.update', $g->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Gelombang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Gelombang</label>
                        <input type="text" class="form-control" name="nama" value="{{ $g->nama }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" name="tahun" value="{{ $g->tahun }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tgl_mulai" value="{{ $g->tgl_mulai->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tgl_selesai" value="{{ $g->tgl_selesai->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Pendaftaran</label>
                        <input type="number" class="form-control" name="biaya_daftar" value="{{ $g->biaya_daftar }}" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="nonaktif" {{ $g->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="aktif" {{ $g->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Tambah Provinsi -->
<div class="modal fade" id="addProvinsiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wilayah.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Provinsi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" value="province">
                    <div class="mb-3">
                        <label class="form-label">ID Provinsi</label>
                        <input type="text" class="form-control" name="province_id" placeholder="Contoh: 11" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Provinsi</label>
                        <input type="text" class="form-control" name="province_name" placeholder="Contoh: ACEH" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Kabupaten -->
<div class="modal fade" id="addKabupatenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wilayah.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kabupaten/Kota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" value="regency">
                    <div class="mb-3">
                        <label class="form-label">ID Kabupaten/Kota</label>
                        <input type="text" class="form-control" name="regency_id" placeholder="Contoh: 1101" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provinsi</label>
                        <select class="form-select" name="province_id" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces ?? [] as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kabupaten/Kota</label>
                        <input type="text" class="form-control" name="regency_name" placeholder="Contoh: KABUPATEN ACEH SELATAN" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal Edit Provinsi -->
@foreach($provinces ?? [] as $p)
<div class="modal fade" id="editProvinsiModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wilayah.update', $p->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="province">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Provinsi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Provinsi</label>
                        <input type="text" class="form-control" name="province_name" value="{{ $p->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Edit Regency -->
@foreach($regencies ?? [] as $r)
<div class="modal fade" id="editRegencyModal{{ $r->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wilayah.update', $r->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="regency">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kabupaten/Kota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kabupaten/Kota</label>
                        <input type="text" class="form-control" name="regency_name" value="{{ $r->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Edit District -->
@foreach($districts ?? [] as $d)
<div class="modal fade" id="editDistrictModal{{ $d->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wilayah.update', $d->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="district">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kecamatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kecamatan</label>
                        <input type="text" class="form-control" name="district_name" value="{{ $d->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Edit Village -->
@foreach($villages ?? [] as $v)
<div class="modal fade" id="editVillageModal{{ $v->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wilayah.update', $v->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="village">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kelurahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelurahan</label>
                        <input type="text" class="form-control" name="village_name" value="{{ $v->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Tambah Persyaratan -->
<div class="modal fade" id="addPersyaratanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.persyaratan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Persyaratan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Persyaratan</label>
                        <input type="text" class="form-control" name="nama" required placeholder="Contoh: Fotokopi Ijazah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="2" placeholder="Deskripsi tambahan (opsional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select class="form-select" name="jenis" required>
                            <option value="dokumen">Dokumen</option>
                            <option value="foto">Foto</option>
                            <option value="sertifikat">Sertifikat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="wajib" value="1" id="wajib">
                            <label class="form-check-label" for="wajib">
                                Persyaratan Wajib
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" class="form-control" name="urutan" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Persyaratan -->
@foreach($persyaratan ?? [] as $p)
<div class="modal fade" id="editPersyaratanModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.persyaratan.update', $p->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Persyaratan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Persyaratan</label>
                        <input type="text" class="form-control" name="nama" value="{{ $p->nama }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="2">{{ $p->deskripsi }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select class="form-select" name="jenis" required>
                            <option value="dokumen" {{ $p->jenis == 'dokumen' ? 'selected' : '' }}>Dokumen</option>
                            <option value="foto" {{ $p->jenis == 'foto' ? 'selected' : '' }}>Foto</option>
                            <option value="sertifikat" {{ $p->jenis == 'sertifikat' ? 'selected' : '' }}>Sertifikat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="wajib" value="1" id="wajib{{ $p->id }}" {{ $p->wajib ? 'checked' : '' }}>
                            <label class="form-check-label" for="wajib{{ $p->id }}">
                                Persyaratan Wajib
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" class="form-control" name="urutan" value="{{ $p->urutan }}" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
// Filter Provinsi
function filterProvinsi() {
    const searchValue = document.getElementById('searchProvinsi').value.toLowerCase();
    const table = document.getElementById('provinsiTable');
    
    if (!table) {
        console.log('Table provinsiTable not found');
        return;
    }
    
    const tbody = table.getElementsByTagName('tbody')[0];
    if (!tbody) {
        console.log('Tbody not found');
        return;
    }
    
    const rows = tbody.getElementsByTagName('tr');
    console.log('Found', rows.length, 'rows in provinsi table');
    console.log('Search value:', searchValue);
    
    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        if (cells.length === 0) continue; // Skip empty rows
        
        const text = rows[i].textContent.toLowerCase();
        const matches = !searchValue || text.includes(searchValue);
        
        console.log('Row', i, '- Text:', text, '- Matches:', matches);
        
        rows[i].style.display = matches ? '' : 'none';
    }
}

// Filter Kabupaten
function filterKabupaten() {
    const searchValue = document.getElementById('searchKabupaten').value.toLowerCase();
    const filterValue = document.getElementById('filterProvinsiKab').value;
    const table = document.getElementById('kabupatenTable');
    
    if (!table) {
        console.log('Table kabupatenTable not found');
        return;
    }
    
    const tbody = table.getElementsByTagName('tbody')[0];
    if (!tbody) {
        console.log('Tbody not found');
        return;
    }
    
    const rows = tbody.getElementsByTagName('tr');
    console.log('Found', rows.length, 'rows');
    console.log('Search value:', searchValue);
    console.log('Filter value:', filterValue);
    
    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        if (cells.length === 0) continue; // Skip empty rows
        
        const text = rows[i].textContent.toLowerCase();
        const provinsiCell = cells[2]; // Kolom provinsi (index 2)
        
        const matchesSearch = !searchValue || text.includes(searchValue);
        const matchesFilter = !filterValue || (provinsiCell && provinsiCell.textContent.trim().includes(filterValue));
        
        console.log('Row', i, '- Search match:', matchesSearch, '- Filter match:', matchesFilter);
        
        rows[i].style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    }
}

// Filter Kabupaten by Provinsi
function filterKabupatenByProvinsi() {
    const filterValue = document.getElementById('filterProvinsiKab').value;
    const table = document.getElementById('kabupatenTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const provinsiCell = rows[i].getElementsByTagName('td')[2]; // Kolom provinsi
        if (!filterValue || (provinsiCell && provinsiCell.textContent.includes(filterValue))) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

// Filter Kecamatan
function filterKecamatan() {
    const searchValue = document.getElementById('searchKecamatan').value.toLowerCase();
    const filterValue = document.getElementById('filterKabupatenKec').value;
    const table = document.getElementById('kecamatanTable');
    
    if (!table) {
        console.log('Table kecamatanTable not found');
        return;
    }
    
    const tbody = table.getElementsByTagName('tbody')[0];
    if (!tbody) {
        console.log('Tbody not found');
        return;
    }
    
    const rows = tbody.getElementsByTagName('tr');
    console.log('Found', rows.length, 'rows in kecamatan table');
    
    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        if (cells.length === 0) continue; // Skip empty rows
        
        const text = rows[i].textContent.toLowerCase();
        const kabupatenCell = cells[2]; // Kolom kabupaten (index 2)
        
        const matchesSearch = !searchValue || text.includes(searchValue);
        const matchesFilter = !filterValue || (kabupatenCell && kabupatenCell.textContent.trim().includes(filterValue));
        
        rows[i].style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    }
}

// Filter Kecamatan by Kabupaten
function filterKecamatanByKabupaten() {
    const filterValue = document.getElementById('filterKabupatenKec').value;
    const table = document.getElementById('kecamatanTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const kabupatenCell = rows[i].getElementsByTagName('td')[2]; // Kolom kabupaten
        if (!filterValue || (kabupatenCell && kabupatenCell.textContent.includes(filterValue))) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

function deleteProvinsi(id) {
    console.log('Delete provinsi clicked:', id);
    if (confirm('Yakin ingin menghapus provinsi ini?')) {
        window.location.href = '/admin/wilayah/' + id + '/delete?type=province';
    }
}

function deleteWilayah(id, type) {
    console.log('Delete wilayah clicked:', id, type);
    const typeNames = {
        'province': 'provinsi',
        'regency': 'kabupaten/kota', 
        'district': 'kecamatan',
        'village': 'kelurahan'
    };
    
    if (confirm('Yakin ingin menghapus ' + typeNames[type] + ' ini?')) {
        window.location.href = '/admin/wilayah/' + id + '/delete?type=' + type;
    }
}

</script>
@endpush