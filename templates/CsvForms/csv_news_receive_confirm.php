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
    	<th><?= __d('csv_form', 'CSV_TABLE_DATE_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_TITLE_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_SUB_TITLE_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_URL_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_URGENCY_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_ORDER_NO_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_DISPLAY_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_ENABLE_FROM_COL') ?></th>
    	<th><?= __d('csv_form', 'CSV_TABLE_ENABLE_TO_COL') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($table_body as $tb): ?>
    	<tr class='<?= $tb['visible_class']?>'>
			<td><?= $tb['school_id'] . ' (' . $tb['school_name'] . ')' ?></td>
			<td><?= $tb['news_date'] ?></td>
			<td title="<?= $tb['news_title'] ?>"><?= $tb['news_title_limit'] ?></td>
			<td title="<?= $tb['news_title_sub'] ?>"><?= $tb['news_title_sub_limit'] ?></td>
			<td title="<?= $tb['news_url'] ?>">
				<a href="<?= $tb['news_url'] ?>" target="_blank"><?= $tb['title_url_line_limit'] ?></a>
			</td>
			<td><?= $tb['urgency'] ?></td>
			<td><?= $tb['order_no'] ?></td>
			<td><?= $tb['is_active'] ?></td>
			<td><?= $tb['enabled_from'] ?></td>
			<td><?= $tb['enabled_to'] ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<p>全<?= $num_records ?>件&nbsp;&nbsp;<?= $pager_link ?></p>
<br />

<?= $this->Form->create(null, ['url' => '/csv_form/csv_news_receive_finish', 'type' => 'post']) ?>
<?= $this->Form->hidden('', ['name' => 'f', 'value' => 'csv_news_receive_finish']) ?>
<?= $this->Form->submit(__d('csv_form', 'CSV_REGISTER_BTN')); ?>
<?= $this->Form->end() ?>