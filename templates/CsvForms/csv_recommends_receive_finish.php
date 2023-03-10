<p><?= $message ?></p>

<?= $this->Form->create(null, ['url' => '/csv_form/index', 'type' => 'post']) ?>
<?= $this->Form->submit(__d('csv_form', 'CSV_RETURN_BTN')); ?>
<?= $this->Form->end() ?>