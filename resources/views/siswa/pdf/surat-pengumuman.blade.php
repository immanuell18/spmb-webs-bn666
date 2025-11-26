<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengumuman - {{ $pendaftar->no_pendaftaran }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #0066cc; margin-bottom: 10px; }
        .subtitle { font-size: 16px; margin-bottom: 5px; }
        .title { font-size: 20px; font-weight: bold; text-decoration: underline; margin: 30px 0; }
        .content { margin: 20px 0; text-align: justify; }
        .result { text-align: center; margin: 30px 0; padding: 20px; border: 3px solid #28a745; background: #f8fff8; }
        .result.lulus { border-color: #28a745; background: #f8fff8; }
        .result.cadangan { border-color: #ffc107; background: #fffbf0; }
        .result.tidak-lulus { border-color: #dc3545; background: #fff5f5; }
        .result-text { font-size: 24px; font-weight: bold; margin: 10px 0; }
        .result-text.lulus { color: #28a745; }
        .result-text.cadangan { color: #ffc107; }
        .result-text.tidak-lulus { color: #dc3545; }
        .data-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .data-table td { padding: 8px; border-bottom: 1px solid #ddd; }
        .data-table .label { width: 200px; font-weight: bold; }
        .signature { margin-top: 50px; }
        .signature-box { float: right; text-align: center; width: 200px; }
        .footer { clear: both; margin-top: 50px; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">SMK BALI GLOBAL BADUNG</div>
        <div class="subtitle">Jl. Pendidikan No. 123, Badung, Bali</div>
        <div class="subtitle">Telp: (0361) 123456 | Email: info@smkbaliglobal.sch.id</div>
    </div>

    <div class="title">SURAT PENGUMUMAN HASIL SELEKSI</div>
    <div class="title">PENERIMAAN PESERTA DIDIK BARU</div>

    <div class="content">
        <p>Berdasarkan hasil seleksi Penerimaan Peserta Didik Baru (PPDB) SMK Bali Global Badung, dengan ini kami sampaikan bahwa:</p>
        
        <table class="data-table">
            <tr>
                <td class="label">Nomor Pendaftaran</td>
                <td>: {{ $pendaftar->no_pendaftaran }}</td>
            </tr>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td>: {{ $pendaftar->nama }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>: {{ $pendaftar->email }}</td>
            </tr>
            <tr>
                <td class="label">Jurusan yang Dipilih</td>
                <td>: {{ $pendaftar->jurusan->nama ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Gelombang Pendaftaran</td>
                <td>: {{ $pendaftar->gelombang->nama ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="result {{ strtolower($pendaftar->status_akhir) }}">
        @if($pendaftar->status_akhir === 'LULUS')
            <div class="result-text lulus">🎉 DINYATAKAN LULUS 🎉</div>
            <p><strong>SELAMAT!</strong> Anda diterima sebagai peserta didik baru di SMK Bali Global Badung.</p>
        @elseif($pendaftar->status_akhir === 'CADANGAN')
            <div class="result-text cadangan">📋 DAFTAR CADANGAN</div>
            <p>Anda masuk dalam daftar cadangan. Mohon tunggu pengumuman selanjutnya.</p>
        @else
            <div class="result-text tidak-lulus">😔 BELUM BERHASIL</div>
            <p>Maaf, Anda belum berhasil dalam seleksi kali ini. Jangan menyerah dan coba lagi di kesempatan berikutnya.</p>
        @endif
    </div>

    @if($pendaftar->status_akhir === 'LULUS')
    <div class="content">
        <p><strong>Informasi Selanjutnya:</strong></p>
        <ul>
            <li>Daftar ulang akan dilaksanakan pada tanggal yang akan diinformasikan kemudian</li>
            <li>Siapkan dokumen asli untuk verifikasi ulang</li>
            <li>Pantau terus website sekolah untuk informasi terbaru</li>
            <li>Hubungi bagian akademik untuk informasi lebih lanjut</li>
        </ul>
    </div>
    @endif

    <div class="content">
        <p>Demikian pengumuman ini kami sampaikan. Terima kasih atas partisipasi Anda dalam seleksi PPDB SMK Bali Global Badung.</p>
    </div>

    <div class="signature">
        <div class="signature-box">
            <p>Badung, {{ $pendaftar->tgl_pengumuman->format('d M Y') }}</p>
            <p><strong>Kepala Sekolah</strong></p>
            <br><br><br>
            <p><strong>Drs. I Made Sutrisna, M.Pd</strong></p>
            <p>NIP. 196512121990031003</p>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis pada {{ now()->format('d M Y H:i') }} WIB</p>
        <p>Untuk verifikasi keaslian dokumen, hubungi SMK Bali Global Badung</p>
    </div>
</body>
</html>