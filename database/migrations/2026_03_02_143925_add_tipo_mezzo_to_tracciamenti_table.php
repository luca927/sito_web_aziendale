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
        Schema::table('tracciamenti', function (Blueprint $table) {
            $table->string('tipo_attivita')->nullable()->after('cantiere_id');
            $table->foreignId('mezzo_id')->nullable()->constrained('mezzi')->onDelete('set null')->after('tipo_attivita');
        });
    }

    public function down(): void
    {
        Schema::table('tracciamenti', function (Blueprint $table) {
            $table->dropForeign(['mezzo_id']);
            $table->dropColumn(['tipo_attivita', 'mezzo_id']);
        });
    }
};
