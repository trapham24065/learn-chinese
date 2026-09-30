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
        // 1. Guest progress aggregate record (identifies guest, aggregate XP, claim status)
        Schema::create('guest_progress', function (Blueprint $table) {
            $table->id();
            $table->uuid('guest_uuid')->unique();
            $table->unsignedInteger('total_xp')->default(0);
            $table->unsignedSmallInteger('activities_count')->default(0);
            $table->string('status', 20)->default('pending')->index(); // pending | claimed | expired
            $table->foreignId('claimed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
        });

        // 2. Guest activities ledger (individual verified learning actions completed by guest)
        Schema::create('guest_activities', function (Blueprint $table) {
            $table->id();
            $table->uuid('guest_uuid')->index();
            $table->string('activity_type', 50)->index();
            $table->string('source_type', 50)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedSmallInteger('xp_earned')->default(0);
            $table->string('idempotency_key', 120)->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->unique(['guest_uuid', 'idempotency_key']);
            $table->foreign('guest_uuid')->references('guest_uuid')->on('guest_progress')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_activities');
        Schema::dropIfExists('guest_progress');
    }
};
