<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            FollowerSeeder::class,
            CategorySeeder::class,
            ServiceSeeder::class,
            PostSeeder::class,
            BookingSeeder::class,
            CommentSeeder::class,
            RatingSeeder::class,
            ScheduleSeeder::class
        ]);
    }
}
