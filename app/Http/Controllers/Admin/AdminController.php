<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosa;
use App\Models\User;
use App\Models\Penyakit;
use App\Models\Gejala;
use App\Models\Aturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Tampilkan dashboard admin dengan statistik sistem
     */
    public function dashboard()
    {
        // Statistik untuk dashboard admin
        $stats = $this->getDashboardStats();
        
        // Data untuk charts
        $chartData = $this->getChartData();
        
        // Diagnosa terbaru
        $diagnosaTerbaru = Diagnosa::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Penyakit paling sering didiagnosa
        $penyakitTerbanyak = $this->getPenyakitTerbanyak();

        return view('admin.dashboard', compact(
            'stats', 
            'chartData', 
            'diagnosaTerbaru', 
            'penyakitTerbanyak'
        ));
    }

    /**
     * Tampilkan statistik lengkap
     */
    public function statistik()
    {
        // Get basic stats
        $stats = $this->getDashboardStats();
        
        // Get chart data
        $chartData = $this->getChartData();
        
        // Get penyakit terbanyak
        $penyakitTerbanyak = $this->getPenyakitTerbanyak();
        
        // Get users aktif
        $usersAktif = $this->getUsersAktif();

        // Get detailed statistics for the table
        $statistikDetail = $this->getStatistikDetail();

        return view('admin.statistik', compact(
            'stats', 
            'chartData', 
            'penyakitTerbanyak', 
            'usersAktif',
            'statistikDetail'
        ));
    }

    /**
     * Get detailed statistics for the table
     */
    private function getStatistikDetail()
    {
        try {
            // Hitung total diagnosa untuk persentase
            $totalDiagnosa = Diagnosa::whereNotNull('penyakit_tertinggi')->count();
            
            // Jika tidak ada data, return array kosong
            if ($totalDiagnosa === 0) {
                return collect([]);
            }

            // Hitung statistik berdasarkan data diagnosa yang sebenarnya
            $statistik = Diagnosa::whereNotNull('penyakit_tertinggi')
                ->select(
                    'penyakit_tertinggi as nama_penyakit',
                    DB::raw('COUNT(*) as jumlah'),
                    DB::raw('ROUND(AVG(cf_tertinggi * 100), 1) as rata_rata_cf'),
                    DB::raw('ROUND((COUNT(*) * 100.0 / ?), 1) as persentase')
                )
                ->addBinding($totalDiagnosa, 'select')
                ->groupBy('penyakit_tertinggi')
                ->orderBy('jumlah', 'desc')
                ->get();

            // Hitung trend berdasarkan data 3 bulan terakhir vs 3 bulan sebelumnya
            $trendData = [];
            foreach ($statistik as $stat) {
                $currentPeriodCount = Diagnosa::where('penyakit_tertinggi', $stat->nama_penyakit)
                    ->where('created_at', '>=', now()->subMonths(3))
                    ->count();

                $previousPeriodCount = Diagnosa::where('penyakit_tertinggi', $stat->nama_penyakit)
                    ->whereBetween('created_at', [now()->subMonths(6), now()->subMonths(3)])
                    ->count();

                if ($previousPeriodCount > 0) {
                    $trend = (($currentPeriodCount - $previousPeriodCount) / $previousPeriodCount) * 100;
                } else {
                    $trend = $currentPeriodCount > 0 ? 100 : 0;
                }

                $trendData[$stat->nama_penyakit] = round($trend, 1);
            }

            // Format data final
            return $statistik->map(function ($item) use ($trendData) {
                return [
                    'nama_penyakit' => $item->nama_penyakit ?? 'Tidak Diketahui',
                    'jumlah' => $item->jumlah ?? 0,
                    'rata_rata_cf' => $item->rata_rata_cf ?? 0,
                    'persentase' => $item->persentase ?? 0,
                    'trend' => $trendData[$item->nama_penyakit] ?? 0
                ];
            });

        } catch (\Exception $e) {
            // Log error dan return array kosong
            \Log::error('Error in getStatistikDetail: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Kelola pengguna
     */
    public function kelolaPengguna()
    {
        $users = User::where('role', 'user')
            ->withCount('diagnosas')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pengguna.index', compact('users'));
    }

    /**
     * Update role pengguna
     */
    public function updateStatusPengguna(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        $user = User::findOrFail($id);
        
        // Jangan izinkan mengubah role admin utama
        if ($user->id === 1 && $request->role !== 'admin') {
            return redirect()->back()->with('error', 'Tidak dapat mengubah role admin utama.');
        }

        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', 'Role pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna
     */
    public function hapusPengguna($id)
    {
        // Cek apakah user memiliki data diagnosa
        $diagnosaCount = Diagnosa::where('user_id', $id)->count();
        
        if ($diagnosaCount > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus pengguna karena memiliki data diagnosa.');
        }

        $user = User::findOrFail($id);
        
        // Jangan izinkan menghapus admin utama atau diri sendiri
        if ($user->id === 1) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus admin utama.');
        }

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Kelola aturan (rules) sistem
     */
    public function kelolaAturan()
    {
        $aturans = Aturan::with(['penyakit', 'gejala'])
            ->orderBy('penyakit_id')
            ->orderBy('gejala_id')
            ->get();

        $penyakits = Penyakit::all();
        $gejalas = Gejala::all();

        return view('admin.aturan.index', compact('aturans', 'penyakits', 'gejalas'));
    }

    /**
     * Simpan aturan baru
     */
    public function simpanAturan(Request $request)
    {
        $request->validate([
            'penyakit_id' => 'required|exists:penyakits,id',
            'gejala_id' => 'required|exists:gejalas,id',
            'cf_pakar' => 'required|numeric|min:0|max:1'
        ]);

        // Cek apakah aturan sudah ada
        $existingRule = Aturan::where('penyakit_id', $request->penyakit_id)
            ->where('gejala_id', $request->gejala_id)
            ->first();

        if ($existingRule) {
            return redirect()->back()->with('error', 'Aturan untuk penyakit dan gejala ini sudah ada.');
        }

        Aturan::create($request->all());

        return redirect()->back()->with('success', 'Aturan berhasil ditambahkan.');
    }

    /**
     * Update aturan
     */
    public function updateAturan(Request $request, $id)
    {
        $request->validate([
            'cf_pakar' => 'required|numeric|min:0|max:1'
        ]);

        $aturan = Aturan::findOrFail($id);
        $aturan->cf_pakar = $request->cf_pakar;
        $aturan->save();

        return redirect()->back()->with('success', 'Aturan berhasil diperbarui.');
    }

    /**
     * Hapus aturan
     */
    public function hapusAturan($id)
    {
        $aturan = Aturan::findOrFail($id);
        $aturan->delete();

        return redirect()->back()->with('success', 'Aturan berhasil dihapus.');
    }

    /**
     * Backup database
     */
    public function backupDatabase()
    {
        // Informasi backup
        $backupInfo = [
            'total_users' => User::count(),
            'total_diagnosa' => Diagnosa::count(),
            'total_penyakit' => Penyakit::count(),
            'total_gejala' => Gejala::count(),
            'total_aturan' => Aturan::count(),
            'backup_time' => now()->format('Y-m-d H:i:s'),
            'database_size' => $this->getDatabaseSize()
        ];

        return view('admin.backup', compact('backupInfo'));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        // Hitung total gejala yang digunakan dalam diagnosa
        $totalGejalaUsed = Diagnosa::getTotalGejalaUsageCount();
        $uniqueGejalaCount = Diagnosa::getUniqueGejalaCount();

        return [
            'total_users' => User::where('role', 'user')->count(),
            'total_diagnosa' => Diagnosa::count(),
            'total_penyakit' => Penyakit::count(),
            'total_gejala' => Gejala::count(),
            'total_gejala_used' => $totalGejalaUsed,
            'unique_gejala_used' => $uniqueGejalaCount,
            'total_aturan' => Aturan::count(),
            'diagnosa_bulan_ini' => Diagnosa::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'diagnosa_minggu_ini' => Diagnosa::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'diagnosa_hari_ini' => Diagnosa::whereDate('created_at', today())->count(),
            'users_baru_bulan_ini' => User::where('role', 'user')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count()
        ];
    }

    /**
     * Get data untuk charts
     */
    private function getChartData()
    {
        // Data diagnosa per bulan (6 bulan terakhir)
        $diagnosaPerBulan = Diagnosa::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        $diagnosaChart = [];
        $labels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $labels[] = $monthYear;
            
            $data = $diagnosaPerBulan->first(function ($item) use ($date) {
                return $item->month == $date->month && $item->year == $date->year;
            });
            
            $diagnosaChart[] = $data ? $data->total : 0;
        }

        // Data distribusi penyakit
        $distribusiPenyakit = Diagnosa::select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->whereNotNull('penyakit_tertinggi')
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $pieLabels = $distribusiPenyakit->pluck('penyakit_tertinggi')->toArray();
        $pieData = $distribusiPenyakit->pluck('total')->toArray();

        return [
            'diagnosa' => [
                'labels' => $labels,
                'data' => $diagnosaChart
            ],
            'distribusi_penyakit' => [
                'labels' => $pieLabels,
                'data' => $pieData
            ]
        ];
    }

    /**
     * Get penyakit paling sering didiagnosa
     */
    private function getPenyakitTerbanyak()
    {
        return Diagnosa::whereNotNull('penyakit_tertinggi')
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'penyakit_tertinggi' => $item->penyakit_tertinggi,
                    'total' => $item->total
                ];
            });
    }

    /**
     * Get users paling aktif
     */
    private function getUsersAktif()
    {
        return User::where('role', 'user')
            ->withCount(['diagnosas' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->having('diagnosas_count', '>', 0)
            ->orderBy('diagnosas_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get database size (estimasi)
     */
    private function getDatabaseSize()
    {
        try {
            $size = DB::select(DB::raw("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb 
                FROM information_schema.tables 
                WHERE table_schema = '" . env('DB_DATABASE') . "'
            "));
            
            return $size[0]->size_mb ?? '0';
        } catch (\Exception $e) {
            return '0';
        }
    }

    /**
     * System information
     */
    public function systemInfo()
    {
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_driver' => config('database.default'),
            'timezone' => config('app.timezone'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'maintenance_mode' => app()->isDownForMaintenance() ? 'Enabled' : 'Disabled'
        ];

        return view('admin.system-info', compact('systemInfo'));
    }

    /**
     * Reset password pengguna
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::findOrFail($id);
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil direset.');
    }
    /**
 * Tampilkan form tambah aturan
 */
public function createAturan()
{
    $penyakits = Penyakit::all();
    $gejalas = Gejala::all();

    return view('admin.aturan.create', compact('penyakits', 'gejalas'));
}

/**
 * Tampilkan form edit aturan
 */
public function editAturan($id)
{
    $aturan = Aturan::with(['penyakit', 'gejala'])->findOrFail($id);
    
    return view('admin.aturan.edit', compact('aturan'));
}
}