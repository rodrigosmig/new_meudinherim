<?php

use Carbon\Carbon;

use App\Services\AccountService;

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

?>