<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server | SPMB</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #3b0a1f 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }
        .code {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ef4444, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 1rem;
            animation: shake 0.5s ease-in-out 3;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }
        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #e2e8f0;
        }
        p {
            color: #94a3b8;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.75rem 1.75rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }
        .btn-secondary {
            background: rgba(255,255,255,0.08);
            color: #cbd5e1;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }
        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            font-size: 0.85rem;
            color: #fca5a5;
            text-align: left;
        }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i></div>
        <div class="code">500</div>
        <h1>Terjadi Kesalahan Server</h1>
        <p>
            Maaf, server mengalami masalah saat memproses permintaanmu.<br>
            Tim kami sudah otomatis diberitahu dan sedang memperbaikinya.
        </p>
        @if(config('app.debug') && isset($exception))
        <div class="error-box">
            <strong>Debug Info:</strong><br>
            {{ $exception->getMessage() }}
        </div>
        @endif
        <div class="btn-group">
            <a href="/" class="btn btn-primary">
                🏠 Kembali ke Beranda
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                🔄 Coba Lagi
            </a>
        </div>
    </div>
</body>
</html>
