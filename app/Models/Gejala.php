<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gejala extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama', 
        'deskripsi',
        'gambar'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship with Aturan
     */
    public function aturans()
    {
        return $this->hasMany(Aturan::class);
    }

    /**
     * Relationship with Diagnosa through gejala_terpilih
     */
    public function diagnosas()
    {
        return $this->belongsToMany(Diagnosa::class, 'diagnosa_gejala', 'gejala_id', 'diagnosa_id')
                    ->withTimestamps();
    }

    /**
     * Get related penyakit through aturan
     */
    public function penyakit()
    {
        return $this->belongsToMany(Penyakit::class, 'aturans', 'gejala_id', 'penyakit_id')
                    ->withPivot('cf_pakar')
                    ->withTimestamps();
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
    }

    /**
     * Scope for active gejala
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get usage count in diagnosa
     */
    public function getUsageCountAttribute()
    {
        $diagnosas = Diagnosa::all();
        $count = 0;

        foreach ($diagnosas as $diagnosa) {
            $gejalaTerpilih = json_decode($diagnosa->gejala_terpilih, true);
            if (is_array($gejalaTerpilih) && in_array($this->id, $gejalaTerpilih)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Check if gejala is used in any aturan
     */
    public function getIsUsedInAturanAttribute()
    {
        return $this->aturans()->count() > 0;
    }

    /**
     * Check if gejala is used in any diagnosa
     */
    public function getIsUsedInDiagnosaAttribute()
    {
        return $this->usage_count > 0;
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->gambar && file_exists(public_path('images/gejala/' . $this->gambar))) {
            return asset('images/gejala/' . $this->gambar);
        }
        return null;
    }
}