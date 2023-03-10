<?php 
    $currentPage = isset($table_body) ? intval($this->Paginator->counter('{{page}}')) : 0;
    $totalPage =  isset($table_body) ? intval($this->Paginator->counter('{{pages}}')) : 0;
    $this->Paginator->setTemplates([
        'nextActive' => '<a title="next page" rel="next" href="{{url}}">{{text}}</a>',
        'prevActive' => '<a title="previous page" rel="prev" href="{{url}}">{{text}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        'first' => '<a title="first page" href="{{url}}">{{text}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        'last' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title="last page" href="{{url}}">{{text}}</a>',
        'current' => '<b><u>{{text}}</u></b>'
    ]);
?>
<p>
    <?= "全" . $num_records . "件" ?>&nbsp;
    <?php if ($totalPage > 11) : ?>
        <?= $this->Paginator->first(__d('holiday', 'PAGINATE_FIRST')) ?>
    <?php endif; ?>
    <?php if ($currentPage !== 1) : ?>
        <?= $this->Paginator->prev(__d('holiday', 'PAGINATE_BACK')) ?>
    <?php endif; ?>
    <?= $this->PaginatorCustom->numbers(['separator' => '|', 'modulus' => 10, 'cms_custom' => true]) ?>
    <?php if ($currentPage !== $totalPage) : ?>
        <?= $this->Paginator->next(__d('holiday', 'PAGINATE_NEXT')) ?>
    <?php endif; ?>
    <?php if ($totalPage > 11) : ?>
        <?= $this->Paginator->last(__d('holiday', 'PAGINATE_LAST')) ?>
    <?PHP endif; ?>
</p>

<?= $this->Form->create(null, ['url' => '/holidays_list/holidays_change_records', 'type' => 'post']) ?>
    <div>
        <input type="button" value="<?= __d('holiday', 'BTN_CHECK_ALL') ?>" onclick="changeCheckboxAll('ids[]', true);" />
        <input type="button" value="<?= __d('holiday', 'BTN_UNCHECK_ALL') ?>" onclick="changeCheckboxAll('ids[]', false);" />
        <input type="button" value="<?= __d('holiday', 'BTN_REVERT_CHECK') ?>" onclick="revertCheckboxAll('ids[]');" />
    </div>
    <table class='catalog'>
        <thead>
            <tr>
                <th>
                    <img src='<?= $this->Url->assetUrl('css/admin_custom/images/icon_check.gif') ?>' width='12' height='13' alt='checkbox' />
                </th>
                <th><?= __d('holiday', 'HOLIDAY_DATE_COL') ?></th>
                <th><?= __d('holiday', 'HOLIDAY_NAME_COL') ?></th>
                <th><?= __d('holiday', 'HOLIDAY_CREATE_DATE_COL') ?></th>
                <th><?= __d('holiday', 'HOLIDAY_MODIFY_DATE_COL') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($table_body as $tb): ?>
                <tr class='<?= $tb['tr_class'] ?>'>
                    <td>
                        <input type='checkbox' name='<?= $tb['checkbox_name'] ?>' value='<?= $tb["holiday_date"] ?>' />
                    </td>
                    <td><?= $tb['holiday_date'] ?></td>
                    <td><?= $tb['holiday_name'] ?></td>
                    <td><?= $tb['created'] ?></td>
                    <td><?= $tb['modified'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <p>
        <?= "全" . $num_records . "件" ?>&nbsp;
        <?php if ($totalPage > 11) : ?>
            <?= $this->Paginator->first(__d('holiday', 'PAGINATE_FIRST')) ?>
        <?php endif; ?>
        <?php if ($currentPage !== 1) : ?>
            <?= $this->Paginator->prev(__d('holiday', 'PAGINATE_BACK')) ?>
        <?php endif; ?>
        <?= $this->PaginatorCustom->numbers(['separator' => '|', 'modulus' => 10, 'cms_custom' => true]) ?>
        <?php if ($currentPage !== $totalPage) : ?>
            <?= $this->Paginator->next(__d('holiday', 'PAGINATE_NEXT')) ?>
        <?php endif; ?>
        <?php if ($totalPage > 11) : ?>
            <?= $this->Paginator->last(__d('holiday', 'PAGINATE_LAST')) ?>
        <?PHP endif; ?>
    </p>
    <?= $this->Form->submit(__d('holiday', 'BTN_DELETE_CHECKED'), ['name' => 'delete']) ?>
<?= $this->Form->end() ?>