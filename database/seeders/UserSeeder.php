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

        // Crear 10 usuarios normales de ejemplo
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => "Usuario $i",
                'email' => "user$i@storybook.com",
                'password' => bcrypt('123456')
            ]);

            $user->roles()->attach($userRole->id);
        }
    }
}