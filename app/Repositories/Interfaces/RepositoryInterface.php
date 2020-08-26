<?php

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    public function findAllByUser();
    public function findById($id);
    public function findWhere($column, $value);
    public function findWhereFirst($column, $value); //testar alterar por first
    public function store(array $data);
    public function update($id, array $data);
    public function delete($id);
}