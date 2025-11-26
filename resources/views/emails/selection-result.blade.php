<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengumuman Hasil Seleksi</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        @if($status === 'LULUS')
            <div style="background: #4CAF50; color: white; padding: 20px; text-align: center;">
                <h1>🎉 SELAMAT! ANDA DITERIMA</h1>
            </div>
        @elseif($status === 'CADANGAN')
            <div style="background: #FF9800; color: white; padding: 20px; text-align: center;">
                <h1>📋 DAFTAR CADANGAN</h1>
            </div>
        @else
            <div style="background: #f44336; color: white; padding: 20px; text-align: center;">
                <h1>📢 PENGUMUMAN HASIL SELEKSI</h1>
            </div>
        @endif
        
        <div style="padding: 20px; background: #f9f9f9;">
            <h2>Halo {{ $pendaftar->nama }},</h2>
            
            <p>Hasil seleksi untuk pendaftaran nomor <strong>{{ $pendaftar->no_pendaftaran }}</strong> telah diumumkan.</p>
            
            @if($status === 'LULUS')
                <div style="background: #e8f5e8; border: 1px solid #4CAF50; padding: 15px; margin: 20px 0; border-radius: 5px;">
                    <h3 style="color: #2e7d32; margin-top: 0;">🎉 SELAMAT!</h3>
                    <p>Anda <strong>DITERIMA</strong> di:</p>
                    <ul>
                        <li><strong>Jurusan:</strong> {{ $pendaftar->jurusan->nama }}</li>
                        <li><strong>Gelombang:</strong> {{ $pendaftar->gelombang->nama }}</li>
                    </ul>
                </div>
                
                <p><strong>Langkah selanjutnya:</strong></p>
                <ol>
                    <li>Cetak surat penerimaan dari dashboard</li>
                    <li>Siapkan berkas daftar ulang</li>
                    <li>Tunggu informasi jadwal daftar ulang</li>
                </ol>
                
            @elseif($status === 'CADANGAN')
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;">
                    <h3 style="color: #856404; margin-top: 0;">📋 DAFTAR CADANGAN</h3>
                    <p>Anda masuk dalam <strong>DAFTAR CADANGAN</strong> untuk jurusan {{ $pendaftar->jurusan->nama }}.</p>
                    <p>Kami akan menghubungi Anda jika ada kesempatan.</p>
                </div>
                
            @else
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;">
                    <h3 style="color: #721c24; margin-top: 0;">Hasil Seleksi</h3>
                    <p>Mohon maaf, Anda <strong>belum berhasil</strong> pada seleksi kali ini.</p>
                    <p>Jangan menyerah! Anda dapat mencoba lagi pada gelombang berikutnya.</p>
                </div>
            @endif
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $dashboardUrl }}" style="background: #2196F3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    Lihat Detail Hasil
                </a>
            </div>
            
            <p><small>Jika tombol tidak berfungsi, copy dan paste link berikut: {{ $dashboardUrl }}</small></p>
        </div>
        
        <div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">
            <p>Email ini dikirim otomatis oleh sistem SPMB Online</p>
        </div>
    </div>
</body>
</html>