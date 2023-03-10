<?php

namespace App\Repositories\Recommends;

use App\Repositories\BaseRepositoryInterface;

interface RecommendRepositoryInterface extends BaseRepositoryInterface
{
    public function selectById($id);
    public function selectBySchoolAndKouza($args);
}
