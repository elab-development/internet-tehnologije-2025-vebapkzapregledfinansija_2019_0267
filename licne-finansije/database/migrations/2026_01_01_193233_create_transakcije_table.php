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
        Schema::create('transakcije', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategorija_id')->constrained('kategorije');
            $table->decimal('iznos', 10, 2);
            $table->dateTime('datum_vreme');
            $table->string('tip'); // enum TipTransakcije
            $table->text('opis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transakcije');
    }
};
