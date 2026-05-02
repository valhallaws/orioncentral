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
        Schema::create('sat_usos_comprobantes', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('descripcion');
            $table->boolean('aplica_fisica')->default(false);
            $table->boolean('aplica_moral')->default(false);
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
        Schema::dropIfExists('sat_usos_comprobantes');
    }
};
