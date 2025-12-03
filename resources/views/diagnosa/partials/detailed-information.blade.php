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
                Gejala yang Dipilih
            </h3>
            
            <div class="space-y-3">
                @forelse($gejalas as $index => $gejala)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-green-300 transition">
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $gejala->kode }} - {{ $gejala->nama }}</h4>
                            @if($gejala->deskripsi)
                            <p class="text-sm text-gray-600 mt-1">{{ $gejala->deskripsi }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Tidak ada data gejala yang tersedia
                </div>
                @endforelse
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