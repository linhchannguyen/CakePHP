<?php

namespace App\Repositories\CriteoCourses;

interface CriteoCourseRepositoryInterface
{
    public function getAllList();
    public function getByCourseId($conditions);
}
