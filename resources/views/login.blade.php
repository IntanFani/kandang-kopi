<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kandang Kopi - Login</title>
    
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo">
                <div class="logo-icon">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Kandang Kopi">
                </div>
                <div class="logo-text">
                    <h1>KANDANG KOPI</h1>
                    <p>Sistem Pencatatan Keuangan Coffe Shop</p>
                </div>
            </div>

            <div class="box-login">
                <h2 class="login-title">LOGIN KE AKUN ANDA</h2>

                <form action="/login" method="POST">
                    @csrf
                    
                    @if(session()->has('loginError'))
                        <div class="alert-error" style="color: #ff4d4d; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">
                            <i class="bi bi-exclamation-circle"></i> {{ session('loginError') }}
                        </div>
                    @endif

                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="login-btn">Login Sekarang</button>
                </form>
            </div>
        </div>

        <div class="login-right">
            <img src="{{ asset('assets/images/logo kandang kopi.png') }}" style="width: 300px; height: 300px;" alt="Branding Kandang Kopi">
        </div>
    </div>

    <div class="footer">
        &copy; 2025 Kandang Kopi | Sistem Pencatatan Keuangan Coffe Shop
    </div>

</body>
</html>