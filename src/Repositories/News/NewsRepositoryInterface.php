<?php
namespace App\Repositories\News;

use App\Repositories\BaseRepositoryInterface;

interface NewsRepositoryInterface extends BaseRepositoryInterface {
    public function selectBySchoolAndTitleDate($args);
    public function selectById($id);
    public function updateById($id, $data);
    public function insert($data);
    public function deleteById($id);
}
