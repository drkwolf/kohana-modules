<div id="content-wrapper">  
<div class="tabtable"> 
    <ul class="nav nav-tabs">
        <?php if ($user->id AND $user->has_perm('delete')): ?>
        <li class="active"> <?php echo Action::edit($user, __('Edit')); ?>  </li>
        <li> <?php echo HTML::anchor('admin_user/delete/'.$user->id, __('Delete')) ?>  </li>
        <?php else: ?>
        <li class="active"> <?php echo __('Create User'); ?>  </li>
        <?php endif; ?>
    </ul>
</div> <!-- contentnav -->

<div class="content-box">
<?php
    // FIXME Ugly
   $form = new AppForm($errors, $user, 'password');
    echo Form::open();
    echo $form->hidden('id');
?>
<div class="content-form">
<ul>
   <li><label><?php echo __('Username'); ?></label></li>
   <?php echo $form->input('username', null, array('info' => __('Letters, numbers'))); ?>
   <li><label><?php echo __('Email address'); ?></label></li>
   <?php echo $form->input('email') ?>
   <li><label><?php echo __('Password'); ?></label></li>
   <?php echo $form->password('password', null, array('info' => __('between 6-42 characters.'))) ?>
   <li><label><?php echo __('Re-type Password'); ?></label></li>
   <?php echo $form->password('password_confirm') ?>
   <li><h2><?php echo __('Roles'); ?></h2></li>
   <li>

<div class="tabtable">
  <ul class="nav nav-tabs"> 
    <?php $i=0; 
        foreach(Model_Role::$types as $type): ?>
    <li <?php if($i==0) echo 'class="active"';$i++ ?> ><?php echo Action::anchor("#$type", __($type), array('class'=>'active', 'data-toggle'=>'tab')); ?>
    </li>
    <?php endforeach; ?>
  </ul>

   <div class="tab-content"> 
    <?php $i = 0;
          foreach(Model_Role::$types as $type): ?>
    <div class="tab-pane <?php if($i==0) echo 'active';$i++ ?>" id="<?php echo $type ?>">
      <table class="table">
      <thead>
        <tr class="heading">
          <th>  </th> 
          <th><?php echo __('Role'); ?></th>
          <th><?php echo __('Description'); ?></th>
        </tr>
      </thead>
      <tbody>
      <?php
        $user_roles = $user->roles->find();
        foreach(ORM::factory('Role')->find_all() as $role) {
           echo '<tr>';
           echo '<td>'.Form::checkbox('roles['.$role->id.']', $role->name, ($user->has('roles', $role) ? true : false)).'</td>';
           echo '<td>'.ucfirst($role->name).'</td><td>'.$role->description.'</td>';
           echo '</tr>';
        }
     ?>
      </tbody>
      </table>
   </div> <!-- tab-pane -->
    <?php endforeach; ?>
   </div> <!-- tab-content -->
   </div> <!-- tabtables -->
   </li>
<?php  
        //echo $form->lcheckbox('notify', __('Send Registration Code'));

      echo $form->button('save', __('Save'), array('class' => 'btn','type'  => 'submit') );
      echo Form::close();
?>
</ul>
</div> <!-- content-form -->
</div> <!-- content-box -->
</div> <!-- wrapper -->
