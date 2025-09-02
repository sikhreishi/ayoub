<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->insert([
        [
            'name_en' => 'Jordan',
            'name_ar' => 'الأردن',
            'code'    => 'JOR',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name_en' => 'Syria',
            'name_ar' => 'سوريا',
            'code'    => 'SYR',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }
}
