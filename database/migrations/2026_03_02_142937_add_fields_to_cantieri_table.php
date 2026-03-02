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
        Schema::table('cantieri', function (Blueprint $table) {
            $table->string('referente')->nullable()->after('nome');
            $table->string('giorni')->nullable()->after('referente');
        });
    }

    public function down(): void
    {
        Schema::table('cantieri', function (Blueprint $table) {
            $table->dropColumn(['referente', 'giorni']);
        });
    }
};
