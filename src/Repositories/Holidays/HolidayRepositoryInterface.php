<?php
namespace App\Repositories\Holidays;

use App\Repositories\BaseRepositoryInterface;

interface HolidayRepositoryInterface extends BaseRepositoryInterface {
    public function getDataSetByDate($dates);
    public function deleteDBByDate($dates);
}
