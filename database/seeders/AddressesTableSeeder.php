<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('users')->first();
        $city = DB::table('cities')->first();

        if ($user && $city) {
            DB::table('addresses')->insert([
                [
                    'user_id' => $user->id,
                    'city_id' => $city->id,
                    'district_id' => null, 
                    'label' => 'Home',
                    'lat' => 31.9552,
                    'lng' => 35.9450,
                    'type' => 'home',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
    
}
