
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->Html->css('main');
        echo $this->Html->css('jscrollpane.css');
        echo $this->Html->script('jquery-2.2.2.min.js');
        echo $this->Html->script('jquery.jscrollpane.js');
        echo $this->Html->script('jquery.datetimepicker.js');
        echo $this->Html->script('jquery.gpfloat-1.0.js');
        echo $this->Html->script('jquery.gpfloat-1.0.min.js');
        echo $this->Html->css('jquery.datetimepicker.css');
		echo $this->Html->css('cake.generic');
        echo $this->Html->css('admin');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header"></div>
		<div id="content">

			<?php echo $this->Session->flash(); ?>
            <?php echo $this->Html->link(__('LOGOUT'), array('controller' => 'SuccessfulCandidates', 'action' => 'logout'),array('class' => 'edit_link')); ?>
            <?php if (!empty($self)): ?>
                <?php if ($self['role_id'] == HIGHEST ): ?>
                    <?php echo $this->Html->link(__d('successful_candidate', 'USER_LIST'), array('controller' => 'SuccessfulCandidates', 'action' => 'userLists'),array('class' => 'edit_link')); ?>
                    <?php echo $this->Html->link(__d('successful_candidate', 'COURSE_LIST'), array('controller' => 'SuccessfulCandidates', 'action' => 'categoryLists'),array('class' => 'edit_link')); ?>
                <?php endif; ?>
            <?php endif; ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer"></div>
	</div>
</body>
</html>
