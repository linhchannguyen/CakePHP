<p>削除完了しました。</p>

<br />

<?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Events', 'action' => 'index'], 'type' => 'post')); ?>
<?php
echo $this->Form->input('', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => 'イベントリスト画面に戻ります',
));
?>
<?php echo $this->Form->end(); ?>