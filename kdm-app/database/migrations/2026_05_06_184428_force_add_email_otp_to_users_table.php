<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add them if they don't exist to prevent crashes
            if (!Schema::hasColumn('users', 'email_otp')) {
                $table->string('email_otp')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'email_otp_expiry')) {
                $table->timestamp('email_otp_expiry')->nullable()->after('email_otp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_otp', 'email_otp_expiry']);
        });
    }
};