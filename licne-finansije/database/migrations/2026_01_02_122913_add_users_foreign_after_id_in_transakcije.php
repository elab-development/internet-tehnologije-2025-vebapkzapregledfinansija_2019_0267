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
           $table->foreignId('idKorisnik')
                  ->after('id')
                  ->constrained('users')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transakcije', function (Blueprint $table) {
            $table->dropForeign(['idKorisnik']);
            $table->dropColumn('idKorisnik');
        });
    }
};
