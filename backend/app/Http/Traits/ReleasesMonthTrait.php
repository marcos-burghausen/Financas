<?php

namespace App\Http\Traits;

use DateTime;

trait ReleasesMonthTrait
{
    public function classifiesReleases(object $releases, string $typeReleases): array
    {
        $status = 'PAGA';
        $type = 'Pay';
        if ($typeReleases === 'Revenues') {
            $status = 'RECEBIDA';
            $type = 'Received';
        }

        $valueReceivedOrPay = $this->valuePending($releases, $status);
        $valuePending = $this->valuePending($releases, "AGUARDANDO");
        // $valuePending = $this->valuePending($releases, date('m'), "AGUARDANDO");
        $valueTotalMonth = $this->valueReleasesMonth($releases, date('m'));
        $releasesgroupByMonth = $this->groupByMonth($releases);
        $addTotalValueMonth = $this->addTotalValueMonth($releasesgroupByMonth);
        $releasesMonth = $this->releasesMonth($releases, date('m'));
        $Data = [
            "Value{$type}{$typeReleases}" => $valueReceivedOrPay,
            "ValuePending{$typeReleases}" => $valuePending,
            "ValueTotal{$typeReleases}Month" => $valueTotalMonth,
            "{$typeReleases}GroupByMonth" => $releasesgroupByMonth,
            "{$typeReleases}AddTotalValueMonth" => $addTotalValueMonth,
            "{$typeReleases}Month" => $releasesMonth,
        ];

        if ($typeReleases === 'Expenses') {
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

    public function releasesMonth(object $data, string $month): array
    {
        $releasesMonth = [];
        foreach ($data as $data) {

            if ($data && strtotime($data->date) >= strtotime(date("Y/{$month}/01")) && strtotime($data->date) <= strtotime(date("Y/{$month}/t"))) {
                $releasesMonth[] = $data;
            }
        }
        return $releasesMonth;
    }

    public function valueReleasesMonth(object $data, string $month): int
    {
        $releasesMonth = $this->releasesMonth($data, $month);

        $valueReleasesMonth = [];
        foreach ($releasesMonth as $releases) {
            $valueReleasesMonth[] = $releases->valor;
        }
        return $valueReleasesMonth = array_sum($valueReleasesMonth);
    }

    public function valuePendingMonth(object $data, string $month, string $status): int
    {
        $releasesMonth = $this->releasesMonth($data, $month);

        $valuePendingMonth = [];
        foreach ($releasesMonth as $release) {
            if ($release->status === $status) {
                $valuePendingMonth[] = $release->valor;
            }
        }
        return $valuePendingMonth = array_sum($valuePendingMonth);
    }
    public function valuePending(object $data, string $status): int
    {
        $valuePending = [];
        foreach ($data as $release) {
            info($release);
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
