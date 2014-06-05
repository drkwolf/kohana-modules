<?php defined('SYSPATH') OR die('No Direct Script Access'); ?>
 
<li class="dropdown-submenu"> 
	<?php echo HTML::anchor('admin_user/', __('Users')); ?> 
	<ul class="dropdown-menu">
      <li>  <?php echo HTML::anchor('admin_user/', __('List')); ?></li>
      <li>  <?php echo HTML::anchor('admin_user/edit', __('Create')); ?></li>
      <li>  <?php echo HTML::anchor('admin_user/config', __('Configuration')); ?></li>
    </ul>
</li>
<?php if(Kohana::$config->load('drkwolf/user.roles.managed')): ?>
<li  class="dropdown-submenu">
	<?php echo HTML::anchor('admin_role/', __('Roles')); ?> 
	<ul class="dropdown-menu">
      <li>  <?php echo HTML::anchor('admin_role/', __('List')); ?></li>
      <li>  <?php echo HTML::anchor('admin_role/edit', __('Create')); ?></li>
      <li>  <?php echo HTML::anchor('admin_role/config', __('Configuration')); ?></li>
    </ul>
</li>
<?php endif; ?>
