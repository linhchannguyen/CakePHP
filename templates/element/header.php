<div id="pageHeader">
	<h2 id="titlehed">拠点ページ管理システム</h2>
	<h1 id="title">
		<?php if ($this->getResponse()->getStatusCode() == 500) {
			echo __d('events', 'TITLE_HEAD_ERROR');
		} else {
			echo $title_head ?? '管理TOP';
		} ?>
	</h1>
</div>