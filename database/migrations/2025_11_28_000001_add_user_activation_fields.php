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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending')->after('role');
            $table->string('activation_token', 64)->nullable()->after('status');
            $table->timestamp('token_expiry')->nullable()->after('activation_token');
            $table->timestamp('email_verified_at')->nullable()->after('token_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'activation_token')) {
                $table->dropColumn('activation_token');
            }
            if (Schema::hasColumn('users', 'token_expiry')) {
                $table->dropColumn('token_expiry');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }
};
