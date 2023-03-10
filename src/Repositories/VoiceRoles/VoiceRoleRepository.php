<?php

namespace App\Repositories\VoiceRoles;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceRoles\VoiceRoleRepositoryInterface;

class VoiceRoleRepository extends BaseRepository implements VoiceRoleRepositoryInterface
{
    /**
     * Get voice role by id
     *
     * @param $roleId
     *
     * @return App\Model\Entity\VoiceRole
     * @throws new \RecordNotFoundException
     *
    */
    public function getRoleByID($roleId) {
        return $this->model->get($roleId);
    }

    /**
     * Get voice role by id
     *
     * @return array
    */
    public function getListRoles() {
        return $this->model->find('list')
        ->order(['id'])
        ->toArray();
    }

    /**
     * Get list voice role's id by ROLE level
     *
     * @param $roleLevel
     *
     * @return array
     *
    */
    public function getListRoleIDByRole($roleLevel) {
        $query = $this->model->find('list', [
            'keyField' => 'id',
            'valueField' => 'id'
        ]);
        switch ($roleLevel) {
            case HIGHEST:
                $query->where([
                    'highest' => 1
                ]);
                break;
            default:
                break;
        }

        return $query->toArray();
    }
}
