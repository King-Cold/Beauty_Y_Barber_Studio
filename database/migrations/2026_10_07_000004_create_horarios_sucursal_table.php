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
        if (!Schema::hasTable('horarios_sucursal')) {
            Schema::create('horarios_sucursal', function (Blueprint $table) {
                $table->id();
                $table->string('dia', 20)->unique();
                $table->boolean('abierto')->default(true);
                $table->string('apertura', 10)->default('09:00');
                $table->string('cierre', 10)->default('20:00');
                $table->timestamps();
            });
        } else {
            Schema::table('horarios_sucursal', function (Blueprint $table) {
                if (!Schema::hasColumn('horarios_sucursal', 'dia')) {
                    $table->string('dia', 20)->nullable();
                }
                if (!Schema::hasColumn('horarios_sucursal', 'abierto')) {
                    $table->boolean('abierto')->default(true);
                }
                if (!Schema::hasColumn('horarios_sucursal', 'apertura')) {
                    $table->string('apertura', 10)->default('09:00');
                }
                if (!Schema::hasColumn('horarios_sucursal', 'cierre')) {
                    $table->string('cierre', 10)->default('20:00');
                }
                if (!Schema::hasColumn('horarios_sucursal', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_sucursal');
    }
};
