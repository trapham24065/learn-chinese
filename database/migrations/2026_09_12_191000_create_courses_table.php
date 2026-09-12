<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('hsk_starter')->index();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedTinyInteger('duration_weeks')->default(8);
            $table->unsignedSmallInteger('total_sessions')->default(24);
            $table->json('highlights')->nullable();
            $table->json('curriculum')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
