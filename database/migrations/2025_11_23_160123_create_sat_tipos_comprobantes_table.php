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
        Schema::create('sat_tipos_comprobantes', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('descripcion');
            $table->decimal('valor_maximo', 26, 6)->default(0);
            $table->date('vigencia_desde')->nullable();
            $table->date('vigencia_hasta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sat_tipos_comprobantes');
    }
};
