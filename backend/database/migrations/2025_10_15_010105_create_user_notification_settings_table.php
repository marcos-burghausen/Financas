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
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Configurações de notificações por e-mail
            $table->boolean('email_vencimento')->default(true); // Notificar vencimentos
            $table->boolean('email_limite_cartao')->default(true); // Notificar limite de cartão
            $table->boolean('email_estorno')->default(true); // Notificar estornos
            $table->boolean('email_desvio_orcamento')->default(true); // Notificar desvios de orçamento

            // Configurações de antecedência
            $table->integer('dias_antecedencia_vencimento')->default(3); // Dias antes do vencimento
            $table->integer('percentual_alerta_cartao')->default(80); // % do limite para alertar

            // Configurações gerais
            $table->boolean('receber_resumo_mensal')->default(true); // Resumo mensal por e-mail
            $table->string('horario_preferido')->default('09:00'); // Horário preferido para envios

            $table->timestamps();

            // Índice único para garantir 1 configuração por usuário
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_settings');
    }
};
