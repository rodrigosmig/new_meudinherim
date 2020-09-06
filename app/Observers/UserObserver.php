<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class UserObserver
{
    /**
     * Handle the model "creating" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function creating(Model $model)
    {
        $model->user_id = auth()->user()->id;
    }

    /**
     * Handle the model "updating" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function updating(Model $model)
    {
        $model->user_id = auth()->user()->id;
    }
}
