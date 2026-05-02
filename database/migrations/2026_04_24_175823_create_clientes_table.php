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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('alias')->unique();
            $table->string('razon_social')->unique();
            $table->string('rfc');
            $table->string('id_tributario')->nullable();
            $table->foreignId('condicion_pago_id')->constrained('condicion_pagos')->restrictOnDelete();
            $table->foreignId('regimen_id')->constrained('sat_regimenes_fiscales')->restrictOnDelete();
            $table->foreignId('forma_pago_id')->constrained('sat_formas_pagos')->restrictOnDelete();
            $table->foreignId('metodo_pago_id')->constrained('sat_metodos_pagos')->restrictOnDelete();
            $table->foreignId('uso_cfdi_id')->constrained('sat_usos_comprobantes')->restrictOnDelete();

            $table->string('cuenta_bancaria', 4)->nullable();
            $table->foreignId('banco_id')->nullable()->constrained('sat_bancos')->nullOnDelete();
            $table->foreignId('moneda_id')->constrained('sat_monedas')->restrictOnDelete();
            $table->longText('anotaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
