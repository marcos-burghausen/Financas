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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('placa')->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->year('ano');
            $table->string('cor')->nullable();
            $table->integer('quilometragem')->default(0);
            $table->enum('combustivel', ['Gasolina', 'Diesel', 'Etanol', 'Híbrido', 'Elétrico'])->default('Gasolina');
            $table->integer('proximaManutencao')->default(40000);
            $table->enum('status', ['ativo', 'inativo', 'manutenção'])->default('ativo');
            $table->timestamps();
            $table->index('user_id');
            $table->index('placa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
