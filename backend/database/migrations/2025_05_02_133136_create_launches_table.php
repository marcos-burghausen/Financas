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
        Schema::create('launches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->uuid('installment_group_id')->nullable();
            // Fatura de cartão (quando tipo_lancamento = CartaoCredito ou pagamento de fatura)
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('description', 500);
            $table->integer('value');
            $table->enum('launch_type', ['RECEITA', 'DESPESA', 'CARTAO_CREDITO']);
            // Estorno (para CartaoCredito)
            $table->boolean('is_refund')->default(false);
            $table->unsignedBigInteger('original_launch_id')->nullable();
            $table->enum('recurrence', ['NAO_RECORRENTE', 'PARCELADO', 'FIXA']);
            $table->integer('qtd_installments')->nullable()->default(1);
            $table->integer('num_installment')->nullable()->default(1);
            $table->enum('installment_type', ['TOTAL', 'PARCELA'])->nullable()->default('TOTAL');
            $table->enum('periodicity', ['MENSAL', 'DIARIO', 'SEMANAL', 'QUINZENAL', 'TRIMESTRAL', 'ANUAL'])->nullable()->default(null);
            $table->date('due_date');
            $table->enum('launch_status', ['EFETIVADA', 'PENDENTE'])->default('PENDENTE');
            $table->string('category', 30);
            $table->string('subcategory', 30);
            $table->text('observations')->nullable();
            $table->date('launch_date');
            $table->date('effective_date')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('credit_card_invoices')->onDelete('set null');
            $table->foreign('original_launch_id')->references('id')->on('launches')->onDelete('set null');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('launches');
    }
};
