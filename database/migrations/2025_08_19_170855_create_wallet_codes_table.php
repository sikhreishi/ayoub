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
        Schema::create('wallet_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('balance', 10, 2);
            $table->enum('status', ['unused', 'used'])->default('unused');
            $table->unsignedBigInteger('generated_by');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('used_by')->nullable();
            $table->foreign('used_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_codes');
    }
};
