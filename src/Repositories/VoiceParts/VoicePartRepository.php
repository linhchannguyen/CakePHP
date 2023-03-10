<?php

namespace App\Repositories\VoiceParts;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceParts\VoicePartRepositoryInterface;

class VoicePartRepository extends BaseRepository implements VoicePartRepositoryInterface
{
    /**
     * Get voice part by form id
     *
     * @param $partId
     *
     * @return App\Model\Entity\VoicePart
     * @throws new \RecordNotFoundException
     *
    */
    public function getByID($partId) {
        return $this->model->get($partId);
    }

    /**
     * Get voice part by form id with order, key value pairs
     *
     * @param $formId
     * @param string $keyField
     * @param $valueField
     * @param array $order
     *
     * @return array
     *
    */
    public function getListSelectedFieldsWithKeyValue($formId, $keyField, $valueField, $orders = []) {
        return $this->model->find('list', [
            'keyField' => $keyField,
            'valueField' => $valueField
        ])->where([
            'form_id' => $formId
        ])
        ->order('id')
        ->order($orders)  // add more order
        ->toArray();
    }

    /**
     * Get voice part by form id with order
     *
     * @param $formId
     * @param array $fields
     * @param array $order
     *
     * @return array
     *
    */
    public function getListByFormID($formId, $fields = [], $orders = []) {
        return $this->model->find('list', [
            'valueField' => $fields
        ])->where([
            'form_id' => $formId
        ])
        ->order($orders)  // add more order
        ->toArray();
    }

    /**
     * Get all voice part by form id with order, fix form != 1
     *
     * @param $formId
     * @param array $orders
     *
     * @return array
     *
    */
    public function getAllByFormIDFixForm($formId, $orders = []) {
        $query = $this->model->find()->where([
            'form_id' => $formId
        ])->where([
            'slug NOT IN' => [ZEIRISHI,TACNUMBER,NAME,FURIGANA,BIRTHDAY,MAIL,PHOTO,RELEASE],
            'NOT' => [
                'fix_form' => 1
            ],
        ]);

        return $query->order($orders)->all()->toArray();
    }

    /**
     * Get with conditions
     *
     * @param $formId
     * @param array $orders
     *
     * @return array
     *
    */
    public function getAllByFormID($formId, $orders = []) {
        $query = $this->model->find()->where([
            'form_id' => $formId
        ])->where([
            'slug NOT IN' => [ZEIRISHI,TACNUMBER,NAME,FURIGANA,BIRTHDAY,MAIL,PHOTO,RELEASE],
        ]);

        return $query->order($orders)->all()->toArray();
    }

    /**
     * Count voice part by form id
     *
     * @param $formId
     * @param int $hidden
     * @param array $notConditions
     *
     * @return int
     *
    */
    public function countByFormID($formId, $hidden = 0, $notConditions = []) {
        $query = $this->model->find('all')->where([
            'form_id' => $formId,
            'hidden' => $hidden
        ]);

        if (!empty($notConditions)) {

            if (isset($notConditions['id'])) {
                $query->where([
                    'NOT' => [
                        'id' => $notConditions['id']
                    ]
                ]);
            }
            if (isset($notConditions['slug'])) {
                $query->where([
                    'NOT' => [
                        'slug IN' => $notConditions['slug']
                    ]
                ]);
            }
        }

        return $query->count();
    }

    /**
     * Association delete: VoicePart, VoicePartOption
     *
     * @param $partId
     *
     * @return boolean
    */
    public function deleteByPartID($partId) {
        $entity = $this->model->get($partId);
        return $this->model->delete($entity);
    }

    /**
     * Get details voice part by form id
     *
     * @param $formId
     *
     * @return App\Model\Entity\VoicePart
     * @throws new \RecordNotFoundException
     *
    */
    public function getDetailsByFormID($formId) {
        return $this->model->find()->where([
            'form_id' => $formId,
            'slug IN' => [ZEIRISHI]
        ])
        ->order(['id' => 'ASC', 'fix_form' => 'ASC'])
        ->first();
    }

    /**
     * Get details voice part by slug
     *
     * @param $slug
     *
     * @return array
     *
    */
    public function getListPartIDBySlug($slug) {
        return $this->model->find('list')->where([
            'slug' => $slug
        ])
        ->select(['id'])
        ->toArray();
    }

    /**
     * Get voice part by form id with option
     *
     * @param $partId
     *
     * @return App\Model\Entity\VoicePart
     *
    */
    public function getByIDWithPartOptions($partId) {
        return $this->model->get($partId, [
            'contain' => ['VoicePartOptions']
        ]);
    }


    /**
     * Check partId is valid with categoryId
     *
     * @param $partId
     * @param $categoryId
     *
     * @return App\Model\Entity\VoicePart
     *
    */
    public function checkExistVoicePartsWithCategoryID($partId, $categoryId) {
        return $this->model
        ->exists($this->model->find()
            ->join([
                'VoiceForms'=>  [
                    'table'      => 'voice_forms',
                    'type'       => 'INNER',
                    'conditions' => 'VoiceForms.id = VoiceParts.form_id',
                ]
            ])
            ->where([
                'VoiceForms.category_id' => $categoryId,
                'VoiceParts.id' => $partId
            ])
            ->select(['VoiceParts.id'])
        );
    }
}
