<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\Files\FilesRepository;
use Cake\Core\Configure;
use Exception;

/**
 * UploadFiles Controller
 *
 * @method \App\Model\Entity\UploadFile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UploadFilesController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        // load models
        $this->Files = $this->fetchTable('Files');

        // define repo
        $this->filesRepo = new FilesRepository($this->Files);
    }

    /**
     * Upload pdf files method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function pdfUpload()
    {
        if ($this->request->isPost()) {
            for ($i = 0; $i < 20; $i++) {
                $key = 'pdf' . $i;

                if ($_FILES[$key]['error'] == UPLOAD_ERR_OK) {

                    $tmp_name = $_FILES[$key]['tmp_name'];
                    $uploads_dir = Configure::read('App.pdfBaseUrl');
                    $name = $_FILES[$key]['name'];
                    $uniqid = uniqid();
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $dest = "$uploads_dir/$uniqid.$ext";

                    $result = move_uploaded_file($tmp_name, $dest);

                    if ($result) {
                        try {
                            $args = [
                                'file_path' => "../pdf/$uniqid.$ext",
                                'file_size' => $_FILES[$key]['size'],
                                'file_comment' => $_FILES[$key]['name']
                            ];
                            $this->filesRepo->insertDB($args);
                        } catch (Exception $e) {
                            return $this->redirect('/files_list/index');
                        }
                    }
                }
            }
        }

        return $this->redirect('/files_list/index');
    }
}
