<?php

namespace App\Repositories\Core;

use App\Repositories\Exceptions\NoDefinedEntity;
use App\Repositories\Interfaces\RepositoryInterface;

class BaseEloquentRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->resolvModel();
    }

    protected function resolvModel()
    {
        if (! $this->model) {
            throw new NoDefinedEntity("No Defined Entity");
        }

        return app($this->model);
    }

    public function findAllByUser()
    {
        return $this->model
                    ->where('user_id', auth()->user()->id)
                    ->get();
    }

    public function findById($id)
    {
        return $this->model
            ->where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->first();
    }

    public function findWhere($column, $value)
    {
        return $this->model
                    ->where($column, $value)
                    ->where('user_id', auth()->user()->id)->first()
                    ->get();
    }

    public function findWhereFirst($column, $value)
    {
        return $this->model
                    ->where($column, $value)
                    ->first();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $model = $this->findById($id);

        return $model->update($data);
    }

    public function delete($id)
    {
        $model = $this->findById($id);

        return $model->delete();
    }
}