<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('accessibility_logs')) {
            Schema::table('accessibility_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('accessibility_logs', 'platform')) {
                    $table->string('platform')->nullable()->after('device_type');
                }
                if (! Schema::hasColumn('accessibility_logs', 'response_time')) {
                    $table->integer('response_time')->nullable()->after('duration');
                }
                if (! Schema::hasColumn('accessibility_logs', 'language')) {
                    $table->string('language')->nullable()->after('response_time');
                }
                if (! Schema::hasColumn('accessibility_logs', 'location')) {
                    $table->string('location')->nullable()->after('language');
                }
                if (! Schema::hasColumn('accessibility_logs', 'timezone')) {
                    $table->string('timezone')->nullable()->after('location');
                }
            });
        }

        if (Schema::hasTable('audio') && ! Schema::hasColumn('audio', 'created_by')) {
            Schema::table('audio', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('medications') && ! Schema::hasColumn('medications', 'created_by')) {
            Schema::table('medications', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->after('doctor_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('accessibility_logs')) {
            Schema::table('accessibility_logs', function (Blueprint $table) {
                foreach (['platform', 'response_time', 'language', 'location', 'timezone'] as $col) {
                    if (Schema::hasColumn('accessibility_logs', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('audio') && Schema::hasColumn('audio', 'created_by')) {
            Schema::table('audio', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            });
        }

        if (Schema::hasTable('medications') && Schema::hasColumn('medications', 'created_by')) {
            Schema::table('medications', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            });
        }
    }
};
