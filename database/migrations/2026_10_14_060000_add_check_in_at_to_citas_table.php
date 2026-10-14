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
        if (Schema::hasTable('citas') && !Schema::hasColumn('citas', 'check_in_at')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->timestamp('check_in_at')->nullable()->after('observaciones');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('citas') && Schema::hasColumn('citas', 'check_in_at')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->dropColumn('check_in_at');
            });
        }
    }
};
