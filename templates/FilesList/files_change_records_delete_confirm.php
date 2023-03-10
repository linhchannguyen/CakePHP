<p><?= __d('files_list', 'TITLE_DELETE_FOLLOWING_DATA'); ?></p>

<table class="catalog">
    <thead>
        <tr>
            <th>ID</th>
            <th>サーバ上のファイル名</th>
            <th>オリジナル名</th>
            <th>サイズ</th>
            <th>作成日時</th>
            <th>URL</th>
        </tr>
    </thead>
    <tbody>
        <?= $table_body; ?>
    </tbody>
</table>

<br />

<div class="frm">
    <?php echo $this->Form->create(null, array('url' => ['controller' => 'FilesList', 'action' => 'filesChangeRecordsDeleteFinish'], 'type' => 'post')); ?>
    <?php
        echo $this->Form->input('ids', array(
            'type'     => 'hidden',
            'div'      => false,
            'value'    => $ids,
        ));
        echo $this->Form->input('f', array(
            'type'     => 'hidden',
            'div'      => false,
            'value'    => 'files_change_records_delete_finish',
        ));
        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => __d('files_list', 'DELETE'),
        ));
    ?>
    <?php echo $this->Form->end(); ?>
    &nbsp;
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'FilesList', 'action' => 'index'], 'type' => 'post')); ?>
    <?php
        echo $this->Form->input('f', array(
            'type'     => 'hidden',
            'div'      => false,
            'value'    => 'files_list',
        ));
        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => __d('default', 'BACK'),
        ));
    ?>
    <?php echo $this->Form->end(); ?>
</div>
