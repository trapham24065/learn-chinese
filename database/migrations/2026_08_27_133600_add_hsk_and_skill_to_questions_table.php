<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedTinyInteger('hsk_level')->default(1)->after('lesson_id')->index();
            $table->string('skill_type')->default('reading')->after('difficulty')->index(); // listening, reading, grammar
            $table->text('audio_text')->nullable()->after('pinyin');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['hsk_level', 'skill_type', 'audio_text']);
        });
    }
};
