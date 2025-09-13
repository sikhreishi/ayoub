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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->integer('trip_id')->nullable()->after('reference_id');
            $table->integer('payment_transaction_id')->nullable()->after('trip_id');
        });

        DB::statement("
            ALTER TABLE wallet_transactions
            MODIFY reference_type ENUM('admin_adjustment', 'ride_commission', 'recharge_code', 'trip') NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE wallet_transactions
            MODIFY reference_type ENUM('admin_adjustment', 'ride_commission', 'recharge_code') NOT NULL
        ");

        Schema::table('wallet_transactions', function (Blueprint $table) {
              $table->dropColumn(['trip_id', 'payment_transaction_id']);
        });
    }
};
