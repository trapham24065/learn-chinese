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
        // 1. Pet species catalog
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('image_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Stage definitions per species
        Schema::create('pet_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('stage')->unsigned();
            $table->string('name');
            $table->string('emoji', 10);
            $table->integer('required_exp')->default(0);
            $table->integer('required_mastered_vocabulary')->default(0);
            $table->integer('required_used_vocabulary')->default(0);
            $table->integer('required_reading_activities')->default(0);
            $table->integer('required_listening_activities')->default(0);
            $table->tinyInteger('dialogue_level')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['pet_id', 'stage']);
        });

        // 3. User's active pet (one per user)
        Schema::create('user_pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50)->nullable();
            $table->tinyInteger('stage')->default(0);
            $table->integer('exp')->default(0);
            $table->integer('total_fed_xp')->default(0);
            $table->tinyInteger('hunger')->default(100);
            $table->enum('status', ['active', 'dormant', 'egg'])->default('active');
            $table->timestamp('last_fed_at')->nullable();
            $table->timestamp('last_hunger_calculated_at')->useCurrent();
            $table->timestamp('dormant_at')->nullable();
            $table->smallInteger('reset_count')->default(0);
            $table->tinyInteger('best_stage')->default(0);
            $table->timestamps();

            $table->unique('user_id'); // one pet per user
        });

        // 4. Immutable feeding audit log
        Schema::create('pet_feeding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('xp_amount');
            $table->smallInteger('daily_fed_before')->comment('Daily XP fed before this transaction');
            $table->string('idempotency_key', 191)->unique();
            $table->timestamp('fed_at');
            $table->timestamps();

            $table->index(['user_pet_id', 'fed_at']);
        });

        // 5. Milestone event memories (insert-only, no updates)
        Schema::create('pet_memories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_pet_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_memories');
        Schema::dropIfExists('pet_feeding_logs');
        Schema::dropIfExists('user_pets');
        Schema::dropIfExists('pet_stages');
        Schema::dropIfExists('pets');
    }
};
