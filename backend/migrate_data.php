<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = [
    'users',
    'categories',
    'brands',
    'products',
    'product_images',
    'orders',
    'order_items',
    'banners',
    'coupons',
    'contact_messages',
    'reviews',
    'shipping_zones',
    'store_settings',
    'merchants',
    'carts',
    'favorites',
    'blog_posts',
    'user_notifications',
    'suppliers',
    'purchases',
    'purchase_items',
    'personal_access_tokens'
];

DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($tables as $table) {
    echo "Migrating table: $table...\n";
    
    // Check if table exists in both
    if (Schema::connection('sqlite')->hasTable($table) && Schema::connection('mysql')->hasTable($table)) {
        // Clear MySQL table first
        DB::connection('mysql')->table($table)->truncate();
        
        // Chunk data from SQLite and insert into MySQL
        DB::connection('sqlite')->table($table)->orderBy('id')->chunk(100, function ($rows) use ($table) {
            $data = json_decode(json_encode($rows), true);
            DB::connection('mysql')->table($table)->insert($data);
        });
        
        echo "Done.\n";
    } else {
        echo "Skipping $table (not found in one of the databases).\n";
    }
}

DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\nMigration finished successfully!\n";
