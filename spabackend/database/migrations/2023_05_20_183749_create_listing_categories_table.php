<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Merchant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listing_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Merchant::class);
            $table->string('name')->required();
            $table->string('category');
            $table->string('description', 1000)->nullable();
            $table->string('address', 1000);
            $table->string('city', 150);
            $table->string('listing_photos', 1000)->nullable();
            // $table->foreignId('merchant_id')
            //     ->constrained(table: 'merchants', indexName: 'idx_merchant_id')
            //     ->onDelete('cascade');
            $table->timestamps();

            // $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_categories');
    }
};
