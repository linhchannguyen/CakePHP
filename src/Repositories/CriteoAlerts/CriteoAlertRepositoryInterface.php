<?php

namespace App\Repositories\CriteoAlerts;

interface CriteoAlertRepositoryInterface
{
    public function getCriteoAlertByCondition($conditions);
}
