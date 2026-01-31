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
        // Solo usuarios normales pueden comentar (no admin ni empresas)
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'user');
        })->get();

        // Solo posts normales (no stories)
        $posts = Post::where('is_story', false)->get();

        if ($users->isEmpty() || $posts->isEmpty()) {
            return;
        }

        $commentTexts = [
            '¡Excelente servicio! Muy recomendable.',
            'Me encanta, volveré sin duda. 😊',
            'Muy buena atención al cliente.',
            '¡Genial! Justo lo que necesitaba.',
            'Totalmente recomendado.',
            '¡Qué buena experiencia!',
            'Muchas gracias, todo perfecto.',
            'Me ha gustado mucho, repetiré seguro.',
            '¡Fantástico! Superó mis expectativas.',
            'Excelente profesionalidad.',
            'Muy satisfecho con el resultado.',
            'Gran calidad-precio.',
            '¡Me encanta este sitio!',
            'Servicio rápido y eficiente.',
            'Muy buena relación calidad-precio.',
            'Lo recomendaré a mis amigos.',
            '¡Increíble! No puedo estar más contento.',
            'Atención excepcional.',
            'Volveré pronto, sin duda.'
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
