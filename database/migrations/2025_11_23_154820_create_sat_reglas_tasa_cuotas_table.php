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
        Schema::create('sat_reglas_tasa_cuotas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->decimal('minimo', '12', 6)->nullable();
            $table->decimal('valor', '12', 6)->default(0);
            $table->string('impuesto');
            $table->string('factor');
            $table->boolean('traslado')->default(false);
            $table->boolean('retencion')->default(false);
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
        Schema::dropIfExists('sat_reglas_tasa_cuotas');
    }
};
