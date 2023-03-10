<?php

namespace App\Repositories\VoiceZeirishiKamokuLists;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceZeirishiKamokuLists\VoiceZeirishiKamokuListRepositoryInterface;

class VoiceZeirishiKamokuListRepository extends BaseRepository implements VoiceZeirishiKamokuListRepositoryInterface
{

    /**
     * Get list zeirishiLists kamoku
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
