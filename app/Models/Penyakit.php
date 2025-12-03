<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Penyakit extends Model
{
    use HasFactory;

    // Hanya gunakan kolom yang ada di database
    protected $fillable = [
        'kode',
        'nama', 
        'deskripsi',
        'solusi',
        'gambar'
        // Hapus kolom yang tidak ada: kategori, tingkat_keparahan, dll
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'gambar_url',
        'diagnosa_count',
        'gejala_count',
        'is_used_in_diagnosa'
    ];

    /**
     * Get the aturans for the penyakit.
     */
    public function aturans(): HasMany
    {
        return $this->hasMany(Aturan::class, 'penyakit_id');
    }

    /**
     * Get the gejala through aturan.
     */
    public function gejalas(): BelongsToMany
    {
        return $this->belongsToMany(Gejala::class, 'aturans', 'penyakit_id', 'gejala_id')
                    ->withPivot('cf_pakar')
                    ->withTimestamps();
    }

    /**
     * Get the diagnosas for the penyakit.
     */
    public function diagnosas(): HasMany
    {
        return $this->hasMany(Diagnosa::class, 'penyakit_tertinggi', 'nama');
    }

    /**
     * Scope a query to only include active penyakit.
     * Karena tidak ada kolom status, kita anggap semua aktif
     */
    public function scopeActive($query)
    {
        return $query; // Semua penyakit dianggap aktif
    }

    /**
     * Scope a query to only include penyakit with aturan.
     */
    public function scopeWithAturan($query)
    {
        return $query->has('aturans');
    }

    /**
     * Get diagnosa count.
     */
    public function getDiagnosaCountAttribute(): int
    {
        return $this->diagnosas()->count();
    }

    /**
     * Get gejala count.
     */
    public function getGejalaCountAttribute(): int
    {
        return $this->aturans()->count();
    }

    /**
     * Check if penyakit is used in any diagnosa.
     */
    public function getIsUsedInDiagnosaAttribute(): bool
    {
        return $this->diagnosas()->exists();
    }

    /**
     * Get image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->gambar) {
            return asset('storage/images/penyakit/' . $this->gambar);
        }
        return null;
    }

    /**
     * Get gambar URL untuk view (fallback)
     */
    public function getGambarUrlAttribute(): ?string
    {
        return $this->getImageUrlAttribute();
    }

    /**
     * Dummy method untuk kompatibilitas - selalu return default
     */
    public function getKategoriAttribute(): string
    {
        return 'Umum'; // Default value
    }

    /**
     * Dummy method untuk kompatibilitas - selalu return default
     */
    public function getTingkatKeparahanAttribute(): string
    {
        return 'sedang'; // Default value
    }

    /**
     * Dummy method untuk kompatibilitas
     */
    public function getSeverityColorAttribute(): string
    {
        return 'secondary'; // Default color
    }

    /**
     * Check if penyakit can be deleted
     */
    public function getCanDeleteAttribute(): bool
    {
        return !$this->is_used_in_diagnosa && $this->gejala_count === 0;
    }
}