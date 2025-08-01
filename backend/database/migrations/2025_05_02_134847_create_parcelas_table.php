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
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('revenue_id');
            $table->foreign('revenue_id')->references('id')->on('revenues')->constrained()->onDelete('cascade');
            $table->integer('parcela');
            $table->integer('totalParcelas');
            $table->integer('valor');
            $table->date('dataVencimento');
            $table->date('dataLancamento');
            $table->date('dataEfetivacao')->nullable()->default(null);
            $table->integer('taxaMulta')->nullable();
            $table->integer('valorPago')->nullable();
            $table->enum('status', ['Efetivada', 'Pendente'])->default('Pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelas');
    }
};
