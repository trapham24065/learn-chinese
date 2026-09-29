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
        Schema::table('user_daily_progress', function (Blueprint $table) {
            $table->unsignedSmallInteger('practice_count')->default(0)->after('pinyin_count');
            $table->unsignedSmallInteger('fast_match_count')->default(0)->after('practice_count');
            $table->unsignedSmallInteger('audio_quiz_count')->default(0)->after('fast_match_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_daily_progress', function (Blueprint $table) {
            $table->dropColumn(['practice_count', 'fast_match_count', 'audio_quiz_count']);
        });
    }
};
