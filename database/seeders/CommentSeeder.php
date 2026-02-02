<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;

class CommentSeeder extends Seeder
{
    public function run()
    {
        // Only regular users can comment (not admin or companies)
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'user');
        })->get();

        // Only regular posts (not stories)
        $posts = Post::where('is_story', false)->get();

        if ($users->isEmpty() || $posts->isEmpty()) {
            return;
        }

        $commentTexts = [
            'Excellent service! Highly recommended.',
            'Love it, will definitely come back. 😊',
            'Very good customer service.',
            'Great! Just what I needed.',
            'Totally recommended.',
            'What a great experience!',
            'Thank you very much, everything perfect.',
            'I really liked it, will repeat for sure.',
            'Fantastic! Exceeded my expectations.',
            'Excellent professionalism.',
            'Very satisfied with the result.',
            'Great quality-price ratio.',
            'I love this place!',
            'Fast and efficient service.',
            'Very good value for money.',
            'Will recommend to my friends.',
            'Amazing! Couldn\'t be happier.',
            'Exceptional service.',
            'Will be back soon, no doubt.'
        ];

        // Cada post recibe entre 0 y 5 comentarios
        foreach ($posts as $post) {
            $count = rand(0, 5);
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $commentText = $commentTexts[array_rand($commentTexts)];
                
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'content' => $commentText,
                    'created_at' => Carbon::parse($post->created_at)->addHours(rand(1, 48))
                ]);
            }
        }
    }
}
