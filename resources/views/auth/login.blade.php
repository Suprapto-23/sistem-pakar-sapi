<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SapiSehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #10b981;
            --accent: #f59e0b;
            --light-bg: #f8fafc;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }
        
        .cow-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        
        .btn-primary-custom:active {
            transform: translateY(0);
        }
        
        .input-custom {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            transition: all 0.3s ease;
            background: #ffffff;
        }
        
        .input-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: #ffffff;
        }
        
        .demo-card {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fcd34d;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }
        
        .demo-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        
        .feature-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.1) 100%);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .password-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: #374151;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .pulse-dot {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <!-- Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-32 w-80 h-80 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
        <div class="absolute top-40 left-1/2 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-4000"></div>
    </div>
    
    <div class="relative w-full max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <!-- Left Side - Brand & Features -->
            <div class="text-center lg:text-left text-white">
                <!-- Brand Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-center lg:justify-start space-x-4 mb-6">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform">
                            <span class="text-3xl">🐄</span>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">SapiSehat</h1>
                            <div class="flex items-center space-x-2 mt-1 justify-center lg:justify-start">
                                <div class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></div>
                                <p class="text-sm opacity-80">Sistem Online</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-xl opacity-90 mb-4">Sistem Pakar Diagnosa Penyakit Sapi</p>
                    <p class="text-lg opacity-80 max-w-md mx-auto lg:mx-0">Solusi cerdas untuk kesehatan ternak Anda dengan teknologi Certainty Factor terdepan</p>
                </div>

                <!-- Animated Illustration -->
                <div class="hidden lg:block mb-8">
                    <div class="relative">
                        <div class="cow-animation">
                            <div class="w-80 h-80 mx-auto bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-2xl">
                                <div class="text-center">
                                    <span class="text-8xl">🐄</span>
                                    <p class="text-sm mt-4 opacity-80 font-medium">Diagnosa Akurat & Cepat</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Floating elements -->
                        <div class="absolute -top-4 -left-4 w-20 h-20 bg-yellow-400/30 rounded-full blur-xl"></div>
                        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-green-400/30 rounded-full blur-xl"></div>
                    </div>
                </div>

                <!-- Features List -->
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="feature-card p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-bolt text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold">Diagnosa Real-time</h4>
                                <p class="text-sm opacity-80">Hasil instan dan akurat</p>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-brain text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold">Metode CF</h4>
                                <p class="text-sm opacity-80">Certainty Factor teruji</p>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-chart-line text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold">Laporan Detail</h4>
                                <p class="text-sm opacity-80">Analisis menyeluruh</p>
                            </div>
                        </div>
                    </div>
                    <div class="feature-card p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-shield-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold">Data Terproteksi</h4>
                                <p class="text-sm opacity-80">Keamanan terjamin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-container p-8 lg:p-12">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Selamat Datang Kembali</h2>
                    <p class="text-gray-600 mt-2">Masuk untuk mengakses dashboard sistem pakar</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl">
                        <div class="flex items-center space-x-3 mb-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <span class="font-semibold">Terjadi kesalahan:</span>
                        </div>
                        <ul class="list-disc list-inside text-sm ml-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>Alamat Email
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="email"
                            class="input-custom w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="masukkan email anda"
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-lock mr-2 text-gray-400"></i>Password
                        </label>
                        <div class="password-container">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                class="input-custom w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-12"
                                placeholder="masukkan password anda"
                            >
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input 
                                    id="remember_me" 
                                    type="checkbox" 
                                    name="remember"
                                    class="sr-only"
                                >
                                <div class="w-4 h-4 bg-gray-100 border-2 border-gray-300 rounded flex items-center justify-center transition-colors checkbox-style">
                                    <svg class="w-3 h-3 text-white opacity-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="ml-2 text-sm text-gray-600 select-none">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a 
                                href="{{ route('password.request') }}" 
                                class="text-sm text-blue-600 hover:text-blue-500 transition-colors font-medium"
                            >
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn-primary-custom w-full text-white text-lg font-semibold py-4 mb-6">
                        <i class="fas fa-sign-in-alt mr-3"></i>Masuk ke Sistem
                    </button>

                    <!-- Register Link -->
                    <div class="text-center pt-6 border-t border-gray-200">
                        <p class="text-gray-600">
                            Belum punya akun?
                            <a 
                                href="{{ route('register') }}" 
                                class="font-semibold text-blue-600 hover:text-blue-500 transition-colors ml-1"
                            >
                                Daftar di sini
                            </a>
                        </p>
                    </div>
                </form>

                <!-- Demo Accounts -->
                <div class="mt-8">
                    <div class="demo-card p-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-vial text-white text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-yellow-800 text-sm mb-3">Akun Demo untuk Testing</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-yellow-700 font-medium text-xs">Admin:</span>
                                        <div class="text-right">
                                            <div class="font-mono text-xs bg-yellow-100 px-2 py-1 rounded">admin@sapi.com</div>
                                            <div class="font-mono text-xs text-yellow-600 mt-1">password: password</div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-yellow-700 font-medium text-xs">User:</span>
                                        <div class="text-right">
                                            <div class="font-mono text-xs bg-yellow-100 px-2 py-1 rounded">user@sapi.com</div>
                                            <div class="font-mono text-xs text-yellow-600 mt-1">password: password</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                    <div class="stats-card p-4">
                        <div class="text-blue-600 font-bold text-xl">5+</div>
                        <div class="text-blue-500 text-xs font-medium">Penyakit</div>
                    </div>
                    <div class="stats-card p-4">
                        <div class="text-green-600 font-bold text-xl">10+</div>
                        <div class="text-green-500 text-xs font-medium">Gejala</div>
                    </div>
                    <div class="stats-card p-4">
                        <div class="text-purple-600 font-bold text-xl">100+</div>
                        <div class="text-purple-500 text-xs font-medium">Diagnosa</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        .checkbox-style {
            transition: all 0.2s ease;
        }
        
        input:checked + .checkbox-style {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        
        input:checked + .checkbox-style svg {
            opacity: 1;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');
            
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            // Custom checkbox styling
            const rememberCheckbox = document.getElementById('remember_me');
            const checkboxStyle = document.querySelector('.checkbox-style');
            
            rememberCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    checkboxStyle.classList.add('bg-blue-500', 'border-blue-500');
                } else {
                    checkboxStyle.classList.remove('bg-blue-500', 'border-blue-500');
                }
            });
            
            // Add loading state to login button
            const loginForm = document.querySelector('form');
            loginForm.addEventListener('submit', function() {
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                submitButton.disabled = true;
            });
        });
    </script>
</body>
</html>