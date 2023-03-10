<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repositories\Files\FilesRepository;
use App\Repositories\Files\FileCacheRepository;
use Cake\Event\EventInterface;
use App\Traits\dateMiscTrait;
use App\Traits\lib_utilityTrait;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\NotFoundException;
use Exception;

/**
 * FilesList Controller
 *
 * @method \App\Model\Entity\FilesList[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FilesListController extends AppController
{
    use dateMiscTrait;
    use lib_utilityTrait;


    public function initialize(): void
    {
        parent::initialize();

        // load models
        $this->Files = $this->fetchTable('Files');

        // define repo
        $this->filesRepo = new FilesRepository($this->Files);
        $this->fileCacheRepo = new FileCacheRepository($this->Files);
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $this->genFormControls();
        $this->viewBuilder()->setLayout('custom');
    }

    /**
     * Gen default data: selection options
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function genFormControls() {
        // Registered month list (start)
        $ym = date('Y-m', strtotime(date('Y-m-1') . ' -6 month'));          // n months ago
        $ym_list = $this->genYearMonthList($ym, 10);                        // Display for m months
        $ym_list_from = array(0 => __d('files_list', 'REGISTED_DATE_START')) + $ym_list;

        // Registration month list (end)
        $ym_list_to = array(0 => __d('files_list', 'REGISTED_DATE_END')) + $ym_list;

        $this->set(compact('ym_list_from', 'ym_list_to'));
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        try {
            $this->setSessionParameter();
            $files_date_from = $this->request->getSession()->read('files_date_from');
            $files_date_to = $this->request->getSession()->read('files_date_to');
            $conditions = [
                'created_from' => ($files_date_from == 0) ? null : $this->getFirstDayOfMonth($files_date_from),
                'created_to' => ($files_date_to == 0) ? null : $this->getLastDayOfMonth($files_date_to)
            ];

            $num_records = $this->filesRepo->getDataCountByCreatedDate($conditions);

            if ($num_records === false) {
                throw new \Exception(__d('files_list', 'ERROR_DATABASE_GET_INFORMATION'));
            }

            // Pagination
            $limit = PER_PAGE;           // Number of data displayed on one page
            $lastPage = (int) ceil($num_records / $limit);
            $page = $this->request->getQuery('page', 1);
            // Calculate page number
            if ($lastPage < $page) {
                $this->request = $this->request->withQueryParams(['page' => $lastPage]);
            }

            $this->paginate = [
                'Files' => [
                    // 'fields' => '*',
                    'limit' => $limit,
                    // 'order' => array('created' => 'ASC'),
                    'conditions' => []
                ]
            ];
            $records = $this->filesRepo->getDataByCreatedDate($conditions);

            if ($records === false) {
                throw new \Exception(__d('files_list', 'ERROR_DATABASE_GET_INFORMATION'));
            }
            $records = $this->paginate($records);
            $table_body = $this->makeTableTrFilesList($records->toArray(), true, 'ids[]');

            $this->set('title_head', __d('files_list', 'TITLE_HEAD'));
            $this->set(compact('records', 'num_records', 'table_body', 'files_date_from', 'files_date_to'));
        } catch (\Exception $e) {
            $this->set(['error_messages' => [$e->getMessage()]]);
            $this->set('title_head', __d('files_list', 'TITLE_HEAD_ERROR'));
            $this->render('empty');
            return;
        }
    }

    /**
     * Create a table's body
     *
     * @return String Table body
     */
    public function makeTableTrFilesList($records, $with_checkbox = false, $checkbox_name = 'ids[]') {

        $cnt = count($records);
        $tag = '';
        for ($i = 0; $i < $cnt; $i++) {
            // Sort order
            $r = $records[$i];

            $id = $r['id'];
            $file_size = $r['file_size'];
            $file_path = $r['file_path'];
            $file_name = basename($r['file_path']);
            $pdf_url = "http://$_SERVER[HTTP_HOST]/tacmap/pdf/";
            // replace for test - undo when deploy
            // $file_url = 'https://www.tac-school.co.jp/tacmap/pdf/' . $file_name;path_to_url($file_path);
            $file_url = $pdf_url . $file_name;path_to_url($file_path);
            $file_comment = $r['file_comment'];
            $created = substr($r['created']->format('Y-m-d H:i:s'), 0, strlen($r['created']->format('Y-m-d H:i:s')) - 3);

            $file_name_line = '<td title="' . $file_url . '"><a href="' . $file_url . '" target="_blank">' . cleanTags($file_name) . '</a></td>';

            $id_field = cleanTags($id);
            $file_comment_short = mb_strimwidth($file_comment, 0, ADMIN_FILES_ORG_NAME_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $file_comment_line = '<td title="' . cleanTags($file_comment) . '">' . cleanTags($file_comment_short) . '</a></td>';

            list($s, $u) = num2HFormat($file_size, 1);
            $file_size_field = "$s $u";
            $file_size_line =  '<td title="' . cleanTags($file_size) . '">' . cleanTags($file_size_field) . '</a></td>';

            $chekcbox_line = '';
            if ($with_checkbox) {
                $chekcbox_line = "<td><input type=\"checkbox\" name=\"{$checkbox_name}\" value=\"{$id}\"/></td>";
            }

            $tr_class = ($i % 2) ? 'even' : 'odd';
            $tag .= "<tr class=\"{$tr_class}\">"
                .  $chekcbox_line
                .  "<td>{$id_field}</td>"
                .  $file_name_line
                .  $file_comment_line
                .  $file_size_line
                .  '<td>' . cleanTags($created). '</td>'
                .  '<td style="text-align:left">' . cleanTags($file_url). '</td>'
                .  '</tr>';
        }
        return $tag;
    }

    /**
     * Files change records
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function filesChangeRecords() {
        try {
            $postedData = $this->request->getData();
            $validator = $this->Files->getValidator('default');
            $errors = $validator->validate($postedData);

            if (empty($errors)) {
                throw new Exception();
            }

            $ids = $this->request->getData('ids') ?? [];
            $this->request->getSession()->write('FilesModel', $ids);

            if (!is_null($this->request->getData('delete'))) {
                $this->filesChangeRecordsDeleteConfirm();
            } else {
                throw new Exception();
            }
        } catch (\Exception $e) {
            $this->set('title_head', __d('files_list', 'TITLE_HEAD_ERROR'));
            $this->render('empty');
            return;
        }
    }

    /**
     * Confirm files change records delete
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function filesChangeRecordsDeleteConfirm() {
        try {

            $this->request->getSession()->write('is_reload_files_list', false);

            $ids = $this->request->getSession()->read('FilesModel');
            $table_body = '';
            $records = $this->filesRepo->getDataSetById($ids);
            if ($records === false) {
                throw new Exception(__d('files_list', 'ERROR_DATABASE_GET_INFORMATION'));
            }
            $table_body = $this->makeTableTrFilesList($records, false);

            $this->set(compact('table_body'));
            $this->set('title_head', __d('files_list', 'TITLE_HEAD_FILES_CHANGE_RECORDS_DELETE_CONFIRM'));
            $this->render('files_change_records_delete_confirm');

        } catch (\Exception $e) {
            $this->set(['error_messages' => [$e->getMessage()]]);
            $this->set('title_head', __d('files_list', 'TITLE_HEAD_ERROR'));
            $this->render('empty');
            return;
        }
    }

    /**
     * Finish files change records delete
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function filesChangeRecordsDeleteFinish() {
        try {

            // Exit without doing anything when reloading
            if ($this->request->getSession()->read('is_reload_files_list')) {
                $this->set('title_head', __d('files_list', 'TITLE_HEAD_FILES_CHANGE_RECORDS_DELETE_FINISH'));
                $this->render('files_change_records_delete_finish');
                return;
            }

            $ids = explode(' ', $this->request->getData('ids') ?? '');
            $records = $this->filesRepo->getDataSetById($ids);

            if ($records === false) {
                throw new Exception(__d('files_list', 'ERROR_DATABASE_GET_DELETED_FILE'));
            }

            // Unlink the files
            $file_results = array();
            foreach($records as $file_info) {
                if (isset($file_info['file_path'])) {
                    // customzie file path
                    $path = 'pdf/' . $file_info['file_path'];
                    $r = @unlink($path);
                }
                $file_results[] = array($path, $this->bool2str($r));
            }

            // Delete records by IDs
            $conditions = [
                'id IN' => $ids
            ];
            $result = $this->filesRepo->destroyByCondition($conditions);

            if (!$result) {
                throw new Exception(__d('files_list', 'ERROR_DATABASE_DELETED_FILE'));
            }
            $this->request->getSession()->write('is_reload_files_list', true);
            $this->request->getSession()->delete('FilesModel');
            // $this->fileCacheRepo->destroyCache();
            $this->set('title_head', __d('files_list', 'TITLE_HEAD_FILES_CHANGE_RECORDS_DELETE_FINISH'));
            $this->render('files_change_records_delete_finish');

        } catch (\Exception $e) {
            $this->set(['error_messages' => [$e->getMessage()]]);
            $this->set('title_head', __d('files_list', 'TITLE_HEAD_ERROR'));
            $this->render('empty');
            return;
        }
    }

    /**
     * Write session data by request parameters
     *
     * @return void
     */
    public function setSessionParameter() {
        $datas = $this->request->getData();
        foreach ($datas as $key => $value) {
            $this->request->getSession()->write($key, $value);
        }
    }
}
