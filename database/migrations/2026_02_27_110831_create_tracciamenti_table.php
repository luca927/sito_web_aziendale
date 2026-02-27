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
        Schema::create('tracciamenti', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dipendente_id')->constrained('dipendenti')->onDelete('cascade');
        $table->foreignId('cantiere_id')->constrained('cantieri')->onDelete('cascade');
        $table->timestamp('data_ora');
        $table->string('note')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracciamenti');
    }
};
