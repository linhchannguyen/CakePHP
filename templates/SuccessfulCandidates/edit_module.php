<h2><?php echo $form['name'] . ' ' . __d('successful_candidate', 'ADD_MODULE'); ?></h2>
<?php echo $this->Form->create(null, array('url' => '/successful_candidates/editModule')); ?>
<table class="formTable">

<!--<dl class="commonDl edit inner clearfix">-->
    <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' =>  !empty($voiceModule['id'])? $voiceModule['id'] : null));?>
    <?php echo $this->Form->input('slug', array('type' => 'hidden', 'value' => $moduleSlug));?>
    <?php echo $this->Form->input('form_id', array('type' => 'hidden', 'value' => $formId));?>
    <?php echo $this->Form->input('fix_form', array('type' => 'hidden', 'value' => !empty($fix)? $fix : 0));?>
    <?php if (!empty($initial)): ?>
    <?php echo $this->Form->input('initial', array('type' => 'hidden', 'value' => $initial));?>
    <?php else: ?>
    <?php echo $this->Form->input('initial', array('type' => 'hidden', 'value' => !empty($voiceModule['initial'])? $voiceModule['initial'] : 0));?>
    <?php endif; ?>
    <tr>
    <th><?= __d('successful_candidate', 'HEADLINE_NAME'); ?></th>
    <td><?php echo $this->Form->input('title_name', array('type' => 'text','required' => true,'label' => false, 'value' => !empty($voiceModule['title_name'])? $voiceModule['title_name'] : '', 'div' => false, 'class' => 'commonInput block')); ?></td>
    </tr>
    <tr>
    <th><?= __d('successful_candidate', 'HEADING_LEVEL'); ?></th>
    <td>
        <?php
            $this->Form->setTemplates([
                'nestingLabel' => '{{input}}{{text}}</br>',
                'inputContainer' => '{{content}}'
            ]);

            echo $this->Form->control('head_id', array('type' => 'radio', 'options' => $headLineLists, 'label' => false, 'hiddenField' => false,
                'default' => !empty($voiceModule['head_id'])? $voiceModule['head_id'] : 1, 'div' => false, 'class' => 'commonCheck'
        ));
        ?>
        <!--<label for="VoicePartHeadId"></label>-->
    </td>
    </tr>
    <tr>
    <th><?= __d('successful_candidate', 'TOP_TEXT'); ?></th>
    <td><?php echo $this->Form->input('textbox1', array('type' => 'text','label' => false, 'value' => !empty($voiceModule['textbox1'])?  $voiceModule['textbox1'] : '', 'div' => false, 'class' => 'commonInput block')); ?></td>
    </tr>
    <tr>
    <th><?= __d('successful_candidate', 'BOTTOM_TEXT'); ?></th>
    <td><?php echo $this->Form->input('textbox2', array('type' => 'text','label' => false, 'value' => !empty($voiceModule['textbox2'])? $voiceModule['textbox2'] : '', 'div' => false, 'class' => 'commonInput block')); ?></td>
    </tr>
    <tr>
    <?php if (!in_array($moduleSlug, array(PERSONALINFORMATION))): ?>
    <th><?= __d('successful_candidate', 'MAKE_IT_REQUIRED'); ?></th>
    <td>
        <?php echo $this->Form->control('required', array('type' => 'checkbox', 'label' => false, 'checked' => !empty($voiceModule['required'])? $voiceModule['required'] : 0,'div' => false, 'class' => 'commonCheck')); ?>
        <label for="VoicePartRequired"></label>
    </td>
    </tr>
    <tr>
    <th><?= __d('successful_candidate', 'BROWSING_PAGE_PRIVATE'); ?></th>
    <td>
        <?php echo $this->Form->control('hidden', array('type' => 'checkbox', 'label' => false, 'checked' => !empty($voiceModule['hidden'])? $voiceModule['hidden'] : 0,'div' => false, 'class' => 'commonCheck', 'id' => 'VoicePartHidden')); ?>
        <label for="VoicePartHidden"></label>
    </td>
    </tr>
    <tr>
    <th><?= __d('successful_candidate', 'FORM_PAGE_DISPLAY_ORDER') . '  '. __d('successful_candidate', 'NUMBER_ONLY'); ?></th>
    <td><?php echo $this->Form->input('form_display_order', array('type' => 'text','required' => true,'label' => false, 'value' => !empty($voiceModule['form_display_order'])? $voiceModule['form_display_order'] : 99, 'div' => false, 'class' => 'commonInput block')); ?></td>
    </tr>
    <tr>
    <th><?= __d('successful_candidate', 'USER_VIEW_PAGE_DISPLAY_ORDER') . '  '. __d('successful_candidate', 'NUMBER_ONLY'); ?></th>
    <td><?php echo $this->Form->input('public_display_order', array('type' => 'text','required' => true,'label' => false, 'value' => !empty($voiceModule['public_display_order'])? $voiceModule['public_display_order'] : 99, 'div' => false, 'class' => 'commonInput block')); ?></td>
    </tr>

    <?php endif; ?>

    <?php if (in_array($moduleSlug, array(ZEIRISHI))): ?>
        <?php if (!empty($zeirishiLists)):?>
            <?php foreach ($zeirishiLists as $key => $value): ?>
            <?php echo $this->Form->input('VoicePartOption.value.', array('type' => 'hidden','label' => false, 'value' => $value)); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (in_array($moduleSlug, array(ZEIRISHI_KAMOKU))): ?>
        <?php if (!empty($zeirishiKamokuLists)):?>
            <?php foreach ($zeirishiKamokuLists as $key => $value): ?>
            <?php echo $this->Form->input('VoicePartOption.value.', array('type' => 'hidden','label' => false, 'value' => $value)); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (in_array($moduleSlug, array(RELEASE))): ?>
        <?php if (!empty($releaseiLists)):?>
            <?php foreach ($releaseiLists as $key => $value): ?>
            <?php echo $this->Form->input('VoicePartOption.value.'.($key-1), array('type' => 'hidden','label' => false, 'value' => $value)); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (in_array($moduleSlug, array(JYUKENTIKU1))): ?>
        <?php if (!empty($jyukentikuLists)):?>
            <?php foreach ($jyukentikuLists as $key => $value): ?>
            <?php echo $this->Form->input('VoicePartOption.value.', array('type' => 'hidden','label' => false, 'value' => $value)); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (in_array($moduleSlug, array(MAIL, BIRTHDAY))): ?>
    <tr>
    <th><?= __d('successful_candidate', 'INITIAL_VALUE'); ?></th>
    <td><?php echo $this->Form->input('place_holder', array('type' => 'text','label' => false, 'value' => !empty($voiceModule['place_holder'])?  $voiceModule['place_holder'] : '', 'div' => false, 'class' => 'commonInput block')); ?></td>
    </tr>
    <?php endif; ?>

    <?php if ($fix == 0): ?>
        <?php echo $this->element('SuccessfulCandidates/wide_use_form'); ?>
    <?php endif; ?>
</table>
    <div class="submit pb">
        <?php echo $this->Form->submit(__d('successful_candidate', 'SAVE'), array('class' => 'commonBtn')); ?>

    <?php echo $this->Form->end(); ?>
    </div>
<!--</dl>-->
<script>
$('form').submit(function() {
    var maxCount = "<?php echo MAX_COUNT ?>";
    var showModule = "<?php echo $showModule ?>";
    if ($("#VoicePartHidden").prop("checked") == false) {
        if (parseInt(maxCount) < (parseInt(showModule) + 1)) {
            alert('閲覧ページに表示できるのは' + maxCount + '個のモジュールまでです。');
            return false;
        }
    }
});
</script>
<div>
<?php echo $this->Html->link(__d('default', 'BACK'), array('controller' => 'SuccessfulCandidates','action' => 'editForm', '?' => array('formId' => $formId))); ?>
</div>
