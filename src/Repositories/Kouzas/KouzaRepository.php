<?php
namespace App\Repositories\Kouzas;

use App\Repositories\BaseRepository;
use App\Repositories\Kouzas\KouzaRepositoryInterface;
use Exception;

class KouzaRepository extends BaseRepository implements KouzaRepositoryInterface {

    /**
     * Get list of kouzas by conditions with order
     * @param array $conditions
     * @param array $fields
     * @param array $orderBy
     *
     * @return array
     */
    public function getByConditionsOrderBy($conditions, $fields = [], $orderBy = []) {
        return $this->model
            ->find()
            ->select($fields)
            ->where($conditions)
            ->order($orderBy)
            ->all()
            ->toList();
    }

    
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
                    'kouza_name',
                    'kouza_url',
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
    public function getKouzaInfo()
    {
        try {
            $vo = $this->selectByIsActive(true)->toArray();
            if ($vo === false) {
                return false;
            }
            return $vo;
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
    function getKouzaInfoForSelectbox()
    {
        $vo = $this->getKouzaInfo();
        if ($vo === false) {
            return false;
        }

        $list = array();
        foreach ($vo as $data) {
            $list[$data['id']] = $data['kouza_name'];
        }
        return $list;
    }
}
