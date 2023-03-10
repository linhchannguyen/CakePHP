<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Repositories\Users\UserRepository;

class StudentsController extends AppController
{
    private $userRepository;

    public function initialize(): void
    {
        parent::initialize();

        $this->loadModel("Students");
        $this->userRepository = new UserRepository($this->Students);
    }

    public function addStudent()
    {
        debug($this->userRepository->getAll());
        debug($this->userRepository->getByEmail('huynl@ominext.com'));
        $student = $this->Students->newEmptyEntity();
        if ($this->request->is('post')) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been created.'));
                return $this->redirect(['action' => 'listStudents']);
            }
            $this->Flash->error(__('Failed to create student. Please, try again.'));
        }
        $this->set("title", "Add Stduent");
        $this->set(compact("student"));
    }

    public function listStudents()
    {
        $students = $this->Students->find()->toList();
        $this->set("title", "List Student");
        $this->set(compact("students"));
    }

    public function editStudent($id = null)
    {
        $student = $this->Students->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student data has been updated successfully.'));

                return $this->redirect(['action' => 'listStudents']);
            }
            $this->Flash->error(__('The student could not be updated. Please, try again.'));
        }
        $this->set(compact('student'));
        $this->set("title", "Edit Student");
    }

    public function deleteStudent($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        if ($this->Students->delete($student)) {
            $this->Flash->success(__('The student has been deleted.'));
        } else {
            $this->Flash->error(__('The student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'listStudents']);
    }
}
