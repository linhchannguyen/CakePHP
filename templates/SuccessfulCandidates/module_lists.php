<h2><?= __d('successful_candidate', 'ADD_MODULE'); ?></h2>
<?= __d('successful_candidate', 'PUBLIC_MODULE_REQUIRED'); ?>

<h3><?= __d('successful_candidate', 'FIXED_MODULE'); ?></h3>

<li class="nav_item"><?= __d('successful_candidate', 'MEMBERSHIP_NUMBER_MODULE'); ?><?php if (empty($moduleLists[TACNUMBER])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => TACNUMBER, 'fix' => 1, 'formId' => $formId))); ?><?php endif; ?></li>


<li class="nav_item"><?= __d('successful_candidate', 'NAME_MODULE'); ?><?php if (empty($moduleLists[NAME])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => NAME, 'fix' => 1, 'formId' => $formId))); ?><?php endif; ?></li>


<li class="nav_item"><?= __d('successful_candidate', 'FURIGANA_MODULE'); ?><?php if (empty($moduleLists[FURIGANA])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => FURIGANA, 'fix' => 1, 'formId' => $formId))); ?><?php endif; ?></li>


<li class="nav_item"><?= __d('successful_candidate', 'DATE_OF_BIRTH_MODULE'); ?><?php if (empty($moduleLists[BIRTHDAY])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => BIRTHDAY, 'fix' => 1, 'formId' => $formId))); ?><?php endif; ?></li>


<li class="nav_item"><?= __d('successful_candidate', 'EMAIL_ADDRESS_MODULE'); ?><?php if (empty($moduleLists[MAIL])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => MAIL, 'fix' => 1, 'formId' => $formId))); ?><?php endif; ?></li>


<li class="nav_item"><?= __d('successful_candidate', 'PUBLIC_PRIVATE_MODULE'); ?><?php if (empty($moduleLists[RELEASE])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => RELEASE, 'fix' => 1, 'formId' => $formId))); ?><?php endif; ?></li>
<li class="nav_item"><?= __d('successful_candidate', 'PUBLIC_ANONYMOUS_PRIVATE_MODULE'); ?><?php if (empty($moduleLists[RELEASE])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => RELEASE, 'fix' => 1, 'formId' => $formId, 'initial' => 1))); ?><?php endif; ?></li>


<li class="nav_item"><?= __d('successful_candidate', 'PHOTO_UPLOAD_MODULE'); ?><?php if (empty($moduleLists[PHOTO])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => PHOTO, 'fix' => 1, 'formId' => $formId))); ?><?php endif; ?></li>


<li class="nav_item"><?= __d('successful_candidate', 'TAX_ACCOUNTANT_LIST_MODULE'); ?><?php if (empty($moduleLists[ZEIRISHI])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => ZEIRISHI, 'fix' => 1, 'formId' => $formId))); ?><?php endif; ?></li>

<li class="nav_item"><?= __d('successful_candidate', 'TAX_ACCOUNTANT_SUBJECT_MODULE'); ?><?php if (empty($moduleLists[ZEIRISHI_KAMOKU])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => ZEIRISHI_KAMOKU, 'fix' => 0, 'formId' => $formId))); ?><?php endif; ?></li>

<li class="nav_item"><?= __d('successful_candidate', 'STUDY_AREA_TEACHER_MODULE'); ?><?php if (empty($moduleLists[JYUKENTIKU1])): ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => JYUKENTIKU1, 'fix' => 0, 'formId' => $formId))); ?><?php endif; ?></li>



<h3><?= __d('successful_candidate', 'GENERAL_PURPOSE_MODULE'); ?></h3>
<li class="nav_item"><?= __d('successful_candidate', 'SINGLE_BYTE_ALPHANUMERIC_TEXT_MODULE'); ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => ALPHABET, 'fix' => 0, 'formId' => $formId))); ?></li>
<li class="nav_item"><?= __d('successful_candidate', 'RADIO_MODULE'); ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => RADIO, 'fix' => 0, 'formId' => $formId))); ?></li>
<li class="nav_item"><?= __d('successful_candidate', 'CHECKBOX_MODULE'); ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => CHECKBOX, 'fix' => 0, 'formId' => $formId))); ?></li>
<li class="nav_item"><?= __d('successful_candidate', 'LIST_MODULE'); ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => 'LIST', 'fix' => 0, 'formId' => $formId))); ?></li>
<li class="nav_item"><?= __d('successful_candidate', 'FREE_COMMENT_MODULE'); ?><?php echo $this->Html->link(__d('successful_candidate', 'ADDITION'), array('controller' => 'SuccessfulCandidates','action' => 'editModule' , '?' => array('moduleSlug' => FREECOMMENT, 'fix' => 0, 'formId' => $formId))); ?></li>

<div>
<?php echo $this->Html->link(__('BACK'), array('controller' => 'SuccessfulCandidates','action' => 'editForm', '?' => array('formId' => $formId))); ?>
</div>
