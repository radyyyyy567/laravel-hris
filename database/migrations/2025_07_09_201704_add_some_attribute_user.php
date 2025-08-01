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
            // Additional columns based on your ERD
            $table->string('nip', 25)->nullable()->after('email');
            $table->string('role', 20)->default('user')->after('nip');
            $table->boolean('status')->default(true)->after('remember_token');
            $table->longText('description')->nullable()->after('status');
            // updated_at and created_at already exist due to $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nip',
                'role',
                'status',
                'description',
            ]);
        });
    }
};
