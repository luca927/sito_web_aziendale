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
        Schema::create('cantieri', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('indirizzo');
        $table->decimal('latitudine', 10, 7)->nullable();
        $table->decimal('longitudine', 10, 7)->nullable();
        $table->date('data_inizio')->nullable();
        $table->date('data_fine')->nullable();
        $table->enum('stato', ['attivo', 'completato', 'sospeso'])->default('attivo');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantieri');
    }
};
