<?php 
  #file::/home/drkwolf/usr/var/www/kohana/apps/proj3/application/views/user
defined('SYSPATH') OR die('No Direct Script Access');
?>

<div id="content-wrapper">  
  <div id="tabtable tabs-below"> 
      <ul class="nav nav-tabs">
          <li> <?php echo HTML::anchor('/user/profile', __('Profile')); ?> </li>
          <li class="active"> <?php echo HTML::anchor('/user/edit_profile', __('Change Password')); ?> </li>
      </ul>
  </div> <!-- contentnav -->

  <div class="content-box">
  <div class="content-form" id="knowledge-box">
  <ul>
  <?php 
  $form = new Appform($errors, $user, 'password');
  echo $form->open();

  echo $form->label('username');
  echo $form->input('username');

  echo $form->label('email');
  echo $form->input('email');

  echo $form->label('password');
  echo $form->password('password');

  echo $form->label('password_confirm');
  echo $form->password('password_confirm');

  echo $form->button('save', __('Save'), array('class' => 'btn','type'  => 'submit') );
  echo $form->close(); 
  ?>
  </ul>
  </div>
  </div>
</div>
