<?php

namespace App\Repositories\Schools;

use App\Repositories\BaseRepository;
use App\Repositories\Schools\SchoolRepositoryInterface;
use Exception;

class SchoolRepository extends BaseRepository implements SchoolRepositoryInterface
{
    //------------------------------------------------
    /// @brief  Fetch records based on the value of is_active column
    /// @param  $is_active condition for is_active column
    ///         - true
    ///         - false
    /// @retval !false  record array
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function selectByIsActive($is_active)
    {
        try {
            $result = $this->model
                ->find()
                ->select([
                    'id',
                    'school_name',
                    'school_url',
                    'school_tag_name',
                    'order_no',
                    'is_active',
                    'created',
                    'modified'
                ])
                ->where(['is_active' => $is_active])
                ->enableHydration(false)
                ->order(['order_no', 'id'])
                ->all();
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }


    //------------------------------------------------
    /// @brief  Get school information
    /// @param  none
    /// @retval !false  normal termination
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function getSchoolInfo()
    {
        try {
            $vo = $this->selectByIsActive(true);
            if ($vo === false) {
                return false;
            }
            return $vo->toArray();
        } catch (Exception $e) {
            return false;
        }
    }


    //------------------------------------------------
    /// @brief  Get school building information array for select box
    /// @param  none
    /// @retval !false  normal termination
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function getSchoolInfoForSelectbox()
    {
        $vo = $this->getSchoolInfo();
        if ($vo === false) {
            return false;
        }

        $list = array();
        foreach ($vo as $data) {
            $list[$data['id']] = $data['school_name'];
        }
        return $list;
    }
}
