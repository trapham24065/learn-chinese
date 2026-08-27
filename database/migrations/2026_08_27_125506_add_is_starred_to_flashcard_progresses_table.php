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
        Schema::table('flashcard_progresses', function (Blueprint $table) {
            $table->boolean('is_starred')->default(false)->after('next_review_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flashcard_progresses', function (Blueprint $table) {
            $table->dropColumn('is_starred');
        });
    }
};
