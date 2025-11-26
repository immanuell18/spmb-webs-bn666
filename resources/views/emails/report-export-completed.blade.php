<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Laporan Selesai</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .download-btn { 
            display: inline-block; 
            background: #28a745; 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 15px 0;
        }
        .info-box { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Export Laporan Selesai</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $userName }}</strong>,</p>
            
            <p>Export laporan SPMB Anda telah selesai diproses dan siap untuk diunduh.</p>
            
            <div class="info-box">
                <strong>Detail Export:</strong><br>
                📄 Tipe: {{ ucfirst(str_replace('_', ' ', $exportType)) }}<br>
                📁 File: {{ $fileName }}<br>
                ⏰ Berlaku sampai: {{ $expiryTime->format('d F Y H:i') }} WIB<br>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $downloadUrl }}" class="download-btn">
                    📥 Download Laporan
                </a>
            </div>
            
            <div class="info-box">
                <strong>⚠️ Penting:</strong><br>
                • Link download akan expired dalam 24 jam<br>
                • File akan otomatis terhapus setelah expired<br>
                • Jika ada masalah, silakan hubungi administrator<br>
            </div>
            
            <p>Terima kasih telah menggunakan Sistem SPMB.</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem SPMB<br>
            {{ config('app.name') }} - {{ now()->year }}</p>
        </div>
    </div>
</body>
</html>