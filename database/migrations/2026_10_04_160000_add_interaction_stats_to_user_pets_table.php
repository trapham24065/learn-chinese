<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_pets', function (Blueprint $table) {
            $table->json('interaction_stats')->nullable()->after('learning_dna');
        });
    }

    public function down(): void
    {
        Schema::table('user_pets', function (Blueprint $table) {
            $table->dropColumn('interaction_stats');
        });
    }
};
