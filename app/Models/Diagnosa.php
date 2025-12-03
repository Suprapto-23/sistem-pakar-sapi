<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Diagnosa extends Model
{
    use HasFactory;

    protected $table = 'diagnosas';
    
    protected $fillable = [
        'user_id', 
        'gejala_terpilih', 
        'hasil_perhitungan',
        'hasil_diagnosa',
        'penyakit_tertinggi', 
        'cf_tertinggi', 
        'pdf_path',
        'gejala_ids',
        'penyakit_ids',
        'status'
    ];

    protected $casts = [
        'gejala_terpilih' => 'array',
        'hasil_perhitungan' => 'array',
        'hasil_diagnosa' => 'array',
        'gejala_ids' => 'array',
        'penyakit_ids' => 'array',
        'cf_tertinggi' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'cf_tertinggi' => 0,
        'status' => 'completed'
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Penyakit based on penyakit_tertinggi
     */
    public function penyakit()
    {
        return $this->belongsTo(Penyakit::class, 'penyakit_tertinggi', 'nama');
    }

    /**
     * Relationship with multiple Penyakit through penyakit_ids
     */
    public function penyakitMany()
    {
        return $this->belongsToMany(Penyakit::class, 'diagnosa_penyakit', 'diagnosa_id', 'penyakit_id')
                    ->withPivot('cf_value')
                    ->withTimestamps();
    }

    /**
     * Relationship with Gejala through gejala_ids
     */
    public function gejala()
    {
        return $this->belongsToMany(Gejala::class, 'diagnosa_gejala', 'diagnosa_id', 'gejala_id')
                    ->withTimestamps();
    }

    /**
     * Scope for user's diagnoses
     */
    public function scopeUserDiagnoses($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent diagnoses
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for completed diagnoses
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for diagnoses with high confidence (above threshold)
     */
    public function scopeHighConfidence($query, $threshold = 0.7)
    {
        return $query->where('cf_tertinggi', '>=', $threshold);
    }

    /**
     * Scope for diagnoses by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Format confidence percentage attribute
     */
    protected function confidencePercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format(($this->cf_tertinggi ?? 0) * 100, 1) . '%'
        );
    }

    /**
     * Format tanggal attribute
     */
    protected function tanggalFormat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->format('d M Y H:i')
        );
    }

    /**
     * Format tanggal singkat attribute
     */
    protected function tanggalSingkat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->format('d M Y')
        );
    }

    /**
     * Get penyakit names from hasil_perhitungan
     */
    protected function penyakitNames(): Attribute
    {
        return Attribute::make(
            get: function () {
                $detectedPenyakit = $this->detected_penyakit;
                if (!empty($detectedPenyakit)) {
                    return collect($detectedPenyakit)->pluck('nama')->toArray();
                }
                return [$this->penyakit_tertinggi];
            }
        );
    }

    /**
     * Get top 3 penyakit from hasil_perhitungan
     */
    protected function topPenyakit(): Attribute
    {
        return Attribute::make(
            get: function () {
                $detectedPenyakit = $this->detected_penyakit;
                if (!empty($detectedPenyakit)) {
                    return collect($detectedPenyakit)
                        ->sortByDesc('cf_akhir')
                        ->take(3)
                        ->values()
                        ->toArray();
                }
                return [];
            }
        );
    }

    /**
     * Get gejala count
     */
    protected function gejalaCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!empty($this->gejala_terpilih) && is_array($this->gejala_terpilih)) {
                    return count($this->gejala_terpilih);
                }
                return 0;
            }
        );
    }

    /**
     * Check if diagnosa has PDF report
     */
    protected function hasPdfReport(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->pdf_path) && file_exists(storage_path('app/' . $this->pdf_path))
        );
    }

    /**
     * Get PDF report URL
     */
    protected function pdfUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!empty($this->pdf_path) && file_exists(storage_path('app/' . $this->pdf_path))) {
                    return route('diagnosa.download', $this->id);
                }
                return null;
            }
        );
    }

    /**
     * Get confidence level (High, Medium, Low)
     */
    protected function confidenceLevel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $cf = $this->cf_tertinggi ?? 0;
                if ($cf >= 0.8) {
                    return 'Tinggi';
                } elseif ($cf >= 0.5) {
                    return 'Sedang';
                } else {
                    return 'Rendah';
                }
            }
        );
    }

    /**
     * Get confidence level color
     */
    protected function confidenceColor(): Attribute
    {
        return Attribute::make(
            get: function () {
                $cf = $this->cf_tertinggi ?? 0;
                if ($cf >= 0.8) {
                    return 'success';
                } elseif ($cf >= 0.5) {
                    return 'warning';
                } else {
                    return 'danger';
                }
            }
        );
    }

    /**
     * Get confidence level for Tailwind CSS
     */
    protected function confidenceColorTailwind(): Attribute
    {
        return Attribute::make(
            get: function () {
                $cf = $this->cf_tertinggi ?? 0;
                if ($cf >= 0.8) {
                    return 'green';
                } elseif ($cf >= 0.5) {
                    return 'yellow';
                } else {
                    return 'red';
                }
            }
        );
    }

    /**
     * Get gejala names from gejala_terpilih
     */
    public function getGejalaNamesAttribute()
    {
        if (!empty($this->gejala_terpilih) && is_array($this->gejala_terpilih)) {
            $gejalaIds = $this->gejala_terpilih;
            $gejala = Gejala::whereIn('id', $gejalaIds)->get();
            return $gejala->pluck('nama')->toArray();
        }
        return [];
    }

    /**
     * Get detailed hasil diagnosa from hasil_perhitungan
     */
    public function getDetailedHasilAttribute()
    {
        $detectedPenyakit = $this->detected_penyakit;
        
        if (!empty($detectedPenyakit)) {
            return collect($detectedPenyakit)
                ->map(function ($item) {
                    return [
                        'penyakit' => $item['nama'] ?? '',
                        'kode' => $item['kode'] ?? '',
                        'persentase' => $item['persentase'] ?? 0,
                        'cf' => $item['cf_akhir'] ?? 0,
                        'deskripsi' => $item['deskripsi'] ?? '',
                        'solusi' => $item['solusi'] ?? '',
                        'gejala_terdeteksi' => $item['gejala_terdeteksi'] ?? []
                    ];
                })
                ->sortByDesc('cf')
                ->values()
                ->toArray();
        }
        return [];
    }

    /**
     * Get primary penyakit result
     */
    public function getPrimaryPenyakitAttribute()
    {
        $detectedPenyakit = $this->detected_penyakit;
        return !empty($detectedPenyakit) ? $detectedPenyakit[0] : null;
    }

    /**
     * Get all detected penyakit with CF values from hasil_perhitungan
     */
    public function getDetectedPenyakitAttribute()
    {
        if (!empty($this->hasil_perhitungan) && is_array($this->hasil_perhitungan)) {
            $hasilPerhitungan = $this->hasil_perhitungan;
            
            // Check the structure of hasil_perhitungan
            if (isset($hasilPerhitungan['hasil_perhitungan']) && is_array($hasilPerhitungan['hasil_perhitungan'])) {
                return collect($hasilPerhitungan['hasil_perhitungan'])
                    ->map(function ($item) {
                        return [
                            'id' => $item['penyakit_id'] ?? null,
                            'nama' => $item['nama'] ?? '',
                            'kode' => $item['kode'] ?? '',
                            'cf_akhir' => $item['cf_akhir'] ?? 0,
                            'persentase' => $item['persentase'] ?? 0,
                            'deskripsi' => $item['deskripsi'] ?? '',
                            'solusi' => $item['solusi'] ?? '',
                            'gejala_terdeteksi' => $item['gejala_terdeteksi'] ?? []
                        ];
                    })
                    ->sortByDesc('cf_akhir')
                    ->values()
                    ->toArray();
            }
            
            // Alternative structure: direct array of penyakit
            if (is_array($hasilPerhitungan) && isset($hasilPerhitungan[0]['nama'])) {
                return collect($hasilPerhitungan)
                    ->map(function ($item) {
                        return [
                            'id' => $item['penyakit_id'] ?? null,
                            'nama' => $item['nama'] ?? '',
                            'kode' => $item['kode'] ?? '',
                            'cf_akhir' => $item['cf_akhir'] ?? 0,
                            'persentase' => $item['persentase'] ?? 0,
                            'deskripsi' => $item['deskripsi'] ?? '',
                            'solusi' => $item['solusi'] ?? '',
                            'gejala_terdeteksi' => $item['gejala_terdeteksi'] ?? []
                        ];
                    })
                    ->sortByDesc('cf_akhir')
                    ->values()
                    ->toArray();
            }
        }
        
        // Fallback: return empty array
        return [];
    }

    /**
     * Get detected gejala with details
     */
    public function getDetectedGejalaAttribute()
    {
        if (!empty($this->gejala_terpilih) && is_array($this->gejala_terpilih)) {
            $gejalaIds = $this->gejala_terpilih;
            return Gejala::whereIn('id', $gejalaIds)->get()->toArray();
        }
        return [];
    }

    /**
     * Get user CF values from hasil_perhitungan
     */
    public function getUserCfValuesAttribute()
    {
        $cfUser = [];
        $detectedPenyakit = $this->detected_penyakit;
        
        if (!empty($detectedPenyakit)) {
            foreach ($detectedPenyakit as $penyakit) {
                if (isset($penyakit['gejala_terdeteksi']) && is_array($penyakit['gejala_terdeteksi'])) {
                    foreach ($penyakit['gejala_terdeteksi'] as $gejala) {
                        if (isset($gejala['gejala_id']) && isset($gejala['cf_user'])) {
                            $cfUser[$gejala['gejala_id']] = $gejala['cf_user'];
                        }
                    }
                }
            }
        }
        
        return $cfUser;
    }

    /**
     * Check if diagnosa has valid results
     */
    public function getHasValidDiagnosisAttribute()
    {
        $primaryPenyakit = $this->primary_penyakit;
        return $primaryPenyakit && 
               ($this->cf_tertinggi ?? 0) > 0 && 
               $primaryPenyakit['nama'] !== 'Tidak diketahui';
    }

    /**
     * Get ranking penyakit for display
     */
    public function getRankingPenyakitAttribute()
    {
        return $this->detected_penyakit;
    }

    /**
     * Get penyakit tertinggi from hasil_perhitungan
     */
    public function getPenyakitTertinggiDataAttribute()
    {
        if (!empty($this->hasil_perhitungan) && is_array($this->hasil_perhitungan)) {
            $hasilPerhitungan = $this->hasil_perhitungan;
            
            if (isset($hasilPerhitungan['penyakit_tertinggi']) && is_array($hasilPerhitungan['penyakit_tertinggi'])) {
                return $hasilPerhitungan['penyakit_tertinggi'];
            }
        }
        
        // Fallback to primary penyakit
        return $this->primary_penyakit;
    }

    /**
     * Create PDF report for this diagnosa
     */
    public function generatePdfReport()
    {
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('diagnosa.export-pdf', [
                'diagnosa' => $this,
                'hasilPerhitungan' => $this->hasil_perhitungan,
                'gejalas' => $this->detected_gejala
            ]);
            
            $filename = 'diagnosa-report-' . $this->id . '-' . time() . '.pdf';
            $path = 'public/reports/' . $filename;
            
            // Simpan file PDF
            \Storage::put($path, $pdf->output());
            
            $this->update(['pdf_path' => $path]);
            
            return $path;
            
        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete PDF report
     */
    public function deletePdfReport()
    {
        if (!empty($this->pdf_path) && \Storage::exists($this->pdf_path)) {
            \Storage::delete($this->pdf_path);
            $this->update(['pdf_path' => null]);
            return true;
        }
        return false;
    }

    /**
     * Duplicate diagnosa for new analysis
     */
    public function duplicate()
    {
        return $this->replicate()->fill([
            'created_at' => now(),
            'updated_at' => now(),
            'pdf_path' => null
        ]);
    }

    /**
     * Get readable status
     */
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match($this->status) {
                    'completed' => 'Selesai',
                    'pending' => 'Menunggu',
                    'failed' => 'Gagal',
                    default => $this->status
                };
            }
        );
    }

    /**
     * Check if diagnosa is reliable (CF > 0.5)
     */
    protected function isReliable(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->cf_tertinggi ?? 0) > 0.5
        );
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set default values before create
        static::creating(function ($model) {
            if (empty($model->status)) {
                $model->status = 'completed';
            }
            if (empty($model->cf_tertinggi)) {
                $model->cf_tertinggi = 0;
            }
        });

        // Clean up PDF when model is deleted
        static::deleting(function ($model) {
            $model->deletePdfReport();
        });
    }

    /**
     * Custom method to get formatted date for views
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->translatedFormat('d F Y');
    }

    /**
     * Custom method to get formatted time for views
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->translatedFormat('H:i');
    }

    /**
     * Custom method to get diagnosis result summary
     */
    public function getResultSummaryAttribute()
    {
        $primary = $this->primary_penyakit;
        
        if ($primary) {
            return "Diagnosa: {$primary['nama']} ({$primary['persentase']}%)";
        }
        
        return "Diagnosa: {$this->penyakit_tertinggi} (" . number_format(($this->cf_tertinggi ?? 0) * 100, 1) . "%)";
    }

    /**
     * Get gejala with user CF values for display
     */
    public function getGejalaWithCfAttribute()
    {
        $gejalas = $this->detected_gejala;
        $userCf = $this->user_cf_values;
        
        return collect($gejalas)->map(function ($gejala) use ($userCf) {
            $gejala['cf_user'] = $userCf[$gejala['id']] ?? 0.8;
            return $gejala;
        })->toArray();
    }
    /**
     * Get unique gejala IDs from all diagnoses (static method)
     */
    public static function getUniqueGejalaIds()
    {
        $diagnosas = self::completed()->get();
        $uniqueGejalaIds = [];

        foreach ($diagnosas as $diagnosa) {
            if (!empty($diagnosa->gejala_terpilih) && is_array($diagnosa->gejala_terpilih)) {
                $uniqueGejalaIds = array_merge($uniqueGejalaIds, $diagnosa->gejala_terpilih);
            }
        }

        return array_unique($uniqueGejalaIds);
    }

    /**
     * Get count of unique gejala used in diagnoses
     */
    public static function getUniqueGejalaCount()
    {
        $uniqueGejalaIds = self::getUniqueGejalaIds();
        return count($uniqueGejalaIds);
    }

    /**
     * Get total gejala usage count across all diagnoses
     */
    public static function getTotalGejalaUsageCount()
    {
        $diagnosas = self::completed()->get();
        $totalUsage = 0;

        foreach ($diagnosas as $diagnosa) {
            if (!empty($diagnosa->gejala_terpilih) && is_array($diagnosa->gejala_terpilih)) {
                $totalUsage += count($diagnosa->gejala_terpilih);
            }
        }

        return $totalUsage;
    }

    /**
     * Get gejala usage statistics
     */
    public static function getGejalaUsageStats()
    {
        $diagnosas = self::completed()->get();
        $gejalaUsage = [];

        foreach ($diagnosas as $diagnosa) {
            if (!empty($diagnosa->gejala_terpilih) && is_array($diagnosa->gejala_terpilih)) {
                foreach ($diagnosa->gejala_terpilih as $gejalaId) {
                    if (!isset($gejalaUsage[$gejalaId])) {
                        $gejalaUsage[$gejalaId] = 0;
                    }
                    $gejalaUsage[$gejalaId]++;
                }
            }
        }

        arsort($gejalaUsage);
        return $gejalaUsage;
    }

    /**
     * Get most used gejala
     */
    public static function getMostUsedGejala($limit = 5)
    {
        $usageStats = self::getGejalaUsageStats();
        $mostUsed = array_slice($usageStats, 0, $limit, true);
        
        $result = [];
        foreach ($mostUsed as $gejalaId => $count) {
            $gejala = Gejala::find($gejalaId);
            if ($gejala) {
                $result[] = [
                    'gejala' => $gejala,
                    'usage_count' => $count
                ];
            }
        }
        
        return $result;
    }

    /**
     * Get gejala usage frequency (method yang aman)
     */
    public static function getGejalaUsageFrequency()
    {
        $diagnosas = self::all();
        $usageFrequency = [];

        foreach ($diagnosas as $diagnosa) {
            $gejalaTerpilih = json_decode($diagnosa->gejala_terpilih, true);
            if (is_array($gejalaTerpilih)) {
                foreach ($gejalaTerpilih as $gejalaId) {
                    if (!isset($usageFrequency[$gejalaId])) {
                        $usageFrequency[$gejalaId] = 0;
                    }
                    $usageFrequency[$gejalaId]++;
                }
            }
        }

        // Get gejala details
        $result = [];
        foreach ($usageFrequency as $gejalaId => $count) {
            $gejala = Gejala::find($gejalaId);
            if ($gejala) {
                $result[] = [
                    'gejala' => $gejala,
                    'usage_count' => $count
                ];
            }
        }

        // Sort by usage count descending
        usort($result, function($a, $b) {
            return $b['usage_count'] - $a['usage_count'];
        });

        return $result;
    }
}