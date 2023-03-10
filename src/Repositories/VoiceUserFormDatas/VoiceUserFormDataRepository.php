<?php

namespace App\Repositories\VoiceUserFormDatas;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceUserFormDatas\VoiceUserFormDataRepositoryInterface;

class VoiceUserFormDataRepository extends BaseRepository implements VoiceUserFormDataRepositoryInterface
{
    /**
     * Get details user form data by form ID
     *
     * @param $formId
     *
     * @return App\Model\Entity\VoiceUserFormData
     * @throws new \RecordNotFoundException
     *
     */
    public function getDetailUserFormDataByFormID($formId) {
        return $this->model->find()
        ->where([
            'form_id' => $formId
        ])
        ->order(['id' => 'ASC'])
        ->first();
    }

    /**
     * Create or Update record
     *
     * @param array $data
     *
     * @return App\Model\Entity\VoiceUserFormData | boolean
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
     * Get details user form data by ID
     *
     * @param $id
     *
     * @return App\Model\Entity\VoiceUserFormData
     * @throws new \RecordNotFoundException
     */
    public function findById($id) {
        return $this->model->get($id);
    }

    /**
     * Get details user form data by ID
     *
     * @param $id
     *
     * @return boolean
     *
     */
    public function destroy($id) {
        $entity = $this->model->get($id);
        return $this->model->delete($entity);
    }

    /**
     * Get list user form data by conditions
     *
     * @param array $conditions
     * @param array $fields
     * @param array $orders
     * @param array $options
     *
     * @return array
     *
     */
    public function getByConditions($conditions = [], $fields = [], $orders = [], $options = []) {
        $query = $this->model->find()
        ->where($conditions)
        ->order($orders)
        ->select($fields);
        if (isset($options['offset'])) {
            $query->offset($options['offset']);
        }
        if (isset($options['limit'])) {
            $query->limit($options['limit']);
        }
        return $query->toArray();
    }

    /**
     * Get list user form data by conditions
     *
     * @param array $conditions
     * @param array $fields
     * @param array $orders
     *
     * @return int
     *
     */
    public function countByConditions($conditions = [], $fields = [], $orders = []) {
        return $this->model->find()
        ->where($conditions)
        ->order($orders)
        ->select($fields)
        ->count();
    }
}
