<?php
namespace App\Repositories\Schools;

use App\Repositories\BaseRepositoryInterface;

interface SchoolRepositoryInterface extends BaseRepositoryInterface {
    public function selectByIsActive($is_active);
    public function getSchoolInfo();
    public function getSchoolInfoForSelectbox();
}
