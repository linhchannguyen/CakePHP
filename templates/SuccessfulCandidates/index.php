<h2><?php echo isset($category['name']) ? $category['name'] : '' ?> フォーム一覧</h2>
<?php if ($role != NOMAL): ?>
    <p><?php echo $this->Html->link(__d('successful_candidate', 'ADD_FORM'), array('controller' => 'SuccessfulCandidates','action' => 'editForm')); ?></p>
<?php endif; ?>


<table>
    <tr>
        <th>ID</th>
        <th >フォーム名</th>
        <th>アクション</th>
    </tr>


<?php foreach ($voiceForms as $key => $voiceForm): ?>

    <tr>
        <td>
            <?php echo $voiceForm['id']; ?>
        </td>
        <td>
            <?php echo $voiceForm['name'] ?>
        </td>
        <td>
        <?php echo $this->Html->link(__d('successful_candidate', 'PUBLISHING_CONTROL'), '/successful_candidates_control?formId=' . $voiceForm['id']); ?>
        <?php if ($role != NOMAL): ?>
            <?php echo $this->Html->link(__d('successful_candidate', 'EDIT'), array('controller' => 'SuccessfulCandidates','action' => 'editForm', '?' => array('formId' => $voiceForm['id']))); ?>
            <?php //echo $this->Html->link('削除', array('controller' => 'SuccessfulCandidates','action' => 'deleteForm', '?' => array('formId' => $voiceForm['id'])), array('confirm' => '削除しますか？')); ?>
        <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
