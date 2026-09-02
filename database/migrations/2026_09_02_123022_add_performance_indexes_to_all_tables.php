<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * C7: Add missing database indexes for frequently queried columns.
 * Resolves full-table scans on key tables, improving TTFB significantly.
 */
return new class extends Migration
{
    public function up(): void
    {
        // flashcards: hanzi AJAX dictionary lookup + level/status/order filter
        Schema::table('flashcards', function (Blueprint $table) {
            $table->index('hanzi');
            $table->index(['hsk_level', 'is_active', 'sort_order']);
        });

        // study_sessions: streak calculation + per-user activity feed
        Schema::table('study_sessions', function (Blueprint $table) {
            $table->index(['user_id', 'session_type', 'completed_at']);
            $table->index(['user_id', 'completed_at']);
        });

        // flashcard_progresses: SRS due-card dashboard + flashcard index queries
        Schema::table('flashcard_progresses', function (Blueprint $table) {
            $table->index(['user_id', 'next_review_at']);
        });

        // lessons: HSK roadmap listing filtered by published status
        Schema::table('lessons', function (Blueprint $table) {
            $table->index(['is_published', 'hsk_level', 'sort_order']);
        });

        // questions: mock test + quiz pool filtered by level and skill type
        Schema::table('questions', function (Blueprint $table) {
            $table->index(['is_active', 'hsk_level', 'skill_type']);
        });

        // lesson_progresses: count completed lessons per user
        Schema::table('lesson_progresses', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropIndex(['hanzi']);
            $table->dropIndex(['hsk_level', 'is_active', 'sort_order']);
        });

        Schema::table('study_sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'session_type', 'completed_at']);
            $table->dropIndex(['user_id', 'completed_at']);
        });

        Schema::table('flashcard_progresses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'next_review_at']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'hsk_level', 'sort_order']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'hsk_level', 'skill_type']);
        });

        Schema::table('lesson_progresses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
        });
    }
};
