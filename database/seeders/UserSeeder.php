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

        // Crear 20 usuarios normales con nombres realistas
        $users = [
            'Ana García', 'Carlos Martínez', 'Laura Fernández', 'Javier López',
            'María Sánchez', 'Pedro Rodríguez', 'Carmen Pérez', 'Miguel González',
            'Isabel Romero', 'David Ruiz', 'Lucía Díaz', 'Antonio Moreno',
            'Sara Muñoz', 'Francisco Álvarez', 'Elena Jiménez', 'José Hernández',
            'Paula Torres', 'Manuel Ramírez', 'Cristina Vázquez', 'Alberto Castro'
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