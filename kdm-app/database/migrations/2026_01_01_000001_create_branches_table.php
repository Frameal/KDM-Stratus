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
    Schema::create('branches', function (Blueprint $table) {
        $table->id();
        $table->string('name'); 
        $table->string('address')->nullable();
        $table->string('operating_hours')->default('24/7');
        $table->integer('total_pcs')->default(0);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
