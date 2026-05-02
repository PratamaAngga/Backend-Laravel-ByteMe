<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('sessions')) {
            return;
        }

        $connection = Schema::getConnection()->getDriverName();

        if ($connection === 'pgsql') {
            DB::statement("ALTER TABLE sessions ALTER COLUMN user_id TYPE varchar USING user_id::varchar;");
        } else {
            Schema::table('sessions', function (Blueprint $table) {
                $table->string('user_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('sessions')) {
            return;
        }

        $connection = Schema::getConnection()->getDriverName();

        if ($connection === 'pgsql') {
            DB::statement("ALTER TABLE sessions ALTER COLUMN user_id TYPE bigint USING user_id::bigint;");
        } else {
            Schema::table('sessions', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->change();
            });
        }
    }
};