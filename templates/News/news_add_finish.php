<?php if (!empty($errors)) { ?>
    <ul>
        <li>
            <?= $errors; ?>
        </li>
    </ul>
    <p>
        <?php echo $this->Html->link(__d('default', 'BACK'), '/news_list?f=news_add&recovery=true'); ?>
    </p>
<?php } else { ?>
    <p>以下のデータを追加しました。</p>
    <table class="catalog">
        <thead>
            <tr>
                <th>ID</th>
                <th>校舎</th>
                <th>日付</th>
                <th>タイトル</th>
                <th>サブタイトル</th>
                <th>リンク</th>
                <th>緊急度</th>
                <th>並び補正</th>
                <th>表示</th>
                <th>有効期間(始)</th>
                <th>有効期間(終)</th>
                <th>作成日</th>
                <th>更新日</th>
            </tr>
        </thead>
        <tbody>
            <?= $table_body ?>
        </tbody>
    </table>
    <?php echo $this->Form->create(null, $option = array('url' => [
        'controller' => 'News', 'action' => 'index',
        '?' => [
            'f' => 'news_add',
            'recovery' => 'true'
        ]
    ], 'type' => 'post')); ?>
    <?php
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => '続けて登録する',
    ));
    ?>
    <?php echo $this->Form->end(); ?>
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'News', 'action' => 'index'], 'type' => 'post')); ?>
    <?php
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => 'リストに戻る',
    ));
    ?>
    <?php echo $this->Form->end(); ?>
<?php } ?>