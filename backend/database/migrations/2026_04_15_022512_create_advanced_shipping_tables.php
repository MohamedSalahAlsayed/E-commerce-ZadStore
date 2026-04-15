<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Governorates Table
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->foreignId('shipping_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // 2. Create Shipping Methods Table
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->onDelete('cascade');
            $table->string('name_ar');
            $table->string('name_en');
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('delivery_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('driver')->default('internal'); // internal, aramex, bosta, etc.
            $table->string('provider_code')->nullable();
            $table->timestamps();
        });

        // 3. Migrate data from shipping_zones to shipping_methods before dropping columns
        $zones = DB::table('shipping_zones')->get();
        foreach ($zones as $zone) {
            DB::table('shipping_methods')->insert([
                'shipping_zone_id' => $zone->id,
                'name_ar' => 'شحن عادي',
                'name_en' => 'Standard Shipping',
                'fee' => $zone->fee ?? 0,
                'delivery_time' => $zone->delivery_time ?? '3-5 days',
                'is_active' => $zone->is_active,
                'driver' => 'internal',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Update Shipping Zones (Remove legacy columns)
        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->dropColumn(['fee', 'delivery_time', 'cod_fee']);
        });

        // 5. Update Orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('governorate_id')->nullable()->after('address')->constrained()->nullOnDelete();
            $table->foreignId('shipping_method_id')->nullable()->after('governorate_id')->constrained()->nullOnDelete();
            $table->decimal('total_weight', 10, 2)->default(0)->after('total');
            $table->string('tracking_number')->nullable()->after('status');
            $table->string('shipping_provider')->nullable()->after('tracking_number');
            $table->json('shipping_metadata')->nullable()->after('shipping_provider');
        });

        // 6. Update Products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight_kg', 8, 2)->default(0)->after('stock');
            $table->decimal('width_cm', 8, 2)->default(0)->after('weight_kg');
            $table->decimal('height_cm', 8, 2)->default(0)->after('width_cm');
            $table->decimal('length_cm', 8, 2)->default(0)->after('height_cm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight_kg', 'width_cm', 'height_cm', 'length_cm']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_method_id', 'tracking_number', 'shipping_provider', 'shipping_metadata']);
        });

        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('delivery_time')->nullable();
            $table->decimal('cod_fee', 10, 2)->default(0);
        });

        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('governorates');
    }
};
