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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->string('categoria', 30);
            $table->integer('valor_orcado'); // em centavos
            $table->string('mes_ano', 7); // formato YYYY-MM
            $table->text('observacao')->nullable();
            $table->timestamps();

            // Índice único para evitar duplicatas de categoria por usuário/mês
            $table->unique(['user_id', 'categoria', 'mes_ano']);

            // Índices para performance
            $table->index(['user_id', 'mes_ano']);
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
