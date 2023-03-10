<p><?= __d('files_list', 'TITLE_DELETE_COMPLETED'); ?></p>

<br />
<?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'FilesList', 'action' => 'index'], 'type' => 'post')); ?>
<?php
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => __d('files_list', 'RETURN_TO_FILES_LIST'),
    ));
?>
<?php echo $this->Form->end(); ?>
