<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        // Solo usuarios normales hacen reservas (no admin ni empresas)
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'user');
        })->get();
        
        $services = Service::all();

        if ($users->isEmpty() || $services->isEmpty()) {
            return;
        }

        // Crear reservas variadas para cada usuario
        foreach ($users as $user) {
            // Cada usuario tiene entre 2 y 5 reservas
            $count = rand(2, 5);
            
            for ($i = 0; $i < $count; $i++) {
                $service = $services->random();
                
                // Fechas variadas: algunas pasadas, algunas futuras
                if ($i % 3 === 0) {
                    // Reservas pasadas (confirmadas o canceladas)
                    $date = Carbon::now()->subDays(rand(5, 60))->toDateString();
                    $status = rand(0, 1) ? 'CONFIRMED' : 'CANCELLED';
                } else {
                    // Reservas futuras (pendientes o confirmadas)
                    $date = Carbon::now()->addDays(rand(1, 45))->toDateString();
                    $status = rand(0, 1) ? 'PENDING' : 'CONFIRMED';
                }
                
                // Horarios de negocio: 09:00 a 19:00
                $hour = rand(9, 18);
                $minute = rand(0, 1) ? '00' : '30';
                $time = sprintf('%02d:%s:00', $hour, $minute);

                // Evitar duplicados por la constraint única
                $exists = Booking::where('service_id', $service->id)
                    ->where('date', $date)
                    ->where('time', $time)
                    ->exists();

                if ($exists) {
                    continue;
                }

                Booking::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'date' => $date,
                    'time' => $time,
                    'status' => $status
                ]);
            }
        }
    }
}
