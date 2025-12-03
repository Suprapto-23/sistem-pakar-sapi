<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard - {{ config('app.name', 'SapiSehat') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <style>
        :root {
            --primary: #2c7da0;
            --primary-dark: #1a5a7a;
            --primary-light: #61a5c2;
            --secondary: #a4c639;
            --secondary-dark: #8aac1e;
            --accent: #ff9e44;
            --accent-dark: #e68a3a;
            --light-bg: #f8f9fa;
            --dark-text: #343a40;
            --gray-light: #e9ecef;
            --gray-medium: #adb5bd;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--light-bg);
        }

        .sidebar {
            transition: all 0.3s;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
        }

        .stats-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .action-btn {
            padding: 12px 20px;
            border-radius: 8px;
            text-align: center;
            color: white;
            text-decoration: none;
            display: block;
            transition: all 0.3s;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            color: white;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
            border: none;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform md:relative md:translate-x-0 transition duration-200 ease-in-out z-30" id="sidebar">
            <!-- Logo -->
            <div class="text-white flex items-center space-x-2 px-4">
                <span class="text-2xl font-bold">🐄 SapiSehat</span>
                <span class="text-sm font-semibold">Admin</span>
            </div>

            <!-- Navigation -->
            <nav>
                <a href="{{ route('admin.dashboard') }}" 
                   class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="{{ route('admin.penyakit.index') }}" 
                   class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.penyakit.*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    <i class="fas fa-disease mr-2"></i>Data Penyakit
                </a>
                <a href="{{ route('admin.gejala.index') }}" 
                   class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.gejala.*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    <i class="fas fa-clipboard-list mr-2"></i>Data Gejala
                </a>
                <a href="{{ route('admin.aturan.index') }}" 
                   class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.aturan.*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    <i class="fas fa-cogs mr-2"></i>Aturan Diagnosa
                </a>
                <a href="{{ route('admin.diagnosa.index') }}" 
                   class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.diagnosa.*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    <i class="fas fa-stethoscope mr-2"></i>Riwayat Diagnosa
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700' }}">
                    <i class="fas fa-users mr-2"></i>Manajemen User
                </a>
                
                <!-- Divider -->
                <div class="border-t border-green-700 my-2"></div>
                
                <a href="{{ route('landing') }}" 
                   class="block py-2.5 px-4 rounded transition duration-200 text-green-100 hover:bg-green-700">
                    <i class="fas fa-globe mr-2"></i>Kembali ke Site
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block py-2.5 px-4 rounded transition duration-200 text-green-100 hover:bg-green-700">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between p-4">
                    <!-- Mobile menu button -->
                    <button class="md:hidden rounded-lg focus:outline-none focus:shadow-outline" onclick="toggleSidebar()">
                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                        <div class="relative">
                            <button class="flex items-center space-x-2 focus:outline-none" onclick="toggleUserMenu()">
                                <div class="user-avatar">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </button>
                            
                            <!-- User Dropdown -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-50" id="userMenu">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-green-600">
                                    <i class="fas fa-home mr-2"></i>Dashboard
                                </a>
                            </li>
                            @if(isset($breadcrumb))
                                @foreach($breadcrumb as $item)
                                    <li>
                                        <div class="flex items-center">
                                            <span class="mx-2 text-gray-400">›</span>
                                            @if(isset($item['url']))
                                                <a href="{{ $item['url'] }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-green-600 md:ml-2">
                                                    {{ $item['label'] }}
                                                </a>
                                            @else
                                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                                                    {{ $item['label'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ol>
                    </nav>
                </div>

                <!-- Notifications -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Page Content -->
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('mobile-open');
        }

        function toggleUserMenu() {
            const userMenu = document.getElementById('userMenu');
            userMenu.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            if (!userButton && userMenu && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('[role="alert"]');
                alerts.forEach(function(alert) {
                    alert.style.display = 'none';
                });
            }, 5000);
        });

        // SweetAlert for CRUD operations
        function confirmDelete(formId, itemName) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus ${itemName}. Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        function showSuccess(message) {
            Swal.fire({
                title: 'Berhasil!',
                text: message,
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }

        function showError(message) {
            Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }

        // Show success message if exists in session
        @if(session('success'))
            showSuccess('{{ session('success') }}');
        @endif

        // Show error message if exists in session
        @if(session('error'))
            showError('{{ session('error') }}');
        @endif
    </script>

    @stack('scripts')
</body>
</html>