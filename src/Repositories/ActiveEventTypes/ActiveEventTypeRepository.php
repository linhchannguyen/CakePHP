<?php

namespace App\Repositories\ActiveEventTypes;

use App\Repositories\BaseRepository;
use App\Repositories\ActiveEventTypes\ActiveEventTypeRepositoryInterface;

class ActiveEventTypeRepository extends BaseRepository implements ActiveEventTypeRepositoryInterface {

    /**
     * Delete all active event types records
     *
     * @param
     * @return int
     */
    public function deleteAllByConditions($conditions = []) {
        return $this->model->deleteAll($conditions);
    }
}
