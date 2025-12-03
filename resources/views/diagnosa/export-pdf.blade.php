<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Diagnosa - SapiSehat</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #10b981;
        }
        
        .header h1 {
            color: #10b981;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #f8fafc;
            padding: 8px 12px;
            border-left: 4px solid #10b981;
            margin-bottom: 12px;
            font-weight: bold;
            color: #374151;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .table th {
            background-color: #f1f5f9;
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
        }
        
        .table td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
        }
        
        .table tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .diagnosis-summary {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .confidence-high { color: #059669; }
        .confidence-medium { color: #d97706; }
        .confidence-low { color: #dc2626; }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-4 { margin-bottom: 16px; }
        .mt-4 { margin-top: 16px; }
    </style>
</head>
<body>
    @php
        // Process data for PDF
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
        $confidenceClass = 'confidence-low';
        if ($cfTertinggi >= 0.8) {
            $confidenceLevel = 'tinggi';
            $confidenceClass = 'confidence-high';
        } elseif ($cfTertinggi >= 0.5) {
            $confidenceLevel = 'sedang';
            $confidenceClass = 'confidence-medium';
        }
        
        $hasValidDiagnosis = $penyakitTertinggi && $cfTertinggi > 0 && 
                            ($penyakitTertinggi['nama'] ?? '') !== 'Tidak diketahui';
    @endphp

    <!-- Header -->
    <div class="header">
        <table width="100%">
            <tr>
                <td width="33%">
                    <span style="font-size: 32px;">🐄</span>
                </td>
                <td width="34%" class="text-center">
                    <h1>SapiSehat</h1>
                    <p>Sistem Pakar Diagnosa Penyakit Sapi</p>
                </td>
                <td width="33%" class="text-right">
                    <p>ID Diagnosa: #{{ $diagnosa->id }}</p>
                    <p>Tanggal: {{ $diagnosa->created_at->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Diagnosis Summary -->
    <div class="section">
        <div class="section-title">RINGKASAN DIAGNOSA</div>
        
        @if($hasValidDiagnosis)
        <div class="diagnosis-summary">
            <table width="100%">
                <tr>
                    <td width="70%">
                        <h2 style="margin: 0; color: #059669;">DIAGNOSIS UTAMA</h2>
                        <p style="font-size: 18px; font-weight: bold; margin: 5px 0; color: #059669;">
                            {{ $penyakitTertinggi['nama'] ?? 'N/A' }}
                        </p>
                        <p style="margin: 0; color: #047857;">Kode: {{ $penyakitTertinggi['kode'] ?? 'N/A' }}</p>
                    </td>
                    <td width="30%" class="text-right">
                        <p style="font-size: 28px; font-weight: bold; margin: 0; color: #059669;">
                            {{ number_format($cfTertinggi * 100, 1) }}%
                        </p>
                        <p style="margin: 0; color: #047857;">Tingkat Keyakinan {{ $confidenceLevel }}</p>
                    </td>
                </tr>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 20px; background-color: #fef3c7; border: 1px solid #f59e0b;">
            <h3 style="margin: 0; color: #92400e;">Tidak Dapat Menentukan Diagnosis</h3>
            <p style="margin: 5px 0; color: #92400e;">Sistem tidak menemukan penyakit yang sesuai dengan gejala yang dipilih.</p>
        </div>
        @endif
    </div>

    <!-- Informasi Diagnosa -->
    <div class="section">
        <div class="section-title">INFORMASI DIAGNOSA</div>
        <table class="table">
            <tr>
                <td width="30%"><strong>ID Diagnosa</strong></td>
                <td width="70%">#{{ $diagnosa->id }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Diagnosa</strong></td>
                <td>{{ $diagnosa->created_at->format('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Nama Pengguna</strong></td>
                <td>{{ $diagnosa->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Total Gejala Dipilih</strong></td>
                <td>{{ count($gejalas) }} gejala</td>
            </tr>
            <tr>
                <td><strong>Status Diagnosa</strong></td>
                <td>{{ $diagnosa->status ?? 'Selesai' }}</td>
            </tr>
        </table>
    </div>

    @if($hasValidDiagnosis)
    <!-- Informasi Penyakit -->
    <div class="section">
        <div class="section-title">INFORMASI PENYAKIT</div>
        <table class="table">
            <tr>
                <td width="30%"><strong>Nama Penyakit</strong></td>
                <td width="70%">{{ $penyakitTertinggi['nama'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Kode Penyakit</strong></td>
                <td>{{ $penyakitTertinggi['kode'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Deskripsi</strong></td>
                <td>{{ $penyakitTertinggi['deskripsi'] ?? 'Deskripsi tidak tersedia' }}</td>
            </tr>
            <tr>
                <td><strong>Rekomendasi Penanganan</strong></td>
                <td>{{ $penyakitTertinggi['solusi'] ?? 'Rekomendasi penanganan tidak tersedia' }}</td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Gejala yang Dipilih -->
    <div class="section">
        <div class="section-title">GEJALA YANG DIPILIH ({{ count($gejalas) }} GEJALA)</div>
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode Gejala</th>
                    <th width="35%">Nama Gejala</th>
                    <th width="45%">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gejalas as $index => $gejala)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $gejala->kode }}</strong></td>
                    <td>{{ $gejala->nama }}</td>
                    <td>{{ $gejala->deskripsi ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($hasValidDiagnosis && !empty($rankingPenyakit))
    <!-- Analisis Certainty Factor -->
    <div class="section page-break">
        <div class="section-title">ANALISIS CERTAINTY FACTOR</div>
        <table class="table">
            <thead>
                <tr>
                    <th width="5%">Rank</th>
                    <th width="35%">Penyakit</th>
                    <th width="10%">Kode</th>
                    <th width="15%">CF Akhir</th>
                    <th width="15%">Persentase</th>
                    <th width="10%">Gejala</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankingPenyakit as $index => $result)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $result['nama'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $result['kode'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($result['cf_akhir'] ?? 0, 3) }}</td>
                    <td class="text-center {{ $confidenceClass }}">{{ number_format($result['persentase'] ?? 0, 1) }}%</td>
                    <td class="text-center">{{ count($result['gejala_terdeteksi'] ?? []) }}</td>
                    <td class="text-center">
                        @if($index === 0)
                        <strong>UTAMA</strong>
                        @else
                        Alternatif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Catatan Penting -->
    <div class="section">
        <div class="section-title">CATATAN PENTING</div>
        <div style="background-color: #fffbeb; border: 1px solid #f59e0b; padding: 15px; border-radius: 4px;">
            <p style="margin: 0 0 8px 0;"><strong>Perhatian:</strong></p>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Hasil diagnosa ini merupakan perkiraan berdasarkan sistem pakar menggunakan metode Certainty Factor</li>
                <li>Tingkat akurasi bergantung pada kelengkapan dan keakuratan gejala yang dipilih</li>
                <li>Disarankan untuk berkonsultasi dengan dokter hewan untuk diagnosis dan penanganan yang lebih akurat</li>
                <li>Laporan ini bersifat informatif dan tidak menggantikan pemeriksaan medis oleh profesional</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Pakar SapiSehat</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>