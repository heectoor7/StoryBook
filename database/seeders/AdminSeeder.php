<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Crear admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@storybook.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('123456') // cambiar si quieres
            ]
        );

        // Asignarle todos los roles
        $roles = Role::all();
        $admin->roles()->sync($roles->pluck('id'));
    }
}