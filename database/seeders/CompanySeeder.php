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

        // 10 empresas reales con nombres auténticos
        $companies = [
            [
                'name' => 'Peluquería Carmen',
                'email' => 'empresa1@storybook.com',
                'description' => 'Peluquería de confianza con más de 20 años de experiencia. Especializados en cortes modernos y coloración.',
                'address' => 'Calle Mayor 23',
                'city' => 'Madrid',
                'phone' => '910123456'
            ],
            [
                'name' => 'Taller Paco',
                'email' => 'empresa2@storybook.com',
                'description' => 'Taller mecánico especializado en todo tipo de reparaciones y mantenimiento de vehículos.',
                'address' => 'Avenida Industrial 45',
                'city' => 'Barcelona',
                'phone' => '932654789'
            ],
            [
                'name' => 'Restaurante El Rincón',
                'email' => 'empresa3@storybook.com',
                'description' => 'Comida casera mediterránea con los mejores ingredientes frescos del mercado.',
                'address' => 'Plaza de la Constitución 8',
                'city' => 'Valencia',
                'phone' => '963789012'
            ],
            [
                'name' => 'Gimnasio FitLife',
                'email' => 'empresa4@storybook.com',
                'description' => 'Centro deportivo completo con las mejores instalaciones y entrenadores profesionales.',
                'address' => 'Calle del Deporte 12',
                'city' => 'Sevilla',
                'phone' => '954321098'
            ],
            [
                'name' => 'Veterinaria San Francisco',
                'email' => 'empresa5@storybook.com',
                'description' => 'Clínica veterinaria con servicio 24h. Cuidamos de tus mascotas como si fueran nuestras.',
                'address' => 'Calle Veterinaria 34',
                'city' => 'Zaragoza',
                'phone' => '976543210'
            ],
            [
                'name' => 'Panadería La Espiga',
                'email' => 'empresa6@storybook.com',
                'description' => 'Pan artesanal recién horneado cada día. Bollería y repostería casera.',
                'address' => 'Calle del Pan 5',
                'city' => 'Málaga',
                'phone' => '952147852'
            ],
            [
                'name' => 'Librería Cervantes',
                'email' => 'empresa7@storybook.com',
                'description' => 'Librería de barrio con amplio catálogo. También vendemos papelería y material escolar.',
                'address' => 'Calle Libreros 18',
                'city' => 'Bilbao',
                'phone' => '944258963'
            ],
            [
                'name' => 'Floristería Jardín',
                'email' => 'empresa8@storybook.com',
                'description' => 'Flores frescas para cualquier ocasión. Ramos personalizados y decoración floral.',
                'address' => 'Paseo de las Flores 9',
                'city' => 'Granada',
                'phone' => '958369147'
            ],
            [
                'name' => 'Estudio Foto Luz',
                'email' => 'empresa9@storybook.com',
                'description' => 'Fotografía profesional para eventos, bodas y retratos. Estudio fotográfico moderno.',
                'address' => 'Avenida de la Imagen 27',
                'city' => 'Murcia',
                'phone' => '968753159'
            ],
            [
                'name' => 'Spa Relax Center',
                'email' => 'empresa10@storybook.com',
                'description' => 'Centro de bienestar y spa. Masajes, tratamientos faciales y corporales.',
                'address' => 'Calle Relax 15',
                'city' => 'Alicante',
                'phone' => '965147258'
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
                'address' => $companyData['address'],
                'city' => $companyData['city'],
                'phone' => $companyData['phone'],
                'verified' => true
            ]);
        }
    }
}