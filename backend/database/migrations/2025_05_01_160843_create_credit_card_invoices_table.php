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
            $table->unsignedBigInteger('account_id'); // conta do tipo Cartão de Crédito
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->string('competence', 7); // YYYY-MM
            $table->date('closing_date');
            $table->date('due_date');
            $table->enum('status_invoice', ['ABERTA', 'FECHADA', 'PARCIAL', 'PAGA'])->default('ABERTA');
            // Totais (cache)
            $table->integer('total_invoice')->default(0);     // compras - estornos (efetivados) + encargos
            $table->integer('value_pay')->default(0);       // soma de pagamentos (efetivados)
            $table->integer('charges')->default(0);         // juros/IOF/multa (se houver)
            $table->timestamp('pay_in')->nullable();
            $table->unsignedBigInteger('launch_payment_id')->nullable(); // ID do lançamento de pagamento
            $table->timestamps();
            $table->unique(['account_id', 'competence']);
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
