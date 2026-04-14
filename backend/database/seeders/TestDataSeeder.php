<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        $brands = Brand::all();

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please seed categories first.');
            return;
        }

        $this->command->info('Seeding 10 products for each category...');

        foreach ($categories as $category) {
            for ($i = 1; $i <= 10; $i++) {
                $brand = $brands->random() ?? null;
                $price = rand(100, 5000);
                $discount = rand(0, 30);
                $title = $this->getProductName($category->name, $i);

                Product::create([
                    'sku' => strtoupper(Str::random(4)) . rand(1000, 9999),
                    'category_id' => $category->id,
                    'brand_id' => $brand ? $brand->id : null,
                    'title' => $title,
                    'description' => "هذا وصف تجريبي للمنتج {$title}. يتمتع هذا المنتج بجودة عالية وتصميم عصري يناسب احتياجاتكم. المميزات تشمل المتانة، الأداء العالي، والضمان المعتمد.",
                    'price' => $price,
                    'discount_percentage' => $discount,
                    'stock' => rand(5, 100),
                    'thumbnail' => $this->getProductImage($category->name, $i),
                    'is_flash_deal' => rand(0, 5) === 0,
                    'is_best_seller' => rand(0, 3) === 0,
                    'is_active' => true,
                    'rating' => rand(3, 5),
                    'purchase_price' => $price * 0.7,
                ]);
            }
        }

        $this->command->info('Seeding 5 banners...');
        for ($b = 1; $b <= 5; $b++) {
            Banner::create([
                'title' => "عرض خاص {$b}",
                'image' => "https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1200&q=80",
                'link' => "/products",
                'is_active' => true,
            ]);
        }

        $this->command->info('Seeding completed successfully!');
    }

    private function getProductName($categoryName, $index)
    {
        $names = [
            'إلكترونيات' => ['شاحن سريع', 'سماعات لاسلكية', 'باور بانك', 'مكبر صوت بلوتوث', 'كاميرا مراقبة', 'ماوس احترافي', 'لوحة مفاتيح مضيئة', 'منصة شحن', 'محول ذكي', 'مروحة تبريد'],
            'موبايلات ذكية' => ['هاتف ذكي برو', 'موبايل فلاجشيب', 'هاتف اقتصادي', 'موبايل أندرويد اكس', 'ايفون ريفربشد', 'هاتف التصوير المتقدم', 'موبايل الألعاب', 'هاتف بشاشة كبيرة', 'موبايل بلس', 'هاتف نانو'],
            'ملابس رجالية' => ['تيشيرت قطني', 'بنطلون جينز أزرق', 'قميص كاجوال', 'جاكيت شتوي', 'بدلة رسمية', 'هودي رياضي', 'قميص صيفي', 'بنطلون قماش', 'شورت مريح', 'معطف طويل'],
            'ملابس نسائية' => ['فستان سهرة', 'بلوزة حرير', 'تنورة ميدي', 'كارديجان صوف', 'عباءة مطرزة', 'تيشيرت بناتي', 'بنطلون قماش واسع', 'جاكيت جلد نسائي', 'ملابس رياضية', 'فستان كاجوال'],
            'أحذية رياضية' => ['حذاء جري خفيف', 'حذاء كرة قدم', 'سنيكرز عصري', 'حذاء تدريب', 'سنيكرز مرتفع', 'حذاء المشي المريح', 'حذاء تنس', 'سنيكرز جلدي', 'حذاء رياضة احترافي', 'سنيكرز أبيض'],
            'مستلزمات منزلية' => ['طقم أواني', 'خلاط كهربائي', 'مكواة بخار', 'ميزان رقمي', 'طقم سكاكين', 'غلاية ماء', 'طقم مناشف قطنية', 'منظفة بخار', 'طقم قهوة سيراميك', 'فرامة خضروات'],
            'أجهزة لابتوب' => ['لابتوب ألعاب برو', 'جهاز نوت بوك خفيف', 'لابتوب أعمال', 'جهاز ماك بوك تجريبي', 'لابتوب للطلاب', 'جهاز بيكسل برو', 'لابتوب تاتش', 'لابتوب 2 في 1', 'جهاز مكتبي متطور', 'لابتوب بشاشة 4K'],
            'عطور فرنسية' => ['عطر كلاسيك باريس', 'عطر الزهور البرية', 'عطر المسك والعود', 'عطر الصيف المنعش', 'عطر ليلي ساحر', 'عطر رجالي فخم', 'عطر نسائي رقيق', 'عطر ميس باريس', 'عطر رويال', 'عطر الفانيليا هارت'],
            'ألعاب فيديو' => ['منصة العاب برو', 'يد تحكم لاسلكية', 'لعبة مغامرات', 'سماعات محيطية', 'كرسي العاب مريح', 'لعبة سباق سيارات', 'شريط لعبة أكشن', 'قاعدة تبريد للمنصة', 'لعبة كرة قدم 2024', 'حقيبة للمنصة'],
            'ساعات ذكية' => ['ساعة ذكية رياضية', 'ساعة ذكية جلد', 'ساعة تتبع اللياقة', 'ساعة ذكية بشاشة أموليد', 'ساعة كلاسيكية ذكية', 'ساعة أطفال ذكية', 'ساعة مقاومة للماء', 'ساعة ذكية برو', 'ساعة تيتانيوم', 'ساعة ذكية بلس'],
        ];

        return $names[$categoryName][$index - 1] ?? "منتج {$categoryName} {$index}";
    }

    private function getProductImage($categoryName, $index)
    {
        $images = [
            'إلكترونيات' => "https://images.unsplash.com/photo-1526733170371-09888be6a9dd?auto=format&fit=crop&w=800&q=80",
            'موبايلات ذكية' => "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800&q=80",
            'ملابس رجالية' => "https://images.unsplash.com/photo-1593030761757-71fae45fa0e7?auto=format&fit=crop&w=800&q=80",
            'ملابس نسائية' => "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80",
            'أحذية رياضية' => "https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80",
            'مستلزمات منزلية' => "https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=800&q=80",
            'أجهزة لابتوب' => "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80",
            'عطور فرنسية' => "https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=800&q=80",
            'ألعاب فيديو' => "https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?auto=format&fit=crop&w=800&q=80",
            'ساعات ذكية' => "https://images.unsplash.com/photo-1508685096489-7a316bd4743e?auto=format&fit=crop&w=800&q=80",
        ];

        return $images[$categoryName] ?? "https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80";
    }
}
