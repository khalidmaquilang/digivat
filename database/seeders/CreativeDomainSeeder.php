<?php

namespace Database\Seeders;

use App\Features\CreativeDomain\Models\CreativeDomain;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreativeDomainSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $creative_domains = [
            'Audiovisual Media',
            'Digital Interactive Media',
            'Creative Services',
            'Design',
            'Publishing and Printed Media',
            'Performing Arts',
            'Visual Arts',
            'Traditional Cultural Expressions',
            'Cultural Sites',
        ];

        foreach ($creative_domains as $creative_domain) {
            CreativeDomain::create([
                'name' => $creative_domain,
            ]);
        }
    }
}
