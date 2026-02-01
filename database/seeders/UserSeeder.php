<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userRole = Role::where('name', 'user')->first();

        // Create 20 regular users with realistic names
        $users = [
            'Emma Johnson', 'Michael Smith', 'Olivia Williams', 'James Brown',
            'Sophia Jones', 'William Davis', 'Ava Miller', 'Robert Wilson',
            'Isabella Moore', 'David Taylor', 'Mia Anderson', 'John Thomas',
            'Charlotte Jackson', 'Daniel White', 'Amelia Harris', 'Joseph Martin',
            'Emily Thompson', 'Matthew Garcia', 'Harper Martinez', 'Christopher Robinson'
        ];

        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'name' => $users[$i - 1],
                'email' => "user$i@storybook.com",
                'password' => bcrypt('123456')
            ]);

            $user->roles()->attach($userRole->id);
        }
    }
}