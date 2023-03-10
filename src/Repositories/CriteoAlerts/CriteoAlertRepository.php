<?php

namespace App\Repositories\CriteoAlerts;

use App\Repositories\BaseRepository;
use App\Repositories\CriteoAlerts\CriteoAlertRepositoryInterface;

class CriteoAlertRepository extends BaseRepository implements CriteoAlertRepositoryInterface
{
    public function getCriteoAlertByCondition($conditions)
    {
        $feedList = $this->model
            ->find()
            ->where($conditions)
            ->group(['id', 'name', 'url', 'browsetime'])
            ->enableHydration(false)
            ->all();
        return $feedList;
    }
}
