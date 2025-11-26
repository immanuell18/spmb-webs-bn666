<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Instruksi Pembayaran</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #2196F3; color: white; padding: 20px; text-align: center;">
            <h1>Instruksi Pembayaran</h1>
        </div>
        
        <div style="padding: 20px; background: #f9f9f9;">
            <h2>Halo {{ $pendaftar->nama }},</h2>
            
            <p>Selamat! Berkas pendaftaran Anda dengan nomor <strong>{{ $pendaftar->no_pendaftaran }}</strong> telah diverifikasi dan dinyatakan lengkap.</p>
            
            <div style="background: #e8f5e8; border: 1px solid #4CAF50; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #2e7d32; margin-top: 0;">Detail Pembayaran:</h3>
                <table style="width: 100%;">
                    <tr>
                        <td><strong>Jurusan:</strong></td>
                        <td>{{ $pendaftar->jurusan->nama }}</td>
                    </tr>
                    <tr>
                        <td><strong>Gelombang:</strong></td>
                        <td>{{ $pendaftar->gelombang->nama }}</td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Pendaftaran:</strong></td>
                        <td><strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
            
            <p><strong>Langkah pembayaran:</strong></p>
            <ol>
                <li>Login ke sistem SPMB</li>
                <li>Buka halaman Pembayaran</li>
                <li>Pilih metode pembayaran yang tersedia</li>
                <li>Lakukan pembayaran sesuai instruksi</li>
                <li>Upload bukti pembayaran</li>
            </ol>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $paymentUrl }}" style="background: #2196F3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    Bayar Sekarang
                </a>
            </div>
            
            <p><small>Jika tombol tidak berfungsi, copy dan paste link berikut: {{ $paymentUrl }}</small></p>
        </div>
        
        <div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">
            <p>Email ini dikirim otomatis oleh sistem SPMB Online</p>
        </div>
    </div>
</body>
</html>