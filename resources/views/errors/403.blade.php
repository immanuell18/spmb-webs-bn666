<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | SPMB</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1a1a3e 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .container { text-align: center; padding: 2rem; max-width: 600px; }
        .code {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 1rem;
        }
        h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 0.75rem; color: #e2e8f0; }
        p { color: #94a3b8; font-size: 1rem; line-height: 1.6; margin-bottom: 2rem; }
        .btn-group { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            padding: 0.75rem 1.75rem; border-radius: 10px; font-weight: 600;
            text-decoration: none; font-size: 0.95rem; transition: all 0.2s ease;
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-primary { background: linear-gradient(135deg, #f59e0b, #f97316); color: white; border: none; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(245,158,11,0.4); }
        .btn-secondary {
            background: rgba(255,255,255,0.08); color: #cbd5e1;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.15); transform: translateY(-2px); }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚫</div>
        <div class="code">403</div>
        <h1>Akses Ditolak</h1>
        <p>
            Kamu tidak memiliki izin untuk mengakses halaman ini.<br>
            Silakan login dengan akun yang sesuai atau kembali ke beranda.
        </p>
        <div class="btn-group">
            <a href="/" class="btn btn-primary">🏠 Kembali ke Beranda</a>
            <a href="{{ route('login') }}" class="btn btn-secondary">🔐 Login</a>
        </div>
    </div>
</body>
</html>
