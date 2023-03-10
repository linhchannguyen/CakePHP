<?php

namespace App\Repositories\Events;

use App\Repositories\BaseRepository;
use App\Repositories\Events\EventRepositoryInterface;
use Exception;

class EventRepository extends BaseRepository implements EventRepositoryInterface
{
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
                    "Events.id",
                    "Events.school_id",
                    "Events.kouza_id",
                    "Events.event_title",
                    "Events.event_body",
                    "Events.event_date",
                    "Events.event_type",
                    "Events.order_no",
                    "Events.is_active",
                    "Events.created",
                    "Events.modified",
                    "school_name" => "Schools.school_name",
                    "kouza_name" => "Kouzas.kouza_name"
                ])
                ->leftJoinWith('Schools')
                ->leftJoinWith('Kouzas')
                ->where(['Events.id IN' => $id])
                ->enableHydration(false)
                ->order(['Kouzas.id', 'Events.id']);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    //------------------------------------------------
    /// @brief  Update records based on id columns
    /// @param  $id     id value or array of ids
    /// @param  $args   Parameter array
    ///         - $args['school_id']    School ID (Foreign key)
    ///         - $args['kouza_id']     Course ID (Foreign key)
    ///         - $args['event_title']  Title string
    ///         - $args['event_body']   Content description string
    ///         - $args['event_date']   Title Date and time
    ///         - $args['event_type']   Event type code (0 is the type code that has no type)
    ///         - $args['order_no']     Parameters for alignment and smooth adjustment
    ///         - $args['is_active']    Enable/Disable Flags
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
    /// @brief  Add a record
    /// @param  $args   Parameter array
    ///         - $args['school_id']    School ID (Foreign key)
    ///         - $args['kouza_id']     Course ID (Foreign key)
    ///         - $args['event_title']  Title string
    ///         - $args['event_body']   Content description string
    ///         - $args['event_date']   Title Date and time
    ///         - $args['event_type']   Event type code (0 is the type code that has no type)
    ///         - $args['order_no']     Parameters for alignment and smooth adjustment
    ///         - $args['is_active']    Enable/Disable Flags
    /// @retval !false  success
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
            $entity = $this->model->find()->where(['id IN' => $id])->toList();
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

    //------------------------------------------------
    /// @brief  Get the number of records based on school ID and course ID
    /// @param  $args                       Conditional array
    ///         - $args['school_id']        School ID (Foreign key)
    ///         - $args['kouza_id']         Course ID (Foreign key)
    ///         - $args['event_type']       Event Type Code
    ///         - $args['event_date_from']  Event date and time (start)
    ///         - $args['event_date_to']    Event Date and Time (End)
    ///         - $args['is_active']        Enable/Disable Flags
    /// @retval !false  record array
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function getDataCountBySchoolAndKouzaAndEventTypeAndEventDate($args)
    {
        try {
            $conditions = [];
            $orders = [];
            if (isset($args['school_id'])) {
                $conditions['Events.school_id'] = $args['school_id'];
            }

            if (isset($args['kouza_id'])) {
                $conditions['Events.kouza_id'] = $args['kouza_id'];
            }

            if (isset($args['event_type'])) {
                $conditions['Events.event_type'] = $args['event_type'];
            }

            if (isset($args['is_active'])) {
                $conditions['Events.is_active'] = $args['is_active'];
            }

            if (!empty($args['event_date_from'])) {
                $conditions['Events.event_date >='] = $args['event_date_from'];
            }

            if (!empty($args['event_date_to'])) {
                $conditions['Events.event_date <='] = date('Y-m-d', strtotime('+1 day', strtotime($args['event_date_to'])));
            }

            $sql_is_desc = 'ASC';
            if (isset($args['is_desc']) && ($args['is_desc'])) {
                $sql_is_desc = 'DESC';
            }
            if (isset($args['order'])) {
                if ('modified' == $args['order']) {
                    $orders['Events.modified'] = $sql_is_desc;
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['Events.event_type'] = $sql_is_desc;
                    $orders['Events.id'] = $sql_is_desc;
                } else if ('event_date' == $args['order']) {
                    $orders['Events.event_date'] = $sql_is_desc;
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['Events.event_type'] = $sql_is_desc;
                    $orders['Events.id'] = $sql_is_desc;
                } else if ('school' == $args['order']) {
                    $orders['Schools.order_no'] = $sql_is_desc;
                    $orders['Events.event_date'] = $sql_is_desc;
                    $orders['Events.event_type'] = $sql_is_desc;
                    $orders['Events.id'] = $sql_is_desc;
                } else {
                    $orders['Events.id'] = $sql_is_desc;
                }
            }

            $result = $this->model
                ->find()
                ->select([
                    "Events.id",
                    "Events.school_id",
                    "Events.kouza_id",
                    "Events.event_title",
                    "Events.event_body",
                    "Events.event_date",
                    "Events.event_type",
                    "Events.order_no",
                    "Events.is_active",
                    "Events.created",
                    "Events.modified",
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
}
