<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun SPMB</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px; font-weight: bold;">🎉 Selamat Datang!</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">SMK Bakti Nusantara 666</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Halo {{ $userName }}! 👋</h2>
            
            <p style="color: #555; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                Selamat! Akun SPMB Anda telah berhasil dibuat. Sekarang Anda dapat memulai proses pendaftaran di SMK Bakti Nusantara 666.
            </p>
            
            <div style="background: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 30px 0;">
                <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px;">📋 Langkah Selanjutnya:</h3>
                <ol style="color: #555; margin: 0; padding-left: 20px;">
                    <li style="margin-bottom: 8px;">Login ke sistem menggunakan email dan password Anda</li>
                    <li style="margin-bottom: 8px;">Lengkapi formulir pendaftaran dengan data diri</li>
                    <li style="margin-bottom: 8px;">Upload berkas persyaratan yang diperlukan</li>
                    <li style="margin-bottom: 8px;">Lakukan pembayaran setelah berkas diverifikasi</li>
                    <li>Tunggu pengumuman hasil seleksi</li>
                </ol>
            </div>
            
            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ $loginUrl }}" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: bold; font-size: 16px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                    🚀 Login Sekarang
                </a>
            </div>
            
            <div style="background: #e3f2fd; border-radius: 8px; padding: 20px; margin: 30px 0;">
                <p style="margin: 0; color: #1976d2; font-size: 14px; text-align: center;">
                    💡 <strong>Tips:</strong> Pastikan semua berkas dalam format PDF dan ukuran maksimal 2MB
                </p>
            </div>
            
            <p style="color: #888; font-size: 14px; margin: 20px 0 0 0;">
                Jika tombol tidak berfungsi, copy dan paste link berikut ke browser Anda:<br>
                <a href="{{ $loginUrl }}" style="color: #667eea; word-break: break-all;">{{ $loginUrl }}</a>
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background: #f8f9fa; text-align: center; padding: 30px 20px; border-top: 1px solid #eee;">
            <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">
                <strong>SMK Bakti Nusantara 666</strong><br>
                Sistem Penerimaan Mahasiswa Baru Online
            </p>
            <p style="margin: 0; color: #999; font-size: 12px;">
                Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>