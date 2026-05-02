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
        Schema::create('instancia_respaldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instancia_id')->constrained('instancias')->cascadeOnDelete();
            $table->enum('type', ['database', 'files', 'full'])->default('database');
            $table->string('file_path')->nullable();
            $table->bigInteger('size_bytes')->nullable();
            $table->enum('estatus', ['pendiente', 'completado', 'fallido'])->default('pendiente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instancia_respaldos');
    }
};
