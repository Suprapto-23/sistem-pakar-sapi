<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SapiSehat - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        #sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            height: 100vh;
            position: fixed;
            transition: all var(--transition-speed) ease;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        #sidebar .sidebar-header {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #sidebar .sidebar-header h3 {
            font-size: 1.2rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
        }

        #sidebar.collapsed .sidebar-header h3 {
            display: none;
        }

        #sidebar .sidebar-content {
            padding: 15px 0;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }

        #sidebar ul.components {
            padding: 0;
            list-style: none;
        }

        #sidebar ul li {
            padding: 0 15px;
        }

        #sidebar ul li a {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
            white-space: nowrap;
            overflow: hidden;
        }

        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        #sidebar ul li a i {
            margin-right: 10px;
            font-size: 1.1rem;
            min-width: 25px;
            text-align: center;
        }

        #sidebar.collapsed ul li a span {
            display: none;
        }

        #sidebar.collapsed ul li a i {
            margin-right: 0;
        }

        /* Main Content Styles */
        #content {
            margin-left: var(--sidebar-width);
            transition: all var(--transition-speed) ease;
            min-height: 100vh;
        }

        #content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Navbar Styles */
        .top-navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 25px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .navbar-right {
            display: flex;
            align-items: center;
        }

        #sidebarToggle {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 1.2rem;
            margin-right: 15px;
            cursor: pointer;
        }

        .user-info {
            display: flex;
            align-items: center;
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
            margin-right: 10px;
            font-weight: bold;
        }

        /* Card Styles */
        .dashboard-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 2.5rem;
            opacity: 0.7;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            color: #6c757d;
        }

        .stats-card .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .stats-card .card-trend {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        .quick-action-card .card-body {
            padding: 1.5rem;
        }

        .action-btn {
            padding: 15px;
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

        .action-btn i {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .action-btn span {
            display: block;
            font-weight: 600;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Activity List */
        .activity-item {
            border-left: 3px solid var(--primary);
            padding-left: 15px;
            margin-bottom: 20px;
        }

        .activity-item.success {
            border-left-color: var(--success);
        }

        .activity-item.warning {
            border-left-color: var(--warning);
        }

        .activity-item.danger {
            border-left-color: var(--danger);
        }

        /* DataTables Customization */
        .dataTables_wrapper {
            padding: 0;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            padding: 1rem;
        }

        .dataTables_wrapper .dataTables_info {
            padding: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding: 1rem;
        }

        .dt-buttons {
            padding: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -var(--sidebar-width);
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
            }

            #content.expanded {
                margin-left: 0;
            }

            .top-navbar {
                padding: 10px 15px;
            }
        }

        /* Border left colors for stats cards */
        .border-left-primary {
            border-left: 4px solid var(--primary) !important;
        }
        
        .border-left-success {
            border-left: 4px solid var(--success) !important;
        }
        
        .border-left-info {
            border-left: 4px solid var(--info) !important;
        }
        
        .border-left-warning {
            border-left: 4px solid var(--warning) !important;
        }

        /* Table Styles */
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

        /* Form Styles */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(44, 125, 160, 0.25);
        }

        /* Modal Styles */
        .modal-header {
            background-color: var(--primary);
            color: white;
        }

        /* Tab Styles */
        .nav-tabs .nav-link.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .nav-tabs .nav-link {
            color: var(--primary);
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-cow me-2"></i>SapiSehat</h3>
        </div>

        <div class="sidebar-content">
            <ul class="components">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.penyakit.index') }}" class="{{ request()->routeIs('admin.penyakit.*') ? 'active' : '' }}">
                        <i class="fas fa-disease"></i>
                        <span>Kelola Penyakit</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.gejala.index') }}" class="{{ request()->routeIs('admin.gejala.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Kelola Gejala</span>
                    </a>
                </li>
                <li class="nav-item">
    <a class="nav-link" href="{{ route('admin.kelola-aturan') }}">
        <i class="fas fa-project-diagram me-2"></i>
        Kelola Aturan
    </a>
</li>
                <li>
                    <a href="{{ route('admin.diagnosa.index') }}" class="{{ request()->routeIs('admin.diagnosa.*') ? 'active' : '' }}">
                        <i class="fas fa-stethoscope"></i>
                        <span>Data Diagnosa</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.laporan.index') }}" class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.statistik') }}" class="{{ request()->routeIs('admin.statistik') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>Statistik</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Manajemen User</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="navbar-content">
                <div class="navbar-left">
                    <button id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0" id="pageTitle">
                        @yield('page_title', 'Dashboard')
                    </h4>
                </div>
                <div class="navbar-right">
                    <div class="user-info me-3">
                        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div class="user-details">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role text-muted">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="container-fluid p-4">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

    <script>
        // Initialize DataTables
        $(document).ready(function() {
            // Initialize all tables with DataTables
            $('.data-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });

        // Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const sidebarToggle = document.getElementById('sidebarToggle');

            // Toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('expanded');

                // Change icon based on state
                const icon = this.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-chevron-right');
                } else {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-bars');
                }
            });

            // Mobile sidebar toggle
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                content.classList.add('expanded');
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                    content.classList.add('expanded');
                } else {
                    sidebar.classList.remove('collapsed');
                    content.classList.remove('expanded');
                }
            });
        });

        // SweetAlert Confirm Delete
        function confirmDelete(type, id, name) {
            const typeNames = {
                'penyakit': 'Penyakit',
                'gejala': 'Gejala',
                'diagnosa': 'Diagnosa',
                'user': 'User'
            };
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus data ${typeNames[type]} "${name}". Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form deletion
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }

        // Initialize charts
        function initializeCharts() {
            // Diagnosa Chart
            const diagnosaCtx = document.getElementById('diagnosaChart');
            if (diagnosaCtx) {
                new Chart(diagnosaCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
                        datasets: [{
                            label: 'Jumlah Diagnosa',
                            data: [12, 19, 15, 22, 18, 25, 30, 28, 35, 42],
                            borderColor: '#2c7da0',
                            backgroundColor: 'rgba(44, 125, 160, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            // Laporan Chart
            const laporanCtx = document.getElementById('laporanChart');
            if (laporanCtx) {
                new Chart(laporanCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Mulut & Kuku', 'Bloat', 'Mastitis', 'Scabies', 'Lainnya'],
                        datasets: [{
                            label: 'Jumlah Diagnosa',
                            data: [42, 35, 28, 15, 8],
                            backgroundColor: [
                                'rgba(44, 125, 160, 0.8)',
                                'rgba(164, 198, 57, 0.8)',
                                'rgba(255, 158, 68, 0.8)',
                                'rgba(220, 53, 69, 0.8)',
                                'rgba(108, 117, 125, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        }

        // Initialize charts on page load
        document.addEventListener('DOMContentLoaded', initializeCharts);
    </script>

    @yield('scripts')
</body>
</html>