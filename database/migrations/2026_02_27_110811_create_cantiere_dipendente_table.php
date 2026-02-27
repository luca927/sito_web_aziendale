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
        Schema::create('cantiere_dipendente', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cantiere_id')->constrained('cantieri')->onDelete('cascade');
        $table->foreignId('dipendente_id')->constrained('dipendenti')->onDelete('cascade');
        $table->date('data_assegnazione');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantiere_dipendente');
    }
};
