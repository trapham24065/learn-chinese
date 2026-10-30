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
        Schema::create('duel_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_code', 8)->unique();
            $table->unsignedTinyInteger('hsk_level');
            $table->enum('mode', ['random', 'friend'])->default('random');
            $table->enum('status', ['waiting', 'countdown', 'in_progress', 'finished', 'abandoned'])->default('waiting');

            // Rematch relationship
            $table->foreignId('parent_room_id')->nullable()->constrained('duel_rooms')->nullOnDelete();
            $table->foreignId('rematch_room_id')->nullable()->constrained('duel_rooms')->nullOnDelete();
            $table->unsignedTinyInteger('rematch_requested_by')->nullable();
            $table->boolean('rematch_rejected')->default(false);

            // Player 1 (Slot 1)
            $table->enum('player1_type', ['user', 'guest']);
            $table->foreignId('player1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('player1_guest_token_hash', 64)->nullable();
            $table->string('player1_name', 100);
            $table->string('player1_avatar', 255)->nullable();
            $table->boolean('player1_ready')->default(false);
            $table->unsignedInteger('player1_score')->default(0);
            $table->unsignedTinyInteger('player1_correct_count')->default(0);
            $table->decimal('player1_total_correct_time', 8, 4)->default(0.0000);
            $table->decimal('player1_last_seen_at', 14, 4)->nullable();

            // Player 2 (Slot 2)
            $table->enum('player2_type', ['user', 'guest'])->nullable();
            $table->foreignId('player2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('player2_guest_token_hash', 64)->nullable();
            $table->string('player2_name', 100)->nullable();
            $table->string('player2_avatar', 255)->nullable();
            $table->boolean('player2_ready')->default(false);
            $table->unsignedInteger('player2_score')->default(0);
            $table->unsignedTinyInteger('player2_correct_count')->default(0);
            $table->decimal('player2_total_correct_time', 8, 4)->default(0.0000);
            $table->decimal('player2_last_seen_at', 14, 4)->nullable();

            // Match progress
            $table->unsignedTinyInteger('current_round')->default(1);
            $table->enum('round_phase', ['answering', 'revealing'])->nullable();
            $table->decimal('phase_started_at', 14, 4)->nullable();
            $table->decimal('phase_ends_at', 14, 4)->nullable();

            // Question snapshot (10 questions CLEC metadata)
            $table->json('questions_snapshot')->nullable();

            // Outcomes & XP
            $table->unsignedTinyInteger('winner_slot')->nullable();
            $table->boolean('is_draw')->default(false);
            $table->unsignedTinyInteger('forfeit_by_slot')->nullable();
            $table->boolean('xp_awarded')->default(false);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['status', 'hsk_level']);
            $table->index('room_code');
            $table->index(['player1_id', 'player1_guest_token_hash']);
            $table->index(['player2_id', 'player2_guest_token_hash']);
            $table->index(['status', 'phase_ends_at']);
        });

        Schema::create('duel_round_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duel_room_id')->constrained('duel_rooms')->cascadeOnDelete();
            $table->unsignedTinyInteger('round_number');
            $table->unsignedTinyInteger('player_slot');
            $table->foreignId('question_id')->nullable()->constrained('questions')->nullOnDelete();

            $table->string('selected_answer', 255)->nullable(); // null represents Timeout
            $table->boolean('is_correct')->default(false);
            $table->decimal('answer_time_seconds', 6, 4)->default(10.0000);
            $table->unsignedInteger('score_awarded')->default(0);

            $table->string('idempotency_key', 64)->nullable();
            $table->decimal('answered_at', 14, 4);
            $table->timestamps();

            // Strict unique constraint: One answer per player per round per room
            $table->unique(['duel_room_id', 'round_number', 'player_slot'], 'uq_room_round_slot');
            $table->index(['duel_room_id', 'round_number']);
            $table->index(['duel_room_id', 'player_slot', 'idempotency_key'], 'idx_room_slot_idempotency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duel_round_answers');
        Schema::dropIfExists('duel_rooms');
    }
};
