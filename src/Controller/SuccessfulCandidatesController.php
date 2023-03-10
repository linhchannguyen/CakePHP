<?php
declare(strict_types=1);

namespace App\Controller;

use Authentication\AuthenticationService;
use App\Repositories\VoiceCategories\VoiceCategoryRepository;
use App\Repositories\VoiceUsers\VoiceUserRepository;
use App\Repositories\VoiceForms\VoiceFormRepository;
use App\Repositories\VoiceRoles\VoiceRoleRepository;
use App\Repositories\VoiceParts\VoicePartRepository;
use App\Repositories\VoiceReleases\VoiceReleaseRepository;
use App\Repositories\VoiceZeirishiLists\VoiceZeirishiListRepository;
use App\Repositories\VoiceZeirishiKamokuLists\VoiceZeirishiKamokuListRepository;
use App\Repositories\VoiceJyukentikuLists\VoiceJyukentikuListRepository;
use App\Repositories\VoiceHeads\VoiceHeadRepository;
use App\Repositories\Category\CategoryRepository;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Event\EventInterface;
use Cake\Http\Exception;
use Cake\Http\Exception\InternalErrorException;

/**
 * SuccessfulCandidates Controller
 *
 * @method \App\Model\Entity\SuccessfulCandidate[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SuccessfulCandidatesController extends AppController
{
    use LocatorAwareTrait;
    /**
     * Define repositories
     *
     */
    private $voiceCategoryRepository;

    /**
     * Global variables
     */
    private $currentRole;

    public function initialize(): void
    {
        parent::initialize();

        // Configure flash messages
        $this->Flash->setConfig('clear', true);

        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');

        // load models
        $this->VoiceCategories = $this->fetchTable('VoiceCategories');
        $this->VoiceUsers = $this->fetchTable('VoiceUsers');
        $this->VoiceForms = $this->fetchTable('VoiceForms');
        $this->VoiceRoles = $this->fetchTable('VoiceRoles');
        $this->VoiceParts = $this->fetchTable('VoiceParts');
        $this->VoiceReleases = $this->fetchTable('VoiceReleases');
        $this->VoiceZeirishiLists = $this->fetchTable('VoiceZeirishiLists');
        $this->VoiceZeirishiKamokuLists = $this->fetchTable('VoiceZeirishiKamokuLists');
        $this->VoiceJyukentikuLists = $this->fetchTable('VoiceJyukentikuLists');
        $this->VoiceHeads = $this->fetchTable('VoiceHeads');


        // define repo
        $this->voiceCategoryRepo = new VoiceCategoryRepository($this->VoiceCategories);
        $this->voiceUserRepo = new VoiceUserRepository($this->VoiceUsers);
        $this->voiceFormRepo = new VoiceFormRepository($this->VoiceForms);
        $this->voiceRoleRepo = new VoiceRoleRepository($this->VoiceRoles);
        $this->voicePartRepo = new VoicePartRepository($this->VoiceParts);
        $this->voiceReleaseRepo = new VoiceReleaseRepository($this->VoiceReleases);
        $this->voiceZeirishiListRepo = new VoiceZeirishiListRepository($this->VoiceZeirishiLists);
        $this->voiceZeirishiKamokuListRepo = new VoiceZeirishiKamokuListRepository($this->VoiceZeirishiKamokuLists);
        $this->voiceJyukentikuListRepo = new VoiceJyukentikuListRepository($this->VoiceJyukentikuLists);
        $this->voiceHeadRepo = new VoiceHeadRepository($this->VoiceHeads);
    }

    public function beforeFilter(EventInterface $event) {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'logout', 'add']);
        $action = $this->request->getParam('action');
        if (
            !in_array($action, $this->Authentication->getUnauthenticatedActions(), true) &&
            !$this->Authentication->getIdentity()
        ) {
            $this->Flash->error(__d('successful_candidate', 'LOGGED_IN_TO_USED'));
        }
    }

    public function beforeRender(EventInterface $event) {
        // Set the layout.

        $result = $this->request->getAttribute('authentication')->authenticate($this->request);
        if ($result && $result->isValid() && $this->request->getSession()->read('isLoggedIn') == 1) {
            $self = $result->getData();
            $this->Authentication->setIdentity($self);

            // Set your own data if you are already logged in
            $voiceRole = $this->voiceRoleRepo->getRoleByID($self->role_id);
            $role = $voiceRole['id'];
            $this->set(compact('self', 'role'));

            $this->viewBuilder()->setLayout('successful');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function login() {
        $this->Authentication->logout();

        if ( $this->request->getSession()->read('SuccessfulCandidates.reload') === 0) {
            $this->request->getSession()->delete('SuccessfulCandidates');
        } else {
            $this->request->getSession()->write('SuccessfulCandidates.reload', 0);
        }
        $this->request->getSession()->write('isLoggedIn', 0);
        $this->request->getSession()->delete('Auth');
        $this->request->getSession()->delete('categoryId');
        $this->request->allowMethod(['get', 'post']);

        $categoryLists = $this->voiceCategoryRepo->getVoiceCategories();
        $this->set('categoryLists', $categoryLists);

        if ($this->request->is('post')) {
            $this->request->getSession()->delete('SuccessfulCandidates');

            // If the user is logged in send them away.
            $result = $this->request->getAttribute('authentication')->authenticate($this->request);
            if ($result && $result->isValid()) {
                $self = $result->getData();
                $this->Authentication->setIdentity($self);
                $currentRole = $self->role_id;

                $username = $this->request->getData('username');
                $password = $this->request->getData('password');
                if (!empty($this->request->getData('category_id'))) {
                    $categoryId = $this->request->getData('category_id');
                }
                if (empty($categoryId) && $currentRole != HIGHEST) {
                    $this->Flash->error(__d('successful_candidate', 'LOGIN_FAILED'));
                    $this->Authentication->logout();
                    $this->request->getSession()->write('SuccessfulCandidates.password', $password);
                    $this->request->getSession()->write('SuccessfulCandidates.username', $username);
                    $this->request->getSession()->write('SuccessfulCandidates.reload', 1);
                    return $this->redirect('/successful_candidates/login');
                }
                $redirectURL = '/successful_candidates';
                if ($currentRole != HIGHEST) {  // NORMAL, ADMIN

                    // Check username and course_id
                    $voiceUser = $this->voiceUserRepo->getByUsernameCategoryID($username, [$categoryId]);

                    // Store username and password
                    $this->request->getSession()->write('SuccessfulCandidates.password', $password);
                    $this->request->getSession()->write('SuccessfulCandidates.username', $username);
                    $this->request->getSession()->write('SuccessfulCandidates.categoryId', $categoryId);
                    $this->request->getSession()->write('SuccessfulCandidates.reload', 1);

                    if (empty($voiceUser)) {
                        $this->Flash->error(__d('successful_candidate', 'LOGIN_FAILED'));
                        $this->Authentication->logout();
                        return $this->redirect('/successful_candidates/login');
                    }
                    $this->request->getSession()->write('categoryId', $categoryId);
                } else {  // HIGHEST
                    // Check username and course_id
                    if (isset($categoryId)) {
                        $voiceUser = $this->voiceUserRepo->getByUsernameCategoryID($username, [$categoryId, 0]);
                    }
                    if (empty($voiceUser)) {
                        $redirectURL = '/successful_candidates/userLists';
                    } else {
                        $this->request->getSession()->write('categoryId', $categoryId);
                    }
                }
                $this->redirect($redirectURL);
                $this->request->getSession()->write('isLoggedIn', 1);
            }

            if (!$result->isValid()) {
                $this->Flash->error(__d('successful_candidate', 'LOGIN_FAILED'));
                $this->Authentication->logout();
            }
        }
    }

    // Logout process
    public function logout() {
        $this->disableAutoRender();
        $result = $this->Authentication->getResult();
        $this->request->getSession()->delete('categoryId');
        $this->request->getSession()->delete('SuccessfulCandidates');

        if ($result && $result->isValid()) {
            $this->Authentication->logout();
        }
        return $this->redirect('/successful_candidates/login');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $categoryId = $this->request->getSession()->read('categoryId');
        $category = [];
        if (empty($categoryId)) {
            $this->Authentication->logout();
            $voiceForms = [];
            throw new Exception\NotFoundException;
        } else {
            $category = $this->voiceCategoryRepo->getVoiceCategoryByID($categoryId);
            $voiceForms = $this->voiceFormRepo->getVoiceFormsByCategoryID($categoryId);
        }

        $this->set(compact('categoryId', 'category', 'voiceForms'));
    }

    /**
     * Edit form
     *
     * @return \Cake\Http\Response|null|void Renders edit_form
     */
    public function editForm() {

        $categoryId = $this->request->getSession()->read('categoryId');
        $formId = $this->request->getQuery('formId');

        if (empty($categoryId)) {
            throw new Exception\NotFoundException;
        } else if (!empty($formId) && !$this->voiceFormRepo->checkExistVoiceFormsWithCategoryID($formId, $categoryId)) { // Check authorization
            throw new Exception\NotFoundException;
        }

        // Define variable
        $voiceForm = [
            'id' => null,
            'name' => null,
            'category_id' => null,
            'lock' => null,
            'show_people' => null,
            'send_mail' => null
        ];

        $category = $this->voiceCategoryRepo->getVoiceCategoryByID($categoryId);
        if ($this->request->is('post') && $this->request->getData()) {
            // save data
            $postedData = $this->request->getData();
            $validator = $this->VoiceForms->getValidator('default');
            $errors = $validator->validate($postedData);

            if (empty($errors)) {
                if ($this->voiceFormRepo->createOrUpdate($postedData)) {
                    $this->Flash->success(__d('successful_candidate', 'REGISTER_COMPLETED'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__d('successful_candidate', 'CONTENT_INCOMPLETE'));
            }
        } else if (!empty($this->request->getQuery('formId'))) {
            $formId = $this->request->getQuery('formId');
            $voiceForm = $this->voiceFormRepo->getVoiceFormByIDWithAssoc($formId);
        } else {
            // Create edit-form
            $saveForm = [
                'name' => $category['name'],
                'category_id' => $categoryId,
                'lock' => 0,
                'show_people' => 0,
                'send_mail' => 1
            ];
            $result = $this->voiceFormRepo->createOrUpdate($saveForm);
            $voiceForm = $this->voiceFormRepo->getVoiceFormByIDWithAssoc($result->id);
        }

        $this->set(compact('voiceForm', 'category'));
    }

    /**
     * Module list form
     *
     * @return \Cake\Http\Response|null|void Renders module_lists
     */
    public function moduleLists() {
        $categoryId = $this->request->getSession()->read('categoryId');
        if (empty($this->request->getQuery('formId')) || empty($categoryId)) {
           throw new Exception\NotFoundException;
        }

        $formId = $this->request->getQuery('formId');

        // Check existing formId by Category
        if (!$this->voiceFormRepo->checkExistVoiceFormsWithCategoryID($formId, $categoryId)) {
            throw new Exception\NotFoundException;
        }

        $moduleLists = $this->voicePartRepo->getListSelectedFieldsWithKeyValue($formId, 'slug', 'slug');
        $this->set(compact('formId', 'moduleLists'));
    }

    /**
     * Module edit form
     *
     * @return \Cake\Http\Response|null|void Renders edit_module
     */
    public function editModule() {

        $categoryId = $this->request->getSession()->read('categoryId');
        if (empty($categoryId)) {
           throw new Exception\NotFoundException;
        }

        // Define variables
        $voiceModule = [];
        $initial = null;

        if ($this->request->is('post') && $this->request->getData()) {
            $postedData = $this->request->getData();

            $validator = $this->VoiceParts->getValidator('default');
            $errors = $validator->validate($postedData);

            if (!empty($errors)) {
                throw new InternalErrorException();
            }

            $messages = $this->VoiceParts->saveVoicePart($postedData);

            // show messages and redirect
            if (!empty($messages)) {
                if (isset($messages['error']) && $messages['error'] == 0) {
                    $this->Flash->success($messages['message'] ?? __d('successful_candidate', 'REGISTER_COMPLETED'));
                } else if ($messages['error'] != 0) {
                    throw new Exception\InternalErrorException();
                } else {
                    $this->Flash->error($messages['message'] ?? __d('successful_candidate', 'CONTENT_INCOMPLETE'));
                }
                return $this->redirect($messages['redirect']);
            }
        } else if (
            (empty($this->request->getQuery('moduleSlug')) && empty($this->request->getQuery('formId')) && empty($this->request->getQuery('partId')))
            || (!empty($this->request->getQuery('formId')) && !$this->voiceFormRepo->checkExistVoiceFormsWithCategoryID($this->request->getQuery('formId'), $categoryId))
            || (!empty($this->request->getQuery('partId')) && !$this->voicePartRepo->checkExistVoicePartsWithCategoryID($this->request->getQuery('partId'), $categoryId))
        ) {
            throw new Exception\NotFoundException;
        }
        if (!empty($this->request->getQuery('partId'))) {
            $partId = $this->request->getQuery('partId');
        }
        if (!empty($this->request->getQuery('initial'))) {
            $initial = $this->request->getQuery('initial');
            $releaseiLists = $releaseiLists = $this->voiceReleaseRepo->getListByInitial();

        } else {
            $releaseiLists = $this->voiceReleaseRepo->getListByInitial(1);
        }
        $fix = null;
        if (empty($partId)) {
            $formId = $this->request->getQuery('formId');
            $moduleSlug = $this->request->getQuery('moduleSlug');
            if (!empty($this->request->getQuery('fix'))) {
                $fix = $this->request->getQuery('fix');
            }

            $showModule = $this->voicePartRepo->countByFormID($formId, 0, [
                'slug' => [JYUKENTIKU2,JYUKENTIKU3,JYUKENTIKU_TEXT1, JYUKENTIKU_TEXT2,JYUKENTIKU_TEXT3]
            ]);
        } else {
            $voiceModule = $this->voicePartRepo->getByIDWithPartOptions($partId);
            $formId = $voiceModule['form_id'];
            $moduleSlug = $voiceModule['slug'];

            if (!empty($this->request->getQuery('fix'))) {
                $fix = $this->request->getQuery('fix');
            }

            $showModule = $this->voicePartRepo->countByFormID($formId, 0, [
                'id' => $partId,
                'slug' => [JYUKENTIKU2,JYUKENTIKU3,JYUKENTIKU_TEXT1, JYUKENTIKU_TEXT2,JYUKENTIKU_TEXT3]
            ]);
        }

        $form = $this->voiceFormRepo->getVoiceFormByIDWithAssoc($formId);

        $zeirishiLists = $this->voiceZeirishiListRepo->getList();
        $zeirishiKamokuLists = $this->voiceZeirishiKamokuListRepo->getList();
        $jyukentikuLists = $this->voiceJyukentikuListRepo->getList();
        $headLineLists = $this->voiceHeadRepo->getList();

        $this->set(compact('moduleSlug', 'headLineLists', 'formId', 'voiceModule', 'fix', 'showModule',
            'initial', 'zeirishiLists', 'form', 'releaseiLists', 'jyukentikuLists', 'zeirishiKamokuLists'));
    }

    /**
     * Delete Module method
     *
     * @param null
     * @return void Redirect
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteModule() {
        $partId = $this->request->getQuery('partId');
        $this->disableAutoRender();
        if (empty($partId)) {
            return false;
        }

        $categoryId = $this->request->getSession()->read('categoryId');
        if (empty($categoryId) || !$this->voicePartRepo->checkExistVoicePartsWithCategoryID($partId, $categoryId)) {
           throw new Exception\NotFoundException;
        }

        // Association delete
        if ($this->voicePartRepo->deleteByPartID($partId)) {
            $this->Flash->success(__d('successful_candidate', 'DELETE_COMPLETED'));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * List category method
     *
     * @param null
     * @return \Cake\Http\Response|null|void Renders category_lists
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function categoryLists() {

        $categoryLists = $this->voiceCategoryRepo->getAllCategories();
        $this->set(compact('categoryLists'));
    }

    /**
     * Add category method
     *
     * @param $id
     * @return \Cake\Http\Response|null|void Renders add_category
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function addCategory($id = null) {
        // Set headers Cache-Control
        header("Cache-Control: no cache");
        $self = $this->Authentication->getIdentity();
        if ($self->role_id != HIGHEST) {
            throw new Exception\NotFoundException;
        }

        // Define variables
        $categoryLists = [
            'name' => null,
            'id' => null
        ];

        if (!empty($id)) {
            $categoryLists = $this->voiceCategoryRepo->getVoiceCategoryByID($id);
        }
        $this->set(compact('categoryLists'));

        // POST method
        if ($this->request->is('post') && !empty($this->request->getData())) {
            if ($this->voiceCategoryRepo->createOrUpdate($this->request->getData())) {
                $this->Flash->success(__d('successful_candidate', 'REGISTRATION_COMPLETED'));
                $this->redirect(array('controller' => 'SuccessfulCandidates','action' => 'categoryLists'));
            } else {
                $this->Flash->error(__d('successful_candidate', 'NEED_FIX'));
                return false;
            }
        }
    }

    /**
     * Delete category method
     *
     * @param $id
     * @return void Redirect
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteCategory($id = null) {
        $this->disableAutoRender();

        if (empty($id)) {
            return false;
        }
        $this->voiceCategoryRepo->delete($id);
        $this->redirect(['action' => 'categoryLists']);
    }

    /**
     * List User method
     *
     * @param null
     * @return \Cake\Http\Response|null|void Renders user_lists
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function userLists() {
        $this->paginate = array(
            'VoiceUsers' => [
                // 'fields' => '*',
                // 'limit' => 4,
                // 'order' => array('created' => 'ASC'),
                'conditions' => []
        ]);

        $users = $this->paginate($this->voiceUserRepo->getAllUsers());

        $roleLists = $this->voiceRoleRepo->getListRoles();
        $categoryLists = $this->voiceCategoryRepo->getVoiceCategories(true);

        $this->set(compact('users', 'roleLists', 'categoryLists'));
    }

    /**
     * Delete voice user method
     *
     * @param $id
     * @return void|boolean Redirect
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteVoiceUser($id = null) {
        $this->disableAutoRender();

        if (empty($id)) {
            return false;
        }
        $this->voiceUserRepo->deletebyID($id);
        $this->redirect(['action' =>'userLists']);
    }

    /**
     * Registration voice user method
     *
     * @param $id
     * @return \Cake\Http\Response|null|void Renders user_lists
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function add($id = null) {
        header("Cache-Control: no cache");
        $self = $this->Authentication->getIdentity();
        if ($self->role_id != HIGHEST) {
            throw new Exception\NotFoundException;
        }
        $categoryLists = $this->voiceCategoryRepo->getVoiceCategories();
        $roleLists = $this->voiceRoleRepo->getListRoles();

        // Define variables
        $user = [
            'id' => null,
            'username' => null,
            'role_id' => null,
            'category_id' => null
        ];
        if (!empty($id)) {
            $user = $this->voiceUserRepo->getByID($id);
        }

        $rawData = $user;
        $this->set(compact('roleLists', 'categoryLists',  'user', 'rawData'));

        // POST / PUT method
        if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->getData())) {
            $params = $this->request->getData();
            $highestRoles = $this->voiceRoleRepo->getListRoleIDByRole(HIGHEST);
            $self = $this->Authentication->getIdentity();
            $currentRole = $self->role_id;

            if (empty($this->request->getData('category_id'))) {
                $params['category_id'] = 0;
            }
            $this->request = $this->request->withParsedBody($params);

            if ((!in_array($this->request->getData('role_id'), $highestRoles)) && empty($this->request->getData('category_id'))) {
                $this->Flash->error(__d('successful_candidate', 'SELECT_COURSE'));
                return;
            }
            $user = $this->voiceUserRepo->createOrUpdate($this->request->getData());

            if (empty($user->getErrors())) {
                $this->Flash->success(__d('successful_candidate', 'REGISTRATION_COMPLETED'));

                // If the user changed role / category => logout
                if ($user->id == $self->id && ($user->role_id !== $self->role_id || $user->category_id !== $self->category_id)) {
                    $this->logout();
                    return;
                }
                $this->redirect(array('controller' => 'SuccessfulCandidates', 'action' => 'userLists'));
            } else {
                $this->Flash->error(__d('successful_candidate', 'NEED_FIX'));
                $this->set(compact('user', 'rawData'));
                return;
            }
        }
    }

    /**
     * View method
     *
     * @param string|null $id Successful Candidate id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {

    }

    /**
     * Edit method
     *
     * @param string|null $id Successful Candidate id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {

    }

    /**
     * Delete method
     *
     * @param string|null $id Successful Candidate id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {

    }
}
