<h2><?= __d('successful_candidate', 'USER_LIST'); ?></h2>
<?php echo $this->Html->link(__d('successful_candidate', 'USER_REGISTRATION'), array('controller' => 'SuccessfulCandidates', 'action' => 'add'),array('class' => '')); ?>
<table>

    <?php
        $this->Paginator->setTemplates([
            'nextActive' => '<span class="next"><a rel="next" href="{{url}}">{{text}}</a></span>',
            'nextDisabled' => '<span class="next disabled">{{text}}</a></span>',
            'prevActive' => '<span class="prev"><a rel="prev" href="{{url}}">{{text}}</a></span>',
            'prevDisabled' => '<span class="prev disabled">{{text}}</a></span>',
            'first' => '<span class="first"><a href="{{url}}">{{text}}</a></span>',
            'last' => '<span class="last"><a href="{{url}}">{{text}}</a></span>',
            'sort' => '<a href="{{url}}&page=' . $this->Paginator->current() . '">{{text}}</a>',
            'sortAsc' => '<a class="asc" href="{{url}}&page=' . $this->Paginator->current() . '">{{text}}</a>',
            'sortDesc' => '<a class="desc" href="{{url}}&page=' . $this->Paginator->current() . '">{{text}}</a>',
        ]);
    ?>
    <tr>
        <th><?php echo $this->Paginator->sort('id', 'ID'); ?></th>
        <th ><?php echo $this->Paginator->sort('username', __d('successful_candidate', 'EMAIL_ADDRESS')); ?></th>
        <th ><?php echo $this->Paginator->sort('role_id',  __d('successful_candidate', 'AUTHORITY')); ?></th>
        <th ><?php echo $this->Paginator->sort('category_id', __d('successful_candidate', 'COURSE_NAME')); ?></th>
        <th ><?php echo $this->Paginator->sort('created', __d('successful_candidate', 'REGISTED_DATE')); ?></th>
        <th ><?php echo $this->Paginator->sort('modified', __d('successful_candidate', 'UPDATE_DATE')); ?></th>
        <th><?= __d('successful_candidate', 'ACTION'); ?></th>
    </tr>

    <?php if (!empty($users)):?>
        <?php foreach ($users as $key => $user): ?>
        <tr>
            <td>
                <?php echo $user['id']; ?>
            </td>
            <td>
                <?php echo $user['username']; ?>
            </td>
            <td>
                <?php echo $roleLists[$user['role_id']]; ?>
            </td>
            <td>
                <?php echo $categoryLists[$user['category_id']] ?? ''; ?>
            </td>
            <td>
                <?php echo date('Y-m-d', strtotime($user['created'])); ?>
            </td>
            <td>
                <?php echo date('Y-m-d', strtotime($user['modified'])); ?>
            </td>
            <td>
                <?php echo $this->Form->postLink(__d('action', 'EDIT'), array('action' => 'add', $user['id']));?>
                <?php echo $this->Form->postLink(__d('action', 'DELETE'), array('action' => 'delete_voice_user', $user['id']), array('confirm' => __d('successful_candidate', 'COMFIRM_DELETE')));?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>

</table>
<?php echo $this->Paginator->first('<< 最初へ'); ?>
<?php echo $this->Paginator->prev('前へ' . __(''), array(), null, array('class' => 'prev disabled')); ?>
<?php echo $this->Paginator->next(__('') . ' 次へ', array(), null, array('class' => 'next disabled')); ?>
<?php echo $this->Paginator->last('最後へ >>'); ?>
<?php echo $this->Paginator->counter(__('{{page}}/{{pages}}ページ')); ?>
<div>
    <?php echo $this->Html->link(__d('default', 'BACK'), '/successful_candidates'); ?>
</div>
