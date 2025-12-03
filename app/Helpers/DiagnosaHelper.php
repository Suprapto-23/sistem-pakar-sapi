<?php

namespace App\Helpers;

use App\Models\Gejala;

class DiagnosaHelper
{
    public static function processDiagnosaData($diagnosa)
    {
        // Process data dengan handling yang lebih baik
        $hasilPerhitungan = $diagnosa->hasil_perhitungan ?? [];
        
        // Extract data dengan struktur yang konsisten
        $rankingPenyakit = $hasilPerhitungan['hasil_perhitungan'] ?? [];
        $penyakitTertinggi = $hasilPerhitungan['penyakit_tertinggi'] ?? null;
        $cfTertinggi = $hasilPerhitungan['cf_tertinggi'] ?? 0;
        
        // Jika struktur berbeda, coba extract dari data yang ada
        if (empty($rankingPenyakit) && !empty($hasilPerhitungan)) {
            // Cek jika hasil_perhitungan adalah array langsung
            if (isset($hasilPerhitungan[0]) && is_array($hasilPerhitungan[0])) {
                $rankingPenyakit = $hasilPerhitungan;
                // Cari penyakit dengan CF tertinggi
                $cfTertinggi = 0;
                foreach ($rankingPenyakit as $result) {
                    $cfValue = $result['cf_akhir'] ?? ($result['persentase'] / 100 ?? 0);
                    if ($cfValue > $cfTertinggi) {
                        $cfTertinggi = $cfValue;
                        $penyakitTertinggi = $result;
                    }
                }
            }
        }
        
        // Get gejala data
        $gejalaTerpilih = json_decode($diagnosa->gejala_terpilih, true) ?? [];
        $gejalas = Gejala::whereIn('id', $gejalaTerpilih)->get();
        
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
        
        // Check valid diagnosis
        $hasValidDiagnosis = $penyakitTertinggi && $cfTertinggi > 0 && 
                            ($penyakitTertinggi['nama'] ?? '') !== 'Tidak diketahui' &&
                            !empty($penyakitTertinggi['nama']);

        return compact(
            'rankingPenyakit',
            'penyakitTertinggi',
            'cfTertinggi',
            'gejalas',
            'confidenceLevel',
            'confidenceColor',
            'hasValidDiagnosis'
        );
    }

    public static function getConfidenceColor($cfValue)
    {
        if ($cfValue >= 0.8) return 'confidence-high';
        if ($cfValue >= 0.5) return 'confidence-medium';
        return 'confidence-low';
    }

    public static function getConfidenceLevel($cfValue)
    {
        if ($cfValue >= 0.8) return 'tinggi';
        if ($cfValue >= 0.5) return 'sedang';
        return 'rendah';
    }

    public static function getConfidenceDescription($level)
    {
        $descriptions = [
            'tinggi' => 'Sangat Yakin',
            'sedang' => 'Cukup Yakin',
            'rendah' => 'Kurang Yakin'
        ];
        
        return $descriptions[$level] ?? 'Tidak Diketahui';
    }
}