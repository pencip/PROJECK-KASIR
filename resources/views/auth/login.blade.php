<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Resto POS</title>
    <link rel="stylesheet" href="{{ asset('css/dark-theme.css') }}">
    <style>
        body {
            background-color: var(--bg-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .login-glass-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 440px;
            padding: 36px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.7);
            position: relative;
        }

        .login-brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-badge-logo {
            width: 54px;
            height: 54px;
            margin: 0 auto 14px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: var(--shadow-glow);
        }

        .login-brand-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .login-brand-header p {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .demo-selector-box {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid var(--border-subtle);
        }

        .demo-selector-box p {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 12px;
            text-align: center;
        }

        .demo-btn-row {
            display: flex;
            gap: 8px;
        }

        .btn-demo-pill {
            flex: 1;
            padding: 8px 4px;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-pill);
            color: var(--text-secondary);
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-demo-pill:hover {
            background-color: var(--bg-pill);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .error-alert-box {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="login-glass-card">
    <div class="login-brand-header">
        <div class="brand-badge-logo">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 3v12a6 6 0 0 0 12 0V3"></path>
            </svg>
        </div>
        <h2>Sistem Resto POS</h2>
        <p>Masuk ke akun operasional dan analitik</p>
    </div>

    @if($errors->any())
        <div class="error-alert-box">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group-dark">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control-dark" value="{{ old('email', 'kasir@resto.com') }}" required autofocus>
        </div>

        <div class="form-group-dark">
            <label for="password">Kata Sandi</label>
            <input type="password" id="password" name="password" class="form-control-dark" value="password" required>
        </div>

        <button type="submit" class="btn-white-pill" style="width: 100%; padding: 13px; margin-top: 10px;">
            Masuk ke Sistem &rarr;
        </button>
    </form>

    <div class="demo-selector-box">
        <p>Pilih Akun Demo (1-Klik):</p>
        <div class="demo-btn-row">
            <button type="button" class="btn-demo-pill" onclick="quickFill('kasir@resto.com')">Kasir</button>
            <button type="button" class="btn-demo-pill" onclick="quickFill('manajer@resto.com')">Manajer</button>
            <button type="button" class="btn-demo-pill" onclick="quickFill('admin@resto.com')">Admin</button>
        </div>
    </div>
</div>

<script>
    function quickFill(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
    }
</script>

</body>
</html>
