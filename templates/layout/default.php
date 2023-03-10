<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
    <head>
        <?= $this->Html->charset(); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>拠点ページ管理システム</title>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
        <?= $this->Html->script('jquery-2.2.2.min.js') ?>
        <?= $this->Html->script('jquery.jscrollpane.js') ?>
        <?= $this->Html->css(['main', 'jscrollpane.css', 'admin/css/admin.css', 'admin/css/setting.css', 'admin/css/yui/reset.css', 'admin/css/yui/fonts.css']) ?>
        <style>
            input { display: inline-block; }
        </style>
    </head>
    <body>
        <div id="container">
            <div id="header"></div>
            <div id="content">
                <div id="pageContainer">
                    <?= $this->element('header'); ?>
                    <?= $this->element('sidebar'); ?>
                    <div id="mainContents">
                        <?php if (str_replace(['criteo', 'successful_candidates','Request'], '', strtolower($_SERVER["REQUEST_URI"])) !== strtolower($_SERVER["REQUEST_URI"])): ?>
                            <div class="hedder01">
                                <?php
                                    if (stristr($_SERVER["REQUEST_URI"], 'criteo') !== FALSE) {
                                        echo "CRITEOフィード管理画面";
                                    } else if (stristr($_SERVER["REQUEST_URI"], 'successful_candidates') !== FALSE) {
                                        echo "合格者の声管理画面";
                                    } else if (stristr($_SERVER["REQUEST_URI"], 'Request') !== FALSE) {
                                        echo "資料請求管理ページ";
                                    }
                                ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($course_name)) : ?>
                            <h2 class="course_title"><?= $course_name; ?></h2>
                            <span class="logout"><?= $this->Html->link('ログアウト', array('action' => 'logout', 'controller' => 'users')); ?></span>
                        <?php endif; ?>
                        <div class="double">
                            <p class="flash"></p>
                            <?= $this->fetch('content'); ?>
                        </div>
                        <br />
                        <div style="clear:both;"></div>
                    </div>
                    <?= $this->element('footer'); ?>
                </div>
            </div>
            <?= $this->element('footer'); ?>
        </div>
    </body>
</html>