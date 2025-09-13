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
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedBigInteger('cancel_reason_id')->nullable()->after('cancelled_by');
            $table->string('cancel_reason_note', 500)->nullable()->after('cancel_reason_id');
            $table->foreign('cancel_reason_id')->references('id')->on('cancel_reasons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['cancel_reason_id']);
            $table->dropColumn('cancel_reason_id');
            $table->dropColumn('cancel_reason_note');
        });
    }
};
