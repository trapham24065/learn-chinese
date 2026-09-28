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
        // 1. Learning activities log (with idempotency support)
        Schema::create('learning_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type', 50)->index();
            $table->string('source_type', 50)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedSmallInteger('xp_earned')->default(0);
            $table->string('idempotency_key', 120)->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();

            $table->index(['user_id', 'activity_type']);
            $table->index(['user_id', 'created_at']);
        });

        // 2. User learning overall stats (fast cache-like aggregate per user)
        Schema::create('user_learning_stats', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_xp')->default(0);
            $table->unsignedSmallInteger('current_streak')->default(0);
            $table->unsignedSmallInteger('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });

        // 3. User daily progress (date-based metrics for daily goals)
        Schema::create('user_daily_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->unsignedSmallInteger('flashcards_count')->default(0);
            $table->unsignedSmallInteger('quiz_count')->default(0);
            $table->unsignedSmallInteger('reading_minutes')->default(0);
            $table->unsignedSmallInteger('pinyin_count')->default(0);
            $table->unsignedSmallInteger('xp_earned')->default(0);
            $table->boolean('is_goal_completed')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_daily_progress');
        Schema::dropIfExists('user_learning_stats');
        Schema::dropIfExists('learning_activities');
    }
};
