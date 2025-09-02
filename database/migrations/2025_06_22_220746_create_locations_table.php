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
        Schema::create('locations', function (Blueprint $table) {
           $table->id();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->onDelete('cascade'); // Add trip reference
            $table->tinyInteger('type')->default(0); // 0 = user, 1 = driver
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('speed', 6, 2)->nullable(); // meters/second or km/h depending on logic
            $table->float('heading')->nullable(); 
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
            $table->index(['type', 'recorded_at']); // helps with time-based queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
