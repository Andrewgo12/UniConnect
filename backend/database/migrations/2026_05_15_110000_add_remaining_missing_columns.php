<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // appointments: add appointment_date alias column (tests use this name)
        if (!Schema::hasColumn('appointments', 'appointment_date')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->timestamp('appointment_date')->nullable()->after('scheduled_date');
            });
        }

        // medical_records: accessibility_data already exists — no action needed

        // sign_languages: add transcript and language columns
        Schema::table('sign_languages', function (Blueprint $table) {
            $table->text('transcript')->nullable()->after('metadata');
            $table->string('language')->default('es-CO')->after('transcript');
        });

        // messages: add deleted_at for soft deletes, voice_duration, and language
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('edited_at');
            $table->integer('voice_duration')->nullable()->after('deleted_at');
            $table->string('language')->default('es-CO')->after('voice_duration');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('appointment_date');
        });
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('accessibility_data');
        });
        Schema::table('sign_languages', function (Blueprint $table) {
            $table->dropColumn(['transcript', 'language']);
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
