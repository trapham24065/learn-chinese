<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_registration_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_registration_id')->constrained('course_registrations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // admin/staff who performed action
            $table->string('type')->index(); // created, status_changed, payment_updated, call, zalo_sent, note_added, meet_sent
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_registration_activities');
    }
};
