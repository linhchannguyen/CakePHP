<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
    <head>
        <?= $this->Html->charset(); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>拠点ページ管理システム</title>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
        <?= $this->Html->script(['jquery-1.6.0.min.js', 'admin/js/admin.js']) ?>
        <?= $this->Html->css(['admin_custom/css/admin.css']) ?>
    </head>
    <body>
        <div id="container">
            <div id="header"></div>
            <div id="content">
                <div id="pageContainer">
                    <?= $this->element('header'); ?>
                    <?= $this->element('sidebar'); ?>
                    <div id="mainContents">
                        <?php if(isset($error_messages) && !empty($error_messages)): ?>
                            <ul>
                                <?php foreach($error_messages as $rm): ?>
                                    <li><?= $rm ?></li>
                                <?php endforeach ?>
                            </ul>
                            <?php if(isset($return_url)): ?>
                                <p>
                                    <a href="<?= $this->Url->build($return_url, ['fullBase' => true]) ?>">
                                        <?= __d('csv_form', 'CSV_RETURN_BTN') ?>
                                    </a>
                                </p>
                            <?php endif ?>
                        <?php else: ?>
                            <?= $this->fetch('content'); ?>
                        <?php endif ?>
                    </div>
                    <?= $this->element('footer'); ?>
                </div>
            </div>
        </div>
    </body>
</html>