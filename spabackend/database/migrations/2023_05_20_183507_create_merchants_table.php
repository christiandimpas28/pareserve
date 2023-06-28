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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('name',500)->required();
            $table->string('bus_contact_name')->required();
            $table->string('bus_contact_no', 15)->nullable();
            $table->string('bus_email', 500)->required();
            $table->string('bus_address', 1000);   
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
        Schema::dropIfExists('merchants');
    }
};
