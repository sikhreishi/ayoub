<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->enum('type', ['email', 'phone'])->default("phone");
            $table->boolean('verified')->default(false);
            $table->unsignedInteger('attempts')->default(0); // Number of attempts to verify the OTP
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
