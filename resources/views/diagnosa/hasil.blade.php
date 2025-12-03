<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Diagnosa - SapiSehat</title>
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
                    <a href="{{ route('diagnosa.riwayat') }}" class="text-gray-700 hover:text-purple-600 font-medium transition duration-300 flex items-center">
                        <i class="fas fa-history mr-2"></i>
                        Riwayat
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @php
            // Process data from diagnosis
            $hasilPerhitungan = is_string($diagnosa->hasil_perhitungan) ? 
                               json_decode($diagnosa->hasil_perhitungan, true) : 
                               $diagnosa->hasil_perhitungan;
            
            $rankingPenyakit = $hasilPerhitungan['hasil_perhitungan'] ?? [];
            $penyakitTertinggi = $hasilPerhitungan['penyakit_tertinggi'] ?? null;
            $cfTertinggi = $hasilPerhitungan['cf_tertinggi'] ?? 0;
            
            // Get gejala yang dipilih
            $gejalaTerpilih = is_string($diagnosa->gejala_terpilih) ? 
                             json_decode($diagnosa->gejala_terpilih, true) : 
                             $diagnosa->gejala_terpilih;
            
            $gejalas = \App\Models\Gejala::whereIn('id', $gejalaTerpilih)->get();
            
            // Confidence level
            $confidenceLevel = 'rendah';
            $confidenceColor = 'confidence-low';
            if ($cfTertinggi >= 0.8) {
                $confidenceLevel = 'tinggi';
                $confidenceColor = 'confidence-high';
            } elseif ($cfTertinggi >= 0.5) {
                $confidenceLevel = 'sedang';
                $confidenceColor = 'confidence-medium';
            }
            
            // Check if there's a valid diagnosis
            $hasValidDiagnosis = $penyakitTertinggi && $cfTertinggi > 0 && 
                                ($penyakitTertinggi['nama'] ?? '') !== 'Tidak diketahui';
        @endphp

        <!-- Print Header -->
        <div class="print-only bg-white p-6 mb-6 border-b">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-3xl">🐄</span>
                    <div>
                        <h1 class="text-2xl font-bold text-green-600">SapiSehat</h1>
                        <p class="text-gray-600">Sistem Pakar Diagnosa Penyakit Sapi</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-gray-600">Tanggal: {{ $diagnosa->created_at->format('d/m/Y H:i') }}</p>
                    <p class="text-gray-600">ID Diagnosa: #{{ $diagnosa->id }}</p>
                </div>
            </div>
        </div>

        <!-- Header Section -->
        <div class="text-center mb-8 fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <i class="fas fa-file-medical-alt text-3xl text-green-600"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Hasil Diagnosa Penyakit Sapi</h1>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Laporan lengkap hasil analisis sistem pakar menggunakan metode Certainty Factor</p>
        </div>

        <!-- Diagnosis Summary -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 card-hover fade-in">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Diagnosis Info -->
                <div class="md:col-span-2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Ringkasan Diagnosa</h2>
                    
                    @if($hasValidDiagnosis)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-200">
                            <div>
                                <h3 class="text-xl font-bold text-green-800">Diagnosis Utama</h3>
                                <p class="text-2xl font-bold text-green-600">{{ $penyakitTertinggi['nama'] ?? 'N/A' }}</p>
                                <p class="text-green-700">Kode: {{ $penyakitTertinggi['kode'] ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-green-600">
                                    {{ number_format($cfTertinggi * 100, 1) }}%
                                </div>
                                <div class="text-sm text-green-700 capitalize">Tingkat Keyakinan {{ $confidenceLevel }}</div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">🤔</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak Dapat Menentukan Diagnosis</h3>
                        <p class="text-gray-600">Sistem tidak menemukan penyakit yang sesuai dengan gejala yang dipilih.</p>
                    </div>
                    @endif
                </div>

                <!-- Confidence Meter -->
                <div class="flex flex-col items-center justify-center">
                    <div class="relative w-32 h-32 mb-4">
                        <svg class="w-full h-full" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e7eb" stroke-width="12" />
                            <circle cx="60" cy="60" r="54" fill="none" 
                                    stroke="{{ $confidenceColor === 'confidence-high' ? '#10b981' : ($confidenceColor === 'confidence-medium' ? '#f59e0b' : '#ef4444') }}" 
                                    stroke-width="12" stroke-linecap="round"
                                    stroke-dasharray="{{ 2 * 3.14159 * 54 }}" 
                                    stroke-dashoffset="{{ 2 * 3.14159 * 54 * (1 - $cfTertinggi) }}" 
                                    transform="rotate(-90 60 60)" />
                            <text x="60" y="65" text-anchor="middle" font-size="24" font-weight="bold" 
                                  fill="{{ $confidenceColor === 'confidence-high' ? '#10b981' : ($confidenceColor === 'confidence-medium' ? '#f59e0b' : '#ef4444') }}">
                                {{ number_format($cfTertinggi * 100, 0) }}%
                            </text>
                        </svg>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-gray-800 capitalize">Keyakinan {{ $confidenceLevel }}</div>
                        <div class="text-sm text-gray-600">{{ $confidenceLevel === 'tinggi' ? 'Sangat Yakin' : ($confidenceLevel === 'sedang' ? 'Cukup Yakin' : 'Kurang Yakin') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($hasValidDiagnosis)
        <!-- Detailed Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Disease Description -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Disease Details -->
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover fade-in">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Informasi Penyakit
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Deskripsi</h4>
                            <p class="text-gray-600 leading-relaxed">
                                {{ $penyakitTertinggi['deskripsi'] ?? 'Deskripsi tidak tersedia' }}
                            </p>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Rekomendasi Penanganan</h4>
                            <p class="text-gray-600 leading-relaxed">
                                {{ $penyakitTertinggi['solusi'] ?? 'Rekomendasi penanganan tidak tersedia' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Selected Symptoms -->
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover fade-in break-inside">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-clipboard-check text-green-500 mr-2"></i>
                        Gejala yang Dipilih ({{ count($gejalas) }} gejala)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="table-report">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Gejala</th>
                                    <th>Nama Gejala</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gejalas as $index => $gejala)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="font-semibold">{{ $gejala->kode }}</td>
                                    <td>{{ $gejala->nama }}</td>
                                    <td class="text-sm">{{ $gejala->deskripsi ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="space-y-6">
                <!-- Diagnosis Metadata -->
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover fade-in">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                        Informasi Diagnosa
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">ID Diagnosa</span>
                            <span class="font-semibold text-gray-800">#{{ $diagnosa->id }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Tanggal</span>
                            <span class="font-semibold text-gray-800">{{ $diagnosa->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Total Gejala</span>
                            <span class="font-semibold text-gray-800">{{ count($gejalas) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Status</span>
                            <span class="font-semibold text-green-600 capitalize">{{ $diagnosa->status ?? 'Selesai' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Pengguna</span>
                            <span class="font-semibold text-gray-800">{{ $diagnosa->user->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Confidence Information -->
                <div class="bg-white rounded-2xl shadow-lg p-6 card-hover fade-in">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-orange-500 mr-2"></i>
                        Tingkat Keyakinan
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 mb-2">
                                {{ number_format($cfTertinggi * 100, 1) }}%
                            </div>
                            <div class="text-sm text-gray-600">Nilai Certainty Factor</div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tinggi</span>
                                <span class="text-gray-600">≥ 80%</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Sedang</span>
                                <span class="text-gray-600">50% - 79%</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Rendah</span>
                                <span class="text-gray-600">≤ 49%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 card-hover fade-in break-before">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-microscope text-purple-500 mr-2"></i>
                Analisis Detail Certainty Factor
            </h3>
            
            <div class="overflow-x-auto">
                <table class="table-report">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Penyakit</th>
                            <th>Kode</th>
                            <th>CF Akhir</th>
                            <th>Persentase</th>
                            <th>Gejala Terdeteksi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankingPenyakit as $index => $result)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-semibold">{{ $result['nama'] ?? 'N/A' }}</td>
                            <td>{{ $result['kode'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ number_format($result['cf_akhir'] ?? 0, 3) }}</td>
                            <td class="text-center">{{ number_format($result['persentase'] ?? 0, 1) }}%</td>
                            <td class="text-center">{{ count($result['gejala_terdeteksi'] ?? []) }}</td>
                            <td class="text-center">
                                @if($index === 0)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    Diagnosis Utama
                                </span>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                                    Alternatif
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 no-print">
            <a href="{{ route('diagnosa.index') }}" 
               class="inline-flex items-center justify-center px-6 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-xl font-bold transition transform hover:scale-105">
                <i class="fas fa-stethoscope mr-2"></i>
                Diagnosa Baru
            </a>
            
            <a href="{{ route('diagnosa.riwayat') }}" 
               class="inline-flex items-center justify-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition transform hover:scale-105">
                <i class="fas fa-history mr-2"></i>
                Lihat Riwayat
            </a>
            
            <a href="{{ route('diagnosa.export.pdf', $diagnosa->id) }}" 
               class="inline-flex items-center justify-center px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition transform hover:scale-105">
                <i class="fas fa-download mr-2"></i>
                Download PDF
            </a>
            
            <button onclick="window.print()" 
                    class="inline-flex items-center justify-center px-6 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold transition transform hover:scale-105">
                <i class="fas fa-print mr-2"></i>
                Print Laporan
            </button>
        </div>

        <!-- Important Notice -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mb-6 fade-in">
            <div class="flex items-start">
                <span class="text-yellow-500 text-2xl mr-4">💡</span>
                <div>
                    <h4 class="font-bold text-yellow-800 text-lg mb-2">Penting untuk Diketahui</h4>
                    <div class="text-yellow-700 space-y-2">
                        <p>• Hasil diagnosa ini merupakan perkiraan berdasarkan sistem pakar menggunakan metode Certainty Factor</p>
                        <p>• Tingkat akurasi bergantung pada kelengkapan dan keakuratan gejala yang dipilih</p>
                        <p>• Disarankan untuk berkonsultasi dengan dokter hewan untuk diagnosis dan penanganan yang lebih akurat</p>
                        <p>• Laporan ini bersifat informatif dan tidak menggantikan pemeriksaan medis oleh profesional</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Information -->
        <div class="text-center text-gray-500 text-sm mt-8 pb-4">
            <p>Laporan ini dibuat secara otomatis oleh Sistem Pakar SapiSehat</p>
            <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Animasi progress bar dan elemen fade-in
        document.addEventListener('DOMContentLoaded', function() {
            // Animasi progress bars
            const progressBars = document.querySelectorAll('.bg-green-600, .bg-yellow-600, .bg-orange-600, .bg-blue-600');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });

            // Animasi fade-in pada scroll
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
        });
    </script>
</body>
</html>