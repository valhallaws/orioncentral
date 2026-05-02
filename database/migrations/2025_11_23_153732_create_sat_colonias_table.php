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
        Schema::create('sat_colonias', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('descripcion');
            $table->string('codigo_postal');
            $table->timestamps();

            $table->unique(['clave', 'codigo_postal'], 'sat_colonias_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sat_colonias');
    }
};
