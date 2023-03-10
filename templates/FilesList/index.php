<?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'FilesList', 'action' => 'index'], 'type' => 'post')); ?>
<div>
    <?php
        // Registration date (start)
        echo $this->Form->select('files_date_from', $ym_list_from, array(
            'value' => $files_date_from,
            'size'      => 1,
            'multiple'    => false,
            'use_group'    => false,
            'class'    => 'form01'
        ));

        // Registration date (end)
        echo $this->Form->select('files_date_to', $ym_list_to, array(
            'value' => $files_date_to,
            'size'      => 1,
            'multiple'    => false,
            'use_group'    => false,
            'style'    => "margin-left:4px;",
            'class'    => 'form01'
        ));

        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => __d('files_list', 'NARROW_DOWN_EXECUTION'),
            'style'    => "margin-left:5px;"
        ));
    ?>
</div>
<?php echo $this->Form->end(); ?>

<?php
    $page = intval($this->request->getQuery('page') ?? 0);
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
    <?php if ($this->Paginator->counter('{{pages}}') > 11) : ?>
        <?= $this->Paginator->first(__d('paginate', 'FIRST_PAGE_SCHOOL_CMS')) ?>
    <?php endif; ?>
    <?php if ($page > 0) : ?>
        <?= $this->Paginator->prev(__d('paginate', 'BTN_PREV_SCHOOL_CMS')) ?>
    <?php endif ?>
    <?php if (!empty($records)) : ?>
        <?= $this->PaginatorCustom->numbers(['separator' => '|', 'modulus' => 10, 'cms_custom' => true]) ?>
    <?php endif ?>
    <?php if ($page < $this->Paginator->counter('{{pages}}') && $this->Paginator->counter('{{pages}}') != 1) : ?>
        <?= $this->Paginator->next(__d('paginate', 'BTN_NEXT_SCHOOL_CMS')) ?>
    <?php endif ?>
    <?php if ($this->Paginator->counter('{{pages}}') > 11) : ?>
        <?= $this->Paginator->last(__d('paginate', 'LAST_PAGE_SCHOOL_CMS')) ?>
    <?php endif; ?>
</p>

<?php echo $this->Form->create(null, array('url' => ['controller' => 'FilesList', 'action' => 'filesChangeRecords'], 'type' => 'post')); ?>
<div>
    <input type="button" value="<?= __d('files_list', 'SELECT_ALL'); ?>" onclick="changeCheckboxAll('ids[]', true);" />
    <input type="button" value="<?= __d('files_list', 'DESELECT_ALL'); ?> " onclick="changeCheckboxAll('ids[]', false);" />
    <input type="button" value="<?= __d('files_list', 'INVERT_SELECTION'); ?> " onclick="revertCheckboxAll('ids[]');" />
</div>
<table class="catalog">
    <thead>
        <tr>
            <th><img src="../css/admin_custom/images/icon_check.gif" width="12" height="13" alt="checkbox"/></th>
            <th>ID</th>
            <th>サーバ上のファイル名</th>
            <th>オリジナル名</th>
            <th>サイズ</th>
            <th>作成日時</th>
            <th>URL</th>
        </tr>
    </thead>
    <tbody>
        <?= $table_body ?>
    </tbody>
</table>

<p>
    <?= "全" . $num_records . "件" ?>&nbsp;
    <?php if ($this->Paginator->counter('{{pages}}') > 11) : ?>
        <?= $this->Paginator->first(__d('paginate', 'FIRST_PAGE_SCHOOL_CMS')) ?>
    <?php endif; ?>
    <?php if ($page > 0) : ?>
        <?= $this->Paginator->prev(__d('paginate', 'BTN_PREV_SCHOOL_CMS')) ?>
    <?php endif ?>
    <?php if (!empty($records)) : ?>
        <?= $this->PaginatorCustom->numbers(['separator' => '|', 'modulus' => 10, 'cms_custom' => true]) ?>
    <?php endif ?>
    <?php if ($page < $this->Paginator->counter('{{pages}}') && $this->Paginator->counter('{{pages}}') != 1) : ?>
        <?= $this->Paginator->next(__d('paginate', 'BTN_NEXT_SCHOOL_CMS')) ?>
    <?php endif ?>
    <?php if ($this->Paginator->counter('{{pages}}') > 11) : ?>
        <?= $this->Paginator->last(__d('paginate', 'LAST_PAGE_SCHOOL_CMS')) ?>
    <?php endif; ?>
</p>

<?php
    echo $this->Form->input('delete', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => __d('files_list', 'DELETE_MARKED_ITEM'),
    ));
?>
<?php echo $this->Form->end(); ?>

<div style="margin-top:30px;padding-top:30px;width:100%;border-top:#aaaaaa 1px dashed">
<h2 style="margin:0.5em 0"><?= __d('files_list', 'BULK_UPLOAD_OF_PDF_FILES'); ?></h2>
    <?php echo $this->Form->create(null, array('url' => ['controller' => 'UploadFiles', 'action' => 'pdfUpload'], 'type' => 'file')); ?>

        <?php for ($x = 0; $x < 20; $x++): ?>
            <input type="file" name="pdf<?= $x; ?>" style="margin:2px 0"><br>
        <?php endfor; ?>

        <input type="submit" value="<?= __d('files_list', 'UPLOAD'); ?>" style="margin-top:20px">
    <?php echo $this->Form->end(); ?>
    <br />
</div>
