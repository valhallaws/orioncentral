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
        Schema::create('sat_codigos_postales', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('estado');
            $table->string('municipio')->nullable();
            $table->string('localidad')->nullable();
            $table->boolean('estimulo_frontera')->default(false);
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
        Schema::dropIfExists('sat_codigos_postales');
    }
};
