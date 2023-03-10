<?php

namespace App\Repositories\VoiceReleases;

use App\Repositories\BaseRepository;
use App\Repositories\VoiceReleases\VoiceReleaseRepositoryInterface;

class VoiceReleaseRepository extends BaseRepository implements VoiceReleaseRepositoryInterface
{

    /**
     * Get list voice release by initial
     *
     * @param int $initial
     *
     * @return array
     *
    */
    public function getListByInitial($initial = null) {
        $query = $this->model->find('list');
        if (!is_null($initial)) {
            $query->where([
                'NOT' => ['initial' => $initial]
            ]);
        }
        return $query->order('id')->toArray();
    }
}
