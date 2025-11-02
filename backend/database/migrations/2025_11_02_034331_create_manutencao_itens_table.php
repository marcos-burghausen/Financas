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
        Schema::create('manutencao_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manutencao_id')->constrained('manutencoes')->onDelete('cascade');
            $table->string('nome'); // Ex: Óleo 5W30, Pneu Michelin, etc
            $table->text('descricao')->nullable();
            $table->integer('quantidade')->default(1);
            $table->decimal('valor_unitario', 12, 2);
            $table->decimal('valor_total', 12, 2); // quantidade * valor_unitario
            $table->timestamps();
            $table->index('manutencao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manutencao_itens');
    }
};
