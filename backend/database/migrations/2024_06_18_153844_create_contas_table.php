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
            $table->integer('saldo')->default(0);
            $table->integer('saldoInicial')->default(0);
            $table->integer('incluirEmSomaInicial')->default(false);
            $table->string('descricao', 50)->nullable();
            $table->enum('tipo', ['Pessoal', 'Empresarial', 'investimento'])->default('Pessoal');
            $table->enum('status', ['Ativo', 'Inativo'])->default('Ativo');
            $table->string('conta', 20)->nullable();
            $table->integer('limite')->nullable();
            $table->integer('dia_fechamento')->nullable();
            $table->integer('dia_vencimento')->nullable();
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
