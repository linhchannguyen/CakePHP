<?php
namespace App\Repositories;

interface BaseRepositoryInterface {
    public function getAll();
    
    public function getByCondition($conditions, $fields);

    public function getWithFields($fields);

    public function getById($id);

    public function findById($id);

    public function findByCondition($conditions, $fields);

    public function create($data);

    public function update($id, $data);

    public function destroy($id);

    public function getWithKeyValuePairs($keyField, $valueField);

    public function paginate($conditions);

    public function getKeyValuePairsWithCondition($fields, $conditions);
}
