<?php

namespace App\Repositories\VoiceSubjectTypes;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceSubjectTypes\VoiceSubjectTypeRepositoryInterface;

class VoiceSubjectTypeRepository extends BaseRepository implements VoiceSubjectTypeRepositoryInterface
{

    /**
     * Get list zeirishiLists with select fields
     *
     * @param string $keyField
     * @param $valueField
     * @param array $orders
     *
     * @return array
     *
    */
    public function getListSelectedFieldsWithKeyValue($keyField, $valueField, $orders = []) {
        return $this->model->find('list', [
            'keyField' => $keyField,
            'valueField' => $valueField
        ])
        ->toArray();
    }
}
