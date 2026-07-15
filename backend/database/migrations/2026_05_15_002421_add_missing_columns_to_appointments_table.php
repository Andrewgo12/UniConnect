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
        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('is_virtual')->default(false)->after('location_type');
            $table->string('meeting_link')->nullable()->after('is_virtual');
            $table->json('reminders')->nullable()->after('meeting_link');
            $table->timestamp('completed_at')->nullable()->after('reminders');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['is_virtual', 'meeting_link', 'reminders', 'completed_at', 'cancelled_at']);
        });
    }
};
