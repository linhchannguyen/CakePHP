<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Database\StatementInterface $error
 * @var string $message
 * @var string $url
 */
use Cake\Core\Configure;

?>
<h2><?php echo 'An Internal Error Has Occurred.'; ?></h2>
<p class="error">
	<strong><?= __d('cake', 'Error'); ?>: </strong>
	<?= __d('cake', 'An Internal Error Has Occurred.'); ?>
</p>
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
?>
