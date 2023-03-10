<script type="text/JavaScript">
  $(function() {
      $('table tr:odd').addClass("odd");
      $('table tr:not(:first-child):even').addClass("even");
  });
</script>

<p>全<?= $num_records ?>件&nbsp;&nbsp;<?= $pager_link ?></p>

<table class="catalog">
  <thead>
    <tr>
		<th><?= __d('csv_form', 'CSV_TABLE_SCHOOL_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_KOUZA_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_EVENT_TYPE_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_DATE_TIME_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_TITLE_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_ARTICLE_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_ORDER_NO_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_DISPLAY_COL') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($table_body as $tb): ?>
    	<tr class='<?= $tb['visible_class']?>'>
			<td><?= $tb['school_id'] . ' (' . $tb['school_name'] . ')' ?></td>
			<td><?= $tb['kouza_id'] . ' (' . $tb['kouza_name'] . ')' ?></td>
			<td><?= $tb['event_type'] . ' (' . $tb['event_type_name'] . ')' ?></td>
			<td><?= $tb['event_date'] ?></td>
			<td title="<?= $tb['event_title'] ?>"><?= $tb['event_title_limit'] ?></td>
			<td title="<?= $tb['event_body'] ?>"><?= $tb['event_body_limit'] ?></td>
			<td><?= $tb['order_no'] ?></td>
			<td><?= $tb['is_active'] ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<p>全<?= $num_records ?>件&nbsp;&nbsp;<?= $pager_link ?></p>
<br />

<?= $this->Form->create(null, ['url' => '/csv_form/csv_events_receive_finish', 'type' => 'post']) ?>
<?= $this->Form->hidden('', ['name' => 'f', 'value' => 'csv_events_receive_finish']) ?>
<?= $this->Form->submit(__d('csv_form', 'CSV_REGISTER_BTN')); ?>
<?= $this->Form->end() ?>