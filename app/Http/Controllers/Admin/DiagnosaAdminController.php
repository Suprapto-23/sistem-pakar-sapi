<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosa;
use App\Models\User;
use App\Models\Penyakit;
use App\Models\Gejala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiagnosaAdminController extends Controller
{
    /**
     * Constructor - Tambahkan middleware manual untuk keamanan
     */
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         if (!auth()->check()) {
    //             return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    //         }

    //         if (auth()->user()->role !== 'admin') {
    //             return redirect()->route('landing')->with('error', 'Anda tidak memiliki akses ke halaman admin.');
    //         }

    //         return $next($request);
    //     });
    // }

    /**
     * Tampilkan daftar semua diagnosa dengan filter dan pencarian
     */
    public function index(Request $request)
    {
        $query = Diagnosa::with('user');

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('penyakit_tertinggi', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter berdasarkan penyakit
        if ($request->has('penyakit') && $request->penyakit != '') {
            $query->where('penyakit_tertinggi', $request->penyakit);
        }

        // Filter berdasarkan tingkat kepercayaan
        if ($request->has('cf_min') && $request->cf_min != '') {
            $query->where('cf_tertinggi', '>=', $request->cf_min);
        }

        if ($request->has('cf_max') && $request->cf_max != '') {
            $query->where('cf_tertinggi', '<=', $request->cf_max);
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['created_at', 'cf_tertinggi', 'penyakit_tertinggi'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $diagnosas = $query->paginate(15)->withQueryString();

        // Data untuk filter
        $penyakitList = Diagnosa::whereNotNull('penyakit_tertinggi')
            ->distinct('penyakit_tertinggi')
            ->pluck('penyakit_tertinggi')
            ->sort();

        return view('admin.diagnosa.index', compact('diagnosas', 'penyakitList'));
    }

    /**
     * Tampilkan detail diagnosa
     */
    public function show($id)
    {
        $diagnosa = Diagnosa::with('user')->findOrFail($id);
        
        $hasilPerhitungan = json_decode($diagnosa->hasil_perhitungan, true);
        $gejalaTerpilih = json_decode($diagnosa->gejala_terpilih, true);
        
        // Get gejala details
        $gejalas = Gejala::whereIn('id', $gejalaTerpilih)->get()->keyBy('id');

        // Calculate confidence level
        $confidenceLevel = $this->getConfidenceLevel($diagnosa->cf_tertinggi);

        return view('admin.diagnosa.show', compact(
            'diagnosa', 
            'hasilPerhitungan', 
            'gejalaTerpilih', 
            'gejalas',
            'confidenceLevel'
        ));
    }

    /**
     * Hapus data diagnosa
     */
    public function destroy($id)
    {
        $diagnosa = Diagnosa::findOrFail($id);
        $userName = $diagnosa->user->name;
        $diagnosa->delete();

        return redirect()->route('admin.diagnosa.index')
            ->with('success', "Data diagnosa untuk {$userName} berhasil dihapus.");
    }

    /**
     * Hapus multiple diagnosa
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:diagnosas,id'
        ]);

        $diagnosas = Diagnosa::whereIn('id', $request->ids)->get();
        $count = $diagnosas->count();

        foreach ($diagnosas as $diagnosa) {
            $diagnosa->delete();
        }

        return redirect()->route('admin.diagnosa.index')
            ->with('success', "{$count} data diagnosa berhasil dihapus.");
    }

    /**
     * Export data diagnosa
     */
    public function export(Request $request)
    {
        $query = Diagnosa::with('user');

        // Apply filters if any
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $diagnosas = $query->orderBy('created_at', 'desc')->get();

        // Format data untuk export
        $exportData = $diagnosas->map(function($diagnosa) {
            return [
                'Tanggal' => $diagnosa->created_at->format('d/m/Y H:i'),
                'Pengguna' => $diagnosa->user->name,
                'Email' => $diagnosa->user->email,
                'Penyakit' => $diagnosa->penyakit_tertinggi,
                'Tingkat Keyakinan' => round($diagnosa->cf_tertinggi * 100, 2) . '%',
                'Jumlah Gejala' => count(json_decode($diagnosa->gejala_terpilih, true)),
                'Status' => $diagnosa->status
            ];
        });

        // Untuk sekarang, return view sementara
        return view('admin.diagnosa.export', compact('diagnosas', 'exportData'));
    }

    /**
     * Analisis data diagnosa
     */
    public function analisis()
    {
        $stats = $this->getDiagnosaStats();
        $trends = $this->getDiagnosaTrends();
        $patterns = $this->getDiagnosaPatterns();

        return view('admin.diagnosa.analisis', compact('stats', 'trends', 'patterns'));
    }

    /**
     * API untuk data chart diagnosa
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'monthly');

        switch ($type) {
            case 'weekly':
                $data = $this->getWeeklyDiagnosaData();
                break;
            case 'disease':
                $data = $this->getDiseaseDistributionData();
                break;
            case 'user':
                $data = $this->getUserActivityData();
                break;
            default:
                $data = $this->getMonthlyDiagnosaData();
        }

        return response()->json($data);
    }

    /**
     * Get comprehensive diagnosa statistics
     */
    private function getDiagnosaStats()
    {
        $totalDiagnosa = Diagnosa::count();
        $todayDiagnosa = Diagnosa::whereDate('created_at', today())->count();
        $weekDiagnosa = Diagnosa::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        $monthDiagnosa = Diagnosa::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total' => $totalDiagnosa,
            'today' => $todayDiagnosa,
            'this_week' => $weekDiagnosa,
            'this_month' => $monthDiagnosa,
            'average_daily' => $totalDiagnosa > 0 ? round($totalDiagnosa / max(1, Diagnosa::min('created_at')->diffInDays(now())), 2) : 0,
            'success_rate' => $this->getSuccessRate(),
            'most_common_disease' => $this->getMostCommonDisease(),
            'most_active_user' => $this->getMostActiveUser(),
        ];
    }

    /**
     * Get diagnosa trends
     */
    private function getDiagnosaTrends()
    {
        // Monthly trends for the last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Diagnosa::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }

        // Weekly trends for the last 8 weeks
        $weeklyData = [];
        for ($i = 7; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = now()->subWeeks($i)->endOfWeek();
            
            $count = Diagnosa::whereBetween('created_at', [$start, $end])->count();
            
            $weeklyData[] = [
                'week' => $start->format('d M') . ' - ' . $end->format('d M'),
                'count' => $count
            ];
        }

        return [
            'monthly' => $monthlyData,
            'weekly' => $weeklyData
        ];
    }

    /**
     * Get diagnosa patterns
     */
    private function getDiagnosaPatterns()
    {
        // Disease patterns
        $diseasePatterns = Diagnosa::whereNotNull('penyakit_tertinggi')
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as count'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'disease' => $item->penyakit_tertinggi,
                    'count' => $item->count,
                    'percentage' => round(($item->count / Diagnosa::whereNotNull('penyakit_tertinggi')->count()) * 100, 1)
                ];
            });

        // Time patterns (hourly distribution)
        $timePatterns = Diagnosa::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // User activity patterns
        $userPatterns = User::where('role', 'user')
            ->withCount('diagnosas')
            ->orderBy('diagnosas_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'user' => $user->name,
                    'count' => $user->diagnosas_count,
                    'last_activity' => $user->diagnosas()->latest()->first()->created_at->diffForHumans() ?? 'Tidak ada'
                ];
            });

        return [
            'diseases' => $diseasePatterns,
            'time_distribution' => $timePatterns,
            'user_activity' => $userPatterns
        ];
    }

    /**
     * Get monthly diagnosa data
     */
    private function getMonthlyDiagnosaData()
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
     * Get weekly diagnosa data
     */
    private function getWeeklyDiagnosaData()
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
    private function getDiseaseDistributionData()
    {
        return Diagnosa::whereNotNull('penyakit_tertinggi')
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->get()
            ->map(function($item) {
                return [
                    'penyakit' => $item->penyakit_tertinggi,
                    'total' => $item->total
                ];
            })->toArray();
    }

    /**
     * Get user activity data
     */
    private function getUserActivityData()
    {
        return User::where('role', 'user')
            ->withCount('diagnosas')
            ->orderBy('diagnosas_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'user' => $user->name,
                    'diagnosa_count' => $user->diagnosas_count
                ];
            })->toArray();
    }

    /**
     * Get success rate based on CF values
     */
    private function getSuccessRate()
    {
        $total = Diagnosa::count();
        if ($total === 0) return 0;

        $highConfidence = Diagnosa::where('cf_tertinggi', '>=', 0.7)->count();
        return round(($highConfidence / $total) * 100, 2);
    }

    /**
     * Get most common disease
     */
    private function getMostCommonDisease()
    {
        $disease = Diagnosa::whereNotNull('penyakit_tertinggi')
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->first();

        return $disease ? [
            'name' => $disease->penyakit_tertinggi,
            'count' => $disease->total
        ] : null;
    }

    /**
     * Get most active user
     */
    private function getMostActiveUser()
    {
        $user = User::where('role', 'user')
            ->withCount('diagnosas')
            ->orderBy('diagnosas_count', 'desc')
            ->first();

        return $user ? [
            'name' => $user->name,
            'count' => $user->diagnosas_count
        ] : null;
    }

    /**
     * Get confidence level based on CF value
     */
    private function getConfidenceLevel($cfValue)
    {
        if ($cfValue >= 0.8) {
            return ['level' => 'Tinggi', 'color' => 'success', 'icon' => 'fa-check-circle'];
        } elseif ($cfValue >= 0.6) {
            return ['level' => 'Sedang', 'color' => 'warning', 'icon' => 'fa-info-circle'];
        } else {
            return ['level' => 'Rendah', 'color' => 'danger', 'icon' => 'fa-exclamation-circle'];
        }
    }

    /**
     * Bulk actions for diagnosa
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,export',
            'ids' => 'required|array',
            'ids.*' => 'exists:diagnosas,id'
        ]);

        switch ($request->action) {
            case 'delete':
                return $this->destroyMultiple($request);
            case 'export':
                return $this->exportSelected($request);
            default:
                return redirect()->back()->with('error', 'Aksi tidak valid.');
        }
    }

    /**
     * Export selected diagnosa
     */
    private function exportSelected(Request $request)
    {
        $diagnosas = Diagnosa::with('user')->whereIn('id', $request->ids)->get();
        
        // Format data untuk export
        $exportData = $diagnosas->map(function($diagnosa) {
            return [
                'Tanggal' => $diagnosa->created_at->format('d/m/Y H:i'),
                'Pengguna' => $diagnosa->user->name,
                'Penyakit' => $diagnosa->penyakit_tertinggi,
                'Tingkat Keyakinan' => round($diagnosa->cf_tertinggi * 100, 2) . '%',
                'Jumlah Gejala' => count(json_decode($diagnosa->gejala_terpilih, true))
            ];
        });

        return view('admin.diagnosa.export-selected', compact('diagnosas', 'exportData'));
    }
}