<?= $error_message ?? '' ?>
<p>※印は必須入力項目です。</p>
<div class="frm">
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'News', 'action' => 'newsEditFinish'], 'type' => 'post', 'enctype' => 'multipart/form-data')); ?>
    <dl>
        <dt>緊急度</dt>
        <dd>
            <div>
                <?php
                // Urgency
                echo $this->Form->input('urgency', array(
                    'type' => 'select',
                    'options' => $controls['urgency_list'],
                    'value' => $news_form['urgency'],
                    'div' => false,
                    'size' => 1,
                    'multiple' => false,
                    'use_group' => false,
                    'class' => 'form01',
                    'id' => 'dataNewsModelurgency'
                ));
                ?>
                　※高に設定すると「お知らせ」に表示されます。
            </div>
        </dd>
    </dl>
    <dl>
        <dt>校舎選択 (※)</dt>
        <dd>
            <div>
                <?php
                // school list
                echo $this->Form->input('school', array(
                    'type'     => 'select',
                    'options'  => $school_list,
                    'value' => $news_form['school'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'id' => 'dataNewsModelschool'
                ));
                ?>
                <?= $errors['school'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>タイトル日付 (※)</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('title_ym', array(
                    'type'     => 'select',
                    'options'  => $controls['title_ym'],
                    'value' => $news_form['title_ym'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'id' => 'dataNewsModeltitle_ym'
                ));
                echo $this->Form->input('title_day', array(
                    'type'     => 'select',
                    'options'  => $controls['title_day'],
                    'value' => intval($news_form['title_day']),
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:4px;",
                    'id' => 'dataNewsModeltitle_day'
                ));
                ?>
                <?= $errors['title_ym'] ?? '' ?>
                <?= $errors['title_day'] ?? '' ?>
                <?= $errors['news_date'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>有効期間(開始)</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('from_ym', array(
                    'type'     => 'select',
                    'options'  => $controls['from_ym'],
                    'value' => $news_form['from_ym'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'id' => 'dataNewsModelfrom_ym'
                ));
                echo $this->Form->input('from_day', array(
                    'type'     => 'select',
                    'options'  => $controls['from_day'],
                    'value' => intval($news_form['from_day']),
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:4px;",
                    'id' => 'dataNewsModelfrom_day'
                ));
                echo $this->Form->input('from_time', array(
                    'type'     => 'select',
                    'options'  => $controls['from_time'],
                    'value' => $news_form['from_time'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:5px;",
                    'id' => 'dataNewsModelfrom_time'
                ));
                ?>
                <?= $errors['enabled_from'] ?? ''; ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>有効期間(終了)</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('to_ym', array(
                    'type'     => 'select',
                    'options'  => $controls['to_ym'],
                    'value' => $news_form['to_ym'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'id' => 'dataNewsModelto_ym'
                ));
                echo $this->Form->input('to_day', array(
                    'type'     => 'select',
                    'options'  => $controls['to_day'],
                    'value' => intval($news_form['to_day']),
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:4px;",
                    'id' => 'dataNewsModelto_day'
                ));
                echo $this->Form->input('to_time', array(
                    'type'     => 'select',
                    'options'  => $controls['to_time'],
                    'value' => $news_form['to_time'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:5px;",
                    'id' => 'dataNewsModelto_time'
                ));
                ?>
                <?= $errors['enabled_to'] ?? ''; ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>タイトル (※)</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->textarea('text_title', array(
                    'value' => $news_form['text_title'],
                    'div'      => false,
                    'class'    => 'form01',
                    'cols'    => '30',
                    'rows'    => '6',
                    'id' => 'dataNewsModeltext_title'
                ));
                ?>
                <?= $errors['text_title'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>サブタイトル</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->textarea('text_title_sub', array(
                    'value' => $news_form['text_title_sub'],
                    'div'      => false,
                    'class'    => 'form01',
                    'cols'    => '30',
                    'rows'    => '6',
                    'id' => 'dataNewsModeltext_title_sub'
                ));
                ?>
                <?= $errors['text_title_sub'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>リンク種別</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_link', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_link'][0]],
                    'value'    => $news_form['radio_link'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                    'id'       => 'dataNewsModelradio_link'
                ));
                echo $this->Form->text('text_link_url', array(
                    'value' => $news_form['text_link_url'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_NEWS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => NEWS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
                    'id'       => 'dataNewsModeltext_link_url'
                ));
                ?>
                <?= $errors['text_link_url'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_link', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_link'][1]],
                    'value'    => $news_form['radio_link'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                    'id' => 'dataNewsModelradio_link'
                ));
                echo $this->Form->input('file_pdf', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_NEWS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                    'id'       => 'dataNewsModelfile_pdf'
                ));
                ?>
                <?= $errors['file_pdf'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>並び順補正</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->text('text_order_no', array(
                    'value'    => $news_form['text_order_no'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_NEWS_ORDER_TEXT_BOX_SIZE,
                    'maxlength' => MAX_ORDER_LEN,
                    'id'       => 'dataNewsModeltext_order_no'
                ));
                ?>
                <?= $errors['text_order_no'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>表示許可</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_is_active', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_is_active'][1]],
                    'value'    => $news_form['radio_is_active'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                    'id'       => 'dataNewsModelradio_is_active'
                ));
                echo $this->Form->input('radio_is_active', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_is_active'][0]],
                    'value'    => $news_form['radio_is_active'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-left:4.5px;margin-right:4.5px;",
                    'id'       => 'dataNewsModelradio_is_active'
                ));
                ?>
                <?= $errors['radio_is_active'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <?php
    echo $this->Form->input('id', array(
        'type'     => 'hidden',
        'div'      => false,
        'value'    => $id,
    ));
    echo $this->Form->input('news_date', array(
        'type'     => 'hidden',
        'div'      => false,
        'value'    => '',
    ));
    echo $this->Form->input('enabled_from', array(
        'type'     => 'hidden',
        'div'      => false,
        'value'    => '',
    ));
    echo $this->Form->input('enabled_to', array(
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
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'News', 'action' => 'newsDetail'], 'type' => 'post')); ?>
    <?php
    echo $this->Form->input('id', array(
        'type'     => 'hidden',
        'div'      => false,
        'value'    => $id,
    ));
    echo $this->Form->input('', array(
        'type'     => 'submit',
        'div'      => false,
        'value'    => '戻る',
    ));
    ?>
    <?php echo $this->Form->end(); ?>
</div>