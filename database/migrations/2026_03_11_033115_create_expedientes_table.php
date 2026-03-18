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
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Ej: IA-2026-0001
            $table->enum('tipo', ['IA', 'JRD', 'CONCILIACION']);
            $table->string('materia'); // Ej: Incumplimiento de contrato
            $table->string('demandante'); // O Solicitante
            $table->string('demandado')->nullable(); // O Invitado
            $table->enum('estado', ['REGISTRADO', 'EN_CURSO', 'CONCLUIDO'])->default('REGISTRADO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
