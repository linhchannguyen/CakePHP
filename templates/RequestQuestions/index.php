<head>
    <?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js') ?>
    <?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js') ?>
    <?= $this->Html->css('https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css') ?>
    <?= $this->Html->css('https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css') ?>
</head>

<script type="text/javascript" language="javascript">
    function onButtonClick() {
        var targetid = document.getElementById("RequestQuestionCourseid").value;
        document.getElementById('CsvExportFormCourseid').value = targetid;
        var targetFrom = document.getElementById("RequestQuestionRecordDayFrom").value;
        document.getElementById('CsvExportFormRecordDayFrom').value = targetFrom;
        var targetTo = document.getElementById("RequestQuestionRecordDayTo").value;
        document.getElementById('CsvExportFormRecordDayTo').value = targetTo;
    }
    $(document).ready(function(){
        $(".dateSelect").datepicker();
        $('table#stripe-table tr:even').css('background-color', '#F0E68C');
        $('table#stripe-table tr:odd').css('background-color', '#D3D3D3');
    });
</script>

<span class="logout">
	<?= $this->Html->link(__d('request_question', 'BTN_LOGOUT'), ['action' => 'logout', 'controller' => 'RequestQuestions']) ?>
</span>

<p>&nbsp;</p>
<p>&nbsp;</p>

<?= $this->Flash->render() ?>
<?= $this->Form->create($requestValidate, ['url' => '/request_questions/searchQuestion', 'class' => 'search', 'type' => 'post']) ?>
<table frame="box">
    <tr>
        <td>
            <?= $this->Form->control('RequestQuestion.courseid', [
                'id' => 'RequestQuestionCourseid',
                'label'    => __d('request_question', 'LABEL_COURSE_ID'),
                'type'     => 'select',
                'options'  => $selectMaster,
                'value' => $courseid = !empty($searchedParam['RequestQuestion']['courseid']) ? $searchedParam['RequestQuestion']['courseid'] : 9999999,
                'style' => 'min-width: 300px;'])
            ?>
        </td>
        <td>
            <?= $this->Form->submit(__d('request_question', 'BTN_SUBMIT_FORM_SEARCH'), ['style' => 'width: 200px; height:30px;']) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= $this->Form->control('RequestQuestion.recordDayFrom', [
                'id' => 'RequestQuestionRecordDayFrom',
                'label' => __d('request_question', 'LABEL_DAY_FORM'),
                'type'  => 'text',
                'value' => $recordDayFrom = !empty($searchedParam['RequestQuestion']['recordDayFrom']) ? $searchedParam['RequestQuestion']['recordDayFrom'] : '',
                'class' => 'dateSelect',
                'autocomplete' => 'off'])
            ?>
        </td>
        <td>
            <?= $this->Form->control('RequestQuestion.recordDayTo', [
                'id' => 'RequestQuestionRecordDayTo',
                'label' => __d('request_question', 'LABEL_DAY_TO'),
                'type'  => 'text',
                'value' => $recordDayTo = !empty($searchedParam['RequestQuestion']['recordDayTo']) ? $searchedParam['RequestQuestion']['recordDayTo'] : '',
                'class' => 'dateSelect',
                'autocomplete' => 'off'])
            ?>
        </td>
    </tr>
</table>
<?= $this->Form->end() ?>

<p>&nbsp;</p>
<?= "全".$countRecord."件" ?>
<div style='float:right; width:50%; text-align:right;'>
    <?= $this->PaginatorCustom->prev(__d('request_question', 'BTN_PREV')) ?>
    <?php if (!empty($RequestQuestion)): ?>
        <?= $this->PaginatorCustom->numbers(['separator' => ' | ']) ?>
    <?php endif ?>
    <?= $this->PaginatorCustom->next(__d('request_question', 'BTN_NEXT')) ?>
</div>

<p>&nbsp;</p>
<table style="width:740px;" id="stripe-table">
    <tr>
        <th>登録日</th>
        <th>講座</th>
        <th>お名前</th>
        <th>メールアドレス</th>
    </tr>
    <?php foreach($RequestQuestion as $key => $val): ?>
        <tr>
            <td><?= $val['record_day'] ?></td>
            <td>
                <?php
                    if (array_key_exists(intval($val['courseid']), $master)) {
                        if (array_key_exists($val['course_cd1'], $master[intval($val['courseid'])])) {
                            echo $seikyuList[$master[intval($val['courseid'])][$val['course_cd1']]['kouza_id']];
                        } else {
                            echo '不正な値です。';
                        }
                    } else {
                        echo '不正な値です。';
                    }
                ?>
            </td>
            <td><?= $val['name'] ?></td>
            <td><?= $val['mailadress'] ?></td>
        </tr>
    <?php endforeach ?>
</table>
<p>&nbsp;</p>


<div style='float:right; width:50%;text-align:right;'>
    <?= $this->PaginatorCustom->prev(__d('request_question', 'BTN_PREV')) ?>
    <?php if (!empty($RequestQuestion)): ?>
        <?= $this->PaginatorCustom->numbers(['separator' => ' | ', 'test']) ?>
    <?php endif ?>
    <?= $this->PaginatorCustom->next(__d('request_question', 'BTN_NEXT')) ?>
</div>

<?= $this->Form->create(null, ['url' => '/request_questions/exportCsv', 'type' => 'post', 'onsubmit ' => 'onButtonClick()']) ?>
<?= $this->Form->hidden('CsvExportForm.courseid', ['value' => '', 'id' => 'CsvExportFormCourseid']) ?>
<?= $this->Form->hidden('CsvExportForm.recordDayFrom', ['value' => '', 'id' => 'CsvExportFormRecordDayFrom']) ?>
<?= $this->Form->hidden('CsvExportForm.recordDayTo', ['value' => '', 'id' => 'CsvExportFormRecordDayTo']) ?>
<?= $this->Form->submit(__d('request_question', 'BTN_CSV_EXPORT')) ?>
<?= $this->Form->end() ?>