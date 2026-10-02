<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_pets', function (Blueprint $table) {
            $table->json('learning_dna')->nullable()->after('affinity');
        });

        Schema::create('pet_world_objects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_pet_id')->constrained('user_pets')->cascadeOnDelete();
            $table->foreignId('flashcard_id')->nullable()->constrained('flashcards')->nullOnDelete();
            $table->string('hanzi', 20);
            $table->string('object_type', 50);
            $table->string('emoji', 10);
            $table->string('object_key', 100)->unique();
            $table->unsignedTinyInteger('pos_x')->default(50);
            $table->unsignedTinyInteger('pos_y')->default(50);
            $table->unsignedTinyInteger('size')->default(3);
            $table->boolean('is_visible')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_pet_id', 'object_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_world_objects');
        Schema::table('user_pets', function (Blueprint $table) {
            $table->dropColumn('learning_dna');
        });
    }
};
