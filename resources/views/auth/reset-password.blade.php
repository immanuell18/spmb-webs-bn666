<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SPMB SMK Bakti Nusantara 666</title>
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
            background: linear-gradient(135deg, #06BBCC 0%, #181d38 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reset-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reset-header {
            background: linear-gradient(135deg, #06BBCC 0%, #181d38 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .reset-body {
            padding: 40px 30px;
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
            cursor: pointer;
            border-left: none;
            border-radius: 0 10px 10px 0;
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
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(6, 187, 204, 0.4);
        }
        .password-strength {
            margin-top: 10px;
        }
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            overflow: hidden;
        }
        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            width: 0%;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <i class="fas fa-lock fa-3x mb-3"></i>
            <h3 class="mb-0">Buat Password Baru</h3>
            <p class="mb-0">Masukkan password baru untuk akun Anda</p>
        </div>
        
        <div class="reset-body">
            <div class="info-box">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <small>Password harus minimal 8 karakter dan mengandung kombinasi huruf, angka, dan simbol.</small>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="input-group-text" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <small class="text-muted" id="strengthText">Kekuatan password</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        <span class="input-group-text" id="togglePasswordConfirm">
                            <i class="fas fa-eye" id="eyeIconConfirm"></i>
                        </span>
                    </div>
                    <small class="text-muted" id="matchText"></small>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn" disabled>
                    <i class="fas fa-save me-2"></i>Simpan Password Baru
                </button>
            </form>

            <div class="text-center">
                <small class="text-muted">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
                    </a>
                </small>
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

        // Password strength checker
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirmation');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const matchText = document.getElementById('matchText');
        const submitBtn = document.getElementById('submitBtn');

        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = [];

            if (password.length >= 8) strength += 25;
            else feedback.push('minimal 8 karakter');

            if (/[a-z]/.test(password)) strength += 25;
            else feedback.push('huruf kecil');

            if (/[A-Z]/.test(password)) strength += 25;
            else feedback.push('huruf besar');

            if (/[0-9]/.test(password)) strength += 25;
            else feedback.push('angka');

            return { strength, feedback };
        }

        passwordField.addEventListener('input', function() {
            const password = this.value;
            const { strength, feedback } = checkPasswordStrength(password);
            
            strengthFill.style.width = strength + '%';
            
            if (strength < 50) {
                strengthFill.style.background = '#dc3545';
                strengthText.textContent = 'Lemah - Perlu: ' + feedback.join(', ');
                strengthText.className = 'text-danger';
            } else if (strength < 75) {
                strengthFill.style.background = '#ffc107';
                strengthText.textContent = 'Sedang';
                strengthText.className = 'text-warning';
            } else {
                strengthFill.style.background = '#28a745';
                strengthText.textContent = 'Kuat';
                strengthText.className = 'text-success';
            }
            
            checkFormValidity();
        });

        confirmField.addEventListener('input', function() {
            const password = passwordField.value;
            const confirm = this.value;
            
            if (confirm === '') {
                matchText.textContent = '';
            } else if (password === confirm) {
                matchText.textContent = '✓ Password cocok';
                matchText.className = 'text-success';
            } else {
                matchText.textContent = '✗ Password tidak cocok';
                matchText.className = 'text-danger';
            }
            
            checkFormValidity();
        });

        function checkFormValidity() {
            const password = passwordField.value;
            const confirm = confirmField.value;
            const { strength } = checkPasswordStrength(password);
            
            if (strength >= 75 && password === confirm && password.length > 0) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary');
            }
        }
    </script>
</body>
</html>