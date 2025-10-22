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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->string('name', 20);
            $table->string('icon', 20)->default('');
            $table->string('color', 20)->default('');
            $table->integer('balance')->default(0);
            $table->integer('include_in_initial_sum')->default(false);
            $table->string('description', 500)->nullable();
            $table->enum('account_type', ['CARTEIRA', 'CONTA_CORRENTE', 'POUPANCA', 'INVESTIMENTO', 'OUTRO', 'CARTAO_CREDITO'])->default('CARTEIRA');
            $table->enum('account_status', ['ATIVO', 'INATIVO'])->default('ATIVO');
            $table->integer('limit')->nullable();
            $table->tinyInteger('closing_day')->unsigned()->nullable(); // Dia do mês (1-31)
            $table->tinyInteger('due_day')->unsigned()->nullable(); // Dia do mês (1-31)

            // Esta coluna só será preenchida se 'account_type' for 'CARTAO_CREDITO'.
            $table->unsignedBigInteger('parent_account_id')->nullable(); //relacionamento auto-referencial (ou recursivo)
            $table->foreign('parent_account_id')->references('id')->on('accounts')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
