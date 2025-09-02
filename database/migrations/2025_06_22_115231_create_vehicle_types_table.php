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
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->decimal('start_fare', 8, 2);
            $table->decimal('day_per_km_rate', 8, 2);
            $table->decimal('night_per_km_rate', 8, 2);
            $table->decimal('day_per_minute_rate', 8, 2);
            $table->decimal('night_per_minute_rate', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->string('icon_url')->nullable();
            $table->decimal('commission_percentage', 5, 2)->default(10.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }
};
