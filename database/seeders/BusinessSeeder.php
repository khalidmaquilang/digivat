<?php

namespace Database\Seeders;

use App\Features\Business\Models\Business;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Features\User\Models\User;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'first_name' => 'Romeo',
            'middle_name' => 'D.',
            'last_name' => 'Lumagui',
            'suffix' => 'Jr.',
            'email' => 'aa@aa.com',
            'password' => bcrypt('123123123'),
            'email_verified_at' => now(),
        ]);
        Business::create([
            'owner_id' => $user->id,
            'name' => 'BIR',
            'slug' => 'bir',
            'tin_number' => '1234567890',
        ]);

        $user = User::create([
            'first_name' => 'Nong',
            'last_name' => 'Rangasa',
            'email' => 'bb@bb.com',
            'password' => bcrypt('123123123'),
            'email_verified_at' => now(),
        ]);
        Business::create([
            'owner_id' => $user->id,
            'name' => 'LCCAD',
            'slug' => 'lccad',
            'tin_number' => '1234567890',
        ]);

        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'cc@cc.com',
            'password' => bcrypt('123123123'),
            'email_verified_at' => now(),
        ]);
        Business::create([
            'owner_id' => $user->id,
            'name' => 'Test Company',
            'slug' => 'test-company',
            'tin_number' => '1234567890',
        ]);
    }
}
