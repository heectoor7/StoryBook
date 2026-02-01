<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Company;
use Faker\Factory as Faker;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $companies = Company::all();

        if ($companies->isEmpty()) {
            return;
        }

        $days = ['MON','TUE','WED','THU','FRI','SAT','SUN'];

        foreach ($companies as $company) {
            // cada empresa tendrá horarios para 5-7 días
            $count = rand(5, 7);
            $sampleDays = (array) array_slice($days, 0, $count);
            foreach ($sampleDays as $d) {
                // horario típico de 09:00-18:00 con variaciones
                $startHour = $faker->numberBetween(8, 10);
                $endHour = $faker->numberBetween(17, 20);
                $start = sprintf('%02d:00:00', $startHour);
                $end = sprintf('%02d:00:00', $endHour);

                // Evitar duplicados
                if (Schedule::where('company_id', $company->id)->where('day', $d)->exists()) continue;

                Schedule::create([
                    'company_id' => $company->id,
                    'day' => $d,
                    'start' => $start,
                    'end' => $end
                ]);
            }
        }
    }
}
