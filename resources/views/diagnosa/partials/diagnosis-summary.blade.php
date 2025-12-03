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
                            {{ number_format(($penyakitTertinggi['persentase'] ?? $cfTertinggi * 100), 1) }}%
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
                <div class="text-sm text-gray-600">
                    {{ \App\Helpers\DiagnosaHelper::getConfidenceDescription($confidenceLevel) }}
                </div>
            </div>
        </div>
    </div>
</div>