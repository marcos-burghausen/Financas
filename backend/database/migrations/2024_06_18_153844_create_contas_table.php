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
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->string('name', 20);
            $table->string('icon', 20)->default('');
            $table->string('bandeira', 20)->default('');
            $table->integer('saldo')->default(0);
            $table->integer('saldo_inicial')->default(0);
            $table->integer('incluir_em_soma_inicial')->default(false);
            $table->string('descricao', 50)->nullable();
            $table->enum('tipo_conta', ['Carteira', 'Conta Corrente', 'Poupança', 'Investimento', 'Outro', 'Cartão de Crédito'])->default('Carteira');
            $table->enum('status_conta', ['Ativo', 'Inativo'])->default('Ativo');
            $table->integer('limite')->nullable();
            $table->tinyInteger('dia_fechamento')->unsigned()->nullable(); // Dia do mês (1-31)
            $table->tinyInteger('dia_vencimento')->unsigned()->nullable(); // Dia do mês (1-31)

            // Esta coluna só será preenchida se 'tipo_conta' for 'Cartão de Crédito'.
            $table->unsignedBigInteger('conta_pai_id')->nullable(); //relacionamento auto-referencial (ou recursivo)
            $table->foreign('conta_pai_id')->references('id')->on('contas')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contas');
    }
};
