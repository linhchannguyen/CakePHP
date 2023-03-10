<?php

namespace App\Repositories\VoicePartOptions;

use App\Repositories\BaseRepository;
use App\Repositories\VoicePartOptions\VoicePartOptionRepositoryInterface;

class VoicePartOptionRepository extends BaseRepository implements VoicePartOptionRepositoryInterface
{
    /**
     * Get voice part by form id with order
     *
     * @param $partId
     *
     * @return array
     *
    */
    public function getListByPartID($partId) {
        return $this->model->find('list', [
            'keyField' => 'value',
            'valueField' => 'name'
        ])->where([
            'part_id' => $partId
        ])
        ->select(['value', 'name'])
        ->toArray();
    }
}
