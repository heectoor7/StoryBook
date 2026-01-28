<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $companyRole = Role::where('name', 'company')->first();

        // Crear 5 empresas de ejemplo
        for ($i = 1; $i <= 5; $i++) {

            $user = User::create([
                'name' => "Empresa $i",
                'email' => "empresa$i@storybook.com",
                'password' => bcrypt('123456')
            ]);

            $user->roles()->attach($companyRole->id);

            Company::create([
                'user_id' => $user->id,
                'name' => "Empresa $i",
                'description' => "Descripción de Empresa $i",
                'address' => "Calle $i, Ciudad",
                'city' => "Ciudad $i",
                'phone' => "60000000$i",
                'verified' => true
            ]);
        }
    }
}