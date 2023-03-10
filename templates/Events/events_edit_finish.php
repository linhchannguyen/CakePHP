<?php if (!empty($errors)) { ?>
    <ul>
        <li>
            <?= $errors; ?>
        </li>
    </ul>
    <p>
        <?php echo $this->Html->link(__d('default', 'BACK'), '/events_list?f=events_edit&recovery=true'); ?>
    </p>
<?php } else { ?>
    <p>以下のデータを変更しました。</p>

    <table class="catalog">
        <thead>
            <tr>
                <th>ID</th>
                <th>校舎</th>
                <th>講座</th>
                <th>種別</th>
                <th>日時</th>
                <th>タイトル</th>
                <th>本文</th>
                <th>並び補正</th>
                <th>表示</th>
                <th>作成日時</th>
                <th>更新日時</th>
            </tr>
        </thead>
        <tbody>
            <?= $table_body ?>
        </tbody>
    </table>

    <?php echo $this->Form->create(null, $option = array('url' => [
        'controller' => 'Events', 'action' => 'index',
        '?' => [
            'f' => 'events_edit',
            'recovery' => 'true'
        ]
    ], 'type' => 'post')); ?>
    <?php
    echo $this->Form->input('', array(
        'type'     => 'hidden',
        'div'      => false,
        'name'     => 'id',
        'value'    => $id,
    ));
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => 'もう一度編集する',
    ));
    ?>
    <?php echo $this->Form->end(); ?>
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Events', 'action' => 'index'], 'type' => 'post')); ?>
    <?php
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => 'リストに戻る',
    ));
    ?>
    <?php echo $this->Form->end(); ?>

<?php } ?>