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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('store_name');
            $table->string('phone');
            $table->string('store_category')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('commercial_record')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('documents_paths')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
