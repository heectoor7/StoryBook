<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Hair Salon',
            'Auto Repair',
            'Restaurant',
            'Gym',
            'Veterinary',
            'Bakery',
            'Bookstore',
            'Florist',
            'Photography',
            'Spa & Wellness'
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
