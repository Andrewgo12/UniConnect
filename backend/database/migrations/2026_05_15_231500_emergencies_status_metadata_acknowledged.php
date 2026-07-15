<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('emergencies')) {
            return;
        }

        Schema::table('emergencies', function (Blueprint $table) {
            if (! Schema::hasColumn('emergencies', 'metadata')) {
                $table->json('metadata')->nullable()->after('accessibility_needs');
            }
            if (! Schema::hasColumn('emergencies', 'acknowledged_at')) {
                $table->timestamp('acknowledged_at')->nullable()->after('resolved_at');
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE emergencies MODIFY status VARCHAR(32) NOT NULL DEFAULT 'active'");
            DB::statement('ALTER TABLE emergencies MODIFY location TEXT NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('emergencies')) {
            return;
        }

        Schema::table('emergencies', function (Blueprint $table) {
            if (Schema::hasColumn('emergencies', 'metadata')) {
                $table->dropColumn('metadata');
            }
            if (Schema::hasColumn('emergencies', 'acknowledged_at')) {
                $table->dropColumn('acknowledged_at');
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE emergencies MODIFY status ENUM('active','resolved','cancelled') NOT NULL DEFAULT 'active'");
            DB::statement('ALTER TABLE emergencies MODIFY location JSON NULL');
        }
    }
};
