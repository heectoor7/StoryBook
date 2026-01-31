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

        // Servicios específicos por empresa
        $servicesData = [
            'Peluquería Carmen' => [
                'category' => 'Peluquería',
                'services' => [
                    ['name' => 'Corte de pelo mujer', 'description' => 'Corte profesional adaptado a tu estilo', 'price' => 25.00],
                    ['name' => 'Corte de pelo hombre', 'description' => 'Corte clásico o moderno', 'price' => 18.00],
                    ['name' => 'Tinte completo', 'description' => 'Coloración completa con productos de calidad', 'price' => 45.00],
                    ['name' => 'Mechas balayage', 'description' => 'Técnica moderna de iluminación capilar', 'price' => 65.00],
                    ['name' => 'Tratamiento de keratina', 'description' => 'Alisado y nutrición profunda', 'price' => 80.00]
                ]
            ],
            'Taller Paco' => [
                'category' => 'Taller Mecánico',
                'services' => [
                    ['name' => 'Cambio de aceite', 'description' => 'Cambio de aceite y filtro', 'price' => 45.00],
                    ['name' => 'Revisión completa', 'description' => 'Revisión de todos los sistemas del vehículo', 'price' => 75.00],
                    ['name' => 'Cambio de pastillas de freno', 'description' => 'Sustitución de pastillas delanteras o traseras', 'price' => 90.00],
                    ['name' => 'Alineación y equilibrado', 'description' => 'Ajuste de neumáticos para mejor conducción', 'price' => 55.00],
                    ['name' => 'Cambio de batería', 'description' => 'Instalación de batería nueva', 'price' => 120.00]
                ]
            ],
            'Restaurante El Rincón' => [
                'category' => 'Restaurante',
                'services' => [
                    ['name' => 'Menú del día', 'description' => 'Primer plato, segundo, postre y bebida', 'price' => 12.50],
                    ['name' => 'Menú degustación', 'description' => '5 platos especiales del chef', 'price' => 35.00],
                    ['name' => 'Paella valenciana (2 pers)', 'description' => 'Paella tradicional para dos personas', 'price' => 28.00],
                    ['name' => 'Reserva sala privada', 'description' => 'Espacio privado para eventos (consumición aparte)', 'price' => 50.00]
                ]
            ],
            'Gimnasio FitLife' => [
                'category' => 'Gimnasio',
                'services' => [
                    ['name' => 'Matrícula mensual', 'description' => 'Acceso ilimitado al gimnasio durante un mes', 'price' => 35.00],
                    ['name' => 'Matrícula trimestral', 'description' => 'Acceso ilimitado durante 3 meses', 'price' => 90.00],
                    ['name' => 'Clase de spinning', 'description' => 'Sesión individual de spinning', 'price' => 8.00],
                    ['name' => 'Clase de yoga', 'description' => 'Sesión de yoga guiada', 'price' => 10.00],
                    ['name' => 'Entrenamiento personal', 'description' => 'Sesión 1h con entrenador personal', 'price' => 40.00]
                ]
            ],
            'Veterinaria San Francisco' => [
                'category' => 'Veterinaria',
                'services' => [
                    ['name' => 'Consulta general', 'description' => 'Revisión veterinaria completa', 'price' => 30.00],
                    ['name' => 'Vacunación', 'description' => 'Vacuna obligatoria o recomendada', 'price' => 25.00],
                    ['name' => 'Desparasitación', 'description' => 'Tratamiento antiparasitario interno y externo', 'price' => 20.00],
                    ['name' => 'Esterilización', 'description' => 'Cirugía de esterilización (perro/gato)', 'price' => 150.00],
                    ['name' => 'Urgencia 24h', 'description' => 'Atención veterinaria urgente', 'price' => 80.00]
                ]
            ],
            'Panadería La Espiga' => [
                'category' => 'Panadería',
                'services' => [
                    ['name' => 'Barra de pan', 'description' => 'Pan fresco del día', 'price' => 0.80],
                    ['name' => 'Croissant', 'description' => 'Croissant de mantequilla', 'price' => 1.20],
                    ['name' => 'Tarta personalizada', 'description' => 'Tarta decorada para eventos (1kg)', 'price' => 25.00],
                    ['name' => 'Bandeja de bollería', 'description' => '12 piezas variadas de bollería', 'price' => 15.00]
                ]
            ],
            'Librería Cervantes' => [
                'category' => 'Librería',
                'services' => [
                    ['name' => 'Libro bestseller', 'description' => 'Últimas novedades editoriales', 'price' => 18.00],
                    ['name' => 'Material escolar completo', 'description' => 'Kit básico para curso escolar', 'price' => 35.00],
                    ['name' => 'Cómic/Manga', 'description' => 'Ediciones de cómics y manga', 'price' => 12.00],
                    ['name' => 'Tarjeta regalo', 'description' => 'Tarjeta regalo por valor elegido', 'price' => 20.00]
                ]
            ],
            'Floristería Jardín' => [
                'category' => 'Floristería',
                'services' => [
                    ['name' => 'Ramo de rosas (12 uds)', 'description' => 'Docena de rosas frescas', 'price' => 30.00],
                    ['name' => 'Centro de mesa', 'description' => 'Arreglo floral decorativo', 'price' => 25.00],
                    ['name' => 'Planta de interior', 'description' => 'Planta natural en maceta decorativa', 'price' => 18.00],
                    ['name' => 'Decoración floral boda', 'description' => 'Servicio completo de decoración floral', 'price' => 350.00]
                ]
            ],
            'Estudio Foto Luz' => [
                'category' => 'Fotografía',
                'services' => [
                    ['name' => 'Sesión retrato individual', 'description' => 'Sesión de fotos profesional 1h', 'price' => 80.00],
                    ['name' => 'Sesión familiar', 'description' => 'Reportaje familiar 2h + 20 fotos editadas', 'price' => 150.00],
                    ['name' => 'Book fotográfico', 'description' => 'Sesión profesional con 30 fotos editadas', 'price' => 200.00],
                    ['name' => 'Fotografía de boda', 'description' => 'Cobertura completa del evento', 'price' => 800.00]
                ]
            ],
            'Spa Relax Center' => [
                'category' => 'Spa y Bienestar',
                'services' => [
                    ['name' => 'Masaje relajante 60min', 'description' => 'Masaje completo de relajación', 'price' => 45.00],
                    ['name' => 'Tratamiento facial', 'description' => 'Limpieza e hidratación facial profunda', 'price' => 50.00],
                    ['name' => 'Masaje con piedras calientes', 'description' => 'Terapia con piedras volcánicas', 'price' => 65.00],
                    ['name' => 'Circuito spa 2h', 'description' => 'Acceso a jacuzzi, sauna y baño turco', 'price' => 35.00],
                    ['name' => 'Bono 4 sesiones', 'description' => 'Bono mensual de masajes relajantes', 'price' => 130.00]
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

            foreach ($data['services'] as $serviceData) {
                Service::create([
                    'company_id' => $company->id,
                    'category_id' => $category->id,
                    'name' => $serviceData['name'],
                    'description' => $serviceData['description'],
                    'price' => $serviceData['price']
                ]);
            }
        }
    }
}
