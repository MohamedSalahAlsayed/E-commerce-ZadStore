<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            ['name' => 'Apple', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg'],
            ['name' => 'Samsung', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/2/24/Samsung_Logo.svg'],
            ['name' => 'Nike', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg'],
            ['name' => 'Sony', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/c/ca/Sony_logo.svg'],
            ['name' => 'Adidas', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/2/20/Adidas_Logo.svg'],
            ['name' => 'Dell', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/1/18/Dell_logo_2016.svg'],
            ['name' => 'HP', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/ad/HP_logo_2012.svg'],
            ['name' => 'Lenovo', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/b/b8/Lenovo_logo_2015.svg'],
            ['name' => 'LG', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/b/bf/LG_logo_%282015%29.svg'],
            ['name' => 'Asus', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/ASUS_Logo.svg'],
            ['name' => 'Huawei', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/0/00/Huawei_Logo.svg'],
            ['name' => 'Xiaomi', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/2/29/Xiaomi_logo.svg'],
            ['name' => 'Puma', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/ac/Puma_Logo.svg'],
            ['name' => 'Gucci', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/7/79/1960s_Gucci_Logo.svg'],
            ['name' => 'Zara', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/f/fd/Zara_Logo.svg'],
            ['name' => 'H&M', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/5/53/H%26M-Logo.svg'],
            ['name' => 'Rolex', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/f/f7/Rolex_logo.svg'],
            ['name' => 'Canon', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Canon_wordmark.svg'],
            ['name' => 'Nikon', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/b/b5/Nikon_logo.svg'],
            ['name' => 'Panasonic', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/1/18/Panasonic_logo_%28Blue%29.svg'],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['name' => $brand['name']],
                [
                    'logo' => $brand['logo'],
                ]
            );
        }
    }
}
