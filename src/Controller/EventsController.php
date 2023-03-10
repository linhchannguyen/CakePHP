<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\Events\EventRepository;
use App\Repositories\EventTypes\EventTypeRepository;
use App\Repositories\Kouzas\KouzaRepository;
use App\Repositories\Schools\SchoolRepository;
use App\Traits\dateMiscTrait;
use App\Traits\lib_utilityTrait;
use Cake\Http\Exception\InternalErrorException;
use Exception;

class EventsController extends AppController
{
    use dateMiscTrait, lib_utilityTrait;
    public $m_types_info = [];
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('custom');
        $this->loadModel('Events');
        $this->loadModel('EventTypes');
        $this->loadModel('Kouzas');
        $this->loadModel('Schools');
        $this->eventRepository = new EventRepository($this->Events);
        $this->eventTypeRepository = new EventTypeRepository($this->EventTypes);
        $this->kouzaRepository = new KouzaRepository($this->Kouzas);
        $this->schoolRepository = new SchoolRepository($this->Schools);
        $this->getEvent_typeInfoForSelectbox();
    }

    public function index()
    {
        try {
            $mode = null;
            if (!empty($this->request->getQuery('f'))) {
                $mode = $this->request->getQuery('f');
            } else if (!empty($this->request->getData('f'))) {
                $mode = $this->request->getData('f');
            }
            if (empty($this->request->getQuery('f'))) {
                $this->request->getSession()->delete('events_add_finish_mode');
            }
            $school_list = $this->schoolRepository->getSchoolInfoForSelectbox();
            $school_list = array(0 => '校舎') + $school_list;
            // kouza list
            $kouza_list = $this->kouzaRepository->getKouzaInfoForSelectbox();
            $kouza_list = array(0 => '講座') + $kouza_list;
            // Event type list
            $event_type_list = array(0 => 'イベント種別') + $this->m_types_info;
            if (!$mode) {
                // form control generation
                $controls = $this->_genFormIndexControls();
                // school list
                $data = $this->getEventsList();
                $this->set('school_list', $school_list);
                $this->set('kouza_list', $kouza_list);
                $this->set('event_type_list', $event_type_list);
                $this->set('controls', $controls);
                $this->set('num_records', $data['num_records']);
                $this->set('records', $data['records']);
                $table_body = $this->makeTableTrEventsList($data['records']->toArray(), true, 'ids[]');
                $this->set('table_body', $table_body);
                $this->set('title_head', __d('events', 'TITLE_HEAD'));
                $this->render('index');
            } else if ($mode == 'events_add') {
                $this->request->getSession()->write('is_reload_events_add', false);
                $getSessionEventsAddFinish = $this->request->getSession()->read('events_add_finish_mode') ?? null;
                if ($getSessionEventsAddFinish) {
                    if ($getSessionEventsAddFinish['added']) {
                        $events_form = $this->initFormData();
                    } else {
                        $events_form = $getSessionEventsAddFinish['events_form'];
                        $this->set('errors', $getSessionEventsAddFinish['errors']);
                        $this->set('error_message', $getSessionEventsAddFinish['error_message']);
                    }
                } else {
                    $events_form = $this->initFormData();
                }
                $controls = $this->_genFormEditControls();
                $this->set('controls', $controls);
                $this->set('events_form', $events_form);
                $this->set('school_list', $school_list);
                $this->set('kouza_list', $kouza_list);
                $this->set('event_type_list', $event_type_list);
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_ADD'));
                $this->render('events_add');
            } else if ($mode == 'events_edit') {
                $id = $this->request->getData('id') ?? null;
                $this->request->getSession()->write('is_reload_events_edit', false);
                $getSessionEventsEditFinish = $this->request->getSession()->read('events_edit_finish_mode') ?? null;
                if ($getSessionEventsEditFinish) {
                    $id = $getSessionEventsEditFinish['id'];
                    $events_form = $getSessionEventsEditFinish['events_form'];
                    $this->set('errors', $getSessionEventsEditFinish['errors']);
                    $this->set('error_message', $getSessionEventsEditFinish['error_message']);
                } else {
                    $events = $this->eventRepository->selectById($id)->first();
                    if (!$events) {
                        $events_form = $this->initFormData();
                    }else{
                        $events_form = $this->initFormDataByRecordId($events);
                    }
                }
                $controls = $this->_genFormEditControls();
                $this->set('controls', $controls);
                $this->set('school_list', $school_list);
                $this->set('kouza_list', $kouza_list);
                $this->set('event_type_list', $event_type_list);
                $this->set('events_form', $events_form);
                $this->set('id', $id);
                $this->set('school_list', $school_list);
                $this->set('kouza_list', $kouza_list);
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_EDIT'));
                $this->render('events_edit');
            } else if ($mode == 'events_delete_confirm') {
                $id = $this->request->getData('id');
                $records = $this->eventRepository->selectById($id)->first();
                if (!$records) {
                    throw new Exception('');
                }
                $table_body = $this->makeTableTrEventsList(array($records), false);
                $this->set('table_body', $table_body);
                $this->set('id', $id);
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_DELETE_CONFIRM'));
                $this->render('events_delete_confirm');
            } else if ($mode == 'events_delete_finish') {
                $id = $this->request->getData('id');
                $this->deleteDBById($id);
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_DELETED'));
                $this->render('events_delete_finish');
            } else if ($mode == 'events_change_records_visible_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->updateDBByIdWithParam($ids, true);
                if (!$result) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_VISIBLE_FINISH'));
                $this->render('events_change_records_visible_finish');
            } else if ($mode == 'events_change_records_invisible_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->updateDBByIdWithParam($ids, false);
                if (!$result) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_INVISIBLE_FINISH'));
                $this->render('events_change_records_invisible_finish');
            } else if ($mode == 'events_change_records_delete_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->deleteDBById($ids);
                if ($result === 500) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_DELETE_FINISH'));
                $this->render('events_change_records_delete_finish');
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('events', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    //------------------------------------------------
    /// @brief  Create events
    /// @author ChanNL
    //------------------------------------------------
    public function eventsAddFinish()
    {
        try {
            $eventsAddFinish['Events'] = $this->request->getData();
            // validation
            $defaultValidator = $this->Events->getValidator('custom');
            $validate = $defaultValidator->validate($eventsAddFinish['Events']);
            if (!$validate) { // empty
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_ADD_FINISH'));
                $param = $eventsAddFinish['Events'];
                $events_add_finish_mode = [
                    'events_form' => $eventsAddFinish['Events'],
                    'errors' => [],
                    'error_message' => ''
                ];
                $inserted_record = [];
                //Exit without doing anything when reloading
                if (!$this->request->getSession()->read('is_reload_events_add')) {
                    $id = $this->insertDB($param);
                    if (!$id) {
                        throw new Exception('');
                    }
                    $inserted_record = $this->eventRepository->selectById($id)->first();
                }
                $table_body = '';
                if ($inserted_record) {
                    $table_body = $this->makeTableTrEventsList(array($inserted_record));
                }
                $events_add_finish_mode['added'] = true;
                $this->set('table_body', $table_body);
                $this->request->getSession()->write('events_add_finish_mode', $events_add_finish_mode);
                $this->request->getSession()->write('is_reload_events_add', true);
                $this->render('events_add_finish');
            } else {
                $errors = array();
                foreach ($validate as $errKey => $errVal) {
                    $messageError = "";
                    foreach ($errVal as $message) {
                        $messageError = $message;
                        break;
                    }
                    $errors[$errKey] = '<br /><span class="warn">' . $messageError . '</span>';
                }
                if (!empty($errors['event_date'])) {
                    if (!empty($errors['event_ym'])) {
                        unset($errors['event_date']);
                    } else if (!empty($errors['event_day'])) {
                        unset($errors['event_date']);
                    } else if (!empty($errors['event_time_h'])) {
                        unset($errors['event_date']);
                    } else if (!empty($errors['event_time_m'])) {
                        unset($errors['event_date']);
                    }
                }
                $num_total_error = count($errors);
                $error_message = '';
                if (0 < $num_total_error) {
                    $error_message = "<p class=\"warn\">{$num_total_error}個のエラーがあります。</p>";
                }
                $this->request->getSession()->write('events_add_finish_mode', [
                    'events_form' => $eventsAddFinish['Events'],
                    'errors' => $errors,
                    'error_message' => $error_message,
                    'added' => false
                ]);
                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'f' => 'events_add',
                        'recovery' => 'true'
                    ],
                ]);
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('events', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    //------------------------------------------------
    /// @brief  Update events
    /// @author ChanNL
    //------------------------------------------------
    public function eventsEditFinish()
    {
        try {
            $eventsEditFinish['Events'] = $this->request->getData();
            $id = $this->request->getData('id') ?? null;
            // validation
            $defaultValidator = $this->Events->getValidator('custom');
            $validate = $defaultValidator->validate($eventsEditFinish['Events']);
            if (!$validate) { // empty
                $this->set('id', $id);
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_EDIT_FINISH'));
                $param = $eventsEditFinish['Events'];
                $table_body = '';
                //Exit without doing anything when reloading
                if (!$this->request->getSession()->read('is_reload_events_edit')) {
                    $inserted_record = [];
                    $update = $this->updateDBById($param);
                    if (!$update) {
                        throw new Exception('');
                    }
                    $inserted_record = $this->eventRepository->selectById($id)->first();
                    if (!$inserted_record) {
                        throw new Exception('');
                    }
                    if ($inserted_record) {
                        $table_body = $this->makeTableTrEventsList(array($inserted_record));
                    }
                }
                $this->set('table_body', $table_body);
                $this->request->getSession()->write('events_edit_finish_mode', [
                    'id' => $id,
                    'events_form' => $eventsEditFinish['Events'],
                    'errors' => [],
                    'error_message' => ''
                ]);
                $this->request->getSession()->write('is_reload_events_edit', true);
                $this->render('events_edit_finish');
            } else {
                $errors = array();
                foreach ($validate as $errKey => $errVal) {
                    $messageError = "";
                    foreach ($errVal as $message) {
                        $messageError = $message;
                        break;
                    }
                    $errors[$errKey] = '<br /><span class="warn">' . $messageError . '</span>';
                }
                if (!empty($errors['event_date'])) {
                    if (!empty($errors['event_ym'])) {
                        unset($errors['event_date']);
                    } else if (!empty($errors['event_day'])) {
                        unset($errors['event_date']);
                    } else if (!empty($errors['event_time_h'])) {
                        unset($errors['event_date']);
                    } else if (!empty($errors['event_time_m'])) {
                        unset($errors['event_date']);
                    }
                }
                $num_total_error = count($errors);
                $error_message = '';
                if (0 < $num_total_error) {
                    $error_message = "<p class=\"warn\">{$num_total_error}個のエラーがあります。</p>";
                }
                $this->request->getSession()->write('events_edit_finish_mode', [
                    'id' => $id,
                    'events_form' => $eventsEditFinish['Events'],
                    'errors' => $errors,
                    'error_message' => $error_message
                ]);
                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'f' => 'events_edit',
                        'recovery' => 'true'
                    ],
                ]);
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('Events', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    private function getEvent_typeInfoForSelectbox()
    {
        $conditions = [
            'is_active' => true
        ];
        $fields = ['id', 'icon', 'order_no', 'event_type_name', 'inner_class', 'content'];
        $orderBy = ['order_no', 'id'];

        $event_types = $this->eventTypeRepository->getByConditionsOrderBy($conditions, $fields, $orderBy);
        for ($i = 0; $i < count($event_types); $i++) {
            $this->m_types_info[$event_types[$i]['id']] = $event_types[$i]['event_type_name'];
        }
    }

    //------------------------------------------------
    /// @brief  Store the information obtained from the form in the DB
    /// @param  none
    /// @retval !false  ID of the inserted record
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function insertDB($param)
    {
        $fd = $param;

        $datetime = sprintf(
            "%s-%02d %s:%s:00",
            $fd['event_ym'],
            $fd['event_day'],
            $fd['event_time_h'],
            $fd['event_time_m']
        );
        $datetime = date('Y-m-d H:i:s', strtotime($datetime));
        if (false === isDateTime($datetime)) {
            return false;
        }
        $params = array(
            'school_id' => $fd['school'],
            'kouza_id' => $fd['kouza'],
            'event_title' => $fd['text_title'],
            'event_body' => $fd['text_body'],
            'event_date' => $datetime,
            'event_type' => $fd['event_type'],
            'order_no' => 0,
            'is_active' => 1
        );
        return $this->eventRepository->insert($params);
    }

    public function updateDBByIdWithParam($ids, $is_active)
    {
        $params = array(
            'is_active' => $is_active
        );
        return $this->eventRepository->updateById($ids, $params);
    }

    //------------------------------------------------
    /// @brief  Update records on DB with information obtained from form
    /// @param  $id     record ID
    /// @retval !false  success
    /// @retval false   error
    /// @author ChanlNL
    //------------------------------------------------
    public function updateDBById($param)
    {
        $fd = $param;
        $datetime = sprintf(
            "%s-%02d %s:%s:00",
            $fd['event_ym'],
            $fd['event_day'],
            $fd['event_time_h'],
            $fd['event_time_m']
        );
        $datetime = date('Y-m-d H:i:s', strtotime($datetime));
        if (false === isDateTime($datetime)) {
            return false;
        }
        $params = array(
            'school_id' => $fd['school'],
            'kouza_id' => $fd['kouza'],
            'event_title' => $fd['text_title'] ?? null,
            'event_body' => $fd['text_body'] ?? null,
            'event_date' => $datetime,
            'event_type' => $fd['event_type'] ?? null,
            'order_no' => $fd['text_order_no'],
            'is_active' => $fd['radio_is_active'] ? 1 : 0
        );
        return $this->eventRepository->updateById($fd['id'], $params);
    }

    //------------------------------------------------
    /// @brief  Delete record with given id
    /// @param  $id     Record ID or ID array
    /// @retval !false  success
    /// @retval false   error
    /// @author ChanNL
    //------------------------------------------------
    public function deleteDBById($id)
    {
        return $this->eventRepository->deleteById($id);
    }

    //------------------------------------------------
    /// @brief  Get info events
    /// @author ChanNL
    //------------------------------------------------
    public function eventsDetail()
    {
        try {
            $id = null;
            if (!empty($this->request->getQuery('id'))) {
                $id = $this->request->getQuery('id');
            } else if (!empty($this->request->getData('id'))) {
                $id = $this->request->getData('id');
            }
            $events_list = $this->eventRepository->selectById($id)->first();
            $event_type_list = $this->m_types_info;
            $event_type_name = $event_type_list[arrayGet($events_list, 'event_type', '')];
            $contents = array(
                'id' => arrayGet($events_list, 'id', ''),
                'event_title' => arrayGet($events_list, 'event_title', ''),
                'event_body' => $this->lfToBr(arrayGet($events_list, 'event_body', '')),
                'event_date' => arrayGet($events_list, 'event_date', ''),
                'order_no' => arrayGet($events_list, 'order_no', ''),
                'is_active' => arrayGet($events_list, 'is_active', ''),
                'created' => arrayGet($events_list, 'created', ''),
                'modified' => arrayGet($events_list, 'modified', ''),
                'school_name' => arrayGet($events_list, 'school_name', ''),
                'kouza_name' => arrayGet($events_list, 'kouza_name', ''),
                'event_type_name' => $event_type_name ?? ''
            );
            if (('t' == $contents['is_active']) || ('true' == $contents['is_active'])) {
                $contents['is_active'] = '○';
            } else {
                $contents['is_active'] = '×';
            }
            if ($events_list) {
                $contents['event_date'] = $this->checkAndFormatDate($contents['event_date']);
                $contents['created'] = $this->checkAndFormatDate($contents['created']);
                $contents['modified'] = $this->checkAndFormatDate($contents['modified']);
            }
            $this->request->getSession()->delete('events_edit_finish_mode');
            $this->set('contents', $contents);
            $this->set('title_head', __d('events', 'TITLE_DETAIL'));
            $this->render('events_detail');
        } catch (Exception $e) {
            $this->set('title_head', __d('events', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    public function eventsChangeRecords()
    {
        try {
            $ids = $this->request->getData('ids');
            if (empty($ids)) {
                $events_list = [];
            } else {
                $events_list = $this->eventRepository->selectById($ids)->toArray();
            }
            $table_body = $this->makeTableTrEventsList($events_list, false);
            $this->set('ids', $ids);
            $this->set('table_body', $table_body);
            if ($this->request->getData('visible')) {
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_CHANGE_RECORDS_VISIBLE'));
                $this->render('events_change_records_visible_confirm');
            } else if ($this->request->getData('invisible')) {
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_CHANGE_RECORDS_INVISIBLE'));
                $this->render('events_change_records_invisible_confirm');
            } else if ($this->request->getData('delete')) {
                $this->set('title_head', __d('events', 'TITLE_HEAD_EVENTS_CHANGE_RECORDS_DELETE'));
                $this->render('events_change_records_delete_confirm');
            } else {
                $this->set('title_head', __d('events', 'TITLE_HEAD_ERROR'));
                $this->render('error');
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('events', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    //------------------------------------------------
    /// @brief  Download Csv
    /// @author ChanNL
    //------------------------------------------------
    public function eventsDownloadCsv()
    {
        $school_id = $this->request->getSession()->read('event_school_id') ?? null;
        $kouza_id = $this->request->getSession()->read('event_kouza_id') ?? null;
        $event_type = $this->request->getSession()->read('event_event_type') ?? null;
        $is_active = $this->request->getSession()->read('event_is_active') ?? null;
        if (!is_null($is_active)) {
            switch ($is_active) {
                case 1:
                    $is_active = true;
                    break;
                case 2:
                    $is_active = false;
                    break;
                case 0:
                default:
                    $is_active = null;
            }
        }
        $order_by = $this->request->getSession()->read('event_order_by') ?? ADMIN_EVENTS_LIST_DEFAULT_ORDER;
        switch ($order_by) {
            case 0:
                $is_desc = false;
                $order = 'events_date';
                break;
            case 1:
                $is_desc = true;
                $order = 'events_date';
                break;
            case 2:
                $is_desc = false;
                $order = 'id';
                break;
            case 3:
                $is_desc = true;
                $order = 'id';
                break;
            case 4:
                $is_desc = false;
                $order = 'school';
                break;
            case 5:
                $is_desc = true;
                $order = 'school';
                break;
            case 6:
                $is_desc = false;
                $order = 'modified';
                break;
            case 7:
                $is_desc = true;
                $order = 'modified';
                break;
        }

        $event_date_from = $this->request->getSession()->read('event_event_date_from') ?? null;
        $event_date_to = $this->request->getSession()->read('event_event_date_to') ?? null;

        $args = array(
            'school_id' => (0 == $school_id) ? null : $school_id,
            'kouza_id' => (0 == $kouza_id) ? null : $kouza_id,
            'event_type' => (0 == $event_type) ? null : $event_type,
            'is_active' => $is_active,
            'event_date_from' => (0 == $event_date_from) ? null : $this->getFirstDayOfMonth($event_date_from),
            'event_date_to' => (0 == $event_date_to) ? null : $this->getLastDayOfMonth($event_date_to),
            'order' => $order,
            'is_desc' => $is_desc
        );

        $records = $this->eventRepository->getDataCountBySchoolAndKouzaAndEventTypeAndEventDate($args);
        if (false === $records) {
            throw new InternalErrorException();
        }
        $num_records = $records->count();
        $fname = 'events-' . date('YmdHi') . '.csv';

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename={$fname}");

        $num_blocks = ceil($num_records / CSV_DIVIDE);

        $args['offset'] = 0;
        $args['limit'] = CSV_DIVIDE;
        for ($i = 0; $i < $num_blocks; $i++) {
            $args['offset'] = $i * CSV_DIVIDE;
            foreach ($records as $elm) {
                $is_active = $this->arrayGet($elm, 'is_active', '');
                if (('t' == $is_active) || ('true' == $is_active)) {
                    $is_active = 1;
                } else {
                    $is_active = 0;
                }
                $event_date = $this->arrayGet($elm, 'event_date', '');
                $event_day = $event_date->format('Y-m-d');
                $event_time = $event_date->format('H:i:s');

                $str = '"' . $this->arrayGet($elm, 'school_id', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'kouza_id', '') . '",'
                    .  '"' . $event_day . '",'
                    .  '"' . $event_time . '",'
                    .  '"' . $this->arrayGet($elm, 'event_type', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'event_title', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'event_body', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'order_no', '') . '",'
                    .  '"' . $is_active . '",'
                    .  '"' . $this->arrayGet($elm, 'created', '')->format('Y-m-d H:i:s') . '",'
                    .  '"' . $this->arrayGet($elm, 'modified', '')->format('Y-m-d H:i:s') . '"'
                    . "\n";

                echo $this->toSJIS($str, EFP_DB_ENCODING);
            }
        }
        exit(0);
    }

    //------------------------------------------------
    /// @brief  Get events list
    /// @author ChanNL
    //------------------------------------------------
    public function getEventsList()
    {
        $school_id = $this->request->getSession()->read('event_school_id') ?? null;
        $kouza_id = $this->request->getSession()->read('event_kouza_id') ?? null;
        $event_type = $this->request->getSession()->read('event_event_type') ?? null;
        $is_active = $this->request->getSession()->read('event_is_active') ?? null;
        if (!is_null($is_active)) {
            switch ($is_active) {
                case 1:
                    $is_active = true;
                    break;
                case 2:
                    $is_active = false;
                    break;
                case 0:
                default:
                    $is_active = null;
            }
        }

        $order_by = $this->request->getSession()->read('event_order_by') ?? ADMIN_EVENTS_LIST_DEFAULT_ORDER;
        switch ($order_by) {
            case 0:
                $is_desc = false;
                $order = 'event_date';
                break;
            case 1:
                $is_desc = true;
                $order = 'event_date';
                break;
            case 2:
                $is_desc = false;
                $order = 'id';
                break;
            case 3:
                $is_desc = true;
                $order = 'id';
                break;
            case 4:
                $is_desc = false;
                $order = 'school';
                break;
            case 5:
                $is_desc = true;
                $order = 'school';
                break;
            case 6:
                $is_desc = false;
                $order = 'modified';
                break;
            case 7:
                $is_desc = true;
                $order = 'modified';
                break;
        }

        $event_date_from = $this->request->getSession()->read('event_event_date_from') ?? null;
        $event_date_to = $this->request->getSession()->read('event_event_date_to') ?? null;

        $args = array(
            'school_id' => (0 == $school_id) ? null : $school_id,
            'kouza_id' => (0 == $kouza_id) ? null : $kouza_id,
            'event_type' => (0 == $event_type) ? null : $event_type,
            'is_active' => $is_active,
            'event_date_from' => (0 == $event_date_from) ? null : $this->getFirstDayOfMonth($event_date_from),
            'event_date_to' => (0 == $event_date_to) ? null : $this->getLastDayOfMonth($event_date_to),
            'order' => $order,
            'is_desc' => $is_desc
        );

        $limit = PER_PAGE; // Number of data displayed on one page
        $records = $this->eventRepository->getDataCountBySchoolAndKouzaAndEventTypeAndEventDate($args);
        if (false === $records) {
            throw new InternalErrorException();
        }
        $num_records = $records->count();
        // Pagination
        $lastPage = (int) ceil($num_records / $limit);
        $page = $this->request->getQuery('page', 1);
        // Calculate page number
        if ($lastPage < $page) {
            $this->request = $this->request->withQueryParams(['page' => $lastPage]);
        }
        return [
            'records' => $this->paginate($records, ['limit' => $limit]),
            'num_records' => $num_records
        ];
    }

    //------------------------------------------------
    /// @brief Generate controls to place on the form
    /// @param none
    /// @return an array containing the controls
    /// @author ChanNL
    //------------------------------------------------
    public function _genFormIndexControls()
    {
        $controls = array();
        // school list
        $this->setSessionParameter();
        $controls['school_id'] = $this->request->getSession()->read('event_school_id') ?? 0;

        // kouza list
        $controls['kouza_id'] = $this->request->getSession()->read('event_kouza_id') ?? 0;

        // kouza list
        $controls['event_type'] = $this->request->getSession()->read('event_event_type') ?? 0;

        // display state list
        $controls['is_active_list'] = array(
            0 => __d('events', 'IS_ACTIVE_DISPLAY_STATE'),
            1 => __d('events', 'IS_ACTIVE_DISPLAY'),
            2 => __d('events', 'IS_ACTIVE_HIDDEN')
        );
        $controls['is_active'] = $this->request->getSession()->read('event_is_active') ?? 0;

        // Publication start month list
        $ym = date('Y-m', strtotime(date('Y-m-1') . ' -6 month')); // n months ago
        $ym_list = $this->genYearMonthList($ym, 10); // Display for m months
        $controls['ym_list_from'] = array(0 => __d('events', 'TITLE_DATE_AND_TIME_START')) + $ym_list;
        $controls['event_date_from'] = $this->request->getSession()->read('event_event_date_from') ?? 0;

        // Publication end month list
        $controls['ym_list_to'] = array(0 => __d('events', 'TITLE_DATE_AND_TIME_END')) + $ym_list;
        $controls['event_date_to'] = $this->request->getSession()->read('event_event_date_to') ?? 0;

        // sorted list
        $order_by_list = array(
            0 => __d('events', 'SORT_BY_DATE_ASC'),
            1 => __d('events', 'SORT_BY_DATE_DESC'),
            2 => __d('events', 'SORT_BY_ID_ASC'),
            3 => __d('events', 'SORT_BY_ID_DESC'),
            4 => __d('events', 'SORT_BY_SCHOOL_ORDER_ASC'),
            5 => __d('events', 'SORT_BY_SCHOOL_ORDER_DESC'),
            6 => __d('events', 'SORT_BY_MODIFIED_ASC'),
            7 => __d('events', 'SORT_BY_MODIFIED_DESC')
        );
        $controls['order_by_list'] = $order_by_list;
        $controls['order_by'] = $this->request->getSession()->read('event_order_by') ?? ADMIN_EVENTS_LIST_DEFAULT_ORDER;

        return $controls;
    }

    //------------------------------------------------
    /// @brief  Set session for form data
    /// @author ChanNL
    //------------------------------------------------
    public function setSessionParameter()
    {
        $datas = $this->request->getData();
        foreach ($datas as $key => $value) {
            $this->request->getSession()->write('event_' . $key, $value);
        }
    }

    //------------------------------------------------
    /// @brief  Generate tr row(s) of table for What's Event list
    /// @param  $records        Data array for table output
    /// @param  $with_checkbox  whether to add a checkbox
    /// @param  $checkbox_name  The name to set in the name field of the checkbox (default is 'ids[]')
    /// @return tr tag
    /// @author ChanNL
    //------------------------------------------------
    function makeTableTrEventsList($records, $with_checkbox = false, $checkbox_name = 'ids[]')
    {
        $event_type_list = $this->m_types_info;
        $request_uri = $_SERVER['REQUEST_URI'];
        $pos = strpos($request_uri, 'events_list');
        if ($pos !== false) {
            $request_uri = '/events_list';
        }
        $document_root_path = str_replace('/events_edit_finish', '', $request_uri);
        $document_root_path = str_replace('/events_add_finish', '', $document_root_path);
        $document_root_path = str_replace('/events_change_records', '', $document_root_path);
        $cnt = count($records);
        $tag = '';
        for ($i = 0; $i < $cnt; $i++) {
            // Sort order
            // チェックボックス、id、校舎、講座、イベント種別、日時、タイトル、本文、並び補正、表示、登録日、作成日、更新日
            $r = $records[$i];

            $id = $this->cleanTags($r['id']);
            $id_field = '<a href="' . $document_root_path . '/events_detail?id=' . $id . '">' . $id . '</a>';
            if (('t' == $r['is_active']) || ('true' == $r['is_active'])) {
                $is_active = '○';
                $visible_class = 'class_active';
            } else {
                $is_active = '×';
                $visible_class = 'class_inactive';
            }
            $event_type = $r['event_type'];
            $event_type_name = $event_type_list[$event_type];
            $event_date = $r['event_date']->format('Y-m-d H:i');
            $created = $r['created']->format('Y-m-d H:i');
            $modified = $r['modified']->format('Y-m-d H:i');

            // It seems that mb_strimwidth is specified in bytes instead of characters
            $event_title = mb_strimwidth($r['event_title'], 0, ADMIN_EVENTS_TITLE_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $event_body = mb_strimwidth($r['event_body'], 0, ADMIN_EVENTS_BODY_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $tr_class = ($i % 2) ? 'even' : 'odd';

            $chekcbox_line = '';
            if ($with_checkbox) {
                $chekcbox_line = "<td><input type=\"checkbox\" name=\"{$checkbox_name}\" value=\"{$id}\"/></td>";
            }

            $tag .= "<tr class=\"{$tr_class} {$visible_class}\">"
                .  $chekcbox_line
                .  "<td>{$id_field}</td>"
                .  '<td>' . $this->cleanTags($r['school_name']) . '</td>'
                .  '<td>' . $r['kouza_name'] . '</td>'      // 講座名には実体参照(&reg;)を含めるのでタグ除去しない(2012-07-20 15:13:13)
                .  '<td>' . $this->cleanTags($event_type_name) . '</td>'
                .  '<td>' . $this->cleanTags($event_date) . '</td>'
                .  '<td title="' . $this->cleanTags($r['event_title']) . '">' . $this->cleanTags($event_title) . '</td>'
                .  '<td title="' . $this->cleanTags($r['event_body']) . '">' . $this->cleanTags($event_body) . '</td>'
                .  '<td>' . $this->cleanTags($r['order_no']) . '</td>'
                .  '<td>' . $this->cleanTags($is_active) . '</td>'
                .  '<td>' . $created . '</td>'
                .  '<td>' . $modified . '</td>'
                .  '</tr>';
        }

        return $tag;
    }

    function cleanTags($data, $encoding = null, $rlevel = 5)
    {
        if ($rlevel < 0) {
            return $data;
        }

        $encoding;
        if (!is_array($data)) {
            if (!is_string($data)) {
                return $data;
            }
            return htmlspecialchars($data, ENT_QUOTES);
        } else {
            $ary_tmp = array();
            foreach ($data as $key => $val) {
                $ary_tmp[$key] = $this->cleanTags($val, $encoding, $rlevel - 1);
            }
            return $ary_tmp;
        }
    }

    //------------------------------------------------
    /// @brief  Init form data
    /// @author ChanNL
    //------------------------------------------------
    public function initFormData()
    {
        $events['school'] = 0;
        $events['kouza'] = 0;
        $events['event_type'] = 0;
        $events['event_ym'] = 0;
        $events['event_day'] = 0;
        $events['event_time_h'] = -1;
        $events['event_time_m'] = -1;
        $events['text_title'] = '';
        $events['text_body'] = '';
        $events['radio_is_active'] = 1;
        return $events;
    }

    //------------------------------------------------
    /// @brief  Initialize the form control data from the data in the DB obtained based on the ID
    /// @param  id      record ID
    /// @retval !false  success
    /// @retval false   Failure
    /// @note   Use this data and FormHelper to set the initial value of the edit screen.
    /// @author ChanNL
    //------------------------------------------------
    public function initFormDataByRecordId($vo)
    {
        $school_id = $vo['school_id'];
        $kouza_id = $vo['kouza_id'];
        $event_title = $vo['event_title'];
        $event_body = $vo['event_body'];
        $event_date = $this->checkAndFormatDate($vo['event_date']);
        $event_type = $vo['event_type'];
        $order_no = $vo['order_no'];
        $is_active = ('t' == $vo['is_active']) ? 1 : 0;
        // $event_dateを分解
        $ary = date_parse($event_date);
        $event_ym = sprintf('%04d-%02d', $ary['year'], $ary['month']);
        $event_day = $ary['day'];
        $event_time_h = $ary['hour'];
        $event_time_m = $ary['minute'];

        $events['school'] = $school_id;
        $events['kouza'] = $kouza_id;
        $events['event_type'] = $event_type;
        $events['event_ym'] = $event_ym;
        $events['event_day'] = $event_day;
        $events['event_time_h'] = $event_time_h;
        $events['event_time_m'] = $event_time_m;
        $events['text_title'] = $event_title;
        $events['text_body'] = $event_body;
        $events['text_order_no'] = $order_no;
        $events['radio_is_active'] = $is_active;
        $events['event_date'] = $event_date;
        $events['ids'] = array();
        return $events;
    }

    //------------------------------------------------
    /// @brief  Generate controls to place on the form
    /// @param  none
    /// @return an array containing the controls
    /// @author ChanNL
    //------------------------------------------------
    function _genFormEditControls()
    {
        $ym = date('Y-m', strtotime(date('Y-m-1') . ' -1 month'));             // n months ago
        $ym_list = $this->genYearMonthList($ym, 24);                            // Display for m months
        $ym_list = array(0 => '年-月') + $ym_list;
        $controls['event_ym'] = $ym_list;

        $day_list = $this->genDayList(1, 31, false);
        $day_list = array(0 => '日') + $day_list;
        $controls['event_day'] = $day_list;

        $hour_list = $this->genHourList(6, 21, 1, false);
        $hour_list = array(-1 => '時') + $hour_list;
        $controls['event_time_h'] = $hour_list;

        $minut_list = $this->genMinutList(0, 60, 5, false);
        $minut_list = array(-1 => '分') + $minut_list;
        $controls['event_time_m'] = $minut_list;

        // Display state selection radio button
        $options = array(0 => '非表示', 1 => '表示');
        $controls['radio_is_active'] = $options;

        return $controls;
    }
}
