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
        Schema::table('duel_rooms', function (Blueprint $table) {
            $table->string('player1_ip', 45)->nullable()->after('player1_last_seen_at');
            $table->string('player2_ip', 45)->nullable()->after('player2_last_seen_at');
            $table->boolean('is_same_ip')->default(false)->after('player2_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duel_rooms', function (Blueprint $table) {
            $table->dropColumn(['player1_ip', 'player2_ip', 'is_same_ip']);
        });
    }
};
