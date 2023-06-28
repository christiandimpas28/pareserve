<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PaymentStatus;
use App\Models\User;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booked_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('checkout_session', 500);
            $table->string('booking_reference')->required();
            $table->decimal('total', $precision = 8, $scale = 2);
            $table->string('payment_mode')->required();
            $table->enum('payment_status',[
                PaymentStatus::PENDING->value,
                PaymentStatus::AWAITING->value,
                PaymentStatus::DECLINED->value,
                PaymentStatus::ABANDONED->value,
                PaymentStatus::CANCELLED->value,
                PaymentStatus::COMPLETED->value,
            ])->default(PaymentStatus::PENDING->value);
            // $table->foreignId('user_id')
            //     ->constrained(table: 'users', indexName: 'idx_user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booked_summaries');
    }
};
