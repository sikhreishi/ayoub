<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Jordan country id
        $jordan = DB::table('countries')->where('code', 'JOR')->first();
        $syria = DB::table('countries')->where('code', 'SYR')->first();

        if ($jordan) {
            DB::table('cities')->insert([
                ['country_id' => $jordan->id, 'name_en' => 'Amman',       'name_ar' => 'عمان',        'lat' => 31.9552,  'lng' => 35.9450,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Irbid',       'name_ar' => 'إربد',        'lat' => 32.5556,  'lng' => 35.8500,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Zarqa',       'name_ar' => 'الزرقاء',     'lat' => 32.0728,  'lng' => 36.0880,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Russeifa',    'name_ar' => 'الرصيفة',     'lat' => 32.0199,  'lng' => 36.0099,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Mafraq',      'name_ar' => 'المفرق',      'lat' => 32.3451,  'lng' => 36.2097,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Al-Karak',    'name_ar' => 'الكرك',       'lat' => 31.1859,  'lng' => 35.7044,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Madaba',      'name_ar' => 'مادبا',       'lat' => 31.7154,  'lng' => 35.7933,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Ajloun',      'name_ar' => 'عجلون',       'lat' => 32.3273,  'lng' => 35.7512,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Tafila',      'name_ar' => 'الطفيلة',     'lat' => 30.8370,  'lng' => 35.5792,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Salt',        'name_ar' => 'السلط',       'lat' => 32.0358,  'lng' => 35.7274,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Ma\'an',      'name_ar' => 'معان',        'lat' => 30.1902,  'lng' => 35.7289,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Jerash',      'name_ar' => 'جرش',         'lat' => 32.2803,  'lng' => 35.8994,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Aqaba',       'name_ar' => 'العقبة',      'lat' => 29.5328,  'lng' => 35.0063,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Balqa',       'name_ar' => 'البلقاء',     'lat' => 32.0000,  'lng' => 35.6667,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Madaba',      'name_ar' => 'مادبا',       'lat' => 31.7154,  'lng' => 35.7933,  'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $jordan->id, 'name_en' => 'Jarash',      'name_ar' => 'جرش',         'lat' => 32.2803,  'lng' => 35.8994,  'created_at' => now(), 'updated_at' => now()],
            ]);

        }
        if ($syria) {
            DB::table('cities')->insert([
                ['country_id' => $syria->id, 'name_en' => 'Damascus',    'name_ar' => 'دمشق',       'lat' => 33.5138, 'lng' => 36.2765, 'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $syria->id, 'name_en' => 'Aleppo',      'name_ar' => 'حلب',        'lat' => 36.2021, 'lng' => 37.1343, 'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $syria->id, 'name_en' => 'Homs',        'name_ar' => 'حمص',        'lat' => 34.7308, 'lng' => 36.7092, 'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $syria->id, 'name_en' => 'Latakia',     'name_ar' => 'اللاذقية',    'lat' => 35.5316, 'lng' => 35.7761, 'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $syria->id, 'name_en' => 'Hama',        'name_ar' => 'حماة',        'lat' => 35.1318, 'lng' => 36.7578, 'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $syria->id, 'name_en' => 'Deir ez-Zor', 'name_ar' => 'دير الزور',   'lat' => 35.3333, 'lng' => 40.1500, 'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $syria->id, 'name_en' => 'Raqqa',       'name_ar' => 'الرقة',       'lat' => 35.9500, 'lng' => 39.0167, 'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $syria->id, 'name_en' => 'Daraa',       'name_ar' => 'درعا',        'lat' => 32.6189, 'lng' => 36.1069, 'created_at' => now(), 'updated_at' => now()],
                ['country_id' => $syria->id, 'name_en' => 'Qamishli',    'name_ar' => 'القامشلي',    'lat' => 37.0420, 'lng' => 41.2277, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
