<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->foreignId('kategori_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->string('jadwal')->nullable();
            $table->string('ruangan', 100)->nullable();
            $table->decimal('biaya', 12, 2)->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->string('foto')->nullable();
            $table->text('gambaran_event')->nullable(); // JSON stored as text
            $table->integer('total_peserta')->default(0);
            $table->string('link_navigasi', 500)->default('');
            $table->boolean('is_link_eksternal')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
