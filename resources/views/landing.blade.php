<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SapiSehat - Sistem Pakar Diagnosa Penyakit Sapi</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2c7da0;
            --primary-dark: #1a5276;
            --primary-light: #61a5c2;
            --secondary: #a4c639;
            --secondary-dark: #8aa62c;
            --accent: #ff9e44;
            --accent-dark: #e68a3d;
            --light: #f8f9fa;
            --dark: #2d3e50;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --success: #28a745;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            color: var(--dark);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Pattern */
        .background-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(44, 125, 160, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(164, 198, 57, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 158, 68, 0.02) 0%, transparent 50%);
            z-index: -1;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 10px 0;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary) !important;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 10px;
            color: var(--secondary);
        }

        .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
            margin: 0 8px;
            transition: all 0.3s ease;
            position: relative;
            padding: 8px 16px !important;
            border-radius: 50px;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary) !important;
            background-color: rgba(44, 125, 160, 0.08);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 70%;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            color: white;
            font-weight: 500;
            padding: 10px 24px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(44, 125, 160, 0.2);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(44, 125, 160, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            border: none;
            color: white;
            font-weight: 500;
            padding: 10px 24px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(164, 198, 57, 0.2);
        }

        .btn-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(164, 198, 57, 0.3);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .welcome-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 249, 250, 0.9) 100%);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .welcome-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            font-size: 24px;
            margin-right: 20px;
            box-shadow: 0 5px 15px rgba(44, 125, 160, 0.3);
        }

        .welcome-text h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .welcome-text p {
            color: var(--gray);
            margin-bottom: 0;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 8px;
        }

        .user-badge.admin {
            background: rgba(255, 158, 68, 0.15);
            color: var(--accent-dark);
        }

        .user-badge.user {
            background: rgba(164, 198, 57, 0.15);
            color: var(--secondary-dark);
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--gray);
            margin-bottom: 40px;
            max-width: 600px;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-hero {
            padding: 16px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
        }

        .btn-hero i {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .btn-hero:hover i {
            transform: translateX(5px);
        }

        .hero-image {
            position: relative;
            text-align: center;
        }

        .cow-illustration {
            max-width: 100%;
            filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.15));
            animation: float 6s ease-in-out infinite;
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background-color: white;
            position: relative;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .section-title p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            transition: height 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .feature-card:hover::before {
            height: 100%;
            opacity: 0.03;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(44, 125, 160, 0.25);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .feature-card p {
            color: var(--gray);
            margin-bottom: 0;
        }

        /* Process Section */
        .process-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
            position: relative;
        }

        .process-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            height: 100%;
            text-align: center;
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .process-number {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(44, 125, 160, 0.25);
        }

        .process-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .process-card p {
            color: var(--gray);
            margin-bottom: 0;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .cta-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .cta-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta-subtitle {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-cta {
            background: white;
            color: var(--primary);
            padding: 16px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: var(--primary);
        }

        /* Footer */
        .footer {
            background-color: var(--dark);
            color: white;
            padding: 80px 0 30px;
        }

        .footer-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            display: inline-block;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .footer-links a i {
            margin-right: 10px;
            font-size: 14px;
        }

        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }

        .copyright {
            text-align: center;
            padding-top: 40px;
            margin-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
        }

        /* Success Alert */
        .alert-success-custom {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
            border: 1px solid rgba(40, 167, 69, 0.2);
            color: var(--dark);
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        /* Animations */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-float {
            animation: float 5s ease-in-out infinite;
        }

        /* Login Required Modal */
        .login-required-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .login-required-modal .modal-header {
            border-bottom: none;
            padding: 30px 30px 0;
        }

        .login-required-modal .modal-body {
            padding: 20px 30px 30px;
            text-align: center;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            font-size: 32px;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .welcome-card {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.3rem;
            }

            .hero-content {
                text-align: center;
            }

            .hero-subtitle {
                margin: 0 auto 30px;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .welcome-content {
                flex-direction: column;
                text-align: center;
            }

            .welcome-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }

            .btn-hero,
            .btn-cta {
                padding: 14px 28px;
                font-size: 1rem;
            }

            .feature-card,
            .process-card {
                padding: 30px 20px;
            }

            .feature-icon {
                width: 70px;
                height: 70px;
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <!-- Background Pattern -->
    <div class="background-pattern"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <i class="fas fa-cow"></i> SapiSehat
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('landing') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                    
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <!-- Hanya tampilkan Dashboard untuk Admin -->
                            @if(Auth::user()->role === 'admin')
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @endif
                            
                            <li><a class="dropdown-item" href="{{ route('diagnosa.index') }}"><i class="fas fa-stethoscope me-2"></i>Diagnosa</a></li>
                            <li><a class="dropdown-item" href="{{ route('diagnosa.riwayat') }}"><i class="fas fa-history me-2"></i>Riwayat Diagnosa</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                    @endauth
                    
                    <li class="nav-item ms-2">
                        @auth
                            <a href="{{ route('diagnosa.index') }}" class="btn btn-primary-custom">Mulai Diagnosa</a>
                        @else
                            <a href="#" class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#loginRequiredModal">Mulai Diagnosa</a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Success Message -->
    @if(session('success'))
    <div class="container mt-4">
        <div class="alert alert-success-custom d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-3 text-success" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="container mt-4">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <!-- Welcome Message -->
                    @auth
                    <div class="welcome-card">
                        <div class="d-flex welcome-content">
                            <div class="welcome-icon">
                                <i class="fas fa-hand-wave"></i>
                            </div>
                            <div class="welcome-text">
                                <h3>Selamat datang, {{ Auth::user()->name }}!</h3>
                                <p>
                                    @if(Auth::user()->role == 'admin')
                                    <span class="user-badge admin">
                                        <i class="fas fa-crown me-2"></i> Administrator
                                    </span>
                                    @else
                                    <span class="user-badge user">
                                        <i class="fas fa-user me-2"></i> User - Siap untuk mendiagnosa kesehatan sapi Anda?
                                    </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endauth

                    <h1 class="hero-title">Diagnosa <span>Penyakit Sapi</span> Lebih Cepat & Akurat</h1>
                    <p class="hero-subtitle">Sistem pakar berbasis web dengan metode Certainty Factor untuk mendeteksi penyakit pada sapi Simmental secara real-time.</p>
                    <div class="hero-buttons">
                        @auth
                            <a href="{{ route('diagnosa.index') }}" class="btn btn-primary-custom btn-hero">
                                Mulai Diagnosa <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="{{ route('diagnosa.riwayat') }}" class="btn btn-secondary-custom btn-hero">
                                Lihat Riwayat
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary-custom btn-hero">
                                Daftar Sekarang <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="#tentang" class="btn btn-secondary-custom btn-hero">
                                Pelajari Lebih Lanjut
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 hero-image">
                    <img src="{{ asset('img/sapii.jpg') }}" alt="Sapi Sehat" class="cow-illustration" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDUwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI1MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjZjhmYWZjIi8+CjxwYXRoIGQ9Ik0yNTAgMTUwQzI5NS4yMjkgMTUwIDMzMCAxODQuNzcxIDMzMCAyMzBDMzMwIDI3NS4yMjkgMjk1LjIyOSAzMTAgMjUwIDMxMEMyMDQuNzcxIDMxMCAxNzAgMjc1LjIyOSAxNzAgMjMwQzE3MCAxODQuNzcxIDIwNC43NzEgMTUwIDI1MCAxNTBaIiBmaWxsPSIjNjFhNWMyIiBmaWxsLW9wYWNpdHk9IjAuMiIvPgo8cGF0aCBkPSJNMjUwIDE2MEMyODkuODIzIDE2MCAzMjAgMTkwLjE3NyAzMjAgMjMwQzMyMCAyNjkuODIzIDI4OS44MjMgMzAwIDI1MCAzMDBDMjEwLjE3NyAzMDAgMTgwIDI2OS44MjMgMTgwIDIzMEMxODAgMTkwLjE3NyAyMTAuMTc3IDE2MCAyNTAgMTYwWiIgZmlsbD0iIzJjN2RhMCIgZmlsbC1vcGFjaXR5PSIwLjMiLz4KPHN2Zz4KPC9zdmc+'">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="tentang">
        <div class="container">
            <div class="section-title">
                <h2>Keunggulan SapiSehat</h2>
                <p>Platform diagnosa penyakit sapi terdepan dengan teknologi modern</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Diagnosa Real-time</h3>
                        <p>Dapatkan hasil diagnosa secara instan dengan sistem yang bekerja cepat dan efisien.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3>Metode CF Teruji</h3>
                        <p>Menggunakan metode Certainty Factor yang telah teruji untuk hasil diagnosa yang akurat.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h3>Basis Pengetahuan Luas</h3>
                        <p>Database lengkap gejala dan penyakit sapi berdasarkan pengetahuan ahli peternakan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="section-title">
                <h2>Cara Kerja Sistem</h2>
                <p>Tiga langkah mudah untuk diagnosa yang akurat</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="process-card">
                        <div class="process-number">1</div>
                        <h3>Pilih Gejala</h3>
                        <p>Identifikasi dan pilih gejala yang dialami oleh sapi Anda dari daftar yang tersedia.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="process-card">
                        <div class="process-number">2</div>
                        <h3>Analisis Sistem</h3>
                        <p>Sistem menganalisis gejala menggunakan metode Certainty Factor untuk menentukan kemungkinan penyakit.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="process-card">
                        <div class="process-number">3</div>
                        <h3>Hasil & Solusi</h3>
                        <p>Dapatkan diagnosa penyakit dan rekomendasi penanganan yang tepat untuk sapi Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Siap Deteksi Kesehatan Sapi Anda?</h2>
                <p class="cta-subtitle">Bergabunglah dengan peternak lainnya yang telah menggunakan SapiSehat untuk menjaga kesehatan ternak mereka. Dapatkan diagnosa yang akurat dan solusi yang tepat.</p>
                @auth
                    <a href="{{ route('diagnosa.index') }}" class="btn btn-cta">
                        Mulai Diagnosa Sekarang <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-cta">
                        Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="footer-title">SapiSehat</h4>
                    <p>Sistem pakar diagnosa penyakit sapi berbasis web dengan metode Certainty Factor untuk membantu peternak menjaga kesehatan ternak mereka.</p>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h4 class="footer-title">Tautan</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('landing') }}"><i class="fas fa-chevron-right"></i> Beranda</a></li>
                        <li><a href="#tentang"><i class="fas fa-chevron-right"></i> Tentang</a></li>
                        @auth
                        <li><a href="{{ route('diagnosa.index') }}"><i class="fas fa-chevron-right"></i> Diagnosa</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h4 class="footer-title">Layanan</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('diagnosa.index') }}"><i class="fas fa-chevron-right"></i> Diagnosa Online</a></li>
                        <li><a href="{{ route('diagnosa.riwayat') }}"><i class="fas fa-chevron-right"></i> Riwayat Diagnosa</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 mb-4">
                    <h4 class="footer-title">Kontak Kami</h4>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-map-marker-alt"></i> Jl. Peternakan No. 123, Jakarta</a></li>
                        <li><a href="#"><i class="fas fa-phone"></i> +62 21 1234 5678</a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i> info@sapisehat.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 SapiSehat - Sistem Diagnosa Penyakit Sapi. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Login Required Modal -->
    <div class="modal fade login-required-modal" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="login-icon">
                        <i class="fas fa-user-lock"></i>
                    </div>
                    <h3 class="mb-3">Login Diperlukan</h3>
                    <p class="mb-4">Anda perlu login terlebih dahulu untuk mengakses fitur diagnosa penyakit sapi.</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('login') }}" class="btn btn-primary-custom">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-secondary-custom">Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Add smooth scrolling
        $(document).ready(function() {
            $('a[href*="#"]').on('click', function(e) {
                if ($(this).attr('href') !== '#') {
                    e.preventDefault();

                    $('html, body').animate({
                        scrollTop: $($(this).attr('href')).offset().top - 70
                    }, 500, 'linear');
                }
            });

            // Navbar background on scroll
            $(window).scroll(function() {
                if ($(window).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });

            // Auto show login modal if redirected from protected route
            @if(session('login_required'))
                $('#loginRequiredModal').modal('show');
            @endif

            // Auto dismiss alerts after 5 seconds
            $('.alert').delay(5000).fadeOut(300);
        });
    </script>
</body>
</html>