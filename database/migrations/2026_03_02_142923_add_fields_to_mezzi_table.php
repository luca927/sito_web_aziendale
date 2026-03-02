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
    Schema::table('mezzi', function (Blueprint $table) {
        $table->integer('anno')->nullable()->after('modello');
        $table->date('prossima_manutenzione')->nullable()->after('anno');
        $table->enum('stato', ['attivo', 'in_manutenzione', 'fuori_uso'])->default('attivo')->change();
    });
}

public function down(): void
{
    Schema::table('mezzi', function (Blueprint $table) {
        $table->dropColumn(['anno', 'prossima_manutenzione']);
    });
}
};
