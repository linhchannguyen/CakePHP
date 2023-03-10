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
            <dt>日付</dt>
            <dd>
                <?= $contents['event_date'] ?>
            </dd>
        </dl>

        <dl>
            <dt>イベントタイプ</dt>
            <dd>
                <?= $contents['event_type_name'] ?>
            </dd>
        </dl>

        <dl>
            <dt>タイトル</dt>
            <dd>
                <?= $contents['event_title'] ?>
            </dd>
        </dl>

        <dl>
            <dt>本文</dt>
            <dd>
                <?= $contents['event_body'] ?>&nbsp;
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

        <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Events', 'action' => 'index'], 'type' => 'post')); ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'hidden',
            'div'      => false,
            'name'     => 'f',
            'value'    => 'events_edit',
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
        <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Events', 'action' => 'index'], 'type' => 'post')); ?>
        <?php
        echo $this->Form->input('', array(
            'type'     => 'hidden',
            'div'      => false,
            'name'     => 'f',
            'value'    => 'events_delete_confirm',
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
        <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Events', 'action' => 'index'], 'type' => 'post')); ?>
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