<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Peluquería',
            'Taller Mecánico',
            'Restaurante',
            'Gimnasio',
            'Veterinaria',
            'Panadería',
            'Librería',
            'Floristería',
            'Fotografía',
            'Spa y Bienestar'
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
