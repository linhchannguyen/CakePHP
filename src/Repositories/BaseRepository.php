<?php
namespace App\Repositories;

use App\Repositories\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface {

    protected $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function getAll() {
        return $this->model->find()->all()->toList();
    }

    public function getByCondition($conditions, $fields = []) {
        return $this->model
            ->find()
            ->select($fields)
            ->where($conditions)
            ->all()
            ->toList();
    }

    public function getWithFields($fields) {
        return $this->model
            ->find()
            ->select($fields)
            ->all()
            ->toList();
    }

    public function getById($id) {
        return $this->model
            ->find()
            ->where(['id' => $id])
            ->first();
    }

    public function findById($id) {
        return $this->model
            ->find()
            ->where(['id' => $id])
            ->first();
    }

    public function findByCondition($conditions, $fields = []) {
        return $this->model
            ->find()
            ->select($fields)
            ->where($conditions)
            ->first();
    }

    public function create($data) {
        $entity = $this->model->newEmptyEntity();
        $entity = $this->model->patchEntity($entity, $data);
        return $this->model->save($entity);
    }

    public function createMany($data) {
        $entity = $this->model->newEntities($data);
        return $this->model->saveMany($entity);
    }

    public function update($id, $data) {
        $entity = $this->model->find()->where(['id' => $id])->first();
        if (!$entity) {
            return false;
        }
        $entity = $this->model->patchEntity($entity, $data);
        return $this->model->save($entity);
    }

    public function destroy($id) {
        $entity = $this->model->find()->where(['id' => $id])->first();
        if (!$entity) {
            return false;
        }
        return $this->model->delete($entity);
    }

    public function getWithKeyValuePairs($keyField, $valueField) {
        return $this->model
            ->find('list', [
                'keyField' => $keyField,
                'valueField' => $valueField
            ])->toArray();
    }

    public function paginate($conditions = []) {
        return $this->model->find()->where($conditions);
    }

    public function getKeyValuePairsWithCondition($fields, $conditions) {
        [$keyField, $valueField] = $fields;
        return $this->model
            ->find('list', [
                'keyField' => $keyField,
                'valueField' => $valueField
            ])
            ->where($conditions)
            ->order(['id' => 'ASC'])
            ->toArray();
    }
}
