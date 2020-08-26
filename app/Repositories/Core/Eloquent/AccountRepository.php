<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Account;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\AccountRepositoryInterface;

class AccountRepository extends BaseEloquentRepository implements AccountRepositoryInterface
{
    protected $model = Account::class;

    public function getAccountsByUser() {

        return $this->model::where('user_id', auth()->user()->id)->get();
    }

    public function getTypeList()
    {
        $types = [];

        foreach ($this->model::TYPES as $key => $type) {
            $types[$key] = __('global.' . $type);
        }

        return $types;
    }
    
}