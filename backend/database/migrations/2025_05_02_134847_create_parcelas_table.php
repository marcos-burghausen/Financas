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
            $table->unsignedBigInteger('lacamentos_id');
            $table->foreign('lacamentos_id')->references('id')->on('lancamentos')->constrained()->onDelete('cascade');
            $table->integer('numero');
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
