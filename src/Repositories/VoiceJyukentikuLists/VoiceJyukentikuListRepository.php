<?php

namespace App\Repositories\VoiceJyukentikuLists;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceJyukentikuLists\VoiceJyukentikuListRepositoryInterface;

class VoiceJyukentikuListRepository extends BaseRepository implements VoiceJyukentikuListRepositoryInterface
{

    /**
     * Get list Jyukentik
     *
     * @return array
     *
    */
    public function getList() {
        return $this->model->find('list', [
            'order' => 'id'
        ])->toArray();
    }
}
