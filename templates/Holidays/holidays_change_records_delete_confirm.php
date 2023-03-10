<p><?= __d('holiday', 'HOLIDAYS_LIST_DELETE') ?></p>
<table class="catalog">
    <thead>
        <tr>
            <th><?= __d('holiday', 'HOLIDAY_DATE_COL') ?></th>
            <th><?= __d('holiday', 'HOLIDAY_NAME_COL') ?></th>
            <th><?= __d('holiday', 'HOLIDAY_CREATE_DATE_COL') ?></th>
            <th><?= __d('holiday', 'HOLIDAY_MODIFY_DATE_COL') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($table_body as $tb): ?>
            <tr class='<?= $tb['tr_class'] ?>'>
                <td><?= $tb['holiday_date'] ?></td>
                <td><?= $tb['holiday_name'] ?></td>
                <td><?= $tb['created'] ?></td>
                <td><?= $tb['modified'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<br />
<div class="frm">
    <?= $this->Form->create(null, ['url' => '/holidays_list/holidays_change_records_delete_finish', 'type' => 'post']) ?>
        <?= $this->Form->input('', ['type' => 'hidden', 'div' => false, 'value' => 'holidays_change_records_delete_finish']) ?>
        <?= $this->Form->input('', ['type' => 'submit', 'div' => false, 'value' => __d('holiday', 'BTN_DELETE_CONFIRM')]) ?>
    <?= $this->Form->end() ?>
    &nbsp;
    <?= $this->Form->create(null, ['url' => '/holidays_list/index', 'type' => 'post']) ?>
        <?= $this->Form->input('', ['type' => 'hidden', 'div' => false, 'value' => 'holidays_list']) ?>
        <?= $this->Form->input('', ['type' => 'submit', 'div' => false, 'value' => __d('holiday', 'BTN_BACK')]) ?>
    <?= $this->Form->end() ?>
</div>