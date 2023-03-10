<?php

namespace App\Repositories\News;

use App\Repositories\BaseRepository;
use App\Repositories\News\NewsRepositoryInterface;
use Exception;

class NewsRepository extends BaseRepository implements NewsRepositoryInterface
{
    //------------------------------------------------
    /// @brief  Get records based on school building ID and title date
    /// @param  $args                       conditional array
    ///         - $args['school_id']        school building ID
    ///         - $args['is_active']        Display state
    ///         - $args['news_date_from']   Title date (start filtering)
    ///         - $args['news_date_to']     Title date (finishing end)
    ///         - $args['order']            Column to be sort key
    ///             - 'id'                  sort by id
    ///             - 'news_date'           Sort by title date
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
    public function selectBySchoolAndTitleDate($args)
    {
        try {
            $conditions = [];
            $orders = [];
            if (isset($args['school_id'])) {
                $conditions['News.school_id'] = $args['school_id'];
            }

            if (isset($args['is_active'])) {
                $conditions['News.is_active'] = $args['is_active'];
            }

            if (isset($args['urgency'])) {
                $conditions['News.urgency'] = $args['urgency'];
            }

            if (!empty($args['news_date_from'])) {
                $conditions['News.news_date >='] = $args['news_date_from'];
            }

            if (!empty($args['news_date_to'])) {
                $conditions['News.news_date <='] = $args['news_date_to'];
            }

            $sql_is_desc = 'ASC';
            if (isset($args['is_desc']) && ($args['is_desc'])) {
                $sql_is_desc = 'DESC';
            }
            if (isset($args['order'])) {
                if ('modified' == $args['order']) {
                    $orders['News.modified'] = $sql_is_desc;
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['News.id'] = $sql_is_desc;
                } else if ('news_date' == $args['order']) {
                    $orders['News.news_date'] = $sql_is_desc;
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['News.id'] = $sql_is_desc;
                } else if ('school' == $args['order']) {
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['News.news_date'] = $sql_is_desc;
                    $orders['News.id'] = $sql_is_desc;
                } else {
                    $orders['News.id'] = $sql_is_desc;
                }
            }

            $result = $this->model
                ->find()
                ->select([
                    "News.id",
                    "News.school_id",
                    "News.news_title",
                    "News.news_title_sub",
                    "News.news_date",
                    "News.news_url",
                    "enabled_from" => "CASE WHEN News.enabled_from = '-infinity' THEN '1000-01-01 00:00:00' ELSE News.enabled_from END",
                    "enabled_to" => "CASE WHEN News.enabled_to = 'infinity' THEN '9999-12-31 23:59:59' ELSE News.enabled_to END",
                    "News.order_no",
                    "News.is_active",
                    "News.urgency",
                    "News.created",
                    "News.modified",
                    "school_name" => "Schools.school_name"
                ])
                ->leftJoinWith('Schools')
                ->where($conditions)
                ->enableHydration(false)
                ->order($orders);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }


    //------------------------------------------------
    /// @brief  Get records based on id column value
    /// @param  $id     id value or array of ids
    /// @retval !false  record
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function selectById($id)
    {
        try {
            $result = $this->model
                ->find()
                ->select([
                    "News.id",
                    "News.school_id",
                    "News.news_title",
                    "News.news_title_sub",
                    "News.news_date",
                    "News.news_url",
                    "enabled_from" => "CASE WHEN News.enabled_from = '-infinity' THEN '1000-01-01 00:00:00' ELSE News.enabled_from END",
                    "enabled_to" => "CASE WHEN News.enabled_to = 'infinity' THEN '9999-12-31 23:59:59' ELSE News.enabled_to END",
                    "News.order_no",
                    "News.is_active",
                    "News.urgency",
                    "News.created",
                    "News.modified",
                    "school_name" => "Schools.school_name"
                ])
                ->leftJoinWith('Schools')
                ->where(['News.id IN' => $id])
                ->enableHydration(false)
                ->order(['News.news_date', 'News.order_no', 'News.id']);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    //------------------------------------------------
    /// @brief  Update record based on id column
    /// @param  $data   parameter array
    ///         - $data['id']               News id
    ///         - $data['school_id']        School ID (foreign key)
    ///         - $data['news_title']       title string
    ///         - $data['news_title_sub']   subtitle string
    ///         - $data['news_date']        title date
    ///         - $data['news_url']         Link URL string
    ///         - $data['enabled_from']     Posting valid period start date and time
    ///         - $data['enabled_to']       End date and time of publication validity period
    ///         - $data['order_no']         Sort order adjustment parameter
    ///         - $data['is_active']        enable/disable flag
    ///         - $data['urgency']          Urgency
    /// @retval !false  success
    /// @retval false   error
    /// @author ChanNL
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
    /// @brief  add record
    /// @param  $data   parameter array
    ///         - $data['school_id']        School ID (foreign key)
    ///         - $data['news_title']       title string
    ///         - $data['news_title_sub']   subtitle string
    ///         - $data['news_date']        title date
    ///         - $data['news_url']         Link URL string
    ///         - $data['enabled_from']     Posting valid period start date and time
    ///         - $data['enabled_to']       End date and time of publication validity period
    ///         - $data['order_no']         Sort order adjustment parameter
    ///         - $data['is_active']        enable/disable flag
    ///         - $data['urgency']          Urgency
    /// @retval !false  ID of the inserted record
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function insert($data)
    {
        try {
            $news = $this->model->newEmptyEntity();
            $news = $this->model->patchEntity($news, $data);
            if ($this->model->save($news)) {
                // The $news entity contains the id now
                return $news->id;
            }
            return false;
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
