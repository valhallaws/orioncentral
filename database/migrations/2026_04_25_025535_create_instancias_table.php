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
        Schema::create('instancias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('alias');
            $table->string('carpeta');
            $table->string('dominio');
            $table->string('base_path')->default('/var/www/');
            $table->string('repositorio');
            $table->string('rama')->default('main');
            $table->string('database_name');
            $table->string('database_user');
            $table->string('database_password');
            $table->enum('estado', ['instalando', 'pendiente', 'activo', 'suspendido', 'mantenimiento', 'baja']);

            $table->dateTime('deployed_at');
            $table->dateTime('suspended_at')->nullable();
            $table->dateTime('delete_by')->nullable();
            $table->dateTime('last_fetch')->nullable();
            $table->dateTime('last_backup')->nullable();

            $table->boolean('is_sandbox')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instancias');
    }
};
