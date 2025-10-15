<?php

namespace App\Http\Controllers;

use App\Models\Lancamento;
use App\Notifications\VencimentoContaNotification;
use App\Notifications\LimiteCartaoNotification;
use App\Notifications\EstornoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationSettingsController extends Controller
{
    /**
     * Buscar configurações de notificação do usuário autenticado
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $settings = $user->getOrCreateNotificationSettings();

        return response()->json([
            'settings' => $settings
        ]);
    }

    /**
     * Atualizar configurações de notificação
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'email_vencimento' => 'sometimes|boolean',
            'email_limite_cartao' => 'sometimes|boolean',
            'email_estorno' => 'sometimes|boolean',
            'email_desvio_orcamento' => 'sometimes|boolean',
            'dias_antecedencia_vencimento' => 'sometimes|integer|min:0|max:30',
            'percentual_alerta_cartao' => 'sometimes|integer|min:50|max:100',
            'receber_resumo_mensal' => 'sometimes|boolean',
            'horario_preferido' => 'sometimes|date_format:H:i',
        ]);

        $user = $request->user();
        $settings = $user->getOrCreateNotificationSettings();
        $settings->update($validated);

        return response()->json([
            'message' => 'Configurações atualizadas com sucesso!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Testar envio de notificação de vencimento
     */
    public function testVencimento(Request $request)
    {
        $user = $request->user();

        // Buscar um lançamento pendente do usuário para teste
        $lancamento = Lancamento::where('user_id', $user->id)
            ->where('tipo_lancamento', 'DESPESA')
            ->where('status_lancamento', 'PENDENTE')
            ->first();

        if (!$lancamento) {
            return response()->json([
                'message' => 'Você não possui lançamentos pendentes para testar a notificação.',
                'tip' => 'Crie uma despesa pendente para testar o envio.'
            ], 404);
        }

        // Calcular dias restantes (ou usar 3 para teste)
        $diasRestantes = $lancamento->data_vencimento
            ? Carbon::now()->diffInDays($lancamento->data_vencimento, false)
            : 3;

        // Enviar notificação de teste
        $user->notify(new VencimentoContaNotification($lancamento, max(0, $diasRestantes)));

        return response()->json([
            'message' => 'E-mail de teste enviado com sucesso!',
            'details' => [
                'email' => $user->email,
                'lancamento' => $lancamento->descricao,
                'valor' => $lancamento->valor,
                'vencimento' => $lancamento->data_vencimento,
            ]
        ]);
    }

    /**
     * Testar envio de notificação de limite de cartão
     */
    public function testLimiteCartao(Request $request)
    {
        $user = $request->user();

        // Buscar um cartão de crédito do usuário
        $cartao = DB::table('contas')
            ->where('user_id', $user->id)
            ->where('tipo_conta', 'Cartão de Crédito')
            ->where('status_conta', 'Ativo')
            ->first();

        if (!$cartao) {
            return response()->json([
                'message' => 'Você não possui cartões de crédito cadastrados.',
                'tip' => 'Cadastre um cartão para testar a notificação.'
            ], 404);
        }

        // Calcular valor utilizado (para teste, usamos dados reais ou simulados)
        $valorUtilizado = Lancamento::where('user_id', $user->id)
            ->where('conta_id', $cartao->id)
            ->where('tipo_lancamento', 'CARTAO_CREDITO')
            ->where('status_lancamento', 'PENDENTE')
            ->sum('valor');

        $percentualUtilizado = $cartao->limite > 0
            ? round(($valorUtilizado / $cartao->limite) * 100)
            : 85; // valor padrão para teste

        // Enviar notificação de teste
        $user->notify(new LimiteCartaoNotification(
            $cartao->name,
            $valorUtilizado ?: ($cartao->limite * 0.85), // 85% se não houver lançamentos
            $cartao->limite,
            $percentualUtilizado ?: 85
        ));

        return response()->json([
            'message' => 'E-mail de teste enviado com sucesso!',
            'details' => [
                'email' => $user->email,
                'cartao' => $cartao->name,
                'limite' => $cartao->limite,
                'utilizado' => $valorUtilizado,
                'percentual' => $percentualUtilizado . '%',
            ]
        ]);
    }

    /**
     * Testar envio de notificação de estorno
     */
    public function testEstorno(Request $request)
    {
        $user = $request->user();

        // Buscar um lançamento de estorno do usuário
        $lancamentoEstorno = Lancamento::where('user_id', $user->id)
            ->where('is_estorno', true)
            ->first();

        if (!$lancamentoEstorno) {
            return response()->json([
                'message' => 'Você não possui estornos registrados.',
                'tip' => 'Registre um estorno para testar a notificação.'
            ], 404);
        }

        // Buscar lançamento original
        $lancamentoOriginal = Lancamento::find($lancamentoEstorno->original_lancamento_id);

        if (!$lancamentoOriginal) {
            // Se não encontrar, criar um objeto fake para teste
            $lancamentoOriginal = new Lancamento();
            $lancamentoOriginal->descricao = 'Lançamento Original (Teste)';
            $lancamentoOriginal->categoria = $lancamentoEstorno->categoria;
        }

        // Enviar notificação de teste
        $user->notify(new EstornoNotification($lancamentoEstorno, $lancamentoOriginal));

        return response()->json([
            'message' => 'E-mail de teste enviado com sucesso!',
            'details' => [
                'email' => $user->email,
                'estorno' => $lancamentoEstorno->descricao,
                'original' => $lancamentoOriginal->descricao,
                'valor' => $lancamentoEstorno->valor,
            ]
        ]);
    }

    /**
     * Estatísticas de notificações enviadas (futuro)
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        // Por enquanto, retornar estrutura básica
        // No futuro, podemos criar uma tabela de log de notificações
        return response()->json([
            'stats' => [
                'total_enviadas' => 0,
                'ultima_notificacao' => null,
                'notificacoes_pendentes' => 0,
            ],
            'message' => 'Estatísticas de notificações (em desenvolvimento)'
        ]);
    }
}
