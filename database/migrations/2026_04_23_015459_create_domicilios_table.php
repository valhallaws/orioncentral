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
        Schema::create('domicilios', function (Blueprint $table) {
            $table->id();
            $table->morphs('domiciliable');
            $table->string('alias');
            $table->string('calle');
            $table->string('exterior')->nullable();
            $table->string('interior')->nullable();
            $table->string('codigo_postal', '5');
            $table->foreignId('colonia_id')->nullable()->constrained('sat_colonias')->nullOnDelete();
            $table->string('municipio');
            $table->foreignId('estado_id')->constrained('sat_estados')->restrictOnDelete();
            $table->foreignId('pais_id')->constrained('sat_paises')->restrictOnDelete();
            $table->boolean('es_fiscal')->default(false);

            //Extras
            $table->string('referencia')->nullable();
            $table->string('telefono')->nullable();

            $table->foreignId('metodo_entrega_id')->nullable()->constrained('metodo_entregas')->nullOnDelete();
            $table->foreignId('contacto_ventas_id')->nullable()->constrained('contactos')->nullOnDelete();
            $table->foreignId('contacto_compras_id')->nullable()->constrained('contactos')->nullOnDelete();
            $table->foreignId('contacto_usuario_id')->nullable()->constrained('contactos')->nullOnDelete();
            $table->foreignId('contacto_destinatario_id')->nullable()->constrained('contactos')->nullOnDelete();
            $table->foreignId('contacto_pagos_id')->nullable()->constrained('contactos')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domicilios');
    }
};
