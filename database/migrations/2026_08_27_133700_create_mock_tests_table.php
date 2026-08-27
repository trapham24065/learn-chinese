<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('hsk_level')->index();
            $table->string('title');
            $table->unsignedSmallInteger('total_questions');
            $table->unsignedSmallInteger('correct_answers');
            $table->unsignedSmallInteger('total_score'); // Standard HSK scale: 0-300
            $table->unsignedSmallInteger('max_score')->default(300);
            $table->unsignedSmallInteger('listening_score')->default(0);
            $table->unsignedSmallInteger('reading_score')->default(0);
            $table->unsignedSmallInteger('grammar_score')->default(0);
            $table->unsignedSmallInteger('listening_total')->default(0);
            $table->unsignedSmallInteger('listening_correct')->default(0);
            $table->unsignedSmallInteger('reading_total')->default(0);
            $table->unsignedSmallInteger('reading_correct')->default(0);
            $table->unsignedSmallInteger('grammar_total')->default(0);
            $table->unsignedSmallInteger('grammar_correct')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedSmallInteger('time_limit_minutes')->default(20);
            $table->boolean('passed')->default(false)->index();
            $table->string('certificate_code')->nullable()->unique();
            $table->json('details')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_tests');
    }
};
