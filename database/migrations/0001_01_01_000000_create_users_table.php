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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // El email sigue siendo único para notificaciones de cortesía
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            // OPTIMIZACIÓN: Documento de identidad
            $table->string('document_type', 3)->default('DNI'); // DNI, RUC, CE
            $table->string('document_number', 15)->unique()->index();

            // OPTIMIZACIÓN: Roles y Estado
            $table->enum('role', ['admin', 'operador', 'supervisor', 'usuario_final'])
                ->default('usuario_final');

            $table->string('password');
            $table->boolean('is_active')->default(true);

            // SEGURIDAD: Para auditoría legal
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // IMPORTANTE: Nunca borres un usuario, solo desactívalo (Trazabilidad)
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
