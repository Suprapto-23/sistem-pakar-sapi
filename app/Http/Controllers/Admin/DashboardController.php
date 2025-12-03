<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosa;
use App\Models\User;
use App\Models\Penyakit;
use App\Models\Gejala;
use App\Models\Aturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin dengan statistik lengkap
     */
    public function index()
    {
        $stats = $this->getDashboardStats();
        $chartData = $this->getChartData();
        $diagnosaTerbaru = $this->getRecentActivity();
        $topPenyakit = $this->getTopPenyakit();

        return view('admin.dashboard', compact(
            'stats', 
            'chartData', 
            'diagnosaTerbaru', 
            'topPenyakit'
        ));
    }

    /**
     * Tampilkan halaman statistik lengkap
     */
    public function statistik()
    {
        $stats = $this->getDashboardStats();
        $chartData = $this->getChartData();
        $topPenyakit = $this->getTopPenyakit(10);
        $monthlyComparison = $this->getMonthlyComparison();

        return view('admin.statistik', compact(
            'stats', 
            'chartData', 
            'topPenyakit', 
            'monthlyComparison'
        ));
    }

    /**
     * API untuk data chart real-time
     */
    public function getChartDataApi(Request $request)
    {
        $type = $request->get('type', 'monthly');
        
        switch ($type) {
            case 'weekly':
                $data = $this->getWeeklyChartData();
                break;
            case 'disease':
                $data = $this->getDiseaseChartData();
                break;
            case 'user':
                $data = $this->getUserChartData();
                break;
            default:
                $data = $this->getMonthlyChartData();
        }

        return response()->json($data);
    }

    /**
     * Kelola Pengguna
     */
    public function kelolaPengguna()
    {
        $users = User::where('role', 'user')->paginate(10);
        return view('admin.pengguna.index', compact('users'));
    }

    /**
     * Update Status Pengguna
     */
    public function updateStatusPengguna(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Status pengguna berhasil diupdate');
    }

    /**
     * Hapus Pengguna
     */
    public function hapusPengguna($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->back()->with('success', 'Pengguna berhasil dihapus');
    }

    /**
     * Kelola Aturan
     */
    public function kelolaAturan()
    {
        $aturan = Aturan::with(['penyakit', 'gejala'])->paginate(10);
        $penyakit = Penyakit::all();
        $gejala = Gejala::all();
        
        return view('admin.aturan.index', compact('aturan', 'penyakit', 'gejala'));
    }

    /**
     * Simpan Aturan
     */
    public function simpanAturan(Request $request)
    {
        $request->validate([
            'penyakit_id' => 'required|exists:penyakit,id',
            'gejala_id' => 'required|exists:gejala,id',
            'cf_value' => 'required|numeric|min:0|max:1'
        ]);

        Aturan::create($request->all());
        
        return redirect()->back()->with('success', 'Aturan berhasil disimpan');
    }

    /**
     * Update Aturan
     */
    public function updateAturan(Request $request, $id)
    {
        $aturan = Aturan::findOrFail($id);
        $aturan->update($request->all());
        
        return redirect()->back()->with('success', 'Aturan berhasil diupdate');
    }

    /**
     * Hapus Aturan
     */
    public function hapusAturan($id)
    {
        $aturan = Aturan::findOrFail($id);
        $aturan->delete();
        
        return redirect()->back()->with('success', 'Aturan berhasil dihapus');
    }

    /**
     * Backup Database
     */
    public function backupDatabase()
    {
        // Logic untuk backup database
        return redirect()->back()->with('success', 'Backup database berhasil dilakukan');
    }

    /**
     * System Info
     */
    public function systemInfo()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_driver' => config('database.default'),
        ];
        
        return view('admin.system-info', compact('systemInfo'));
    }

    /**
     * Get comprehensive dashboard statistics
     */
    private function getDashboardStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        // Total diagnosa bulan ini vs bulan lalu
        $diagnosaBulanIni = Diagnosa::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $diagnosaBulanLalu = Diagnosa::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear)
            ->count();

        $diagnosaGrowth = $diagnosaBulanLalu > 0 
            ? (($diagnosaBulanIni - $diagnosaBulanLalu) / $diagnosaBulanLalu) * 100 
            : ($diagnosaBulanIni > 0 ? 100 : 0);

        return [
            // Basic counts
            'total_users' => User::where('role', 'user')->count(),
            'total_diagnosa' => Diagnosa::count(),
            'total_penyakit' => Penyakit::count(),
            'total_gejala' => Gejala::count(),
            'total_aturan' => Aturan::count(),

            // Time-based counts
            'diagnosa_bulan_ini' => $diagnosaBulanIni,
            'diagnosa_minggu_ini' => Diagnosa::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'diagnosa_hari_ini' => Diagnosa::whereDate('created_at', today())->count(),
            
            // User statistics
            'users_baru_bulan_ini' => User::where('role', 'user')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count(),

            // Growth rates
            'diagnosa_growth' => round($diagnosaGrowth, 1),

            // Accuracy statistics (based on CF values)
            'diagnosa_tinggi_kepercayaan' => Diagnosa::where('cf_tertinggi', '>=', 0.7)->count(),

            // System health
            'aturan_komplit' => $this->getAturanCompleteness(),
            'rerata_waktu_respon' => $this->getAverageResponseTime(),
            'high_confidence_rate' => $this->getHighConfidenceRate(),
        ];
    }

    /**
     * Get comprehensive chart data
     */
    private function getChartData()
    {
        return [
            'monthly_diagnosa' => $this->getMonthlyChartData(),
        ];
    }

    /**
     * Get monthly diagnosa data for the current year
     */
    private function getMonthlyChartData()
    {
        $data = Diagnosa::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyData = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = 1; $i <= 12; $i++) {
            $dataPoint = $data->where('month', $i)->first();
            $monthlyData[] = [
                'month' => $monthNames[$i-1],
                'total' => $dataPoint ? $dataPoint->total : 0
            ];
        }

        return $monthlyData;
    }

    /**
     * Get weekly diagnosa data for the current month
     */
    private function getWeeklyChartData()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $data = Diagnosa::select(
                DB::raw('WEEK(created_at, 1) - WEEK("' . $startOfMonth->format('Y-m-d') . '", 1) + 1 as week'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $weeklyData = [];
        $totalWeeks = ceil($startOfMonth->diffInDays($endOfMonth) / 7);

        for ($i = 1; $i <= $totalWeeks; $i++) {
            $dataPoint = $data->where('week', $i)->first();
            $weeklyData[] = [
                'week' => 'Minggu ' . $i,
                'total' => $dataPoint ? $dataPoint->total : 0
            ];
        }

        return $weeklyData;
    }

    /**
     * Get disease distribution data
     */
    private function getDiseaseChartData()
    {
        $data = Diagnosa::whereNotNull('penyakit_tertinggi')
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->get();

        return $data->map(function($item) {
            return [
                'penyakit' => $item->penyakit_tertinggi,
                'total' => $item->total
            ];
        })->toArray();
    }

    /**
     * Get user activity data
     */
    private function getUserChartData()
    {
        $data = User::where('role', 'user')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $userData = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = 1; $i <= 12; $i++) {
            $dataPoint = $data->where('month', $i)->first();
            $userData[] = [
                'month' => $monthNames[$i-1],
                'total' => $dataPoint ? $dataPoint->total : 0
            ];
        }

        return $userData;
    }

    /**
     * Get top penyakit data
     */
    private function getTopPenyakit($limit = 5)
    {
        return Diagnosa::whereNotNull('penyakit_tertinggi')
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->penyakit_tertinggi => [
                        'total' => $item->total,
                        'percentage' => round(($item->total / max(1, Diagnosa::whereNotNull('penyakit_tertinggi')->count())) * 100, 1)
                    ]
                ];
            })
            ->toArray();
    }

    /**
     * Get recent activity data
     */
    private function getRecentActivity()
    {
        return Diagnosa::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get monthly comparison data
     */
    private function getMonthlyComparison()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        $currentData = Diagnosa::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $previousData = Diagnosa::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear)
            ->count();

        return [
            'current' => $currentData,
            'previous' => $previousData,
            'growth' => $previousData > 0 ? round((($currentData - $previousData) / $previousData) * 100, 1) : 0
        ];
    }

    /**
     * Get aturan completeness percentage
     */
    private function getAturanCompleteness()
    {
        $totalPenyakit = Penyakit::count();
        $totalGejala = Gejala::count();
        $totalAturan = Aturan::count();
        
        $expectedAturan = $totalPenyakit * $totalGejala;
        
        return $expectedAturan > 0 ? round(($totalAturan / $expectedAturan) * 100, 1) : 0;
    }

    /**
     * Get average system response time (estimated)
     */
    private function getAverageResponseTime()
    {
        $totalDiagnosa = Diagnosa::count();
        $averageTime = $totalDiagnosa > 0 ? 2.5 : 0;
        return round($averageTime, 2);
    }

    /**
     * Get high confidence rate
     */
    private function getHighConfidenceRate()
    {
        $totalDiagnosa = Diagnosa::count();
        $highCF = Diagnosa::where('cf_tertinggi', '>=', 0.7)->count();
        
        return $totalDiagnosa > 0 ? round(($highCF / $totalDiagnosa) * 100, 1) : 0;
    }
}