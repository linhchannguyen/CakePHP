<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\Files\FilesRepository;
use App\Repositories\Kouzas\KouzaRepository;
use App\Repositories\Recommends\RecommendRepository;
use App\Repositories\Schools\SchoolRepository;
use App\Traits\dateMiscTrait;
use App\Traits\lib_utilityTrait;
use Cake\Core\Configure;
use Cake\Http\Exception\InternalErrorException;
use Exception;

class RecommendsController extends AppController
{
    use dateMiscTrait, lib_utilityTrait;
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('custom');
        $this->loadModel('Recommends');
        $this->loadModel('Schools');
        $this->loadModel('Kouzas');
        $this->loadModel('Files');
        $this->recommendRepository = new RecommendRepository($this->Recommends);
        $this->schoolRepository = new SchoolRepository($this->Schools);
        $this->kouzaRepository = new KouzaRepository($this->Kouzas);
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
                $this->request->getSession()->delete('recommends_add_finish_mode');
            }
            // school list
            $school_list = $this->schoolRepository->getSchoolInfoForSelectbox();
            $school_list = array(0 => '校舎') + $school_list;
            // course list
            $kouza_list = $this->kouzaRepository->getKouzaInfoForSelectbox();
            $kouza_list = array(0 => '講座') + $kouza_list;
            if (!$mode) {
                // form control generation
                $controls = $this->_genFormIndexControls();
                // school list
                $data = $this->getRecommendsList();
                $this->set('school_list', $school_list);
                $this->set('kouza_list', $kouza_list);
                $this->set('controls', $controls);
                $this->set('num_records', $data['num_records']);
                $this->set('records', $data['records']);
                $table_body = $this->makeTableTrRecommendsList($data['records']->toArray(), true, 'ids[]');
                $this->set('table_body', $table_body);
                $this->set('title_head', __d('recommends', 'TITLE_HEAD'));
                $this->render('index');
            } else if ($mode == 'recommends_edit') {
                $id = $this->request->getData('id') ?? null;
                $this->request->getSession()->write('is_reload_recommends_edit', false);
                $getSessionRecommendsEditFinish = $this->request->getSession()->read('recommends_edit_finish_mode') ?? null;
                if ($getSessionRecommendsEditFinish) {
                    $id = $getSessionRecommendsEditFinish['id'];
                    $recommends_form = $getSessionRecommendsEditFinish['recommends_form'];
                    if ($getSessionRecommendsEditFinish['edited']) {
                        $recommends = $this->recommendRepository->selectById($id)->first();
                        $recommends_form['text_link_url'] = $recommends['recommend_url'];
                        $recommends_form['sub_url1'] = $recommends['sub_url1'];
                        $recommends_form['sub_url2'] = $recommends['sub_url2'];
                        $recommends_form['sub_url3'] = $recommends['sub_url3'];
                        $recommends_form['sub_url4'] = $recommends['sub_url4'];
                        $recommends_form['image_url1'] = $recommends['image_url1'];
                        $recommends_form['image_url2'] = $recommends['image_url2'];
                        $recommends_form['image_url3'] = $recommends['image_url3'];
                    }
                    $this->set('errors', $getSessionRecommendsEditFinish['errors']);
                    $this->set('error_message', $getSessionRecommendsEditFinish['error_message']);
                } else {
                    $recommends = $this->recommendRepository->selectById($id)->first();
                    if (!$recommends) {
                        $recommends_form = $this->initFormData();
                    } else {
                        $recommends_form = $this->initFormDataByRecordId($recommends);
                    }
                }
                $recommends_form['radio_link'] = 0;
                $recommends_form['radio_image'] = 0;
                $controls = $this->_genFormEditControls();
                $this->set('controls', $controls);
                $this->set('recommends_form', $recommends_form);
                $this->set('id', $id);
                $this->set('school_list', $school_list);
                $this->set('kouza_list', $kouza_list);
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_EDIT'));
                $this->render('recommends_edit');
            } else if ($mode == 'recommends_add') {
                $this->request->getSession()->write('is_reload_recommends_add', false);
                $getSessionRecommendsAddFinish = $this->request->getSession()->read('recommends_add_finish_mode') ?? null;
                if ($getSessionRecommendsAddFinish) {
                    if ($getSessionRecommendsAddFinish['added']) {
                        $recommends_form = $this->initFormData();
                    } else {
                        $recommends_form = $getSessionRecommendsAddFinish['recommends_form'];
                        $this->set('errors', $getSessionRecommendsAddFinish['errors']);
                        $this->set('error_message', $getSessionRecommendsAddFinish['error_message']);
                    }
                } else {
                    $recommends_form = $this->initFormData();
                }
                $controls = $this->_genFormEditControls();
                $this->set('controls', $controls);
                $this->set('recommends_form', $recommends_form);
                $this->set('school_list', $school_list);
                $this->set('kouza_list', $kouza_list);
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_ADD'));
                $this->render('recommends_add');
            } else if ($mode == 'recommends_delete_confirm') {
                $id = $this->request->getData('id');
                $records = $this->recommendRepository->selectById($id)->first();
                if (!$records) {
                    throw new Exception('');
                }
                $table_body = $this->makeTableTrRecommendsList(array($records), false);
                $this->set('table_body', $table_body);
                $this->set('id', $id);
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_DELETE_CONFIRM'));
                $this->render('recommends_delete_confirm');
            } else if ($mode == 'recommends_delete_finish') {
                $id = $this->request->getData('id');
                $this->deleteDBById($id);
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_DELETED'));
                $this->render('recommends_delete_finish');
            } else if ($mode == 'recommends_change_records_visible_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->updateDBByIdWithParam($ids, true);
                if (!$result) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_VISIBLE_FINISH'));
                $this->render('recommends_change_records_visible_finish');
            } else if ($mode == 'recommends_change_records_invisible_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->updateDBByIdWithParam($ids, false);
                if (!$result) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_INVISIBLE_FINISH'));
                $this->render('recommends_change_records_invisible_finish');
            } else if ($mode == 'recommends_change_records_delete_finish') {
                $ids = $this->request->getData('ids');
                $ids = array_map('intval', explode(' ', $ids));
                $result = $this->deleteDBById($ids);
                if ($result === 500) {
                    throw new Exception('');
                }
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_DELETE_FINISH'));
                $this->render('recommends_change_records_delete_finish');
            }
        } catch (Exception $e) {
            throw new InternalErrorException();
        }
    }


    //------------------------------------------------
    /// @brief  Create recommends
    /// @author ChanNL
    //------------------------------------------------
    public function recommendsAddFinish()
    {
        try {
            $recommendsAddFinish['Recommends'] = $this->request->getData();
            // validation
            $defaultValidator = $this->Recommends->getValidator('custom');
            if (!$recommendsAddFinish['Recommends']['radio_link']) {
                $defaultValidator->add('text_link_url', 'valid-url', ['rule' => 'url', 'message' => __d('validation', 'RECOMMENDS_FORMAT_URL_INVALID')]);
            }
            $validate = $defaultValidator->validate($recommendsAddFinish['Recommends']);
            if (!$validate) { // empty
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_ADD_FINISH'));
                $param = $recommendsAddFinish['Recommends'];
                $recommends_add_finish_mode = [
                    'recommends_form' => $recommendsAddFinish['Recommends'],
                    'errors' => [],
                    'error_message' => ''
                ];
                $upload_fail = false;
                // Image file reception processing
                $number_list = array(1, 2, 3);
                foreach ($number_list as $number) {
                    $form_key = 'radio_image' . $number;    // radio_image1
                    $tmp_name_key = 'image_file' . $number; // image_file1
                    $field_key = 'image_url' . $number;     // image_url1
                    if (
                        $_POST[$form_key] == 1 &&
                        $_FILES[$tmp_name_key]['error'] == UPLOAD_ERR_OK
                    ) {
                        if (!file_exists(WWW_ROOT . 'face_image')) {
                            mkdir(WWW_ROOT . 'face_image', 0666, true);
                        }
                        $uploads_dir = WWW_ROOT . 'face_image';
                        $tmp_name = $_FILES[$tmp_name_key]['tmp_name'];
                        $name = $_FILES[$tmp_name_key]['name'];
                        $uniqid = uniqid();
                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                        $dest = "$uploads_dir/$uniqid.$ext";
                        $result = move_uploaded_file($tmp_name, $dest);
                        if ($result) {
                            $param[$field_key] = "/face_image/$uniqid.$ext";
                        }
                    }
                }
                // File reception processing for link (subtitle)
                $number_list = array(1, 2, 3, 4);
                foreach ($number_list as $number) {
                    $form_key = 'radio_sub_url' . $number;    // radio_sub_url1
                    $tmp_name_key = 'sub_url_file' . $number; // sub_url_file1
                    $field_key = 'sub_url' . $number;         // sub_url1
                    if (
                        $_POST[$form_key] == 1 &&
                        $_FILES[$tmp_name_key]['error'] == UPLOAD_ERR_OK
                    ) {
                        $tmp_name = $_FILES[$tmp_name_key]['tmp_name'];
                        $uploads_dir = Configure::read('App.pdfBaseUrl');
                        $name = $_FILES[$tmp_name_key]['name'];
                        $uniqid = uniqid();
                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                        $dest = "$uploads_dir/$uniqid.$ext";
                        $result = move_uploaded_file($tmp_name, $dest);
                        if ($result) {
                            $param[$field_key] = "/pdf/$uniqid.$ext";
                            try {
                                $params = array(
                                    'file_path' => "../pdf/$uniqid.$ext",                // File path name on server
                                    'file_size' => $_FILES[$tmp_name_key]['size'],       // file size
                                    'file_comment' => $_FILES[$tmp_name_key]['name'],    // comment
                                );
                                $this->filesRepository->insertDB($params);
                            } catch (Exception $e) {
                                throw new Exception('');
                            }
                        }
                    }
                }
                if ($recommendsAddFinish['Recommends']['radio_link']) {
                    $file = $this->request->getData('file_pdf');
                    if (empty($file->getClientFilename())) {
                        $upload_fail = true;
                    }
                    $result = $this->_receiveFile();
                    if (!$result) {
                        $upload_fail = true;
                    }
                    $recommends_url = $result;
                    $param['recommend_url'] = $recommends_url;
                }
                if ($upload_fail) {
                    $recommends_add_finish_mode['added'] = false;
                    $this->set('errors', __d('validation', 'RECOMMENDS_FILE_UPLOAD_FAIL'));
                    $this->set('title_head', __d('recommends', 'TITLE_HEAD_ERROR'));
                } else {
                    $inserted_record = [];
                    //Exit without doing anything when reloading
                    if (!$this->request->getSession()->read('is_reload_recommends_add')) {
                        $id = $this->insertDB($param);
                        if (!$id) {
                            throw new Exception('');
                        }
                        $inserted_record = $this->recommendRepository->selectById($id)->first();
                    }
                    $table_body = '';
                    if ($inserted_record) {
                        $table_body = $this->makeTableTrRecommendsList(array($inserted_record));
                    }
                    $recommends_add_finish_mode['added'] = true;
                    $this->set('table_body', $table_body);
                }
                $this->request->getSession()->write('recommends_add_finish_mode', $recommends_add_finish_mode);
                $this->request->getSession()->write('is_reload_recommends_add', true);
                $this->render('recommends_add_finish');
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
                if (!empty($errors['recommends_date'])) {
                    if (!empty($errors['title_ym'])) {
                        unset($errors['recommends_date']);
                    } else if (!empty($errors['title_day'])) {
                        unset($errors['recommends_date']);
                    }
                }
                $num_total_error = count($errors);
                $error_message = '';
                if (0 < $num_total_error) {
                    $error_message = "<p class=\"warn\">{$num_total_error}個のエラーがあります。</p>";
                }
                $this->request->getSession()->write('recommends_add_finish_mode', [
                    'recommends_form' => $recommendsAddFinish['Recommends'],
                    'errors' => $errors,
                    'error_message' => $error_message,
                    'added' => false
                ]);
                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'f' => 'recommends_add',
                        'recovery' => 'true'
                    ],
                ]);
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('recommends', 'TITLE_HEAD_ERROR'));
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
            $url = $fd['recommend_url'];
        } else {
            $url = $fd['text_link_url'];
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
            'kouza_id' => $fd['kouza'],
            'recommend_title' => $fd['text_title'],
            'recommend_title_sub' => $fd['text_title_sub'],
            'recommend_url' => $url,
            'enabled_from' => $enable_from,
            'enabled_to' => $enable_to,
            'order_no' => 0,
            'is_active' => 1,
            'image_url1' => $fd['image_url1'],
            'image_url2' => $fd['image_url2'],
            'image_url3' => $fd['image_url3'],
            'sub_title1' => $fd['sub_title1'],
            'sub_title2' => $fd['sub_title2'],
            'sub_title3' => $fd['sub_title3'],
            'sub_title4' => $fd['sub_title4'],
            'sub_url1' => $fd['sub_url1'],
            'sub_url2' => $fd['sub_url2'],
            'sub_url3' => $fd['sub_url3'],
            'sub_url4' => $fd['sub_url4']
        );
        return $this->recommendRepository->insert($params);
    }

    //------------------------------------------------
    /// @brief  Update recommends
    /// @author ChanNL
    //------------------------------------------------
    public function recommendsEditFinish()
    {
        try {
            $recommendsEditFinish['Recommends'] = $this->request->getData();
            $id = $this->request->getData('id') ?? null;
            // validation
            $defaultValidator = $this->Recommends->getValidator('custom');
            if (!$recommendsEditFinish['Recommends']['radio_link']) {
                $defaultValidator->add('text_link_url', 'valid-url', ['rule' => 'url', 'message' => __d('validation', 'RECOMMENDS_FORMAT_URL_INVALID')]);
            }
            $validate = $defaultValidator->validate($recommendsEditFinish['Recommends']);
            if (!$validate) { // empty
                $this->set('id', $id);
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_EDIT_FINISH'));
                $param = $recommendsEditFinish['Recommends'];
                $recommends_edit_finish_mode = [
                    'id' => $id,
                    'recommends_form' => $param,
                    'errors' => [],
                    'error_message' => '',
                    'edited' => false
                ];
                $upload_fail = false;
                $table_body = '';
                //Exit without doing anything when reloading
                if (!$this->request->getSession()->read('is_reload_recommends_edit')) {
                    // Image file reception processing
                    $number_list = array(1, 2, 3);
                    foreach ($number_list as $number) {
                        $form_key = 'radio_image' . $number;    // radio_image1
                        $tmp_name_key = 'image_file' . $number; // image_file1
                        $field_key = 'image_url' . $number;     // image_url1
                        if (
                            $_POST[$form_key] == 1 &&
                            $_FILES[$tmp_name_key]['error'] == UPLOAD_ERR_OK
                        ) {

                            if (!file_exists(WWW_ROOT . 'face_image')) {
                                mkdir(WWW_ROOT . 'face_image', 0666, true);
                            }
                            $uploads_dir = WWW_ROOT . 'face_image';
                            $tmp_name = $_FILES[$tmp_name_key]['tmp_name'];

                            $name = $_FILES[$tmp_name_key]['name'];
                            $uniqid = uniqid();
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $dest = "$uploads_dir/$uniqid.$ext";
                            $result = move_uploaded_file($tmp_name, $dest);
                            if ($result) {
                                $param[$field_key] = "/face_image/$uniqid.$ext";
                            }
                        }
                    }
                    // File reception processing for link (subtitle)
                    $number_list = array(1, 2, 3, 4);
                    foreach ($number_list as $number) {
                        $form_key = 'radio_sub_url' . $number;    // radio_sub_url1
                        $tmp_name_key = 'sub_url_file' . $number; // sub_url_file1
                        $field_key = 'sub_url' . $number;         // sub_url1
                        if (
                            $_POST[$form_key] == 1 &&
                            $_FILES[$tmp_name_key]['error'] == UPLOAD_ERR_OK
                        ) {
                            $tmp_name = $_FILES[$tmp_name_key]['tmp_name'];
                            $uploads_dir = Configure::read('App.pdfBaseUrl');
                            $name = $_FILES[$tmp_name_key]['name'];
                            $uniqid = uniqid();
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $dest = "$uploads_dir/$uniqid.$ext";
                            $result = move_uploaded_file($tmp_name, $dest);
                            if ($result) {
                                $param[$field_key] = "/pdf/$uniqid.$ext";
                                try {
                                    $params = array(
                                        'file_path' => "../pdf/$uniqid.$ext",                // File path name on server
                                        'file_size' => $_FILES[$tmp_name_key]['size'],       // file size
                                        'file_comment' => $_FILES[$tmp_name_key]['name'],    // comment
                                    );
                                    $this->filesRepository->insertDB($params);
                                } catch (Exception $e) {
                                    throw new Exception('');
                                }
                            }
                        }
                    }
                    if ($recommendsEditFinish['Recommends']['radio_link']) {
                        $file = $this->request->getData('file_pdf');
                        if (empty($file->getClientFilename())) {
                            $upload_fail = true;
                        }
                        $result = $this->_receiveFile();
                        if (!$result) {
                            $upload_fail = true;
                        }
                        $recommends_url = $result;
                        $text_link_url = $recommends_url;
                        $param['recommend_url'] = $recommends_url;
                        $param['text_link_url'] = $text_link_url;
                    }
                    if ($upload_fail) {
                        $this->set('errors', __d('validation', 'RECOMMENDS_FILE_UPLOAD_FAIL'));
                        $this->set('title_head', __d('recommends', 'TITLE_HEAD_ERROR'));
                    } else {
                        $inserted_record = [];
                        $update = $this->updateDBById($param);
                        if (!$update) {
                            throw new Exception('');
                        }
                        $inserted_record = $this->recommendRepository->selectById($id)->first();
                        if (!$inserted_record) {
                            throw new Exception('');
                        }
                        if ($inserted_record) {
                            $table_body = $this->makeTableTrRecommendsList(array($inserted_record));
                        }
                        $recommends_edit_finish_mode['edited'] = true;
                    }
                }
                $this->set('table_body', $table_body);
                $recommendsEditFinish['Recommends']['radio_link'] = 0;
                $recommendsEditFinish['Recommends']['radio_image'] = 0;
                $recommendsEditFinish['Recommends']['radio_file'] = 0;
                $this->request->getSession()->write('recommends_edit_finish_mode', $recommends_edit_finish_mode);
                $this->request->getSession()->write('is_reload_recommends_edit', true);
                $this->render('recommends_edit_finish');
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
                $num_total_error = count($errors);
                $error_message = '';
                if (0 < $num_total_error) {
                    $error_message = "<p class=\"warn\">{$num_total_error}個のエラーがあります。</p>";
                }
                $recommendsEditFinish['Recommends']['radio_link'] = 0;
                $recommendsEditFinish['Recommends']['radio_image'] = 0;
                $recommendsEditFinish['Recommends']['radio_file'] = 0;
                $this->request->getSession()->write('recommends_edit_finish_mode', [
                    'id' => $id,
                    'recommends_form' => $recommendsEditFinish['Recommends'],
                    'errors' => $errors,
                    'error_message' => $error_message,
                    'edited' => false
                ]);
                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'f' => 'recommends_edit',
                        'recovery' => 'true'
                    ],
                ]);
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('recommends', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    public function updateDBByIdWithParam($ids, $is_active)
    {
        $params = array(
            'is_active' => $is_active
        );
        return $this->recommendRepository->updateById($ids, $params);
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
            $url = $fd['recommend_url'];
        } else {
            $url = $fd['text_link_url'];
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
            'kouza_id' => $fd['kouza'],
            'recommend_title' => $fd['text_title'],
            'recommend_title_sub' => $fd['text_title_sub'],
            'recommend_url' => $url,
            'enabled_from' => $enable_from,
            'enabled_to' => $enable_to,
            'order_no' => $fd['text_order_no'],
            'is_active' => $fd['radio_is_active'] ? 1 : 0,
            'image_url1' => $fd['image_url1'],
            'image_url2' => $fd['image_url2'],
            'image_url3' => $fd['image_url3'],
            'sub_title1' => $fd['sub_title1'],
            'sub_title2' => $fd['sub_title2'],
            'sub_title3' => $fd['sub_title3'],
            'sub_title4' => $fd['sub_title4'],
            'sub_url1' => $fd['sub_url1'],
            'sub_url2' => $fd['sub_url2'],
            'sub_url3' => $fd['sub_url3'],
            'sub_url4' => $fd['sub_url4']
        );
        return $this->recommendRepository->updateById($fd['id'], $params);
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
        return $this->recommendRepository->deleteById($id);
    }

    //------------------------------------------------
    /// @brief  Get info recommends
    /// @author ChanNL
    //------------------------------------------------
    public function recommendsDetail()
    {
        try {
            $id = null;
            if (!empty($this->request->getQuery('id'))) {
                $id = $this->request->getQuery('id');
            } else if (!empty($this->request->getData('id'))) {
                $id = $this->request->getData('id');
            }
            $recommends_list = $this->recommendRepository->selectById($id)->first();
            $contents = array(
                'id' => arrayGet($recommends_list, 'id', ''),
                'recommend_title' => arrayGet($recommends_list, 'recommend_title', ''),
                'recommend_title_sub' => $this->lfToBr(arrayGet($recommends_list, 'recommend_title_sub', '')),
                'recommend_url' => arrayGet($recommends_list, 'recommend_url', ''),
                'enabled_from' => arrayGet($recommends_list, 'enabled_from', ''),
                'enabled_to' => arrayGet($recommends_list, 'enabled_to', ''),
                'order_no' => arrayGet($recommends_list, 'order_no', ''),
                'is_active' => arrayGet($recommends_list, 'is_active', ''),
                'created' => arrayGet($recommends_list, 'created', ''),
                'modified' => arrayGet($recommends_list, 'modified', ''),
                'school_name' => arrayGet($recommends_list, 'school_name', ''),
                'kouza_name' => arrayGet($recommends_list, 'kouza_name', ''),
                'image_url1' => arrayGet($recommends_list, 'image_url1', ''),
                'image_url2' => arrayGet($recommends_list, 'image_url2', ''),
                'image_url3' => arrayGet($recommends_list, 'image_url3', ''),
                'sub_title1' => arrayGet($recommends_list, 'sub_title1', ''),
                'sub_title2' => arrayGet($recommends_list, 'sub_title2', ''),
                'sub_title3' => arrayGet($recommends_list, 'sub_title3', ''),
                'sub_title4' => arrayGet($recommends_list, 'sub_title4', ''),
                'sub_url1' => !empty(arrayGet($recommends_list, 'sub_url1', '')) ? "/tacmap" . arrayGet($recommends_list, 'sub_url1', '') : '',
                'sub_url2' => !empty(arrayGet($recommends_list, 'sub_url2', '')) ? "/tacmap" . arrayGet($recommends_list, 'sub_url2', '') : '',
                'sub_url3' => !empty(arrayGet($recommends_list, 'sub_url3', '')) ? "/tacmap" . arrayGet($recommends_list, 'sub_url3', '') : '',
                'sub_url4' => !empty(arrayGet($recommends_list, 'sub_url4', '')) ? "/tacmap" . arrayGet($recommends_list, 'sub_url4', '') : '',
            );
            if (('t' == $contents['is_active']) || ('true' == $contents['is_active'])) {
                $contents['is_active'] = '○';
            } else {
                $contents['is_active'] = '×';
            }
            if ($recommends_list) {
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
            $this->request->getSession()->delete('recommends_edit_finish_mode');

            $this->set('contents', $contents);
            $this->set('title_head', __d('recommends', 'TITLE_HEAD'));
            $this->render('recommends_detail');
        } catch (Exception $e) {
            $this->set('title_head', __d('recommends', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    public function recommendsChangeRecords()
    {
        try {
            $ids = $this->request->getData('ids');
            if (empty($ids)) {
                $recommends_list = [];
            } else {
                $recommends_list = $this->recommendRepository->selectById($ids)->toArray();
            }
            $table_body = $this->makeTableTrRecommendsList($recommends_list, false);
            $this->set('ids', $ids);
            $this->set('table_body', $table_body);
            if ($this->request->getData('visible')) {
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_CHANGE_RECORDS_VISIBLE'));
                $this->render('recommends_change_records_visible_confirm');
            } else if ($this->request->getData('invisible')) {
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_CHANGE_RECORDS_INVISIBLE'));
                $this->render('recommends_change_records_invisible_confirm');
            } else if ($this->request->getData('delete')) {
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_RECOMMENDS_CHANGE_RECORDS_DELETE'));
                $this->render('recommends_change_records_delete_confirm');
            } else {
                $this->set('title_head', __d('recommends', 'TITLE_HEAD_ERROR'));
                $this->render('error');
            }
        } catch (Exception $e) {
            $this->set('title_head', __d('recommends', 'TITLE_HEAD_ERROR'));
            throw new InternalErrorException();
        }
    }

    //------------------------------------------------
    /// @brief  Download Csv
    /// @author ChanNL
    //------------------------------------------------
    public function recommendsDownloadCsv()
    {
        $school_id = $this->request->getSession()->read('recommend_school_id') ?? null;
        $kouza_id = $this->request->getSession()->read('recommend_kouza_id') ?? null;
        $is_active = $this->request->getSession()->read('recommend_is_active') ?? null;
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
        $order_by = $this->request->getSession()->read('recommend_order_by') ?? ADMIN_RECOMMENDS_LIST_DEFAULT_ORDER;
        switch ($order_by) {
            case 0:
                $is_desc = false;
                $order = 'kouza_order_no';
                break;
            case 1:
                $is_desc = true;
                $order = 'kouza_order_no';
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

        $args = array(
            'school_id' => (0 == $school_id) ? null : $school_id,
            'kouza_id' => (0 == $kouza_id) ? null : $kouza_id,
            'is_active' => $is_active,
            'order' => $order,
            'is_desc' => $is_desc
        );

        $records = $this->recommendRepository->selectBySchoolAndKouza($args);
        if (false === $records) {
            throw new InternalErrorException();
        }
        $num_records = $records->count();
        $fname = 'recommends-' . date('YmdHi') . '.csv';

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

                $str = '"' . $this->arrayGet($elm, 'school_id', '') . '",'
                    . '"' . $this->arrayGet($elm, 'kouza_id', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'recommend_title', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'recommend_title_sub', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'recommend_url', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'sub_title1', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'sub_url1', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'sub_title2', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'sub_url2', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'sub_title3', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'sub_url3', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'sub_title4', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'sub_url4', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'image_url1', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'image_url2', '') . '",'
                    .  '"' . $this->arrayGet($elm, 'image_url3', '') . '",'
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
    /// @brief  Get recommends list
    /// @author ChanNL
    //------------------------------------------------
    public function getRecommendsList()
    {
        $school_id = $this->request->getSession()->read('recommend_school_id') ?? null;
        $kouza_id = $this->request->getSession()->read('recommend_kouza_id') ?? null;
        $is_active = $this->request->getSession()->read('recommend_is_active') ?? null;
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

        $order_by = $this->request->getSession()->read('recommend_order_by') ?? ADMIN_RECOMMENDS_LIST_DEFAULT_ORDER;
        switch ($order_by) {
            case 0:
                $is_desc = false;
                $order = 'kouza_order_no';
                break;
            case 1:
                $is_desc = true;
                $order = 'kouza_order_no';
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

        $args = array(
            'school_id' => (0 == $school_id) ? null : $school_id,
            'kouza_id' => (0 == $kouza_id) ? null : $kouza_id,
            'is_active' => $is_active,
            'order' => $order,
            'is_desc' => $is_desc
        );

        $limit = PER_PAGE; // Number of data displayed on one page
        $records = $this->recommendRepository->selectBySchoolAndKouza($args);
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
        $controls['school_id'] = $this->request->getSession()->read('recommend_school_id') ?? 0;

        // course list
        $controls['kouza_id'] = $this->request->getSession()->read('recommend_kouza_id') ?? 0;

        // display state list
        $controls['is_active_list'] = array(
            0 => __d('recommends', 'IS_ACTIVE_DISPLAY_STATE'),
            1 => __d('recommends', 'IS_ACTIVE_DISPLAY'),
            2 => __d('recommends', 'IS_ACTIVE_HIDDEN')
        );
        $controls['is_active'] = $this->request->getSession()->read('recommend_is_active') ?? 0;

        // sorted list
        $order_by_list = array(
            0 => __d('recommends', 'SORT_BY_COURSE_ORDER_ASC'),
            1 => __d('recommends', 'SORT_BY_COURSE_ORDER_DESC'),
            2 => __d('recommends', 'SORT_BY_ID_ASC'),
            3 => __d('recommends', 'SORT_BY_ID_DESC'),
            4 => __d('recommends', 'SORT_BY_SCHOOL_ORDER_ASC'),
            5 => __d('recommends', 'SORT_BY_SCHOOL_ORDER_DESC'),
            6 => __d('recommends', 'SORT_BY_MODIFIED_ASC'),
            7 => __d('recommends', 'SORT_BY_MODIFIED_DESC')
        );
        $controls['order_by_list'] = $order_by_list;
        $controls['order_by'] = $this->request->getSession()->read('recommend_order_by') ?? ADMIN_RECOMMENDS_LIST_DEFAULT_ORDER;

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
            $this->request->getSession()->write('recommend_' . $key, $value);
        }
    }

    //------------------------------------------------
    /// @brief  Generate tr rows (multiple) of recommended course table
    /// @param  $records        Data array for table output
    /// @param  $with_checkbox  whether to add a checkbox
    /// @param  $checkbox_name  The name to set in the name field of the checkbox (default is 'ids[]')
    /// @return tr tag
    /// @author ChanNL
    //------------------------------------------------
    public function makeTableTrRecommendsList($records, $with_checkbox = false, $checkbox_name = 'ids[]')
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $pos = strpos($request_uri, 'recommends_list');
        if ($pos !== false) {
            $request_uri = '/recommends_list';
        }
        $document_root_path = str_replace('/recommends_edit_finish', '', $request_uri);
        $document_root_path = str_replace('/recommends_add_finish', '', $document_root_path);
        $document_root_path = str_replace('/recommends_change_records', '', $document_root_path);
        $cnt = count($records);
        $tag = '';
        for ($i = 0; $i < $cnt; $i++) {
            // Sort order
            // チェックボックス、id, 校舎、講座、タイトル、サブタイトル、リンク、並び補正、表示、登録日、有効期間（始）、有効期間（終）、作成日、更新日
            $r = $records[$i];

            $id = cleanTags($r['id']);
            $id_field = '<a href="' . $document_root_path . '/recommends_detail?id=' . $id . '">' . $id . '</a>';
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
            $recommend_title = mb_strimwidth($r['recommend_title'], 0, ADMIN_RECOMMENDS_TITLE_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $recommend_title_sub = mb_strimwidth($r['recommend_title_sub'], 0, ADMIN_RECOMMENDS_TITLE_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $recommend_url = mb_strimwidth($r['recommend_url'], 0, ADMIN_RECOMMENDS_LINK_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $tr_class = ($i % 2) ? 'even' : 'odd';
            $title_url_line = '';
            if (0 < strlen($r['recommend_url'])) {
                $url = cleanTags($r['recommend_url']);
                $title_url_line = '<td title="' . $url . '"><a href="' . $url . '" target="_blank">' . cleanTags($recommend_url) . '</a></td>';
            } else {
                $title_url_line = '<td title="' . cleanTags($r['recommend_url']) . '">' . cleanTags($recommend_url) . '</td>';
            }

            $chekcbox_line = '';
            if ($with_checkbox) {
                $chekcbox_line = "<td><input type=\"checkbox\" name=\"{$checkbox_name}\" value=\"{$id}\"/></td>";
            }

            $tag .= "<tr class=\"{$tr_class} {$visible_class}\">"
                .  $chekcbox_line
                .  "<td>{$id_field}</td>"
                .  '<td>' . cleanTags($r['school_name']) . '</td>'
                .  '<td>' . $r['kouza_name'] . '</td>'      // 講座名には実体参照(&reg;)を含めるのでタグ除去しない(2012-07-20 15:13:13)
                .  '<td title="' . cleanTags($r['recommend_title']) . '">' . cleanTags($recommend_title) . '</td>'
                .  '<td title="' . cleanTags($r['recommend_title_sub']) . '">' . cleanTags($recommend_title_sub) . '</td>'
                . $title_url_line
                .  '<td>' . cleanTags($r['order_no']) . '</td>'
                .  '<td>' . cleanTags($is_active) . '</td>'
                .  '<td>' . $enabled_from . '</td>'
                .  '<td>' . $enabled_to . '</td>'
                .  '<td>' . $created . '</td>'
                .  '<td>' . $modified . '</td>'
                .  '</tr>';
        }

        return $tag;
    }

    //------------------------------------------------
    /// @brief  Init form data
    /// @author ChanNL
    //------------------------------------------------
    public function initFormData()
    {
        $recommends['school'] = 0;
        $recommends['kouza'] = 0;
        $recommends['from_ym'] = 0;
        $recommends['from_day'] = 0;
        $recommends['from_time'] = '_';
        $recommends['to_ym'] = 0;
        $recommends['to_day'] = 0;
        $recommends['to_time'] = '_';
        $recommends['text_title'] = '';
        $recommends['text_title_sub'] = '';
        $recommends['text_link_url'] = '';
        $recommends['radio_link'] = 0;                      // 常にURLがデフォルト
        $recommends['radio_image'] = 0;                      // 常にURLがデフォルト
        $recommends['image_url1'] = '';
        $recommends['image_url2'] = '';
        $recommends['image_url3'] = '';
        $recommends['sub_title1'] = '';
        $recommends['sub_title2'] = '';
        $recommends['sub_title3'] = '';
        $recommends['sub_title4'] = '';
        $recommends['sub_url1'] = '';
        $recommends['sub_url2'] = '';
        $recommends['sub_url3'] = '';
        $recommends['sub_url4'] = '';
        $recommends['radio_is_active'] = 1;
        return $recommends;
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
        $recommend_title = $vo['recommend_title'];
        $recommend_title_sub = $vo['recommend_title_sub'];
        $recommend_url = $vo['recommend_url'];
        $vo['enabled_from'] = $this->checkAndFormatDate($vo['enabled_from']);
        $vo['enabled_to'] = $this->checkAndFormatDate($vo['enabled_to']);
        $vo['created'] = $this->checkAndFormatDate($vo['created']);
        $vo['modified'] = $this->checkAndFormatDate($vo['modified']);
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
        $image_url1 = $vo['image_url1'];
        $image_url2 = $vo['image_url2'];
        $image_url3 = $vo['image_url3'];

        $sub_title1 = $vo['sub_title1'];
        $sub_title2 = $vo['sub_title2'];
        $sub_title3 = $vo['sub_title3'];
        $sub_title4 = $vo['sub_title4'];

        $sub_url1 = $vo['sub_url1'];
        $sub_url2 = $vo['sub_url2'];
        $sub_url3 = $vo['sub_url3'];
        $sub_url4 = $vo['sub_url4'];

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

        $recommends['school'] = $school_id;
        $recommends['kouza'] = $kouza_id;
        $recommends['from_ym'] = $from_ym;
        $recommends['from_day'] = $from_day;
        $recommends['from_time'] = $from_time;
        $recommends['to_ym'] = $to_ym;
        $recommends['to_day'] = $to_day;
        $recommends['to_time'] = $to_time;
        $recommends['text_title'] = $recommend_title;
        $recommends['text_title_sub'] = $recommend_title_sub;
        $recommends['text_link_url'] = $recommend_url;
        $recommends['radio_link'] = 0;                      // 常にURLがデフォルト
        $recommends['text_order_no'] = $order_no;
        $recommends['radio_is_active'] = $is_active;
        $recommends['radio_image'] = 0;                      // 常にURLがデフォルト
        $recommends['image_url1'] = $image_url1;
        $recommends['image_url2'] = $image_url2;
        $recommends['image_url3'] = $image_url3;
        $recommends['sub_title1'] = $sub_title1;
        $recommends['sub_title2'] = $sub_title2;
        $recommends['sub_title3'] = $sub_title3;
        $recommends['sub_title4'] = $sub_title4;
        $recommends['sub_url1'] = $sub_url1;
        $recommends['sub_url2'] = $sub_url2;
        $recommends['sub_url3'] = $sub_url3;
        $recommends['sub_url4'] = $sub_url4;
        return $recommends;
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

        // Publication start month list
        $controls['from_ym'] = $ym_list;

        // Publication end month list
        $controls['to_ym'] = $ym_list_enabled_to;

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
        $controls['radio_link'] = array(0 => 'URL', 1 => 'PDF');
        $controls['radio_image'] = array(0 => 'URL', 1 => 'FILE');
        $controls['radio_file'] = array(0 => 'URL', 1 => 'PDF');

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
