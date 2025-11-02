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
        Schema::create('manutencoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculos')->onDelete('cascade');
            $table->string('tipo'); // Troca de Óleo, Revisão, Pneu, etc
            $table->date('data');
            $table->integer('quilometragem');
            $table->decimal('valor_total', 12, 2)->default(0);

            // Dados da Oficina
            $table->string('oficina_nome')->nullable();
            $table->string('oficina_telefone')->nullable();
            $table->string('oficina_email')->nullable();
            $table->string('oficina_endereco')->nullable();

            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->index('veiculo_id');
            $table->index('data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manutencoes');
    }
};
