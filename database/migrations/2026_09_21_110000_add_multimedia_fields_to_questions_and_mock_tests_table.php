<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('exam_standard', 32)->default('hsk_2_0')->after('hsk_level')->index();
            $table->string('question_type', 32)->default('multiple_choice')->after('skill_type')->index();
            $table->string('media_type', 32)->default('text')->after('question_type')->index();
            $table->string('image')->nullable()->after('audio_text');
            $table->string('image_alt')->nullable()->after('image');
            $table->json('image_set')->nullable()->after('image_alt');
        });

        Schema::table('mock_tests', function (Blueprint $table) {
            $table->string('exam_standard', 32)->default('hsk_2_0')->after('hsk_level')->index();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'exam_standard',
                'question_type',
                'media_type',
                'image',
                'image_alt',
                'image_set',
            ]);
        });

        Schema::table('mock_tests', function (Blueprint $table) {
            $table->dropColumn(['exam_standard']);
        });
    }
};
