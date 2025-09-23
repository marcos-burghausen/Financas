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
        Schema::table('contas', function (Blueprint $table) {
            $table->string('bandeira')->nullable()->after('tipo'); // Ex: Mastercard, Visa
            $table->integer('limite')->nullable()->after('saldo');
            $table->integer('dia_fechamento')->nullable()->after('limite'); // Dia do mês (1-31)
            $table->integer('dia_vencimento')->nullable()->after('dia_fechamento'); // Dia do mês (1-31)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropColumn(['bandeira', 'limite', 'dia_fechamento', 'dia_vencimento']);
        });
    }
};
