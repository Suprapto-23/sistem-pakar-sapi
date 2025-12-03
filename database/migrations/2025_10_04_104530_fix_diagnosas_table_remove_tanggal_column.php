<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hapus kolom tanggal jika ada
        if (Schema::hasColumn('diagnosas', 'tanggal')) {
            Schema::table('diagnosas', function (Blueprint $table) {
                $table->dropColumn('tanggal');
            });
        }
    }

    public function down()
    {
        Schema::table('diagnosas', function (Blueprint $table) {
            $table->timestamp('tanggal')->nullable();
        });
    }
};