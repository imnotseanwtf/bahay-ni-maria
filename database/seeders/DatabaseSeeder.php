<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\SensorsValue;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => UserType::Admin,
            'mobile_number' => '09123456789'
        ]);

        // SensorsValue::factory(100)->create();
    }
}
