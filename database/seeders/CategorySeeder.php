<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Plumbing', 'Electrical', 'Cleaning', 'IT Support', 'Design', 'Marketing'];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
