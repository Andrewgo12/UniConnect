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
        Schema::create('phrases', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('icon')->nullable();
            $table->json('vibration_pattern'); // array of vibration durations
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->string('category')->default('general'); // 'general', 'emergency', 'medical'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phrases');
    }
};
