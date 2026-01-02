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
        Schema::create('podsetnici', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idKorisnik')->constrained('users')->cascadeOnDelete();
            $table->text('opis');
            $table->dateTime('datum_vreme');
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('podsetnici');
    }
};
