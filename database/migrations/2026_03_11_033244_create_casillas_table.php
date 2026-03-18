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
        Schema::create('casillas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Dueño de la casilla
            $table->string('numero_casilla')->unique(); // Ej: CAS-7782
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casillas');
    }
};
