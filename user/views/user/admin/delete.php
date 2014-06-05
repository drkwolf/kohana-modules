<?php 
  #file::/home/drkwolf/usr/var/www/kohana/apps/proj3/application/views/user/admin
defined('SYSPATH') OR die('No Direct Script Access');
?>

<div id="content-wrapper">  
<div class="tabtable"> 
 <ul class="nav nav-tabs">
    <li> <?php echo Action::create('admin_user') ?>  </li>
 </ul>
</div> <!-- contentnav -->
<div class="content-box">
<div class="content-form">
<ul>
<?php

$form = new Appform($errors, $default);

echo Form::open(NULL, array('style' => 'display: inline;'));

echo '<p>'.__('Are you sure you want to delete user ":user"', array(':user' => $default->username)).'</p>';

echo Form::label('Yes');
echo Form::radio('confirmation', 'Y', false, array('id' => 'conf_y'));

echo Form::label('No');
echo Form::radio('confirmation', 'N', true, array('id' => 'conf_n'));

echo '<br>';
echo Form::submit(NULL, __('Delete'));
echo Form::close();

echo Form::open('admin_user/index', array('style' => 'display: inline; padding-left: 10px;'));
echo Form::submit(NULL, __('Cancel'));
echo Form::close();
?>
</ul>
</div>

</div> <!-- content-box -->
</div> <!-- wrapper -->
