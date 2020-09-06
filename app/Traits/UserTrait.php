<?php

namespace App\Traits;

use App\Scopes\UserScope;
use App\Observers\UserObserver;

trait UserTrait
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::observe(UserObserver::class);

        static::addGlobalScope(new UserScope);
    }
}