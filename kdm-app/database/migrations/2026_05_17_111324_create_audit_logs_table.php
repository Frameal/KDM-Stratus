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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // The admin/manager who did it
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null'); // Where it happened (Null = HQ)
            $table->string('action'); // e.g., "Cash Top-Up", "Cancel & Refund"
            $table->text('details'); // e.g., "Added ₱50 to @narpim"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
