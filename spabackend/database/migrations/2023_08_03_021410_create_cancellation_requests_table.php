<?php

use App\Models\User;
use App\Models\Books;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cancellation_request', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Books::class);
            $table->foreignIdFor(User::class);
            $table->text('remarks');
            $table->string('request_status', 1000)->nullable();
            $table->tinyInteger('refunded')->default(0);
            $table->decimal('refunded_amount', $precision = 8, $scale = 2)->default(0);
            $table->timestamp('refunded_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellation_request');
    }
};
