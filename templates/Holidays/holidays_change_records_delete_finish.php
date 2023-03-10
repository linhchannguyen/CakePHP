<p><?= __d('holiday', 'HOLIDAYS_LIST_DELETED_SUCCESS') ?></p>
<br />
<?= $this->Form->create(null, ['url' => '/holidays_list/index', 'type' => 'post']) ?>
    <?= $this->Form->input('', ['type' => 'hidden', 'div' => false, 'value' => 'holidays_list']) ?>
    <?= $this->Form->input('', ['type' => 'submit', 'div' => false, 'value' => __d('holiday', 'BTN_BACK_TO_LIST')]) ?>
<?= $this->Form->end() ?>