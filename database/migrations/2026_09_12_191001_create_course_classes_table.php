<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('schedule_days'); // e.g. "Thứ 2 - 4 - 6"
            $table->string('schedule_time'); // e.g. "19:30 - 21:00"
            $table->unsignedSmallInteger('max_students')->default(15);
            $table->string('status')->default('open')->index(); // draft, open, full, ongoing, completed, cancelled
            $table->string('meet_url')->nullable(); // Google Meet link (private, admin/enrolled only)
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_classes');
    }
};
