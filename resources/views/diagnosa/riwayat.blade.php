<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Diagnosa - SapiSehat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --secondary: #3b82f6;
            --accent: #f59e0b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .diagnosa-gradient {
            background: linear-gradient(135deg, #f0fdf4 0%, #f0f9ff 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.5s ease-out;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        
        .confidence-high { background: linear-gradient(135deg, #10b981, #059669); }
        .confidence-medium { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .confidence-low { background: linear-gradient(135deg, #ef4444, #dc2626); }
        
        .status-completed { background: linear-gradient(135deg, #10b981, #059669); }
        .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .status-failed { background: linear-gradient(135deg, #ef4444, #dc2626); }
        
        .print-only { display: none; }
        
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .break-before { page-break-before: always; }
            .break-after { page-break-after: always; }
            .break-inside { page-break-inside: avoid; }
            
            body {
                background: white !important;
                font-size: 12pt;
            }
            
            .bg-gradient-to-br { background: white !important; }
            .shadow-lg { box-shadow: none !important; }
            .border { border: 1px solid #e5e7eb !important; }
        }
        
        .table-report {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-report th,
        .table-report td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            text-align: left;
        }
        
        .table-report th {
            background-color: #f8fafc;
            font-weight: 600;
        }
        
        .empty-state {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
    </style>
</head>
<body class="font-sans antialiased diagnosa-gradient min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                        <span class="text-2xl">🐄</span>
                        <span class="text-xl font-bold text-green-600">SapiSehat</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('landing') }}" class="text-gray-700 hover:text-green-600 font-medium transition duration-300 flex items-center">
                        <i class="fas fa-home mr-2"></i>
                        Beranda
                    </a>
                    <a href="{{ route('diagnosa.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition duration-300 flex items-center">
                        <i class="fas fa-stethoscope mr-2"></i>
                        Diagnosa Baru
                    </a>
                    <a href="{{ route('diagnosa.riwayat') }}" class="text-green-600 font-medium transition duration-300 flex items-center">
                        <i class="fas fa-history mr-2"></i>
                        Riwayat
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 hover:text-green-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="text-center mb-8 fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <i class="fas fa-history text-3xl text-green-600"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Riwayat Diagnosa</h1>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Lihat semua hasil diagnosa yang pernah Anda lakukan</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 fade-in">
            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Diagnosa</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $riwayat->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-stethoscope text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Berhasil</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $riwayat->where('status', 'completed')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Rata-rata CF</p>
                        <p class="text-2xl font-bold text-orange-600">
                            {{ number_format($riwayat->avg('cf_tertinggi') * 100, 1) }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Diagnosa Terakhir</p>
                        <p class="text-lg font-bold text-gray-800">
                            @if($riwayat->count() > 0)
                                {{ $riwayat->first()->created_at->diffForHumans() }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 card-hover fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Cari berdasarkan penyakit atau gejala..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               id="searchInput">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <select class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="completed">Berhasil</option>
                        <option value="pending">Menunggu</option>
                        <option value="failed">Gagal</option>
                    </select>
                    
                    <select class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            id="confidenceFilter">
                        <option value="">Semua Keyakinan</option>
                        <option value="high">Tinggi (≥80%)</option>
                        <option value="medium">Sedang (50-79%)</option>
                        <option value="low">Rendah (≤49%)</option>
                    </select>
                </div>
            </div>
        </div>

        @if($riwayat->count() > 0)
        <!-- Diagnosa List -->
        <div class="space-y-6 fade-in" id="diagnosaList">
            @foreach($riwayat as $diagnosa)
            @php
                $hasilPerhitungan = is_string($diagnosa->hasil_perhitungan) ? 
                                   json_decode($diagnosa->hasil_perhitungan, true) : 
                                   $diagnosa->hasil_perhitungan;
                
                $penyakitTertinggi = $hasilPerhitungan['penyakit_tertinggi'] ?? null;
                $cfTertinggi = $hasilPerhitungan['cf_tertinggi'] ?? $diagnosa->cf_tertinggi;
                
                // Get gejala yang dipilih
                $gejalaTerpilih = is_string($diagnosa->gejala_terpilih) ? 
                                 json_decode($diagnosa->gejala_terpilih, true) : 
                                 $diagnosa->gejala_terpilih;
                
                $jumlahGejala = is_array($gejalaTerpilih) ? count($gejalaTerpilih) : 0;
                
                // Confidence level
                $confidenceLevel = 'rendah';
                $confidenceColor = 'confidence-low';
                $confidenceBadge = 'bg-red-100 text-red-800';
                if ($cfTertinggi >= 0.8) {
                    $confidenceLevel = 'tinggi';
                    $confidenceColor = 'confidence-high';
                    $confidenceBadge = 'bg-green-100 text-green-800';
                } elseif ($cfTertinggi >= 0.5) {
                    $confidenceLevel = 'sedang';
                    $confidenceColor = 'confidence-medium';
                    $confidenceBadge = 'bg-yellow-100 text-yellow-800';
                }
                
                // Status styling
                $statusBadge = 'bg-gray-100 text-gray-800';
                if ($diagnosa->status === 'completed') {
                    $statusBadge = 'bg-green-100 text-green-800';
                } elseif ($diagnosa->status === 'pending') {
                    $statusBadge = 'bg-yellow-100 text-yellow-800';
                } elseif ($diagnosa->status === 'failed') {
                    $statusBadge = 'bg-red-100 text-red-800';
                }
            @endphp

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover diagnosa-item"
                 data-status="{{ $diagnosa->status }}"
                 data-confidence="{{ $confidenceLevel }}"
                 data-penyakit="{{ strtolower($penyakitTertinggi['nama'] ?? '') }}"
                 data-gejala="{{ $jumlahGejala }}">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Left Section - Diagnosis Info -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-1">
                                        @if($penyakitTertinggi && $penyakitTertinggi['nama'])
                                            {{ $penyakitTertinggi['nama'] }}
                                        @else
                                            Diagnosis Tidak Ditemukan
                                        @endif
                                    </h3>
                                    <p class="text-gray-600 text-sm">
                                        ID: #{{ $diagnosa->id }} • 
                                        {{ $diagnosa->created_at->format('d M Y, H:i') }} • 
                                        {{ $jumlahGejala }} gejala dipilih
                                    </p>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusBadge }} capitalize">
                                        {{ $diagnosa->status === 'completed' ? 'Selesai' : ($diagnosa->status === 'pending' ? 'Menunggu' : 'Gagal') }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $confidenceBadge }} capitalize">
                                        Keyakinan {{ $confidenceLevel }}
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span>Tingkat Keyakinan</span>
                                    <span class="font-semibold">{{ number_format($cfTertinggi * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full {{ $confidenceColor }} progress-bar" 
                                         style="width: {{ $cfTertinggi * 100 }}%"></div>
                                </div>
                            </div>

                            <!-- Disease Details -->
                            @if($penyakitTertinggi && $penyakitTertinggi['nama'])
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Kode Penyakit:</span>
                                    <span class="font-semibold text-gray-800 ml-2">{{ $penyakitTertinggi['kode'] ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Solusi:</span>
                                    <span class="font-semibold text-gray-800 ml-2">
                                        {{ Str::limit($penyakitTertinggi['solusi'] ?? 'Tidak tersedia', 50) }}
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Section - Actions -->
                        <div class="flex flex-col sm:flex-row lg:flex-col gap-3">
                            <a href="{{ route('diagnosa.show', $diagnosa->id) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition transform hover:scale-105">
                                <i class="fas fa-eye mr-2"></i>
                                Lihat Detail
                            </a>
                            
                            <a href="{{ route('diagnosa.export.pdf', $diagnosa->id) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition transform hover:scale-105">
                                <i class="fas fa-download mr-2"></i>
                                Download PDF
                            </a>
                            
                            <form action="{{ route('diagnosa.destroy', $diagnosa->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus diagnosa ini?')"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition transform hover:scale-105">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 fade-in">
            {{ $riwayat->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center empty-state fade-in">
            <div class="max-w-md mx-auto">
                <div class="text-6xl mb-6">🔍</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Belum Ada Riwayat Diagnosa</h3>
                <p class="text-gray-600 mb-8">
                    Anda belum melakukan diagnosa apapun. Mulai diagnosa pertama Anda untuk melihat riwayat di sini.
                </p>
                <a href="{{ route('diagnosa.index') }}" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition transform hover:scale-105">
                    <i class="fas fa-stethoscope mr-2"></i>
                    Mulai Diagnosa Pertama
                </a>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8 no-print">
            <a href="{{ route('diagnosa.index') }}" 
               class="inline-flex items-center justify-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition transform hover:scale-105">
                <i class="fas fa-stethoscope mr-2"></i>
                Diagnosa Baru
            </a>
            
            <a href="{{ route('landing') }}" 
               class="inline-flex items-center justify-center px-6 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-xl font-bold transition transform hover:scale-105">
                <i class="fas fa-home mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const confidenceFilter = document.getElementById('confidenceFilter');
            const diagnosaItems = document.querySelectorAll('.diagnosa-item');

            function filterDiagnosa() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const confidenceValue = confidenceFilter.value;

                diagnosaItems.forEach(item => {
                    const penyakit = item.getAttribute('data-penyakit') || '';
                    const gejala = item.getAttribute('data-gejala') || '';
                    const status = item.getAttribute('data-status');
                    const confidence = item.getAttribute('data-confidence');

                    const matchesSearch = penyakit.includes(searchTerm) || 
                                         gejala.toString().includes(searchTerm);
                    const matchesStatus = !statusValue || status === statusValue;
                    const matchesConfidence = !confidenceValue || confidence === confidenceValue;

                    if (matchesSearch && matchesStatus && matchesConfidence) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterDiagnosa);
            statusFilter.addEventListener('change', filterDiagnosa);
            confidenceFilter.addEventListener('change', filterDiagnosa);

            // Progress bar animation
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });

            // Fade-in animation on scroll
            const fadeElements = document.querySelectorAll('.fade-in');
            
            const fadeInOnScroll = () => {
                fadeElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('visible');
                    }
                });
            };
            
            window.addEventListener('scroll', fadeInOnScroll);
            fadeInOnScroll();

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>