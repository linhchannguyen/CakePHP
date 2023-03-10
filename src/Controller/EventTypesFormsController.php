<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repositories\ActiveEventTypes\ActiveEventTypeRepository;
use App\Repositories\EventTypes\EventTypeRepository;
use App\Repositories\Kouzas\KouzaRepository;
use Cake\Event\EventInterface;
use PDOException;


/**
 * EventTypesForms Controller
 *
 * @property \App\Model\Table\ActiveEventTypesTable $ActiveEventTypes
 * @method \App\Model\Entity\ActiveEventType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EventTypesFormsController extends AppController
{
    // Define variables
    public $m_types_info = [];

    public function initialize(): void
    {
        parent::initialize();

        $this->ActiveEventTypes = $this->fetchTable('ActiveEventTypes');
        $this->EventTypes = $this->fetchTable('EventTypes');
        $this->Kouzas = $this->fetchTable('Kouzas');

        $this->activeEventTypeRepo = new ActiveEventTypeRepository($this->ActiveEventTypes);
        $this->eventTypeRepo = new EventTypeRepository($this->EventTypes);
        $this->kouzaRepo = new KouzaRepository($this->Kouzas);

        $this->prepare();
    }

    public function beforeRender(EventInterface $event)
    {
        $this->viewBuilder()->setLayout('custom');
    }

    public function prepare() {
        $event_types = [];
        try {
            $conditions = [
                'is_active' => true
            ];
            $fields = ['id', 'icon', 'order_no', 'event_type_name', 'inner_class', 'content'];
            $orderBy = ['order_no', 'id'];

            $event_types = $this->eventTypeRepo->getByConditionsOrderBy($conditions, $fields, $orderBy);
        } catch (\Exception $e) { // use exception to catch exceptions
            $event_types = [];
        }

        for ($i = 0; $i < count($event_types); $i++) {
            $this->m_types_info[$i+1] = array(
                'id' => $i + 1,
                'icon' => $event_types[$i]['icon'],
                'order_no' => $event_types[$i]['order_no'],
                'event_type_name' => $event_types[$i]['event_type_name'],
            );
        }
    }

    public function escHtml($str) {
        return htmlspecialchars((string) $str, ENT_QUOTES);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        try {
        // Method POST
            $post_ids = [];
            $this->set('title_head', __d('event_types_form', 'TITLE_HEAD'));

            if ($this->request->isPost()) {
                header("Cache-Control: no cache");
                try {
                    // Delete all current active event types records
                    $this->activeEventTypeRepo->deleteAllByConditions();
                } catch (PDOException $e) {
                    throw new \Exception();
                }

                // Insert new event types records
                // Save new POST value data and continue to turn on the checkbox
                foreach ($_POST as $key => $value) {
                    if (preg_match('/active_([0-9]+)_([0-9]+)/', $key, $matches)) {
                        try {
                            $post_ids[] = $key;
                            $param = [
                                'kouza_id' => $matches[1],
                                'event_type' => $matches[2]
                            ];
                            $this->activeEventTypeRepo->create($param);
                        } catch (PDOException $e) {
                            throw new \Exception();
                        }
                    }
                }

            } else {
                try {
                    $active_list = $this->activeEventTypeRepo->getAll();
                } catch (PDOException $e) {
                    $active_list = [];
                }

                foreach ($active_list as $active) {
                    $post_ids[] = 'active_'.$active['kouza_id'].'_'.$active['event_type'];
                }

            }
            $this->set(compact('post_ids'));

            $new_list = [];
            foreach ($this->m_types_info as $info) {
                $new_list[] = array('id' => $info['id'], 'name' => $info['event_type_name']);
            }
            $event_type_list = $new_list;

            // Get list of kouza
            $kouza_list = [];
            try {
                $conditions = [
                    'is_active' => true
                ];
                $fields = ['id', 'kouza_name'];
                $orderBy = ['order_no', 'id'];
                $kouza_list = $this->kouzaRepo->getByConditionsOrderBy($conditions, $fields, $orderBy);
            } catch (PDOException $e) {
                $kouza_list = [];
            }

            // Filtering before output
            $new_list = [];
            foreach ($kouza_list as $kouza) {
                $kouza = array_map('self::escHtml', $kouza->toArray());
                $kouza = array_map('nl2br', $kouza);
                $new_list[] = $kouza;
            }
            $kouza_list = $new_list;

            // Filtering before output
            $new_list = [];
            foreach ($event_type_list as $event_type) {
                $event_type = array_map('self::escHtml', $event_type);
                $event_type = array_map('nl2br', $event_type);
                $new_list[] = $event_type;
            }
            $event_type_list = $new_list;

            $this->set(compact('event_type_list', 'kouza_list'));
        } catch (\Exception $e) {
            $error_messages = [__d('event_types_form', 'DATABASE_ERROR_FAILED_GET_INFORMATION')];
            $this->set('title_head', __d('event_types_form', 'PAGE_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }
    }
}
