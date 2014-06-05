<?php
foreach ($message->messages as $type => $messages) {
    foreach ($messages as $msg) {
      echo '<div class="alert alert-'.$type.'">'
        . '<button type="button" class="close" data-dismiss="alert">×</button>'
        . '<strong>'.$type.'! </strong>'
        . $msg
        . '</div>';
    }
} 
?>
