<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Faker\Factory as Faker;

class ECommerceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');

        $categories = [
            'إلكترونيات', 'موبايلات ذكية', 'ملابس رجالية', 'ملابس نسائية', 
            'أحذية رياضية', 'مستلزمات منزلية', 'أجهزة لابتوب', 'عطور فرنسية', 
            'ألعاب فيديو', 'ساعات ذكية'
        ];

        Product::query()->delete();
        Category::query()->delete();
        Brand::query()->delete();

        $categoryIds = [];
        foreach ($categories as $cat) {
            $category = Category::create([
                'name' => $cat,
                'is_active' => true,
                'image' => "https://picsum.photos/seed/" . urlencode($cat) . "/200"
            ]);
            $categoryIds[] = $category->id;
        }

        $brands = ['Apple', 'Samsung', 'Nike', 'Sony', 'Adidas', 'Dell', 'HP', 'Lenovo', 'LG', 'Asus'];
        $brandIds = [];
        foreach ($brands as $brandName) {
            $brand = Brand::create([
                'name' => $brandName,
                'logo' => "https://picsum.photos/seed/" . urlencode($brandName) . "/100"
            ]);
            $brandIds[] = $brand->id;
        }

        for ($i = 0; $i < 100; $i++) {
            $price = $faker->randomFloat(2, 50, 5000);
            Product::create([
                'category_id' => $faker->randomElement($categoryIds),
                'brand_id' => $faker->randomElement($brandIds),
                'title' => 'منتج ' . $faker->words(3, true),
                'description' => 'هذا منتج رائع عالي الجودة ومميز من الفئة الفاخرة مصمم ليناسب احتياجاتك اليومية بشكل كامل بكل احترافية بتصميم ديناميكي.',
                'price' => $price,
                'discount_percentage' => $faker->randomElement([0, 0, 0, 10, 15, 20, 50]),
                'stock' => $faker->numberBetween(0, 150),
                'rating' => $faker->randomFloat(1, 3.5, 5.0),
                'thumbnail' => "https://picsum.photos/seed/product_" . rand(1, 1000) . "/400/400",
                'is_flash_deal' => $faker->boolean(20),
            ]);
        }
    }
}
