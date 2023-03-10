<div style="padding:3px;"><?= __d('event_types_form', 'NOTICE_SETTINGS'); ?></div>
<?php echo $this->Flash->render(); ?>
<?php
    if ($this->request->isPost()) {
        echo '<font color="red">' . __d('event_types_form', 'UPDATED_DATA') . '</font>';
    }
?>
<?php echo $this->Form->create(null, $option = array('url' => ['controller' => 'EventTypesForms', 'action' => 'index'], 'type' => 'post', 'style' => 'margin-bottom:20px')); ?>
    <?php
        foreach ($kouza_list as $kouza) {
            $background = $i % 2 == 0 ? 'background:#ffffca;' : '';
            echo '<fieldset style="margin:10px;' . $background . '">';
            echo '<legend>' . $kouza['kouza_name'] . '</legend>';
            foreach ($event_type_list as $event_type) {
                $id = 'active_' . $kouza['id'] . '_' . $event_type['id'];
                $checked = in_array($id, $post_ids) ? 'checked' : '';
                echo '<input type="checkbox" id="' . $id . '" name="' . $id . '" ' . $checked . ' style="margin-left:16px;"><label for="' . $id . '" style="margin-right:4px">' . $event_type['name'] . '</label>' . "\n";
            }
            echo '</fieldset>';
            $i++;
        }

        echo $this->Form->input('', array(
            'type'     => 'submit',
            'div'      => false,
            'value'    => __d('event_types_form', 'SAVE_SETTINGS')
        ));
    ?>
<?php echo $this->Form->end(); ?>
