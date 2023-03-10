<?php

namespace App\Repositories\Events;

use App\Repositories\BaseRepositoryInterface;

interface EventRepositoryInterface extends BaseRepositoryInterface
{
    public function getDataCountBySchoolAndKouzaAndEventTypeAndEventDate($args);
    public function selectById($id);
    public function updateById($id, $data);
}
