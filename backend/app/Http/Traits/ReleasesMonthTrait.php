<?php

namespace App\Http\Traits;

use App\Models\Conta;
use DateTime;

trait ReleasesMonthTrait
{
    public function classifiesReleases(object $lacamentos, string $tipoLancamentos, $data = null): array
    {
        $mes = $data ?? date('Y-m');

        $releasesMonth = $this->lancamentosMes($lacamentos, $mes);

        $valorRecebidoOuPago = $this->valorPendenteMes($releasesMonth, "Efetivada");
        $valuePending = $this->valorPendenteMes($releasesMonth, "Pendente");
        $valorTotalMes = $this->valorLancamentosMes($releasesMonth);

        $Data = [
            "valuePay" => $valorRecebidoOuPago,
            "valuePending" => $valuePending,
            "valueTotalMonth" => $valorTotalMes,
            "byMonth" => $releasesMonth,
        ];

        if ($tipoLancamentos === 'Expenses') {
            $totalExpensesDay = $this->totalExpensesDay($releasesMonth);
            $totalByCategoryExpenses = $this->totalByCategory($releasesMonth);
            $Data["totalDay"] = $totalExpensesDay;
            $Data["byCategory"] = $totalByCategoryExpenses;
        }

        return $Data;
    }

    public function totalExpensesDay(array $releasesMonth): int
    {
        $totalExpensesDay = 0;
        foreach ($releasesMonth as $release) {
            if ($release->status_lancamento === 'Efetivada' && strtotime($release->data_lancamento) === strtotime(date('Y-m-d'))) {
                $totalExpensesDay += $release->valor;
            }
        }
        return $totalExpensesDay;
    }

    public function totalByCategory(array $releases): array
    {
        $groupByCategory = [];

        foreach ($releases as $release) {
            $category = $release->categoria;
            $groupByCategory[$category][] = $release;
        }

        $totalByCategory = [];
        foreach ($groupByCategory as $category => $items) {
            $totalCategory = 0;
            foreach ($items as $item) {
                $totalCategory += $item['valor'];
            }
            $totalByCategory[$category] = $totalCategory;
        }

        return $totalByCategory;
    }

    public function lancamentosMes(object $lancamentos, string $mes): array
    {
        $lancamentosMes = [];
        $dataInicio = "{$mes}-01";
        $dataFim = (new DateTime($dataInicio))->format("Y-m-t");

        foreach ($lancamentos as $lancamento) {
            if ($lancamento && $lancamento->data_vencimento >= $dataInicio && $lancamento->data_vencimento <= $dataFim) {
                $lancamentosMes[] = $lancamento;
            }
        }
        return $lancamentosMes;
    }

    public function valorLancamentosMes(array $lancamentosMes): int
    {
        return array_sum(array_column($lancamentosMes, 'valor'));
    }

    public function valorPendenteMes(array $lancamentosMes, string $status): int
    {
        $valorPendenteMes = 0;
        foreach ($lancamentosMes as $lancamento) {
            if ($lancamento->status_lancamento  === $status) {
                $valorPendenteMes += $lancamento->valor;
            }
        }
        return $valorPendenteMes;
    }

    public function groupByMonth(object $releases): array
    {
        $releasesByMonth = [
            'Jan' => [],
            'Feb' => [],
            'Mar' => [],
            'Apr' => [],
            'May' => [],
            'Jun' => [],
            'Jul' => [],
            'Aug' => [],
            'Sep' => [],
            'Oct' => [],
            'Nov' => [],
            'Dec' => [],

        ];

        foreach ($releases as $release) {

            $date = new DateTime($release['dataLancamento']);
            $month = $date->format('M');
            $releasesByMonth[$month][] = $release;
        }

        return $releasesByMonth;
    }

    public function addTotalValueMonth(array $groupReleases): array
    {
        $totalByMonth = [];

        foreach ($groupReleases as $month => $releases) {
            $totalMonth = 0;

            foreach ($releases as $release) {
                $totalMonth +=  $release['valor'];
            }

            $totalByMonth[$month] = $this->formatValue($totalMonth);
        }
        return $totalByMonth;
    }

    public static function formatValue(int $value)
    {
        $formattedValue = $value / 100;
        $formattedValue     = number_format($formattedValue, 2, ',', '');
        return $formattedValue;
    }

    public static function formatValueInArray($releasesMonth)
    {
        $releases = [];
        foreach ($releasesMonth as $key => $release) {
            $formattedValue = $release->valor / 100;
            $formattedValue     = number_format($formattedValue, 2, ',', '.');
            $releases[] = $release;
        }
        return $formattedValue;
    }

    public function obterSaldoInicial(object $user, string $mes): int
    {
        $dataLimite = (new DateTime("$mes-01"))->modify('-1 day')->format('Y-m-d');

        // 1. Começa com a soma dos saldos iniciais das contas que devem ser incluídas.
        $saldoInicial = $user->contas()
            ->where('incluir_em_soma_inicial', true)
            ->sum('saldo_inicial');

        // 2. Busca todos os lançamentos efetivados ANTES do início do mês de referência.
        $lancamentosAnteriores = $user->lancamentos()
            ->where('status_lancamento', 'Efetivada')
            ->where('data_efetivacao', '<', $dataLimite)
            ->get();

        // 3. Soma as receitas e subtrai as despesas do período.
        $totalReceitasAnteriores = $lancamentosAnteriores->where('tipo_lancamento', 'Receita')->sum('valor');
        $totalDespesasAnteriores = $lancamentosAnteriores->where('tipo_lancamento', 'Despesa')->sum('valor');

        // 4. Calcula o saldo final
        return $saldoInicial + $totalReceitasAnteriores - $totalDespesasAnteriores;
    }

    public function obterSaldoAtual(object $user, $mes = null): float
    {
        $dataLimite = (new DateTime("$mes-01"))->modify('-1 day')->format('Y-m-d');

        // 1. Pega o saldo inicial do mês.
        $saldoDoInicioDoMes = $this->obterSaldoInicial($user, $mes);

        // 2. Busca todos os lançamentos efetivados DENTRO do mês de referência.
        $lancamentosDoMes = $user->lancamentos()
            ->where('status_lancamento', 'Efetivada')
            ->whereBetween('data_efetivacao', ["{$mes}-01", $dataLimite])
            ->get();

        // 3. Soma as receitas e subtrai as despesas do mês.
        $totalReceitasDoMes = $lancamentosDoMes->where('tipo_lancamento', 'Receita')->sum('valor');
        $totalDespesasDoMes = $lancamentosDoMes->where('tipo_lancamento', 'Despesa')->sum('valor');

        // 4. Calcula o saldo final do mês.
        return $saldoDoInicioDoMes + $totalReceitasDoMes - $totalDespesasDoMes;
    }
}
