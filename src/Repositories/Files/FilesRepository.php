<?php

namespace App\Repositories\Files;

use App\Repositories\BaseRepository;
use App\Repositories\Files\FilesRepositoryInterface;
use Exception;

class FilesRepository extends BaseRepository implements FilesRepositoryInterface
{
    public function insertDB($args)
    {
        try {
            return $this->save($args);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function save($data)
    {
        $file = $this->model->newEmptyEntity();
        $file = $this->model->patchEntity($file, $data);
        $this->model->save($file);
    }

    /**
     * @param $conditions
     *
     * @return int | boolean: false if get error
     */
    public function getDataCountByCreatedDate($conditions = [])
    {
        try {
            $query = $this->model->find('all');

            if (isset($conditions['created_from'])) {
                $query->where(['created >=' => $conditions['created_from']]);
            }

            if (isset($conditions['created_to'])) {
                $query->where(['created <=' => $conditions['created_to']]);
            }

            return $query->count();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $conditions
     *
     * @return int | boolean: false if get error
     */
    public function getDataByCreatedDate($conditions = []) {
        try {
            $query = $this->model->find('all')
            ->order('id');

            if (isset($conditions['created_from'])) {
                $query->where(['created >=' => $conditions['created_from']]);
            }

            if (isset($conditions['created_to'])) {
                $query->where(['created <=' => $conditions['created_to']]);
            }

            $result = $query->select([
                'id',
                'file_path',
                'file_size',
                'file_comment',
                'created',  // with second
                'modified'  // with second
            ]);

            if (!$result) {
                throw new \Exception();
            }
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get records by ids
     *
     * @param array $ids array of ids
     * @return array
     */
    function getDataSetById($ids)
    {
        try {
            if (!empty($ids) ) {
                $query = $this->getByCondition(['id IN' => $ids]);
            }
            return $query ? $query : [];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *  Delete records by ids
     *
     * @param array $ids
     * @return  App\Model\Entity\Files|boolean
     */
    public function destroyByCondition($conditions = []) {
        $entities = $this->model->find()->where($conditions)->all();
        if (!$entities) {
            return false;
        }
        return $this->model->deleteMany($entities);
    }
}
