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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_pinyin')->nullable();
            $table->string('title_vi');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('hsk_level')->default(1);
            $table->string('category')->default('Đời sống');
            $table->string('cover_color')->default('#991b1b');
            $table->text('summary')->nullable();
            $table->json('content_json');
            $table->json('quiz_json')->nullable();
            $table->unsignedInteger('word_count')->default(0);
            $table->unsignedSmallInteger('estimated_reading_minutes')->default(3);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['hsk_level', 'is_published']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
