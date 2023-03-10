<h2><?php echo $category['name'] ?> <?= __d('successful_candidate', 'TITLE_ADD_EDIT_FORM'); ?></h2>

<table class="formTable">
<?php echo $this->Form->create(null , array('action' => 'editForm')); ?>
    <tr>
    <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $voiceForm['id']));?>
    <th><?= __d('successful_candidate', 'LABEL_FORM_ID'); ?></th>
    <td><?php echo $voiceForm['id'];?></td>
    </tr>
    <tr>
    <th><?= __d('successful_candidate', 'LABEL_FORM_NAME'); ?></th>
    <td><?php echo $this->Form->input('name', array('type' => 'text','label' => false, 'value' => $voiceForm['name'], 'div' => false, 'class' => 'commonInput block', 'required' => true)); ?><td>
    </tr>
    <?php echo $this->Form->input('category_id', array('type' => 'hidden', 'value' => $voiceForm['category_id']));?>
    <?php echo $this->Form->input('lock', array('type' => 'hidden', 'value' => $voiceForm['lock']));?>
    <?php echo $this->Form->input('show_people', array('type' => 'hidden', 'value' => $voiceForm['show_people']));?>
    <?php echo $this->Form->input('send_mail', array('type' => 'hidden', 'value' => $voiceForm['send_mail']));?>
</table>
<div class="submit pb">
    <?php echo $this->Form->submit(__d('successful_candidate', 'SAVE'), array('class' => 'commonBtn')); ?>
</div>
<?php echo $this->Form->end(); ?>



<h2><?= __d('successful_candidate', 'TITLE_LIST_OF_REGISTERED_MODULES'); ?></h2>
<p><?= __d('successful_candidate', 'MAXIMUM_DISPLAY_MODULES', MAX_COUNT); ?></p>

<p><?= __d('successful_candidate', 'PUBLIC_MODULE_REQUIRED'); ?></p>

<p><?php echo $this->Html->link(__d('successful_candidate', 'ADD_MODULE'), array('controller' => 'SuccessfulCandidates','action' => 'moduleLists', '?' => array('formId' => $voiceForm['id']))); ?></p>

<table>
    <tr>
        <th>ID</th>
        <th ><?= __d('successful_candidate', 'MODULE_NAME'); ?></th>
        <th ><?= __d('successful_candidate', 'MODULE_TYPE'); ?></th>
        <th ><?= __d('successful_candidate', 'INPUT_REQUIRED'); ?></th>
        <th ><?= __d('successful_candidate', 'SHOW_ON_BROWSER_PAGE'); ?></th>
        <th ><?= __d('successful_candidate', 'FORM_DISPLAY_ORDER'); ?></th>
        <th ><?= __d('successful_candidate', 'VIEWED_PAGE_DISPLAY_ORDER'); ?></th>
        <th><?= __d('successful_candidate', 'ACTION'); ?></th>
    </tr>

    <?php if (!empty($voiceForm['voice_parts'][0])): ?>
        <?php foreach ($voiceForm['voice_parts'] as $key => $voiceParts): ?>
            <?php if (!in_array($voiceParts['slug'], array(JYUKENTIKU2,JYUKENTIKU3,JYUKENTIKU_TEXT1,JYUKENTIKU_TEXT2,JYUKENTIKU_TEXT3) )): ?>

            <tr>
                <td>
                    <?php echo $voiceParts['id'] ?>
                </td>
                <td>
                    <?php echo $voiceParts['title_name'] ?>
                </td>
                <td>
                <?php if (!empty($voiceParts['fix_form']) || $voiceParts['slug'] == JYUKENTIKU1 || $voiceParts['slug'] == ZEIRISHI_KAMOKU): ?>
                <?= __d('successful_candidate', 'FIXED_PREFIX'); ?>
                <?php else: ?>
                <?= __d('successful_candidate', 'GENERAL_PURPOSE_PREFIX'); ?>
                <?php endif; ?>
                <?php echo $voiceParts['slug'] ?>
                </td>
                <td>
                    <?php if (!empty($voiceParts['required'])): ?>
                    ○
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (empty($voiceParts['hidden'])): ?>
                    ○
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $voiceParts['form_display_order'] ?>
                </td>
                <td>
                    <?php echo $voiceParts['public_display_order'] ?>
                </td>
                <td>
                <?php echo $this->Html->link(__d('successful_candidate', 'EDIT'), array('controller' => 'SuccessfulCandidates','action' => 'editModule', '?' => array('partId' => $voiceParts['id'], 'fix' => $voiceParts['fix_form'], 'initial' => $voiceParts['initial']))); ?>

                <?php echo $this->Html->link(__d('successful_candidate', 'DELETE'), array('controller' => 'SuccessfulCandidates','action' => 'deleteModule', '?' => array('partId' => $voiceParts['id'])), array('confirm' => __d('successful_candidate', 'COMFIRM_DELETE'))); ?>
                </td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<div>
<?php echo $this->Html->link(__d('default', 'BACK'), '/successful_candidates'); ?>
</div>
