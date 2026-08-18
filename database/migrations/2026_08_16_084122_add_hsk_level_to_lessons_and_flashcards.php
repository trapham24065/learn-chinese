<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedTinyInteger('hsk_level')->nullable()->after('difficulty')
                ->comment('HSK level 1–6, null = chưa phân loại');
        });

        Schema::table('flashcards', function (Blueprint $table) {
            $table->unsignedTinyInteger('hsk_level')->nullable()->after('is_active')
                ->comment('HSK level 1–6, null = chưa phân loại');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('hsk_level');
        });

        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropColumn('hsk_level');
        });
    }
};
