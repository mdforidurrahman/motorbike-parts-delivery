<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Engine Parts', 'description' => 'Engine components and parts'],
            ['name' => 'Brake System', 'description' => 'Brake pads, discs, and calipers'],
            ['name' => 'Electrical Parts', 'description' => 'Batteries, lights, and wiring'],
            ['name' => 'Body Parts', 'description' => 'Fairings, mirrors, and panels'],
            ['name' => 'Suspension', 'description' => 'Forks, shocks, and springs'],
            ['name' => 'Tires & Wheels', 'description' => 'Tires, rims, and tubes'],
            ['name' => 'Transmission', 'description' => 'Chains, sprockets, and gears'],
            ['name' => 'Exhaust System', 'description' => 'Mufflers and exhaust pipes'],
        ];
        
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}