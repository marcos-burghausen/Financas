<?php

namespace App\Http\Traits;

trait ReleasesMonthTrait
{
    public function releasesMonth(object $data, string $month): array
    {
        $releasesMonth = [];
        foreach ($data as $data) {

            if (strtotime($data->date) >= strtotime(date("Y/{$month}/01")) && strtotime($data->date) <= strtotime(date("Y/{$month}/t"))) {
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

    public function valuePending(object $data, string $month, string $status): int
    {
        $releasesMonth = $this->releasesMonth($data, $month);

        $valuePendingMonth = [];
        foreach ($releasesMonth as $dado) {
            if ($dado->status === $status) {
                $valuePendingMonth[] = $dado->valor;
            }
        }
        return $valuePendingMonth = array_sum($valuePendingMonth);
    }
}
