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

        // Contenidos realistas para cada tipo de empresa
        $contents = [
            'Peluquería Carmen' => [
                'posts' => [
                    '¡Nuevo servicio de mechas balayage! 💇‍♀️ Reserva tu cita y luce un cabello radiante.',
                    'Este mes tenemos descuentos especiales en tratamientos de keratina. ¡No te lo pierdas!',
                    'Gracias a todas nuestras clientas por confiar en nosotros. ❤️ ¡Os esperamos!',
                    '¿Ya has probado nuestro nuevo servicio de manicura y pedicura? ¡Te encantará!'
                ],
                'stories' => [
                    '✨ Hoy: Corte + Peinado = €35',
                    '¡Mira este antes y después! 😍',
                    'Abrimos hasta las 20:00h'
                ]
            ],
            'Taller Paco' => [
                'posts' => [
                    'Revisión completa de tu vehículo antes del verano. ¡Viaja seguro! 🚗',
                    'Oferta especial: Cambio de aceite + filtro por solo €45.',
                    'Recordatorio: La ITV está cerca. Agenda tu cita con nosotros.',
                    '¿Problemas con los frenos? Ven a vernos, revisión gratuita.'
                ],
                'stories' => [
                    '⚠️ Promoción flash: 20% dto',
                    'Abierto sábados por la mañana',
                    'Nuevo servicio de diagnosis'
                ]
            ],
            'Restaurante El Rincón' => [
                'posts' => [
                    '¡Hoy menú especial de mariscos! 🦐 Ven a disfrutar con tu familia.',
                    'Nueva carta de vinos de la región. ¡Ven a probarlos! 🍷',
                    'Gracias por vuestras reseñas. ¡Sois los mejores!',
                    'Reserva tu mesa para el fin de semana. ¡Te esperamos!'
                ],
                'stories' => [
                    '🍝 Plato del día: Paella Valenciana',
                    'Mesa disponible a las 14:00',
                    'Postre casero: Tarta de queso'
                ]
            ],
            'Gimnasio FitLife' => [
                'posts' => [
                    'Nuevas clases de spinning todos los lunes y miércoles. 🚴‍♂️',
                    '¡Únete este mes y llévate una semana gratis! 💪',
                    'Consejos: Hidrátate bien durante tu entrenamiento.',
                    'Nuestros entrenadores están aquí para ayudarte a conseguir tus objetivos.'
                ],
                'stories' => [
                    '🏋️ Clase de yoga en 30 min',
                    'Inscripciones abiertas',
                    '¡Feliz viernes! #MotivaciónFitLife'
                ]
            ],
            'Veterinaria San Francisco' => [
                'posts' => [
                    'Recuerda: las vacunas de tu mascota son importantes. 🐕',
                    '¿Tu gato tiene pulgas? Tenemos el tratamiento perfecto.',
                    'Servicio de urgencias 24h. Estamos para cuidar de ellos. 🐾',
                    'Consejos: Cepilla los dientes de tu perro regularmente.'
                ],
                'stories' => [
                    '🐈 Campaña de esterilización',
                    'Descuento en consultas hoy',
                    'Nuevos productos en tienda'
                ]
            ],
            'Panadería La Espiga' => [
                'posts' => [
                    'Pan recién horneado a las 7:00 de la mañana. ¡Ven por el tuyo! 🍞',
                    'Este fin de semana: Roscón de Reyes especial.',
                    'Nueva variedad: Pan integral con semillas. ¡Pruébalo!',
                    'Gracias por elegirnos cada día. ❤️'
                ],
                'stories' => [
                    '🥐 Croissants recién hechos',
                    'Quedan 10 barras de pan',
                    'Mañana: Pan de pueblo'
                ]
            ],
            'Librería Cervantes' => [
                'posts' => [
                    'Nuevos libros de bestsellers internacionales. ¡Ven a descubrirlos! 📚',
                    '20% de descuento en material escolar todo el mes.',
                    'Club de lectura: Próxima reunión el viernes a las 18:00.',
                    '¿Buscas un regalo? Tenemos tarjetas regalo disponibles.'
                ],
                'stories' => [
                    '📖 Libro recomendado del día',
                    'Firma de autor este sábado',
                    'Nuevos cómics disponibles'
                ]
            ],
            'Floristería Jardín' => [
                'posts' => [
                    'Ramos de rosas frescas para ese día especial. 🌹',
                    'Decoración floral para bodas y eventos. ¡Contáctanos!',
                    'Plantas de interior: perfectas para tu hogar. 🌿',
                    'San Valentín se acerca... ¡Haz tu pedido con antelación!'
                ],
                'stories' => [
                    '🌸 Flores del día: Tulipanes',
                    'Oferta: 3x2 en plantas',
                    'Centro de mesa disponible'
                ]
            ],
            'Estudio Foto Luz' => [
                'posts' => [
                    'Sesión de fotos para familias con 20% de descuento. 📸',
                    '¿Boda a la vista? Consulta nuestros paquetes especiales.',
                    'Book fotográfico profesional. ¡Reserva tu sesión!',
                    'Gracias por confiar en nosotros para capturar vuestros momentos.'
                ],
                'stories' => [
                    '📷 Sesión de hoy: Bebé recién nacido',
                    'Disponibilidad para este sábado',
                    'Mira este resultado 😍'
                ]
            ],
            'Spa Relax Center' => [
                'posts' => [
                    'Masaje relajante de 60 minutos por solo €45. 💆‍♀️',
                    'Tratamiento facial con productos naturales. ¡Te encantará!',
                    'Bono mensual: 4 sesiones por el precio de 3.',
                    '¿Estrés? Ven a desconectar con nosotros.'
                ],
                'stories' => [
                    '🧘‍♀️ Sesión de meditación a las 18h',
                    'Hueco disponible esta tarde',
                    'Nuevo tratamiento corporal'
                ]
            ]
        ];

        foreach ($companies as $company) {
            $companyName = $company->name;
            
            if (!isset($contents[$companyName])) {
                continue;
            }

            $data = $contents[$companyName];

            // Crear publicaciones normales (con fechas diferentes)
            foreach ($data['posts'] as $index => $content) {
                Post::create([
                    'company_id' => $company->id,
                    'content' => $content,
                    'is_story' => false,
                    'expires_at' => null,
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23))
                ]);
            }

            // Crear historias (expiran en 24 horas)
            foreach ($data['stories'] as $index => $content) {
                Post::create([
                    'company_id' => $company->id,
                    'content' => $content,
                    'is_story' => true,
                    'expires_at' => Carbon::now()->addHours(rand(12, 24)),
                    'created_at' => Carbon::now()->subHours(rand(1, 12))
                ]);
            }
        }
    }
}
