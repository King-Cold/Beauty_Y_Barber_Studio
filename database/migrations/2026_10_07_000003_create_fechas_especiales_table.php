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
        if (!Schema::hasTable('fechas_especiales')) {
            Schema::create('fechas_especiales', function (Blueprint $table) {
                $table->id();
                $table->date('fecha')->unique();
                $table->string('motivo');
                $table->boolean('cerrado_todo_el_dia')->default(false);
                $table->time('hora_apertura')->nullable();
                $table->time('hora_cierre')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fechas_especiales');
    }
};
