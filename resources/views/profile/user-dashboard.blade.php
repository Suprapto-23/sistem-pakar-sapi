<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - SapiSehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        .content-transition {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="h-full" x-data="{ sidebarOpen: window.innerWidth >= 1024, mobileMenuOpen: false }" @resize.window="sidebarOpen = window.innerWidth >= 1024">
    <!-- Sidebar Backdrop (mobile) -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 flex z-40 lg:hidden" 
         @click="mobileMenuOpen = false">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" aria-hidden="true"></div>
    </div>

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-800 sidebar-transition transform lg:translate-x-0 lg:static lg:inset-0"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         x-show="true">
        
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 bg-blue-900 px-4">
            <span class="text-white text-xl font-bold flex items-center space-x-2">
                <span>🐄</span>
                <span>SapiSehat</span>
            </span>
        </div>
        
        <!-- Navigation -->
        <nav class="mt-8 px-4 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-lg group {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
                <span class="text-lg">🏠</span>
                <span class="ml-3 font-medium">Dashboard</span>
            </a>

            <!-- Diagnosa -->
            <div class="px-4 py-2 text-blue-300 text-sm font-semibold uppercase tracking-wider">
                Diagnosa
            </div>
            
            <a href="{{ route('diagnosa') }}" 
               class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-lg group {{ request()->routeIs('diagnosa') ? 'bg-blue-700' : '' }}">
                <span class="text-lg">🔍</span>
                <span class="ml-3 font-medium">Diagnosa Baru</span>
            </a>
            
            <a href="{{ route('diagnosa.riwayat') }}" 
               class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-lg group {{ request()->routeIs('diagnosa.riwayat') ? 'bg-blue-700' : '' }}">
                <span class="text-lg">📋</span>
                <span class="ml-3 font-medium">Riwayat Diagnosa</span>
            </a>

            <!-- Akun -->
            <div class="px-4 py-2 text-blue-300 text-sm font-semibold uppercase tracking-wider">
                Akun
            </div>

            <a href="{{ route('profile.edit') }}" 
               class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-lg group {{ request()->routeIs('profile.edit') ? 'bg-blue-700' : '' }}">
                <span class="text-lg">👤</span>
                <span class="ml-3 font-medium">Profil Saya</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-lg group">
                    <span class="text-lg">🚪</span>
                    <span class="ml-3 font-medium">Logout</span>
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="content-transition flex-1 flex flex-col lg:ml-64">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Left side - Menu toggle & Page title -->
                <div class="flex items-center">
                    <button @click="mobileMenuOpen = true" 
                            class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="hidden lg:block p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 ml-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                    </button>

                    <!-- Dynamic Page Title -->
                    <div class="ml-4">
                        <h1 class="text-xl font-semibold text-gray-800">
                            Selamat Datang, {{ Auth::user()->name }}!
                        </h1>
                    </div>
                </div>

                <!-- Right side - User menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700 hidden sm:block">
                        Status: <span class="user-text font-semibold">User</span>
                    </span>
                    
                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border">
                            <div class="px-4 py-2 border-b">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">
                                    <span class="user-text">User</span>
                                </p>
                            </div>
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <span class="mr-2">👤</span>
                                Profil Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <span class="mr-2">🚪</span>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 pb-8">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content -->
            <div class="px-4 sm:px-6 lg:px-8 py-8">
                <!-- Welcome Card -->
                <div class="bg-gradient-to-r from-blue-500 to-green-600 rounded-2xl p-8 text-white mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold mb-2">
                                Selamat Datang, {{ Auth::user()->name }}! 👋
                            </h1>
                            <p class="text-blue-100 opacity-90 text-lg">
                                Siap untuk mendiagnosa kesehatan sapi Anda?
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <span class="text-6xl">🐄</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <a href="{{ route('diagnosa') }}" class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow group">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 p-4 rounded-xl">
                                <span class="text-2xl text-green-600">🔍</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600">Diagnosa Baru</h3>
                                <p class="text-sm text-gray-600 mt-1">Mulai diagnosa penyakit sapi</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('diagnosa.riwayat') }}" class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow group">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 p-4 rounded-xl">
                                <span class="text-2xl text-blue-600">📋</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">Riwayat Diagnosa</h3>
                                <p class="text-sm text-gray-600 mt-1">Lihat hasil diagnosa sebelumnya</p>
                            </div>
                        </div>
                    </a>

                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 p-4 rounded-xl">
                                <span class="text-2xl text-purple-600">ℹ️</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Info Akun</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Status: <span class="user-text font-semibold">User</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Aktivitas Terbaru</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600">🔍</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Diagnosa Penyakit</p>
                                    <p class="text-sm text-gray-500">Mulai diagnosa baru untuk sapi Anda</p>
                                </div>
                            </div>
                            <a href="{{ route('diagnosa') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                Mulai
                            </a>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600">📋</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Lihat Riwayat</p>
                                    <p class="text-sm text-gray-500">Cek hasil diagnosa sebelumnya</p>
                                </div>
                            </div>
                            <a href="{{ route('diagnosa.riwayat') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                Lihat
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
                    <div class="flex items-start">
                        <span class="text-yellow-500 text-xl mr-4">💡</span>
                        <div>
                            <h4 class="font-semibold text-yellow-800">Tips Diagnosa yang Akurat:</h4>
                            <ul class="text-yellow-700 text-sm mt-2 space-y-1">
                                <li>• Pastikan Anda mengamati gejala dengan teliti</li>
                                <li>• Pilih semua gejala yang sesuai dengan kondisi sapi</li>
                                <li>• Hasil diagnosa merupakan perkiraan berdasarkan sistem pakar</li>
                                <li>• Disarankan untuk berkonsultasi dengan dokter hewan untuk penanganan lebih lanjut</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Save sidebar state to localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarState = localStorage.getItem('sidebarOpen');
            if (sidebarState !== null) {
                Alpine.data('sidebarOpen', JSON.parse(sidebarState));
            }

            // Watch for sidebar state changes
            Alpine.effect(() => {
                localStorage.setItem('sidebarOpen', JSON.stringify(Alpine.$data.sidebarOpen));
            });
        });
    </script>

    <style>
        .user-text {
            color: #ef4444;
            font-weight: 600;
        }
    </style>
</body>
</html>