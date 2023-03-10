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
      <th><?= __d('csv_form', 'CSV_TABLE_DATE_COL') ?></th>
      <th><?= __d('csv_form', 'CSV_TABLE_HOLIDAY_NAME_COL') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($table_body as $tb): ?>
    	<tr class='class_active'>
			<td><?= $tb['holiday_date'] ?></td>
			<td><?= $tb['holiday_name'] ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<p>全<?= $num_records ?>件&nbsp;&nbsp;<?= $pager_link ?></p>
<br />

<?= $this->Form->create(null, ['url' => '/csv_form/csv_holidays_receive_finish', 'type' => 'post']) ?>
<?= $this->Form->hidden('', ['name' => 'f', 'value' => 'csv_holidays_receive_finish']) ?>
<?= $this->Form->submit(__d('csv_form', 'CSV_REGISTER_BTN')); ?>
<?= $this->Form->end() ?>