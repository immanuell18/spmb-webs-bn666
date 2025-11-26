@props(['pendaftar'])

@php
    $progress = $pendaftar ? $pendaftar->getProgressPercentage() : 0;
    $status = $pendaftar->status ?? null;
    $statusAkhir = $pendaftar->status_akhir ?? null;
@endphp

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">📊 Status Pendaftaran</h5>
    </div>
    <div class="card-body">
        @if($pendaftar)
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold">{{ $pendaftar->getStatusLabel() }}</span>
                    <span class="badge bg-{{ $progress == 100 ? 'success' : ($progress >= 50 ? 'info' : 'warning') }}">
                        {{ $progress }}%
                    </span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar 
                        @if($progress == 100) bg-success
                        @elseif($progress >= 75) bg-info  
                        @elseif($progress >= 50) bg-warning
                        @else bg-danger
                        @endif" 
                        style="width: {{ $progress }}%">
                    </div>
                </div>
            </div>

            <!-- Status Steps -->
            <div class="row text-center">
                <div class="col-3">
                    <div class="step {{ $status == 'SUBMIT' ? 'active' : ($progress > 25 ? 'completed' : '') }}">
                        <div class="step-icon">📝</div>
                        <small>Submit</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="step {{ $status == 'ADM_PASS' ? 'active' : ($progress > 50 ? 'completed' : '') }}">
                        <div class="step-icon">✅</div>
                        <small>Verifikasi</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="step {{ $status == 'PAID' ? 'active' : ($progress > 75 ? 'completed' : '') }}">
                        <div class="step-icon">💰</div>
                        <small>Bayar</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="step {{ $statusAkhir ? 'completed' : '' }}">
                        <div class="step-icon">🎓</div>
                        <small>Pengumuman</small>
                    </div>
                </div>
            </div>

            @if($statusAkhir)
                <div class="alert alert-{{ $statusAkhir == 'LULUS' ? 'success' : ($statusAkhir == 'CADANGAN' ? 'warning' : 'danger') }} mt-3">
                    <h6 class="alert-heading">🎉 Pengumuman Hasil Seleksi</h6>
                    <p class="mb-0">
                        Status Akhir: <strong>{{ $statusAkhir }}</strong>
                        @if($statusAkhir == 'LULUS')
                            - Selamat! Anda diterima di {{ $pendaftar->jurusan->nama }}
                        @elseif($statusAkhir == 'CADANGAN')
                            - Anda masuk daftar cadangan. Tunggu informasi selanjutnya.
                        @else
                            - Mohon maaf, Anda belum berhasil pada seleksi kali ini.
                        @endif
                    </p>
                </div>
            @endif

            @if($status == 'ADM_REJECT')
                <div class="alert alert-warning mt-3">
                    <h6 class="alert-heading">⚠️ Berkas Perlu Perbaikan</h6>
                    <p class="mb-0">{{ $pendaftar->catatan_admin ?? 'Silakan perbaiki berkas sesuai petunjuk.' }}</p>
                </div>
            @endif

        @else
            <div class="text-center py-4">
                <h6 class="text-muted">Belum Ada Pendaftaran</h6>
                <p class="text-muted">Silakan lengkapi formulir pendaftaran terlebih dahulu.</p>
                <a href="{{ route('siswa.pendaftaran') }}" class="btn btn-primary">Mulai Pendaftaran</a>
            </div>
        @endif
    </div>
</div>

<style>
.step {
    padding: 10px;
    margin: 5px 0;
}

.step.active .step-icon {
    background: #007bff;
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
}

.step.completed .step-icon {
    background: #28a745;
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
}

.step .step-icon {
    background: #e9ecef;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
}
</style>