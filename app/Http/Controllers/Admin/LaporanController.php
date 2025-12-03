<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosa;
use App\Models\Penyakit;
use App\Models\User;
use App\Models\Gejala;
use App\Models\Aturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        $totalDiagnosa = Diagnosa::count();
        $diagnosaAkurat = Diagnosa::where('cf_tertinggi', '>=', 0.7)->count();
        $persentaseAkurat = $totalDiagnosa > 0 ? round(($diagnosaAkurat / $totalDiagnosa) * 100, 1) : 0;
        
        $penyakitTerbanyak = Diagnosa::select('penyakit_tertinggi', DB::raw('COUNT(*) as count'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('count', 'desc')
            ->first();
            
        // Perbaikan: Gunakan role 'user' saja, tanpa is_active
        $userAktif = User::where('role', 'user')->count();

        $statistikPenyakit = Diagnosa::select(
            'penyakit_tertinggi as nama_penyakit',
            DB::raw('COUNT(*) as jumlah'),
            DB::raw('AVG(cf_tertinggi) as rata_rata_cf')
        )
        ->groupBy('penyakit_tertinggi')
        ->orderBy('jumlah', 'desc')
        ->get();

        // Data untuk chart
        $chartData = $this->getChartData();

        return view('admin.laporan.index', compact(
            'totalDiagnosa',
            'diagnosaAkurat',
            'persentaseAkurat',
            'penyakitTerbanyak',
            'userAktif',
            'statistikPenyakit',
            'chartData'
        ));
    }

    public function cetak()
    {
        // Data untuk cetak PDF
        $totalDiagnosa = Diagnosa::count();
        $diagnosaAkurat = Diagnosa::where('cf_tertinggi', '>=', 0.7)->count();
        $persentaseAkurat = $totalDiagnosa > 0 ? round(($diagnosaAkurat / $totalDiagnosa) * 100, 1) : 0;
        
        $statistikPenyakit = Diagnosa::select(
            'penyakit_tertinggi as nama_penyakit',
            DB::raw('COUNT(*) as jumlah'),
            DB::raw('AVG(cf_tertinggi) as rata_rata_cf')
        )
        ->groupBy('penyakit_tertinggi')
        ->orderBy('jumlah', 'desc')
        ->get();

        $userAktif = User::where('role', 'user')->count();

        // Logic untuk cetak PDF akan ditambahkan di sini
        return view('admin.laporan.cetak', compact(
            'totalDiagnosa',
            'diagnosaAkurat',
            'persentaseAkurat',
            'statistikPenyakit',
            'userAktif'
        ));
    }

    /**
     * Get laporan detail dengan filter
     */
    public function detail(Request $request)
    {
        $query = Diagnosa::with('user');

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter berdasarkan penyakit
        if ($request->has('penyakit') && $request->penyakit) {
            $query->where('penyakit_tertinggi', 'like', "%{$request->penyakit}%");
        }

        // Filter berdasarkan tingkat keyakinan
        if ($request->has('confidence_level') && $request->confidence_level) {
            switch ($request->confidence_level) {
                case 'high':
                    $query->where('cf_tertinggi', '>=', 0.8);
                    break;
                case 'medium':
                    $query->whereBetween('cf_tertinggi', [0.5, 0.79]);
                    break;
                case 'low':
                    $query->where('cf_tertinggi', '<', 0.5);
                    break;
            }
        }

        $diagnosas = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.laporan.detail', compact('diagnosas'));
    }

    /**
     * Get statistik performa sistem
     */
    public function statistikPerforma()
    {
        // Statistik bulanan
        $bulanan = $this->getStatistikBulanan();
        
        // Statistik penyakit
        $penyakitStats = $this->getPenyakitStats();
        
        // Statistik user
        $userStats = $this->getUserStats();

        return view('admin.laporan.performa', compact('bulanan', 'penyakitStats', 'userStats'));
    }

    /**
     * Export data ke Excel
     */
    public function exportExcel(Request $request)
    {
        // Filter data berdasarkan request
        $query = Diagnosa::with('user');

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $diagnosas = $query->orderBy('created_at', 'desc')->get();

        // Format data untuk Excel
        $exportData = $diagnosas->map(function ($diagnosa) {
            return [
                'ID Diagnosa' => $diagnosa->id,
                'Nama User' => $diagnosa->user->name,
                'Penyakit Terdiagnosa' => $diagnosa->penyakit_tertinggi,
                'Tingkat Keyakinan' => round($diagnosa->cf_tertinggi * 100, 1) . '%',
                'Jumlah Gejala' => count(json_decode($diagnosa->gejala_terpilih, true) ?? []),
                'Tanggal Diagnosa' => $diagnosa->created_at->format('d/m/Y H:i'),
                'Status' => $diagnosa->status
            ];
        });

        return view('admin.laporan.export-excel', compact('exportData'));
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

        // Data distribusi penyakit untuk pie chart
        $distribusiPenyakit = Diagnosa::select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->whereNotNull('penyakit_tertinggi')
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->limit(6)
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
     * Get statistik bulanan
     */
    private function getStatistikBulanan()
    {
        $currentYear = now()->year;

        return Diagnosa::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('COUNT(*) as total_diagnosa'),
            DB::raw('AVG(cf_tertinggi) as rata_rata_cf'),
            DB::raw('COUNT(CASE WHEN cf_tertinggi >= 0.7 THEN 1 END) as diagnosa_akurat')
        )
        ->whereYear('created_at', $currentYear)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->map(function ($item) {
            return [
                'bulan' => Carbon::create()->month($item->bulan)->format('F'),
                'total_diagnosa' => $item->total_diagnosa,
                'rata_rata_cf' => round($item->rata_rata_cf * 100, 1),
                'diagnosa_akurat' => $item->diagnosa_akurat,
                'akurasi' => $item->total_diagnosa > 0 ? round(($item->diagnosa_akurat / $item->total_diagnosa) * 100, 1) : 0
            ];
        });
    }

    /**
     * Get statistik penyakit
     */
    private function getPenyakitStats()
    {
        return Diagnosa::select(
            'penyakit_tertinggi',
            DB::raw('COUNT(*) as jumlah'),
            DB::raw('AVG(cf_tertinggi) as rata_rata_cf'),
            DB::raw('MIN(created_at) as pertama_kali'),
            DB::raw('MAX(created_at) as terakhir_kali')
        )
        ->whereNotNull('penyakit_tertinggi')
        ->groupBy('penyakit_tertinggi')
        ->orderBy('jumlah', 'desc')
        ->limit(10)
        ->get();
    }

    /**
     * Get statistik user
     */
    private function getUserStats()
    {
        return User::where('role', 'user')
            ->withCount(['diagnosas as total_diagnosa'])
            ->withCount(['diagnosas as diagnosa_bulan_ini' => function ($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->orderBy('total_diagnosa', 'desc')
            ->limit(10)
            ->get();
    }
}