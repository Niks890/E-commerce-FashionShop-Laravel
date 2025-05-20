<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginatorHelper
{
    public static function getSmartPaginationRange(LengthAwarePaginator $paginator, $onEachSide = 2)
    {
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        $start = max($current - $onEachSide, 1);
        $end = min($current + $onEachSide, $last);

        return [
            'start' => $start,
            'end' => $end,
            'current' => $current,
            'last' => $last,
        ];
    }
}
