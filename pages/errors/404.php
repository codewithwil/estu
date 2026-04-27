<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | ESTU CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --black: #0a0a0a;
            --dark-gray: #171717;
            --gray-900: #262626;
            --gray-800: #333333;
            --gray-700: #404040;
            --gray-600: #525252;
            --gray-500: #737373;
            --gray-400: #a3a3a3;
            --gray-300: #d4d4d4;
            --white: #ffffff;
            --red: #ef4444;
            --blue: #3b82f6;
            --blue-dark: #2563eb;
            --purple: #8b5cf6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--black);
            color: var(--white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .bg-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--blue);
            border-radius: 50%;
            opacity: 0.3;
            animation: float 15s infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .grid-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 1;
        }

        /* Glow orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            z-index: 1;
        }

        .glow-orb-1 {
            width: 400px;
            height: 400px;
            background: var(--blue);
            top: -100px;
            right: -100px;
            animation: pulse 8s ease-in-out infinite;
        }

        .glow-orb-2 {
            width: 300px;
            height: 300px;
            background: var(--purple);
            bottom: -50px;
            left: -50px;
            animation: pulse 10s ease-in-out infinite reverse;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.15; }
            50% { transform: scale(1.2); opacity: 0.25; }
        }

        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px 20px;
            max-width: 600px;
        }

        .error-code {
            font-size: 180px;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--white) 0%, var(--gray-500) 50%, var(--blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            letter-spacing: -8px;
            animation: glitch 3s infinite;
        }

        @keyframes glitch {
            0%, 90%, 100% {
                text-shadow: none;
                transform: translate(0);
            }
            92% {
                text-shadow: -2px 0 var(--red), 2px 0 var(--blue);
                transform: translate(2px, 0);
            }
            94% {
                text-shadow: 2px 0 var(--red), -2px 0 var(--blue);
                transform: translate(-2px, 0);
            }
            96% {
                text-shadow: -1px 0 var(--red), 1px 0 var(--blue);
                transform: translate(1px, 0);
            }
        }

        .error-code::after {
            content: '404';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(135deg, var(--blue) 0%, var(--purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            opacity: 0.1;
            filter: blur(20px);
            transform: translateY(10px);
        }

        .error-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 32px;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.05) 100%);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--red);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            20% { transform: rotate(-10deg); }
            40% { transform: rotate(10deg); }
            60% { transform: rotate(-5deg); }
            80% { transform: rotate(5deg); }
        }

        .error-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--white);
        }

        .error-desc {
            font-size: 16px;
            color: var(--gray-400);
            line-height: 1.7;
            margin-bottom: 40px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background: var(--blue);
            color: white;
        }

        .btn-primary:hover {
            background: var(--blue-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -8px rgba(59, 130, 246, 0.5);
        }

        .btn-secondary {
            background: var(--gray-800);
            color: var(--gray-200);
            border: 1px solid var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-700);
            border-color: var(--gray-600);
            color: var(--white);
        }

        .terminal-path {
            background: var(--gray-900);
            border: 1px solid var(--gray-800);
            border-radius: 12px;
            padding: 16px 20px;
            margin: 32px auto;
            max-width: 400px;
            text-align: left;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: var(--gray-400);
            position: relative;
            overflow: hidden;
        }

        .terminal-path::before {
            content: '$';
            color: var(--green);
            margin-right: 8px;
        }

        .terminal-path .cursor {
            display: inline-block;
            width: 8px;
            height: 18px;
            background: var(--blue);
            animation: blink 1s step-end infinite;
            vertical-align: middle;
            margin-left: 2px;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .terminal-path .error-text {
            color: var(--red);
        }

        @media (max-width: 640px) {
            .error-code {
                font-size: 120px;
                letter-spacing: -4px;
            }

            .error-title {
                font-size: 22px;
            }

            .error-desc {
                font-size: 14px;
            }

            .btn {
                padding: 12px 20px;
                font-size: 14px;
                width: 100%;
                justify-content: center;
            }

            .actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-particles" id="particles"></div>
    <div class="grid-bg"></div>
    <div class="glow-orb glow-orb-1"></div>
    <div class="glow-orb glow-orb-2"></div>

    <div class="container">
        <div class="error-icon">
            <i class="fas fa-ghost"></i>
        </div>

        <div class="error-code">404</div>

        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">
            Sepertinya halaman yang Anda cari telah menghilang ke dimensi lain. 
            Mungkin sudah dipindahkan, dihapus, atau tidak pernah ada.
        </p>

        <div class="terminal-path">
            <span class="error-text">Error:</span> Route not found<br>
            <span class="error-text">Path:</span> <span id="currentPath">/<?php echo htmlspecialchars($_GET['url'] ?? ''); ?></span><span class="cursor"></span>
        </div>

        <div class="actions">
            <a href="<?php echo (function_exists('url') ? url('dashboard') : 'dashboard'); ?>" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Kembali ke Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Halaman Sebelumnya
            </a>
        </div>
    </div>

    <script>
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 15 + 's';
            particle.style.animationDuration = (10 + Math.random() * 10) + 's';
            particle.style.width = (2 + Math.random() * 4) + 'px';
            particle.style.height = particle.style.width;
            particlesContainer.appendChild(particle);
        }
    </script>
</body>
</html>