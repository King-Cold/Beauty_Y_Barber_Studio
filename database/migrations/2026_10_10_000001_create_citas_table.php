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
        if (!Schema::hasTable('citas')) {
            Schema::create('citas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('trabajador_id')->constrained('trabajadores')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->date('fecha');
                $table->string('hora_inicio', 10);
                $table->string('hora_fin', 10);
                $table->unsignedInteger('duracion_minutos')->default(30);
                $table->decimal('precio', 10, 2);
                $table->string('estado', 50)->default('confirmada');
                $table->text('notas')->nullable();
                $table->timestamps();

                $table->index(['trabajador_id', 'fecha']);
                $table->index(['user_id', 'fecha']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
