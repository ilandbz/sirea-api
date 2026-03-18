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
            $table->string('codigo_seguimiento')->unique();
            $table->foreignId('expediente_id')->constrained();
            $table->foreignId('receptor_id')->constrained('users');
            $table->foreignId('emisor_id')->constrained('users');
            $table->string('asunto');
            $table->longText('cuerpo');
            $table->timestamp('fecha_deposito');
            $table->timestamp('fecha_lectura')->nullable();
            $table->string('ip_lectura', 45)->nullable();
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
