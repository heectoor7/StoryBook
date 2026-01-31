<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\User;
use App\Models\Company;

class RatingSeeder extends Seeder
{
    public function run()
    {
        // Solo usuarios normales pueden valorar (no admin ni empresas)
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'user');
        })->get();
        
        $companies = Company::all();

        if ($users->isEmpty() || $companies->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            // Cada usuario valora entre 2 y 6 empresas
            $count = rand(2, min(6, $companies->count()));
            $sample = $companies->random($count);
            
            foreach ($sample as $company) {
                // Evitar duplicados por unique constraint
                if (Rating::where('user_id', $user->id)->where('company_id', $company->id)->exists()) {
                    continue;
                }

                // Distribución realista de ratings (más 4 y 5 estrellas)
                $ratingValue = rand(1, 100);
                if ($ratingValue <= 50) {
                    $rating = 5;
                } elseif ($ratingValue <= 80) {
                    $rating = 4;
                } elseif ($ratingValue <= 90) {
                    $rating = 3;
                } elseif ($ratingValue <= 95) {
                    $rating = 2;
                } else {
                    $rating = 1;
                }

                Rating::create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'rating' => $rating
                ]);
            }
        }
    }
}
