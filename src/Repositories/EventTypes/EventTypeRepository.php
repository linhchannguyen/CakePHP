<?php
namespace App\Repositories\EventTypes;

use App\Repositories\BaseRepository;
use App\Repositories\EventTypes\EventTypeRepositoryInterface;

class EventTypeRepository extends BaseRepository implements EventTypeRepositoryInterface {

    /**
     * Get list of event types by conditions with order
     * @param array $conditions
     * @param array $fields
     * @param array $orderBy
     *
     * @return array
     */
    public function getByConditionsOrderBy($conditions, $fields = [], $orderBy = []) {
        return $this->model
            ->find()
            ->select($fields)
            ->where($conditions)
            ->order($orderBy)
            ->all()
            ->toList();
    }
}
