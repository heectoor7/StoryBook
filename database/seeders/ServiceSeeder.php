<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Company;
use App\Models\Category;
use Faker\Factory as Faker;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $companies = Company::all();
        $categories = Category::all();

        if ($companies->isEmpty() || $categories->isEmpty()) {
            return; // asegúrate de ejecutar Role/Company/Category seeders antes
        }

        foreach ($companies as $company) {
            $count = rand(2, 5);
            for ($i = 0; $i < $count; $i++) {
                $cat = $categories->random();

                Service::create([
                    'company_id' => $company->id,
                    'category_id' => $cat->id,
                    'name' => $faker->words(rand(2,4), true),
                    'description' => $faker->sentence(12),
                    'price' => $faker->randomFloat(2, 10, 500)
                ]);
            }
        }
    }
}
