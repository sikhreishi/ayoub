<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle\VehicleType;

class VehicleTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Taxi',
                'description' => 'Standard taxi service',
                'start_fare' => 5.00,
                'day_per_minute_rate' => 0.50,
                'night_per_minute_rate' => 0.75,
                'day_per_km_rate' => 1.50,
                'night_per_km_rate' => 2.00,
                'icon_url' => 'icons/taxi.png',
            ],
            [
                'name' => 'Economy',
                'description' => 'Affordable ride for everyday use',
                'start_fare' => 5.00,
                'day_per_minute_rate' => 0.50,
                'night_per_minute_rate' => 0.75,
                'day_per_km_rate' => 1.50,
                'night_per_km_rate' => 2.00,
                'icon_url' => 'icons/economy.png',
            ],
            [
                'name' => 'Luxury',
                'description' => 'Premium ride experience',
                'start_fare' => 5.00,
                'day_per_minute_rate' => 0.50,
                'night_per_minute_rate' => 0.75,
                'day_per_km_rate' => 1.50,
                'night_per_km_rate' => 2.00,
                'icon_url' => 'icons/luxury.png',
            ],
        ];

        foreach ($types as $type) {
            VehicleType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
