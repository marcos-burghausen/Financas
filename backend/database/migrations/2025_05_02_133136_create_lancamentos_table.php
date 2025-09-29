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
        Schema::create('lancamentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('installment_group_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->string('descricao', 50);
            $table->integer('valor');
            $table->enum('tipo', ['Receita', 'Despesa', 'CartaoCredito']);
            $table->boolean('is_estorno')->default(false);
            $table->unsignedBigInteger('original_lancamento_id')->nullable();
            $table->enum('recorrencia', ['Não recorrente', 'Parcelado', 'Fixa']);
            $table->integer('numParcelas')->nullable()->default(1);
            $table->integer('parcelaAtual')->nullable()->default(1);
            $table->enum('tipoParcela', ['total', 'parcela'])->nullable()->default('total');
            $table->enum('periodicidade', ['Mensal', 'Diario', 'Semanal', 'Quinzenal', 'Trimenstral', 'Anual'])->nullable()->default(null);
            $table->date('dataVencimento');
            $table->enum('status', ['Efetivada', 'Pendente'])->default('Pendente');
            $table->string('categoria', 30);
            $table->string('subcategoria', 30);
            $table->date('dataLancamento');
            $table->date('dataEfetivacao')->nullable()->default(null);
            $table->string('conta', 30);
            $table->unsignedBigInteger('conta_id')->nullable()->after('conta');
            $table->foreign('invoice_id')->references('id')->on('credit_card_invoices')->onDelete('set null');
            $table->foreign('original_lancamento_id')->references('id')->on('lancamentos')->onDelete('set null');
            $table->foreign('conta_id')->references('id')->on('contas')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lancamentos');
    }
};
