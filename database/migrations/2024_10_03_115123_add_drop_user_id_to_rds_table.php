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
        Schema::table('rds', function (Blueprint $table) {
           // $table->dropForeign('rds_user_id_foreign');
           $table->dropForeign(['user_id']);  // Suppression de la clé étrangère
            $table->dropColumn('user_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rds', function (Blueprint $table) {
            //
        });
    }
};