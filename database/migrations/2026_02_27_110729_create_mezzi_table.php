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
        Schema::create('mezzi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dipendente_id')->constrained('dipendenti')->onDelete('cascade');
        $table->string('tipo');
        $table->string('targa');
        $table->string('modello')->nullable();
        $table->enum('stato', ['disponibile', 'in_uso', 'manutenzione'])->default('disponibile');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mezzi');
    }
};
