<?= $error_message ?? '' ?>
<p>※印は必須入力項目です。</p>
<div class="frm">
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Recommends', 'action' => 'recommendsEditFinish'], 'type' => 'post', 'enctype' => 'multipart/form-data')); ?>
    <dl>
        <dt>校舎選択 (※)</dt>
        <dd>
            <div>
                <?php
                // school list
                echo $this->Form->input('school', array(
                    'type'     => 'select',
                    'options'  => $school_list,
                    'value' => $recommends_form['school'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
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
                // kouza list
                echo $this->Form->input('kouza', array(
                    'type'     => 'select',
                    'options'  => $kouza_list,
                    'value' => $recommends_form['kouza'],
                    'div'      => false,
                    'size'      => 1,
                    'label'    => false,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                ));
                ?>
                <?= $errors['kouza'] ?? '' ?>
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
                    'value' => $recommends_form['from_ym'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                ));
                echo $this->Form->input('from_day', array(
                    'type'     => 'select',
                    'options'  => $controls['from_day'],
                    'value' => intval($recommends_form['from_day']),
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:4px;",
                ));
                echo $this->Form->input('from_time', array(
                    'type'     => 'select',
                    'options'  => $controls['from_time'],
                    'value' => $recommends_form['from_time'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:5px;",
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
                    'value' => $recommends_form['to_ym'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                ));
                echo $this->Form->input('to_day', array(
                    'type'     => 'select',
                    'options'  => $controls['to_day'],
                    'value' => intval($recommends_form['to_day']),
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:4px;",
                ));
                echo $this->Form->input('to_time', array(
                    'type'     => 'select',
                    'options'  => $controls['to_time'],
                    'value' => $recommends_form['to_time'],
                    'div'      => false,
                    'size'      => 1,
                    'multiple'    => false,
                    'use_group'    => false,
                    'class'    => 'form01',
                    'style'    => "margin-left:5px;",
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
                    'value' => $recommends_form['text_title'],
                    'div'      => false,
                    'class'    => 'form01',
                    'cols'    => '30',
                    'rows'    => '6',
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
                    'value' => $recommends_form['text_title_sub'],
                    'div'      => false,
                    'class'    => 'form01',
                    'cols'    => '30',
                    'rows'    => '6',
                ));
                ?>
                <?= $errors['text_title_sub'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl style="border-bottom:none">
        <dt>リンク（タイトル）</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_link', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_link'][0]],
                    'value'    => $recommends_form['radio_link'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->text('text_link_url', array(
                    'value' => $recommends_form['text_link_url'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => RECOMMENDS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
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
                    'value'    => $recommends_form['radio_link'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('file_pdf', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                ));
                ?>
                <?= $errors['file_pdf'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl style="border-bottom:none">
        <dt>リンク（サブタイトル）</dt>
        <dd>
            <div>
                名称（１２０文字以内で入力してください）<br>
                <?php
                echo $this->Form->text('sub_title1', array(
                    'value' => $recommends_form['sub_title1'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => 64,
                    'maxlength'    => 120,
                ));
                ?>
                <?= $errors['sub_title1'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl style="border-bottom:none">
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_sub_url1', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_file'][0]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->text('sub_url1', array(
                    'value' => $recommends_form['sub_url1'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => RECOMMENDS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
                ));
                ?>
                <?= $errors['sub_url1'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_sub_url1', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_file'][1]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('sub_url_file1', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                ));
                ?>
                <?= $errors['sub_url_file1'] ?? '' ?>
            </div>
        </dd>
    </dl>


    <dl style="border-bottom:none">
        <dt>&nbsp;</dt>
        <dd>
            <div>
                名称（１２０文字以内で入力してください）<br>
                <?php
                echo $this->Form->text('sub_title2', array(
                    'value' => $recommends_form['sub_title2'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => 64,
                    'maxlength'    => 120,
                ));
                ?>
                <?= $errors['sub_title2'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl style="border-bottom:none">
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_sub_url2', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_file'][0]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->text('sub_url2', array(
                    'value' => $recommends_form['sub_url2'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => RECOMMENDS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
                ));
                ?>
                <?= $errors['sub_url2'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_sub_url2', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_file'][1]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('sub_url_file2', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                ));
                ?>
                <?= $errors['sub_url_file2'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl style="border-bottom:none">
        <dt>&nbsp;</dt>
        <dd>
            <div>
                名称（１２０文字以内で入力してください）<br>
                <?php
                echo $this->Form->text('sub_title3', array(
                    'value' => $recommends_form['sub_title3'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => 64,
                    'maxlength'    => 120,
                ));
                ?>
                <?= $errors['sub_title3'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl style="border-bottom:none">
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_sub_url3', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_file'][0]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->text('sub_url3', array(
                    'value' => $recommends_form['sub_url3'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => RECOMMENDS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
                ));
                ?>
                <?= $errors['sub_url3'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_sub_url3', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_file'][1]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('sub_url_file3', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                ));
                ?>
                <?= $errors['sub_url_file3'] ?? '' ?>
            </div>
        </dd>
    </dl>


    <dl style="border-bottom:none">
        <dt>&nbsp;</dt>
        <dd>
            <div>
                名称（１２０文字以内で入力してください）<br>
                <?php
                echo $this->Form->text('sub_title4', array(
                    'value' => $recommends_form['sub_title4'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => 64,
                    'maxlength'    => 120,
                ));
                ?>
                <?= $errors['sub_title4'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl style="border-bottom:none">
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_sub_url4', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_file'][0]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->text('sub_url4', array(
                    'value' => $recommends_form['sub_url4'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => RECOMMENDS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
                ));
                ?>
                <?= $errors['sub_url4'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_sub_url4', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_file'][1]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('sub_url_file4', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                ));
                ?>
                <?= $errors['sub_url_file4'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl style="border-bottom:none">
        <dt>画像１</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_image1', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_image'][0]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->text('image_url1', array(
                    'value' => $recommends_form['image_url1'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => RECOMMENDS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
                ));
                ?>
                <?= $errors['image_url1'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_image1', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_image'][1]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('image_file1', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                ));
                ?>
                <?= $errors['image_file1'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl style="border-bottom:none">
        <dt>画像２</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_image2', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_image'][0]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->text('image_url2', array(
                    'value' => $recommends_form['image_url2'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => RECOMMENDS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
                ));
                ?>
                <?= $errors['image_url2'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_image2', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_image'][1]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('image_file2', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                ));
                ?>
                <?= $errors['image_file2'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl style="border-bottom:none">
        <dt>画像３</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_image3', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_image'][0]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->text('image_url3', array(
                    'value' => $recommends_form['image_url3'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE,
                    'maxlength'    => RECOMMENDS_MAX_URL_LEN,
                    'style'    => "margin-left:4px;padding-left:.5px",
                ));
                ?>
                <?= $errors['image_url3'] ?? '' ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->input('radio_image3', array(
                    'type'     => 'radio',
                    'options'  => [1 => $controls['radio_image'][1]],
                    'value'    => $recommends_form['radio_image'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('image_file3', array(
                    'type'     => 'file',
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE,
                    'style'    => "margin-left:4.5px;",
                ));
                ?>
                <?= $errors['image_file3'] ?? '' ?>
            </div>
        </dd>
    </dl>

    <dl>
        <dt>並び順補正</dt>
        <dd>
            <div>
                <?php
                echo $this->Form->text('text_order_no', array(
                    'value'    => $recommends_form['text_order_no'],
                    'div'      => false,
                    'class'    => 'form01',
                    'size'     => ADMIN_RECOMMENDS_ORDER_TEXT_BOX_SIZE,
                    'maxlength' => MAX_ORDER_LEN,
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
                    'value'    => $recommends_form['radio_is_active'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-right:4.5px;",
                ));
                echo $this->Form->input('radio_is_active', array(
                    'type'     => 'radio',
                    'options'  => [0 => $controls['radio_is_active'][0]],
                    'value'    => $recommends_form['radio_is_active'],
                    'div'      => false,
                    'label'    => true,
                    'class'    => 'form01',
                    'style'    => "margin-left:4.5px;margin-right:4.5px;",
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
    <?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'Recommends', 'action' => 'recommendsDetail'], 'type' => 'post')); ?>
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