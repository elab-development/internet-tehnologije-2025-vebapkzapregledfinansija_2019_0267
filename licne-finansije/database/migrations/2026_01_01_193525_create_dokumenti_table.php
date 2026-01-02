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
        Schema::create('dokumenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transakcija_id')->constrained('transakcije')->cascadeOnDelete();
            $table->string('naziv');
            $table->date('datum');
            $table->string('putanja');
            $table->string('tip');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumenti');
    }
};
