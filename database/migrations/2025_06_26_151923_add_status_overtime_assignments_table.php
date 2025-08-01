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
        Schema::table('overtime_assignments', function (Blueprint $table) {
            $table->string('status')->default('waiting'); // adjust 'after' if needed
        });
    }

    public function down(): void
    {
        Schema::table('overtime_assignments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
