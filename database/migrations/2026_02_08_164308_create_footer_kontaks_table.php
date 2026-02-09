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
        Schema::create('footer_kontaks', function (Blueprint $table) {
            $table->id();
            $table->string('email')->default('info@gastronomirun.com');
            $table->string('phone', 100)->default('(021) 1234-5678');
            $table->text('address');
            $table->text('description')->nullable();
            $table->string('copyright_text');
            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_youtube')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_kontaks');
    }
};
