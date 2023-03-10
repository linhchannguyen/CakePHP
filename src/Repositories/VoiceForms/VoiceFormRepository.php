<?php

namespace App\Repositories\VoiceForms;

use App\Model\Entity\VoiceForm;
use App\Repositories\BaseRepository;
use App\Repositories\VoiceForms\VoiceFormRepositoryInterface;

class VoiceFormRepository extends BaseRepository implements VoiceFormRepositoryInterface
{
    /**
     * Get voice forms by category id
     *
     * @param $formId
     *
     * @return App\Model\Entity\VoiceForm
     * @throws new \RecordNotFoundException
     *
    */
    public function getVoiceFormsByCategoryID($categoryId) {
        return $this->model->find('all', [
            'conditions' => [
                'category_id' => $categoryId
            ],
            'order' => ['id' => 'ASC']
        ]);
    }

    /**
     * Get voice form by form id with Association
     *
     * @param $formId
     *
     * @return App\Model\Entity\VoiceForm
     * @throws new \RecordNotFoundException
    */
    public function getVoiceFormByIDWithAssoc($formId) {
        return $this->model->get($formId, [
            'contain' => ['VoiceParts']
        ]);
    }

    /**
     * Get voice form by form id
     *
     * @param $formId
     *
     * @return App\Model\Entity\VoiceForm
    */
    public function getVoiceFormByID($formId) {
        return $this->model->get($formId, [
            'order' => ['id' => 'DESC']
        ]);
    }

    /**
     * Change send mail
     *
     * @param $formId
     * @param array $data
     *
     * @return boolean
    */
    public function updateFields($formId, $data) {
        if (!empty($formId)) {
                $query = $this->model->query();
                $query->update()
                ->set($data)
                ->where(['id' => $formId])
                ->execute();
        }
        return false;
    }

    /**
     * Create or Update record
     *
     * @param array $data
     *
     * @return App\Model\Entity\VoiceForm | boolean
     * @throws new \RecordNotFoundException
     *
    */
    public function createOrUpdate($data) {

        if (isset($data['lock'])) {
            $data['`lock`'] = $data['lock'];
            unset($data['lock']);
        }

        if (!empty($data['id'])) {
            $entity = $this->model->get($data['id']);
        } else {
            $entity = $this->model->newEmptyEntity();
        }
        $entity = $this->model->patchEntity($entity, $data);
        return $this->model->save($entity);
    }

    /**
     * Check exists records with categoryId and formId
     *
     * @param $formId
     * @param $categoryId
     *
     * @return boolean
     *
    */
    public function checkExistVoiceFormsWithCategoryID($formId, $categoryId) {
        return $this->model->exists($this->model->find('all', [
            'conditions' => [
                'id' => $formId,
                'category_id' => $categoryId
            ],
            'fields' => ['id']
        ]));
    }
}
