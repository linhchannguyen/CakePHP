<!-- in /templates/SuccessfulCandidates/login.php -->
<?= $this->Flash->render() ?>
<?= $this->Form->create(null, ['action' => 'login', 'class' => 'auth']) ?>

<div type="hidden" style="margin-top: 1em;"></div>
<?= $this->Form->control('username', [
    'label' => __d('form', 'LABEL_LOGIN_ID'),
    'class' => 'auth',
    'required' => true,
    'type' => 'text',
    'value' => $this->getRequest()->getSession()->read('SuccessfulCandidates.username')
]) ?>
<?= $this->Form->control('password', [
    'label' => __d('form', 'LABEL_PASSWORD'),
    'required' => true,
    'class' => 'auth',
    'value' => $this->getRequest()->getSession()->read('SuccessfulCandidates.password')
]) ?>

<?= $this->Form->control('category_id',  [
    'label' => __d('form', 'LABEL_COURSE'),
    'type' => 'select',
    'options' => $categoryLists,
    'empty' => __d('form', 'PLACEHOLDER_COURSE_SELECTION'),
    'value' => $this->getRequest()->getSession()->read('SuccessfulCandidates.categoryId')
]) ?>

<?= $this->Form->submit(__d('form', 'BUTTON_LOGIN')); ?>
<?= $this->Form->end() ?>
