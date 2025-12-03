<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToPenyakitsTable extends Migration
{
    public function up()
    {
        Schema::table('penyakits', function (Blueprint $table) {
            // Tambahkan kolom yang diperlukan
            $table->string('kategori')->nullable()->after('solusi');
            $table->enum('tingkat_keparahan', ['rendah', 'sedang', 'tinggi', 'kritis'])->nullable()->after('kategori');
            $table->text('pencegahan')->nullable()->after('tingkat_keparahan');
            $table->text('penanganan_medis')->nullable()->after('pencegahan');
            $table->string('masa_karantina')->nullable()->after('penanganan_medis');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('masa_karantina');
        });
    }

    public function down()
    {
        Schema::table('penyakits', function (Blueprint $table) {
            $table->dropColumn([
                'kategori',
                'tingkat_keparahan', 
                'pencegahan',
                'penanganan_medis',
                'masa_karantina',
                'status'
            ]);
        });
    }
}