<?php

namespace App\Repositories\VoiceZeirishiLists;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceZeirishiLists\VoiceZeirishiListRepositoryInterface;

class VoiceZeirishiListRepository extends BaseRepository implements VoiceZeirishiListRepositoryInterface
{
    /**
     * Get list zeirishiLists
     *
     * @return array
     *
    */
    public function getList() {
        return $this->model->find('list', [
            'order' => 'id'
        ])->toArray();
    }

    /**
     * Get list zeirishiLists with conditions
     *
     * @return array
     *
    */
    public function getListID() {
        return $this->model->find('list', [
            'keyField' => 'id',
            'valueField' => 'id'
        ])
        ->where([
            'none' => 1,
            'subject_type_id' => 2
        ])
        ->toArray();
    }

    /**
     * Get list zeirishiLists with select fields
     *
     * @param string $keyField
     * @param $valueField
     *
     * @return array
     *
    */
    public function getListSelectedFieldsWithKeyValue($keyField, $valueField) {
        return $this->model->find('list', [
            'keyField' => $keyField,
            'valueField' => $valueField
        ])
        ->toArray();
    }
}
