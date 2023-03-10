<?= $error_message ?? '' ?>
<p>※印は必須入力項目です。</p>
<div class="frm">
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Events', 'action' => 'eventsAddFinish'], 'type' => 'post', 'enctype' => 'multipart/form-data')); ?>
    <dl>
        <dt>校舎選択 (※)</dt>
        <dd>
            <div>
                <?php
                // school list
                echo $this->Form->input('school', array(
                    'type'     => 'select',
                    'options'  => $school_list,
                    'value' => $events_form['school'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01'
                ));
                ?>
                <?= $errors['school'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>講座選択 (※)</dt>
        <dd>
            <div>
                <?php
                // school list
                echo $this->Form->input('kouza', array(
                    'type'     => 'select',
                    'options'  => $kouza_list,
                    'value' => $events_form['kouza'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01'
                ));
                ?>
                <?= $errors['kouza'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>イベント種別選択 (※)</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('event_type', array(
                    'type'     => 'select',
                    'options'  => $event_type_list,
                    'value' => $events_form['event_type'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01'
                ));
                ?>
                <?= $errors['event_type'] ?? ''; ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>イベント日時 (※)</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('event_ym', array(
                    'type'     => 'select',
                    'options'  => $controls['event_ym'],
                    'value' => $events_form['event_ym'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01'
                ));
                echo $this->Form->input('event_day', array(
                    'type'     => 'select',
                    'options'  => $controls['event_day'],
                    'value' => intval($events_form['event_day']),
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:4px;"
                ));
                echo $this->Form->input('event_time_h', array(
                    'type'     => 'select',
                    'options'  => $controls['event_time_h'],
                    'value' => intval($events_form['event_time_h']),
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:5px;"
                ));
                echo $this->Form->input('event_time_m', array(
                    'type'     => 'select',
                    'options'  => $controls['event_time_m'],
                    'value' => intval($events_form['event_time_m']),
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:4px;"
                ));
                ?>
                <?= $errors['event_ym'] ?? ''; ?>
                <?= $errors['event_day'] ?? ''; ?>
                <?= $errors['event_time_h'] ?? ''; ?>
                <?= $errors['event_time_m'] ?? ''; ?>
                <?= $errors['event_date'] ?? ''; ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>タイトル (※)</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->textarea('text_title', array(
                    'value' => $events_form['text_title'],
                    'div'      => false,
                    'class'    => 'form01',
                    'cols'    => '30',
                    'rows'    => '6'
                ));
                ?>
                <?= $errors['text_title'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>本文</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->textarea('text_body', array(
                    'value' => $events_form['text_body'],
                    'div'      => false,
                    'class'    => 'form01',
                    'cols'    => '30',
                    'rows'    => '6'
                ));
                ?>
                <?= $errors['text_body'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <?php
    echo $this->Form->input('event_date', array(
        'type'     => 'hidden',
        'div'      => false,
        'value'    => '',
    ));
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => '登録',
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