<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESTU CMS - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --black: #0a0a0a;
            --dark-gray: #171717;
            --gray-800: #262626;
            --gray-700: #404040;
            --gray-600: #525252;
            --gray-500: #737373;
            --gray-400: #a3a3a3;
            --gray-300: #d4d4d4;
            --gray-200: #e5e5e5;
            --gray-100: #f5f5f5;
            --white: #ffffff;
            --error: #dc2626;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, var(--black) 0%, var(--dark-gray) 50%, var(--gray-800) 100%);
            position: relative;
            overflow: hidden;
        }

        /* Subtle grid pattern */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            margin: auto;
            padding: 40px;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        /* Left Side - Branding */
        .brand-side {
            flex: 1;
            color: var(--white);
        }

        .logo-large {
            font-size: 72px;
            font-weight: 700;
            letter-spacing: -4px;
            margin-bottom: 24px;
            background: linear-gradient(135deg, var(--white) 0%, var(--gray-400) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tagline {
            font-size: 18px;
            color: var(--gray-400);
            line-height: 1.6;
            max-width: 400px;
            font-weight: 300;
        }

        .features {
            margin-top: 48px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--gray-300);
            font-size: 14px;
        }

        .feature-icon {
            width: 24px;
            height: 24px;
            border: 1px solid var(--gray-700);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--white);
        }

        /* Right Side - Form */
        .form-side {
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(10px);
            border: 1px solid var(--gray-800);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            margin-bottom: 32px;
        }

        .card-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 8px;
        }

        .card-header p {
            font-size: 14px;
            color: var(--gray-500);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-300);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrap {
            position: relative;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            background: var(--black);
            border: 1px solid var(--gray-800);
            border-radius: 8px;
            color: var(--white);
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
            outline: none;
        }

        input:focus {
            border-color: var(--gray-600);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.05);
        }

        input::placeholder {
            color: var(--gray-600);
        }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-pass:hover {
            color: var(--white);
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 24px 0;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--gray-400);
        }

        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--white);
            cursor: pointer;
        }

        .forgot {
            color: var(--gray-400);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot:hover {
            color: var(--white);
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--white) 0%, var(--gray-200) 100%);
            color: var(--black);
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 30px -10px rgba(255, 255, 255, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .divider {
            margin: 28px 0;
            text-align: center;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gray-800), transparent);
        }

        .divider span {
            background: transparent;
            padding: 0 16px;
            color: var(--gray-600);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
        }

        .social-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .social-btn {
            padding: 12px;
            background: transparent;
            border: 1px solid var(--gray-800);
            border-radius: 8px;
            color: var(--gray-300);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .social-btn:hover {
            border-color: var(--gray-700);
            background: rgba(255, 255, 255, 0.02);
            color: var(--white);
        }

        .signup {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: var(--gray-500);
        }

        .signup a {
            color: var(--white);
            text-decoration: none;
            font-weight: 600;
            margin-left: 4px;
        }

        .signup a:hover {
            text-decoration: underline;
        }

        .error-msg {
            color: var(--error);
            font-size: 12px;
            margin-top: 6px;
            display: none;
        }

        .error-msg.show {
            display: block;
        }

        input.error {
            border-color: var(--error);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .container {
                flex-direction: column;
                gap: 40px;
                padding: 24px;
            }

            .brand-side {
                text-align: center;
            }

            .tagline {
                margin: 0 auto;
            }

            .features {
                align-items: center;
            }

            .form-side {
                max-width: 100%;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .logo-large {
                font-size: 48px;
            }

            .login-card {
                padding: 28px 24px;
            }

            .social-login {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Brand Side -->
        <div class="brand-side">
            <div class="logo-large">ESTU</div>
            <p class="tagline">
                Content Management System yang dirancang untuk performa, 
                keamanan, dan kemudahan penggunaan.
            </p>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">✓</div>
                    <span>Keamanan enterprise-grade</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">✓</div>
                    <span>Performa ultra-cepat</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">✓</div>
                    <span>Interface intuitif</span>
                </div>
            </div>
        </div>

        <!-- Form Side -->
        <div class="form-side">
            <div class="login-card">
                <div class="card-header">
                    <h2>Masuk ke Akun</h2>
                    <p>Silakan masukkan kredensial Anda</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <p style="color:red;">Email atau password salah</p>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="process/login.php">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            placeholder="nama@perusahaan.com"
                            autocomplete="email"
                        >
                        <div class="error-msg">Format email tidak valid</div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                            >
                            <button type="button" class="toggle-pass" onclick="togglePassword()">
                                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <div class="error-msg">Password minimal 6 karakter</div>
                    </div>

                    <div class="options">
                        <label class="remember">
                            <input type="checkbox" id="remember">
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" class="forgot">Lupa password?</a>
                    </div>

                    <button type="submit" class="submit-btn">Masuk</button>
                </form>

                <div class="divider">
                    <span>atau</span>
                </div>

                <div class="social-login">
                    <button class="social-btn" onclick="socialLogin('google')">
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </button>
                    <button class="social-btn" onclick="socialLogin('github')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        GitHub
                    </button>
                </div>

                <div class="signup">
                    Belum punya akun?<a href="#">Daftar sekarang</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        function socialLogin(provider) {
            console.log('Login dengan ' + provider);
        }

        if (localStorage.getItem('estu_email')) {
            document.getElementById('email').value = localStorage.getItem('estu_email');
            document.getElementById('remember').checked = true;
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            if (document.getElementById('remember').checked) {
                localStorage.setItem('estu_email', document.getElementById('email').value);
            } else {
                localStorage.removeItem('estu_email');
            }
        });
    </script>
</body>
</html> 