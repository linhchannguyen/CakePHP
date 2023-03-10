<?php if (isset($contents)) : ?>
    <div class="frm">
        <dl>
            <dt>ID</dt>
            <dd>
                <?= $contents['id'] ?>
            </dd>
        </dl>

        <dl>
            <dt>校舎</dt>
            <dd>
                <?= $contents['school_name'] ?>
            </dd>
        </dl>

        <dl>
            <dt>講座</dt>
            <dd>
                <?= $contents['kouza_name'] ?>
            </dd>
        </dl>

        <dl>
            <dt>タイトル</dt>
            <dd>
                <?= $contents['recommend_title'] ?>
            </dd>
        </dl>

        <dl>
            <dt>サブタイトル</dt>
            <dd>
                <?= $contents['recommend_title_sub'] ?>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>リンク（タイトル）</dt>
            <dd>
                <a href="<?= $contents['recommend_url'] ?>" target="_blank"><?= $contents['recommend_url'] ?></a>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>リンク（サブタイトル）</dt>
            <dd>
                <?= $contents['sub_title1'] ?>&nbsp;<br>
                <a href="<?= $contents['sub_url1'] ?>" target="_blank"><?= $contents['sub_url1'] ?></a>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>&nbsp;</dt>
            <dd>
                <?= $contents['sub_title2'] ?>&nbsp;<br>
                <a href="<?= $contents['sub_url2'] ?>" target="_blank"><?= $contents['sub_url2'] ?></a>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>&nbsp;</dt>
            <dd>
                <?= $contents['sub_title3'] ?>&nbsp;<br>
                <a href="<?= $contents['sub_url3'] ?>" target="_blank"><?= $contents['sub_url3'] ?></a>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>&nbsp;</dt>
            <dd>
                <?= $contents['sub_title4'] ?>&nbsp;<br>
                <a href="<?= $contents['sub_url4'] ?>" target="_blank"><?= $contents['sub_url4'] ?></a>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>画像１</dt>
            <dd>
                <?php if ($contents['image_url1']) : ?>
                    <img src="<?= $contents['image_url1'] ?>">
                    <?php endif ?>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>画像２</dt>
            <dd>
                <?php if ($contents['image_url2']) : ?>
                    <img src="<?= $contents['image_url2'] ?>">
                    <?php endif ?>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>画像３</dt>
            <dd>
                <?php if ($contents['image_url3']) : ?>
                    <img src="<?= $contents['image_url3'] ?>">
                    <?php endif ?>&nbsp;
            </dd>
        </dl>

        <dl>
            <dt>並び順補正</dt>
            <dd>
                <?= $contents['order_no'] ?>
            </dd>
        </dl>

        <dl>
            <dt>表示許可</dt>
            <dd>
                <?= $contents['is_active'] ?>
            </dd>
        </dl>

        <dl>
            <dt>有効期間(開始)</dt>
            <dd>
                <?= $contents['enabled_from'] ?>
            </dd>
        </dl>

        <dl>
            <dt>有効期間(終了)</dt>
            <dd>
                <?= $contents['enabled_to'] ?>
            </dd>
        </dl>

        <dl>
            <dt>作成日時</dt>
            <dd>
                <?= $contents['created'] ?>
            </dd>
        </dl>

        <dl>
            <dt>更新日時</dt>
            <dd>
                <?= $contents['modified'] ?>
            </dd>
        </dl>

        <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Recommends', 'action' => 'index'], 'type' => 'post')); ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'hidden',
            'div'      => false,
            'name'     => 'f',
            'value'    => 'recommends_edit',
        ));
        ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'hidden',
            'div'      => false,
            'name'     => 'id',
            'value'    => $contents['id'],
        ));
        ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => '編集',
        ));
        ?>
        <?php echo $this->Form->end(); ?>
        &nbsp;
        <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Recommends', 'action' => 'index'], 'type' => 'post')); ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'hidden',
            'div'      => false,
            'name'     => 'f',
            'value'    => 'recommends_delete_confirm',
        ));
        ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'hidden',
            'div'      => false,
            'name'     => 'id',
            'value'    => $contents['id'],
        ));
        ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => '削除',
        ));
        ?>
        <?php echo $this->Form->end(); ?>
        &nbsp;
        <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Recommends', 'action' => 'index'], 'type' => 'post')); ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => '戻る',
        ));
        ?>
        <?php echo $this->Form->end(); ?>

    </div>
<?php endif ?>