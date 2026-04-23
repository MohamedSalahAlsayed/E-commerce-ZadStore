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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('return_type')->nullable(); // 'full' or 'partial'
            $table->json('return_target_items')->nullable(); // JSON array of item IDs
            $table->string('return_reason_code')->nullable();
            $table->text('return_reason')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_returned')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'return_type',
                'return_target_items',
                'return_reason_code',
                'return_reason'
            ]);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('is_returned');
        });
    }
};
