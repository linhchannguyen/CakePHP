<?php

namespace App\Repositories\VoiceUserFormDataOptions;

use App\Model\Entity\VoiceUserFormDataOption;
use App\Repositories\BaseRepository;
use App\Repositories\VoiceUserFormDataOptions\VoiceUserFormDataOptionRepositoryInterface;

class VoiceUserFormDataOptionRepository extends BaseRepository implements VoiceUserFormDataOptionRepositoryInterface
{
    /**
     * Get details user form data by part ID
     *
     * @param $partId
     * @param array $zeiriSearchLists
     *
     * @return array
     *
     */
    public function getUserFormDataOptionByPartID($partId, $zeiriSearchLists) {
        return $this->model->find('list', [
            'valueField' => 'user_form_data_id'
        ])
        ->where([
            'part_id' => $partId,
            'value IN' => $zeiriSearchLists
        ])->toArray();
    }

    /**
     * Get all user form data by form ID
     *
     * @param $userFormId
     *
     * @return array
     *
     */
    public function getUserFormDataOptionByUserFormID($userFormId) {
        return $this->model->find('all')
        ->contain(['VoiceParts'])
        ->where([
            'user_form_data_id' => $userFormId
        ])
        ->order(['VoiceUserFormDataOptions.id' => 'DESC'])
        ->toArray();
    }

    /**
     * Create or Update record
     *
     * @param array $data
     *
     * @return App\Model\Entity\VoiceUserFormDataOption | boolean
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
}
