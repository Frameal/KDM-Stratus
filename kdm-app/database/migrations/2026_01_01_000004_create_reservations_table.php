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
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users');
        $table->foreignId('pc_id')->constrained('pcs');
        $table->decimal('fee_deducted', 8, 2)->default(5.00);
        $table->enum('status', ['active', 'completed', 'forfeited'])->default('active');
        $table->timestamps(); // "created_at" acts as the start of the 15-minute timer
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
