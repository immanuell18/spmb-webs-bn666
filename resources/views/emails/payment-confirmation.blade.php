<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pembayaran Terverifikasi</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #4CAF50; color: white; padding: 20px; text-align: center;">
            <h1>✅ Pembayaran Terverifikasi</h1>
        </div>
        
        <div style="padding: 20px; background: #f9f9f9;">
            <h2>Halo {{ $pendaftar->nama }},</h2>
            
            <p>Pembayaran Anda untuk pendaftaran nomor <strong>{{ $pendaftar->no_pendaftaran }}</strong> telah berhasil diverifikasi!</p>
            
            <div style="background: #e8f5e8; border: 1px solid #4CAF50; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #2e7d32; margin-top: 0;">Status Pendaftaran:</h3>
                <p style="margin-bottom: 0;">✅ <strong>LUNAS</strong> - Pendaftaran Anda telah selesai</p>
            </div>
            
            <p><strong>Langkah selanjutnya:</strong></p>
            <ul>
                <li>Tunggu pengumuman hasil seleksi</li>
                <li>Pantau status melalui dashboard</li>
                <li>Kami akan mengirimkan notifikasi saat hasil keluar</li>
            </ul>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $dashboardUrl }}" style="background: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    Lihat Dashboard
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