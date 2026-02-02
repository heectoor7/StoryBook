<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\User;
use App\Models\Service;

class RatingSeeder extends Seeder
{
    public function run()
    {
        // Solo usuarios normales pueden valorar (no admin ni empresas)
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'user');
        })->whereDoesntHave('roles', function($q) {
            $q->where('name', 'admin');
        })->get();
        
        $services = Service::all();

        if ($users->isEmpty() || $services->isEmpty()) {
            return;
        }

        // Comentarios por rating
        $comments = [
            5 => [
                'Excellent service! Highly recommended.',
                'Amazing experience, will definitely come back!',
                'Outstanding quality and great customer service.',
                'Perfect! Exceeded all my expectations.',
                'Best service I\'ve ever had. Five stars!',
                'Absolutely wonderful! Couldn\'t be happier.',
                'Top-notch service from start to finish.',
                'Incredible experience, worth every penny!'
            ],
            4 => [
                'Very good service, just minor details to improve.',
                'Great experience overall, really satisfied.',
                'Good quality and professional service.',
                'Pleased with the service, would recommend.',
                'Solid performance, very happy with the results.',
                'Really good, just a small room for improvement.',
                'Excellent work, minor issues but nothing major.'
            ],
            3 => [
                'Decent service, met my expectations.',
                'It was okay, nothing special but adequate.',
                'Average experience, could be better.',
                'Fair service, some things could improve.',
                'Acceptable, but there\'s room for enhancement.',
                'Not bad, but not exceptional either.'
            ],
            2 => [
                'Below expectations, several issues.',
                'Not very satisfied, needs improvement.',
                'Disappointed with some aspects of the service.',
                'Could be much better, had some problems.',
                'Service was lacking in several areas.'
            ],
            1 => [
                'Very disappointed, would not recommend.',
                'Poor service, many issues encountered.',
                'Not satisfied at all with the experience.',
                'Terrible experience, needs major improvements.',
                'Would not use this service again.'
            ]
        ];

        foreach ($users as $user) {
            // Cada usuario valora entre 3 y 8 servicios
            $count = rand(3, min(8, $services->count()));
            $sample = $services->random($count);
            
            foreach ($sample as $service) {
                // Evitar duplicados por unique constraint
                if (Rating::where('user_id', $user->id)->where('service_id', $service->id)->exists()) {
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

                // 70% de probabilidad de tener comentario
                $comment = rand(1, 100) <= 70 
                    ? $comments[$rating][array_rand($comments[$rating])]
                    : null;

                Rating::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'created_at' => now()->subDays(rand(1, 90))
                ]);
            }
        }
    }
}
