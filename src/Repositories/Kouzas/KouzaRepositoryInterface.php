<?php
namespace App\Repositories\Kouzas;

use App\Repositories\BaseRepositoryInterface;

interface KouzaRepositoryInterface extends BaseRepositoryInterface {
    public function getByConditionsOrderBy($conditions, $fields = [], $orderBy = []);
}
