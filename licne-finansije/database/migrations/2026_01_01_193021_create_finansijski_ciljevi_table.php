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
        Schema::create('finansijski_ciljevi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idKorisnik')->constrained('users')->cascadeOnDelete();
            $table->string('naziv');
            $table->decimal('ciljni_iznos', 10, 2);
            $table->decimal('trenutni_iznos', 10, 2)->default(0);
            $table->date('rok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finansijski_ciljevi');
    }
};
