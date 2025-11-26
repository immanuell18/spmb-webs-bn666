<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Pembayaran - {{ $pendaftar->no_pendaftaran }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #0066cc; }
        .subtitle { font-size: 14px; margin-top: 5px; }
        .card { border: 2px solid #000; padding: 20px; margin: 20px 0; }
        .row { display: flex; margin-bottom: 10px; }
        .label { width: 150px; font-weight: bold; }
        .value { flex: 1; }
        .status { padding: 5px 10px; border: 1px solid #000; display: inline-block; margin-top: 10px; background: #28a745; color: white; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; }
        .amount { font-size: 24px; font-weight: bold; color: #28a745; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">SMK BALI GLOBAL BADUNG</div>
        <div class="subtitle">SISTEM PENERIMAAN MAHASISWA BARU (SPMB)</div>
        <div class="subtitle">BUKTI PEMBAYARAN</div>
    </div>

    <div class="card">
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
            <div class="label">Jurusan</div>
            <div class="value">: {{ $pendaftar->jurusan->nama ?? 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="label">Gelombang</div>
            <div class="value">: {{ $pendaftar->gelombang->nama ?? 'N/A' }}</div>
        </div>
        
        <div class="amount">
            BIAYA PENDAFTARAN<br>
            Rp {{ number_format($pendaftar->biaya_pendaftaran, 0, ',', '.') }}
        </div>
        
        <div class="row">
            <div class="label">Status Pembayaran</div>
            <div class="value">: <span class="status">TERBAYAR</span></div>
        </div>
        
        @if($pendaftar->tgl_verifikasi_payment)
        <div class="row">
            <div class="label">Tanggal Verifikasi</div>
            <div class="value">: {{ $pendaftar->tgl_verifikasi_payment->format('d M Y H:i') }}</div>
        </div>
        @endif
        
        @if($pendaftar->user_verifikasi_payment)
        <div class="row">
            <div class="label">Diverifikasi oleh</div>
            <div class="value">: {{ $pendaftar->user_verifikasi_payment }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p><strong>PENTING:</strong></p>
        <p>• Bukti pembayaran ini adalah dokumen sah</p>
        <p>• Simpan bukti ini dengan baik</p>
        <p>• Hubungi bagian keuangan jika ada pertanyaan</p>
        <br>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }} WIB</p>
    </div>
</body>
</html>