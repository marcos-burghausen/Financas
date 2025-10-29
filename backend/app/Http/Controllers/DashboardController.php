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

            // Calcular mês anterior
            $mesPrevio = (int)$mes - 1;
            $anoPrevio = (int)$ano;
            if ($mesPrevio < 1) {
                $mesPrevio = 12;
                $anoPrevio--;
            }

            // ========== RECEITAS - MÊS ATUAL ==========
            $receitasData = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'RECEITA')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->selectRaw('
                    COUNT(*) as qtd_total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN 1 ELSE 0 END) as qtd_efetivada,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN 1 ELSE 0 END) as qtd_pendente,
                    SUM(valor) as valor_total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN valor ELSE 0 END) as valor_recebido,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN valor ELSE 0 END) as valor_pendente
                ')
                ->first();

            // ========== RECEITAS - MÊS ANTERIOR ==========
            $receitasDataAnterior = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'RECEITA')
                ->whereYear('data_vencimento', $anoPrevio)
                ->whereMonth('data_vencimento', $mesPrevio)
                ->selectRaw('SUM(valor) as total')
                ->first();

            // Calcular variação de receitas
            $totalReceitasAtual = (float)($receitasData->valor_total ?? 0);
            $totalReceitasAnterior = (float)($receitasDataAnterior->total ?? 0);
            $variacaoReceitas = $this->calcularVariacao($totalReceitasAtual, $totalReceitasAnterior);

            // ========== DESPESAS - MÊS ATUAL ==========
            $despesasData = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'DESPESA')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->selectRaw('
                    COUNT(*) as qtd_total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN 1 ELSE 0 END) as qtd_efetivada,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN 1 ELSE 0 END) as qtd_pendente,
                    SUM(valor) as valor_total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN valor ELSE 0 END) as valor_pago,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN valor ELSE 0 END) as valor_pendente
                ')
                ->first();

            // ========== DESPESAS - MÊS ANTERIOR ==========
            $despesasDataAnterior = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'DESPESA')
                ->whereYear('data_vencimento', $anoPrevio)
                ->whereMonth('data_vencimento', $mesPrevio)
                ->selectRaw('SUM(valor) as total')
                ->first();

            // Calcular variação de despesas
            $totalDespesasAtual = (float)($despesasData->valor_total ?? 0);
            $totalDespesasAnterior = (float)($despesasDataAnterior->total ?? 0);
            info("Total Despesas Atual: $totalDespesasAtual, Total Despesas Anterior: $totalDespesasAnterior");
            $variacaoDespesas = $this->calcularVariacao($totalDespesasAtual, $totalDespesasAnterior);

            // Buscar contas apenas com campos necessários
            $contas = DB::table('contas')
                ->where('user_id', $user->id)
                ->where('status_conta', 'Ativo')
                ->select('id', 'name', 'saldo', 'icon', 'color')
                ->get();

            // Calcular saldo atual
            $saldoInicial = DB::table('contas')
                ->where('user_id', $user->id)
                ->where('incluir_em_soma_inicial', true)
                ->sum('saldo');

            $saldoAtual = $contas->sum('saldo');

            // ========== LANÇAMENTOS PENDENTES ==========
            $lancamentosPendentes = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', '!=', 'CARTAO_CREDITO')
                ->where('status_lancamento', 'PENDENTE')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
                ->orderBy('data_vencimento', 'desc')
                ->get();

            // ========== TODOS OS LANÇAMENTOS ==========
            $lancamentosMes = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', '!=', 'CARTAO_CREDITO')
                ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->orderBy('data_vencimento', 'desc')
                ->get();

            // ========== TRANSAÇÕES RECENTES (5 ÚLTIMAS DE CADA TIPO) ==========
            $receitasRecentes = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'RECEITA')
                ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
                ->orderBy('data_vencimento', 'desc')
                ->limit(5)
                ->get();

            $despesasRecentes = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'DESPESA')
                ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
                ->orderBy('data_vencimento', 'desc')
                ->limit(5)
                ->get();

            // ========== TODOS OS LANÇAMENTOS SEPARADOS POR TIPO ==========
            $todosReceitasLancamentos = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'RECEITA')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
                ->orderBy('data_vencimento', 'desc')
                ->get();

            $todosDespesasLancamentos = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'DESPESA')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
                ->orderBy('data_vencimento', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'mesAno' => $mesAno,
                'receitas' => [
                    'qtd_total' => (int)($receitasData->qtd_total ?? 0),
                    'qtd_efetivada' => (int)($receitasData->qtd_efetivada ?? 0),
                    'qtd_pendente' => (int)($receitasData->qtd_pendente ?? 0),
                    'valor_total' => (float)($receitasData->valor_total ?? 0),
                    'valor_recebido' => (float)($receitasData->valor_recebido ?? 0),
                    'valor_pendente' => (float)($receitasData->valor_pendente ?? 0),
                    'variacao' => $variacaoReceitas,
                ],
                'despesas' => [
                    'qtd_total' => (int)($despesasData->qtd_total ?? 0),
                    'qtd_efetivada' => (int)($despesasData->qtd_efetivada ?? 0),
                    'qtd_pendente' => (int)($despesasData->qtd_pendente ?? 0),
                    'valor_total' => (float)($despesasData->valor_total ?? 0),
                    'valor_pago' => (float)($despesasData->valor_pago ?? 0),
                    'valor_pendente' => (float)($despesasData->valor_pendente ?? 0),
                    'variacao' => $variacaoDespesas,
                ],
                'pendentes' => [
                    'qtd_pendentes' => (int)($lancamentosPendentes->count() ?? 0),
                    'valor_total_pendente' => (float)$lancamentosPendentes->sum('valor'),
                    'lancamentos' => $lancamentosPendentes,
                ],
                'transacoes_recentes' => [
                    'receitas' => $receitasRecentes,
                    'despesas' => $despesasRecentes,
                ],
                'lancamentos' => [
                    'receitas' => $todosReceitasLancamentos,
                    'despesas' => $todosDespesasLancamentos,
                ],
                'contas' => [
                    'lista' => $contas,
                    'qtd_contas_ativas' => (int)$contas->count(),
                ],
                'saldos' => [
                    'inicial' => (float)$saldoInicial,
                    'atual' => (float)$saldoAtual,
                    'diferenca' => (float)($saldoAtual - $saldoInicial),
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

        return response()->json(['success' => true, 'message' => 'Cache limpo com sucesso']);
    }

    /**
     * Calcula a variação percentual entre dois valores
     * 
     * @param float $valorAtual - Valor do mês atual
     * @param float $valorAnterior - Valor do mês anterior
     * @return float - Variação em percentual
     * 
     * Casos:
     * - Valor anterior > 0: ((Atual - Anterior) / Anterior) * 100
     * - Valor anterior = 0 e atual > 0: 100 (crescimento infinito)
     * - Ambos 0: 0
     */
    private function calcularVariacao(float $valorAtual, float $valorAnterior): float
    {
        if ($valorAnterior > 0) {
            return (($valorAtual - $valorAnterior) / $valorAnterior) * 100;
        } elseif ($valorAtual > 0) {
            return 100; // Crescimento de 0 para algo
        }
        return 0; // Ambos 0
    }
}
