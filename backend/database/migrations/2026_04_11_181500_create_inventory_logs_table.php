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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Admin/Staff who made the change
            $table->integer('old_stock');
            $table->integer('new_stock');
            $table->integer('change_amount');
            $table->string('type'); // 'manual', 'order_fulfillment', 'order_cancellation', 'order_return', 'purchase'
            $table->string('notes')->nullable();
            $table->json('metadata')->nullable(); // Extra info like Order ID or Purchase ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
