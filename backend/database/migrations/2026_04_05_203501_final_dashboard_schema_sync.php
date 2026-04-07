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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->change();
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->text('admin_reply')->nullable()->after('comment');
            $table->timestamp('replied_at')->nullable()->after('admin_reply');
        });

        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->string('delivery_time')->nullable()->after('fee');
            $table->decimal('cod_fee', 10, 2)->default(0)->after('delivery_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['admin_reply', 'replied_at']);
        });

        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->dropColumn(['delivery_time', 'cod_fee']);
        });
    }
};
