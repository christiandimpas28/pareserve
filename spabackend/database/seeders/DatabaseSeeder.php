<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Helpers\Otp as Otp;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $range = array(100000,999999);
        $otp = Otp::generate($range);

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@pareserve.com',
            'user_type' => 'Admin',
            'otp' => $otp,
            'otp_at' => time(),
        ]);
    }
}
