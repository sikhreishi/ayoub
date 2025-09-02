<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'from_user_id')) {
                $table->dropColumn('from_user_id');
            }
            if (Schema::hasColumn('wallet_transactions', 'to_user_id')) {
                $table->dropColumn('to_user_id');
            }
            if (Schema::hasColumn('wallet_transactions', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('wallet_transactions', 'reference')) {
                $table->dropColumn('reference');
            }
            if (Schema::hasColumn('wallet_transactions', 'transactions_from_admin')) {
                $table->dropColumn('transactions_from_admin');
            }
            if (Schema::hasColumn('wallet_transactions', 'type')) {
                $table->dropColumn('type');
            }
            $table->enum('reference_type', ['ride_commission', 'recharge_code', 'admin_adjustment'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->string('status')->nullable();
            $table->string('reference')->nullable();
            $table->boolean('transactions_from_admin')->default(false);
            $table->string('type')->nullable();
            $table->enum('reference_type', ['ride_commission', 'recharge_code'])->change();
        });
    }
};
