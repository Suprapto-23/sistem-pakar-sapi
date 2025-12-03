<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'role_name',
        'is_active',
        'initials',
        'diagnosa_count'
    ];

    /**
     * Default attribute values
     *
     * @var array
     */
    protected $attributes = [
        'role' => 'user',
        'status' => 'active'
    ];

    /**
     * RELATIONSHIPS
     */

    /**
     * Get all diagnosas for the user
     */
    public function diagnosas(): HasMany
    {
        return $this->hasMany(Diagnosa::class);
    }

    /**
     * Get the user's latest diagnosas
     */
    public function latestDiagnosas()
    {
        return $this->hasMany(Diagnosa::class)->latest()->limit(5);
    }

    /**
     * SCOPES
     */

    /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include regular users.
     */
    public function scopeUser($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to order by most active (most diagnosas).
     */
    public function scopeMostActive($query, $limit = 10)
    {
        return $query->withCount('diagnosas')
                    ->orderBy('diagnosas_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope a query to get users created in a specific period.
     */
    public function scopeCreatedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * ACCESSORS
     */

    /**
     * Get the user's role name
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'user' => 'Pengguna',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Check if user is active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get user's initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';

        if (count($names) >= 2) {
            $initials = strtoupper($names[0][0] . $names[1][0]);
        } else {
            $initials = strtoupper(substr($this->name, 0, 2));
        }

        return $initials;
    }

    /**
     * Get user's avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        // Generate default avatar with initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=2c7da0';
    }

    /**
     * Get total diagnosa count
     */
    public function getDiagnosaCountAttribute(): int
    {
        return $this->diagnosas()->count();
    }

    /**
     * Get last activity date
     */
    public function getLastActivityAttribute(): ?string
    {
        $latestDiagnosa = $this->diagnosas()->latest()->first();
        return $latestDiagnosa ? $latestDiagnosa->created_at->diffForHumans() : 'Belum ada aktivitas';
    }

    /**
     * Get registration date formatted
     */
    public function getRegisteredAtAttribute(): string
    {
        return $this->created_at->format('d M Y');
    }

    /**
     * Get registration date with time
     */
    public function getRegisteredFullAttribute(): string
    {
        return $this->created_at->format('d F Y H:i');
    }

    /**
     * METHODS
     */

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user can perform admin actions
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage content
     */
    public function canManageContent(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can view statistics
     */
    public function canViewStatistics(): bool
    {
        return $this->isAdmin() || $this->isUser();
    }

    /**
     * Activate user account
     */
    public function activate(): bool
    {
        $this->status = 'active';
        return $this->save();
    }

    /**
     * Deactivate user account
     */
    public function deactivate(): bool
    {
        $this->status = 'inactive';
        return $this->save();
    }

    /**
     * Get user's performance statistics
     */
    public function getPerformanceStats(): array
    {
        $totalDiagnosa = $this->diagnosas()->count();
        $diagnosaThisMonth = $this->diagnosas()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $diagnosaLastMonth = $this->diagnosas()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $monthlyGrowth = $diagnosaLastMonth > 0 
            ? (($diagnosaThisMonth - $diagnosaLastMonth) / $diagnosaLastMonth) * 100 
            : ($diagnosaThisMonth > 0 ? 100 : 0);

        return [
            'total_diagnosa' => $totalDiagnosa,
            'diagnosa_bulan_ini' => $diagnosaThisMonth,
            'diagnosa_bulan_lalu' => $diagnosaLastMonth,
            'pertumbuhan_bulanan' => round($monthlyGrowth, 2),
            'rata_rata_bulanan' => $this->getAverageMonthlyDiagnosa()
        ];
    }

    /**
     * Calculate average monthly diagnosa
     */
    private function getAverageMonthlyDiagnosa(): float
    {
        $firstDiagnosa = $this->diagnosas()->orderBy('created_at')->first();
        
        if (!$firstDiagnosa) {
            return 0;
        }

        $monthsSinceFirst = $firstDiagnosa->created_at->diffInMonths(now()) ?: 1;
        $totalDiagnosa = $this->diagnosas()->count();

        return round($totalDiagnosa / $monthsSinceFirst, 2);
    }

    /**
     * Get user's most common diagnosed diseases
     */
    public function getCommonDiseases($limit = 5): array
    {
        return $this->diagnosas()
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as count'))
            ->whereNotNull('penyakit_tertinggi')
            ->groupBy('penyakit_tertinggi')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->pluck('count', 'penyakit_tertinggi')
            ->toArray();
    }

    /**
     * Get user's activity timeline
     */
    public function getActivityTimeline($limit = 10)
    {
        return $this->diagnosas()
            ->select('id', 'penyakit_tertinggi', 'cf_tertinggi', 'created_at')
            ->with('gejalas')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if user has any diagnosa
     */
    public function hasDiagnosa(): bool
    {
        return $this->diagnosas()->exists();
    }

    /**
     * Get user's recent activity status
     */
    public function getActivityStatus(): string
    {
        $lastActivity = $this->diagnosas()->latest()->first();
        
        if (!$lastActivity) {
            return 'Tidak ada aktivitas';
        }

        $daysAgo = $lastActivity->created_at->diffInDays(now());

        if ($daysAgo === 0) {
            return 'Aktif hari ini';
        } elseif ($daysAgo === 1) {
            return 'Aktif kemarin';
        } elseif ($daysAgo <= 7) {
            return "Aktif {$daysAgo} hari lalu";
        } elseif ($daysAgo <= 30) {
            $weeks = floor($daysAgo / 7);
            return "Aktif {$weeks} minggu lalu";
        } else {
            $months = floor($daysAgo / 30);
            return "Aktif {$months} bulan lalu";
        }
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Set default role when creating user
        static::creating(function ($user) {
            if (empty($user->role)) {
                $user->role = 'user';
            }
            if (empty($user->status)) {
                $user->status = 'active';
            }
        });

        // Prevent deleting admin users
        static::deleting(function ($user) {
            if ($user->isAdmin()) {
                throw new \Exception('Tidak dapat menghapus akun administrator.');
            }
        });
    }
}