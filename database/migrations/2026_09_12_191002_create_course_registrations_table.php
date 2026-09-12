<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_code')->unique()->index(); // e.g. "REG-2410-ABCD"
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_class_id')->nullable()->constrained('course_classes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->string('phone_normalized')->index();
            $table->string('email')->nullable();
            $table->string('zalo')->nullable();
            $table->string('preferred_schedule')->nullable();
            $table->string('current_level')->default('chua_biet_gi'); // chua_biet_gi, co_ban_phat_am, hsk1_2, hsk3_4, giao_tiep
            $table->text('learning_goal')->nullable();
            $table->string('status')->default('pending')->index(); // pending, contacted, consulted, deposit_paid, paid, enrolled, cancelled
            $table->string('payment_status')->default('unpaid')->index(); // unpaid, deposit, paid, refunded
            $table->string('payment_method')->default('bank_transfer'); // bank_transfer, momo, cash, other
            $table->decimal('payment_amount', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};
