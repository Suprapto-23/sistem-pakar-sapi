<nav class="bg-white shadow-sm sticky top-0 z-50 no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                    <div class="relative">
                        <span class="text-2xl">🐄</span>
                    </div>
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