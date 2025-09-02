<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_lng', 10, 7);
            $table->string('pickup_name')->nullable();
            $table->decimal('dropoff_lat', 10, 7);
            $table->decimal('dropoff_lng', 10, 7);
            $table->string('dropoff_name')->nullable();

            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->decimal('driver_accept_lat', 10, 7)->nullable();
            $table->decimal('driver_accept_lng', 10, 7)->nullable();

            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('estimated_fare', 8, 2)->nullable();
            $table->decimal('final_fare', 8, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('payment_method', ['cash', 'card'])->nullable();

            $table->timestamp('cancelled_at')->nullable();
            $table->enum('cancelled_by', ['user', 'driver'])->nullable();
            $table->index('driver_id');
            $table->index(['user_id', 'requested_at']);
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
