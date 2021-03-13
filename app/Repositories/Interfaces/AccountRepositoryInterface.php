<?php

namespace App\Repositories\Interfaces;

interface AccountRepositoryInterface
{
    public function getAccountsByUser();
    public function getTypeList();
}