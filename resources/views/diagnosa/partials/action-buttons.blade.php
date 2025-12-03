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
    
    <button onclick="enhancedPrint()" 
            class="inline-flex items-center justify-center px-6 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold transition transform hover:scale-105">
        <i class="fas fa-print mr-2"></i>
        Print Laporan
    </button>
</div>