<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('priority')->default('medium')->after('status');
            $table->string('category')->default('general')->after('priority');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            $table->timestamp('closed_at')->nullable()->after('closed_by');
            $table->boolean('is_public')->default(false)->after('closed_at');
            $table->boolean('is_pinned')->default(false)->after('is_public');
            $table->boolean('is_muted')->default(false)->after('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['priority', 'category', 'closed_by', 'closed_at', 'is_public', 'is_pinned', 'is_muted']);
        });
    }
};
