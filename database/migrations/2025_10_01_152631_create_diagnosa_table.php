<?php
// database/migrations/2024_01_01_000004_create_diagnosa_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::create('diagnosas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->json('gejala_terpilih');
        $table->json('hasil_perhitungan');
        $table->string('penyakit_tertinggi');
        $table->decimal('cf_tertinggi', 5, 2);
        $table->string('pdf_path')->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('diagnosa');
    }
};