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
        Schema::table('user_pets', function (Blueprint $table) {
            $table->enum('personality', ['playful', 'curious', 'shy', 'cheerful', 'calm'])
                ->default('playful')
                ->after('status');
            $table->smallInteger('affinity')->default(0)->after('personality');
            $table->timestamp('last_studied_at')->nullable()->after('last_fed_at');
            $table->integer('study_session_count')->default(0)->after('last_studied_at');
        });

        Schema::table('pet_memories', function (Blueprint $table) {
            $table->string('memory_key', 100)->nullable()->index()->after('type');
            $table->tinyInteger('importance')->default(50)->after('description');
            $table->foreignId('related_word_id')->nullable()->after('importance')->constrained('flashcards')->nullOnDelete();
            $table->timestamp('last_recalled_at')->nullable()->after('metadata');
            $table->integer('recall_count')->default(0)->after('last_recalled_at');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pet_memories', function (Blueprint $table) {
            $table->dropForeign(['related_word_id']);
            $table->dropColumn([
                'memory_key',
                'importance',
                'related_word_id',
                'last_recalled_at',
                'recall_count',
                'updated_at',
            ]);
        });

        Schema::table('user_pets', function (Blueprint $table) {
            $table->dropColumn([
                'personality',
                'affinity',
                'last_studied_at',
                'study_session_count',
            ]);
        });
    }
};
