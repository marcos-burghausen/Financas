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
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->constrained()->onDelete('cascade');
            $table->string('descricao', 30);
            $table->integer('valor');
            $table->enum('tipo', ['Não recorrente', 'Parcelada', 'Fixa']);
            $table->integer('num_parcelas')->nullable()->default(1);
            $table->enum('periodicidade', ['Mensal', 'Diario', 'Semanal', 'Quinzenal', 'Trimenstral', 'Anual'])->nullable()->default(null);
            $table->date('data_vencimento');
            $table->date('data_lancamento');
            $table->date('data_efetivacao')->nullable()->default(null);
            $table->enum('status', ['EFETIVADA', 'PENDENTE'])->default('PENDENTE');
            $table->string('categoria', 30);
            $table->string('sub_categoria', 30);
            $table->string('conta', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
