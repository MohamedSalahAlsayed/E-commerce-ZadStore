<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->json('notifications_prefs')->nullable();
            $table->decimal('wallet_balance', 10, 2)->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);
        });
    }

    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notifications_prefs', 'wallet_balance', 'total_earnings']);
        });
    }
};
