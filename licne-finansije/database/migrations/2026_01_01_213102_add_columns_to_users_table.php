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
            $table->string('ime')->after('id');
            $table->string('prezime')->after('ime');
            $table->string('uloga')->after('prezime');
            $table->integer('poeni')->default(0)->after('uloga');
            $table->string('nivo')->after('poeni');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ime', 'prezime', 'uloga', 'poeni', 'nivo']);
        });
    }
};
