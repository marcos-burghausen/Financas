<?php

namespace App\Http\Traits;

use App\Models\Conta;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait ReleasesMonthTrait
{
    public function classifiesReleases(object $lacamentos, string $tipoLancamentos, $data = null): array
    {
        $mes = $data ?? date('Y-m');

        $releasesMonth = $this->lancamentosMes($lacamentos, $mes);

        $valorRecebidoOuPago = $this->valorPendenteMes($releasesMonth, "EFETIVADA");
        $valuePending = $this->valorPendenteMes($releasesMonth, "PENDENTE");
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
            if ($release->status_lancamento === 'EFETIVADA' && strtotime($release->data_lancamento) === strtotime(date('Y-m-d'))) {
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


        // 1. Filtra contas que devem ser incluídas
        $contasIncluidas = $user->contas()
            ->where('incluir_em_soma_inicial', true)
            ->pluck('id');


        // 2. Soma os saldos iniciais dessas contas
        $saldoInicial = DB::table('contas')
            ->whereIn('id', $contasIncluidas)
            ->sum('saldo_inicial');




        // 3. Busca lançamentos efetivados antes do mês, apenas das contas incluídas
        $lancamentosAnteriores = $user->lancamentos()
            ->where('status_lancamento', 'EFETIVADA')
            ->where('data_efetivacao', '<', $dataLimite)
            ->whereIn('conta_id', $contasIncluidas)
            ->get();



        // 4. Soma receitas e subtrai despesas
        $totalReceitasAnteriores = $lancamentosAnteriores->where('tipo_lancamento', 'RECEITA')->sum('valor');
        $totalDespesasAnteriores = $lancamentosAnteriores->where('tipo_lancamento', 'DESPESA')->sum('valor');

        return $saldoInicial + $totalReceitasAnteriores - $totalDespesasAnteriores;
    }

    public function obterSaldoAtual(object $user, $mes = null): float
    {
        $mes = $date ?? Carbon::now()->format('Y-m');
        $year = Carbon::parse($mes)->year;
        $month = Carbon::parse($mes)->month;


        // 1. Filtra contas que devem ser incluídas
        $contasIncluidas = $user->contas()
            ->where('incluir_em_soma_inicial', true)
            ->pluck('id');

        // 2. Pega o saldo inicial do mês
        $saldoDoInicioDoMes = $this->obterSaldoInicial($user, $mes);

        // 3. Busca lançamentos efetivados no mês, apenas das contas incluídas
        $lancamentosDoMes = $user->lancamentos()
            ->where('status_lancamento', 'EFETIVADA')
            ->whereYear('data_vencimento', $year)
            ->whereMonth('data_vencimento', $month)
            ->whereIn('conta_id', $contasIncluidas)
            ->get();

        // 4. Soma receitas e subtrai despesas
        $totalReceitasDoMes = $lancamentosDoMes->where('tipo_lancamento', 'RECEITA')->sum('valor');
        $totalDespesasDoMes = $lancamentosDoMes->where('tipo_lancamento', 'DESPESA')->sum('valor');

        return $saldoDoInicioDoMes + $totalReceitasDoMes - $totalDespesasDoMes;
    }
}
