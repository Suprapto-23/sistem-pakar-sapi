<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\Aturan;
use App\Models\Diagnosa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GejalaController extends Controller
{
    /**
     * Tampilkan daftar gejala dengan filter dan pencarian
     */
    public function index(Request $request)
    {
        $query = Gejala::query();

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort_field', 'kode');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['kode', 'nama', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('kode', 'asc');
        }

        $gejalas = $query->paginate(20)->withQueryString();

        // Statistik penggunaan gejala
        $usageStats = $this->getGejalaUsageStats();

        return view('admin.gejala.index', compact('gejalas', 'usageStats'));
    }

    /**
     * Tampilkan form tambah gejala
     */
    public function create()
    {
        // Generate suggested kode
        $lastGejala = Gejala::orderBy('kode', 'desc')->first();
        $nextKode = 'G01';
        
        if ($lastGejala) {
            $lastNumber = (int) substr($lastGejala->kode, 1);
            $nextKode = 'G' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        }

        return view('admin.gejala.create', compact('nextKode'));
    }

    /**
     * Simpan gejala baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => [
                'required',
                'regex:/^G\d{2}$/',
                'unique:gejalas,kode'
            ],
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['kode', 'nama', 'deskripsi']);
            
            // Handle gambar upload
            if ($request->hasFile('gambar')) {
                $gambarName = time() . '_' . $request->kode . '.' . $request->gambar->extension();
                $request->gambar->move(public_path('images/gejala'), $gambarName);
                $data['gambar'] = $gambarName;
            }

            $gejala = Gejala::create($data);

            DB::commit();

            return redirect()->route('admin.gejala.index')
                ->with('success', 'Gejala ' . $gejala->nama . ' berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan gejala: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
 * Tampilkan detail gejala
 */
public function show(Gejala $gejala)
{
    // Get related data
    $gejala->load(['aturans.penyakit']);
    
    $relatedAturan = $gejala->aturans;
    $usageStats = $this->getGejalaUsageStats($gejala->id);
    $diagnosaStats = $this->getDiagnosaStats($gejala->id);
    
    // Get total gejala for navigation
    $totalGejala = Gejala::count();
    
    // Get previous and next gejala for navigation
    $previousGejala = Gejala::where('id', '<', $gejala->id)->orderBy('id', 'desc')->first();
    $nextGejala = Gejala::where('id', '>', $gejala->id)->orderBy('id', 'asc')->first();

    return view('admin.gejala.show', compact(
        'gejala', 
        'relatedAturan', 
        'usageStats',
        'diagnosaStats',
        'totalGejala',
        'previousGejala',
        'nextGejala'
    ));
}

    /**
     * Tampilkan form edit gejala
     */
    public function edit(Gejala $gejala)
    {
        return view('admin.gejala.edit', compact('gejala'));
    }

    /**
     * Update data gejala
     */
    public function update(Request $request, Gejala $gejala)
    {
        $request->validate([
            'kode' => [
                'required',
                'regex:/^G\d{2}$/',
                Rule::unique('gejalas')->ignore($gejala->id)
            ],
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['kode', 'nama', 'deskripsi']);
            
            // Handle gambar upload
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($gejala->gambar && file_exists(public_path('images/gejala/' . $gejala->gambar))) {
                    unlink(public_path('images/gejala/' . $gejala->gambar));
                }

                $gambarName = time() . '_' . $request->kode . '.' . $request->gambar->extension();
                $request->gambar->move(public_path('images/gejala'), $gambarName);
                $data['gambar'] = $gambarName;
            }

            $gejala->update($data);

            DB::commit();

            return redirect()->route('admin.gejala.index')
                ->with('success', 'Gejala ' . $gejala->nama . ' berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui gejala: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus data gejala
     */
    public function destroy(Gejala $gejala)
    {
        DB::beginTransaction();
        try {
            // Check if gejala is used in aturan
            $aturanCount = Aturan::where('gejala_id', $gejala->id)->count();
            if ($aturanCount > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus gejala karena masih digunakan dalam ' . $aturanCount . ' aturan.');
            }

            // Check if gejala is used in diagnosa (menggunakan method yang aman)
            $diagnosaCount = $this->countDiagnosaWithGejala($gejala->id);
            if ($diagnosaCount > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus gejala karena masih digunakan dalam ' . $diagnosaCount . ' diagnosa.');
            }

            // Delete image if exists
            if ($gejala->gambar && file_exists(public_path('images/gejala/' . $gejala->gambar))) {
                unlink(public_path('images/gejala/' . $gejala->gambar));
            }

            $gejalaName = $gejala->nama;
            $gejala->delete();

            DB::commit();

            return redirect()->route('admin.gejala.index')
                ->with('success', 'Gejala ' . $gejalaName . ' berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus gejala: ' . $e->getMessage());
        }
    }

    /**
     * Hapus multiple gejala
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:gejalas,id'
        ]);

        DB::beginTransaction();
        try {
            $gejalas = Gejala::whereIn('id', $request->ids)->get();
            $deletedCount = 0;
            $errorMessages = [];

            foreach ($gejalas as $gejala) {
                // Check if gejala is used
                $aturanCount = Aturan::where('gejala_id', $gejala->id)->count();
                $diagnosaCount = $this->countDiagnosaWithGejala($gejala->id);

                if ($aturanCount > 0 || $diagnosaCount > 0) {
                    $errorMessages[] = "Gejala {$gejala->nama} tidak dapat dihapus karena masih digunakan.";
                    continue;
                }

                // Delete image if exists
                if ($gejala->gambar && file_exists(public_path('images/gejala/' . $gejala->gambar))) {
                    unlink(public_path('images/gejala/' . $gejala->gambar));
                }

                $gejala->delete();
                $deletedCount++;
            }

            DB::commit();

            $response = redirect()->route('admin.gejala.index');

            if ($deletedCount > 0) {
                $response = $response->with('success', "{$deletedCount} gejala berhasil dihapus.");
            }

            if (!empty($errorMessages)) {
                $response = $response->with('errors', $errorMessages);
            }

            return $response;

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus gejala: ' . $e->getMessage());
        }
    }

    /**
     * Import gejala dari file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:1024'
        ]);

        // Implementation for import would go here
        return redirect()->back()->with('info', 'Fitur import akan segera tersedia.');
    }

    /**
     * Export gejala ke file
     */
    public function export()
    {
        $gejalas = Gejala::all();
        
        // Format data untuk export
        $exportData = $gejalas->map(function($gejala) {
            return [
                'Kode' => $gejala->kode,
                'Nama' => $gejala->nama,
                'Deskripsi' => $gejala->deskripsi,
                'Tanggal Dibuat' => $gejala->created_at->format('d/m/Y'),
                'Tanggal Diperbarui' => $gejala->updated_at->format('d/m/Y')
            ];
        });

        return view('admin.gejala.export', compact('gejalas', 'exportData'));
    }

   /**
 * Get gejala usage statistics
 */
private function getGejalaUsageStats($gejalaId = null)
{
    if ($gejalaId) {
        // Stats for specific gejala
        $aturanCount = Aturan::where('gejala_id', $gejalaId)->count();
        $diagnosaCount = $this->countDiagnosaWithGejala($gejalaId);
        
        return [
            'aturan_count' => $aturanCount,
            'diagnosa_count' => $diagnosaCount,
            'usage_percentage' => $this->calculateUsagePercentage($gejalaId)
        ];
    } else {
        // Overall stats menggunakan method yang aman
        $totalGejala = Gejala::count();
        $gejalaWithAturan = Gejala::has('aturans')->count();
        $gejalaWithDiagnosa = Diagnosa::getUniqueGejalaCount();

        return [
            'total_gejala' => $totalGejala,
            'gejala_with_aturan' => $gejalaWithAturan,
            'gejala_with_diagnosa' => $gejalaWithDiagnosa,
            'unused_gejala' => $totalGejala - $gejalaWithAturan
        ];
    }
}

    /**
     * Get diagnosa statistics for specific gejala
     */
    private function getDiagnosaStats($gejalaId)
    {
        $totalDiagnosa = Diagnosa::count();
        if ($totalDiagnosa === 0) {
            return [
                'usage_count' => 0,
                'usage_percentage' => 0,
                'common_diseases' => []
            ];
        }

        $usageCount = $this->countDiagnosaWithGejala($gejalaId);
        $usagePercentage = round(($usageCount / $totalDiagnosa) * 100, 2);

        // Get common diseases for this gejala
        $commonDiseases = $this->getCommonDiseasesForGejala($gejalaId);

        return [
            'usage_count' => $usageCount,
            'usage_percentage' => $usagePercentage,
            'common_diseases' => $commonDiseases
        ];
    }

    /**
     * Calculate usage percentage for gejala
     */
    private function calculateUsagePercentage($gejalaId)
    {
        $totalPenyakit = \App\Models\Penyakit::count();
        $usedInPenyakit = Aturan::where('gejala_id', $gejalaId)->count();

        return $totalPenyakit > 0 ? round(($usedInPenyakit / $totalPenyakit) * 100, 2) : 0;
    }

    /**
     * API untuk mendapatkan data gejala
     */
    public function apiIndex(Request $request)
    {
        $gejalas = Gejala::select('id', 'kode', 'nama', 'deskripsi')
            ->when($request->has('search'), function($query) use ($request) {
                $query->where('nama', 'like', "%{$request->search}%")
                      ->orWhere('kode', 'like', "%{$request->search}%");
            })
            ->orderBy('kode')
            ->limit(50)
            ->get();

        return response()->json($gejalas);
    }

    /**
     * Bulk actions for gejala
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,export',
            'ids' => 'required|array',
            'ids.*' => 'exists:gejalas,id'
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
     * Export selected gejala
     */
    private function exportSelected(Request $request)
    {
        $gejalas = Gejala::whereIn('id', $request->ids)->get();
        
        $exportData = $gejalas->map(function($gejala) {
            return [
                'Kode' => $gejala->kode,
                'Nama' => $gejala->nama,
                'Deskripsi' => $gejala->deskripsi
            ];
        });

        return view('admin.gejala.export-selected', compact('gejalas', 'exportData'));
    }

    /**
     * Count diagnosa that use specific gejala (method yang aman)
     */
    private function countDiagnosaWithGejala($gejalaId)
    {
        $diagnosas = Diagnosa::all();
        $count = 0;

        foreach ($diagnosas as $diagnosa) {
            $gejalaTerpilih = json_decode($diagnosa->gejala_terpilih, true);
            if (is_array($gejalaTerpilih) && in_array($gejalaId, $gejalaTerpilih)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Count unique gejala used in diagnosa (method yang aman)
     */
    private function countGejalaWithDiagnosa()
    {
        $diagnosas = Diagnosa::all();
        $uniqueGejalaIds = [];

        foreach ($diagnosas as $diagnosa) {
            $gejalaTerpilih = json_decode($diagnosa->gejala_terpilih, true);
            if (is_array($gejalaTerpilih)) {
                $uniqueGejalaIds = array_merge($uniqueGejalaIds, $gejalaTerpilih);
            }
        }

        return count(array_unique($uniqueGejalaIds));
    }

    /**
     * Get common diseases for specific gejala (method yang aman)
     */
    private function getCommonDiseasesForGejala($gejalaId)
    {
        $diagnosas = Diagnosa::all();
        $diseaseCounts = [];

        foreach ($diagnosas as $diagnosa) {
            $gejalaTerpilih = json_decode($diagnosa->gejala_terpilih, true);
            
            if (is_array($gejalaTerpilih) && in_array($gejalaId, $gejalaTerpilih) && $diagnosa->penyakit_tertinggi) {
                $diseaseName = $diagnosa->penyakit_tertinggi;
                if (!isset($diseaseCounts[$diseaseName])) {
                    $diseaseCounts[$diseaseName] = 0;
                }
                $diseaseCounts[$diseaseName]++;
            }
        }

        // Sort by count descending and take top 5
        arsort($diseaseCounts);
        $topDiseases = array_slice($diseaseCounts, 0, 5, true);

        // Format result
        $result = [];
        foreach ($topDiseases as $diseaseName => $count) {
            $result[] = [
                'penyakit_tertinggi' => $diseaseName,
                'count' => $count
            ];
        }

        return $result;
    }

    /**
     * Get gejala usage frequency (method yang aman)
     */
    public function getGejalaUsageFrequency()
    {
        $diagnosas = Diagnosa::all();
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