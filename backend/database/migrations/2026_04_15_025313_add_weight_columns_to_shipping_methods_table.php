<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->boolean('is_weight_aware')->default(false)->after('fee');
            $table->decimal('base_weight', 8, 2)->default(1.00)->after('is_weight_aware');
            $table->decimal('extra_weight_fee', 8, 2)->default(0)->after('base_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->dropColumn(['is_weight_aware', 'base_weight', 'extra_weight_fee']);
        });
    }
};
