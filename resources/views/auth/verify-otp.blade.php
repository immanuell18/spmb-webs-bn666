<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - SPMB SMK Bakti Nusantara 666</title>
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
        .otp-container {
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
        .otp-header {
            background: linear-gradient(135deg, #06BBCC 0%, #181d38 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .otp-body {
            padding: 40px 30px;
        }
        .otp-inputs {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 20px 0;
        }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .otp-input:focus {
            border-color: #06BBCC;
            box-shadow: 0 0 0 0.2rem rgba(6, 187, 204, 0.25);
            outline: none;
        }
        .otp-input.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
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
        .countdown {
            font-size: 18px;
            font-weight: bold;
            color: #06BBCC;
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
    <div class="otp-container">
        <div class="otp-header">
            <i class="fas fa-shield-alt fa-3x mb-3"></i>
            <h3 class="mb-0">Verifikasi OTP</h3>
            <p class="mb-0">Masukkan kode yang dikirim ke email</p>
        </div>
        
        <div class="otp-body">
            <div class="info-box">
                <i class="fas fa-envelope text-primary me-2"></i>
                <small>Kode OTP telah dikirim ke <strong>{{ session('email') ?? 'email Anda' }}</strong></small>
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

            <form method="POST" action="{{ route('password.verify-otp') }}" id="otpForm">
                @csrf
                <div class="mb-4">
                    <label class="form-label text-center d-block">Masukkan Kode OTP (6 digit)</label>
                    <div class="otp-inputs">
                        <input type="text" class="otp-input" maxlength="1" data-index="0">
                        <input type="text" class="otp-input" maxlength="1" data-index="1">
                        <input type="text" class="otp-input" maxlength="1" data-index="2">
                        <input type="text" class="otp-input" maxlength="1" data-index="3">
                        <input type="text" class="otp-input" maxlength="1" data-index="4">
                        <input type="text" class="otp-input" maxlength="1" data-index="5">
                    </div>
                    <input type="hidden" name="otp" id="otpValue">
                    <div id="otpError" class="text-danger text-center mt-2" style="display: none;"></div>
                </div>

                <div class="text-center mb-3">
                    <small class="text-muted">Kode akan kadaluarsa dalam: <span class="countdown" id="countdown">10:00</span></small>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
                    <i class="fas fa-check me-2"></i>Verifikasi OTP
                </button>
            </form>

            <div class="text-center">
                <small class="text-muted">
                    Tidak menerima kode? 
                    <a href="{{ route('password.request') }}" class="text-decoration-none">Kirim ulang</a>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // OTP Input handling
        const otpInputs = document.querySelectorAll('.otp-input');
        const otpValue = document.getElementById('otpValue');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                // Only allow numbers
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                
                if (e.target.value.length === 1) {
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                }
                updateOtpValue();
                clearError();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                if (pastedData.length === 6 && /^\d+$/.test(pastedData)) {
                    pastedData.split('').forEach((digit, i) => {
                        if (i < otpInputs.length) {
                            otpInputs[i].value = digit;
                        }
                    });
                    updateOtpValue();
                    clearError();
                }
            });
        });

        function updateOtpValue() {
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            otpValue.value = otp;
        }

        function clearError() {
            document.getElementById('otpError').style.display = 'none';
        }

        function showError(message) {
            const errorDiv = document.getElementById('otpError');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        // Form validation
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            const otp = otpValue.value;
            
            if (otp.length !== 6) {
                e.preventDefault();
                showError('Kode OTP harus 6 digit!');
                return false;
            }
            
            if (!/^\d+$/.test(otp)) {
                e.preventDefault();
                showError('Kode OTP hanya boleh berisi angka!');
                return false;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memverifikasi...';
            submitBtn.disabled = true;
        });

        // Countdown timer
        let timeLeft = 600; // 10 minutes in seconds
        const countdownElement = document.getElementById('countdown');

        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                countdownElement.textContent = 'Kadaluarsa';
                countdownElement.style.color = '#dc3545';
                document.getElementById('otpForm').style.opacity = '0.5';
                otpInputs.forEach(input => input.disabled = true);
            } else {
                timeLeft--;
            }
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    </script>
</body>
</html>