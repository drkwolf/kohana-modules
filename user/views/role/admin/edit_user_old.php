<?php defined('SYSPATH') OR die('No Direct Script Access');
  #file::/home/drkwolf/usr/var/www/kohana/apps/proj3/application/views/role/admin
?>
<div id="content-wrapper">  
<div class="tabtable"> 
    <ul class="nav nav-tabs">
        <li> <?php echo Action::create('role') ?> </li>
    </ul>
</div> <!-- contentnav -->
<div class="content-box">
<div class="title"> 
    <h3> <?php echo __('Edit Roles '). $role->name ?> </h3>
    <div id="metatag"> 
    </div> <!-- metatag -->
</div> <!-- title -->

<div class="content-data">

<?php 
//     echo Debug::dump($_POST);
    echo Form::open();
    echo Form::input('usernames');
    echo Form::hidden('adduser', true);
    echo Form::hidden('id', $role->id);
    echo Form::submit(NULL, __('Add'));
    echo Form::close();
?>

<?php echo Form::open(); ?>
<table class="table">
<thead>
<tr> 
    <th> <?php echo Form::checkbox('id'); ?> </th> 
    <th> <?php echo __('Username') ?> </th> 
</tr>
</thead>
<tbody>
    <?php foreach ($role->users->find_all() as $user): ?>
            <tr>
              <td> <?php echo Form::checkbox('users['.$user->id.']'); ?> </td> 
              <td> <?php echo Action::profile($user); ?> </td> 
            </tr>
    <?php endforeach; ?>
</tbody>
</table>
<?php
       echo Form::hidden('removeuser', true);
       echo Form::submit(NULL, __('remove'));
       echo Form::close();
?>


</div> <!-- content-data -->
</div> <!-- content-box -->
</div> <!-- content-wrapper -->

