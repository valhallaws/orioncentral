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
        Schema::create('sat_formas_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('descripcion');
            $table->boolean('bancarizado')->default(false);
            $table->boolean('requiere_operacion')->default(false);
            $table->date('vigencia_desde');
            $table->date('vigencia_hasta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sat_formas_pagos');
    }
};
