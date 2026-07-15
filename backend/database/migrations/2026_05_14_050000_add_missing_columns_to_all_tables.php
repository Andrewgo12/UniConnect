<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // conversations
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title')->nullable();
            $table->string('type')->default('text');
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->json('settings')->nullable();
        });

        // conversation_participants pivot
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member');
            $table->json('permissions')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            $table->unique(['conversation_id', 'user_id']);
        });

        // messages - add missing columns
        Schema::table('messages', function (Blueprint $table) {
            $table->string('status')->default('sent')->after('type');
            $table->string('priority')->default('medium')->after('status');
            $table->foreignId('parent_id')->nullable()->constrained('messages')->onDelete('set null')->after('priority');
            $table->json('accessibility_data')->nullable()->after('metadata');
            $table->boolean('is_edited')->default(false)->after('accessibility_data');
            $table->boolean('is_deleted')->default(false)->after('is_edited');
            $table->boolean('is_pinned')->default(false)->after('is_deleted');
            $table->timestamp('edited_at')->nullable()->after('is_pinned');
        });

        // sign_languages
        Schema::table('sign_languages', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('difficulty_level')->default('beginner');
            $table->string('region')->default('colombian');
            $table->string('video_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->integer('duration')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->integer('usage_count')->default(0);
            $table->json('metadata')->nullable();
        });

        // audio
        Schema::table('audio', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('speech');
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->nullable();
            $table->integer('duration')->nullable();
            $table->text('transcript')->nullable();
            $table->string('language')->default('es-CO');
            $table->string('quality')->default('medium');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_processed')->default(false);
            $table->json('metadata')->nullable();
        });

        // images
        Schema::table('images', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('general');
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->integer('usage_count')->default(0);
            $table->json('metadata')->nullable();
        });

        // medical_records
        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('patient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('severity')->nullable();
            $table->string('status')->default('active');
            $table->string('diagnosis_code')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->json('medications')->nullable();
            $table->json('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('follow_up_date')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->boolean('is_emergency')->default(false);
            $table->json('metadata')->nullable();
        });

        // medications
        Schema::table('medications', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('medical_record_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('brand_name')->nullable();
            $table->string('generic_name')->nullable();
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('route')->nullable();
            $table->string('strength')->nullable();
            $table->string('unit')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('refills')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('time_of_day')->nullable();
            $table->string('with_food')->nullable();
            $table->text('instructions')->nullable();
            $table->json('side_effects')->nullable();
            $table->json('contraindications')->nullable();
            $table->json('interactions')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_prn')->default(false);
            $table->boolean('is_controlled')->default(false);
            $table->boolean('is_emergency_medication')->default(false);
            $table->string('pharmacy')->nullable();
            $table->string('prescription_number')->nullable();
            $table->json('metadata')->nullable();
        });

        // appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('patient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('medical_record_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('status')->default('scheduled');
            $table->string('priority')->default('medium');
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('scheduled_time')->nullable();
            $table->integer('duration')->default(60);
            $table->string('location')->nullable();
            $table->string('location_type')->default('physical');
            $table->string('virtual_meeting_url')->nullable();
            $table->string('virtual_meeting_id')->nullable();
            $table->text('notes')->nullable();
            $table->text('preparation_instructions')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('rescheduling_notes')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->boolean('is_emergency')->default(false);
            $table->json('metadata')->nullable();
        });

        // analytics
        Schema::table('analytics', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type')->nullable();
            $table->string('category')->nullable();
            $table->string('action')->nullable();
            $table->string('resource_type')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('device_type')->nullable();
            $table->string('platform')->nullable();
            $table->string('language')->nullable();
            $table->string('accessibility_mode')->nullable();
            $table->integer('response_time')->nullable();
            $table->string('error_code')->nullable();
            $table->boolean('success')->default(true);
            $table->string('location')->nullable();
            $table->string('timezone')->nullable();
        });

        // accessibility_logs
        Schema::table('accessibility_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('loggable_type')->nullable();
            $table->unsignedBigInteger('loggable_id')->nullable();
            $table->string('action')->nullable();
            $table->string('feature')->nullable();
            $table->string('accessibility_mode')->nullable();
            $table->string('device_type')->nullable();
            $table->string('input_method')->nullable();
            $table->string('assistive_technology')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->json('context')->nullable();
            $table->string('previous_mode')->nullable();
            $table->string('new_mode')->nullable();
            $table->json('metadata')->nullable();
        });

        // security_logs
        Schema::table('security_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type')->nullable();
            $table->string('severity')->default('low');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->string('location')->nullable();
            $table->boolean('success')->default(true);
            $table->text('failure_reason')->nullable();
            $table->string('resource_type')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('action')->nullable();
            $table->json('previous_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('session_id')->nullable();
            $table->string('request_id')->nullable();
            $table->json('metadata')->nullable();
        });

        // system_logs
        Schema::table('system_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('level')->default('info');
            $table->text('message')->nullable();
            $table->json('context')->nullable();
            $table->string('channel')->nullable();
            $table->string('component')->nullable();
            $table->string('action')->nullable();
            $table->string('resource_type')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('request_id')->nullable();
            $table->bigInteger('memory_usage')->nullable();
            $table->float('execution_time')->nullable();
            $table->text('stack_trace')->nullable();
            $table->string('exception_class')->nullable();
            $table->string('file')->nullable();
            $table->integer('line')->nullable();
            $table->json('metadata')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};
