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
        Schema::create('kelas_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('nama_peserta');
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas', 'Cicilan'])->default('Belum Lunas');
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->foreignId('tiket_kategori_id')->nullable()->constrained('tiket_kategoris')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_pesertas');
    }
};
