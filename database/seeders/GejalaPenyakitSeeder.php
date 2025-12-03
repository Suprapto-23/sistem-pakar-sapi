<?php

namespace Database\Seeders;

use App\Models\Gejala;
use App\Models\Penyakit;
use App\Models\Aturan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GejalaPenyakitSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('aturan')->delete();
        DB::table('gejala')->delete();
        DB::table('penyakit')->delete();

        // Data Gejala dari Jurnal
        $gejalaData = [
            ['kode' => 'G1', 'nama' => 'Demam tinggi'],
            ['kode' => 'G2', 'nama' => 'Lesi pada mulut dan kuku'],
            ['kode' => 'G3', 'nama' => 'Keluarnya air liur berlebih'],
            ['kode' => 'G4', 'nama' => 'Pembengkakan kelenjar getah bening'],
            ['kode' => 'G5', 'nama' => 'Keluarnya nanah dari ambing'],
            ['kode' => 'G6', 'nama' => 'Penurunan produksi susu'],
            ['kode' => 'G7', 'nama' => 'Nafsu makan menurun'],
            ['kode' => 'G8', 'nama' => 'Kembung atau perut membesar'],
            ['kode' => 'G9', 'nama' => 'Kesulitan bernafas'],
            ['kode' => 'G10', 'nama' => 'Diare berlebihan'],
        ];

        // Simpan ID gejala untuk referensi
        $gejalaIds = [];
        foreach ($gejalaData as $gejala) {
            $created = Gejala::create($gejala);
            $gejalaIds[$gejala['kode']] = $created->id;
        }

        // Data Penyakit dari Jurnal
        $penyakitData = [
            [
                'kode' => 'P01', 
                'nama' => 'Penyakit Mulut dan Kuku (PMK)',
                'deskripsi' => 'Penyakit viral yang sangat menular pada sapi yang ditandai dengan demam tinggi dan lesi pada mulut serta kuku.',
                'solusi' => 'Isolasi hewan sakit, berikan pakan lunak, konsultasi dokter hewan, vaksinasi rutin.'
            ],
            [
                'kode' => 'P02', 
                'nama' => 'Brucellosis',
                'deskripsi' => 'Penyakit bakteri yang menyebabkan keguguran pada sapi betina dan infertilitas pada jantan.',
                'solusi' => 'Vaksinasi, tes laboratorium, isolasi hewan terinfeksi, sanitasi kandang.'
            ],
            [
                'kode' => 'P03', 
                'nama' => 'Antraks',
                'deskripsi' => 'Penyakit bakteri akut yang dapat menyebabkan kematian mendadak pada sapi.',
                'solusi' => 'Karantina ketat, vaksinasi, desinfeksi kandang, hindari kontak dengan hewan sakit.'
            ],
            [
                'kode' => 'P04', 
                'nama' => 'Mastitis',
                'deskripsi' => 'Peradangan pada ambing sapi yang biasanya disebabkan oleh infeksi bakteri.',
                'solusi' => 'Kebersihan kandang, antibiotik, kompres hangat, pemerahan susu yang benar.'
            ],
            [
                'kode' => 'P05', 
                'nama' => 'Cacingan',
                'deskripsi' => 'Infeksi parasit cacing pada saluran pencernaan sapi.',
                'solusi' => 'Pemberian obat cacing, sanitasi kandang, rotasi penggembalaan, pakan berkualitas.'
            ],
        ];

        // Simpan ID penyakit untuk referensi
        $penyakitIds = [];
        foreach ($penyakitData as $penyakit) {
            $created = Penyakit::create($penyakit);
            $penyakitIds[$penyakit['kode']] = $created->id;
        }

        // Data Aturan (CF Pakar) dari Tabel di Jurnal
        $aturanData = [
            // P01 - PMK
            ['P01', 'G1', 0.8], ['P01', 'G2', 1.0], ['P01', 'G3', 1.0],
            ['P01', 'G4', 0.6], ['P01', 'G5', 0.4], ['P01', 'G6', 0.4],
            ['P01', 'G7', 0.6], ['P01', 'G8', 0.2], ['P01', 'G9', 0.4],
            ['P01', 'G10', 0.2],
            
            // P02 - Brucellosis
            ['P02', 'G1', 0.2], ['P02', 'G2', 0.4], ['P02', 'G3', 0.2],
            ['P02', 'G4', 0.4], ['P02', 'G5', 0.2], ['P02', 'G6', 0.8],
            ['P02', 'G7', 0.4], ['P02', 'G8', 0.2], ['P02', 'G9', 0.6],
            ['P02', 'G10', 0.2],
            
            // P03 - Antraks
            ['P03', 'G1', 0.2], ['P03', 'G2', 0.2], ['P03', 'G3', 0.2],
            ['P03', 'G4', 0.2], ['P03', 'G5', 0.2], ['P03', 'G6', 0.2],
            ['P03', 'G7', 0.2], ['P03', 'G8', 0.8], ['P03', 'G9', 0.2],
            ['P03', 'G10', 0.8],
            
            // P04 - Mastitis
            ['P04', 'G1', 0.6], ['P04', 'G2', 0.6], ['P04', 'G3', 0.6],
            ['P04', 'G4', 0.6], ['P04', 'G5', 0.6], ['P04', 'G6', 0.6],
            ['P04', 'G7', 0.6], ['P04', 'G8', 0.6], ['P04', 'G9', 0.6],
            ['P04', 'G10', 1.0],
            
            // P05 - Cacingan
            ['P05', 'G1', 0.2], ['P05', 'G2', 0.2], ['P05', 'G3', 0.2],
            ['P05', 'G4', 0.2], ['P05', 'G5', 0.2], ['P05', 'G6', 0.2],
            ['P05', 'G7', 0.8], ['P05', 'G8', 0.0], ['P05', 'G9', 0.8],
            ['P05', 'G10', 1.0],
        ];

        foreach ($aturanData as $aturan) {
            $penyakitKode = $aturan[0];
            $gejalaKode = $aturan[1];
            $cfPakar = $aturan[2];
            
            if (isset($penyakitIds[$penyakitKode]) && isset($gejalaIds[$gejalaKode])) {
                Aturan::create([
                    'penyakit_id' => $penyakitIds[$penyakitKode],
                    'gejala_id' => $gejalaIds[$gejalaKode],
                    'cf_pakar' => $cfPakar
                ]);
            }
        }
    }
}