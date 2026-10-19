<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('citas')) {
            if (!Schema::hasColumn('citas', 'estado_pago')) {
                Schema::table('citas', function (Blueprint $table) {
                    $table->string('estado_pago', 20)->default('pendiente')->after('estado');
                });
            }

            // Asegurar que registros existentes tengan un estado de pago válido por defecto
            DB::table('citas')
                ->whereNull('estado_pago')
                ->orWhere('estado_pago', '')
                ->update(['estado_pago' => 'pendiente']);

            // Agregar restricción CHECK si la base de datos es MySQL 8+ y no existe
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                try {
                    $checkExists = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.TABLE_CONSTRAINTS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'citas' 
                          AND CONSTRAINT_NAME = 'chk_citas_estado_pago'
                    ");

                    if (empty($checkExists)) {
                        DB::statement("ALTER TABLE citas ADD CONSTRAINT chk_citas_estado_pago CHECK (estado_pago IN ('pendiente', 'pagado'))");
                    }
                } catch (\Throwable $e) {
                    // Si el motor MySQL no soporta CHECK o ya existe, continuar de forma segura
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('citas') && Schema::hasColumn('citas', 'estado_pago')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                try {
                    DB::statement("ALTER TABLE citas DROP CHECK chk_citas_estado_pago");
                } catch (\Throwable $e) {}
            }

            Schema::table('citas', function (Blueprint $table) {
                $table->dropColumn('estado_pago');
            });
        }
    }
};
