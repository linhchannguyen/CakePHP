<div class="mini_inner">
<section class="commonSection">

<h1 class="heading_primary"><?= __d('successful_candidate', 'COURSE_REGISTRATION'); ?></h1>
    <dl class="commonDl edit inner clearfix">
    <?php echo $this->Form->create(); ?>
    <?php echo $this->Form->input('id',array('type' => 'hidden', 'value' => $categoryLists['id'])); ?>
    <dt class="require"><?= __d('successful_candidate', 'COURSE_NAME'); ?></dt>
    <dd><?php echo $this->Form->input('name',array('type' => 'text', 'label' => false,'class' => 'commonInput block','div' => false, 'value' => $categoryLists['name'], 'required' => true)); ?>
    </dd>
    </dl>
    <div class="submit pb"><?php echo $this->Form->submit( __d('successful_candidate', 'REGISTRATION'), array('class' => "commonInputBtn")); ?></div>
    <?php echo $this->Form->end(); ?>

</section>

</div>
