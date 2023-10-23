<?php

namespace App\Http\Traits;

use DateTime;

trait GroupReleasesTrait
{
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
                $totalMonth += $release['valor'];
            }

            $totalByMonth[$month] = $totalMonth;
        }
        return $totalByMonth;
    }
}
