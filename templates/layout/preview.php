<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Script-Type" content="text/javascript" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        <?= $this->Html->charset(); ?>
        <title>拠点ページ管理システム</title>

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
        <?= $this->Html->script('jquery-2.2.2.min.js') ?>
        <?= $this->Html->script('jquery.jscrollpane.js') ?>
        <?= $this->Html->css(['admin_custom/css/admin.css']) ?>
        <style type="text/css">
            .double {
                margin-left: 2em;
                margin-top: 10px;
                margin-bottom: 10px;
            }

            .double ul {
                margin: 0;
                padding: 0;
                width: 800px;
                list-style: none outside;
            }

            .double ul li {
                margin: 0;
                margin-bottom: 0.5em;
                padding: 0;
                float: left;
                width: 200px;
            }
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
                        <div class="hedder01">拠点ページ</div>
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
