<h2><?= __d('successful_candidate', 'COURSE_LIST') ?></h2>
<?php echo $this->Html->link(__d('successful_candidate', 'ADD_CATEGORY'), array('controller' => 'SuccessfulCandidates', 'action' => 'addCategory'),array('class' => '')); ?>
<table>
    <tr>
        <th>ID</th>
        <th ><?= __d('successful_candidate', 'COURSE_NAME') ?></th>
        <th ><?= __d('successful_candidate', 'REGISTED_DATE') ?></th>
        <th ><?= __d('successful_candidate', 'UPDATE_DATE') ?></th>
        <th><?= __d('successful_candidate', 'ACTION') ?></th>
    </tr>


<?php if (!empty($categoryLists)):?>
    <?php foreach ($categoryLists as $key => $category): ?>
    <tr>
        <td>
            <?php echo $category['id']; ?>
        </td>
        <td>
            <?php echo $category['name']; ?>
        </td>
        <td>
            <?php echo date('Y-m-d', strtotime($category['created'])); ?>
        </td>
        <td>
            <?php echo date('Y-m-d', strtotime($category['modified'])); ?>
        </td>
        <td>
            <?php echo $this->Form->postLink(__d('action', 'EDIT'), array('controller' => 'SuccessfulCandidates', 'action' => 'addCategory', $category['id']));?>
            <?php echo $this->Form->postLink(__d('action', 'DELETE'), array('controller' => 'SuccessfulCandidates', 'action' => 'delete_category', $category['id']), array('confirm' => __d('successful_candidate', 'COMFIRM_DELETE')));?>
        </td>
    </tr>
    <?php endforeach; ?>
<?php endif; ?>

</table>
<div>
<?php echo $this->Html->link(__d('default', 'BACK'), '/successful_candidates'); ?>
</div>
