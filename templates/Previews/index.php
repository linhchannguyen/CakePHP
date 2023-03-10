<?= $this->Flash->render() ?>
<?php
    if (!empty($school_list)) {
        echo '<ul>';
        foreach ($school_list as $elm) {
            echo '<li><a href="http://www.tac-school.co.jp/tacmap/' . $elm['school_tag_name'] . '.html" target="_blank">' . $elm['school_name'] . '</a></li>';
        }
        echo '</ul>';
    }
?>
