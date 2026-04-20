<?php

namespace Database\Seeders;

use App\Models\Governorate;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingSeeder extends Seeder
{
    public function run()
    {
        // Clean up existing data to ensure a fresh start for the audit
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Governorate::truncate();
        ShippingMethod::truncate();
        ShippingZone::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Shipping Zones
        $zones = [
            ['name' => 'القاهرة والجيزة'],
            ['name' => 'الإسكندرية'],
            ['name' => 'محافظات الدلتا'],
            ['name' => 'محافظات الصعيد'],
            ['name' => 'محافظات القناة'],
            ['name' => 'المحافظات الحدودية'],
        ];

        $zoneIds = [];
        foreach ($zones as $z) {
            $zone = ShippingZone::create([
                'name' => $z['name'],
                'is_active' => true
            ]);
            $zoneIds[$z['name']] = $zone->id;
        }

        // 2. Create Shipping Methods for each zone
        foreach ($zoneIds as $name => $id) {
            // Standard Shipping
            ShippingMethod::create([
                'shipping_zone_id' => $id,
                'name_ar' => 'شحن قياسي',
                'name_en' => 'Standard Shipping',
                'fee' => $this->getFeeForZone($name),
                'is_weight_aware' => true,
                'base_weight' => 5,
                'extra_weight_fee' => 10,
                'delivery_time' => '2-5 أيام عمل',
                'is_active' => true
            ]);

            // Express Shipping
            ShippingMethod::create([
                'shipping_zone_id' => $id,
                'name_ar' => 'شحن سريع (إكسبريس)',
                'name_en' => 'Express Shipping',
                'fee' => $this->getFeeForZone($name) + 25,
                'is_weight_aware' => true,
                'base_weight' => 5,
                'extra_weight_fee' => 15,
                'delivery_time' => '24-48 ساعة',
                'is_active' => true
            ]);
        }

        // 3. Create Governorates and link to Zones
        $govs = [
            ['name_ar' => 'القاهرة', 'name_en' => 'Cairo', 'zone' => 'القاهرة والجيزة'],
            ['name_ar' => 'الجيزة', 'name_en' => 'Giza', 'zone' => 'القاهرة والجيزة'],
            ['name_ar' => 'الإسكندرية', 'name_en' => 'Alexandria', 'zone' => 'الإسكندرية'],
            ['name_ar' => 'الدقهلية', 'name_en' => 'Dakahlia', 'zone' => 'محافظات الدلتا'],
            ['name_ar' => 'البحيرة', 'name_en' => 'Beheira', 'zone' => 'محافظات الدلتا'],
            ['name_ar' => 'المنوفية', 'name_en' => 'Menofia', 'zone' => 'محافظات الدلتا'],
            ['name_ar' => 'الغربية', 'name_en' => 'Gharbia', 'zone' => 'محافظات الدلتا'],
            ['name_ar' => 'كفر الشيخ', 'name_en' => 'Kafr El Sheikh', 'zone' => 'محافظات الدلتا'],
            ['name_ar' => 'دمياط', 'name_en' => 'Damietta', 'zone' => 'محافظات الدلتا'],
            ['name_ar' => 'الشرقية', 'name_en' => 'Sharqia', 'zone' => 'محافظات الدلتا'],
            ['name_ar' => 'القليوبية', 'name_en' => 'Qalyubia', 'zone' => 'القاهرة والجيزة'],
            ['name_ar' => 'بورسعيد', 'name_en' => 'Port Said', 'zone' => 'محافظات القناة'],
            ['name_ar' => 'الإسماعيلية', 'name_en' => 'Ismailia', 'zone' => 'محافظات القناة'],
            ['name_ar' => 'السويس', 'name_en' => 'Suez', 'zone' => 'محافظات القناة'],
            ['name_ar' => 'الفيوم', 'name_en' => 'Fayoum', 'zone' => 'محافظات الصعيد'],
            ['name_ar' => 'بني سويف', 'name_en' => 'Beni Suef', 'zone' => 'محافظات الصعيد'],
            ['name_ar' => 'المنيا', 'name_en' => 'Minya', 'zone' => 'محافظات الصعيد'],
            ['name_ar' => 'أسيوط', 'name_en' => 'Assiut', 'zone' => 'محافظات الصعيد'],
            ['name_ar' => 'سوهاج', 'name_en' => 'Sohag', 'zone' => 'محافظات الصعيد'],
            ['name_ar' => 'قنا', 'name_en' => 'Qena', 'zone' => 'محافظات الصعيد'],
            ['name_ar' => 'الأقصر', 'name_en' => 'Luxor', 'zone' => 'محافظات الصعيد'],
            ['name_ar' => 'أسوان', 'name_en' => 'Aswan', 'zone' => 'محافظات الصعيد'],
            ['name_ar' => 'البحر الأحمر', 'name_en' => 'Red Sea', 'zone' => 'المحافظات الحدودية'],
            ['name_ar' => 'الوادي الجديد', 'name_en' => 'New Valley', 'zone' => 'المحافظات الحدودية'],
            ['name_ar' => 'مطروح', 'name_en' => 'Matrouh', 'zone' => 'المحافظات الحدودية'],
            ['name_ar' => 'شمال سيناء', 'name_en' => 'North Sinai', 'zone' => 'المحافظات الحدودية'],
            ['name_ar' => 'جنوب سيناء', 'name_en' => 'South Sinai', 'zone' => 'المحافظات الحدودية'],
        ];

        foreach ($govs as $g) {
            Governorate::create([
                'name_ar' => $g['name_ar'],
                'name_en' => $g['name_en'],
                'shipping_zone_id' => $zoneIds[$g['zone']]
            ]);
        }
    }

    private function getFeeForZone($zoneName)
    {
        switch ($zoneName) {
            case 'القاهرة والجيزة': return 40;
            case 'الإسكندرية': return 50;
            case 'محافظات الدلتا': return 60;
            case 'محافظات القناة': return 60;
            case 'محافظات الصعيد': return 80;
            case 'المحافظات الحدودية': return 100;
            default: return 50;
        }
    }
}
