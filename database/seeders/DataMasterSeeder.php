<?php

namespace Database\Seeders;

use App\Models\Penyakit;
use App\Models\Gejala;
use App\Models\Aturan;
use Illuminate\Database\Seeder;

class DataMasterSeeder extends Seeder
{
    public function run(): void
    {
        // Data Penyakit
        $penyakits = [
            [
                'kode' => 'P01',
                'nama' => 'Penyakit Mulut dan Kuku (PMK)',
                'deskripsi' => 'Penyakit viral yang sangat menular pada sapi...',
                'solusi' => 'Isolasi hewan sakit, berikan pakan lunak...'
            ],
            [
                'kode' => 'P02', 
                'nama' => 'Brucellosis',
                'deskripsi' => 'Penyakit bakteri yang menyebabkan keguguran...',
                'solusi' => 'Vaksinasi, tes laboratorium, isolasi...'
            ],
            // Tambahkan penyakit lainnya...
        ];

        foreach ($penyakits as $penyakit) {
            Penyakit::create($penyakit);
        }

        // Data Gejala
        $gejalas = [
            ['kode' => 'G01', 'nama' => 'Demam tinggi'],
            ['kode' => 'G02', 'nama' => 'Lesi pada mulut dan kuku'],
            ['kode' => 'G03', 'nama' => 'Keluarnya air liur berlebih'],
            // Tambahkan gejala lainnya...
        ];

        foreach ($gejalas as $gejala) {
            Gejala::create($gejala);
        }

        // Data Aturan (CF Pakar)
        $aturans = [
            // P01 - PMK
            ['penyakit_id' => 1, 'gejala_id' => 1, 'cf_pakar' => 0.8],
            ['penyakit_id' => 1, 'gejala_id' => 2, 'cf_pakar' => 1.0],
            // Tambahkan aturan lainnya...
        ];

        foreach ($aturans as $aturan) {
            Aturan::create($aturan);
        }
    }
}