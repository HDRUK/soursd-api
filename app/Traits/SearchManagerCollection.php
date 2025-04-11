<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * SearchManagerCollection
 *
 * Adds utilities to handle results which are converted to collections
 */
trait SearchManagerCollection
{
    use CommonFunctions;

    public function paginateCollection($items)
    {
        $perPage = request()->integer('per_page', (int)$this->getSystemConfig('PER_PAGE'));
        $page = request()->integer('page');

        $items = $items instanceof Collection ? $items : Collection::make($items);
        $withTotal = collect(['total' => $items->count()]);

        return $withTotal->merge(new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page));
    }
}
