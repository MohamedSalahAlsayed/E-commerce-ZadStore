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
        $tables = ['products', 'categories', 'brands', 'blog_posts'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'meta_title')) {
                    $table->string('meta_title')->nullable()->after('id');
                }
                if (!Schema::hasColumn($table->getTable(), 'meta_description')) {
                    $table->text('meta_description')->nullable()->after('meta_title');
                }
                if (!Schema::hasColumn($table->getTable(), 'meta_keywords')) {
                    $table->text('meta_keywords')->nullable()->after('meta_description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['products', 'categories', 'brands', 'blog_posts'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords']);
            });
        }
    }
};
