<?php echo $this->Flash->render(); ?>
<script type="text/javascript" language="javascript">
    var selectedCourseId = <?= $courseid; ?>;
    var UPDATE_ALL_ALERT = `<?= __d('messages', 'UPDATE_ALL_ALERT'); ?>`;
    var DELETE_COMFIRM = `<?= __d('messages', 'DELETE_COMFIRM'); ?>`;
    var DELETE_CANCELED = `<?= __d('messages', 'DELETE_CANCELED'); ?>`;
</script>

<h3> フィード管理</h3>

<div style="margin-top: 10px;">
    ※ CSVインポート　：　講座ＩD,id,name,producturl,bigimage,description,price,retailprice,recommendable,coop_flg,page_class,extra_atp,delete_flg<br />
    ※ Criteo連携対象を転送する　：　id,name,producturl,bigimage,categoryid1,description,price,retailprice,recommendable,extra_atp<br />
</div>

<div style='float:left; width:800px; margin:10px auto;'>
    <div style='float:left; width:70%;'>
        <?php
        echo $this->Form->create(null, $options = array('url' => ['controller' => 'criteos', 'action' => 'importCsv'], 'class' => 'importCsv', 'type' => 'file', 'onsubmit ' => 'onButtonClick()'));
        echo $this->Form->file('CriteoImportForm.result', array('div' => false));
        echo $this->Form->hidden('CriteoImportForm.courseid', array(
            'value' => '',
            'id' => 'CriteoImportFormCourseid',
            'div' => false
        ));
        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => ' CSVインポート '
        ));
        echo $this->Form->end();
        ?>

        <div style="margin-top: 10px">
            <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'criteos', 'action' => 'searchCategory'], 'class' => 'search', 'type' => 'post', 'onsubmit ' => 'onButtonClick()')); ?>
            <span>対象講座: </span>
            <?php
            echo $this->Form->input('CriteoSeachForm.courseid', array(
                'type'     => 'select',
                'options'  => $courseList,
                'value' => $courseid,
                'onchange' => 'clearSelectedCourseId()',
                'div'      => false,
                'label'    => false,
                'style'    => "height:22px;",
                'id' => 'CriteoSeachFormCourseid'
            ));
            echo $this->Form->input('', array(
                'type'     => 'submit',
                'div'      => false,
                'value'    => ' 検索 ',
                'style'    => "margin-left:5px;"
            ));
            echo $this->Form->end();
            ?>
            <p>(表示件数：<?php echo count($tagList); ?>)</p>
        </div>
    </div>

    <div style='float:left; width:30%;text-align:right;'>
        <?php
        echo $this->Form->create(null, $options = array('url' => ['controller' => 'criteos', 'action' => 'downloadCsv'], 'class' => 'downloadCsv', 'type' => 'post', 'onsubmit ' => 'onButtonClick()'));
        echo $this->Form->hidden('CriteoDownloadForm.courseid', array(
            'value' => '',
            'id' => 'CriteoDownloadFormCourseid',
            'div' => false
        ));
        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => ' CSVエクスポート '
        ));
        echo $this->Form->end();

        echo $this->Form->create(null, $options = array('url' => ['controller' => 'criteos', 'action' => 'exportCsv'], 'class' => 'exportCsv', 'type' => 'post', 'onsubmit ' => 'onButtonClick()'));
        echo $this->Form->hidden('CriteoCsvExportForm.courseid', array(
            'value' => '',
            'id'    => 'CriteoCsvExportFormCourseid'
        ));
        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => ' Criteo連携対象を転送する ',
            'style' => 'margin-top:10px; margin-left:5px;'
        ));
        echo $this->Form->end();
        ?>
    </div>
</div>
<div style='clear:left;'></div>
<p>
    <font color="#FF0000">※</font> は必須です。
</p>

<?= $this->Html->css('/css/Criteo/index.css') ?>

<div class="type_form">
    <?php echo $this->Form->create(null, $options = array('url' => ['controller' => 'criteos', 'action' => 'registTags'], 'class' => 'regist', 'type' => 'post', 'onsubmit ' => 'return onButtonClick()')); ?>
    <table style="width:1000px">
        <tr>
            <td colspan="1"><span class="alert">※</span>ページID</td>
            <td colspan="1">
                <?php echo $this->Form->input('CriteoRegistForm.id', array(
                    'label' => false,
                    'type' => 'text',
                    'div' => false,
                    'style' => 'width:100%',
                )); ?>
            </td>
            <td colspan="1" class="col_header">ページ名</td>
            <td colspan="4">
                <?php echo $this->Form->input('CriteoRegistForm.name', array(
                    'label' => false,
                    'type' => 'text',
                    'div' => false,
                    'style' => 'width:100%'
                )); ?>
            </td>
            <td colspan="1" class="col_header">description</td>
            <td colspan="2">
                <?php echo $this->Form->input('CriteoRegistForm.description', array(
                    'label' => false,
                    'type' => 'text',
                    'style' => 'width:100%',
                    'div' => false
                )); ?>
            </td>
        </tr>
        <tr>
            <td colspan="1"><span class="alert">※</span>URL</td>
            <td colspan="6">
                <?php echo $this->Form->input('CriteoRegistForm.url', array(
                    'label' => false,
                    'type' => 'text',
                    'div' => false,
                    'style' => 'width:100%',
                )); ?>
            </td>
            <td colspan="1" class="col_header">extra_atp</td>
            <td colspan="2">
                <?php echo $this->Form->input('CriteoRegistForm.extra_atp', array(
                    'label' => false,
                    'type' => 'text',
                    'style' => 'width:100%',
                    'div' => false
                )); ?>
            </td>
        </tr>
        <tr>
            <td colspan="1"><span class="alert">※</span>bigimage</td>
            <td colspan="3">
                <?php echo $this->Form->input('CriteoRegistForm.bigimage', array(
                    'label' => false,
                    'type' => 'text',
                    'div' => false,
                    'style' => 'width:100%'
                )); ?>
            </td>
            <td colspan="1" rowspan="2"></td>
            <td colspan="1" class="col_header">ページ種別</td>
            <td colspan="1">
                <?php echo $this->Form->input('CriteoRegistForm.page_type', array(
                    'label'    => false,
                    'type'     => 'select',
                    'options'  => array(
                        1 => '一覧',
                        2 => '詳細',
                        3 => 'フォーム',
                        4 => 'CV',
                    ),
                    'div' => false,
                    'style' => 'width: 100%',
                )); ?>
            </td>
            <td colspan="1" class="col_header">Criteo連携</td>
            <td colspan="1">
                <?php echo $this->Form->input('CriteoRegistForm.recommendable', array(
                    'label'    => false,
                    'type'     => 'select',
                    'options'  => array(
                        1 => '連携',
                        0 => '非連携'
                    ),
                    'div' => false
                )); ?>
            </td>
            <td colspan="1"></td>
        </tr>
        <tr>
            <td colspan="1">価格</td>
            <td colspan="1">
                <?php echo $this->Form->input('CriteoRegistForm.price', array(
                    'label' => false,
                    'type' => 'text',
                    'div' => false,
                    'style' => 'width:100%'
                )); ?>
            </td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="1" class="col_header">Criteo広告</td>
            <td colspan="1">
                <?php echo $this->Form->input('CriteoRegistForm.cooperation_flag', array(
                    'label'    => false,
                    'type'     => 'select',
                    'options'  => array(
                        1 => '公開',
                        0 => '非公開'
                    ),
                    'div' => false
                )); ?>
            </td>
            <td colspan="1" align="right">
                <?php
                echo $this->Form->input('', array(
                    'type'     => 'submit',
                    'div'      => false,
                    'value'    => ' 新規登録 '
                ));
                ?>
            </td>
    </table>
    <?php
    echo $this->Form->input('CriteoRegistForm.courseid', array(
        'value' => '',
        'type' => 'hidden',
        'id' => 'CriteoRegistFormCourseid'
    ));
    echo $this->Form->end();
    ?>
</div>

<?php foreach ($tagList as $tag) : ?>
    <?php
    $tagData = array();
    if (!empty($postData)) {
        if ($tag['id'] == $postData['id']) {
            $tagData = $postData;
        } else {
            $tagData = $tag;
        }
    } else {
        $tagData = $tag;
    }
    ?>

    <div class="category_row" style="<?php echo ($tagData['recommendable'] === '0' ? 'background-color:#c0c0c0;' : ''); ?>">
        <?php echo $this->Form->create(null, $options = array('url' => ['controller' => 'criteos', 'action' => 'update'], 'id' => 'feed-' . $tagData['id'], 'class' => 'editTagsForm', 'type' => 'post', 'onsubmit ' => 'onButtonClick()')); ?>
        <?php
        echo $this->Form->hidden('CriteoEditForm.courseid', array(
            'value' => $tagData['courseid'],
            'class' => 'editTags',
            'id' => 'CriteoEditFormCourseid'
        ));
        ?>
        <table style="width:1000px">
            <tr>
                <td colspan="1"><span class="alert">※</span>ページID</td>
                <td colspan="1">
                    <?php echo $this->Form->input('CriteoEditForm.id', array(
                        'label' => false,
                        'type' => 'text',
                        'value' => $tagData['id'],
                        'div' => false,
                        'style' => 'width:100%',
                        'readonly' => true,
                        'class' => 'editTags',
                    )); ?>
                </td>
                <td colspan="1" class="col_header">ページ名</td>
                <td colspan="4">
                    <?php echo $this->Form->input('CriteoEditForm.name', array(
                        'label' => false,
                        'type' => 'text',
                        'value' => $tagData['name'],
                        'div' => false,
                        'style' => 'width:100%',
                        'class' => 'editTags',
                    )); ?>
                </td>
                <td colspan="1" class="col_header">description</td>
                <td colspan="2">
                    <?php echo $this->Form->input('CriteoEditForm.description', array(
                        'label' => false,
                        'type' => 'text',
                        'value' => $tagData['description'],
                        'style' => 'width:100%',
                        'class' => 'editTags',
                        'div' => false
                    )); ?>
                </td>
            </tr>
            <tr>
                <td colspan="1"><span class="alert">※</span>URL</td>
                <td colspan="6">
                    <?php echo $this->Form->input('CriteoEditForm.url', array(
                        'label' => false,
                        'type' => 'text',
                        'value' => $tagData['url'],
                        'div' => false,
                        'class' => 'editTags',
                        'style' => 'width:100%',
                    )); ?>
                </td>
                <td colspan="1" class="col_header">extra_atp</td>
                <td colspan="2">
                    <?php echo $this->Form->input('CriteoEditForm.extra_atp', array(
                        'label' => false,
                        'type' => 'text',
                        'value' => $tagData['extra_atp'],
                        'style' => 'width:100%',
                        'class' => 'editTags',
                        'div' => false
                    )); ?>
                </td>
            </tr>
            <tr>
                <td colspan="1"><span class="alert">※</span>bigimage</td>
                <td colspan="3">
                    <?php echo $this->Form->input('CriteoEditForm.bigimage', array(
                        'label' => false,
                        'type' => 'text',
                        'value' => $tagData['bigimage'],
                        'div' => false,
                        'style' => 'width:100%',
                        'class' => 'editTags',
                    )); ?>
                </td>
                <td colspan="1" rowspan="2" align="center">
                    <?php if (!empty($tagData['bigimage'])) : ?>
                        <img border='0' src='http://ebook.tac-school.co.jp/criteo/<?php echo $tagData['bigimage']; ?>' width='60' height='60'>
                    <?php endif; ?>
                </td>
                <td colspan="1" class="col_header">ページ種別</td>
                <td colspan="1">
                    <?php echo $this->Form->input('CriteoEditForm.page_type', array(
                        'label'    => false,
                        'type'     => 'select',
                        'options'  => array(
                            1 => '一覧',
                            2 => '詳細',
                            3 => 'フォーム',
                            4 => 'CV',
                        ),
                        'value' =>  $tagData['page_type'],
                        'div' => false,
                        'class' => 'editTags',
                        'style' => 'width: 100%',
                    )); ?>
                </td>
                <td colspan="1" class="col_header">Criteo連携</td>
                <td colspan="1">
                    <?php echo $this->Form->input('CriteoEditForm.recommendable', array(
                        'label'    => false,
                        'type'     => 'select',
                        'options'  => array(
                            1 => '連携',
                            0 => '非連携'
                        ),
                        'value' =>  $tagData['recommendable'],
                        'div' => false,
                        'class' => 'editTags',
                    )); ?>
                </td>
                <td colspan="1" rowspan="2" align="right" valign="bottom">
                    <?php
                    echo $this->Form->button(' 更新 ', array(
                        'type' => 'submit',
                        'div'   => false,
                        'style' => 'float:left;',
                        'name' => 'data[CriteoEditForm][action]',
                        'value' => 'update'
                    ));

                    echo $this->Form->button(' 削除 ', array(
                        'div'   => false,
                        'style' => 'margin-left: 10px;float:left;',
                        'onclick' => 'return deleteCheck()',
                        'name' => 'data[CriteoEditForm][action]',
                        'value' => 'delete'
                    )); ?>
                </td>
            </tr>
            <tr>
                <td colspan="1">価格</td>
                <td colspan="1">
                    <?php echo $this->Form->input('CriteoEditForm.price', array(
                        'label' => false,
                        'type' => 'text',
                        'value' => $tagData['price'],
                        'div' => false,
                        'class' => 'editTags',
                        'style' => 'width:100%',
                    )); ?>
                </td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="1" class="col_header">Criteo広告</td>
                <td colspan="1">
                    <?php echo $this->Form->input('CriteoEditForm.cooperation_flag', array(
                        'label'    => false,
                        'type'     => 'select',
                        'options'  => array(
                            1 => '公開',
                            0 => '非公開'
                        ),
                        'value' => $tagData['cooperation_flag'],
                        'div' => false,
                        'class' => 'editTags',
                    )); ?>
                </td>
            </tr>
        </table>
        <?php echo $this->Form->end(); ?>
    </div>
<?php endforeach; ?>

<?php
echo $this->Form->create(null, $options = array('action' => 'allUpdate', 'class' => 'allUpdate', 'type' => 'post', 'onsubmit ' => 'return allUpdate()', 'style' => 'text-align:center;'));
echo $this->Form->hidden('CriteoAllUpdateForm[courseid]', array(
    'value' => '',
    'id' => 'CriteoAllUpdateFormCourseid'
));
echo $this->Form->hidden('CriteoAllUpdateForm[data]', array(
    'value' => '',
    'id' => 'CriteoAllUpdateFormData'
));
echo $this->Form->input('', array(
    'type'     => 'submit',
    'value'    => ' 一括更新 ',
    'div' => false,
    'class' => 'allUpdate'
));
echo $this->Form->end();
?>
<?= $this->Html->script('/js/Criteo/index.js') ?>