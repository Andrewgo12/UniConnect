<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'diagnosis')) {
                $table->string('diagnosis')->nullable()->after('diagnosis_code');
            }
            if (!Schema::hasColumn('medical_records', 'treatment')) {
                $table->text('treatment')->nullable()->after('diagnosis');
            }
            if (!Schema::hasColumn('medical_records', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('treatment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['diagnosis', 'treatment', 'created_by']);
        });
    }
};
