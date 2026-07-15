<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('emergencies')) {
            Schema::table('emergencies', function (Blueprint $table) {
                if (! Schema::hasColumn('emergencies', 'severity')) {
                    $table->string('severity')->nullable()->after('type');
                }
                if (! Schema::hasColumn('emergencies', 'latitude')) {
                    $table->decimal('latitude', 10, 7)->nullable()->after('location');
                }
                if (! Schema::hasColumn('emergencies', 'longitude')) {
                    $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
                }
                if (! Schema::hasColumn('emergencies', 'contact_name')) {
                    $table->string('contact_name')->nullable()->after('longitude');
                }
                if (! Schema::hasColumn('emergencies', 'contact_phone')) {
                    $table->string('contact_phone')->nullable()->after('contact_name');
                }
                if (! Schema::hasColumn('emergencies', 'contact_relationship')) {
                    $table->string('contact_relationship')->nullable()->after('contact_phone');
                }
                if (! Schema::hasColumn('emergencies', 'medical_conditions')) {
                    $table->json('medical_conditions')->nullable()->after('contact_relationship');
                }
                if (! Schema::hasColumn('emergencies', 'accessibility_needs')) {
                    $table->json('accessibility_needs')->nullable()->after('medical_conditions');
                }
            });
        }

        if (Schema::hasTable('accessibility_logs') && ! Schema::hasColumn('accessibility_logs', 'error_code')) {
            Schema::table('accessibility_logs', function (Blueprint $table) {
                $table->string('error_code')->nullable()->after('error_message');
            });
        }

        if (Schema::hasTable('audio') && ! Schema::hasColumn('audio', 'priority')) {
            Schema::table('audio', function (Blueprint $table) {
                $table->string('priority')->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('emergencies')) {
            Schema::table('emergencies', function (Blueprint $table) {
                foreach ([
                    'severity', 'latitude', 'longitude', 'contact_name', 'contact_phone',
                    'contact_relationship', 'medical_conditions', 'accessibility_needs',
                ] as $col) {
                    if (Schema::hasColumn('emergencies', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('accessibility_logs') && Schema::hasColumn('accessibility_logs', 'error_code')) {
            Schema::table('accessibility_logs', function (Blueprint $table) {
                $table->dropColumn('error_code');
            });
        }

        if (Schema::hasTable('audio') && Schema::hasColumn('audio', 'priority')) {
            Schema::table('audio', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }
    }
};
