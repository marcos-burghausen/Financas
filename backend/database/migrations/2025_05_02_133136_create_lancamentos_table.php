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
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('descricao', 50);
            $table->integer('valor');
            $table->enum('tipo_lancamento', ['Receita', 'Despesa', 'Cartão de crédito']);
            $table->boolean('is_estorno')->default(false);
            $table->unsignedBigInteger('original_lancamento_id')->nullable();
            $table->enum('recorrencia', ['Não recorrente', 'Parcelado', 'Fixa']);
            $table->integer('num_parcelas')->nullable()->default(1);
            $table->integer('parcela_atual')->nullable()->default(1);
            $table->enum('tipo_parcela', ['total', 'parcela'])->nullable()->default('total');
            $table->enum('periodicidade', ['Mensal', 'Diario', 'Semanal', 'Quinzenal', 'Trimenstral', 'Anual'])->nullable()->default(null);
            $table->date('data_vencimento');
            $table->enum('status_lancamento', ['Efetivada', 'Pendente'])->default('Pendente');
            $table->string('categoria', 30);
            $table->string('subcategoria', 30);
            $table->date('data_lancamento');
            $table->date('data_efetivacao')->nullable()->default(null);
            $table->unsignedBigInteger('conta_id')->nullable();
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
