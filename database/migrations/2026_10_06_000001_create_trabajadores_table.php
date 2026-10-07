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
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            
            // Datos del trabajador (Subtarea 3)
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('telefono', 20);
            $table->string('email')->unique();
            $table->string('direccion');
            $table->string('fotografia')->nullable();
            $table->unsignedInteger('experiencia')->default(0);

            // Estado del trabajador: activo / inactivo (Subtarea 8)
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
