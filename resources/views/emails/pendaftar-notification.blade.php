<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $type == 'aktivasi' ? 'Aktivasi Akun' : 'Notifikasi SPMB' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { padding: 20px; text-align: center; color: #666; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SMK BAKTI NUSANTARA 666</h1>
            <p>Sistem Penerimaan Mahasiswa Baru</p>
        </div>
        
        <div class="content">
            <h2>Halo, {{ $pendaftar->nama }}</h2>
            
            @if($type == 'aktivasi')
                <p>Selamat! Akun SPMB Anda telah berhasil dibuat.</p>
                <p><strong>Detail Akun:</strong></p>
                <ul>
                    <li>Email: {{ $pendaftar->user->email }}</li>
                    <li>No. Pendaftaran: {{ $pendaftar->no_pendaftaran }}</li>
                </ul>
                <p>Silakan login ke sistem untuk melengkapi data pendaftaran Anda.</p>
                <a href="{{ url('/login') }}" class="btn">Login Sekarang</a>
                
            @elseif($type == 'berkas_ditolak')
                <p>Berkas pendaftaran Anda memerlukan perbaikan.</p>
                <p><strong>Alasan:</strong> {{ $data['alasan'] ?? 'Berkas tidak sesuai ketentuan' }}</p>
                <p>Silakan login dan perbaiki berkas Anda sesuai petunjuk.</p>
                <a href="{{ url('/siswa/berkas') }}" class="btn">Perbaiki Berkas</a>
                
            @elseif($type == 'berkas_diterima')
                <p>Selamat! Berkas pendaftaran Anda telah diterima dan diverifikasi.</p>
                <p>Silakan lakukan pembayaran untuk menyelesaikan proses pendaftaran.</p>
                <a href="{{ url('/payment') }}" class="btn">Bayar Sekarang</a>
                
            @elseif($type == 'instruksi_bayar')
                <p>Berkas Anda telah diverifikasi. Silakan lakukan pembayaran:</p>
                <p><strong>Jumlah:</strong> Rp {{ number_format($data['jumlah'] ?? 0, 0, ',', '.') }}</p>
                <p><strong>Batas Waktu:</strong> {{ $data['batas_waktu'] ?? '3 hari' }}</p>
                <a href="{{ url('/payment') }}" class="btn">Bayar Sekarang</a>
                
            @elseif($type == 'pembayaran_diterima')
                <p>Pembayaran Anda telah diterima dan diverifikasi.</p>
                <p>Terima kasih telah menyelesaikan proses pendaftaran.</p>
                <p>Tunggu pengumuman hasil seleksi.</p>
                
            @elseif($type == 'pengumuman')
                <p>Pengumuman hasil seleksi telah diumumkan.</p>
                <p><strong>Status:</strong> 
                    @if($data['status'] == 'LULUS')
                        <span style="color: green;">LULUS - Selamat!</span>
                    @elseif($data['status'] == 'TIDAK_LULUS')
                        <span style="color: red;">TIDAK LULUS</span>
                    @else
                        <span style="color: orange;">CADANGAN</span>
                    @endif
                </p>
                <a href="{{ url('/siswa/pengumuman') }}" class="btn">Lihat Detail</a>
            @endif
        </div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem SPMB SMK BAKTI NUSANTARA 666</p>
            <p>Jangan balas email ini. Untuk bantuan, hubungi admin sekolah.</p>
        </div>
    </div>
</body>
</html>