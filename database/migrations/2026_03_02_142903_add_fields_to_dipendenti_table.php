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
    Schema::table('dipendenti', function (Blueprint $table) {
        $table->string('codice_fiscale')->nullable()->after('cognome');
        $table->string('indirizzo')->nullable()->after('codice_fiscale');
        $table->date('data_assunzione')->nullable()->after('indirizzo');
    });
}

public function down(): void
{
    Schema::table('dipendenti', function (Blueprint $table) {
        $table->dropColumn(['codice_fiscale', 'indirizzo', 'data_assunzione']);
    });
}
};
