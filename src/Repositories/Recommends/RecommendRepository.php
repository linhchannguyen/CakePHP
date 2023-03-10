<?php

namespace App\Repositories\Recommends;

use App\Repositories\BaseRepository;
use App\Repositories\Recommends\RecommendRepositoryInterface;
use Exception;

class RecommendRepository extends BaseRepository implements RecommendRepositoryInterface
{
    public function selectById($id)
    {
        try {
            $result = $this->model
                ->find()
                ->select([
                    "Recommends.id",
                    "Recommends.school_id",
                    "Recommends.kouza_id",
                    "Recommends.recommend_title",
                    "Recommends.recommend_title_sub",
                    "Recommends.recommend_url",
                    "enabled_from" => "CASE WHEN Recommends.enabled_from = '-infinity' THEN '1000-01-01 00:00:00' ELSE Recommends.enabled_from END",
                    "enabled_to" => "CASE WHEN Recommends.enabled_to = 'infinity' THEN '9999-12-31 23:59:59' ELSE Recommends.enabled_to END",
                    "Recommends.order_no",
                    "Recommends.is_active",
                    "Recommends.image_url1",
                    "Recommends.image_url2",
                    "Recommends.image_url3",
                    "Recommends.sub_title1",
                    "Recommends.sub_title2",
                    "Recommends.sub_title3",
                    "Recommends.sub_title4",
                    "Recommends.sub_url1",
                    "Recommends.sub_url2",
                    "Recommends.sub_url3",
                    "Recommends.sub_url4",
                    "Recommends.created",
                    "Recommends.modified",
                    "school_name" => "Schools.school_name",
                    "kouza_name" => "Kouzas.kouza_name"
                ])
                ->leftJoinWith('Schools')
                ->leftJoinWith('Kouzas')
                ->where(['Recommends.id IN' => $id])
                ->enableHydration(false)
                ->order(['Kouzas.id', 'Recommends.id']);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    //------------------------------------------------
    /// @brief  Update record based on id column
    /// @param  $id     id value or array of ids
    /// @param  $args   parameter array
    ///         - $args['school_id']            School ID (foreign key)
    ///         - $args['kouza_id']             Course ID (foreign key)
    ///         - $args['recommend_title']      title string
    ///         - $args['recommend_title_sub']  subtitle string
    ///         - $args['recommend_url']        Link URL string
    ///         - $args['enabled_from']         Posting valid period start date and time
    ///         - $args['enabled_to']           End date and time of publication validity period
    ///         - $args['order_no']             Sort order adjustment parameter
    ///         - $args['is_active']            enable/disable flag
    ///         - $args['image_url1']           Lecture image 1
    ///         - $args['image_url2']           Lecture image 2
    ///         - $args['image_url3']           Lecture image 3

    ///         - $args['sub_title1']           Link (Subtitle) 1
    ///         - $args['sub_title2']           Link (Subtitle) 2
    ///         - $args['sub_title3']           Link (Subtitle) 3
    ///         - $args['sub_title4']           Link (Subtitle) 4

    ///         - $args['sub_url1']             Image 1 for link (subtitle)
    ///         - $args['sub_url2']             Image 2 for link (subtitle)
    ///         - $args['sub_url3']             Image 3 for link (subtitle)
    ///         - $args['sub_url4']             Image 4 for link (subtitle)
    /// @retval !false  success
    /// @retval false   error
    /// @author ChanN:
    //------------------------------------------------
    public function updateById($id, $data)
    {
        try {
            $data['modified'] = date('Y-m-d H:i:s');
            $this->model
                ->updateAll(
                    $data,
                    ['id IN' => $id]
                );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //------------------------------------------------
    /// @brief  Acquire records based on school building ID and course ID
    /// @param  $args                       conditional array
    ///         - $args['school_id']        School ID (foreign key)
    ///         - $args['kouza_id']         Course ID (foreign key)
    ///         - $args['is_active']        enable/disable flag
    ///         - $args['order']            Column to be sort key
    ///             - 'id'                  sort by id
    ///             - 'kouza_order_no'      Sort by course
    ///             - 'modified'            Sort by date modified
    ///             - 'school'              sort by school
    ///         - $args['is_desc']          whether in descending order
    ///             - true                  descending order
    ///             - false                 ascending order
    ///         - $args['offset']           Acquisition start record offset
    ///         - $args['limit']            Acquired record count
    /// @retval !false  record array
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function selectBySchoolAndKouza($args)
    {
        try {
            $conditions = [];
            $orders = [];
            if (isset($args['school_id'])) {
                $conditions['Recommends.school_id'] = $args['school_id'];
            }

            if (isset($args['kouza_id'])) {
                $conditions['Recommends.kouza_id'] = $args['kouza_id'];
            }

            if (isset($args['is_active'])) {
                $conditions['Recommends.is_active'] = $args['is_active'];
            }

            $sql_is_desc = 'ASC';
            if (isset($args['is_desc']) && ($args['is_desc'])) {
                $sql_is_desc = 'DESC';
            }
            if (isset($args['order'])) {
                if ('modified' == $args['order']) {
                    $orders['Recommends.modified'] = $sql_is_desc;
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['Recommends.id'] = $sql_is_desc;
                } else if ('kouza_order_no' == $args['order']) {
                    $orders['Kouzas.order_no'] = $sql_is_desc;
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['Recommends.id'] = $sql_is_desc;
                } else if ('school' == $args['order']) {
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['Kouzas.order_no'] = $sql_is_desc;
                    $orders['Recommends.id'] = $sql_is_desc;
                } else {
                    $orders['Recommends.id'] = $sql_is_desc;
                }
            }

            $result = $this->model
                ->find()
                ->select([
                    "Recommends.id",
                    "Recommends.school_id",
                    "Recommends.kouza_id",
                    "Recommends.recommend_title",
                    "Recommends.recommend_title_sub",
                    "Recommends.recommend_url",
                    "enabled_from" => "CASE WHEN Recommends.enabled_from = '-infinity' THEN '1000-01-01 00:00:00' ELSE Recommends.enabled_from END",
                    "enabled_to" => "CASE WHEN Recommends.enabled_to = 'infinity' THEN '9999-12-31 23:59:59' ELSE Recommends.enabled_to END",
                    "Recommends.order_no",
                    "Recommends.is_active",
                    "Recommends.image_url1",
                    "Recommends.image_url2",
                    "Recommends.image_url3",
                    "Recommends.sub_title1",
                    "Recommends.sub_title2",
                    "Recommends.sub_title3",
                    "Recommends.sub_title4",
                    "Recommends.sub_url1",
                    "Recommends.sub_url2",
                    "Recommends.sub_url3",
                    "Recommends.sub_url4",
                    "Recommends.created",
                    "Recommends.modified",
                    "school_name" => "Schools.school_name",
                    "kouza_name" => "Kouzas.kouza_name"
                ])
                ->leftJoinWith('Schools')
                ->leftJoinWith('Kouzas')
                ->where($conditions)
                ->enableHydration(false)
                ->order($orders);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    //------------------------------------------------
    /// @brief  add record
    /// @param  $args   parameter array
    ///         - $args['school_id']            School ID (foreign key)
    ///         - $args['kouza_id']             Course ID (foreign key)
    ///         - $args['recommend_title']      title string
    ///         - $args['recommend_title_sub']  subtitle string
    ///         - $args['recommend_url']        Link URL string
    ///         - $args['enabled_from']         Posting valid period start date and time
    ///         - $args['enabled_to']           End date and time of publication validity period
    ///         - $args['order_no']             Sort order adjustment parameter
    ///         - $args['is_active']            enable/disable flag
	///         - $args['image_url1']           Lecture image 1
	///         - $args['image_url2']           Lecture image 2
	///         - $args['image_url3']           Lecture image 3
	///         - $args['sub_title1']              Link (Subtitle) 1
	///         - $args['sub_title2']              Link (Subtitle) 2
	///         - $args['sub_title3']              Link (Subtitle) 3
	///         - $args['sub_title4']              Link (Subtitle) 4
	///         - $args['sub_url1']             Image 1 for link (subtitle)
	///         - $args['sub_url2']             Image 2 for link (subtitle)
	///         - $args['sub_url3']             Image 3 for link (subtitle)
	///         - $args['sub_url4']             Image 4 for link (subtitle)
    /// @retval !false  ID of the inserted record
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function insert($data)
    {
        try {
            $recommends = $this->model->newEmptyEntity();
            $recommends = $this->model->patchEntity($recommends, $data);
            if(!empty($recommends->getErrors())){
                return false;
            }
            if ($this->model->save($recommends)) {
                // The $recommends entity contains the id now
                return $recommends->id;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    //------------------------------------------------
    /// @brief  Delete records based on id column
    /// @param  $id     id value or array of ids
    /// @retval !false  success
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function deleteById($id)
    {
        try {
            $entity = $this->model->find()->select('id')->where(['id IN' => $id])->toList();
            if (!$entity) {
                return false;
            }
            if ($this->model->deleteMany($entity)) {
                return true;
            }
        } catch (Exception $e) {
            return 500;
        }
    }
}
