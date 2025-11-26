<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Pendaftaran - {{ $pendaftar->no_pendaftaran }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #0066cc; }
        .subtitle { font-size: 14px; margin-top: 5px; }
        .card { border: 2px solid #000; padding: 20px; margin: 20px 0; }
        .row { display: flex; margin-bottom: 10px; }
        .label { width: 150px; font-weight: bold; }
        .value { flex: 1; }
        .photo-box { width: 120px; height: 150px; border: 1px solid #000; text-align: center; padding-top: 60px; margin-left: 20px; }
        .status { padding: 5px 10px; border: 1px solid #000; display: inline-block; margin-top: 10px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">SMK BALI GLOBAL BADUNG</div>
        <div class="subtitle">SISTEM PENERIMAAN MAHASISWA BARU (SPMB)</div>
        <div class="subtitle">KARTU PENDAFTARAN</div>
    </div>

    <div class="card">
        <div style="display: flex;">
            <div style="flex: 1;">
                <div class="row">
                    <div class="label">No. Pendaftaran</div>
                    <div class="value">: {{ $pendaftar->no_pendaftaran }}</div>
                </div>
                <div class="row">
                    <div class="label">Nama Lengkap</div>
                    <div class="value">: {{ $pendaftar->nama }}</div>
                </div>
                <div class="row">
                    <div class="label">Email</div>
                    <div class="value">: {{ $pendaftar->email }}</div>
                </div>
                <div class="row">
                    <div class="label">Jurusan Pilihan</div>
                    <div class="value">: {{ $pendaftar->jurusan->nama ?? 'N/A' }}</div>
                </div>
                <div class="row">
                    <div class="label">Gelombang</div>
                    <div class="value">: {{ $pendaftar->gelombang->nama ?? 'N/A' }}</div>
                </div>
                @if($pendaftar->dataSiswa)
                <div class="row">
                    <div class="label">NIK</div>
                    <div class="value">: {{ $pendaftar->dataSiswa->nik ?? '-' }}</div>
                </div>
                <div class="row">
                    <div class="label">Tempat, Tgl Lahir</div>
                    <div class="value">: {{ $pendaftar->dataSiswa->tmp_lahir ?? '-' }}, {{ $pendaftar->dataSiswa->tgl_lahir ?? '-' }}</div>
                </div>
                @endif
                <div class="row">
                    <div class="label">Tanggal Daftar</div>
                    <div class="value">: {{ $pendaftar->created_at->format('d M Y H:i') }}</div>
                </div>
                <div class="row">
                    <div class="label">Status</div>
                    <div class="value">: 
                        <span class="status">
                            @if($pendaftar->status === 'PAID')
                                TERBAYAR
                            @elseif($pendaftar->status === 'ADM_PASS')
                                TERVERIFIKASI
                            @elseif($pendaftar->status === 'SUBMIT')
                                MENUNGGU VERIFIKASI
                            @else
                                DITOLAK
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="photo-box">
                FOTO<br>3x4
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>PENTING:</strong></p>
        <p>• Kartu ini adalah bukti sah pendaftaran di SMK Bali Global Badung</p>
        <p>• Harap dibawa saat mengikuti proses seleksi</p>
        <p>• Kartu tidak dapat dipindahtangankan</p>
        <br>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }} WIB</p>
    </div>
</body>
</html>