<?php

namespace App\Repositories\VoiceCategories;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceCategories\VoiceCategoryRepositoryInterface;

class VoiceCategoryRepository extends BaseRepository implements VoiceCategoryRepositoryInterface
{
    /**
     *  Get all records
     *
     * @param array $orderBy
     *
     * @return array
     *
     */
    public function getVoiceCategories($orderBy = null) {
        $query = $this->model->find('list', [
            'fields' => ['id', 'name']
        ]);

        if (isset($orderBy)) {
            $query->order(['id']);
        }

        return $query->toArray();
    }

    /**
     * Get details record
     *
     * @param $categoryId
     *
     * @return App\Model\Entity\VoiceCategory
     * @throws new \NotFoundException
     *
     */
    public function getVoiceCategoryByID($categoryId) {
        return $this->model->get($categoryId, [
            'contains' => ['name'],
        ]);
    }

    /**
     * Get all records
     *
     * @return array
     *
     */
    public function getAllCategories() {
        return $this->model->find('all')->toArray();
    }

    /**
     * Create or Update record
     *
     * @param array $data
     *
     * @return App\Model\Entity\VoiceCategory | boolean
     *
     */
    public function createOrUpdate($data) {
        if (!empty($data['id'])) {
            $entity = $this->model->get($data['id']);
        } else {
            $entity = $this->model->newEmptyEntity();
        }
        $entity = $this->model->patchEntity($entity, $data);
        return $this->model->save($entity);
    }

    /**
     * Delete record
     *
     * @param $categoryId
     *
     * @return boolean
     * @throws new \NotFoundException
     */
    public function delete($categoryId) {
        if (!empty($categoryId)) {
            $entity = $this->model->get($categoryId);
        }
        return $this->model->delete($entity);
    }
}
