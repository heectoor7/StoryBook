<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Company;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    public function run()
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            return;
        }

        // Realistic content for each type of company
        $contents = [
            'Carmen\'s Hair Salon' => [
                'posts' => [
                    'New balayage highlights service! 💇‍♀️ Book your appointment and get radiant hair.',
                    'This month we have special discounts on keratin treatments. Don\'t miss it!',
                    'Thank you to all our clients for trusting us. ❤️ We look forward to seeing you!',
                    'Have you tried our new manicure and pedicure service yet? You\'ll love it!'
                ],
                'stories' => [
                    '✨ Today: Cut + Style = $35',
                    'Look at this before and after! 😍',
                    'Open until 8:00 PM'
                ]
            ],
            'Pete\'s Auto Repair' => [
                'posts' => [
                    'Complete vehicle inspection before summer. Travel safe! 🚗',
                    'Special offer: Oil change + filter for only $45.',
                    'Reminder: Inspection time is near. Schedule your appointment with us.',
                    'Brake problems? Come see us, free inspection.'
                ],
                'stories' => [
                    '⚠️ Flash sale: 20% off',
                    'Open Saturday mornings',
                    'New diagnostic service'
                ]
            ],
            'The Corner Restaurant' => [
                'posts' => [
                    'Today\'s special seafood menu! 🦐 Come enjoy with your family.',
                    'New regional wine list. Come try them! 🍷',
                    'Thank you for your reviews. You\'re the best!',
                    'Reserve your table for the weekend. We look forward to seeing you!'
                ],
                'stories' => [
                    '🍝 Dish of the day: Valencian Paella',
                    'Table available at 2:00 PM',
                    'Homemade dessert: Cheesecake'
                ]
            ],
            'FitLife Gym' => [
                'posts' => [
                    'New spinning classes every Monday and Wednesday. 🚴‍♂️',
                    'Join this month and get a free week! 💪',
                    'Tips: Stay well hydrated during your workout.',
                    'Our trainers are here to help you achieve your goals.'
                ],
                'stories' => [
                    '🏋️ Yoga class in 30 min',
                    'Registration open',
                    'Happy Friday! #FitLifeMotivation'
                ]
            ],
            'St. Francis Veterinary' => [
                'posts' => [
                    'Remember: your pet\'s vaccinations are important. 🐕',
                    'Does your cat have fleas? We have the perfect treatment.',
                    '24h emergency service. We\'re here to take care of them. 🐾',
                    'Tips: Brush your dog\'s teeth regularly.'
                ],
                'stories' => [
                    '🐈 Sterilization campaign',
                    'Discount on consultations today',
                    'New products in store'
                ]
            ],
            'The Golden Wheat Bakery' => [
                'posts' => [
                    'Freshly baked bread at 7:00 AM. Come get yours! 🍞',
                    'This weekend: Special Three Kings Cake.',
                    'New variety: Whole wheat bread with seeds. Try it!',
                    'Thank you for choosing us every day. ❤️'
                ],
                'stories' => [
                    '🥐 Fresh croissants',
                    '10 bread loaves left',
                    'Tomorrow: Country bread'
                ]
            ],
            'Cervantes Bookstore' => [
                'posts' => [
                    'New international bestseller books. Come discover them! 📚',
                    '20% off school supplies all month.',
                    'Book club: Next meeting Friday at 6:00 PM.',
                    'Looking for a gift? We have gift cards available.'
                ],
                'stories' => [
                    '📖 Book of the day',
                    'Author signing this Saturday',
                    'New comics available'
                ]
            ],
            'Garden Florist' => [
                'posts' => [
                    'Fresh rose bouquets for that special day. 🌹',
                    'Floral decoration for weddings and events. Contact us!',
                    'Indoor plants: perfect for your home. 🌿',
                    'Valentine\'s Day is coming... Place your order in advance!'
                ],
                'stories' => [
                    '🌸 Flowers of the day: Tulips',
                    'Offer: 3 for 2 on plants',
                    'Centerpiece available'
                ]
            ],
            'Light Photography Studio' => [
                'posts' => [
                    'Family photo sessions with 20% discount. 📸',
                    'Wedding coming up? Check out our special packages.',
                    'Professional photo book. Book your session!',
                    'Thank you for trusting us to capture your moments.'
                ],
                'stories' => [
                    '📷 Today\'s session: Newborn baby',
                    'Availability for this Saturday',
                    'Look at this result 😍'
                ]
            ],
            'Relax Spa Center' => [
                'posts' => [
                    '60-minute relaxing massage for only $45. 💆‍♀️',
                    'Facial treatment with natural products. You\'ll love it!',
                    'Monthly package: 4 sessions for the price of 3.',
                    'Stressed? Come disconnect with us.'
                ],
                'stories' => [
                    '🧘‍♀️ Meditation session at 6 PM',
                    'Spot available this afternoon',
                    'New body treatment'
                ]
            ]
        ];

        foreach ($companies as $company) {
            $companyName = $company->name;
            
            if (!isset($contents[$companyName])) {
                continue;
            }

            $data = $contents[$companyName];

            // Create regular posts (with different dates)
            foreach ($data['posts'] as $index => $content) {
                Post::create([
                    'company_id' => $company->id,
                    'content' => $content,
                    'image' => 'https://picsum.photos/seed/' . $company->id . $index . '/800/600',
                    'is_story' => false,
                    'expires_at' => null,
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23))
                ]);
            }

            // Create stories (expire in 24 hours)
            foreach ($data['stories'] as $index => $content) {
                Post::create([
                    'company_id' => $company->id,
                    'content' => $content,
                    'image' => 'https://picsum.photos/seed/story' . $company->id . $index . '/600/800',
                    'is_story' => true,
                    'expires_at' => Carbon::now()->addHours(rand(12, 24)),
                    'created_at' => Carbon::now()->subHours(rand(1, 12))
                ]);
            }
        }
    }
}
