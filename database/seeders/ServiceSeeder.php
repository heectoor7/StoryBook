<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Company;
use App\Models\Category;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $companies = Company::all();
        $categories = Category::all();

        if ($companies->isEmpty() || $categories->isEmpty()) {
            return;
        }

        // Specific services by company
        $servicesData = [
            'Carmen\'s Hair Salon' => [
                'category' => 'Hair Salon',
                'services' => [
                    ['name' => 'Women\'s Haircut', 'description' => 'Professional cut adapted to your style', 'price' => 25.00],
                    ['name' => 'Men\'s Haircut', 'description' => 'Classic or modern cut', 'price' => 18.00],
                    ['name' => 'Full Color', 'description' => 'Complete coloring with quality products', 'price' => 45.00],
                    ['name' => 'Balayage Highlights', 'description' => 'Modern hair lighting technique', 'price' => 65.00],
                    ['name' => 'Keratin Treatment', 'description' => 'Straightening and deep nourishment', 'price' => 80.00]
                ]
            ],
            'Pete\'s Auto Repair' => [
                'category' => 'Auto Repair',
                'services' => [
                    ['name' => 'Oil Change', 'description' => 'Oil and filter change', 'price' => 45.00],
                    ['name' => 'Complete Inspection', 'description' => 'Review of all vehicle systems', 'price' => 75.00],
                    ['name' => 'Brake Pad Replacement', 'description' => 'Front or rear pad replacement', 'price' => 90.00],
                    ['name' => 'Wheel Alignment', 'description' => 'Tire adjustment for better driving', 'price' => 55.00],
                    ['name' => 'Battery Replacement', 'description' => 'New battery installation', 'price' => 120.00]
                ]
            ],
            'The Corner Restaurant' => [
                'category' => 'Restaurant',
                'services' => [
                    ['name' => 'Daily Menu', 'description' => 'Appetizer, main course, dessert and drink', 'price' => 12.50],
                    ['name' => 'Tasting Menu', 'description' => '5 special chef dishes', 'price' => 35.00],
                    ['name' => 'Paella (2 people)', 'description' => 'Traditional paella for two', 'price' => 28.00],
                    ['name' => 'Private Room Reservation', 'description' => 'Private space for events (consumption aside)', 'price' => 50.00]
                ]
            ],
            'FitLife Gym' => [
                'category' => 'Gym',
                'services' => [
                    ['name' => 'Monthly Membership', 'description' => 'Unlimited gym access for one month', 'price' => 35.00],
                    ['name' => 'Quarterly Membership', 'description' => 'Unlimited access for 3 months', 'price' => 90.00],
                    ['name' => 'Spinning Class', 'description' => 'Individual spinning session', 'price' => 8.00],
                    ['name' => 'Yoga Class', 'description' => 'Guided yoga session', 'price' => 10.00],
                    ['name' => 'Personal Training', 'description' => '1h session with personal trainer', 'price' => 40.00]
                ]
            ],
            'St. Francis Veterinary' => [
                'category' => 'Veterinary',
                'services' => [
                    ['name' => 'General Consultation', 'description' => 'Complete veterinary checkup', 'price' => 30.00],
                    ['name' => 'Vaccination', 'description' => 'Required or recommended vaccine', 'price' => 25.00],
                    ['name' => 'Deworming', 'description' => 'Internal and external antiparasitic treatment', 'price' => 20.00],
                    ['name' => 'Spay/Neuter', 'description' => 'Spay/neuter surgery (dog/cat)', 'price' => 150.00],
                    ['name' => '24h Emergency', 'description' => 'Urgent veterinary care', 'price' => 80.00]
                ]
            ],
            'The Golden Wheat Bakery' => [
                'category' => 'Bakery',
                'services' => [
                    ['name' => 'Bread Loaf', 'description' => 'Fresh bread of the day', 'price' => 0.80],
                    ['name' => 'Croissant', 'description' => 'Butter croissant', 'price' => 1.20],
                    ['name' => 'Custom Cake', 'description' => 'Decorated cake for events (1kg)', 'price' => 25.00],
                    ['name' => 'Pastry Tray', 'description' => '12 assorted pastry pieces', 'price' => 15.00]
                ]
            ],
            'Cervantes Bookstore' => [
                'category' => 'Bookstore',
                'services' => [
                    ['name' => 'Bestseller Book', 'description' => 'Latest publishing releases', 'price' => 18.00],
                    ['name' => 'Complete School Supplies', 'description' => 'Basic kit for school year', 'price' => 35.00],
                    ['name' => 'Comic/Manga', 'description' => 'Comic and manga editions', 'price' => 12.00],
                    ['name' => 'Gift Card', 'description' => 'Gift card for chosen amount', 'price' => 20.00]
                ]
            ],
            'Garden Florist' => [
                'category' => 'Florist',
                'services' => [
                    ['name' => 'Rose Bouquet (12 pcs)', 'description' => 'Dozen fresh roses', 'price' => 30.00],
                    ['name' => 'Centerpiece', 'description' => 'Decorative floral arrangement', 'price' => 25.00],
                    ['name' => 'Indoor Plant', 'description' => 'Natural plant in decorative pot', 'price' => 18.00],
                    ['name' => 'Wedding Floral Decoration', 'description' => 'Complete floral decoration service', 'price' => 350.00]
                ]
            ],
            'Light Photography Studio' => [
                'category' => 'Photography',
                'services' => [
                    ['name' => 'Individual Portrait Session', 'description' => '1h professional photo session', 'price' => 80.00],
                    ['name' => 'Family Session', 'description' => '2h family shoot + 20 edited photos', 'price' => 150.00],
                    ['name' => 'Photo Book', 'description' => 'Professional session with 30 edited photos', 'price' => 200.00],
                    ['name' => 'Wedding Photography', 'description' => 'Complete event coverage', 'price' => 800.00]
                ]
            ],
            'Relax Spa Center' => [
                'category' => 'Spa & Wellness',
                'services' => [
                    ['name' => 'Relaxing Massage 60min', 'description' => 'Full relaxation massage', 'price' => 45.00],
                    ['name' => 'Facial Treatment', 'description' => 'Deep facial cleansing and hydration', 'price' => 50.00],
                    ['name' => 'Hot Stone Massage', 'description' => 'Volcanic stone therapy', 'price' => 65.00],
                    ['name' => 'Spa Circuit 2h', 'description' => 'Access to jacuzzi, sauna and Turkish bath', 'price' => 35.00],
                    ['name' => '4 Session Pass', 'description' => 'Monthly pass for relaxing massages', 'price' => 130.00]
                ]
            ]
        ];

        foreach ($companies as $company) {
            $companyName = $company->name;
            
            if (!isset($servicesData[$companyName])) {
                continue;
            }

            $data = $servicesData[$companyName];
            $category = Category::where('name', $data['category'])->first();
            
            if (!$category) {
                continue;
            }

            foreach ($data['services'] as $index => $serviceData) {
                Service::create([
                    'company_id' => $company->id,
                    'category_id' => $category->id,
                    'name' => $serviceData['name'],
                    'description' => $serviceData['description'],
                    'image' => 'https://picsum.photos/seed/service' . $company->id . $index . '/400/300',
                    'price' => $serviceData['price']
                ]);
            }
        }
    }
}
