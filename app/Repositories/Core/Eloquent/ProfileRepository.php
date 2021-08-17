<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\User;
use App\Models\Category;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\ProfileRepositoryInterface;

class ProfileRepository extends BaseEloquentRepository implements ProfileRepositoryInterface
{
    protected $model = User::class;

    /**
     * Get user of a given email
     *
     * @return User
     */
    public function findByEmail(string $email) {

        return $this->model::where('email', $email)
            ->first();
    }


    /**
     * Returns users with notification enabled
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getUsersForNotification()
    {
        return $this->model::where('enable_notification', true)
            ->get();
    }
}