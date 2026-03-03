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
    Schema::table('timbrature', function (Blueprint $table) {
        $table->string('causale')->default('Lavoro Ordinario')->after('cantiere_id');
        $table->decimal('latitudine', 10, 7)->nullable()->after('uscita');
        $table->decimal('longitudine', 10, 7)->nullable()->after('latitudine');
    });
}

public function down(): void
{
    Schema::table('timbrature', function (Blueprint $table) {
        $table->dropColumn(['causale', 'latitudine', 'longitudine']);
    });
}
};
