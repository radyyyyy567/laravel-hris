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
         Schema::rename('relation_absence_users', 'relation_absence_manpowers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('relation_absence_manpowers', function (Blueprint $table) {
            //
        });
    }
};
