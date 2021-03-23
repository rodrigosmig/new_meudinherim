<?php

namespace App\Repositories\Core;

use Illuminate\Database\Eloquent\Model;
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

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findWhere($column, $value)
    {
        return $this->model
                    ->where($column, $value)
                    ->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($entity, array $data)
    {
        return $entity->update($data);
    }

    public function save($entity)
    {
        return $entity->save();
    }

    public function delete($entity)
    {
        return $entity->delete();
    }
}