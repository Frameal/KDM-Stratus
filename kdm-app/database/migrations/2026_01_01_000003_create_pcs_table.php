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
    Schema::create('pcs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
        $table->string('pc_number'); // e.g., "PC-01"
        $table->enum('status', ['free', 'occupied', 'broken', 'reserved'])->default('free');
        $table->foreignId('current_user_id')->nullable()->constrained('users');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcs');
    }
};
