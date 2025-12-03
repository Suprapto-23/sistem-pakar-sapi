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
    Schema::create('diagnosa_gejala', function (Blueprint $table) {
        $table->id();
        $table->foreignId('diagnosa_id')->constrained()->onDelete('cascade');
        $table->foreignId('gejala_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        
        $table->unique(['diagnosa_id', 'gejala_id']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosa_gejala');
    }
};
