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
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('id_card_front')->nullable();
            $table->string('id_card_back')->nullable();
            $table->string('license_front')->nullable();
            $table->string('license_back')->nullable();
            $table->string('vehicle_license_front')->nullable();
            $table->string('vehicle_license_back')->nullable();
            $table->string('interior_front_seats')->nullable();
            $table->string('interior_back_seats')->nullable();
            $table->string('exterior_front_side')->nullable();
            $table->string('exterior_back_side')->nullable();
            $table->boolean('is_driver_verified')->default(false);
            $table->boolean('registration_complete')->default(false);  
            $table->text('verification_note')->nullable();
            $table->float('reputation_score')->default(5.0);
            $table->text('vehicle_info')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_profiles');
    }
};
