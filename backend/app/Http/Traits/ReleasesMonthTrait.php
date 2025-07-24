<?php

namespace App\Http\Traits;

use App\Models\Conta;
use DateTime;

trait ReleasesMonthTrait
{
    public function classifiesReleases(object $lacamentos, string $tipoLancamentos, $data = null): array
    {
        $mes = $data ?? date('Y-m');
        // $mes = DateTime::createFromFormat('Y-m', $data)->format('m');

        // $lacamentosMes = $this->lancamentosMes($lacamentos, $mes);
        // info(['lacamentosMes ' => $lacamentosMes]);

        $valorRecebidoOuPago = $this->valorPendenteMes($lacamentos, $mes, "Efetivada");
        // dd($valorRecebidoOuPago);
        $valuePending = $this->valorPendenteMes($lacamentos, $mes, "Pendente");
        // $valuePending = $this->valuePending($releases, date('m'), "AGUARDANDO");
        $valorTotalMes = $this->valorLancamentosMes($lacamentos, $mes);
        // $releasesgroupByMonth = $this->groupByMonth($lacamentos);
        // $addTotalValueMonth = $this->addTotalValueMonth($releasesgroupByMonth);
        $releasesMonth = $this->lancamentosMes($lacamentos, $mes);
        $Data = [
            "valuePay" => $valorRecebidoOuPago,
            "valuePending" => $valuePending,
            "valueTotalMonth" => $valorTotalMes,
            // "{$tipoLancamentos}GroupByMonth" => $releasesgroupByMonth,
            // "{$tipoLancamentos}AddTotalValueMonth" => $addTotalValueMonth,
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

    public function totalExpensesDay($releasesMonth)
    {
        $totalExpensesDay = 0;
        foreach ($releasesMonth as $release) {
            if ($release->status === 'Efetivada' && strtotime($release->dataLancamento) === strtotime(date('Y-m-d'))) {
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
        // info('dataInicio ' . $dataInicio);
        $dataFim = (new DateTime($dataInicio))->format("Y-m-t");
        // info('dataFim ' . $dataFim);
        foreach ($lancamentos as $lancamento) {
            if ($lancamento && $lancamento->dataVencimento >= $dataInicio && $lancamento->dataVencimento <= $dataFim) {
                $lancamentosMes[] = $lancamento;
            }
        }
        return $lancamentosMes;
    }

    public function valorLancamentosMes(object $lancamentos, string $mes): int
    {
        $releasesMonth = $this->lancamentosMes($lancamentos, $mes);

        $valueReleasesMonth = [];
        foreach ($releasesMonth as $releases) {
            $valueReleasesMonth[] = $releases->valor;
        }
        return $valueReleasesMonth = array_sum($valueReleasesMonth);
    }

    public function valorPendenteMes(object $lancamentos, string $mes, string $status): int
    {
        $lancamentosMes = $this->lancamentosMes($lancamentos, $mes);
        // dd($lancamentosMes);
        $valorPendenteMes = 0;
        foreach ($lancamentosMes as $lancamento) {
            if ($lancamento->status === $status) {
                $valorPendenteMes += $lancamento->valor;
            }
        }
        return $valorPendenteMes; //= array_sum($valorPendenteMes);
    }
    public function valorPendente(object $data, string $status): int
    {
        $valuePending = [];
        foreach ($data as $release) {
            if ($release->status === $status) {
                $valuePending[] = $release->valor;
            }
        }
        return $valuePending = array_sum($valuePending);
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

    public function obterSaldoInicial(object $user, $mes = null): float
    {
        $mes = $mes ?? date('Y-m');
        $contas = $user->contas()->get();
        $somaSaldo = $user->contas()
            ->where('incluirEmSomaInicial', true)
            ->sum('saldoInicial');
        $saldoInicial = 0;
        // // $despesas = $user->expenses()->get();
        // // $receitas = $user->revenues()->get();
        
        $dataLimite = (new DateTime("$mes-01"))->modify('-1 day')->format('Y-m-d');

        foreach ($contas as $conta) {
            if ($conta->incluirEmSomaInicial) {
                $despesas = $user->expenses()
                    ->where('conta', $conta->name)
                    ->where('status', 'Efetivada')
                    ->where('dataLancamento', '<=', $dataLimite)
                    ->sum('valor');
                // ->get();
                $receitas = $user->revenues()
                    ->where('conta', $conta->name)
                    ->where('status', 'Efetivada')
                    ->where('dataLancamento', '<=', $dataLimite)
                    ->sum('valor');
                $saldoInicial = $somaSaldo + $receitas - $despesas;
            }
        }

        return $saldoInicial; // Retorna o saldo inicial formatado
    }

    public function obterSaldoAtual(object $user, $mes = null): float
    {
        $mes = $mes ?? date('Y-m');
        $contas = $user->contas()->get();
        $saldoAtual = 0;
        foreach ($contas as $conta) {
            if ($conta->incluirEmSomaInicial) {
                $despesas = $user->expenses()
                    ->where('conta', $conta->name)
                    ->where('status', 'Efetivada')
                    ->where('dataLancamento', '<=', "$mes-31")
                    ->sum('valor');

                $receitas = $user->revenues()
                    ->where('conta', $conta->name)
                    ->where('status', 'Efetivada')
                    ->where('dataLancamento', '<=', "$mes-31")
                    ->sum('valor');

                $saldoAtual = $receitas - $despesas;
            }
        }

        return $saldoAtual;
    }
}
