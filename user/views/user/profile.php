<?php 
  #file::/home/drkwolf/usr/var/www/kohana/apps/proj3/application/views/user
  defined('SYSPATH') OR die('No Direct Script Access');
?>

<div id="content-wrapper">  
<div id="tabtable"> 
    <ul class="nav nav-tabs">
        <li class="active"> <?php echo HTML::anchor('/user/profile', __('Profile')); ?> </li>
        <li> <?php echo HTML::anchor('/user/edit_profile', __('Change Password')); ?> </li>
    </ul>
</div> <!-- tabtable -->

<div class="content-box">
<div class="content-data">

<li> <?php echo 'username :'.$user->username ?> </li>
<li> <?php echo 'email :'.$user->email ?> </li>

<h1> <?php echo __('Roles'); ?> </h1>
<table class="table table-bordered table-striped">
 <thead>
    <tr> 
        <th> <?php echo __('Name') ?> </th> 
        <th> <?php echo __('Description') ?> </th> 
    </tr> 
 </thead>
 <tbody>
    <?php foreach($user->roles->find_all() as $role): ?>
    <tr> 
    <td> <?php echo $role->name ?> </td> 
    <td> <?php echo $role->description ?> </td> 
    </tr> 
    <?php endforeach; ?>
 </tbody>
</table>

</div> <!-- content-data -->
</div> <!-- content-box -->

</div> <!-- content-wrapper -->
