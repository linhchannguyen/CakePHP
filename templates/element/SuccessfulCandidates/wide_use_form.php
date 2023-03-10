
<?php if (in_array($moduleSlug, array(ALPHABET, FREECOMMENT))): ?>
<!--<table class="formTable">-->
<tr>
<th>初期値</th>
<td><?php echo $this->Form->input('place_holder', array('type' => 'text','label' => false, 'value' => !empty($voiceModule['place_holder'])?  $voiceModule['place_holder'] : '', 'div' => false, 'class' => 'commonInput block')); ?></td>
</tr>
<?php endif; ?>

<?php if (in_array($moduleSlug, array(ALPHABET, FREECOMMENT,TACNUMBER))): ?>
<tr>
<th>制限文字数 *数値のみ</th>
<td><?php echo $this->Form->input('char_limit', array('type' => 'number','required' => true,'label' => false, 'value' => !empty($voiceModule['char_limit'])? $voiceModule['char_limit'] : 100, 'div' => false, 'class' => 'commonInput block')); ?></td>
</tr>
<?php endif; ?>

<?php if (in_array($moduleSlug, array(RADIO, 'LIST', CHECKBOX))): ?>
<tr>
<th>最大選択数  *数値のみ</th>
<td><?php echo $this->Form->input('select_count', array('type' => 'number','required' => true,'label' => false, 'value' => !empty($voiceModule['select_count'])?  $voiceModule['select_count'] : 1, 'div' => false, 'class' => 'commonInput block')); ?></td>
<tr>
<th>項目名</th>
<td>
<div id="input_pluralBox">
    <?php if (!empty($voiceModule['voice_part_options'])):?>
        <?php foreach ($voiceModule['voice_part_options'] as $key => $value): ?>
        <!--<div id="input_plural">-->
        <div>
            <?php echo $this->Form->input('VoicePartOption.value.', array('type' => 'text','label' => false, 'value' => $value['name'], 'div' => false)); ?>
            <input type="button" value="＋" class="add pluralBtn">
            <input type="button" value="－" class="del pluralBtn">
        </div>
        <!--</div>-->
        <?php endforeach; ?>
    <?php endif; ?>

    <div>
        <?php echo $this->Form->input('VoicePartOption.value.', array('type' => 'text','label' => false, 'value' => '', 'div' => false)); ?>
        <input type="button" value="＋" class="add pluralBtn">
        <input type="button" value="－" class="del pluralBtn">
    </div>
</div>
</td>
</tr>
<!--</table>-->
<?php endif; ?>

<script>
    $(document).on("click", ".add", function() {
        $(this).parent().clone(true).insertAfter($(this).parent());
    });
    $(document).on("click", ".del", function() {
        var target = $(this).parent();
        if (target.parent().children().length > 1) {
            target.remove();
        }
    });
</script>
<style>
    .pluralBtn {
        width: 5ex;
    }
</style>
