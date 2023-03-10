<p>以下の表示設定を変更します。</p>

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

<br />

<div class="frm">
  <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Events', 'action' => 'index'], 'type' => 'post')); ?>
  <?php
  echo $this->Form->input('ids', array(
    'type'     => 'hidden',
    'div'      => false,
    'value'    => $ids,
  ));
  echo $this->Form->input('f', array(
    'type'     => 'hidden',
    'div'      => false,
    'value'    => 'events_change_records_invisible_finish',
  ));
  echo $this->Form->input('', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => '表示禁止に設定します',
  ));
  ?>
  <?php echo $this->Form->end(); ?>
  &nbsp;
  <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Events', 'action' => 'index'], 'type' => 'post')); ?>
  <?php
  echo $this->Form->input('', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => '戻る',
  ));
  ?>
  <?php echo $this->Form->end(); ?>
</div>