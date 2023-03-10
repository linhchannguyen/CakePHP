<?php
namespace App\Repositories\ActiveEventTypes;

use App\Repositories\BaseRepositoryInterface;

interface ActiveEventTypeRepositoryInterface extends BaseRepositoryInterface {
    public function deleteAllByConditions($conditions = []);
}
