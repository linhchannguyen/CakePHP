<?php

namespace App\Repositories\Criteos;

interface CriteoRepositoryInterface
{
    public function getListCriteo($courseid);
    public function getListCriteoByCooperation($conditions, $fields);
    public function checkPageId($page_id);
    public function registCriteo($criteoRegistInfo);
    public function destroy($id);
    public function saveAll($entity, $data);
}
