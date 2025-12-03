<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnosa;
use App\Models\Penyakit;
use App\Models\Gejala;
use App\Models\Aturan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiagnosaController extends Controller
{
    /**
     * Display diagnosa page
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk melakukan diagnosa.');
        }

        $gejalas = Gejala::orderBy('kode')->get();
        return view('diagnosa.diagnosa', compact('gejalas'));
    }

    /**
     * Process diagnosis dengan metode Certainty Factor - IMPROVED VERSION
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $request->validate([
                'gejala' => 'required|array|min:1',
                'gejala.*' => 'exists:gejalas,id',
            ]);

            $selectedGejala = $request->gejala;
            
            Log::info('Memulai proses diagnosa untuk user: ' . Auth::id(), [
                'gejala_terpilih' => $selectedGejala
            ]);

            // Proses perhitungan Certainty Factor
            $hasilPerhitungan = $this->hitungCertaintyFactor($selectedGejala);
            
            // Simpan hasil diagnosa
            $diagnosaData = [
                'user_id' => Auth::id(),
                'gejala_terpilih' => json_encode($selectedGejala),
                'hasil_perhitungan' => json_encode($hasilPerhitungan),
                'penyakit_tertinggi' => $hasilPerhitungan['penyakit_tertinggi']['nama'] ?? 'Tidak diketahui',
                'cf_tertinggi' => $hasilPerhitungan['cf_tertinggi'] ?? 0,
                'status' => 'completed'
            ];

            $diagnosa = Diagnosa::create($diagnosaData);

            Log::info('Diagnosa berhasil disimpan', [
                'diagnosa_id' => $diagnosa->id,
                'penyakit_tertinggi' => $diagnosa->penyakit_tertinggi,
                'cf_tertinggi' => $diagnosa->cf_tertinggi
            ]);

            return redirect()->route('diagnosa.hasil', $diagnosa->id)
                             ->with('success', 'Diagnosa berhasil diproses!');

        } catch (\Exception $e) {
            Log::error('Error dalam proses diagnosa: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'gejala' => $request->gejala ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                             ->with('error', 'Terjadi kesalahan dalam proses diagnosa: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Calculate Certainty Factor - OPTIMIZED VERSION
     */
    private function hitungCertaintyFactor($selectedGejala)
    {
        try {
            Log::info('=== MEMULAI PERHITUNGAN CERTAINTY FACTOR ===');
            Log::info('Gejala terpilih:', $selectedGejala);

            // Dapatkan semua penyakit
            $semuaPenyakit = Penyakit::all();
            $hasil = [];
            $cfTertinggi = 0;
            $penyakitTertinggi = null;

            if ($semuaPenyakit->isEmpty()) {
                Log::warning('Tidak ada penyakit di database');
                return $this->createEmptyResult();
            }

            foreach ($semuaPenyakit as $penyakit) {
                Log::info("🔍 Memproses penyakit: {$penyakit->nama} (ID: {$penyakit->id})");
                
                $cfCombine = 0;
                $gejalaPenyakit = [];
                $firstIteration = true;

                // Dapatkan semua aturan untuk penyakit ini yang sesuai dengan gejala terpilih
                $aturans = Aturan::with(['gejala'])
                    ->where('penyakit_id', $penyakit->id)
                    ->whereIn('gejala_id', $selectedGejala)
                    ->orderBy('cf_pakar', 'desc')
                    ->get();

                Log::info("📊 Jumlah aturan untuk {$penyakit->nama}: " . $aturans->count());

                if ($aturans->isEmpty()) {
                    Log::info("❌ Tidak ada aturan untuk penyakit {$penyakit->nama} dengan gejala terpilih");
                    continue;
                }

                foreach ($aturans as $aturan) {
                    $cfPakar = floatval($aturan->cf_pakar);
                    $gejalaId = $aturan->gejala_id;
                    
                    // Default CF user 0.8
                    $cfUser = 0.8;
                    
                    // Validasi nilai CF
                    $cfPakar = max(0, min(1, $cfPakar));
                    $cfUser = max(0, min(1, $cfUser));

                    // Hitung CF untuk gejala ini: CF_gejala = CF_pakar * CF_user
                    $cfGejala = $cfPakar * $cfUser;
                    
                    Log::info("   📈 Gejala {$aturan->gejala->kode}: CF_Pakar={$cfPakar}, CF_User={$cfUser}, CF_Gejala={$cfGejala}");

                    // Combine dengan CF sebelumnya menggunakan rumus CF Combine
                    if ($firstIteration) {
                        $cfCombine = $cfGejala;
                        $firstIteration = false;
                    } else {
                        $oldCF = $cfCombine;
                        $cfCombine = $oldCF + ($cfGejala * (1 - $oldCF));
                    }

                    $gejalaPenyakit[] = [
                        'gejala_id' => $gejalaId,
                        'gejala_nama' => $aturan->gejala->nama,
                        'gejala_kode' => $aturan->gejala->kode,
                        'cf_pakar' => $cfPakar,
                        'cf_user' => $cfUser,
                        'cf_gejala' => round($cfGejala, 4)
                    ];
                }

                $cfAkhir = round($cfCombine, 4);
                $persentase = round($cfAkhir * 100, 2);

                $hasilPenyakit = [
                    'penyakit_id' => $penyakit->id,
                    'nama' => $penyakit->nama,
                    'kode' => $penyakit->kode,
                    'deskripsi' => $penyakit->deskripsi,
                    'solusi' => $penyakit->solusi,
                    'cf_akhir' => $cfAkhir,
                    'persentase' => $persentase,
                    'gejala_terdeteksi' => $gejalaPenyakit
                ];

                $hasil[] = $hasilPenyakit;

                Log::info("✅ Hasil penyakit {$penyakit->nama}: CF_Akhir={$cfAkhir}, Persentase={$persentase}%");

                // Update penyakit dengan CF tertinggi
                if ($cfAkhir > $cfTertinggi) {
                    $cfTertinggi = $cfAkhir;
                    $penyakitTertinggi = $hasilPenyakit;
                }
            }

            // Handle case ketika tidak ada penyakit yang ditemukan
            if (empty($hasil)) {
                Log::warning('❌ Tidak ada penyakit yang ditemukan setelah perhitungan CF');
                return $this->createEmptyResult();
            }

            // Urutkan berdasarkan CF tertinggi
            usort($hasil, function($a, $b) {
                return $b['cf_akhir'] <=> $a['cf_akhir'];
            });

            $finalResult = [
                'hasil_perhitungan' => $hasil,
                'penyakit_tertinggi' => $penyakitTertinggi,
                'cf_tertinggi' => $cfTertinggi
            ];

            Log::info('🎉 FINAL HASIL PERHITUNGAN CF - Penyakit Tertinggi: ' . ($penyakitTertinggi['nama'] ?? 'Tidak diketahui'));
            Log::info('=== SELESAI PERHITUNGAN CERTAINTY FACTOR ===');

            return $finalResult;

        } catch (\Exception $e) {
            Log::error('💥 ERROR dalam perhitungan CF: ' . $e->getMessage(), [
                'selected_gejala' => $selectedGejala,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->createEmptyResult();
        }
    }

    /**
     * Create empty result structure
     */
    private function createEmptyResult()
    {
        return [
            'hasil_perhitungan' => [],
            'penyakit_tertinggi' => [
                'penyakit_id' => 0,
                'nama' => 'Tidak diketahui',
                'kode' => 'TD',
                'deskripsi' => 'Tidak ada penyakit yang terdeteksi berdasarkan gejala yang dipilih.',
                'solusi' => 'Konsultasikan dengan dokter hewan untuk pemeriksaan lebih lanjut.',
                'cf_akhir' => 0,
                'persentase' => 0,
                'gejala_terdeteksi' => []
            ],
            'cf_tertinggi' => 0
        ];
    }

    /**
     * Display diagnosis result - IMPROVED VERSION
     */
    public function hasil($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $diagnosa = Diagnosa::with('user')->where('id', $id)->firstOrFail();
            
            // Authorization check
            if (Auth::user()->role === 'user' && $diagnosa->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            // Decode JSON data dengan error handling
            $gejalaTerpilih = json_decode($diagnosa->gejala_terpilih, true) ?? [];
            $hasilPerhitungan = json_decode($diagnosa->hasil_perhitungan, true) ?? [];
            
            // Dapatkan detail gejala
            $gejalas = Gejala::whereIn('id', $gejalaTerpilih)->get();

            Log::info('Menampilkan hasil diagnosa:', [
                'diagnosa_id' => $diagnosa->id,
                'penyakit_tertinggi' => $diagnosa->penyakit_tertinggi,
                'cf_tertinggi' => $diagnosa->cf_tertinggi,
                'jumlah_gejala' => count($gejalaTerpilih)
            ]);

            return view('diagnosa.hasil', compact('diagnosa', 'hasilPerhitungan', 'gejalas'));

        } catch (\Exception $e) {
            Log::error('Error menampilkan hasil diagnosa: ' . $e->getMessage(), [
                'diagnosa_id' => $id,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('diagnosa.index')
                             ->with('error', 'Gagal menampilkan hasil diagnosa: ' . $e->getMessage());
        }
    }

    /**
     * Display diagnosis history
     */
    public function riwayat()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        try {
            $user = Auth::user();
            
            $query = Diagnosa::with('user');
            
            if ($user->role === 'user') {
                $query->where('user_id', $user->id);
            }
            
            $riwayat = $query->orderBy('created_at', 'desc')
                           ->paginate(10);

            return view('diagnosa.riwayat', compact('riwayat'));

        } catch (\Exception $e) {
            Log::error('Error menampilkan riwayat diagnosa: ' . $e->getMessage());
            return redirect()->route('diagnosa.index')
                             ->with('error', 'Gagal menampilkan riwayat diagnosa.');
        }
    }


/**
 * Export PDF hasil diagnosa dengan data yang diperbaiki
 */
public function exportPDF($id)
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    try {
        $diagnosa = Diagnosa::with('user')->where('id', $id)->firstOrFail();
        
        if (Auth::user()->role === 'user' && $diagnosa->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Process data untuk PDF
        $hasilPerhitungan = is_string($diagnosa->hasil_perhitungan) ? 
                           json_decode($diagnosa->hasil_perhitungan, true) : 
                           $diagnosa->hasil_perhitungan;
        
        $gejalaTerpilih = is_string($diagnosa->gejala_terpilih) ? 
                         json_decode($diagnosa->gejala_terpilih, true) : 
                         $diagnosa->gejala_terpilih;
        
        $gejalas = Gejala::whereIn('id', $gejalaTerpilih)->get();

        Log::info('Export PDF diagnosa', [
            'diagnosa_id' => $diagnosa->id,
            'gejala_count' => count($gejalas)
        ]);

        $pdf = PDF::loadView('diagnosa.export-pdf', compact('diagnosa', 'hasilPerhitungan', 'gejalas'));
        
        $filename = 'diagnosa-sapi-' . $diagnosa->id . '-' . $diagnosa->created_at->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Error export PDF: ' . $e->getMessage());
        return redirect()->back()
                         ->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
    }
}

    /**
     * Delete diagnosa
     */
    public function destroy($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $diagnosa = Diagnosa::findOrFail($id);
            
            if (Auth::user()->role === 'user' && $diagnosa->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            $diagnosaId = $diagnosa->id;
            $diagnosa->delete();

            Log::info('Diagnosa berhasil dihapus', [
                'diagnosa_id' => $diagnosaId
            ]);

            return redirect()->route('diagnosa.riwayat')
                             ->with('success', 'Diagnosa berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error menghapus diagnosa: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Gagal menghapus diagnosa: ' . $e->getMessage());
        }
    }

    /**
     * Get penyakit terbanyak untuk user
     */
    private function getPenyakitTerbanyakUser($userId)
    {
        return Diagnosa::where('user_id', $userId)
            ->where('penyakit_tertinggi', '!=', 'Tidak diketahui')
            ->whereNotNull('penyakit_tertinggi')
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->pluck('total', 'penyakit_tertinggi')
            ->toArray();
    }

    /**
     * Get penyakit terbanyak untuk admin
     */
    private function getPenyakitTerbanyak()
    {
        return Diagnosa::where('penyakit_tertinggi', '!=', 'Tidak diketahui')
            ->whereNotNull('penyakit_tertinggi')
            ->select('penyakit_tertinggi', DB::raw('COUNT(*) as total'))
            ->groupBy('penyakit_tertinggi')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->pluck('total', 'penyakit_tertinggi')
            ->toArray();
    }

    /**
     * Test method untuk debugging
     */
    public function testPerhitungan()
    {
        try {
            // Test dengan gejala yang sesuai database
            $selectedGejala = [11, 12, 13]; // G01, G02, G03
            $hasil = $this->hitungCertaintyFactor($selectedGejala);
            
            return response()->json([
                'success' => true,
                'test_data' => [
                    'gejala' => $selectedGejala,
                    'gejala_detail' => Gejala::whereIn('id', $selectedGejala)->get(['id', 'kode', 'nama'])
                ],
                'hasil_perhitungan' => $hasil
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}