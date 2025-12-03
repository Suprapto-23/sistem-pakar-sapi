<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('diagnosa_penyakit', function (Blueprint $table) {
        $table->id();
        $table->foreignId('diagnosa_id')->constrained()->onDelete('cascade');
        $table->foreignId('penyakit_id')->constrained()->onDelete('cascade');
        $table->decimal('cf_value', 5, 3)->default(0);
        $table->timestamps();
        
        $table->unique(['diagnosa_id', 'penyakit_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosa_penyakit');
    }
};
