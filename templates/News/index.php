<?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'News', 'action' => 'index'], 'type' => 'post')); ?>
<div>
    <?php
    // school list
    echo $this->Form->input('school_id', array(
        'type'     => 'select',
        'options'  => $school_list,
        'value' => $controls['school_id'],
        'div'      => false,
        'size'      => 1,
        'label'    => false,
        'multiple'    => false,
        'use_group'    => false,
        'class'    => 'form01',
        'id' => 'dataNews_listModelschool_id'
    ));

    // display state list
    echo $this->Form->input('is_active', array(
        'type'     => 'select',
        'options'  => $controls['is_active_list'],
        'value' => $controls['is_active'],
        'div'      => false,
        'size'      => 1,
        'multiple'    => false,
        'use_group'    => false,
        'class'    => 'form01',
        'style'    => "margin-left:4px;",
        'id' => 'dataNews_listModelis_active'
    ));

    // Urgency
    echo $this->Form->input('urgency', array(
        'type'     => 'select',
        'options'  => $controls['urgency_list'],
        'value' => $controls['urgency'],
        'div'      => false,
        'size'      => 1,
        'multiple'    => false,
        'use_group'    => false,
        'class'    => 'form01',
        'style'    => "margin-left:5px;",
        'id' => 'dataNews_listModelurgency'
    ));

    // Publication start month list
    echo $this->Form->input('news_date_from', array(
        'type'     => 'select',
        'options'  => $controls['ym_list_from'],
        'value' => $controls['news_date_from'],
        'div'      => false,
        'size'      => 1,
        'multiple'    => false,
        'use_group'    => false,
        'class'    => 'form01',
        'style'    => "margin-left:4px;",
        'id' => 'dataNews_listModelnews_date_from'
    ));

    // Publication end month list
    echo $this->Form->input('news_date_to', array(
        'type'     => 'select',
        'options'  => $controls['ym_list_to'],
        'value' => $controls['news_date_to'],
        'div'      => false,
        'size'      => 1,
        'multiple'    => false,
        'use_group'    => false,
        'class'    => 'form01',
        'style'    => "margin-left:5px;",
        'id' => 'dataNews_listModelnews_date_to'
    ));

    // sorted list
    echo $this->Form->input('order_by', array(
        'type'     => 'select',
        'options'  => $controls['order_by_list'],
        'value' => $controls['order_by'],
        'div'      => false,
        'size'      => 1,
        'multiple'    => false,
        'use_group'    => false,
        'class'    => 'form01',
        'style'    => "margin-left:4px;",
        'id' => 'dataNews_listModelorder_by'
    ));

    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => '絞り込み実行',
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
    <?php if ($this->Paginator->total() > 11) : ?>
        <?= $this->Paginator->first(__d('paginate', 'FIRST_PAGE_SCHOOL_CMS')) ?>
    <?php endif; ?>
    <?php if ($page > 0 && $page != 1) : ?>
        <?= $this->Paginator->prev(__d('paginate', 'BTN_PREV_SCHOOL_CMS')) ?>
    <?php endif ?>
    <?php if (!empty($records)) : ?>
        <?= $this->PaginatorCustom->numbers(['separator' => '|', 'modulus' => 10, 'cms_custom' => true]) ?>
    <?php endif ?>
    <?php if ($page < $this->Paginator->total() && $this->Paginator->total() != 1) : ?>
        <?= $this->Paginator->next(__d('paginate', 'BTN_NEXT_SCHOOL_CMS')) ?>
    <?php endif ?>
    <?php if ($this->Paginator->total() > 11) : ?>
        <?= $this->Paginator->last(__d('paginate', 'LAST_PAGE_SCHOOL_CMS')) ?>
    <?php endif; ?>
</p>

<div class="frm">
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'News', 'action' => 'index'], 'type' => 'post')); ?>
    <?php
    echo $this->Form->input('f', array(
        'type'     => 'hidden',
        'div'      => false,
        'value'    => 'news_add',
    ));
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => '新規登録',
    ));
    ?>
    <?php echo $this->Form->end(); ?>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="すべて選択" onclick="changeCheckboxAll('ids[]', true);" />
    <input type="button" value="すべて選択解除 " onclick="changeCheckboxAll('ids[]', false);" />
    <input type="button" value="選択状態を反転する " onclick="revertCheckboxAll('ids[]');" />
</div>

<?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'News', 'action' => 'newsChangeRecords'], 'type' => 'post')); ?>
<table class="catalog">
    <thead>
        <tr>
            <th><img src="../css/admin_custom/images/icon_check.gif" width="12" height="13" alt="checkbox" /></th>
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
            <th>作成日時</th>
            <th>更新日時</th>
        </tr>
    </thead>
    <tbody>
        <?= $table_body ?>
    </tbody>
</table>
<p>
    <?= "全" . $num_records . "件" ?>&nbsp;
    <?php if ($this->Paginator->total() > 11) : ?>
        <?= $this->Paginator->first(__d('paginate', 'FIRST_PAGE_SCHOOL_CMS')) ?>
    <?php endif; ?>
    <?php if ($page > 0) : ?>
        <?= $this->Paginator->prev(__d('paginate', 'BTN_PREV_SCHOOL_CMS')) ?>
    <?php endif ?>
    <?php if (!empty($records)) : ?>
        <?= $this->PaginatorCustom->numbers(['separator' => '|', 'modulus' => 10, 'cms_custom' => true]) ?>
    <?php endif ?>
    <?php if ($page < $this->Paginator->total() && $this->Paginator->total() != 1) : ?>
        <?= $this->Paginator->next(__d('paginate', 'BTN_NEXT_SCHOOL_CMS')) ?>
    <?php endif ?>
    <?php if ($this->Paginator->total() > 11) : ?>
        <?= $this->Paginator->last(__d('paginate', 'LAST_PAGE_SCHOOL_CMS')) ?>
    <?php endif; ?>
</p>
<?php
echo $this->Form->input('delete', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => 'マークした項目を削除',
));
echo $this->Form->input('visible', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => 'マークした項目を表示に設定',
    'style'    => "margin-left:4px;padding-left:.5px;"
));
echo $this->Form->input('invisible', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => 'マークした項目を非表示に設定',
    'style'    => "margin-left:5px;"
));
?>
<?php echo $this->Form->end(); ?>

<br />

<div class="frm">
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'News', 'action' => 'index'], 'type' => 'post')); ?>
    <?php
    echo $this->Form->input('f', array(
        'type'     => 'hidden',
        'div'      => false,
        'value'    => 'news_add',
    ));
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => '新規登録',
    ));
    ?>
    <?php echo $this->Form->end(); ?>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'News', 'action' => 'newsDownloadCsv'], 'type' => 'post')); ?>
    <?php
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => '現在の絞り込み条件でCSVダウンロード',
    ));
    ?>
    <?php echo $this->Form->end(); ?>
</div>