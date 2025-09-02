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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_profile_id')
                ->nullable()
                ->constrained('driver_profiles')
                ->onDelete('cascade');

            $table->foreignId('vehicle_type_id')
                ->nullable()
                ->constrained('vehicle_types')
                ->onDelete('set null');

            $table->string('make', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->year('year')->nullable();
            $table->string('color', 30)->nullable();
            $table->string('license_plate', 20)->nullable()->unique();  // License plate must be unique
            $table->tinyInteger('seats')->unsigned()->nullable()->default(4);  // Number of seats
            $table->string('image_url')->nullable();  // Vehicle image URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
