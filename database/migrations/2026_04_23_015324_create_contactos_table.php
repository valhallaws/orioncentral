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
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();

            $table->morphs('contactable');

            $table->string('tratamiento')->nullable();
            $table->string('nombre');
            $table->string('telefono')->nullable();
            $table->string('extension')->nullable();
            $table->string('movil')->nullable();
            $table->string('email')->nullable();
            $table->text('anotaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
