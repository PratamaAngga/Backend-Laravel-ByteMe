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
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('email')->unique()->after('username');
            $table->string('password')->after('email');
            $table->string('remember_token', 100)->nullable()->after('password');
            $table->timestamp('email_verified_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['email', 'password', 'remember_token', 'email_verified_at']);
        });
    }
};
