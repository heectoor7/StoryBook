<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Company;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $companies = Company::all();

        if ($companies->isEmpty()) {
            return;
        }

        foreach ($companies as $company) {
            $count = rand(3, 8);
            for ($i = 0; $i < $count; $i++) {
                $isStory = $faker->boolean(20); // 20% stories
                $expiresAt = null;
                if ($isStory) {
                    $expiresAt = Carbon::now()->addDays(rand(1, 7));
                }

                Post::create([
                    'company_id' => $company->id,
                    'content' => $faker->paragraphs(rand(1,3), true),
                    'is_story' => $isStory,
                    'expires_at' => $expiresAt
                ]);
            }
        }
    }
}
