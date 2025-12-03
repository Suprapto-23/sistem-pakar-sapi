<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penyakit;
use App\Models\Aturan;
use App\Models\Diagnosa;
use App\Models\Gejala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenyakitController extends Controller
{
    /**
     * Tampilkan daftar penyakit dengan filter dan pencarian
     */
    public function index(Request $request)
    {
        $query = Penyakit::withCount(['gejalas', 'diagnosas']);

        // Filter berdasarkan pencarian (hanya kolom yang ada)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('solusi', 'like', "%{$search}%");
            });
        }

        // Sorting (hanya kolom yang ada)
        $sortField = $request->get('sort_field', 'kode');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['kode', 'nama', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('kode', 'asc');
        }

        $penyakits = $query->paginate(20)->withQueryString();

        // Statistik penggunaan penyakit
        $usageStats = $this->getPenyakitUsageStats();
        
        // Data untuk view index.blade.php
        $totalTerhubungGejala = Penyakit::has('aturans')->count();
        $totalDiagnosa = Diagnosa::count();

        return view('admin.penyakit.index', compact(
            'penyakits', 
            'usageStats', 
            'totalTerhubungGejala',
            'totalDiagnosa'
        ));
    }

    /**
     * Tampilkan form tambah penyakit
     */
    public function create()
    {
        // Generate suggested kode
        $lastPenyakit = Penyakit::orderBy('kode', 'desc')->first();
        $nextKode = 'P01';
        
        if ($lastPenyakit) {
            $lastNumber = (int) substr($lastPenyakit->kode, 1);
            $nextKode = 'P' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        }

        // PERBAIKAN: Hapus scope active() karena kolom status tidak ada di tabel gejalas
        $gejalas = Gejala::all();

        return view('admin.penyakit.create', compact('nextKode', 'gejalas'));
    }

    /**
     * Simpan penyakit baru - HAPUS validasi untuk kolom yang tidak ada
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => [
                'required',
                'regex:/^P\d{2}$/',
                'unique:penyakits,kode'
            ],
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'solusi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gejala' => 'nullable|array',
            'gejala.*.id' => 'exists:gejalas,id',
            'gejala.*.cf_pakar' => 'nullable|numeric|min:0|max:1'
        ]);

        DB::beginTransaction();
        try {
            // Hanya gunakan kolom yang ada di database
            $data = $request->only([
                'kode', 'nama', 'deskripsi', 'solusi'
            ]);
            
            // Handle gambar upload
            if ($request->hasFile('gambar')) {
                $gambarName = time() . '_' . $request->kode . '.' . $request->gambar->extension();
                $request->gambar->storeAs('public/images/penyakit', $gambarName);
                $data['gambar'] = $gambarName;
            }

            $penyakit = Penyakit::create($data);

            // Create aturan jika ada gejala yang dipilih
            if ($request->has('gejala')) {
                foreach ($request->gejala as $gejalaData) {
                    if (!empty($gejalaData['id']) && isset($gejalaData['cf_pakar'])) {
                        Aturan::create([
                            'penyakit_id' => $penyakit->id,
                            'gejala_id' => $gejalaData['id'],
                            'cf_pakar' => $gejalaData['cf_pakar'] ?? 0.5
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.penyakit.index')
                ->with('success', 'Penyakit ' . $penyakit->nama . ' berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan penyakit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan detail penyakit
     */
    public function show(Penyakit $penyakit)
    {
        // Load relationships
        $penyakit->load(['aturans.gejala', 'diagnosas']);
        
        $relatedAturan = $penyakit->aturans;
        $diagnosaStats = $this->getDiagnosaStats($penyakit->id);
        $gejalaTerkait = $penyakit->gejalas;

        return view('admin.penyakit.show', compact(
            'penyakit', 
            'relatedAturan',
            'diagnosaStats',
            'gejalaTerkait'
        ));
    }

    /**
     * Tampilkan form edit penyakit
     */
    public function edit(Penyakit $penyakit)
    {
        // PERBAIKAN: Hapus scope active() karena kolom status tidak ada di tabel gejalas
        $gejalas = Gejala::all();
        $currentAturan = Aturan::where('penyakit_id', $penyakit->id)
            ->get()
            ->keyBy('gejala_id');

        return view('admin.penyakit.edit', compact('penyakit', 'gejalas', 'currentAturan'));
    }

    /**
     * Update data penyakit - HAPUS validasi untuk kolom yang tidak ada
     */
    public function update(Request $request, Penyakit $penyakit)
    {
        $request->validate([
            'kode' => [
                'required',
                'regex:/^P\d{2}$/',
                Rule::unique('penyakits')->ignore($penyakit->id)
            ],
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'solusi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gejala' => 'nullable|array',
            'gejala.*.id' => 'exists:gejalas,id',
            'gejala.*.cf_pakar' => 'nullable|numeric|min:0|max:1'
        ]);

        DB::beginTransaction();
        try {
            // Hanya gunakan kolom yang ada di database
            $data = $request->only([
                'kode', 'nama', 'deskripsi', 'solusi'
            ]);
            
            // Handle gambar upload
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($penyakit->gambar && Storage::exists('public/images/penyakit/' . $penyakit->gambar)) {
                    Storage::delete('public/images/penyakit/' . $penyakit->gambar);
                }

                $gambarName = time() . '_' . $request->kode . '.' . $request->gambar->extension();
                $request->gambar->storeAs('public/images/penyakit', $gambarName);
                $data['gambar'] = $gambarName;
            }

            $penyakit->update($data);

            // Update aturan
            if ($request->has('gejala')) {
                // Delete existing aturan
                Aturan::where('penyakit_id', $penyakit->id)->delete();

                // Create new aturan
                foreach ($request->gejala as $gejalaData) {
                    if (!empty($gejalaData['id']) && isset($gejalaData['cf_pakar'])) {
                        Aturan::create([
                            'penyakit_id' => $penyakit->id,
                            'gejala_id' => $gejalaData['id'],
                            'cf_pakar' => $gejalaData['cf_pakar'] ?? 0.5
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.penyakit.index')
                ->with('success', 'Penyakit ' . $penyakit->nama . ' berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui penyakit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus data penyakit
     */
    public function destroy(Penyakit $penyakit)
    {
        DB::beginTransaction();
        try {
            // Check if penyakit is used in aturan
            $aturanCount = $penyakit->aturans()->count();
            if ($aturanCount > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus penyakit karena memiliki ' . $aturanCount . ' aturan terkait.');
            }

            // Check if penyakit is used in diagnosa
            $diagnosaCount = $penyakit->diagnosas()->count();
            if ($diagnosaCount > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus penyakit karena masih digunakan dalam ' . $diagnosaCount . ' diagnosa.');
            }

            // Delete image if exists
            if ($penyakit->gambar && Storage::exists('public/images/penyakit/' . $penyakit->gambar)) {
                Storage::delete('public/images/penyakit/' . $penyakit->gambar);
            }

            $penyakitName = $penyakit->nama;
            $penyakit->delete();

            DB::commit();

            return redirect()->route('admin.penyakit.index')
                ->with('success', 'Penyakit ' . $penyakitName . ' berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus penyakit: ' . $e->getMessage());
        }
    }

    /**
     * Get penyakit usage statistics
     */
    private function getPenyakitUsageStats($penyakitId = null)
    {
        if ($penyakitId) {
            // Stats for specific penyakit
            $penyakit = Penyakit::find($penyakitId);
            $aturanCount = $penyakit ? $penyakit->aturans()->count() : 0;
            $diagnosaCount = $penyakit ? $penyakit->diagnosas()->count() : 0;
            
            return [
                'aturan_count' => $aturanCount,
                'diagnosa_count' => $diagnosaCount,
                'diagnosa_percentage' => $this->calculateDiagnosaPercentage($penyakitId)
            ];
        } else {
            // Overall stats
            $totalPenyakit = Penyakit::count();
            $penyakitWithAturan = Penyakit::has('aturans')->count();
            
            // Hitung penyakit dengan diagnosa
            $penyakitWithDiagnosa = DB::table('penyakits')
                ->join('diagnosas', 'diagnosas.penyakit_tertinggi', '=', 'penyakits.nama')
                ->distinct('penyakits.id')
                ->count('penyakits.id');

            return [
                'total_penyakit' => $totalPenyakit,
                'penyakit_with_aturan' => $penyakitWithAturan,
                'penyakit_with_diagnosa' => $penyakitWithDiagnosa,
                'unused_penyakit' => $totalPenyakit - $penyakitWithAturan
            ];
        }
    }

    /**
     * Get diagnosa statistics for specific penyakit
     */
    private function getDiagnosaStats($penyakitId)
    {
        $penyakit = Penyakit::findOrFail($penyakitId);
        
        $totalDiagnosa = Diagnosa::count();
        $diagnosaCount = $penyakit->diagnosas()->count();
        $diagnosaPercentage = $totalDiagnosa > 0 ? round(($diagnosaCount / $totalDiagnosa) * 100, 2) : 0;

        // Get monthly diagnosa trend
        $monthlyTrend = $penyakit->diagnosas()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Get average CF value
        $averageCF = $penyakit->diagnosas()->avg('cf_tertinggi');

        return [
            'diagnosa_count' => $diagnosaCount,
            'diagnosa_percentage' => $diagnosaPercentage,
            'monthly_trend' => $monthlyTrend,
            'average_cf' => round($averageCF ?? 0, 3),
            'total_diagnosa' => $totalDiagnosa
        ];
    }

    /**
     * Calculate diagnosa percentage for penyakit
     */
    private function calculateDiagnosaPercentage($penyakitId)
    {
        $penyakit = Penyakit::findOrFail($penyakitId);
        $totalDiagnosa = Diagnosa::count();
        $penyakitDiagnosa = $penyakit->diagnosas()->count();

        return $totalDiagnosa > 0 ? round(($penyakitDiagnosa / $totalDiagnosa) * 100, 2) : 0;
    }

    /**
     * API untuk mendapatkan data penyakit
     */
    public function apiIndex(Request $request)
    {
        $penyakits = Penyakit::select('id', 'kode', 'nama', 'deskripsi')
            ->when($request->has('search'), function($query) use ($request) {
                $query->where('nama', 'like', "%{$request->search}%")
                      ->orWhere('kode', 'like', "%{$request->search}%");
            })
            ->orderBy('kode')
            ->limit(50)
            ->get();

        return response()->json($penyakits);
    }

    /**
     * Bulk actions for penyakit
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,export',
            'ids' => 'required|array',
            'ids.*' => 'exists:penyakits,id'
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
     * Destroy multiple penyakit
     */
    private function destroyMultiple(Request $request)
    {
        DB::beginTransaction();
        try {
            $penyakits = Penyakit::whereIn('id', $request->ids)->get();
            $deletedCount = 0;

            foreach ($penyakits as $penyakit) {
                // Check if penyakit is used
                $aturanCount = $penyakit->aturans()->count();
                $diagnosaCount = $penyakit->diagnosas()->count();

                if ($aturanCount > 0 || $diagnosaCount > 0) {
                    continue; // Skip jika digunakan
                }

                // Delete image if exists
                if ($penyakit->gambar && Storage::exists('public/images/penyakit/' . $penyakit->gambar)) {
                    Storage::delete('public/images/penyakit/' . $penyakit->gambar);
                }

                $penyakit->delete();
                $deletedCount++;
            }

            DB::commit();

            if ($deletedCount > 0) {
                return redirect()->route('admin.penyakit.index')
                    ->with('success', $deletedCount . ' penyakit berhasil dihapus.');
            } else {
                return redirect()->back()
                    ->with('error', 'Tidak ada penyakit yang dapat dihapus karena masih digunakan.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus penyakit: ' . $e->getMessage());
        }
    }

    /**
     * Export selected penyakit
     */
    private function exportSelected(Request $request)
    {
        $penyakits = Penyakit::whereIn('id', $request->ids)->get();
        
        $exportData = $penyakits->map(function($penyakit) {
            return [
                'Kode' => $penyakit->kode,
                'Nama' => $penyakit->nama,
                'Deskripsi' => $penyakit->deskripsi,
                'Solusi' => $penyakit->solusi
            ];
        });

        return view('admin.penyakit.export-selected', compact('penyakits', 'exportData'));
    }

    /**
     * Update penyakit status (active/inactive)
     */
    public function updateStatus(Request $request, Penyakit $penyakit)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $penyakit->status = $request->status;
        $penyakit->save();

        $statusText = $request->status === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Penyakit {$penyakit->nama} berhasil {$statusText}.");
    }

    /**
     * Manage aturan for penyakit
     */
    public function manageAturan(Penyakit $penyakit)
    {
        // PERBAIKAN: Hapus scope active() karena kolom status tidak ada di tabel gejalas
        $gejalas = Gejala::all();
        $currentAturan = Aturan::with('gejala')
            ->where('penyakit_id', $penyakit->id)
            ->get()
            ->keyBy('gejala_id');

        return view('admin.penyakit.manage-aturan', compact('penyakit', 'gejalas', 'currentAturan'));
    }

    /**
     * Update aturan for penyakit
     */
    public function updateAturan(Request $request, Penyakit $penyakit)
    {
        $request->validate([
            'gejala' => 'required|array',
            'gejala.*.id' => 'required|exists:gejalas,id',
            'gejala.*.cf_pakar' => 'required|numeric|min:0|max:1'
        ]);

        DB::beginTransaction();
        try {
            // Delete existing aturan
            Aturan::where('penyakit_id', $penyakit->id)->delete();

            // Create new aturan
            foreach ($request->gejala as $gejalaData) {
                Aturan::create([
                    'penyakit_id' => $penyakit->id,
                    'gejala_id' => $gejalaData['id'],
                    'cf_pakar' => $gejalaData['cf_pakar']
                ]);
            }

            DB::commit();

            return redirect()->route('admin.penyakit.show', $penyakit->id)
                ->with('success', 'Aturan untuk penyakit ' . $penyakit->nama . ' berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui aturan: ' . $e->getMessage());
        }
    }
}