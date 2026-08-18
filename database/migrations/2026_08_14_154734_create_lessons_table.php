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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('summary');
            $table->enum('difficulty', ['starter', 'intermediate', 'advanced'])->default('starter');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedSmallInteger('estimated_minutes')->default(10);
            $table->string('accent_color', 20)->default('#991b1b');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
