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

        // 10 real companies with authentic names
        $companies = [
            [
                'name' => 'Carmen\'s Hair Salon',
                'email' => 'empresa1@storybook.com',
                'description' => 'Trusted hair salon with over 20 years of experience. Specialized in modern cuts and coloring.',
                'address' => '23 Main Street',
                'city' => 'New York',
                'phone' => '212-555-0123',
                'logo' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'Pete\'s Auto Repair',
                'email' => 'empresa2@storybook.com',
                'description' => 'Auto repair shop specialized in all types of repairs and vehicle maintenance.',
                'address' => '45 Industrial Avenue',
                'city' => 'Los Angeles',
                'phone' => '310-555-0147',
                'logo' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'The Corner Restaurant',
                'email' => 'empresa3@storybook.com',
                'description' => 'Homemade Mediterranean cuisine with the finest fresh ingredients from the market.',
                'address' => '8 Constitution Plaza',
                'city' => 'Chicago',
                'phone' => '312-555-0189',
                'logo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'FitLife Gym',
                'email' => 'empresa4@storybook.com',
                'description' => 'Complete sports center with the best facilities and professional trainers.',
                'address' => '12 Sports Drive',
                'city' => 'Houston',
                'phone' => '713-555-0198',
                'logo' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'St. Francis Veterinary',
                'email' => 'empresa5@storybook.com',
                'description' => 'Veterinary clinic with 24h service. We care for your pets as if they were our own.',
                'address' => '34 Veterinary Street',
                'city' => 'Phoenix',
                'phone' => '602-555-0210',
                'logo' => 'https://images.unsplash.com/photo-1548681528-6a5c45b66b42?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'The Golden Wheat Bakery',
                'email' => 'empresa6@storybook.com',
                'description' => 'Artisan bread freshly baked daily. Homemade pastries and desserts.',
                'address' => '5 Bread Lane',
                'city' => 'Philadelphia',
                'phone' => '215-555-0852',
                'logo' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'Cervantes Bookstore',
                'email' => 'empresa7@storybook.com',
                'description' => 'Neighborhood bookstore with extensive catalog. We also sell stationery and school supplies.',
                'address' => '18 Book Street',
                'city' => 'San Antonio',
                'phone' => '210-555-0963',
                'logo' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'Garden Florist',
                'email' => 'empresa8@storybook.com',
                'description' => 'Fresh flowers for any occasion. Personalized bouquets and floral decoration.',
                'address' => '9 Flower Boulevard',
                'city' => 'San Diego',
                'phone' => '619-555-0147',
                'logo' => 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'Light Photography Studio',
                'email' => 'empresa9@storybook.com',
                'description' => 'Professional photography for events, weddings and portraits. Modern photo studio.',
                'address' => '27 Image Avenue',
                'city' => 'Dallas',
                'phone' => '214-555-0159',
                'logo' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=200&h=200&fit=crop'
            ],
            [
                'name' => 'Relax Spa Center',
                'email' => 'empresa10@storybook.com',
                'description' => 'Wellness and spa center. Massages, facial and body treatments.',
                'address' => '15 Relax Street',
                'city' => 'San Jose',
                'phone' => '408-555-0258',
                'logo' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=200&h=200&fit=crop'
            ]
        ];

        foreach ($companies as $companyData) {
            $user = User::create([
                'name' => $companyData['name'],
                'email' => $companyData['email'],
                'password' => bcrypt('123456')
            ]);

            $user->roles()->attach($companyRole->id);

            Company::create([
                'user_id' => $user->id,
                'name' => $companyData['name'],
                'description' => $companyData['description'],
                'logo' => $companyData['logo'],
                'address' => $companyData['address'],
                'city' => $companyData['city'],
                'phone' => $companyData['phone'],
                'verified' => true
            ]);
        }
    }
}