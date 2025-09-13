<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CancelReasonsSeeder extends Seeder
{

    public function run(): void
    {
         $reasons = [
        ['reason_en' => 'Change of plans', 'reason_ar' => 'تغيير الخطط', 'is_active' => true],
        ['reason_en' => 'Found a better option', 'reason_ar' => 'وجدت خيارًا أفضل', 'is_active' => true],
        ['reason_en' => 'Scheduling conflict', 'reason_ar' => 'تعارض في الجدول الزمني', 'is_active' => true],
        ['reason_en' => 'Personal reasons', 'reason_ar' => 'أسباب شخصية', 'is_active' => true],
        ['reason_en' => 'Health issues', 'reason_ar' => 'مشاكل صحية', 'is_active' => true],
        ['reason_en' => 'Weather conditions', 'reason_ar' => 'ظروف الطقس', 'is_active' => true],
        ['reason_en' => 'Other', 'reason_ar' => 'أخرى', 'is_active' => true],
    ];
    \DB::table('cancel_reasons')->insert($reasons);
    }
}
