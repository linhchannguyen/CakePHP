<p>以下のデータを削除します。</p>

<table class="catalog">
  <thead>
    <tr>
      <th>ID</th>
      <th>校舎</th>
      <th>講座</th>
      <th>タイトル</th>
      <th>サブタイトル</th>
      <th>リンク</th>
      <th>並び補正</th>
      <th>表示</th>
      <th>有効期間(始)</th>
      <th>有効期間(終)</th>
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
  <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Recommends', 'action' => 'index'], 'type' => 'post')); ?>
  <?php
  echo $this->Form->input('ids', array(
    'type'     => 'hidden',
    'div'      => false,
    'value'    => $ids,
  ));
  echo $this->Form->input('f', array(
    'type'     => 'hidden',
    'div'      => false,
    'value'    => 'recommends_change_records_delete_finish',
  ));
  echo $this->Form->input('', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => '削除します',
  ));
  ?>
  <?php echo $this->Form->end(); ?>
  &nbsp;
  <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Recommends', 'action' => 'index'], 'type' => 'post')); ?>
  <?php
  echo $this->Form->input('', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => '戻る',
  ));
  ?>
  <?php echo $this->Form->end(); ?>
</div>