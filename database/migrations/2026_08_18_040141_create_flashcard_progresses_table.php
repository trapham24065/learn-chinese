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
        Schema::create('flashcard_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flashcard_id')->constrained()->cascadeOnDelete();
            $table->integer('repetition')->default(0);
            $table->float('ease_factor')->default(2.5);
            $table->integer('interval')->default(0);
            $table->timestamp('next_review_at')->nullable();
            $table->timestamps();

            // A user can only have one progress record per flashcard
            $table->unique(['user_id', 'flashcard_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcard_progresses');
    }
};
