<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adds the is_banned column right after the balance column
            $table->boolean('is_banned')->default(false)->after('balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverses the change if we ever need to rollback
            $table->dropColumn('is_banned');
        });
    }
};