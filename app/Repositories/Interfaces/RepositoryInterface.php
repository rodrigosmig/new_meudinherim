<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function findById($id);
    public function findWhere($column, $value);
    public function create(array $data);
    public function update(Model $entity, array $data);
    public function save(Model $entity);
    public function delete(Model $entity);
}