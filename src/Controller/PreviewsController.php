<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\Schools\SchoolRepository;
use Cake\Event\EventInterface;
use Exception;
use Throwable;
/**
 * Previews Controller
 *
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PreviewsController extends AppController
{
    public $errorMsg = [];

    public function initialize(): void
    {
        parent::initialize();

        $this->Schools = $this->fetchTable('Schools');

        $this->schoolRepo = new SchoolRepository($this->Schools);

        // Get school's informations
        $this->exec();
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
        if (!empty($this->errorMsg)) {
            $this->set('title_head', __d('preview', 'TITLE_HEAD_ERROR'));
            $this->viewBuilder()->setLayout('error');
        } else {
            $this->viewBuilder()->setLayout('preview');
        }
    }

    /**
     * Prepare data
     * @param void
     *
     * @return string | array
     */
    public function exec() {
        try {
            $this->request->getSession()->delete('school_list');

            $vo = $this->schoolRepo->getSchoolInfo();
            if (false === $vo) {
                throw new \Exception(__d('preview', 'DATABASE_ERROR_FAILED_GET_INFORMATION'));
            }

            $this->request->getSession()->write('school_list', $vo);
            return $vo;
        } catch (Exception $e) {
            array_push($this->errorMsg, $e->getMessage());
            return 'error';
        }
    }

    /**
     * Index method
     * @param
     *
     * @return void
     */
    public function index() {
        $school_list = $this->request->getSession()->read('school_list', []);
        if (!empty($this->errorMsg)) {
            $this->set(['message' => $this->errorMsg]);
            return;
        }
        $this->set(compact('school_list'));
    }

}
