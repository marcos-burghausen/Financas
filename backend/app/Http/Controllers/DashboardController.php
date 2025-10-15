<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retorna o resumo do dashboard para o mês especificado
     * Com cache de 5 minutos para melhor performance
     */
    public function summary(Request $request)
    {
        $mesAno = $request->query('mesAno', now()->format('Y-m'));
        $user = auth()->user();
        $cacheKey = "dashboard_summary_user_{$user->id}_month_{$mesAno}";

        // Cache por 5 minutos (300 segundos)
        return Cache::remember($cacheKey, 300, function () use ($user, $mesAno) {
            [$ano, $mes] = explode('-', $mesAno);

            // Query otimizada com agregação em uma única consulta
            $receitasData = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'Receita')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->selectRaw('
                    SUM(valor) as total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN valor ELSE 0 END) as pago,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN valor ELSE 0 END) as pendente
                ')
                ->first();

            $despesasData = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'Despesa')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->selectRaw('
                    SUM(valor) as total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN valor ELSE 0 END) as pago,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN valor ELSE 0 END) as pendente
                ')
                ->first();

            // Buscar contas apenas com campos necessários
            $contas = DB::table('contas')
                ->where('user_id', $user->id)
                ->where('status_conta', 'Ativo')
                ->select('id', 'nome as name', 'saldo', 'icon', 'color')
                ->get();

            // Calcular saldo atual
            $saldoInicial = DB::table('contas')
                ->where('user_id', $user->id)
                ->where('incluir_em_soma_inicial', true)
                ->sum('saldo_inicial');

            $saldoAtual = $contas->sum('saldo');

            return response()->json([
                'mesAno' => $mesAno,
                'receitas' => [
                    'total' => (float) ($receitasData->total ?? 0),
                    'pago' => (float) ($receitasData->pago ?? 0),
                    'pendente' => (float) ($receitasData->pendente ?? 0),
                ],
                'despesas' => [
                    'total' => (float) ($despesasData->total ?? 0),
                    'pago' => (float) ($despesasData->pago ?? 0),
                    'pendente' => (float) ($despesasData->pendente ?? 0),
                ],
                'contas' => $contas,
                'saldos' => [
                    'inicial' => (float) $saldoInicial,
                    'atual' => (float) $saldoAtual,
                    'diferenca' => (float) ($saldoAtual - $saldoInicial),
                ],
            ]);
        });
    }

    /**
     * Limpa o cache do dashboard para o usuário
     */
    public function clearCache(Request $request)
    {
        $user = auth()->user();
        $mesAno = $request->query('mesAno', now()->format('Y-m'));
        $cacheKey = "dashboard_summary_user_{$user->id}_month_{$mesAno}";

        Cache::forget($cacheKey);

        return response()->json(['message' => 'Cache limpo com sucesso']);
    }
}
