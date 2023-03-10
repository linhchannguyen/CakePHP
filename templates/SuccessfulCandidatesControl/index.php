<?php

use Cake\Core\Configure;

?>
<h2><?= __d('successful_candidate', 'PUBLISHING_CONTROL'); ?></h2>
<div>
<span><?php echo $this->Html->link('CSVダウンロード', array('controller' => 'SuccessfulCandidatesControl' , 'action' => 'download_csv', '?' => array('isZeiri' => $isZeiri))) ?></span>

<span><a href="<?php echo Configure::read('PreviewUrl'); ?>&formId=<?php echo $formId ?>" target="_blank"><?= __d('successful_candidate_control', 'PREVIEW_SITE_PUBLIS'); ?></a></span>
</div>
<div>
    <?php if ($role == HIGHEST || $role == ADMIN ): ?>
        <span><?= __d('successful_candidate_control', 'PUBLIC_CONTROL_LOCK'); ?><?php echo $this->Form->input('lock', array('type' => 'checkBox','label' => false,'checked' => $voiceForm['lock'],'div' => false, 'class' => 'commonCheck')); ?>
        <label for="VoiceFormLock"></label></span>
    <?php endif; ?>
    <span><?= __d('successful_candidate_control', 'NUMBER_OF_PEOPLE'); ?><?php echo $this->Form->input('show_people', array('type' => 'checkBox','label' => false,'checked' => $voiceForm['show_people'],'div' => false, 'class' => 'commonCheck')); ?>
    <label for="VoiceFormShowPeople"></label></span>
    <?php if (in_array(MAIL, $createdPartLists)): ?>
    <span><?= __d('successful_candidate_control', 'SEND_MAIL'); ?><?php echo $this->Form->input('send_mail', array('type' => 'checkBox','label' => false,'checked' => $voiceForm['send_mail'],'div' => false, 'class' => 'commonCheck')); ?>
    <label for="VoiceFormSendMail"></label></span>
    <?php endif; ?>
</div>
<?php
    echo $this->Paginator->counter(__('{{page}}/{{pages}}ページ'));
?>

    <?php echo $this->Form->create(null, array('url' => array('controller' => 'SuccessfulCandidatesControl', 'action' => 'postData'))); ?>
    <table class='members'>
        <tr>
            <th  class="td_id" >掲載</th>
            <td>
                <?php echo $this->Form->input('Search.release', array(
                    'type' => 'select',
                    'empty' => __d('default', 'SELECT_ALL'),
                    'options' =>  array(1 => '全て可', 2 => 'イニシャル', 3 => '掲載不可'),
                    'label' => false,
                    'value' => (isset($search['release']) && $search['release'] != '') ? $search['release'] : '',
                    'div' => false,
                    'class' => 'commonInput',
                )); ?>
            </td>
            <th  class="td_id" >未確認</th>
            <td>
                <?php echo $this->Form->input('Search.status', array(
                    'type' => 'select',
                    'empty' => __d('default', 'SELECT_ALL'),
                    'options' =>  array(0 => '未確認', 1 => '公開', 2 => '非公開'),
                    'label' => false,
                    'value' => (isset($search['status']) && $search['status'] != '') ? $search['status'] : '',
                    'div' => false,
                    'class' => 'commonInput',
                )); ?>
            </td>
            <th  class="td_id" >登録日</th>
            <td>
                <?php echo $this->Form->input('Search.start', array(
                    'type' => 'text',
                    'label' => false,
                    'value' => !empty($search['start']) ? date('Y/m/d H:i', strtotime($search['start'])) : date('Y/m/d H:i'),
                    'div' => false,
                    'class' => 'datetimepicker commonInput',
                )); ?>~
                <?php echo $this->Form->input('Search.end', array(
                    'type' => 'text',
                    'label' => false,
                    'value' => !empty($search['end']) ? date('Y/m/d H:i', strtotime($search['end'])) : date('Y/m/d H:i'),
                    'div' => false,
                    'class' => 'datetimepicker commonInput',
                )); ?>
            </td>

            <?php if (!empty($isZeiri)): ?>
            <th  class="td_id" >今回合格</th>
            <td>
                <?php echo $this->Form->input('Search.zeirishi2', array(
                    'type' => 'select',
                    'empty' => '全て',
                    'options' =>  array(1=>'合格', 2=>'不合格'),
                    'label' => false,
                    'value' => (isset($search['zeirishi2']) && $search['zeirishi2'] != '') ? $search['zeirishi2'] : '',
                    'div' => false,
                    'class' => 'commonInput',
                )); ?>
            </td>
            <?php endif; ?>

            <!--<th  class="td_id" ></th>-->
            <td><?php echo $this->Form->submit('抽出', array('name'=> 'index','style' => 'margin-bottom: 20px;')); ?>
            <?php echo $this->Form->end();   ?></td>
        </tr>

    </table>


<div class="table-scroll-host">
<table class="userData">

    <?php
        $this->Paginator->setTemplates([
            'nextActive' => '<span class="next"><a rel="next" href="{{url}}">{{text}}</a></span>',
            'nextDisabled' => '<span class="next disabled">{{text}}</a></span>',
            'prevActive' => '<span class="prev"><a rel="prev" href="{{url}}">{{text}}</a></span>',
            'prevDisabled' => '<span class="prev disabled">{{text}}</a></span>',
            'first' => '<span class="first"><a href="{{url}}">{{text}}</a></span>',
            'last' => '<span class="last"><a href="{{url}}">{{text}}</a></span>',
            'number' => '<span><a href="{{url}}">{{text}}</a></span>',
            'current' => '<span class><span>{{text}}</span></span>',
            'sort' => '<a href="{{url}}&page=' . $this->Paginator->current() . '">{{text}}</a>',
            'sortAsc' => '<a class="asc" href="{{url}}&page=' . $this->Paginator->current() . '">{{text}}</a>',
            'sortDesc' => '<a class="desc" href="{{url}}&page=' . $this->Paginator->current() . '">{{text}}</a>',
        ]);
    ?>
    <tr>
        <th ><?php echo $this->Paginator->sort('id', 'ID'); ?></th>
        <th><?php echo $this->Paginator->sort('fix', 'ピン'); ?></th>
        <?php if (in_array(TACNUMBER, $createdPartLists) ): ?>
        <th ><?php echo $this->Paginator->sort('tac_number', '会員番号'); ?></th>
        <?php endif; ?>
        <?php if (in_array(NAME, $createdPartLists) ): ?>
        <th><?php echo $this->Paginator->sort('sei', '名前'); ?></th>
        <?php endif; ?>
        <?php if (in_array(FURIGANA, $createdPartLists) ): ?>
        <th><?php echo $this->Paginator->sort('mei', 'フリガナ'); ?></th>
        <?php endif; ?>
        <?php if (in_array(BIRTHDAY, $createdPartLists) ): ?>
        <th><?php echo $this->Paginator->sort('birthday', '生年月日'); ?></th>
        <?php endif; ?>
        <?php if (in_array(MAIL, $createdPartLists) ): ?>
        <th><?php echo $this->Paginator->sort('mail_address', 'メールアドレス'); ?></th>
        <?php endif; ?>
        <?php if (in_array(PHOTO, $createdPartLists) ): ?>
        <th><?php echo $this->Paginator->sort('photo', '写真'); ?></th>
        <?php endif; ?>
        <?php if (in_array(FURIGANA, $createdPartLists) ): ?>
        <th><?php echo $this->Paginator->sort('initial_name', 'イニシャル'); ?></th>
        <?php endif; ?>
        <?php if (in_array(RELEASE, $createdPartLists) ): ?>
        <th><?php echo $this->Paginator->sort('release', '掲載'); ?></th>
        <?php endif; ?>
        <th><?php echo $this->Paginator->sort('status', '公開状態'); ?></th>
        <?php if (in_array(PHOTO, $createdPartLists) ): ?>
        <th><?php echo $this->Paginator->sort('show_photo', '写真公開'); ?></th>
        <?php endif; ?>
        <?php if (!empty($isZeiri)): ?>
        <th>今回受験科目</th>
        <th>今回合格科目</th>
        <th>合格済科目</th>
        <th>TAC受講履歴のある科目</th>
        <th>次回受験予定の科目</th>
        <?php endif; ?>
        <th><?php echo $this->Paginator->sort('created', '登録日'); ?></th>
        <th><?php echo $this->Paginator->sort('modified', '更新日'); ?></th>

        <?php foreach ($formDatas as $key => $formData): ?>
        <!--<th ><?php //echo $this->Paginator->sort('id', $formData['title_name']); ?></th>-->
        <th><?php echo $this->Csv->mb_wordwrap($formData['title_name'], 7, "<br>", false); ?></th>
        <?php endforeach; ?>
        <th>削除</th>
    </tr>

    <?php //echo $this->Form->create('VoiceUserFormData', array('url' => array('controller' => 'SuccessfulCandidatesControl', 'action' => 'postData'))); ?>
<?php if (!empty($voiceUserFormDatas)):?>
    <?php foreach ($voiceUserFormDatas as $key => $voiceUserFormData): ?>
    <tr>
        <td>
            <?php //echo ($this->params['paging']['VoiceUserFormData']['page'] - 1 ) * $pageLimit + $key + 1; ?>
            <?php echo $voiceUserFormData['VoiceUserFormData']['id'] - $firstId; ?>
            <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.check', array(
                'type' => 'checkbox',
                'div' => false,
                'label' => false,
            ));
            ?>

            <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.id', array(
                'type' => 'hidden',
                'label' => false,
                'value' => !empty($voiceUserFormData['VoiceUserFormData']['id']) ? $voiceUserFormData['VoiceUserFormData']['id'] : '',
                'div' => false,
            )); ?>
        </td>
        <td>
            <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.fix', array(
                'type' => 'select',
                'options' =>  array(0 => '未固定', 1 => '固定'),
                'label' => false,
                'value' => !empty($voiceUserFormData['VoiceUserFormData']['fix']) ? $voiceUserFormData['VoiceUserFormData']['fix'] : 0,
                'div' => false,
                'class' => 'commonInput',
            )); ?>
        </td>
        <?php if (in_array(TACNUMBER, $createdPartLists) ): ?>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['tac_number']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array(NAME, $createdPartLists) ): ?>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['sei']; ?><?php echo $voiceUserFormData['VoiceUserFormData']['mei']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array(FURIGANA, $createdPartLists) ): ?>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['f_sei']; ?><?php echo $voiceUserFormData['VoiceUserFormData']['f_mei']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array(BIRTHDAY, $createdPartLists) ): ?>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['birthday']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array(MAIL, $createdPartLists) ): ?>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['mail_address']; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array(PHOTO, $createdPartLists) ): ?>
        <td>
            <?php if (!empty($voiceUserFormData['VoiceUserFormData']['photo'])): ?>
            <img  width="50" height="62.5" src="<?php echo Configure::read('S3BaseUrl') .  $voiceUserFormData['VoiceUserFormData']['photo']; ?>">
            <?php endif; ?>
        </td>
        <?php endif; ?>
        <?php if (in_array(FURIGANA, $createdPartLists) ): ?>
        <td>
            <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.initial_name', array(
                'type' => 'text',
                'label' => false,
                'value' => !empty($voiceUserFormData['VoiceUserFormData']['initial_name']) ? $voiceUserFormData['VoiceUserFormData']['initial_name'] : '',
                'div' => false,
            )); ?>

        </td>
        <?php endif; ?>
        <?php if (in_array(RELEASE, $createdPartLists) ): ?>
        <td>
            <?php $arr = array(1 => '全て可', 2 => 'イニシャル', 3 => '掲載不可'); ?>
            <?php echo $arr[$voiceUserFormData['VoiceUserFormData']['release']]; ?>
                <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.release', array(
                    'type' => 'hidden',
                    'value' => $voiceUserFormData['VoiceUserFormData']['release'],
                )); ?>

            <?php //echo $this->Form->input('VoiceUserFormData.' . $key.'.release', array(
//                'type' => 'select',
//                'empty' => '全て',
//                'options' =>  array(1=>'すべて可', 2=>'掲載希望', 3=>'掲載不可'),
//                'label' => false,
//                'default' => !empty($voiceUserFormData['VoiceUserFormData']['release']) ? $voiceUserFormData['VoiceUserFormData']['release'] : 1,
//                'div' => false,
//                'class' => 'commonInput',
            //)); ?>

        </td>
        <?php endif; ?>
        <td>
            <?php if ($voiceUserFormData['VoiceUserFormData']['release'] == 3): ?>
                <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.status', array(
                    'type' => 'select',
    //                'empty' => '全て',
                    'options' =>  array(0 => '未確認', 2 => '非公開'),
                    'label' => false,
                    'value' => (($voiceUserFormData['VoiceUserFormData']['release'] == 3) ? 2 : !empty($voiceUserFormData['VoiceUserFormData']['status'])) ? $voiceUserFormData['VoiceUserFormData']['status'] : 0,
                    'div' => false,
                    'class' => 'commonInput',
                )); ?>
            <?php else: ?>
                <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.status', array(
                    'type' => 'select',
    //                'empty' => '全て',
                    'options' =>  array(0 => '未確認', 1 => '公開', 2 => '非公開'),
                    'label' => false,
                    'value' => (($voiceUserFormData['VoiceUserFormData']['release'] == 3) ? 2 : !empty($voiceUserFormData['VoiceUserFormData']['status'])) ? $voiceUserFormData['VoiceUserFormData']['status'] : 0,
                    'div' => false,
                    'class' => 'commonInput',
                )); ?>
            <?php endif; ?>


        </td>
        <?php if (in_array(PHOTO, $createdPartLists) ): ?>
        <td>
            <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.show_photo', array(
                'type' => 'select',
//                'empty' => '全て',
                'options' =>  array(0 => '非公開', 1 => '公開'),
                'label' => false,
                'value' => !empty($voiceUserFormData['VoiceUserFormData']['show_photo']) ? $voiceUserFormData['VoiceUserFormData']['show_photo'] : 0,
                'div' => false,
                'class' => 'commonInput',
            )); ?>
        </td>
        <?php endif; ?>
        <?php if (!empty($isZeiri)): ?>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['zeirishi1'] ?? ''; ?>
        </td>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['zeirishi2'] ?? ''; ?>
        </td>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['zeirishi3'] ?? ''; ?>
        </td>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['zeirishi4'] ?? ''; ?>
        </td>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['zeirishi5'] ?? ''; ?>
        </td>
        <?php endif; ?>

        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['created']->format('Y-m-d H:i:s'); ?>
            <?php echo $this->Form->input('VoiceUserFormData.' . $key.'.created', array(
                'type' => 'hidden',
                'label' => false,
                'value' => !empty($voiceUserFormData['VoiceUserFormData']['created']) ? $voiceUserFormData['VoiceUserFormData']['created']->format('Y-m-d H:i:s') : '',
                'div' => false,
            )); ?>
        </td>
        <td>
            <?php echo $voiceUserFormData['VoiceUserFormData']['modified']->format('Y-m-d H:i:s'); ?>
        </td>
            <?php foreach ($formDatas as $key2 => $formData): ?>
                    <td>
                        <?php if (!empty($voiceUserFormData['VoiceUserFormData'][$formData['id']])): ?>
                            <?php foreach ($voiceUserFormData['VoiceUserFormData'][$formData['id']] as $key3 => $value): ?>
                                <?php if (!in_array($formData['slug'], $selectLists)): ?>
                                    <?php echo $this->Form->input('VoiceUserFormData.' . $key. '.VoiceUserFormDataOption.' . $key3.'.id', array(
                                        'type' => 'hidden',
                                        'label' => false,
                                        'value' => !empty($value['id']) ? $value['id'] : '',
                                        'div' => false,
                                    )); ?>
                                    <?php echo $this->Form->input('VoiceUserFormData.' . $key. '.VoiceUserFormDataOption.' . $key3.'.value', array(
                                        'type' => 'text',
                                        'label' => false,
                                        'value' => !empty($value['value']) ? $value['value'] : '',
                                        'div' => false,
                                    )); ?>
                                <?php else: ?>
                                    <?php echo $value['value'] ?>

                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
            <?php endforeach; ?>
        <td>
         <?php if (!empty($voiceForm['lock']) && ($role == NOMAL)): ?>
        <?php echo $this->Form->postLink(__d('action', 'DELETE'), array('controller'=>'SuccessfulCandidatesControl','action' => 'deleteUserData', $voiceUserFormData['VoiceUserFormData']['id']), array('confirm' => '削除しますか？'));?>
        <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
<?php endif; ?>

</table>
</div>
<div class="submit pb">
    <?php if (!empty($voiceForm['lock']) && ($role == NOMAL)): ?>
        <?php echo $this->Form->button(__d('action', 'SAVE'), array('disabled' => "disabled")); ?>
    <?php else: ?>
        <?php echo $this->Form->submit(__d('action', 'SAVE'), array('name' => 'saveUserFormDatas')); ?>
    <?php endif; ?>


    <?php echo $this->Form->end(); ?>
</div>
<?php
    $this->Paginator->setTemplates([
        'number' => '<span class><a href="{{url}}">{{text}}</a></span> ',
        'current' => '<span>{{text}}<span> ',
    ]);
?>
<?php echo $this->Paginator->first('<< 最初へ'); ?>
<?php echo $this->Paginator->prev('前へ' . __(''), array(), null, array('class' => 'prev disabled')); ?>
<?php echo $this->Paginator->numbers(array('first' => 1, 'last' => 1, 'ellipsis' => ' ... ', 'tag' => 'span', 'currentTag' => 'span', 'currentClass' => '', 'separator' => ' ', 'class' => '')) ?>

<?php echo $this->Paginator->next(__('') . ' 次へ', array(), null, array('class' => 'next disabled')); ?>

<?php echo $this->Paginator->last('最後へ >>'); ?>


<div>
<?php echo $this->Html->link(__d('default', 'BACK'), '/successful_candidates'); ?>
</div>
<script>
  $(function() {
    $('input[name="saveUserFormDatas"]').on('click',function() {
        var providerIds = [];
        $("input[type=checkbox]:checked").each(function(index, value) {
            providerIds.push(value.id);
        });
        if ($.isEmptyObject(providerIds)) {
            alert('<?= __d('successful_candidate_control', 'ERROR_SELECT_USER_TO_EDIT'); ?>');
            return false;
        }

    });
  });
</script>
<script>
  $(function() {
    $(".datetimepicker").datetimepicker({mask:true, format:'Y/m/d H:i'});
  });

    $(function() {

        $('input[name="show_people"]').change(function() {
            var formId = "<?php echo $formId ?>";
            var val =  $('input[name="show_people"]:checked').val();
            if (!val) {
                val = 0;
            }

            var tokenKey = $("input[name='_Token[key]']").val();
            $.ajax({
                url: "successful_candidates_control/changeShowPeople",
                type: "POST",
                cache: false,
                dataType: "json",
                data: {
                    'formId': formId,
                    'val' : val,
                    '_Token': {'key':tokenKey}
                },
                async: true
            }).done(function(data, textStatus, jqXHR) {

            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('<?= __d('successful_candidate_control', 'ERROR_CHECK_NETWORK_ENVIRONMENT'); ?>');
            });
        });

        $('input[name="send_mail"]').change(function() {
            var formId = "<?php echo $formId ?>";
            var val =  $('input[name="send_mail"]:checked').val();
            if (!val) {
                val = 0;
            }

            var tokenKey = $("input[name='_Token[key]']").val();
            $.ajax({
                url: "successful_candidates_control/changeSendMail",
                type: "POST",
                cache: false,
                dataType: "json",
                data: {
                    'formId': formId,
                    'val' : val,
                    '_Token': {'key':tokenKey}
                },
                async: true
            }).done(function(data, textStatus, jqXHR) {
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('<?= __d('successful_candidate_control', 'ERROR_CHECK_NETWORK_ENVIRONMENT'); ?>');
            });
        });

        $('input[name="lock"]').change(function() {
            var formId = "<?php echo $formId ?>";
            var val =  $('input[name="lock"]:checked').val();
            if (!val) {
                val = 0;
            }
            var tokenKey = $("input[name='_Token[key]']").val();
            $.ajax({
                url: "successful_candidates_control/changeLock",
                type: "POST",
                cache: false,
                dataType: "json",
                data: {
                    'formId': formId,
                    'val' : val,
                    '_Token': {'key':tokenKey}
                },
                async: true
            }).done(function(data, textStatus, jqXHR) {

            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert('<?= __d('successful_candidate_control', 'ERROR_CHECK_NETWORK_ENVIRONMENT'); ?>');
            });
        });
    });
</script>
