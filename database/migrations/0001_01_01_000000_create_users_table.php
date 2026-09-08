<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Llave para agrupar usuarios bajo un cliente corporativo
            $table->unsignedBigInteger('empresa_id')->nullable();

            // Nombre dividido en tres campos, como en la mayoría de sistemas mexicanos
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable(); // Opcional: no todos lo usan

            $table->string('email')->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('ultimo_login')->nullable();

            // Estas dos columnas las exige el sistema de Auth de Laravel tal cual
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->rememberToken();
            $table->timestamps();
        });

        // Tablas de soporte de Laravel: nombres y columnas reservados por el framework, no se tocan
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};