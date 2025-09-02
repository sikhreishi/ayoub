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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets', 'id')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('transaction_type', ['deduction', 'recharge']);
            $table->enum('reference_type', ['ride_commission', 'recharge_code']);
            $table->integer('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
