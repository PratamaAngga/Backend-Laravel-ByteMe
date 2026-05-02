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
            // Add status column if not exists
            if (! Schema::hasColumn('profiles', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
        });

        // Migrate data from is_banned to status
        if (Schema::hasColumn('profiles', 'is_banned')) {
            \DB::statement(
                "UPDATE profiles SET status = CASE WHEN is_banned = true THEN 'banned' ELSE 'active' END WHERE status = 'active'"
            );
        }

        // Drop is_banned column if exists
        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'is_banned')) {
                $table->dropColumn('is_banned');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Add back is_banned if exists (for rollback)
            if (! Schema::hasColumn('profiles', 'is_banned')) {
                $table->boolean('is_banned')->default(false)->after('role');
            }
        });

        // Migrate data back from status to is_banned
        \DB::statement(
            "UPDATE profiles SET is_banned = CASE WHEN status IN ('banned', 'suspended') THEN true ELSE false END"
        );

        // Drop status column
        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
