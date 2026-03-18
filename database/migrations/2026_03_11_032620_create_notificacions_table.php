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
        Schema::create('notificacions', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_seguimiento')->unique(); // Ej: NOT-2024-0001
            $table->foreignId('expediente_id')->constrained();
            $table->foreignId('receptor_id')->constrained('users');
            $table->string('asunto');
            $table->longText('cuerpo');
            $table->timestamp('fecha_deposito'); // Fecha exacta de envío
            $table->timestamp('fecha_lectura')->nullable(); // Clave para plazos legales
            $table->string('ip_lectura', 45)->nullable();
            $table->string('hash_integridad'); // SHA-256 del contenido
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacions');
    }
};
