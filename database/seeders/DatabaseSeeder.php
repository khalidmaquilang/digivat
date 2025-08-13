<?php

namespace Database\Seeders;

use Features\User\Models\User;
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
            'first_name' => 'Test User',
            'last_name' => 'Test',
            'tin_number' => '1234567890',
            'email' => 'aa@aa.com',
            'password' => bcrypt('123123123'),
        ]);
    }
}
