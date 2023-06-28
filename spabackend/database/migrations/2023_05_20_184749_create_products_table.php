<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ListingCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ListingCategory::class);
            $table->string('name')->required();
            $table->string('slug');
            $table->string('description', 5000)->nullable();
            $table->tinyInteger('max_guest');
            $table->string('photos', 2000)->nullable();
            $table->tinyInteger('enabled')->default(1);
            $table->decimal('rate', $precision = 8, $scale = 2);
            $table->decimal('discount', $precision = 8, $scale = 2)->default(0.0);
            // $table->foreignId('listing_category_id')
            //     ->constrained(table: 'listing_categories', indexName: 'idx_listing_category_id')
            //     ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
