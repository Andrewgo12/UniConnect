<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('conversations') && ! Schema::hasColumn('conversations', 'last_message_id')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->foreignId('last_message_id')->nullable()->after('created_by')->constrained('messages')->nullOnDelete();
            });
        }

        if (Schema::hasTable('images') && ! Schema::hasColumn('images', 'created_by')) {
            Schema::table('images', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('conversations') && Schema::hasColumn('conversations', 'last_message_id')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropForeign(['last_message_id']);
                $table->dropColumn('last_message_id');
            });
        }

        if (Schema::hasTable('images') && Schema::hasColumn('images', 'created_by')) {
            Schema::table('images', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            });
        }
    }
};
