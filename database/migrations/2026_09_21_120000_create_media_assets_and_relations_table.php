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
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('file_path');
            $table->string('category', 50)->index();
            $table->string('alt_text')->nullable();
            $table->json('keywords')->nullable();
            $table->string('source', 100)->default('Learn Chinese In-House');
            $table->string('license', 100)->default('CC BY-SA 4.0');
            $table->text('attribution')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('media_asset_vocabularies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_asset_id')->constrained('media_assets')->cascadeOnDelete();
            $table->foreignId('vocabulary_id')->constrained('vocabularies')->cascadeOnDelete();
            $table->string('relation_type', 30)->default('primary');
            $table->timestamps();

            $table->unique(['media_asset_id', 'vocabulary_id', 'relation_type'], 'asset_vocab_rel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_asset_vocabularies');
        Schema::dropIfExists('media_assets');
    }
};
