<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    //    Schema::table('relation_notification_users', function (Blueprint $table) {
    // // ✅ Step 1: Drop the existing foreign key constraint
    //        $table->dropForeign('relation_notification_user_notification_id_foreign');
    //         $table->dropColumn('user_id');
    //     });

        


    }

    public function down(): void
    {
        Schema::table('relation_notification_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->string('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
