<?php

namespace App\Repositories\VoiceUsers;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceUsers\VoiceUserRepositoryInterface;

class VoiceUserRepository extends BaseRepository implements VoiceUserRepositoryInterface
{

    /**
     * Get voice user by username and course id
     *
     * @param string $username
     * @param $categoryId
     *
     * @return App\Model\Entity\VoiceUser
     * @throws new \RecordNotFoundException
     *
    */
    public function getByUsernameCategoryID($username, $categoryId) {
        return $this->model->find()->where([
            'username' => $username,
            'category_id IN' => $categoryId
        ])->first();
    }

    /**
     * Get voice users
     *
     * @return array
     *
    */
    public function getAllUsers() {
        return $this->model->find('all');
    }

    /**
     * Delete voice users
     *
     * @param @userId
     *
     * @return boolean
     *
    */
    public function deletebyID($userId) {
        $entity = $this->model->get($userId);
        return $this->model->delete($entity);
    }

    /**
     * Get voice user by id
     *
     * @param $userId
     *
     * @return App\Model\Entity\VoiceUser
     * @throws new \RecordNotFoundException
     *
    */
    public function getByID($userId) {
        return $this->model->get($userId, [
            'fields' => ['id', 'username', 'role_id', 'category_id']
        ]);
    }

    /**
     * Create or Update record
     * @param array $data
     *
     * @return App\Model\Entity\VoiceUser | boolean
     *
    */
    public function createOrUpdate($data) {
        if (!empty($data['id'])) {
            $entity = $this->model->get($data['id']);
        } else {
            $entity = $this->model->newEmptyEntity();
        }
        $entity = $this->model->patchEntity($entity, $data);

        if ($entity && empty($entity->getErrors())) {
            $this->model->save($entity);
        }
        return $entity;
    }
}
