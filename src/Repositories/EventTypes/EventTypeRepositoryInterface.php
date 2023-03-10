<?php
namespace App\Repositories\EventTypes;

use App\Repositories\BaseRepositoryInterface;

interface EventTypeRepositoryInterface extends BaseRepositoryInterface {
    public function getByConditionsOrderBy($conditions, $fields = [], $orderBy = []);
}
