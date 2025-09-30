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
        Schema::create('credit_card_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conta_id'); // conta do tipo Cartão de Crédito
            $table->string('competencia', 7); // YYYY-MM
            $table->date('data_fechamento');
            $table->date('data_vencimento');
            $table->enum('status_fatura', ['Aberta', 'Fechada', 'Paga'])->default('Aberta');
            $table->integer('total_fatura')->default(0);
            $table->integer('encargos')->default(0);
            $table->timestamp('pago_em')->nullable();
            $table->unsignedBigInteger('lancamento_pagamento_id')->nullable(); // ID do lançamento de pagamento
            $table->timestamps();
            $table->unique(['conta_id', 'competencia']);
            $table->foreign('conta_id')->references('id')->on('contas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_card_invoices');
    }
};
