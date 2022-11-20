<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    public function all();

    public function find($id);

    public function findWhere(string $column, $operator = '=', $value = null);

    public function where(string $column, $operator = '=', $value = null);

    public function paginate(int $perPage = 10);

    public function create(array $properties);

    public function update($id, array $properties);

    public function updateWhere($column, $operator = '=', $value = null, array $properties = []);

    public function delete($id);
}
