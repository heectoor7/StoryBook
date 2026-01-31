<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Follower;
use App\Models\User;
use App\Models\Company;

class FollowerSeeder extends Seeder
{
    public function run()
    {
        $companies = Company::all();
        if ($companies->isEmpty()) {
            return;
        }

        // Seleccionar usuarios con rol 'user' (excluir admin)
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'user');
        })->get();

        foreach ($users as $user) {
            // Cada usuario seguirá entre 3 y 7 empresas aleatorias
            $count = rand(3, min(7, $companies->count()));
            $targets = $companies->random($count);

            foreach ($targets as $company) {
                Follower::firstOrCreate([
                    'user_id' => $user->id,
                    'company_id' => $company->id
                ]);
            }
        }
    }
}
