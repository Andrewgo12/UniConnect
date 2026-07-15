<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'voice_duration')) {
                $table->integer('voice_duration')->nullable()->after('deleted_at');
            }
            if (!Schema::hasColumn('messages', 'language')) {
                $table->string('language')->default('es-CO')->after('voice_duration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['voice_duration', 'language']);
        });
    }
};
