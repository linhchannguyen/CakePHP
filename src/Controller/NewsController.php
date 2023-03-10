<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\Files\FilesRepository;
use App\Repositories\News\NewsRepository;
use App\Repositories\Schools\SchoolRepository;
use App\Traits\dateMiscTrait;
use App\Traits\lib_utilityTrait;
use Cake\Core\Configure;
use Cake\Http\Exception\InternalErrorException;
use Exception;

/**
 * News Controller
 *
 * @property \App\Model\Table\NewsTable $News
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NewsController extends AppController
{
    use dateMiscTrait, lib_utilityTrait;
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('custom');
        $this->loadModel('News');
        $this->loadModel('Schools');
        $this->loadModel('Files');
        $this->newsRepository = new NewsRepository($this->News);
        $this->schoolRepository = new SchoolRepository($this->Schools);
        $this->filesRepository = new FilesRepository($this->Files);
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
                $this->request->getSession()->delete('news_add_finish_mode');
            }
            $school_list = $this->schoolRepository->getSchoolInfoForSelectbox();
            $school_list = array(0 => '校舎') + $school_list;
            if (!$mode) {
                // form control generation
                $controls = $this->_genFormIndexControls();
                // school list
                $data = $this->getNewsList();
                $this->set('school_list', $school_list);
                $this->set('controls', $controls);
                $this->set('num_records', $data['num_records']);
                $this->set('records', $data['records']);
                $table_body = $this->makeTableTrNewsList($data['records']->toArray(), true, 'ids[]');
                $this->set('table_body', $table_body);
                $this->set('title_head', __d('news', 'TITLE_HEAD'));
                $this->render('index');
            } else if ($mode == 'news_edit') {
                $id = $this->request->getData('id') ?? null;
                $this->request->getSession()->write('is_reload_news_edit', false);
                $getSessionNewsEditFinish = $this->request->getSession()->read('news_edit_finish_mode') ?? null;
                if ($getSessionNewsEditFinish) {
                    $id = $getSessionNewsEditFinish['id'];
                    $news_form = $getSessionNewsEditFinish['news_form'];
                    if ($getSessionNewsEditFinish['edited']) {
                        $news = $this->newsRepository->selectById($id)->first();
                        $news_form['text_link_url'] = $news['news_url'];
                    }
                    $this->set('errors', $getSessionNewsEditFinish['errors']);
                    $this->set('error_message', $getSessionNewsEditFinish['error_message']);
                } else {
                    $news = $this->newsRepository->selectById($id)->first() ?? [];
                    if (!$news) {
                        $news_form = $this->initFormData();
                    }else{
                        $news_form = $this->initFormDataByRecordId($news);
                    }
                }
                $news_form['radio_link'] = 0;
                $controls = $this->_genFormEditControls();
                $this->set('controls', $controls);
                $this->set('news_form', $news_form);
                $this->set('id', $id);
                $this->set('school_list', $school_list);
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_EDIT'));
                $this->render('news_edit');
            } else if ($mode == 'news_add') {
                $this->request->getSession()->write('is_reload_news_add', false);
                $getSessionNewsAddFinish = $this->request->getSession()->read('news_add_finish_mode') ?? null;
                if ($getSessionNewsAddFinish) {
                    if ($getSessionNewsAddFinish['added']) {
                        $news_form = $this->initFormData();
                    } else {
                        $news_form = $getSessionNewsAddFinish['news_form'];
                        $this->set('errors', $getSessionNewsAddFinish['errors']);
                        $this->set('error_message', $getSessionNewsAddFinish['error_message']);
                    }
                } else {
                    $news_form = $this->initFormData();
                }
                $controls = $this->_genFormEditControls();
                $this->set('controls', $controls);
                $this->set('news_form', $news_form);
                $this->set('school_list', $school_list);
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_ADD'));
                $this->render('news_add');
            } else if ($mode == 'news_delete_confirm') {
                $id = $this->request->getData('id');
                $records = $this->newsRepository->selectById($id)->first();
                if (!$records) {
                    throw new Exception('');
                }
                $table_body = $this->makeTableTrNewsList(array($records), false);
                $this->set('table_body', $table_body);
                $this->set('id', $id);
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_DELETE_CONFIRM'));
                $this->render('news_delete_confirm');
            } else if ($mode == 'news_delete_finish') {
                $id = $this->request->getData('id');
                $this->deleteDBById($id);
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_DELETED'));
                $this->render('news_delete_finish');
            } else if ($mode == 'news_change_records_visible_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->updateDBByIdWithParam($ids, true);
                if (!$result) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_VISIBLE_FINISH'));
                $this->render('news_change_records_visible_finish');
            } else if ($mode == 'news_change_records_invisible_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->updateDBByIdWithParam($ids, false);
                if (!$result) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_INVISIBLE_FINISH'));
                $this->render('news_change_records_invisible_finish');
            } else if ($mode == 'news_change_records_delete_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->deleteDBById($ids);
                if ($result === 500) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_DELETE_FINISH'));
                $this->render('news_change_records_delete_finish');
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('news', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    //------------------------------------------------
    /// @brief  Create news
    /// @author ChanNL
    //------------------------------------------------
    public function newsAddFinish()
    {
        try {
            $newsAddFinish['News'] = $this->request->getData();
            // validation
            $defaultValidator = $this->News->getValidator('custom');
            if (!$newsAddFinish['News']['radio_link']) {
                $defaultValidator->add('text_link_url', 'valid-url', ['rule' => 'url', 'message' => __d('validation', 'NEWS_FORMAT_URL_INVALID')]);
            }
            $validate = $defaultValidator->validate($newsAddFinish['News']);
            if (!$validate) { // empty
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_ADD_FINISH'));
                $param = $newsAddFinish['News'];
                $news_add_finish_mode = [
                    'news_form' => $newsAddFinish['News'],
                    'errors' => [],
                    'error_message' => ''
                ];
                $upload_fail = false;
                if ($newsAddFinish['News']['radio_link']) {
                    $file = $this->request->getData('file_pdf');
                    if (empty($file->getClientFilename())) {
                        $upload_fail = true;
                    }
                    $result = $this->_receiveFile();
                    if (!$result) {
                        $upload_fail = true;
                    }
                    $news_url = $result;
                    $param['news_url'] = $news_url;
                }
                if ($upload_fail) {
                    $news_add_finish_mode['added'] = false;
                    $this->set('errors', __d('validation', 'NEWS_FILE_UPLOAD_FAIL'));
                    $this->set('title_head', __d('news', 'TITLE_HEAD_ERROR'));
                } else {
                    $inserted_record = [];
                    //Exit without doing anything when reloading
                    if (!$this->request->getSession()->read('is_reload_news_add')) {
                        $id = $this->insertDB($param);
                        if (!$id) {
                            throw new Exception('');
                        }
                        $inserted_record = $this->newsRepository->selectById($id)->first();
                    }
                    $table_body = '';
                    if ($inserted_record) {
                        $table_body = $this->makeTableTrNewsList(array($inserted_record));
                    }
                    $news_add_finish_mode['added'] = true;
                    $this->set('table_body', $table_body);
                }
                $this->request->getSession()->write('news_add_finish_mode', $news_add_finish_mode);
                $this->request->getSession()->write('is_reload_news_add', true);
                $this->render('news_add_finish');
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
                if (!empty($errors['news_date'])) {
                    if (!empty($errors['title_ym'])) {
                        unset($errors['news_date']);
                    } else if (!empty($errors['title_day'])) {
                        unset($errors['news_date']);
                    }
                }
                $num_total_error = count($errors);
                $error_message = '';
                if (0 < $num_total_error) {
                    $error_message = "<p class=\"warn\">{$num_total_error}個のエラーがあります。</p>";
                }
                $this->request->getSession()->write('news_add_finish_mode', [
                    'news_form' => $newsAddFinish['News'],
                    'errors' => $errors,
                    'error_message' => $error_message,
                    'added' => false
                ]);
                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'f' => 'news_add',
                        'recovery' => 'true'
                    ],
                ]);
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('news', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    //------------------------------------------------
    /// @brief  Update news
    /// @author ChanNL
    //------------------------------------------------
    public function newsEditFinish()
    {
        try {
            $newsEditFinish['News'] = $this->request->getData();
            $id = $this->request->getData('id') ?? null;
            // validation
            $defaultValidator = $this->News->getValidator('custom');
            if (!$newsEditFinish['News']['radio_link']) {
                $defaultValidator->add('text_link_url', 'valid-url', ['rule' => 'url', 'message' => __d('validation', 'NEWS_FORMAT_URL_INVALID')]);
            }
            $validate = $defaultValidator->validate($newsEditFinish['News']);
            if (!$validate) { // empty
                $this->set('id', $id);
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_EDIT_FINISH'));
                $param = $newsEditFinish['News'];
                $news_edit_finish_mode = [
                    'id' => $id,
                    'news_form' => $newsEditFinish['News'],
                    'errors' => [],
                    'error_message' => '',
                    'edited' => false
                ];
                $upload_fail = false;
                $table_body = '';
                //Exit without doing anything when reloading
                if (!$this->request->getSession()->read('is_reload_news_edit')) {
                    if ($newsEditFinish['News']['radio_link']) {
                        $file = $this->request->getData('file_pdf');
                        if (empty($file->getClientFilename())) {
                            $upload_fail = true;
                        }
                        $result = $this->_receiveFile();
                        if (!$result) {
                            $upload_fail = true;
                        }
                        $news_url = $result;
                        $text_link_url = $news_url;
                        $param['news_url'] = $news_url;
                        $param['text_link_url'] = $text_link_url;
                    }
                    if ($upload_fail) {
                        $this->set('errors', __d('validation', 'NEWS_FILE_UPLOAD_FAIL'));
                        $this->set('title_head', __d('news', 'TITLE_HEAD_ERROR'));
                    } else {
                        $inserted_record = [];
                        $update = $this->updateDBById($param);
                        if (!$update) {
                            throw new Exception('');
                        }
                        $inserted_record = $this->newsRepository->selectById($id)->first();
                        if (!$inserted_record) {
                            throw new Exception('');
                        }
                        if ($inserted_record) {
                            $table_body = $this->makeTableTrNewsList(array($inserted_record));
                        }
                        $news_edit_finish_mode['edited'] = true;
                    }
                }
                $this->set('table_body', $table_body);
                $newsEditFinish['News']['radio_link'] = 0;
                $this->request->getSession()->write('news_edit_finish_mode', $news_edit_finish_mode);
                $this->request->getSession()->write('is_reload_news_edit', true);
                $this->render('news_edit_finish');
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
                if (!empty($errors['news_date'])) {
                    if (!empty($errors['title_ym'])) {
                        unset($errors['news_date']);
                    } else if (!empty($errors['title_day'])) {
                        unset($errors['news_date']);
                    }
                }
                $num_total_error = count($errors);
                $error_message = '';
                if (0 < $num_total_error) {
                    $error_message = "<p class=\"warn\">{$num_total_error}個のエラーがあります。</p>";
                }
                $newsEditFinish['News']['radio_link'] = 0;
                $this->request->getSession()->write('news_edit_finish_mode', [
                    'id' => $id,
                    'news_form' => $newsEditFinish['News'],
                    'errors' => $errors,
                    'error_message' => $error_message,
                    'edited' => false
                ]);
                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'f' => 'news_edit',
                        'recovery' => 'true'
                    ],
                ]);
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('news', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
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
        // Branch the process depending on whether the PDF has been uploaded
        if ($fd['radio_link']) {
            $url = $fd['news_url'];
        } else {
            $url = $fd['text_link_url'];
        }

        // date
        // Invalid date (when input is omitted) is null and let DAO set the default value.
        $news_date = $fd['title_ym'] . '-' . $fd['title_day'];
        @list($y, $m, $d) = explode('-', $news_date);
        if (!checkdate(intval($m), intval($d), intval($y))) {
            $news_date = null;
        }
        $fd['from_day'] = $fd['from_day'] < 10 ? '0' . $fd['from_day'] : $fd['from_day'];
        $fd['to_day'] = $fd['to_day'] < 10 ? '0' . $fd['to_day'] : $fd['to_day'];
        $enable_from = $fd['from_ym'] . '-' . $fd['from_day'] . ' ' . $fd['from_time'] . ':00';   // Add seconds field

        if (false === $this->isDateTime($enable_from)) {
            $enable_from = '-infinity';
        }

        $enable_to = $fd['to_ym'] . '-' . $fd['to_day'] . ' ' . $fd['to_time'] . ':00';           // Add seconds field
        if (false === $this->isDateTime($enable_to)) {
            $enable_to = 'infinity';
        }

        $params = array(
            'school_id' => $fd['school'],
            'news_title' => $fd['text_title'],
            'news_title_sub' => $fd['text_title_sub'],
            'news_date' => $news_date,
            'news_url' => $url,
            'enabled_from' => $enable_from,
            'enabled_to' => $enable_to,
            'order_no' => 0,
            'is_active' => 1,
            'urgency' => $fd['urgency'],
        );
        return $this->newsRepository->insert($params);
    }

    public function updateDBByIdWithParam($ids, $is_active)
    {
        $params = array(
            'is_active' => $is_active
        );
        return $this->newsRepository->updateById($ids, $params);
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
        // Branch the process depending on whether the PDF has been uploaded
        if ($fd['radio_link']) {
            $url = $fd['news_url'];
        } else {
            $url = $fd['text_link_url'];
        }

        // date
        // Invalid date (when input is omitted) is null and let DAO set the default value.
        $news_date = $fd['title_ym'] . '-' . $fd['title_day'];
        @list($y, $m, $d) = explode('-', $news_date);
        if (!checkdate(intval($m), intval($d), intval($y))) {
            $news_date = null;
        }
        $fd['from_day'] = $fd['from_day'] < 10 ? '0' . $fd['from_day'] : $fd['from_day'];
        $fd['to_day'] = $fd['to_day'] < 10 ? '0' . $fd['to_day'] : $fd['to_day'];
        $enable_from = $fd['from_ym'] . '-' . $fd['from_day'] . ' ' . $fd['from_time'] . ':00';   // Add seconds field

        if (false === $this->isDateTime($enable_from)) {
            $enable_from = '-infinity';
        }

        $enable_to = $fd['to_ym'] . '-' . $fd['to_day'] . ' ' . $fd['to_time'] . ':00';           // Add seconds field
        if (false === $this->isDateTime($enable_to)) {
            $enable_to = 'infinity';
        }

        $params = array(
            'school_id' => $fd['school'],
            'news_title' => $fd['text_title'],
            'news_title_sub' => $fd['text_title_sub'],
            'news_date' => $news_date,
            'news_url' => $url,
            'enabled_from' => $enable_from,
            'enabled_to' => $enable_to,
            'order_no' => $fd['text_order_no'],
            'is_active' => $fd['radio_is_active'] ? true : false,
            'urgency' => $fd['urgency']
        );
        return $this->newsRepository->updateById($fd['id'], $params);
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
        return $this->newsRepository->deleteById($id);
    }

    //------------------------------------------------
    /// @brief  Get info news
    /// @author ChanNL
    //------------------------------------------------
    public function newsDetail()
    {
        try {
            $id = null;
            if (!empty($this->request->getQuery('id'))) {
                $id = $this->request->getQuery('id');
            } else if (!empty($this->request->getData('id'))) {
                $id = $this->request->getData('id');
            }
            $news_list = $this->newsRepository->selectById($id)->first();
            $contents = array(
                'id' => $this->arrayGet($news_list, 'id', ''),
                'news_title' => $this->arrayGet($news_list, 'news_title', ''),
                'news_title_sub' => $this->lfToBr($this->arrayGet($news_list, 'news_title_sub', '')),
                'news_date' => $this->arrayGet($news_list, 'news_date', ''),
                'news_url' => $this->arrayGet($news_list, 'news_url', ''),
                'enabled_from' => $this->arrayGet($news_list, 'enabled_from', ''),
                'enabled_to' => $this->arrayGet($news_list, 'enabled_to', ''),
                'order_no' => $this->arrayGet($news_list, 'order_no', ''),
                'is_active' => $this->arrayGet($news_list, 'is_active', ''),
                'created' => $this->arrayGet($news_list, 'created', ''),
                'modified' => $this->arrayGet($news_list, 'modified', ''),
                'school_name' => $this->arrayGet($news_list, 'school_name', ''),
                'urgency' => $this->arrayGet($news_list, 'urgency', ''),
            );
            if (('t' == $contents['is_active']) || ('true' == $contents['is_active'])) {
                $contents['is_active'] = '○';
            } else {
                $contents['is_active'] = '×';
            }
            if ($news_list) {
                $contents['news_date'] = $this->checkAndFormatDate($contents['news_date'], 'Y-m-d');
                $contents['enabled_from'] = $this->checkAndFormatDate($contents['enabled_from']);
                $contents['enabled_to'] = $this->checkAndFormatDate($contents['enabled_to']);
                $contents['created'] = $this->checkAndFormatDate($contents['created']);
                $contents['modified'] = $this->checkAndFormatDate($contents['modified']);
                if (in_array($contents['enabled_from'], [INFINITY_DATE, DEV_INFINITY_DATE])) {
                    $contents['enabled_from'] = '-';
                }
                if (in_array($contents['enabled_to'], [INFINITY_DATE, DEV_INFINITY_DATE])) {
                    $contents['enabled_to'] = '-';
                }
            }
            $this->request->getSession()->delete('news_edit_finish_mode');

            $this->set('contents', $contents);
            $this->set('title_head', __d('news', 'TITLE_HEAD'));
            $this->render('news_detail');
        } catch (Exception $e) {
            $this->set('title_head', __d('news', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    public function newsChangeRecords()
    {
        try {
            $ids = $this->request->getData('ids');
            if (empty($ids)) {
                $news_list = [];
            } else {
                $news_list = $this->newsRepository->selectById($ids)->toArray();
            }
            $table_body = $this->makeTableTrNewsList($news_list, false);
            $this->set('ids', $ids);
            $this->set('table_body', $table_body);
            if ($this->request->getData('visible')) {
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_CHANGE_RECORDS_VISIBLE'));
                $this->render('news_change_records_visible_confirm');
            } else if ($this->request->getData('invisible')) {
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_CHANGE_RECORDS_INVISIBLE'));
                $this->render('news_change_records_invisible_confirm');
            } else if ($this->request->getData('delete')) {
                $this->set('title_head', __d('news', 'TITLE_HEAD_NEWS_CHANGE_RECORDS_DELETE'));
                $this->render('news_change_records_delete_confirm');
            } else {
                $this->set('title_head', __d('news', 'TITLE_HEAD_ERROR'));
                $this->render('error');
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('news', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    //------------------------------------------------
    /// @brief  Download Csv
    /// @author ChanNL
    //------------------------------------------------
    public function newsDownloadCsv()
    {
        $school_id = $this->request->getSession()->read('new_school_id') ?? null;
        $is_active = $this->request->getSession()->read('new_is_active') ?? null;
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
        $order_by = $this->request->getSession()->read('new_order_by') ?? ADMIN_NEWS_LIST_DEFAULT_ORDER;
        switch ($order_by) {
            case 0:
                $is_desc = false;
                $order = 'news_date';
                break;
            case 1:
                $is_desc = true;
                $order = 'news_date';
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

        $news_date_from = $this->request->getSession()->read('new_news_date_from') ?? null;
        $news_date_to = $this->request->getSession()->read('new_news_date_to') ?? null;

        $args = array(
            'school_id' => (0 == $school_id) ? null : $school_id,
            'is_active' => $is_active,
            'news_date_from' => (0 == $news_date_from) ? null : $this->getFirstDayOfMonth($news_date_from),
            'news_date_to' => (0 == $news_date_to) ? null : $this->getLastDayOfMonth($news_date_to),
            'order' => $order,
            'is_desc' => $is_desc,
            'offset' => null,
            'limit' => null
        );

        $records = $this->newsRepository->selectBySchoolAndTitleDate($args);
        if (false === $records) {
            throw new InternalErrorException();
        }
        $num_records = $records->count();
        $fname = 'news-' . date('YmdHi') . '.csv';

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename={$fname}");

        $num_blocks = ceil($num_records / CSV_DIVIDE);

        $args['offset'] = 0;
        $args['limit'] = CSV_DIVIDE;

        for ($i = 0; $i < $num_blocks; $i++) {
            $args['offset'] = $i * CSV_DIVIDE;
            foreach ($records as $elm) {
                $enabled_from = $this->arrayGet($elm, 'enabled_from', '');
                if (in_array($enabled_from->format('Y-m-d H:i:s'), [INFINITY_DATE, DEV_INFINITY_DATE])) {
                    $enabled_from = '';
                } else {
                    $enabled_from = $enabled_from->format('Y-m-d H:i:s');
                }
                $enabled_to = $this->arrayGet($elm, 'enabled_to', '');
                if (in_array($enabled_to->format('Y-m-d H:i:s'), [INFINITY_DATE, DEV_INFINITY_DATE])) {
                    $enabled_to = '';
                } else {
                    $enabled_to = $enabled_to->format('Y-m-d H:i:s');
                }
                $is_active = $this->arrayGet($elm, 'is_active', '');
                if (('t' == $is_active) || ('true' == $is_active)) {
                    $is_active = 1;
                } else {
                    $is_active = 0;
                }
                $urgency = $this->arrayGet($elm, 'urgency', '');
                $urgency = ($urgency == __d('news', 'URGENCY_HIGH')) ? 1 : 0;

                $str = '"' . $this->arrayGet($elm, 'school_id', '') . '",'
                    .  '"' . $urgency . '",'
                    .  '"' . $this->arrayGet($elm, 'news_date', '')->format('Y-m-d') . '",'
                    .  '"' . $this->arrayGet($elm, 'news_title', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'news_title_sub', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'news_url', '') . '",'
                    .  '"' . $enabled_from . '",'
                    .  '"' . $enabled_to . '",'
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
    /// @brief  Get news list
    /// @author ChanNL
    //------------------------------------------------
    public function getNewsList()
    {
        $school_id = $this->request->getSession()->read('new_school_id') ?? null;
        $is_active = $this->request->getSession()->read('new_is_active') ?? null;
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
        $urgency = $this->request->getSession()->read('new_urgency') ?? null;
        if (!is_null($urgency)) {
            switch ($urgency) {
                case 1:
                    $urgency = '高';
                    break;
                case 2:
                    $urgency = '';
                    break;
                default:
                    $urgency = null;
            }
        }

        $order_by = $this->request->getSession()->read('new_order_by') ?? ADMIN_NEWS_LIST_DEFAULT_ORDER;
        switch ($order_by) {
            case 0:
                $is_desc = false;
                $order = 'news_date';
                break;
            case 1:
                $is_desc = true;
                $order = 'news_date';
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

        $news_date_from = $this->request->getSession()->read('new_news_date_from') ?? null;
        $news_date_to = $this->request->getSession()->read('new_news_date_to') ?? null;

        $args = array(
            'school_id' => (0 == $school_id) ? null : $school_id,
            'is_active' => $is_active,
            'news_date_from' => (0 == $news_date_from) ? null : $this->getFirstDayOfMonth($news_date_from),
            'news_date_to' => (0 == $news_date_to) ? null : $this->getLastDayOfMonth($news_date_to),
            'order' => $order,
            'is_desc' => $is_desc,
            'offset' => null,
            'limit' => null,
            'urgency' => $urgency,
        );

        $limit = PER_PAGE; // Number of data displayed on one page
        $records = $this->newsRepository->selectBySchoolAndTitleDate($args);
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
        $controls['school_id'] = $this->request->getSession()->read('new_school_id') ?? 0;

        // Urgency
        $controls['urgency_list'] = array(
            0 => __d('news', 'URGENCY_URGENCY'),
            1 => __d('news', 'URGENCY_HIGH'),
            2 => __d('news', 'URGENCY_NONE')
        );
        $controls['urgency'] = $this->request->getSession()->read('new_urgency') ?? 0;

        // display state list
        $controls['is_active_list'] = array(
            0 => __d('news', 'IS_ACTIVE_DISPLAY_STATE'),
            1 => __d('news', 'IS_ACTIVE_DISPLAY'),
            2 => __d('news', 'IS_ACTIVE_HIDDEN')
        );
        $controls['is_active'] = $this->request->getSession()->read('new_is_active') ?? 0;

        // Publication start month list
        $ym = date('Y-m', strtotime(date('Y-m-1') . ' -6 month')); // n months ago
        $ym_list = $this->genYearMonthList($ym, 10); // Display for m months
        $controls['ym_list_from'] = array(0 => __d('news', 'TITLE_DATE_AND_TIME_START')) + $ym_list;
        $controls['news_date_from'] = $this->request->getSession()->read('new_news_date_from') ?? 0;

        // Publication end month list
        $controls['ym_list_to'] = array(0 => __d('news', 'TITLE_DATE_AND_TIME_END')) + $ym_list;
        $controls['news_date_to'] = $this->request->getSession()->read('new_news_date_to') ?? 0;

        // sorted list
        $order_by_list = array(
            0 => __d('news', 'SORT_BY_DATE_ASC'),
            1 => __d('news', 'SORT_BY_DATE_DESC'),
            2 => __d('news', 'SORT_BY_ID_ASC'),
            3 => __d('news', 'SORT_BY_ID_DESC'),
            4 => __d('news', 'SORT_BY_SCHOOL_ORDER_ASC'),
            5 => __d('news', 'SORT_BY_SCHOOL_ORDER_DESC'),
            6 => __d('news', 'SORT_BY_MODIFIED_ASC'),
            7 => __d('news', 'SORT_BY_MODIFIED_DESC')
        );
        $controls['order_by_list'] = $order_by_list;
        $controls['order_by'] = $this->request->getSession()->read('new_order_by') ?? ADMIN_NEWS_LIST_DEFAULT_ORDER;

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
            $this->request->getSession()->write('new_' . $key, $value);
        }
    }

    //------------------------------------------------
    /// @brief  Generate tr row(s) of table for What's New list
    /// @param  $records        Data array for table output
    /// @param  $with_checkbox  whether to add a checkbox
    /// @param  $checkbox_name  The name to set in the name field of the checkbox (default is 'ids[]')
    /// @return tr tag
    /// @author ChanNL
    //------------------------------------------------
    function makeTableTrNewsList($records, $with_checkbox = false, $checkbox_name = 'ids[]')
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $pos = strpos($request_uri, 'news_list');
        if ($pos !== false) {
            $request_uri = '/news_list';
        }
        $document_root_path = str_replace('/news_edit_finish', '', $request_uri);
        $document_root_path = str_replace('/news_add_finish', '', $document_root_path);
        $document_root_path = str_replace('/news_change_records', '', $document_root_path);
        $cnt = count($records);
        $tag = '';
        for ($i = 0; $i < $cnt; $i++) {
            // Sort order
            // id, 校舎、タイトル日付、タイトル、リンク、優先度、並び補正、表示、登録日、有効期間（始）、有効期間（終）、作成日、更新日、チェックボックス
            $r = $records[$i];

            $id = $this->cleanTags($r['id']);
            $id_field = '<a href="' . $document_root_path . '/news_detail?id=' . $id . '">' . $id . '</a>';
            if (('t' == $r['is_active']) || ('true' == $r['is_active'])) {
                $is_active = '○';
                $visible_class = 'class_active';
            } else {
                $is_active = '×';
                $visible_class = 'class_inactive';
            }

            if (in_array($r['enabled_from']->format('Y-m-d H:i:s'), [INFINITY_DATE, DEV_INFINITY_DATE])) {
                $enabled_from = '-';
            } else {
                $enabled_from = $r['enabled_from']->format('Y-m-d H:i');
            }
            if (in_array($r['enabled_to']->format('Y-m-d H:i:s'), [INFINITY_DATE, DEV_INFINITY_DATE])) {
                $enabled_to = '-';
            } else {
                $enabled_to = $r['enabled_to']->format('Y-m-d H:i');
            }
            $created = $r['created']->format('Y-m-d H:i');
            $modified = $r['modified']->format('Y-m-d H:i');

            // It seems that mb_strimwidth is specified in bytes instead of characters
            $news_title = mb_strimwidth($r['news_title'], 0, ADMIN_NEWS_TITLE_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $news_title_sub = mb_strimwidth($r['news_title_sub'], 0, ADMIN_NEWS_TITLE_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $news_url = mb_strimwidth($r['news_url'], 0, ADMIN_NEWS_LINK_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $tr_class = ($i % 2) ? 'even' : 'odd';
            $title_url_line = '';
            if (0 < strlen($r['news_url'])) {
                $url = $this->cleanTags($r['news_url']);
                $title_url_line = '<td title="' . $url . '"><a href="' . $url . '" target="_blank">' . $this->cleanTags($news_url) . '</a></td>';
            } else {
                $title_url_line = '<td title="' . $this->cleanTags($r['news_url']) . '">' . $this->cleanTags($news_url) . '</td>';
            }

            $chekcbox_line = '';
            if ($with_checkbox) {
                $chekcbox_line = "<td><input type=\"checkbox\" name=\"{$checkbox_name}\" value=\"{$id}\"/></td>";
            }

            $tag .= "<tr class=\"{$tr_class} {$visible_class}\">"
                .  $chekcbox_line
                .  "<td>{$id_field}</td>"
                .  '<td>' . $this->cleanTags($r['school_name']) . '</td>'
                .  '<td>' . $this->cleanTags($r['news_date']->format('Y-m-d')) . '</td>'
                .  '<td title="' . $this->cleanTags($r['news_title']) . '">' . $this->cleanTags($news_title) . '</td>'
                .  '<td title="' . $this->cleanTags($r['news_title_sub']) . '">' . $this->cleanTags($news_title_sub) . '</td>'
                . $title_url_line
                .  '<td>' . $this->cleanTags($r['urgency']) . '</td>'
                .  '<td>' . $this->cleanTags($r['order_no']) . '</td>'
                .  '<td>' . $this->cleanTags($is_active) . '</td>'
                .  '<td>' . $enabled_from . '</td>'
                .  '<td>' . $enabled_to . '</td>'
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
        $news['school'] = 0;
        $news['title_ym'] = 0;
        $news['title_day'] = 0;
        $news['from_ym'] = 0;
        $news['from_day'] = 0;
        $news['from_time'] = '_';
        $news['to_ym'] = 0;
        $news['to_day'] = 0;
        $news['to_time'] = '_';
        $news['text_title'] = '';
        $news['text_title_sub'] = '';
        $news['text_link_url'] = '';
        $news['radio_link'] = 0;
        $news['radio_is_active'] = 1;
        $news['urgency'] = '';
        return $news;
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
        $news_title = $vo['news_title'];
        $news_title_sub = $vo['news_title_sub'];
        $news_url = $vo['news_url'];
        $vo['news_date'] = $this->checkAndFormatDate($vo['news_date'], 'Y-m-d');
        $vo['enabled_from'] = $this->checkAndFormatDate($vo['enabled_from']);
        $vo['enabled_to'] = $this->checkAndFormatDate($vo['enabled_to']);
        $vo['created'] = $this->checkAndFormatDate($vo['created']);
        $vo['modified'] = $this->checkAndFormatDate($vo['modified']);
        $news_date = $vo['news_date'];
        if (in_array($vo['enabled_from'], [INFINITY_DATE, DEV_INFINITY_DATE])) {
            $enabled_from = null;
        } else {
            $enabled_from = $vo['enabled_from'];
        }
        if (in_array($vo['enabled_to'], [INFINITY_DATE, DEV_INFINITY_DATE])) {
            $enabled_to = null;
        } else {
            $enabled_to = $vo['enabled_to'];
        }
        $order_no = $vo['order_no'];
        $is_active = ('t' == $vo['is_active']) ? 1 : 0;
        $urgency = $vo['urgency'];

        $ymd = preg_split('/-| /', $news_date);
        $title_ym = $ymd[0] . '-' . $ymd[1];
        $title_day = $ymd[2];

        if (is_null($enabled_from)) {
            $from_ym = $from_day = 0;
            $from_time = '_';
        } else {
            $ymd = preg_split('/-| /', $enabled_from);
            $from_ym = $ymd[0] . '-' . $ymd[1];
            $from_day = $ymd[2];
            $from_time = substr($ymd[3], 0, strlen($ymd[3]) - 3);
        }

        if (is_null($enabled_to)) {
            $to_ym = $to_day = 0;
            $to_time = '_';
        } else {
            $ymd = preg_split('/-| /', $enabled_to);
            $to_ym = $ymd[0] . '-' . $ymd[1];
            $to_day = $ymd[2];
            $to_time = substr($ymd[3], 0, strlen($ymd[3]) - 3);
        }

        $news['school'] = $school_id;
        $news['title_ym'] = $title_ym;
        $news['title_day'] = $title_day;
        $news['from_ym'] = $from_ym;
        $news['from_day'] = $from_day;
        $news['from_time'] = $from_time;
        $news['to_ym'] = $to_ym;
        $news['to_day'] = $to_day;
        $news['to_time'] = $to_time;
        $news['text_title'] = $news_title;
        $news['text_title_sub'] = $news_title_sub;
        $news['text_link_url'] = $news_url;
        $news['radio_link'] = 0; // URL always defaults
        $news['text_order_no'] = $order_no;
        $news['radio_is_active'] = $is_active;
        $news['urgency'] = $urgency;
        return $news;
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
        $ym_list = $this->genYearMonthList($ym, 4);                            // Display for m months
        $ym_list = array(0 => '年-月') + $ym_list;

        $ym_enabled_to = date('Y-m', strtotime(date('Y-m-1')));                // n months ago
        $ym_list_enabled_to = $this->genYearMonthList($ym_enabled_to, 12);     // Display for m months
        $ym_list_enabled_to = array(0 => '年-月') + $ym_list_enabled_to;

        $day_list = $this->genDayList(1, 31, false);
        $day_list = array(0 => '日') + $day_list;
        // Urgency
        $controls['urgency_list'] = array(
            '高' => __d('news', 'URGENCY_HIGH'),
            '' => __d('news', 'URGENCY_NONE')
        );
        // title month list
        $controls['title_ym'] = $ym_list;

        // Publication start month list
        $controls['from_ym'] = $ym_list;

        // Publication end month list
        $controls['to_ym'] = $ym_list_enabled_to;

        // title month date list
        $controls['title_day'] = $day_list;

        // Release date list within the month
        $controls['from_day'] = $day_list;

        // Release date list within the month
        $controls['to_day'] = $day_list;

        // Publication start month and time list
        // Note that strings starting with numbers will be confused with numbers in the in_array used in formHelper.
        $time_list = $this->genTimeList('00:00:00', '24:00:00', 3600);
        $time_list = array('_' => '時刻') + $time_list;
        $controls['from_time'] = $time_list;

        // Publication end month and time list
        $controls['to_time'] = $time_list;

        // File selection control for PDF upload

        // Radio button for link type selection
        $options = array(0 => 'URL', 1 => 'PDF');
        $controls['radio_link'] = $options;

        // Display state selection radio button
        $options = array(0 => '非表示', 1 => '表示');
        $controls['radio_is_active'] = $options;

        return $controls;
    }

    //------------------------------------------------
    /// @brief  Receive PDF file
    /// @param  $context    A reference to the application context
    /// @retval !false      storage path
    /// @retval false       error
    /// @author ChanNL
    //------------------------------------------------
    function _receiveFile()
    {
        $tmp_name = $_FILES['file_pdf']['tmp_name'];
        $uploads_dir = Configure::read('App.pdfBaseUrl');
        $fname = $this->_genRandomName();
        $new_fname = $fname . '.pdf';
        $dest = "$uploads_dir/$new_fname";
        if (MAX_FILE_SIZE <= filesize($tmp_name)) {
            return false;
        }
        $result = move_uploaded_file($tmp_name, $dest);
        // Store information in DB
        if ($result) {
            $params = array(
                'file_path' => "../pdf/$new_fname",                             // File path name on server
                'file_size' => $_FILES['file_pdf']['size'],       // file size
                'file_comment' => $_FILES['file_pdf']['name'],    // comment
            );
            if (false === $this->filesRepository->insertDB($params)) {
                return false;
            }
            return "http://$_SERVER[HTTP_HOST]/tacmap/pdf/$new_fname";
        }
        return false;
    }

    //------------------------------------------------
    /// @brief  Generate a random filename string
    /// @param  $length     string length
    /// @return random string
    /// @author ChanNL
    //------------------------------------------------
    function _genRandomName($length = 12)
    {
        return $this->genRndString(1, 'aA') . $this->genRndString($length - 1, 'aA0');
    }

    //------------------------------------------------
    /// @brief  generate a random string
    /// @param  $length     string length
    /// @param  $chars      List of character types to use (enumerated strings)
    ///         - 'a'   lowercase alphabet
    ///         - 'A'   uppercase alphabet
    ///         - '0'   numbers
    /// @return random string
    /// @note   It is used for password mail transmission processing, etc.
    ///         「If you add a condition such as "It starts with an alphabetic character and continues with alphanumeric characters...",
    ///          Please take measures such as combining these functions.
    /// @note   http://jp2.php.net/function.mt_rand
    /// @author ChanNL
    //------------------------------------------------
    function genRndString($length = 8, $chars = 'aA0')
    {
        $pool = array();
        if (false !== strpos($chars, 'a')) {
            $pool = array_merge(range('a', 'z'), $pool);
        }
        if (false !== strpos($chars, 'A')) {
            $pool = array_merge(range('A', 'Z'), $pool);
        }
        if (false !== strpos($chars, '0')) {
            $pool = array_merge(range('0', '9'), $pool);
        }

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $str;
    }
}
