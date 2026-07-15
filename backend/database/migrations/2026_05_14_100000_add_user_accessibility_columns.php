<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('name');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('profile_type')->nullable()->after('gender');
            $table->json('accessibility_needs')->nullable()->after('profile_type');
            $table->json('accessibility_settings')->nullable()->after('accessibility_needs');
            $table->json('preferences')->nullable()->after('accessibility_settings');
            $table->boolean('is_active')->default(true)->after('preferences');
            $table->timestamp('phone_verified_at')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('phone_verified_at');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->text('two_factor_secret')->nullable()->after('last_login_ip');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'date_of_birth',
                'gender',
                'profile_type',
                'accessibility_needs',
                'accessibility_settings',
                'preferences',
                'is_active',
                'phone_verified_at',
                'last_login_at',
                'last_login_ip',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });
    }
};
