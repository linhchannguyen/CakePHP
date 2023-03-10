アラート一覧
<script type="text/javascript" language="javascript">
    function onButtonClick() {
        var target = document.getElementById("CriteoAlertBrowsetime").value;
        document.getElementById('CriteoCsvExportAlert').value = target;
    }
</script>
<?php

echo $this->Flash->render();
echo $this->Form->create(null, array('url' => ['controller' => 'criteoAlerts', 'action' => 'searchAlert'], 'class' => 'login', 'type' => 'post'));
echo $this->Form->input('browsetime', array(
    'label' => false,
    'options' => $optionTimeList,
    'div'  => false,
    'type' => 'select',
    'onchange'  => 'onButtonClick()',
    'id'  => 'CriteoAlertBrowsetime'

));

echo $this->Form->input('', array(
    'type'     => 'submit',
    'div'      => false,
    'value'    => '検索'
));

echo $this->Form->end();

// アラート一覧表示
echo '※CRITEOフィード管理にて確認できないページが閲覧されております。<br />';

?>

<div class="type_form">
    <table style="border-spacing : 20px 0px;">
        <tr>
            <td style="border : 44px">
                ページID
            </td>
            <td>
                &nbsp
            </td>
            <td>
                URL
            </td>
            &nbsp
            <td>
            </td>
            <td>
                閲覧時刻
            </td>
        </tr>
        <?php
        foreach ($feedList as $feed) {
            echo '<tr>';
            echo '<td>';
            echo $feed['id'];
            echo '</td>';
            echo '<td>';
            echo '&nbsp';
            echo '</td>';
            echo '<td>';
            echo $feed['url'];
            echo '</td>';
            echo '<td>';
            echo '&nbsp';
            echo '</td>';
            echo '<td>';
            echo date('Y-m-d', strtotime($feed['browsetime']));
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </table>
</div>
<?php
echo $this->Form->create(null, array('url' => ['controller' => 'criteoAlerts', 'action' => 'exportAlert'], 'class' => 'login', 'type' => 'post', 'onClick' => 'onButtonClick()'));

echo $this->Form->hidden('CriteoCsvExportAlert', array(
    'value' => '',
    'id'       => 'CriteoCsvExportAlert',
));

echo $this->Form->control('CSV出力', array(
    'type'     => 'submit',
    'value'    => ' CSV出力 ',
    'id'       => false,
));
echo $this->Form->end();
?>