<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('first_name')->required();
            $table->string('last_name')->required();
            $table->string('id_type')->required();
            $table->string('billing_address', 1000)->required();
            $table->string('contact_no')->required(); 
            $table->string('id_photo')->required(); 
            // $table->foreignId('user_id')
            //     ->constrained(table: 'users', indexName: 'idx_user_id')
            //     ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
