<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('diagnosas', function (Blueprint $table) {
            // Pastikan kolom yang diperlukan ada
            if (!Schema::hasColumn('diagnosas', 'hasil_perhitungan')) {
                $table->json('hasil_perhitungan')->nullable()->after('gejala_terpilih');
            }
            
            if (!Schema::hasColumn('diagnosas', 'cf_tertinggi')) {
                $table->decimal('cf_tertinggi', 5, 3)->default(0)->after('hasil_perhitungan');
            }
            
            if (!Schema::hasColumn('diagnosas', 'penyakit_tertinggi')) {
                $table->string('penyakit_tertinggi')->nullable()->after('cf_tertinggi');
            }
            
            // Ubah tipe data jika diperlukan
            $table->json('gejala_terpilih')->change();
            $table->json('hasil_perhitungan')->change();
        });
    }

    public function down()
    {
        Schema::table('diagnosas', function (Blueprint $table) {
            $table->text('gejala_terpilih')->change();
            $table->dropColumn(['hasil_perhitungan', 'cf_tertinggi', 'penyakit_tertinggi']);
        });
    }
};