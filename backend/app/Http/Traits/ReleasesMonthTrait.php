<?php

namespace App\Http\Traits;

use DateTime;

trait ReleasesMonthTrait
{
    public function classifiesReleases(object $lacamentos, string $tipoLancamentos, $mes = null): array
    {
        $mes = $mes ?? date('m');

        $status = 'PAGA';
        $type = 'Pay';
        if ($tipoLancamentos === 'Revenues') {
            $status = 'RECEBIDA';
            $type = 'Received';
        }

        $valorRecebidoOuPago = $this->valorPendenteMes($lacamentos, $mes, $status);
        $valuePending = $this->valorPendente($lacamentos, "AGUARDANDO", $mes);
        // $valuePending = $this->valuePending($releases, date('m'), "AGUARDANDO");
        $valorTotalMes = $this->valorLancamentosMes($lacamentos, $mes);
        $releasesgroupByMonth = $this->groupByMonth($lacamentos);
        $addTotalValueMonth = $this->addTotalValueMonth($releasesgroupByMonth);
        $releasesMonth = $this->lancamentosMes($lacamentos, $mes);
        $Data = [
            "Value{$type}{$tipoLancamentos}" => $valorRecebidoOuPago,
            "ValuePending{$tipoLancamentos}" => $valuePending,
            "ValueTotal{$tipoLancamentos}Month" => $valorTotalMes,
            "{$tipoLancamentos}GroupByMonth" => $releasesgroupByMonth,
            "{$tipoLancamentos}AddTotalValueMonth" => $addTotalValueMonth,
            "{$tipoLancamentos}Month" => $releasesMonth,
        ];

        if ($tipoLancamentos === 'Expenses') {
            $totalExpensesDay = $this->totalExpensesDay($releasesMonth);
            $totalByCategoryExpenses = $this->totalByCategory($releasesMonth);
            $Data["totalExpensesDay"] = $totalExpensesDay;
            $Data["TotalByCategoryExpenses"] = $totalByCategoryExpenses;
        }

        return $Data;
    }

    public function totalExpensesDay($releasesMonth)
    {
        $totalExpensesDay = 0;
        foreach ($releasesMonth as $release) {
            if ($release->status === 'PAGA' && strtotime($release->date) === strtotime(date('Y-m-d'))) {
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
        foreach ($lancamentos as $lancamento) {
            if ($lancamento && strtotime($lancamento->date) >= strtotime(date("Y/{$mes}/01")) && strtotime($lancamento->date) <= strtotime(date("Y/{$mes}/t"))) {
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
        $valorPendenteMes = [];
        foreach ($lancamentosMes as $lancamento) {
            if ($lancamento->status === $status) {
                $valorPendenteMes[] = $lancamento->valor;
            }
        }
        info($valorPendenteMes);
        return $valorPendenteMes = array_sum($valorPendenteMes);
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

            $date = new DateTime($release['date']);
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
}
