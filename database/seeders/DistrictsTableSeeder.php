<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\City;

class DistrictsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
             // Fetch cities from Jordan and Syria
        $jordanCities = DB::table('cities')->where('country_id', 1)->get();  // Assuming Jordan's country_id is 1
        $syriaCities = DB::table('cities')->where('country_id', 2)->get();   // Assuming Syria's country_id is 2

        // Add districts for Jordan cities
        foreach ($jordanCities as $city) {
            DB::table('districts')->insert([
                ['city_id' => $city->id, 'name_en' => $city->name_en . ' District 1', 'name_ar' => $city->name_ar . ' المنطقة 1', 'lat' => $city->lat + 0.01, 'lng' => $city->lng + 0.01, 'created_at' => now(), 'updated_at' => now()],
                ['city_id' => $city->id, 'name_en' => $city->name_en . ' District 2', 'name_ar' => $city->name_ar . ' المنطقة 2', 'lat' => $city->lat + 0.02, 'lng' => $city->lng + 0.02, 'created_at' => now(), 'updated_at' => now()],
                ['city_id' => $city->id, 'name_en' => $city->name_en . ' District 3', 'name_ar' => $city->name_ar . ' المنطقة 3', 'lat' => $city->lat + 0.03, 'lng' => $city->lng + 0.03, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Add districts for Syria cities
        foreach ($syriaCities as $city) {
            DB::table('districts')->insert([
                ['city_id' => $city->id, 'name_en' => $city->name_en . ' District 1', 'name_ar' => $city->name_ar . ' المنطقة 1', 'lat' => $city->lat + 0.01, 'lng' => $city->lng + 0.01, 'created_at' => now(), 'updated_at' => now()],
                ['city_id' => $city->id, 'name_en' => $city->name_en . ' District 2', 'name_ar' => $city->name_ar . ' المنطقة 2', 'lat' => $city->lat + 0.02, 'lng' => $city->lng + 0.02, 'created_at' => now(), 'updated_at' => now()],
                ['city_id' => $city->id, 'name_en' => $city->name_en . ' District 3', 'name_ar' => $city->name_ar . ' المنطقة 3', 'lat' => $city->lat + 0.03, 'lng' => $city->lng + 0.03, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
