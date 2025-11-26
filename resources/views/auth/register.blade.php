<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SPMB SMK Bakti Nusantara 666</title>
    <link href="{{ asset('assets/images/logo-sekolah.png') }}" rel="icon" type="image/png">
    <link href="{{ asset('assets/images/logo-sekolah.png') }}" rel="shortcut icon" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 400;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .split-container {
            display: flex;
            min-height: 100vh;
        }
        .left-side {
            flex: 1;
            background: linear-gradient(135deg, #06BBCC 0%, #181d38 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 2rem;
            position: relative;
        }
        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('{{ asset("assets/images/siswa-belajar.jpg") }}') center/cover;
            opacity: 0.1;
            z-index: -1;
        }
        .right-side {
            flex: 1;
            background: #F0FBFC;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }
        .right-side::before {
            content: '';
            position: absolute;
            top: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #06BBCC, transparent);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .form-container {
            width: 100%;
            max-width: 400px;
            animation: slideInRight 0.8s ease-out;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .logo-container {
            animation: slideInLeft 0.8s ease-out;
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #06BBCC;
            box-shadow: 0 0 0 0.2rem rgba(6, 187, 204, 0.25);
            transform: translateY(-2px);
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 2px solid #e9ecef;
            border-right: none;
            background: #F0FBFC;
            color: #06BBCC;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0;
        }
        .input-group .form-control:last-child {
            border-radius: 0 10px 10px 0;
        }
        #togglePassword, #togglePasswordConfirm {
            transition: all 0.3s ease;
        }
        #togglePassword:hover, #togglePasswordConfirm:hover {
            background: #06BBCC;
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #06BBCC 0%, #181d38 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(6, 187, 204, 0.4);
        }
        .text-primary {
            color: #06BBCC !important;
        }
        .school-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .split-container {
                flex-direction: column;
            }
            .left-side {
                min-height: 35vh;
                padding: 1.5rem 1rem;
            }
            .right-side {
                min-height: 65vh;
                padding: 1.5rem 1rem;
            }
            .form-container {
                max-width: 100%;
            }
            .school-logo {
                width: 60px;
                height: 60px;
            }
            .left-side h1 {
                font-size: 1.8rem;
            }
            .left-side h3 {
                font-size: 1.2rem;
            }
            .left-side p {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .left-side {
                min-height: 30vh;
                padding: 1rem;
            }
            .right-side {
                padding: 1rem;
            }
            .form-control {
                padding: 10px 12px;
                font-size: 16px; /* Prevent zoom on iOS */
            }
            .btn-primary {
                padding: 10px;
            }
            .school-logo {
                width: 50px;
                height: 50px;
            }
            .left-side h1 {
                font-size: 1.5rem;
            }
            .left-side h3 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <div class="left-side">
            <div class="text-center logo-container">
                <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo SMK Bakti Nusantara 666" class="school-logo mb-4">
                <h1 class="fw-bold mb-3">Daftar Sekarang</h1>
                <h3 class="mb-4">SMK Bakti Nusantara 666</h3>
                <p class="lead">Mulai perjalanan pendidikan Anda</p>
                <p>Bergabunglah dengan ribuan siswa yang telah mempercayai kami</p>
                <div class="mt-4">
                    <i class="fas fa-users text-warning me-3"></i>
                    <i class="fas fa-graduation-cap text-warning me-3"></i>
                    <i class="fas fa-trophy text-warning"></i>
                </div>
            </div>
        </div>
        <div class="right-side">
            <div class="form-container">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Buat Akun</h3>
                    <p class="text-muted">Isi data diri Anda dengan lengkap</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="input-group-text" id="togglePassword" style="cursor: pointer; border-left: none; border-radius: 0 10px 10px 0;">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required>
                            <span class="input-group-text" id="togglePasswordConfirm" style="cursor: pointer; border-left: none; border-radius: 0 10px 10px 0;">
                                <i class="fas fa-eye" id="eyeIconConfirm"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i>Daftar
                    </button>
                </form>

                <div class="text-center">
                    <small class="text-muted">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-decoration-none">Login di sini</a>
                    </small>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <a href="{{ route('beranda') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Beranda
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
        
        // Toggle password confirmation visibility
        document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
            const passwordField = document.getElementById('password_confirmation');
            const eyeIcon = document.getElementById('eyeIconConfirm');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>