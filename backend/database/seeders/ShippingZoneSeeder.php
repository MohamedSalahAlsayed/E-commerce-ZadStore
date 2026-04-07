<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingZone;

class ShippingZoneSeeder extends Seeder
{
    public function run()
    {
        $zones = [
            ['name' => 'القاهرة', 'fee' => 50, 'is_active' => true],
            ['name' => 'الجيزة', 'fee' => 60, 'is_active' => true],
            ['name' => 'الإسكندرية', 'fee' => 80, 'is_active' => true],
            ['name' => 'المنصورة (الدقهلية)', 'fee' => 90, 'is_active' => true],
            ['name' => 'طنطا (الغربية)', 'fee' => 90, 'is_active' => true],
            ['name' => 'الزقازيق (الشرقية)', 'fee' => 90, 'is_active' => true],
            ['name' => 'بورسعيد', 'fee' => 100, 'is_active' => true],
            ['name' => 'الإسماعيلية', 'fee' => 100, 'is_active' => true],
            ['name' => 'السويس', 'fee' => 100, 'is_active' => true],
            ['name' => 'أسيوط', 'fee' => 150, 'is_active' => true],
            ['name' => 'سوهاج', 'fee' => 150, 'is_active' => true],
            ['name' => 'الأقصر', 'fee' => 200, 'is_active' => true],
            ['name' => 'أسوان', 'fee' => 220, 'is_active' => true],
        ];

        foreach ($zones as $zone) {
            ShippingZone::firstOrCreate(
                ['name' => $zone['name']],
                $zone
            );
        }
    }
}
