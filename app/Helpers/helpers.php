<?php

use Carbon\Carbon;

use function PHPSTORM_META\type;
use App\Repositories\Core\Eloquent\AccountRepository;

function toCategoryType($type) :string
{
    $repository = new AccountRepository();

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