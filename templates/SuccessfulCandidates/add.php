<div class="mini_inner">
<section class="commonSection">
<h1 class="heading_primary"><?= __d('successful_candidate', 'USER_REGISTRATION'); ?></h1>
    <?php $this->Form->setTemplates([
            'inputContainer' => '{{content}}',
            'inputContainerError' => '{{content}}{{error}}'
        ]);
    ?>
    <dl class="commonDl edit inner clearfix">
        <?= $this->Flash->render('auth');?>
        <?php
            echo $this->Form->create($user);
            $this->Form->setConfig('autoSetCustomValidity', false);
        ?>
        <?php echo $this->Form->control('id', array('type' => 'hidden', 'value' => $rawData['id'])); ?>
        <dt class="require"><?= __d('successful_candidate', 'USER_ID'); ?></dt>
        <dd><?php echo $this->Form->control('username', array('type' => 'text', 'maxlength' => false, 'label' => false,'class' => 'commonInput block', 'div' => false, 'value' => $rawData['username'], 'required' => true)); ?>
            <p class="notice">ユーザーIDは固有のID(英数字と[_][-][.][@]のみ使用可能)にしてください</p>
        </dd>
        <dt class="require"><?= __d('successful_candidate', 'PASSWORD'); ?></dt>
            <dd><?php echo $this->Form->control('password', array('type' => 'password', 'label' => false, 'class' => 'commonInput block', 'div' => false, 'required' => true)); ?>
                <p class="notice">※パスワード半角英数字と[_][-][.][@]のみ使用可能です。</p>
            </dd>

        <dt><?= __d('successful_candidate', 'AUTHORITY'); ?></dt>
        <dd class="require">
            <?php echo $this->Form->control('role_id', array('type' => 'select','options' => $roleLists, 'label' => false,'div' => false, 'class' => 'commonRole' ,'value' => $rawData['role_id'])); ?>
        </dd>
        <dt><?= __d('successful_candidate', 'COURSE'); ?></dt>
        <dd class="require">
            <?php echo $this->Form->control('category_id', array('type' => 'select','options' => $categoryLists, 'label' => false,'div' => false, 'class' => 'commonRole','value' => $rawData['category_id'], 'empty' => __d('successful_candidate', 'ALL_COURSES'))); ?>
        </dd>
    </dl>

    <div class="submit pb"><?php echo $this->Form->submit(__d('default', 'REGISTRATION'), array('class' => "commonInputBtn")); ?></div>
    <?php echo $this->Form->end(); ?>

</section>

</div>
