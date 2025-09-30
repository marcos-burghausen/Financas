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
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->uuid('installment_group_id')->nullable();
            // Fatura de cartão (quando tipo_lancamento = CartaoCredito ou pagamento de fatura)
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('descricao', 50);
            $table->integer('valor');
            $table->enum('tipo_lancamento', ['RECEITA', 'DESPESA', 'CARTAO_CREDITO']);
            // Estorno (para CartaoCredito)
            $table->boolean('is_estorno')->default(false);
            $table->unsignedBigInteger('original_lancamento_id')->nullable();
            $table->enum('recorrencia', ['NAO_RECORRENTE', 'PARCELADO', 'FIXA']);
            $table->integer('num_parcelas')->nullable()->default(1);
            $table->integer('parcela_atual')->nullable()->default(1);
            $table->enum('tipo_parcela', ['TOTAL', 'PARCELA'])->nullable()->default('TOTAL');
            $table->enum('periodicidade', ['MENSAL', 'DIARIO', 'SEMANAL', 'QUINZENAL', 'TRIMESTRAL', 'ANUAL'])->nullable()->default(null);
            $table->date('data_vencimento');
            $table->enum('status_lancamento', ['EFETIVADA', 'PENDENTE'])->default('PENDENTE');
            $table->string('categoria', 30);
            $table->string('subcategoria', 30);
            $table->date('data_lancamento');
            $table->date('data_efetivacao')->nullable();
            $table->unsignedBigInteger('conta_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('credit_card_invoices')->onDelete('set null');
            $table->foreign('original_lancamento_id')->references('id')->on('lancamentos')->onDelete('set null');
            $table->foreign('conta_id')->references('id')->on('contas')->onDelete('restrict');
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
