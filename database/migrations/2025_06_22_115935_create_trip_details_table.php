<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');

            $table->integer('distance_meters')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->string('distance_text')->nullable();

            $table->integer('duration_seconds')->nullable();
            $table->decimal('duration_min', 5, 2)->nullable();
            $table->string('duration_text')->nullable();

            $table->text('user_note')->nullable();
            $table->text('driver_note')->nullable();

            // $table->float('user_rating', 3, 2)->nullable();
            // $table->float('driver_rating', 3, 2)->nullable(); // optional

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_details');
    }
};
