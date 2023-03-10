<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\CriteoCourses\CriteoCourseRepository;
use App\Repositories\Criteos\CriteoRepository;
use Cake\Log\Log;
use Exception;

/**
 * Criteos Controller
 *
 * @method \App\Model\Entity\Criteo[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CriteosController extends AppController
{
    public $name = 'Criteos';
    public $uses = array('Criteo', 'User', 'CriteoCourse');
    public $componets = array('Auth', 'Session');

    const FILE_NAME = 'tacschooljpcriteo.csv';
    //const FILE_NAME = 'tacschooljpcriteo_test.csv';
    const OUTPUT_DIR = '/var/www/tac_admin/tmp/csv/'; //TMP Path
    // const OUTPUT_DIR = '/home/publis/portal/htdocs/criteo/';
    //const OUTPUT_DIR = '/var/www/html/cakephp/app/tmp/csv/';

    const ALL = 9999;
    const CSV_UPLOAD = '/var/www/tac_admin/tmp/csv/'; //TMP Path
    // const CSV_UPLOAD = '/var/www/cakephp/app/tmp/csv/';
    //const CSV_UPLOAD = '/var/www/html/cakephp/app/tmp/csv/';
    const CSV_RECORD = 13;
    const EXPORT_CSV_HEADER = '講座ID,id,name,producturl,bigimage,description,price,retailprice,recommendable,coop_flg,page_class,extra_atp,delete_flg';
    const CRITEO_DOWNLOAD_FILE_NAME = 'test.csv';
    const CHUNK = 11;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel("Criteo");
        $this->loadModel("CriteoCourse");
        $this->criteoRepository = new CriteoRepository($this->Criteo);
        $this->criteoCourseRepository = new CriteoCourseRepository($this->CriteoCourse);

        if (!empty($this->request->getData('CriteoEditForm')['courseid']) &&  $this->request->getData('CriteoEditForm')['courseid'] != self::ALL) {
            $courseid = $this->request->getData('CriteoEditForm')['courseid'];
        }
        // For new registration
        else if (!empty($this->request->getData('CriteoRegistForm')['courseid']) &&  $this->request->getData('CriteoRegistForm')['courseid'] != self::ALL) {
            $courseid = $this->request->getData('CriteoRegistForm')['courseid'];
        }
        // CSV export
        else if (!empty($this->request->getData('CriteoCsvExportForm')['courseid']) &&  $this->request->getData('CriteoCsvExportForm')['courseid'] != self::ALL) {
            $courseid = $this->request->getData('CriteoCsvExportForm')['courseid'];
        }
        // CSV import
        else if (!empty($this->request->getData('CriteoImportForm')['courseid']) &&  $this->request->getData('CriteoImportForm')['courseid'] != self::ALL) {
            $courseid = $this->request->getData('CriteoImportForm')['courseid'];
        }
        // 検索
        else if (!empty($this->request->getData('CriteoSeachForm')['courseid']) &&  $this->request->getData('CriteoSeachForm')['courseid'] != self::ALL) {
            $courseid = $this->request->getData('CriteoSeachForm')['courseid'];
        } else if (!empty($this->request->getData('CriteoAllUpdateForm')['courseid']) &&  $this->request->getData('CriteoAllUpdateForm')['courseid'] != self::ALL) {
            $courseid = $this->request->getData('CriteoAllUpdateForm')['courseid'];
        } else {
            $courseid = self::ALL;
        }
        $tagList = $this->listTags($courseid)->toArray();

        // Create select box value
        $sort['sort_cd'] = 'ASC';
        $courseList = $this->criteoCourseRepository->getAllList($sort)->toArray();
        $selectCourseList = $courseList;
        // add all
        $courseList[self::ALL] = '全て';
        $this->set('selectCourseList', $selectCourseList);
        $this->set('courseList', $courseList);
        $this->set('tagList', $tagList);
        $this->set('courseid', $courseid);
    }

    public function index()
    {
    }

    /**
     * Register a new Criteo tag
     **/
    public function registTags()
    {
        $registInfo = array();
        $registInfo['Criteo'] = $this->request->getData('CriteoRegistForm');
        $courseid = $registInfo['Criteo']['courseid'];
        $id = $registInfo['Criteo']['id'];
        // validation
        $defaultValidator = $this->Criteo->getValidator('default');
        $validate = $defaultValidator->validate($registInfo['Criteo']);
        if (!$validate) { // empty
            // Check if page ID is already registered
            $pageIdCheck = $this->criteoRepository->checkPageId($id);

            if (!$pageIdCheck) {
                $this->Flash->success(__d('messages', 'PAGE_ID_EXISTS'));
                $this->render('index');
                return;
            }

            // Register course information in answer_bulletin_criteo.
            $this->criteoRepository->registCriteo($registInfo);

            $this->Flash->set(__d('messages', 'NEW_REGISTRATION_COMPLETED'));

            $tagList = $this->listTags($courseid)->toArray();
            $this->set('tagList', $tagList);
            $this->redirect(array('action' => 'index'));
        } else {
            $error = array();
            $errMess = '';
            $error = $validate;
            foreach ($error as $errKey => $errVal) {
                $messageError = "";
                foreach ($errVal as $message) {
                    $messageError = $message;
                    break;
                }
                $errMess .= $errKey . "::" . $messageError . "<br />";
            }
            $this->Flash->error(sprintf($errMess), ['escape' => false]);
            $this->render('index');
        }
    }

    /**
     * List Criteo tags
     **/
    private function listTags($courseid)
    {
        // List tags
        $tagList = array();
        $tagList = $this->criteoRepository->getListCriteo($courseid);

        return $tagList;
    }

    /**
     * Criteo tag update
     **/
    public function update()
    {
        $action = $this->request->getData('data')['CriteoEditForm']['action'];
        if (in_array($action, array('update', 'delete'))) {
            if ($action === 'update') {
                $this->editTags($this->request->getData('CriteoEditForm'));
            } else {
                $this->deleteTags($this->request->getData('CriteoEditForm'));
            }
        }
    }

    /**
     * Edit Criteo tags
     **/
    public function editTags($params)
    {
        $editTag = $params;
        $validateInfo = array();
        $validateInfo['Criteo'] = $editTag;
        try {
            // Remove courseid as it is irrelevant to edit validation
            unset($validateInfo['Criteo']['courseid']);
            // validation
            $defaultValidator = $this->Criteo->getValidator('default');
            $validate = $defaultValidator->validate($validateInfo['Criteo']);
            if ($validate) { // not empty
                throw new Exception(__d('messages', 'UPDATE_FAILED'));
            }

            $result = $this->criteoRepository->updateTag($editTag);

            $this->Flash->success(__d('messages', 'UPDATE_COMPLETED'));
            $tagList = $this->listTags($editTag['courseid'])->toArray();
            $this->set('tagList', $tagList);

            $this->render('index');
        } catch (Exception $e) {
            $error = array();
            $errMess = '';
            $error = $validate;

            foreach ($error as $errKey => $errVal) {
                $messageError = "";
                foreach ($errVal as $message) {
                    $messageError = $message;
                    break;
                }
                $errMess .= $errKey . '::' . $messageError . '<br />';
            }
            $this->set('postData',  $editTag);
            $this->Flash->error(sprintf($errMess), ['escape' => false]);
            // redirect to index
            $this->render('index');
        }
    }

    /**
     * Delete Criteo feed
     **/
    public function deleteTags($params)
    {
        try {
            $deleteInfo['Criteo']['id'] = $params['id'];

            $this->criteoRepository->destroy($deleteInfo['Criteo']['id']);

            $tagList = $this->listTags($params['courseid'])->toArray();

            $this->set('tagList', $tagList);
            $this->Flash->success(__d('messages', 'DELETE_COMPLETED'));
            $this->render('index');
        } catch (Exception $e) {
            $this->Flash->error(__d('messages', 'DELETE_FAILED'));
            // indexにリダイレクト
            $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * CSV import
     **/
    public function importCsv()
    {
        $file = $this->request->getData('CriteoImportForm')['result'];
        $fileName = self::CSV_UPLOAD . $file->getClientFilename();
        $tmp_name = $_FILES['CriteoImportForm']['tmp_name']['result'];

        $result = false;
        if (is_uploaded_file($tmp_name)) {
            move_uploaded_file($tmp_name, $fileName);
            $result = $this->loadCsvData($fileName);
        }

        $tagList = $this->listTags($this->request->getData('CriteoImportForm')['courseid'])->toArray();
        $this->set('tagList', $tagList);
        // Registration result
        if ($result) {
            $this->Flash->success(__d('messages', 'CSV_IMPORT_COMPLETED'));
        } else {
            if (empty($_SESSION['Flash']['flash'])) {
                $this->Flash->error(__d('messages', 'FAILED_TO_IMPORT_CSV'));
            } else {
                return $this->redirect(array('action' => 'index'));
            }
        }
        $this->render('index');
    }

    /**
     * Import CSV data into database
     * @params $fileName CSV file path
     * return boolean
     **/
    private function loadCsvData($fileName)
    {
        // transaction start
        try {
            $lineNo = 1;  // CSV line number (including header)

            // Read CSV data
            $csvData = file($fileName, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            // Repeated import for CSV data
            foreach ($csvData as $line) {
                $record = mb_convert_variables("UTF-8", "SJIS", $line);
                $record = explode(",", $line);

                if (count($record) != self::CSV_RECORD) {
                    // not enough
                    throw new Exception("{$lineNo}行: CSV項目が足りていない。");
                }

                // Skip further processing for header row
                if ($lineNo == 1) {
                    $lineNo++;
                    continue;
                }

                // Delete the records with the delete flag set, and proceed to read the next row
                if ($record[12] == 1) {
                    $this->criteoRepository->destroy($record[1]);
                    continue;
                }

                // extra_atp conversion
                if (!empty($record[11])) {
                    $record[11] = $this->criteoRepository->trimExtra_atp($record[11]);
                }

                $data['Criteo'] = array(
                    'courseid' => $record[0],
                    'id'  => $record[1],
                    'name' => $record[2],
                    'url' => $record[3],
                    'bigimage' => $record[4],
                    'description' => $record[5],
                    'price' => $record[6],
                    'retailprice' => $record[7],
                    'recommendable' => $record[8],
                    'cooperation_flag' => $record[9],
                    'page_type' => $record[10],
                    'extra_atp' => $record[11],
                );

                $conditions = array('courseid' => $data['Criteo']['courseid']);
                $courseidResult = $this->criteoCourseRepository->getByCourseId($conditions);

                if (empty($courseidResult)) {
                    // Exit if course ID does not exist
                    throw new Exception("{$lineNo}行: 講座IDが存在しません。");
                }

                // validation
                $defaultValidator = $this->Criteo->getValidator('default');
                $validate = $defaultValidator->validate($data['Criteo']);
                if (!$validate) {
                    // register
                    $this->criteoRepository->save($data['Criteo']);
                } else {
                    LOG::write('debug', print_r($validate, true));

                    $errMessageList = array();
                    foreach ($validate as $item) {
                        foreach ($item as $error) {
                            $errMessageList[] = $error;
                        }
                    }
                    $errMessage = implode(',', $errMessageList);
                    throw new Exception("{$lineNo}行: {$errMessage}(更新失敗)");
                }

                $lineNo++;
            }
            return true;
        } catch (Exception $e) {
            //roll back
            $this->Flash->error(sprintf($e->getMessage()), ['escape' => false]);
            return false;
        }
    }

    /**
     * CSV transfer
     **/
    public function exportCsv()
    {
        // Get a list from the CRITEO table if it exists
        $criteoList = array();
        $conditions = array('cooperation_flag' => '1');
        $fields = array(
            'id',
            'name',
            'url',
            'bigimage',
            'courseid',
            'description',
            'price',
            'retailprice',
            'recommendable',
            'extra_atp'
        );
        $criteoList = $this->criteoRepository->getListCriteoByCooperation($conditions, $fields)->toArray();

        // Get course list
        $courseList = $this->criteoCourseRepository->getAllList()->toArray();

        try {
            // CSV file creation
            $fileName = self::OUTPUT_DIR . self::FILE_NAME;

            $fp = fopen($fileName, 'w');
            // create header line
            $header = "id,name,producturl,bigimage,categoryid1,description,price,retailprice,recommendable,extra_atp";
            fwrite($fp, $header . "\n");

            // Write the contents of the CRITEO table
            foreach ($criteoList as $criteo) {
                $criteo['courseid'] = $courseList[$criteo['courseid']];
                $criteo['bigimage'] = 'http://ebook.tac-school.co.jp/criteo/' . $criteo['bigimage'];
                $data = implode(',', $criteo) . "\n";

                fwrite($fp, $data);
            }

            // end of writing
            fclose($fp);

            $this->Flash->success(__d('messages', 'CSV_TRANSFER_COMPLETED'));
            $this->render('index');
        } catch (Exception $e) {
            $this->Flash->error(__d('messages', 'CSV_TRANSFER_FAILED'));
            $this->render('index');
        }
    }

    /**
     * CRITEO search
     **/
    public function searchCategory()
    {
        $courseid = $this->request->getData('CriteoSeachForm')['courseid'];

        // in all cases
        if ($courseid == self::ALL) {
            $tagList = $this->criteoRepository->getListCriteo(self::ALL)->toArray();
            $this->set('tagList', $tagList);
            $this->render('index');
            return;
        }

        $conditions = array('courseid' => $courseid);

        $result = $this->criteoCourseRepository->getByCourseId($conditions);

        if (is_null($result)) {
            $this->Flash->error(__d('messages', 'TARGET_COURSE_IS_NOT_EXIST'));
            return $this->render('index');
        }
        $tagList = $this->criteoRepository->getListCriteo($courseid)->toArray();
        $this->set('tagList', $tagList);
        return $this->render('index');
    }

    /**
     * CSV download
     * Output in the same way as items used for CSV import
     **/
    public function downloadCsv()
    {
        // Get Course ID
        $courseid = $this->request->getData('CriteoDownloadForm')['courseid'];

        $tagList = $this->criteoRepository->getListCriteo($courseid)->toArray();

        // CSV output
        try {
            $fileName = self::CRITEO_DOWNLOAD_FILE_NAME;
            $fp = fopen($fileName, 'w');
            $header = self::EXPORT_CSV_HEADER;
            $record = mb_convert_encoding($header, 'SJIS', 'UTF-8');
            fwrite($fp, $record . "\n");

            foreach ($tagList as $key => $val) {
                $record = '';
                $output = '';
                $output =
                    $val['courseid'] . ',' .
                    $val['id'] . ',' .
                    $val['name'] . ',' .
                    $val['url'] . ',' .
                    $val['bigimage'] . ',' .
                    $val['description'] . ',' .
                    $val['price'] . ',' .
                    $val['retailprice'] . ',' .
                    $val['recommendable'] . ',' .
                    $val['cooperation_flag'] . ',' .
                    $val['page_type'] . ',' .
                    $val['extra_atp'] . ',' .
                    '0';

                $record = mb_convert_encoding($output, 'SJIS', 'UTF-8');
                fwrite($fp, $record . "\n");
            }

            fclose($fp);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=criteo.csv');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($fileName));
            readfile($fileName);
            exit();
        } catch (Exception $e) {
            $this->Flash->error(__d('messages', 'CSV_OUTPUT_FAILED'));
            $this->render('index');
        }
    }

    /**
     * Bulk update
     *
     **/
    public function allUpdate()
    {
        $data = $this->request->getData('CriteoAllUpdateForm')['data'];
        $updateArray = array();
        $chunkData = array();
        $saveData = array();
        $errMessageList = array();

        $updateArray = explode(',', $data);
        try {
            if (empty($data)) {
                // Cannot update if 0
                throw new Exception(__d('messages', 'THERE_IS_NO_UPDATE_TARGET'));
            }

            // Split array by 11 records
            $chunkData = array_chunk($updateArray, self::CHUNK);

            Log::write('debug', print_r($chunkData, true));

            // Formatting data for bulk updates
            foreach ($chunkData as $key => $val) {
                // update array
                $saveData['Criteo'][$key]['courseid']         = $val[0];
                $saveData['Criteo'][$key]['id']               = $val[1];
                $saveData['Criteo'][$key]['name']             = $val[2];
                $saveData['Criteo'][$key]['description']      = $val[3];
                $saveData['Criteo'][$key]['url']              = $val[4];
                $saveData['Criteo'][$key]['extra_atp']        = $val[5];
                $saveData['Criteo'][$key]['bigimage']         = $val[6];
                $saveData['Criteo'][$key]['page_type']        = $val[7];
                $saveData['Criteo'][$key]['recommendable']    = $val[8];
                $saveData['Criteo'][$key]['price']            = $val[9];
                $saveData['Criteo'][$key]['cooperation_flag'] = $val[10];

                // array for screen
                $setData[$key]['courseid']         = $val[0];
                $setData[$key]['id']               = $val[1];
                $setData[$key]['name']             = $val[2];
                $setData[$key]['description']      = $val[3];
                $setData[$key]['url']              = $val[4];
                $setData[$key]['extra_atp']        = $val[5];
                $setData[$key]['bigimage']         = $val[6];
                $setData[$key]['page_type']        = $val[7];
                $setData[$key]['recommendable']    = $val[8];
                $setData[$key]['price']            = $val[9];
                $setData[$key]['cooperation_flag'] = $val[10];
            }
            $validate = $this->Criteo->validateMany($saveData['Criteo']);
            $this->set('tagList', $setData);
            $error = $validate;
            if (!empty($error)) {
                foreach ($error as $errKey => $errArr) {
                    $errKey = $errKey + 1;
                    foreach ($errArr as $errVal) {
                        $messageError = "";
                        foreach ($errVal as $message) {
                            $messageError = $message;
                            break;
                        }
                        $errMessageList[] = $errKey . '行目' . $messageError;
                        Log::write('debug', print_r($errMessageList, true));
                    }
                }
                $errMessage = implode('<br />', $errMessageList);
                throw new Exception($errMessage);
            }
            $this->criteoRepository->saveAll($this, $saveData['Criteo']);
            $this->Flash->success(__d('messages', 'UPDATE_COMPLETED'));
            $this->render('index');
        } catch (Exception $e) {
            Log::write('debug', print_r($e, true));
            $this->Flash->error(sprintf($e->getMessage()), ['escape' => false]);
            $this->render('index');
        }
    }
}
