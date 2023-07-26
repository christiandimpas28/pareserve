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
        Schema::create('product_report', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Books::class);
            $table->unsignedBigInteger('product_id');
            $table->string('photos', 2000)->nullable();
            $table->text('remarks');
            $table->string('related_to')->nullable();
            $table->unsignedBigInteger('case_id')->nullable();
            $table->unsignedBigInteger('listing_category_id')->nullable();
            $table->tinyInteger('settled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_report');
    }
};
