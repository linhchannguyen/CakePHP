<?php

namespace App\Repositories\VoiceHeads;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceHeads\VoiceHeadRepositoryInterface;

class VoiceHeadRepository extends BaseRepository implements VoiceHeadRepositoryInterface
{
    /**
     * Get list voice head
     *
     * @return array
     *
     *
    */
    public function getList() {
        return $this->model->find('list', [
            'order' => 'id'
        ])->toArray();
    }
}
