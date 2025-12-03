@if(count($rankingPenyakit) > 1)
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 card-hover fade-in break-before">
    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
        <i class="fas fa-list-ol text-blue-500 mr-2"></i>
        Kemungkinan Penyakit Lainnya
    </h3>
    
    <div class="space-y-4">
        @foreach($rankingPenyakit as $index => $result)
            @if($index > 0 && ($result['persentase'] ?? $result['cf_akhir'] * 100) > 0)
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition">
                <div class="flex items-center space-x-4 flex-1">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">{{ $result['nama'] ?? 'N/A' }}</h4>
                        <p class="text-sm text-gray-600">{{ $result['kode'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="text-right min-w-24">
                    @php
                        $persentase = $result['persentase'] ?? ($result['cf_akhir'] ?? 0) * 100;
                        $textColor = $persentase > 50 ? 'text-green-600' : 
                                    ($persentase > 30 ? 'text-yellow-600' : 'text-orange-600');
                        $bgColor = $persentase > 50 ? 'bg-green-600' : 
                                  ($persentase > 30 ? 'bg-yellow-600' : 'bg-orange-600');
                    @endphp
                    
                    <div class="text-xl font-bold {{ $textColor }} mb-1">
                        {{ number_format($persentase, 1) }}%
                    </div>
                    <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $bgColor }}" 
                             style="width: {{ min($persentase, 100) }}%">
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endif