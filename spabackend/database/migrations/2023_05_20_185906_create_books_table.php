<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BookingStatus;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->unsignedBigInteger('product_id');
            $table->string('product_name')->required();
            $table->string('product_photo')->required();
            $table->string('product_category')->nullable();
            $table->string('product_address', 1000)->required();
            $table->smallInteger('qty')->default(1);
            $table->smallInteger('number_of_guest')->default(1);
            $table->date('from')->required();
            $table->date('to')->required();
            $table->smallInteger('days')->default(1);
            $table->decimal('service_fee_rate', $precision = 8, $scale = 2)->default(0);
            $table->decimal('service_fee', $precision = 8, $scale = 2)->default(0);
            $table->decimal('rate', $precision = 8, $scale = 2)->default(0);
            $table->decimal('discount', $precision = 8, $scale = 2)->default(0);
            $table->decimal('total', $precision = 8, $scale = 2)->default(0);
            $table->enum('booking_status',[
                BookingStatus::PENDING->value,
                BookingStatus::CHECKOUT->value,
                BookingStatus::PAYMENT_SUBMITTED->value,
                BookingStatus::BOOKED->value,
                BookingStatus::CANCELLED->value,
                BookingStatus::COMPLETED->value,
                ])->default(BookingStatus::PENDING->value);
            $table->unsignedBigInteger('booked_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
