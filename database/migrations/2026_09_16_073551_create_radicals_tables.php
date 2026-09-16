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
        Schema::create('radicals', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('radical_number')->comment('1 to 214 Kangxi radical index');
            $table->string('character', 10)->comment('Canonical Kangxi radical character');
            $table->json('variants')->nullable()->comment('Variant forms array, e.g. ["亻"]');
            $table->string('display_character', 10)->comment('Primary display character, e.g. 亻');
            $table->string('slug', 80)->unique();
            $table->string('name_vi', 100)->comment('Sino-Vietnamese name, e.g. Nhân đứng');
            $table->string('pinyin', 50);
            $table->text('meaning_vi');
            $table->unsignedTinyInteger('stroke_count');
            $table->string('position', 30)->default('other')->comment('left, right, top, bottom, surround, inside, other, standalone');
            $table->string('position_desc', 100)->nullable()->comment('Vietnamese position description');
            $table->text('description')->nullable()->comment('Academic etymological notes');
            $table->text('mnemonic')->nullable()->comment('Memory association tip');
            $table->boolean('is_common')->default(false);
            $table->unsignedSmallInteger('common_rank')->nullable()->comment('1 to 100 priority rank');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['stroke_count', 'sort_order']);
            $table->index(['is_common', 'common_rank']);
            $table->index('radical_number');
        });

        Schema::create('radical_characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radical_id')->constrained('radicals')->cascadeOnDelete();
            $table->string('character', 10)->index()->comment('Single Hanzi character belonging to this radical');
            $table->string('pinyin', 50);
            $table->string('meaning_vi', 255);
            $table->unsignedTinyInteger('hsk_level')->nullable()->index();
            $table->foreignId('flashcard_id')->nullable()->constrained('flashcards')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['radical_id', 'is_featured', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radical_characters');
        Schema::dropIfExists('radicals');
    }
};
