<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Brands
        $brands = [
            'Samsung', 'Apple', 'Sony', 'Nike', 'Adidas', 'Huawei', 'HP', 'Dell', 'Casio', 'Rolex', 
            'Xiaomi', 'Oppo', 'ZARA', 'H&M', 'Canon', 'LG', 'Philips', 'Bose'
        ];

        $brandIds = [];
        foreach ($brands as $b) {
            $brand = Brand::firstOrCreate(['name' => $b], [
                'slug' => Str::slug($b),
                'is_active' => true
            ]);
            $brandIds[$b] = $brand->id;
        }

        // 2. Define Categories and their products
        $data = [
            'Mobiles' => [
                'name_ar' => 'موبايلات',
                'products' => [
                    ['title' => 'iPhone 15 Pro Max', 'price' => 65000, 'brand' => 'Apple', 'img' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?q=80&w=500'],
                    ['title' => 'Samsung Galaxy S24 Ultra', 'price' => 58000, 'brand' => 'Samsung', 'img' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?q=80&w=500'],
                    ['title' => 'Google Pixel 8 Pro', 'price' => 45000, 'brand' => 'Google', 'img' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?q=80&w=500'],
                    ['title' => 'Xiaomi 14 Ultra', 'price' => 42000, 'brand' => 'Xiaomi', 'img' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=500'],
                    ['title' => 'Huawei P60 Pro', 'price' => 38000, 'brand' => 'Huawei', 'img' => 'https://images.unsplash.com/photo-1567581935884-3349723552ca?q=80&w=500'],
                    ['title' => 'OnePlus 12', 'price' => 35000, 'brand' => 'OnePlus', 'img' => 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?q=80&w=500'],
                    ['title' => 'iPhone 14', 'price' => 45000, 'brand' => 'Apple', 'img' => 'https://images.unsplash.com/photo-1663499482523-1c0c1bae4ce1?q=80&w=500'],
                    ['title' => 'Samsung A54', 'price' => 15000, 'brand' => 'Samsung', 'img' => 'https://images.unsplash.com/photo-1610945264803-c22b62d2a7b3?q=80&w=500'],
                    ['title' => 'Realme 12 Pro+', 'price' => 18000, 'brand' => 'Realme', 'img' => 'https://images.unsplash.com/photo-1533228891704-94e7b39589d0?q=80&w=500'],
                    ['title' => 'Oppo Reno 11', 'price' => 20000, 'brand' => 'Oppo', 'img' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=500'],
                ]
            ],
            'Laptops' => [
                'name_ar' => 'لابتوبات',
                'products' => [
                    ['title' => 'MacBook Pro M3', 'price' => 95000, 'brand' => 'Apple', 'img' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=500'],
                    ['title' => 'Dell XPS 15', 'price' => 85000, 'brand' => 'Dell', 'img' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?q=80&w=500'],
                    ['title' => 'HP Spectre x360', 'price' => 75000, 'brand' => 'HP', 'img' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?q=80&w=500'],
                    ['title' => 'Asus ROG Zephyrus', 'price' => 88000, 'brand' => 'Asus', 'img' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?q=80&w=500'],
                    ['title' => 'Lenovo Legion 5', 'price' => 55000, 'brand' => 'Lenovo', 'img' => 'https://images.unsplash.com/photo-1589578233441-83f681bb441c?q=80&w=500'],
                    ['title' => 'Microsoft Surface Laptop 5', 'price' => 62000, 'brand' => 'Microsoft', 'img' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=500'],
                    ['title' => 'Acer Predator Helios', 'price' => 52000, 'brand' => 'Acer', 'img' => 'https://images.unsplash.com/photo-1544006659-f0b21f04cb1b?q=80&w=500'],
                    ['title' => 'Samsung Galaxy Book 3', 'price' => 70000, 'brand' => 'Samsung', 'img' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=500'],
                    ['title' => 'Huawei MateBook X Pro', 'price' => 68000, 'brand' => 'Huawei', 'img' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=500'],
                    ['title' => 'MacBook Air M2', 'price' => 55000, 'brand' => 'Apple', 'img' => 'https://images.unsplash.com/photo-1611186871348-b1ec696e52c9?q=80&w=500'],
                ]
            ],
            'Men Fashion' => [
                'name_ar' => 'ملابس رجالي',
                'products' => [
                    ['title' => 'Nike Sport T-Shirt', 'price' => 1200, 'brand' => 'Nike', 'img' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=500'],
                    ['title' => 'Adidas Hoodie', 'price' => 2500, 'brand' => 'Adidas', 'img' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?q=80&w=500'],
                    ['title' => 'Leather Jacket', 'price' => 4500, 'brand' => 'ZARA', 'img' => 'https://images.unsplash.com/photo-1551028711-0305df2f9fb3?q=80&w=500'],
                    ['title' => 'Denim Jeans', 'price' => 1800, 'brand' => 'Levis', 'img' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=500'],
                    ['title' => 'Formal Suit', 'price' => 8500, 'brand' => 'Hugo Boss', 'img' => 'https://images.unsplash.com/photo-1594932224827-c00224673894?q=80&w=500'],
                    ['title' => 'Summer Linen Shirt', 'price' => 1500, 'brand' => 'H&M', 'img' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?q=80&w=500'],
                    ['title' => 'Winter Wool Coat', 'price' => 6500, 'brand' => 'ZARA', 'img' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=500'],
                    ['title' => 'Casual Chinos', 'price' => 1400, 'brand' => 'Pull&Bear', 'img' => 'https://images.unsplash.com/photo-1473966968600-fa804b86827b?q=80&w=500'],
                    ['title' => 'Polo Ralph Lauren T-Shirt', 'price' => 3200, 'brand' => 'Polo', 'img' => 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?q=80&w=500'],
                    ['title' => 'Shorts Swimwear', 'price' => 900, 'brand' => 'Nike', 'img' => 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?q=80&w=500'],
                ]
            ],
            'Watches' => [
                'name_ar' => 'ساعات',
                'products' => [
                    ['title' => 'Rolex Submariner', 'price' => 450000, 'brand' => 'Rolex', 'img' => 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=500'],
                    ['title' => 'Casio G-Shock', 'price' => 5500, 'brand' => 'Casio', 'img' => 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?q=80&w=500'],
                    ['title' => 'Apple Watch Series 9', 'price' => 22000, 'brand' => 'Apple', 'img' => 'https://images.unsplash.com/photo-1544117518-30dd5ff7a4b0?q=80&w=500'],
                    ['title' => 'Samsung Galaxy Watch 6', 'price' => 12000, 'brand' => 'Samsung', 'img' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?q=80&w=500'],
                    ['title' => 'Tissot Gentleman', 'price' => 28000, 'brand' => 'Tissot', 'img' => 'https://images.unsplash.com/photo-1614164185128-e4ec99c436d7?q=80&w=500'],
                    ['title' => 'Seiko 5 Sports', 'price' => 15000, 'brand' => 'Seiko', 'img' => 'https://images.unsplash.com/photo-1587836374828-4dbaba94ee0e?q=80&w=500'],
                    ['title' => 'Citizen Eco-Drive', 'price' => 12000, 'brand' => 'Citizen', 'img' => 'https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?q=80&w=500'],
                    ['title' => 'Omega Speedmaster', 'price' => 350000, 'brand' => 'Omega', 'img' => 'https://images.unsplash.com/photo-1546868871-70c1274a7d66?q=80&w=500'],
                    ['title' => 'Fossil Heritage', 'price' => 8500, 'brand' => 'Fossil', 'img' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?q=80&w=500'],
                    ['title' => 'Huawei Watch GT4', 'price' => 11000, 'brand' => 'Huawei', 'img' => 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?q=80&w=500'],
                ]
            ],
            'Shoes' => [
                'name_ar' => 'أحذية',
                'products' => [
                    ['title' => 'Nike Air Force 1', 'price' => 5200, 'brand' => 'Nike', 'img' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=500'],
                    ['title' => 'Adidas Superstar', 'price' => 4500, 'brand' => 'Adidas', 'img' => 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?q=80&w=500'],
                    ['title' => 'Puma RS-X', 'price' => 4800, 'brand' => 'Puma', 'img' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?q=80&w=500'],
                    ['title' => 'New Balance 574', 'price' => 5500, 'brand' => 'New Balance', 'img' => 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?q=80&w=500'],
                    ['title' => 'Vans Old Skool', 'price' => 3200, 'brand' => 'Vans', 'img' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?q=80&w=500'],
                    ['title' => 'Converse Chuck Taylor', 'price' => 2800, 'brand' => 'Converse', 'img' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?q=80&w=500'],
                    ['title' => 'Reebok Classic', 'price' => 3800, 'brand' => 'Reebok', 'img' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?q=80&w=500'],
                    ['title' => 'Skechers Go Walk', 'price' => 4200, 'brand' => 'Skechers', 'img' => 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?q=80&w=500'],
                    ['title' => 'Timberland Boots', 'price' => 8500, 'brand' => 'Timberland', 'img' => 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?q=80&w=500'],
                    ['title' => 'Adidas Yeezy Boost', 'price' => 12000, 'brand' => 'Adidas', 'img' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?q=80&w=500'],
                ]
            ],
            'Beauty & Perfumes' => [
                'name_ar' => 'تجميل وعطور',
                'products' => [
                    ['title' => 'Dior Sauvage Perfume', 'price' => 6500, 'brand' => 'Dior', 'img' => 'https://images.unsplash.com/photo-1544467328-341e712415d1?q=80&w=500'],
                    ['title' => 'Chanel No. 5', 'price' => 8200, 'brand' => 'Chanel', 'img' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=500'],
                    ['title' => 'L\'Oreal Face Cream', 'price' => 850, 'brand' => 'L\'Oreal', 'img' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?q=80&w=500'],
                    ['title' => 'MAC Lipstick', 'price' => 1200, 'brand' => 'MAC', 'img' => 'https://images.unsplash.com/photo-1586771107445-d3ca888129ff?q=80&w=500'],
                    ['title' => 'Maybelline Mascara', 'price' => 450, 'brand' => 'Maybelline', 'img' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?q=80&w=500'],
                    ['title' => 'Estée Lauder Serum', 'price' => 4200, 'brand' => 'Estée Lauder', 'img' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?q=80&w=500'],
                    ['title' => 'Lancôme Eye Cream', 'price' => 3800, 'brand' => 'Lancôme', 'img' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?q=80&w=500'],
                    ['title' => 'Viktor&Rolf Flowerbomb', 'price' => 5500, 'brand' => 'Viktor&Rolf', 'img' => 'https://images.unsplash.com/photo-1544467328-341e712415d1?q=80&w=500'],
                    ['title' => 'Giorgio Armani Code', 'price' => 4800, 'brand' => 'Armani', 'img' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=500'],
                    ['title' => 'Versace Eros', 'price' => 3500, 'brand' => 'Versace', 'img' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=500'],
                ]
            ],
            'Home Appliances' => [
                'name_ar' => 'أجهزة منزلية',
                'products' => [
                    ['title' => 'Samsung Fridge 500L', 'price' => 45000, 'brand' => 'Samsung', 'img' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=500'],
                    ['title' => 'LG Washing Machine 9kg', 'price' => 28000, 'brand' => 'LG', 'img' => 'https://images.unsplash.com/photo-1582730147043-4dc9791448b3?q=80&w=500'],
                    ['title' => 'Philips Air Fryer XL', 'price' => 8500, 'brand' => 'Philips', 'img' => 'https://images.unsplash.com/photo-1585232351009-aa87416fca90?q=80&w=500'],
                    ['title' => 'Bose QuietComfort Headphones', 'price' => 18000, 'brand' => 'Bose', 'img' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=500'],
                    ['title' => 'Sony 65 inch 4K TV', 'price' => 35000, 'brand' => 'Sony', 'img' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?q=80&w=500'],
                    ['title' => 'Nespresso Coffee Machine', 'price' => 6500, 'brand' => 'Nespresso', 'img' => 'https://images.unsplash.com/photo-1520970014086-2208df4755af?q=80&w=500'],
                    ['title' => 'Dyson V15 Vacuum', 'price' => 32000, 'brand' => 'Dyson', 'img' => 'https://images.unsplash.com/photo-1558317374-067fb5f30001?q=80&w=500'],
                    ['title' => 'NutriBullet Blender', 'price' => 4500, 'brand' => 'NutriBullet', 'img' => 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?q=80&w=500'],
                    ['title' => 'Xiaomi Air Purifier', 'price' => 7500, 'brand' => 'Xiaomi', 'img' => 'https://images.unsplash.com/photo-1585232351009-aa87416fca90?q=80&w=500'],
                    ['title' => 'Apple iPad Air', 'price' => 38000, 'brand' => 'Apple', 'img' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?q=80&w=500'],
                ]
            ],
        ];

        foreach ($data as $catName => $catData) {
            $category = Category::firstOrCreate(['slug' => Str::slug($catName)], [
                'name' => $catData['name_ar'],
                'description' => 'أفضل منتجات الـ ' . $catData['name_ar'],
                'is_active' => true
            ]);

            foreach ($catData['products'] as $p) {
                // Ensure brand exists
                $brandId = $brandIds[$p['brand']] ?? null;
                if (!$brandId) {
                    $brand = Brand::firstOrCreate(['name' => $p['brand']], [
                        'slug' => Str::slug($p['brand']),
                        'is_active' => true
                    ]);
                    $brandId = $brand->id;
                    $brandIds[$p['brand']] = $brandId;
                }

                Product::updateOrCreate(['title' => $p['title']], [
                    'sku' => 'ZAD-' . strtoupper(Str::random(8)),
                    'category_id' => $category->id,
                    'brand_id' => $brandId,
                    'description' => 'هذا المنتج هو واحد من أفضل الخيارات المتاحة في السوق، يتميز بجودة عالية وتصميم عصري.',
                    'price' => $p['price'],
                    'discount_percentage' => rand(5, 25),
                    'stock' => rand(10, 50),
                    'weight_kg' => rand(1, 500) / 100, // From 0.01 to 5.00 kg
                    'thumbnail' => $p['img'],
                    'is_flash_deal' => rand(0, 1),
                    'is_best_seller' => rand(0, 1),
                    'is_active' => true,
                    'rating' => rand(40, 50) / 10,
                    'purchase_price' => $p['price'] * 0.7,
                    'meta_title' => $p['title'],
                    'meta_description' => 'اشتري ' . $p['title'] . ' الآن من زاد ستور بأفضل سعر في مصر.',
                    'meta_keywords' => $p['title'] . ', زاد ستور, تسوق اونلاين'
                ]);
            }
        }
    }
}
