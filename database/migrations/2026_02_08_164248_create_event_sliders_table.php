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
        Schema::create('event_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->text('description')->nullable();
            $table->integer('order_position')->default(0);
            $table->boolean('is_active')->default(1);
            $table->string('orientation', 20)->nullable();
            $table->integer('image_width')->nullable();
            $table->integer('image_height')->nullable();
            $table->string('crop_mode', 20)->default('smart');
            $table->boolean('processed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sliders');
    }
};
