<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosa - SapiSehat</title>
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
        
        .checkbox-custom {
            appearance: none;
            -webkit-appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .checkbox-custom:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .checkbox-custom:checked::after {
            content: "✓";
            position: absolute;
            color: white;
            font-size: 0.875rem;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .radio-custom {
            appearance: none;
            -webkit-appearance: none;
            width: 1rem;
            height: 1rem;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            background-color: white;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .radio-custom:checked {
            border-color: var(--primary);
        }
        
        .radio-custom:checked::after {
            content: "";
            position: absolute;
            width: 0.5rem;
            height: 0.5rem;
            background-color: var(--primary);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
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
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @media (max-width: 768px) {
            .mobile-stack {
                flex-direction: column;
            }
            
            .mobile-full {
                width: 100%;
            }
            
            .mobile-text-center {
                text-align: center;
            }
            
            .mobile-p-4 {
                padding: 1rem;
            }
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Loading animation */
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #10b981;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="font-sans antialiased diagnosa-gradient min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                        <div class="relative">
                            <span class="text-2xl floating-animation">🐄</span>
                        </div>
                        <span class="text-xl font-bold text-green-600">SapiSehat</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('landing') }}" class="text-gray-700 hover:text-green-600 font-medium transition duration-300 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Beranda
                    </a>
                    <a href="{{ route('diagnosa.riwayat') }}" class="text-gray-700 hover:text-blue-600 font-medium transition duration-300 flex items-center">
                        <i class="fas fa-history mr-2"></i>
                        Riwayat Diagnosa
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="open = !open" class="text-gray-700 hover:text-green-600 p-2 transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar -->
    <div x-data="{ open: false }" class="md:hidden">
        <!-- Overlay -->
        <div x-show="open" 
             @click="open = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300"
             :class="open ? 'opacity-100' : 'opacity-0 pointer-events-none'">
        </div>
        
        <!-- Sidebar -->
        <div x-show="open" 
             class="fixed top-0 right-0 h-full w-64 bg-white shadow-xl z-50 p-6 transition-transform duration-300"
             :class="open ? 'translate-x-0' : 'translate-x-full'">
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3" @click="open = false">
                    <span class="text-2xl">🐄</span>
                    <span class="text-xl font-bold text-green-600">SapiSehat</span>
                </a>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="flex flex-col space-y-6">
                <a href="{{ route('landing') }}" @click="open = false" class="text-gray-700 hover:text-green-600 font-medium py-2 transition duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-3"></i>
                    Kembali ke Beranda
                </a>
                <a href="{{ route('diagnosa.riwayat') }}" @click="open = false" class="text-gray-700 hover:text-blue-600 font-medium py-2 transition duration-300 flex items-center">
                    <i class="fas fa-history mr-3"></i>
                    Riwayat Diagnosa
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-8 fade-in mobile-text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <i class="fas fa-stethoscope text-2xl text-green-600"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Diagnosa Penyakit Sapi</h1>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Pilih gejala yang dialami oleh sapi Anda untuk mendapatkan diagnosa yang akurat menggunakan metode Certainty Factor</p>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 card-hover fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
                <div class="flex items-center justify-center md:justify-start">
                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold mr-3 text-lg">1</div>
                    <span class="text-lg font-semibold text-gray-800">Pilih Gejala</span>
                </div>
                <div class="flex items-center justify-center md:justify-start">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold mr-3 text-lg">2</div>
                    <span class="text-lg font-medium text-gray-500">Hasil Diagnosa</span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div id="progressBar" class="bg-green-600 h-3 rounded-full progress-bar w-0"></div>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mt-2">
                <span id="progressText">Pilih gejala untuk memulai</span>
                <span id="progressPercent">0%</span>
            </div>
        </div>

        <!-- Diagnosa Form -->
        <form action="{{ route('diagnosa.store') }}" method="POST" id="diagnosaForm" class="bg-white rounded-2xl shadow-sm overflow-hidden fade-in">
            @csrf
            
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 border-b border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Daftar Gejala</h2>
                        <p class="text-gray-600">Centang gejala yang dialami sapi. Sistem akan menganalisis menggunakan metode Certainty Factor.</p>
                    </div>
                    <div class="bg-white rounded-lg px-4 py-2 shadow-sm">
                        <span class="text-sm font-medium text-gray-700">Total Gejala:</span>
                        <span class="text-lg font-bold text-green-600 ml-2">{{ $gejalas->count() }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Symptoms List -->
            <div class="p-4 md:p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                @if($gejalas->count() > 0)
                <div class="space-y-4">
                    @foreach($gejalas as $index => $item)
                    <div class="border border-gray-200 rounded-xl card-hover overflow-hidden transition-all duration-300 bg-white" 
                         x-data="{ checked: false }">
                        <div class="p-4 md:p-6">
                            <div class="flex items-start space-x-4">
                                <input type="checkbox" 
                                       name="gejala[]" 
                                       value="{{ $item->id }}" 
                                       id="gejala-{{ $item->id }}" 
                                       class="checkbox-custom mt-1 flex-shrink-0"
                                       x-model="checked"
                                       @change="updateProgress()">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                                        <div class="flex-1">
                                            <label for="gejala-{{ $item->id }}" 
                                                   class="block text-lg font-semibold text-gray-800 cursor-pointer hover:text-green-700 transition-colors mb-2">
                                                <span class="bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded mr-2">{{ $item->kode }}</span>
                                                {{ $item->nama }}
                                            </label>
                                            @if($item->deskripsi)
                                            <p class="text-gray-600 text-sm mb-3 leading-relaxed">{{ $item->deskripsi }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Confidence Level - Mobile Optimized -->
                                        <div class="w-full md:w-auto" x-show="checked" x-transition>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat Keyakinan:</label>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                                                @foreach([
                                                    'sangat_yakin' => ['value' => 1.0, 'label' => 'Sangat Yakin', 'color' => 'bg-green-500', 'bg' => 'bg-green-50', 'border' => 'border-green-200'],
                                                    'yakin' => ['value' => 0.8, 'label' => 'Yakin', 'color' => 'bg-green-400', 'bg' => 'bg-green-50', 'border' => 'border-green-200'],
                                                    'cukup_yakin' => ['value' => 0.6, 'label' => 'Cukup Yakin', 'color' => 'bg-yellow-500', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200'],
                                                    'sedikit_yakin' => ['value' => 0.4, 'label' => 'Sedikit Yakin', 'color' => 'bg-orange-400', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200'],
                                                    'tidak_yakin' => ['value' => 0.2, 'label' => 'Tidak Yakin', 'color' => 'bg-red-400', 'bg' => 'bg-red-50', 'border' => 'border-red-200']
                                                ] as $key => $option)
                                                <label class="flex items-center space-x-2 p-2 rounded-lg border cursor-pointer transition-all duration-200 hover:shadow-sm {{ $option['bg'] }} {{ $option['border'] }} text-xs">
                                                    <input type="radio" 
                                                           name="cf_user[{{ $item->id }}]" 
                                                           value="{{ $option['value'] }}" 
                                                           class="radio-custom flex-shrink-0"
                                                           @if($loop->first) checked @endif>
                                                    <span class="flex-1 font-medium text-gray-700 whitespace-nowrap">{{ $option['label'] }}</span>
                                                    <span class="w-2 h-2 rounded-full {{ $option['color'] }} flex-shrink-0"></span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Data Gejala Belum Tersedia</h3>
                    <p class="text-gray-600 max-w-md mx-auto">Silakan hubungi administrator untuk menambahkan data gejala ke dalam sistem.</p>
                </div>
                @endif
            </div>

            <!-- Submit Button -->
            @if($gejalas->count() > 0)
            <div class="border-t border-gray-200 p-6 bg-gray-50">
                <div class="text-center">
                    <button type="submit" 
                            id="submitButton"
                            class="btn-primary text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg transition-all duration-300 mobile-full disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="flex items-center justify-center space-x-3">
                            <i class="fas fa-stethoscope"></i>
                            <span>Proses Diagnosa</span>
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </button>
                    <p class="text-gray-500 text-sm mt-3 flex items-center justify-center space-x-1">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span>Sistem akan menganalisis menggunakan metode Certainty Factor</span>
                    </p>
                </div>
            </div>
            @endif
        </form>

        <!-- Info Box -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6 fade-in card-hover">
            <div class="flex flex-col md:flex-row md:items-start gap-4">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                    <i class="fas fa-lightbulb text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-blue-800 text-lg mb-3">Tips Diagnosa Akurat</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-blue-700">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                            <span>Pilih semua gejala yang sesuai dengan kondisi sapi</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                            <span>Periksa sapi dengan teliti sebelum memilih gejala</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                            <span>Tentukan tingkat keyakinan berdasarkan pengamatan</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                            <span>Semakin banyak gejala yang tepat, hasil semakin akurat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 fade-in">
            <a href="{{ route('landing') }}" class="bg-white rounded-xl p-4 border border-gray-200 flex items-center justify-center space-x-3 card-hover text-gray-700 hover:text-green-600 transition-colors group">
                <i class="fas fa-arrow-left text-gray-400 group-hover:text-green-600 transition-colors"></i>
                <span class="font-medium">Kembali ke Beranda</span>
            </a>
            <a href="{{ route('diagnosa.riwayat') }}" class="bg-white rounded-xl p-4 border border-gray-200 flex items-center justify-center space-x-3 card-hover text-gray-700 hover:text-blue-600 transition-colors group">
                <i class="fas fa-history text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                <span class="font-medium">Lihat Riwayat</span>
            </a>
            <button onclick="clearForm()" class="bg-white rounded-xl p-4 border border-gray-200 flex items-center justify-center space-x-3 card-hover text-gray-700 hover:text-red-600 transition-colors group">
                <i class="fas fa-trash-alt text-gray-400 group-hover:text-red-600 transition-colors"></i>
                <span class="font-medium">Hapus Pilihan</span>
            </button>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
            <div class="loading-spinner mx-auto mb-4"></div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Memproses Diagnosa</h3>
            <p class="text-gray-600 mb-4">Mohon tunggu sebentar, sistem sedang menganalisis gejala yang dipilih...</p>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full animate-pulse"></div>
            </div>
        </div>
    </div>

    <script>
        // Progress bar functionality
        function updateProgress() {
            const checkboxes = document.querySelectorAll('input[name="gejala[]"]');
            const checkedCount = document.querySelectorAll('input[name="gejala[]"]:checked').length;
            const totalCount = checkboxes.length;
            const percentage = totalCount > 0 ? (checkedCount / totalCount) * 100 : 0;
            
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const progressPercent = document.getElementById('progressPercent');
            const submitButton = document.getElementById('submitButton');
            
            progressBar.style.width = percentage + '%';
            progressPercent.textContent = Math.round(percentage) + '%';
            
            if (checkedCount === 0) {
                progressText.textContent = 'Pilih gejala untuk memulai';
                submitButton.disabled = true;
            } else if (checkedCount === 1) {
                progressText.textContent = `${checkedCount} gejala dipilih`;
                submitButton.disabled = false;
            } else {
                progressText.textContent = `${checkedCount} gejala dipilih`;
                submitButton.disabled = false;
            }
        }

        // Form submission handler
        document.getElementById('diagnosaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const checkboxes = document.querySelectorAll('input[name="gejala[]"]:checked');
            if (checkboxes.length === 0) {
                showNotification('Silakan pilih minimal satu gejala untuk melakukan diagnosa.', 'error');
                return false;
            }
            
            // Show loading overlay
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.remove('hidden');
            
            // Disable button to prevent multiple submissions
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            
            showNotification('Memproses diagnosa... Mohon tunggu sebentar.', 'info');
            
            // Submit form after a short delay to show loading state
            setTimeout(() => {
                this.submit();
            }, 1000);
            
            return true;
        });

        function clearForm() {
            const checkboxes = document.querySelectorAll('input[name="gejala[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            const radios = document.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => {
                radio.checked = false;
            });
            
            // Reset first radio to checked state for future selections
            document.querySelectorAll('input[type="radio"][value="1.0"]').forEach(radio => {
                radio.checked = true;
            });
            
            updateProgress();
            showNotification('Semua pilihan telah dihapus.', 'info');
        }

        function showNotification(message, type) {
            // Remove existing notification
            const existingNotification = document.getElementById('custom-notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Create new notification
            const notification = document.createElement('div');
            notification.id = 'custom-notification';
            
            let bgColor = 'bg-green-500';
            let icon = '✅';
            if (type === 'error') {
                bgColor = 'bg-red-500';
                icon = '❌';
            } else if (type === 'warning') {
                bgColor = 'bg-yellow-500';
                icon = '⚠️';
            } else if (type === 'info') {
                bgColor = 'bg-blue-500';
                icon = 'ℹ️';
            }
            
            notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg z-50 transform transition-transform duration-300 translate-x-full max-w-sm`;
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <span class="text-lg">${icon}</span>
                    <span class="flex-1">${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 10);
            
            // Animate out after 4 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 4000);
        }

        // Auto select "Sangat Yakin" when checkbox is checked
        document.querySelectorAll('input[name="gejala[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const gejalaId = this.value;
                const radios = document.querySelectorAll(`input[name="cf_user[${gejalaId}]"]`);
                if (this.checked && radios.length > 0 && !document.querySelector(`input[name="cf_user[${gejalaId}]"]:checked`)) {
                    radios[0].checked = true; // Select first option (Sangat Yakin)
                }
            });
        });
        
        // Fade in animation on scroll
        const fadeElements = document.querySelectorAll('.fade-in');
        
        const fadeInOnScroll = () => {
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 100;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        };
        
        window.addEventListener('scroll', fadeInOnScroll);
        // Initial check for elements in view
        fadeInOnScroll();
        
        // Initialize progress bar on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateProgress();
        });

        // Handle page refresh/back button to reset loading state
        window.addEventListener('pageshow', function(event) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            const submitButton = document.getElementById('submitButton');
            
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-stethoscope"></i><span>Proses Diagnosa</span><i class="fas fa-arrow-right"></i>';
            }
        });
    </script>
</body>
</html>