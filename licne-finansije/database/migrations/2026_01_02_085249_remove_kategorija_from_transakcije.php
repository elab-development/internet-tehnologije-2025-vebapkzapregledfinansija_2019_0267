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
        Schema::table('transakcije', function (Blueprint $table) {
      
            $table->dropForeign(['kategorija_id']);

            
            $table->renameColumn('kategorija_id', 'idKategorija');

           
            $table->foreign('idKategorija')
                  ->references('id')
                  ->on('kategorije')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transakcije', function (Blueprint $table) {
           
            $table->dropForeign(['idKategorija']);

            
            $table->renameColumn('idKategorija', 'kategorija_id');

            
            $table->foreign('kategorija_id')
                  ->references('id')
                  ->on('kategorije');
        });
    }
};
