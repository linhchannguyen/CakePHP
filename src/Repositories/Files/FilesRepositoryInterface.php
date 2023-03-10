<?php

namespace App\Repositories\Files;

use App\Repositories\BaseRepositoryInterface;

interface FilesRepositoryInterface extends BaseRepositoryInterface
{
    public function insertDB($args);
    public function getDataCountByCreatedDate($conditions = []);
    public function getDataByCreatedDate($conditions = []);
    public function destroyByCondition($conditions = []);
}
