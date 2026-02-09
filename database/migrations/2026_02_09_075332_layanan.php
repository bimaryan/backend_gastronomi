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
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100)->default('main'); // Pengelompokan (opsional)
            $table->string('section_key', 100)->unique(); // ID unik konten (misal: hero_title)
            $table->string('content_type', 20)->default('text'); // text, json, image
            $table->longText('content_value')->nullable(); // Isi konten
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
