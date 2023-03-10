<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\SuccessfulCandidatesController;
use App\Repositories\VoiceUserFormDatas\VoiceUserFormDataRepository;
use App\Repositories\VoiceUserFormDataOptions\VoiceUserFormDataOptionRepository;
use App\Repositories\VoicePartOptions\VoicePartOptionRepository;
use App\Repositories\VoiceSubjectTypes\VoiceSubjectTypeRepository;
use Cake\Event\EventInterface;
use Cake\Http\Exception;
use Cake\Core\Configure;


class SuccessfulCandidatesControlController extends SuccessfulCandidatesController {

    public function initialize(): void
    {
        parent::initialize();

        // Configure flash messages
        $this->Flash->setConfig('clear', true);

        // load models
        $this->VoiceUserFormDatas = $this->fetchTable('VoiceUserFormDatas');
        $this->VoiceUserFormDataOptions = $this->fetchTable('VoiceUserFormDataOptions');
        $this->VoicePartOptions = $this->fetchTable('VoicePartOptions');
        $this->VoiceSubjectTypes = $this->fetchTable('VoiceSubjectTypes');

        // define repo
        $this->voiceUserFormDataRepo = new VoiceUserFormDataRepository($this->VoiceUserFormDatas);
        $this->voiceUserFormDataOptionRepo = new VoiceUserFormDataOptionRepository($this->VoiceUserFormDataOptions);
        $this->voicePartOptionRepo = new VoicePartOptionRepository($this->VoicePartOptions);
        $this->voiceSubjectTypeRepo = new VoiceSubjectTypeRepository($this->VoiceSubjectTypes);
    }

    public function beforeFilter(EventInterface $event) {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['index']);
    }

    public function beforeRender(EventInterface $event) {
        parent::beforeRender($event);

        if ($this->request->getParam('action') == 'download_csv') {
            $this->viewBuilder()->setLayout('ajax');
        }
        $this->viewBuilder()->addHelpers(['Csv']);
    }

    public function postData() {
        if (!empty($this->request->getData('saveUserFormDatas'))) {
            $this->saveUserFormDatas();

        } else {
            $this->index();
            $this->render('index');
        }
    }

    public function index() {

        $categoryId = $this->request->getSession()->read('categoryId');
        $isSet = $this->request->getSession()->read('formId');
        if (!empty($isSet)) {
            $formId = $this->request->getSession()->read('formId');
        }

        if ($this->request->isGet() && !empty($this->request->getQuery('formId'))) {
            $formId = $this->request->getQuery('formId');
            $this->request->getSession()->delete('formId');
            $this->request->getSession()->write('formId', $formId);
        }

        if (empty($formId) || empty($categoryId)) {
            throw new Exception\NotFoundException;
        } else if (!$this->voiceFormRepo->checkExistVoiceFormsWithCategoryID($formId, $categoryId)) { // Check authorization
            throw new Exception\NotFoundException;
        }
        $productLists = [];
        $action = null;

        $voiceForm = $this->voiceFormRepo->getVoiceFormByID($formId);

        $createdPartLists = $this->voicePartRepo->getListSelectedFieldsWithKeyValue($formId, 'slug', 'slug', ['fix_form' => 'ASC']);

        $formDatas = $this->voicePartRepo->getAllByFormIDFixForm($formId, ['id' => 'ASC', 'fix_form' => 'ASC']);

        $firstData = $this->voiceUserFormDataRepo->getDetailUserFormDataByFormID($formId);
        $firstId = isset($firstData['id']) ? $firstData['id'] - 1 : 0;


        $pageLimit = 10;
        $paginate = [
            'limit' => $pageLimit,
            'conditions' => ['form_id' => $formId],
            'order' => ['id' => 'ASC', 'fix' => 'DESC']
        ];
        $paginate['conditions'] = $this->_makeConditions($formId);

        $this->paginate = $paginate;
        $voiceUserFormDatas = $voiceUserFormDatasRaw = $this->paginate('VoiceUserFormDatas')->toArray();
        if (!empty($voiceUserFormDatasRaw)) {
            $voiceUserFormDatas = [];
        }
        foreach ($voiceUserFormDatasRaw as $key => $value) {
            $voiceUserFormDatas[$key]['VoiceUserFormData'] = $value->toArray();
            $formData = $this->voiceUserFormDataOptionRepo->getUserFormDataOptionByUserFormID($value['id']);
            $voiceUserFormDatas[$key]['VoiceUserFormDataOption'] = [];
            foreach ($formData as $key2 => $value2) {
                $voiceUserFormDatas[$key]['VoiceUserFormDataOption'][$key2] = $value2->toArray();
                $voiceUserFormDatas[$key]['VoiceUserFormDataOption'][$key2]['VoicePart'] = isset($value2['voice_part']) ? $value2['voice_part']->toArray() : [];
                unset($voiceUserFormDatas[$key]['VoiceUserFormDataOption'][$key2]['voice_part']);
            }
        }

        $voiceUserFormDatas = $this->setZeirishiDatas($voiceUserFormDatas, $formId);
        $selectLists = array(RADIO, 'LIST', ZEIRISHI_KAMOKU,CHECKBOX,JYUKENTIKU1, JYUKENTIKU2,JYUKENTIKU3);

        foreach ($voiceUserFormDatas as $key => $voiceUserFormData) {

            foreach ($voiceUserFormData['VoiceUserFormDataOption'] as $key2 => $voiceUserFormDataOption) {
                $voiceePartLists = $this->voicePartOptionRepo->getListByPartID($voiceUserFormDataOption['part_id']);

                if (!empty($voiceUserFormDataOption['VoicePart']) && in_array($voiceUserFormDataOption['VoicePart']['slug'], $selectLists)) {
                    $voiceUserFormDatas[$key]['VoiceUserFormData'][$voiceUserFormDataOption['part_id']][$key2]['value'] = $voiceePartLists[$voiceUserFormDataOption['value']];
                } else {
                    $voiceUserFormDatas[$key]['VoiceUserFormData'][$voiceUserFormDataOption['part_id']][$key2]['value'] = $voiceUserFormDataOption['value'];
                }
                $voiceUserFormDatas[$key]['VoiceUserFormData'][$voiceUserFormDataOption['part_id']][$key2]['id'] = $voiceUserFormDataOption['id'];
            }
        }

        $this->request->getSession()->write('voiceUserFormDatas', $voiceUserFormDatas);
        $this->set(compact('formDatas', 'voiceUserFormDatas', 'productLists', 'firstId',
        'formId', 'voiceForm', 'selectLists', 'pageLimit', 'createdPartLists'));
    }

    /**
     * Private method: create condition
     *
     * @return Array
     */
    private function _makeConditions($formId) {
        $search = null;
        if (!empty($this->request->getData())) {
            $this->request->getSession()->delete('search');
            $search = $this->request->getData('Search');
            $this->request->getSession()->write('search', $search);
        }
        $isSet = $this->request->getSession()->read('search');
        if (!empty($isSet)) {
            $search = $this->request->getSession()->read('search');
        }
        if (!empty($search['start']) || !empty($search['end'])) {
            $conditions = array('created >= ' => $search['start'],  'created <= ' => $search['end']);
        }
        if (isset($search['release']) && $search['release'] != '') {
            $conditions['`release`'] = $search['release'];
        }

        if (isset($search['status']) && $search['status'] != '') {
            $conditions['status'] = $search['status'];
        }
        if (!empty($formId)) {
            $conditions['form_id'] = $formId;
        }

        if (isset($search['zeirishi2']) && $search['zeirishi2'] != '') {
            $zeiriSearchLists = $this->voiceZeirishiListRepo->getListID();
            $zeiriPartData = $this->voicePartRepo->getDetailsByFormID($formId);

            $zeiriPartId = $zeiriPartData['id'];
            $zeiriVoiceUserDataOptionIds = $this->voiceUserFormDataOptionRepo->getUserFormDataOptionByPartID($zeiriPartId, $zeiriSearchLists);

            if (!empty($zeiriVoiceUserDataOptionIds)) {
                if ($search['zeirishi2'] == 2) { // Failed
                    $conditions['id IN'] = $zeiriVoiceUserDataOptionIds;
                } else if($search['zeirishi2'] == 1) { // Passed
                    $conditions['NOT']['id IN'] = $zeiriVoiceUserDataOptionIds;
                }
            }
        }
        $this->set(compact('search'));
        return $conditions;
    }

    /**
     * method: set Zeirishi data
     *
     * @return Array
     */
    public function setZeirishiDatas($voiceUserFormDatas, $formId) {

        $isZeiri = $this->voicePartRepo->getDetailsByFormID($formId);
        $voiceZeirishiLists = [];
        if ($isZeiri) {
            $voiceZeirishiLists = $this->voiceZeirishiListRepo->getListSelectedFieldsWithKeyValue('id', 'name');
            $zeiriPartIdLists = $this->voicePartRepo->getListPartIDBySlug(ZEIRISHI);
            $voiceZeirishSubjectiLists = $this->voiceZeirishiListRepo->getListSelectedFieldsWithKeyValue('id', 'subject_type_id');

            foreach ($voiceUserFormDatas as $key => $value) {
                if (!isset($voiceUserFormDatas[$key]['VoiceUserFormData'])) {
                    $voiceUserFormDatas[$key]['VoiceUserFormData'] = [];
                }
                foreach ($value['VoiceUserFormDataOption'] as $key2 => $value2) {
                    if (in_array($value2['part_id'], $zeiriPartIdLists)) {
                        $subject_type_id = $voiceZeirishSubjectiLists[$value2['value']];
                        if (isset($voiceUserFormDatas[$key]['VoiceUserFormData']['zeirishi'.$subject_type_id])) {
                            $voiceUserFormDatas[$key]['VoiceUserFormData']['zeirishi'.$subject_type_id] .= $voiceZeirishiLists[$value2['value']] . ',';
                        } else {
                            $voiceUserFormDatas[$key]['VoiceUserFormData']['zeirishi'.$subject_type_id] = $voiceZeirishiLists[$value2['value']] . ',';
                        }
                        unset($voiceUserFormDatas[$key]['VoiceUserFormDataOption'][$key2]);
                    }
                }
            }
        }
        $this->set(compact('isZeiri', 'voiceZeirishiLists'));
        return $voiceUserFormDatas;
    }

    /**
     * method: save post data
     *
     * @return Redirect
     */
    public function saveUserFormDatas() {

        $postedDatas = $this->request->getData();
        foreach ($postedDatas['VoiceUserFormData'] as $postedData) {
            // $this->VoiceUserFormData->create();

            $validator = $this->VoiceUserFormDatas->getValidator('default');
            $errors = $validator->validate($postedData);

            if (empty($errors) && !empty($postedData['check'])) {
                $postedData['modified'] = date('Y-m-d H:i:s');
                $result = $this->voiceUserFormDataRepo->createOrUpdate($postedData);
                if ($result && !$result->hasErrors) {

                    if (!empty($postedData['VoiceUserFormDataOption'])) {
                        foreach ($postedData['VoiceUserFormDataOption'] as $value) {
                            if (!empty($value)) {
                                $saveData = [
                                    'id' => $value['id'],
                                    'value' => $value['value'],
                                ];
                                $this->voiceUserFormDataOptionRepo->createOrUpdate($saveData);
                            }
                        }
                    }
                } else {
                    $this->Flash->error(__d('successful_candidate', 'CONTENT_INCOMPLETE'));
                }
            } else {
                $this->Flash->error(__d('successful_candidate', 'CONTENT_INCOMPLETE'));
            }
        }
        $this->Flash->success(__d('successful_candidate', 'REGISTER_COMPLETED'));
        $isSet = $this->request->getSession()->read('formId');
        if (!empty($isSet)) {
            $formId = $this->request->getSession()->read('formId');
        }
        $this->redirect('/successful_candidates_control?formId=' . $formId);

    }

    /**
     * method: download file csv
     *
     * @return string
     */
    function downloadCsv() {
        $this->viewBuilder()->setLayout('ajax');
        set_time_limit(0);
        $fileName = 'Csv_'.date('YmdHis') . '.csv';
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename={$fileName}");
        $fp = fopen('php://output','w');

        // // SJIS変換
        stream_filter_append($fp, 'convert.iconv.UTF-8/CP932//TRANSLIT', STREAM_FILTER_WRITE);
        $formId = $this->request->getSession()->read('formId');
        $createdPartLists = $this->voicePartRepo->getListByFormID($formId, 'slug', ['id' => 'ASC', 'fix_form' => 'ASC']);

        $thN = array('登録日', 'ID');
        $th = array('created', 'id');

        if (in_array(TACNUMBER, $createdPartLists)) {
            array_push($thN, '会員番号');
            array_push($th, 'tac_number');
        }
        if (in_array(NAME, $createdPartLists)) {
            array_push($thN, '姓');
            array_push($thN, '名');
            array_push($th, 'sei');
            array_push($th, 'mei');
        }
        if (in_array(FURIGANA, $createdPartLists)) {
            array_push($thN, 'セイ');
            array_push($thN, 'メイ');
            array_push($th, 'f_sei');
            array_push($th, 'f_mei');
        }
        if (in_array(BIRTHDAY, $createdPartLists)) {
            array_push($thN, '生年月日');
            array_push($th, 'birthday');
        }
        if (in_array(MAIL, $createdPartLists)) {
            array_push($thN, 'メールアドレス');
            array_push($th, 'mail_address');
        }
        if (in_array(RELEASE, $createdPartLists)) {
            array_push($thN, '公開');
            array_push($th, 'release');
        }
        if (in_array(FURIGANA, $createdPartLists)) {
            array_push($thN, 'イニシャル');
            array_push($th, 'initial_name');
        }

        array_push($thN, '確認');
        array_push($th, 'status');

        if (in_array(PHOTO, $createdPartLists)) {
            array_push($thN, '写真の表示');
            array_push($th, 'show_photo');
        }

        if (!empty($this->request->getQuery('isZeiri'))) {
            $voiceSubjectType = $this->voiceSubjectTypeRepo->getListSelectedFieldsWithKeyValue('id', 'name', ['id']);

            $thN = array_merge($thN, $voiceSubjectType);
            for ($i = 1; $i <= 5; $i++) {
                $th[] = 'zeirishi'.$i;
            }
        }
        $formDatas = $this->voicePartRepo->getAllByFormIDFixForm($formId, ['id' => 'ASC', 'fix_form' => 'ASC']);

        foreach ($formDatas as  $formData) {
            if (empty($formData['fix_form'])) {
                $title_names[] = $formData['title_name'];
                $title_ids[] = $formData['id'];
                //            $thN = array_merge($thN, $formData['title_name']);
            }

        }
        if (!empty($title_names)) {
            $thN = array_merge($thN,$title_names);
        }
        if (!empty($title_ids)) {
            $th = array_merge($th,$title_ids);
        }

        fputcsv($fp, $thN, ',', '"');

        $options = [
            'fields' => [],
            'conditions' => ['form_id' => $formId],
            'order' => ['id' => 'ASC', 'fix' => 'DESC']
        ];
        $options['conditions'] = $this->_makeConditions($formId);

        $firstOptions = [
            'fields' => [],
            'conditions' => ['form_id' => $formId],
            'order' => ['id' => 'ASC'],
        ];

        $firstData = $this->voiceUserFormDataRepo->getByConditions($firstOptions['conditions'], $firstOptions['fields'], $firstOptions['order']);
        $firstId = $firstData[0]['id'] - 1;

        $recordCount = $this->voiceUserFormDataRepo->countByConditions($options['conditions'], $options['fields'], $options['order']);

        $limit = 10;
        $loopCount = $recordCount / $limit + ($recordCount % $limit > 0 ? 1 : 0);
        $options['limit'] = $limit;
        for($i = 0; $i < $loopCount; $i++){
            $offset = $i * $limit;
            $options['offset'] = $offset;
            $voiceUserFormDatasRaw = $this->voiceUserFormDataRepo->getByConditions($options['conditions'], $options['fields'], $options['order'], $options);

            if(empty($voiceUserFormDatasRaw)) {
                break;
            }
            $voiceUserFormDatas = [];
            foreach ($voiceUserFormDatasRaw as $key => $value) {
                $voiceUserFormDatas[$key]['VoiceUserFormData'] = $value->toArray();
                $formData = $this->voiceUserFormDataOptionRepo->getUserFormDataOptionByUserFormID($value['id']);

                $voiceUserFormDatas[$key]['VoiceUserFormDataOption'] = [];
                foreach ($formData as $key2 => $value2) {
                    $voiceUserFormDatas[$key]['VoiceUserFormDataOption'][$key2] = $value2->toArray();
                    $voiceUserFormDatas[$key]['VoiceUserFormDataOption'][$key2]['VoicePart'] = isset($value2['voice_part']) ? $value2['voice_part']->toArray(): [];
                    unset($voiceUserFormDatas[$key]['VoiceUserFormDataOption'][$key2]['voice_part']);
                }
            }
            $voiceUserFormDatas = $this->setZeirishiDatas($voiceUserFormDatas, $formId);

            $selectLists = array(RADIO, 'LIST', ZEIRISHI_KAMOKU,CHECKBOX,JYUKENTIKU1, JYUKENTIKU2,JYUKENTIKU3);
            foreach ($voiceUserFormDatas as $key => $voiceUserFormData) {

                foreach ($voiceUserFormData['VoiceUserFormDataOption'] as $key2 => $voiceUserFormDataOption) {

                    $voiceePartLists = $this->voicePartOptionRepo->getListByPartID($voiceUserFormDataOption['part_id']);
                    if (!empty($voiceUserFormDataOption['VoicePart']) && in_array($voiceUserFormDataOption['VoicePart']['slug'], $selectLists)) {
                        $voiceUserFormDatas[$key]['VoiceUserFormData'][$voiceUserFormDataOption['part_id']][$key2]['value'] = $voiceePartLists[$voiceUserFormDataOption['value']];
                    } else {
                        $voiceUserFormDatas[$key]['VoiceUserFormData'][$voiceUserFormDataOption['part_id']][$key2]['value'] = $voiceUserFormDataOption['value'];
                    }
                    $voiceUserFormDatas[$key]['VoiceUserFormData'][$voiceUserFormDataOption['part_id']][$key2]['id'] = $voiceUserFormDataOption['id'];
                }
            }

            $td = $voiceUserFormDatas;

            $column = 'VoiceUserFormData';
            foreach($td as $k => $t) {
                $fieldData = array();
                foreach ($th as $h) {
                    if (isset($t[$column][$h]) && !is_array($t[$column][$h])) {
                        if ($h == 'created') {
                            $fieldData[] = $t[$column][$h]->format('Y-m-d H:i:s');
                        } elseif ($h == 'id') {
                            $fieldData[] = $t[$column][$h] - $firstId;
                        } else {
                            $fieldData[] = $t[$column][$h];
                        }
                    } else {
                        $a = null;
                        if (isset($t[$column][$h])) {
                            foreach ($t[$column][$h] as $key => $value) {
                                $a .= $value['value'].',';
                            }
                        }
                        $fieldData[] = $a;
                    }
                }
                fputcsv($fp, $fieldData, ',', '"');
            }
        }
        fclose($fp);
        return $this->response->withStringBody('');
    }

    /**
     * method: change send mail
     *
     * @return string
     */
    public function changeSendMail() {
        $this->disableAutoRender();
        Configure::write('debug', 'off');
        if (!$this->request->is('ajax')) {
            return $this->response->withStringBody('0');
        }
        $formId = $this->request->getData('formId');
        $val = $this->request->getData('val') == 'on' ? '1' : '0';
        return $this->response->withStringBody($this->voiceFormRepo->updateFields($formId, ['send_mail' => $val]) ? '1' : '0');
    }

    /**
     * method: change show people
     *
     * @return string
     */
    public function changeShowPeople() {
        $this->disableAutoRender();
        Configure::write('debug', 'off');
        if (!$this->request->is('ajax')) {
            return $this->response->withStringBody('0');
        }
        $formId = $this->request->getData('formId');
        $val = $this->request->getData('val') == 'on' ? '1' : '0';
        return $this->response->withStringBody($this->voiceFormRepo->updateFields($formId, ['show_people' => $val]) ? '1' : '0');
    }

    /**
     * method: change lock
     *
     * @return string
     */
    public function changeLock() {
        $this->disableAutoRender();
        Configure::write('debug', 'off');
        if (!$this->request->is('ajax')) {
            return $this->response->withStringBody('0');
        }
        $formId = $this->request->getData('formId');
        $val = $this->request->getData('val') == 'on' ? '1' : '0';
        return $this->response->withStringBody($this->voiceFormRepo->updateFields($formId, ['`lock`' => $val]) ? '1' : '0');
    }

    /**
     * method: delete user datas with relationship
     *
     * @return string
     */
    public function deleteUserData($id) {

        $formId = $this->request->getSession()->read('formId');
        $categoryId = $this->request->getSession()->read('categoryId');
        if (empty($formId) || empty($categoryId)) {
            throw new Exception\NotFoundException;
        } else if (!$this->voiceFormRepo->checkExistVoiceFormsWithCategoryID($formId, $categoryId)) {
            throw new Exception\NotFoundException;
        }

        $this->disableAutoRender();
        if ($this->request->is('GET')) {
            return false;
        }
        $delete_data = $this->voiceUserFormDataRepo->findById($id);
        if (!empty($delete_data['id'])) {
            $this->voiceUserFormDataRepo->destroy($id);
        }
        $this->redirect('/successful_candidates_control?formId=' . $formId);
    }
}
