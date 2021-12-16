<?php

use Carbon\Carbon;

use App\Services\AccountService;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

function toCategoryType($type) :string
{
    $repository = app(AccountService::class);

    $types = $repository->getTypeList();

    return $types[$type];
}

function toBrMoney($money) :string
{
    return 'R$ ' . number_format($money, 2, ',', '.');
}

function toBrDate($date) :string
{
    return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
}

/**
 * @param int $current_page
 * @param int $per_page
 * @param array $collection
 * @return array
 */
function paginate($current_page, $per_page, $collection): array
{
    $totalRegisters = count($collection);
    $last_page = ceil($totalRegisters / $per_page);

    if ($current_page > $last_page) {
        $current_page = $last_page;
    }

    $starting_point = ($current_page * $per_page) - $per_page;
    
    $items = array_slice($collection, $starting_point, $per_page);

    $result = new Paginator($items, $totalRegisters, $per_page, $current_page, [
        'path' => request()->url(),
        'query' => request()->query(),
    ]);

    return $result->toArray();
}
