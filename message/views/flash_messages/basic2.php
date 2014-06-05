<?php
    foreach ($message->messages as $type => $messages) {
        foreach ($messages as $msg) {
            echo '<div class="flash '.$type.'">'.$msg.'</div>';
        }
    }
?>
