<?php

namespace App\Repositories\CriteoCourses;

use App\Repositories\BaseRepository;
use App\Repositories\CriteoCourses\CriteoCourseRepositoryInterface;

class CriteoCourseRepository extends BaseRepository implements CriteoCourseRepositoryInterface
{
    public function getAllList($sort = [])
    {
        return $this->model
            ->find()
            ->order($sort)
            ->all()
            ->combine('courseid', 'categoryid');
    }

    public function getByCourseId($conditions)
    {
        return $this->model
            ->find()
            ->where($conditions)
            ->enableHydration(false)
            ->first();
    }
}
