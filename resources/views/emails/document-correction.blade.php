<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbaikan Berkas Diperlukan</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%); color: white; padding: 40px 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px; font-weight: bold;">📝 Perbaikan Berkas</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">Diperlukan Tindakan Anda</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Halo {{ $pendaftar->nama }}! 👋</h2>
            
            <p style="color: #555; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                Berkas pendaftaran Anda dengan nomor <strong style="color: #667eea;">{{ $pendaftar->no_pendaftaran }}</strong> memerlukan perbaikan.
            </p>
            
            <!-- Alert Box -->
            <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left: 4px solid #ff6b6b; padding: 25px; margin: 30px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="color: #d63031; margin: 0 0 15px 0; font-size: 18px; display: flex; align-items: center;">
                    ⚠️ Catatan Verifikator:
                </h3>
                <p style="margin: 0; color: #856404; font-size: 16px; font-weight: 500; background: white; padding: 15px; border-radius: 6px; border: 1px solid #ffeaa7;">
                    {{ $correctionMessage }}
                </p>
            </div>
            
            <!-- Steps -->
            <div style="background: #f8f9fa; border-radius: 8px; padding: 25px; margin: 30px 0;">
                <h3 style="color: #667eea; margin: 0 0 20px 0; font-size: 18px;">📋 Yang Perlu Anda Lakukan:</h3>
                <ol style="color: #555; margin: 0; padding-left: 20px;">
                    <li style="margin-bottom: 10px; font-size: 15px;">Login ke sistem SPMB</li>
                    <li style="margin-bottom: 10px; font-size: 15px;">Buka halaman Upload Berkas</li>
                    <li style="margin-bottom: 10px; font-size: 15px;">Perbaiki berkas sesuai catatan di atas</li>
                    <li style="margin-bottom: 10px; font-size: 15px;">Upload ulang berkas yang sudah diperbaiki</li>
                    <li style="font-size: 15px;">Tunggu verifikasi ulang dari tim kami</li>
                </ol>
            </div>
            
            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ $berkasUrl }}" style="background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: bold; font-size: 16px; box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3); transition: all 0.3s ease;">
                    🚀 Perbaiki Berkas Sekarang
                </a>
            </div>
            
            <div style="background: #e8f4fd; border-radius: 8px; padding: 20px; margin: 30px 0;">
                <p style="margin: 0; color: #1976d2; font-size: 14px; text-align: center;">
                    ⏰ <strong>Batas Waktu:</strong> Harap perbaiki berkas dalam 7 hari untuk menghindari pembatalan pendaftaran
                </p>
            </div>
            
            <p style="color: #888; font-size: 14px; margin: 20px 0 0 0;">
                Jika tombol tidak berfungsi, copy dan paste link berikut ke browser Anda:<br>
                <a href="{{ $berkasUrl }}" style="color: #667eea; word-break: break-all;">{{ $berkasUrl }}</a>
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background: #f8f9fa; text-align: center; padding: 30px 20px; border-top: 1px solid #eee;">
            <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">
                <strong>SMK Bakti Nusantara 666</strong><br>
                Tim Verifikasi Berkas
            </p>
            <p style="margin: 0; color: #999; font-size: 12px;">
                Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>