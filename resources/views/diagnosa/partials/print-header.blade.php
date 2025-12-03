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