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
        Schema::create('hsk_standards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('version', 20);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('vocabularies', function (Blueprint $table) {
            $table->id();
            $table->string('hanzi', 100)->index();
            $table->string('pinyin', 100)->index();
            $table->text('meaning');
            $table->string('simplified', 100)->nullable();
            $table->string('traditional', 100)->nullable();
            $table->string('part_of_speech', 50)->nullable();
            $table->text('example')->nullable();
            $table->string('example_pinyin')->nullable();
            $table->text('example_meaning')->nullable();
            $table->string('audio_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vocabulary_hsk_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vocabulary_id')->constrained('vocabularies')->cascadeOnDelete();
            $table->foreignId('hsk_standard_id')->constrained('hsk_standards')->cascadeOnDelete();
            $table->unsignedTinyInteger('level');
            $table->string('source', 100)->nullable();
            $table->string('topic', 100)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['vocabulary_id', 'hsk_standard_id']);
            $table->index(['hsk_standard_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_hsk_levels');
        Schema::dropIfExists('vocabularies');
        Schema::dropIfExists('hsk_standards');
    }
};
