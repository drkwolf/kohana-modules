<?php defined('SYSPATH') OR die('No Direct Script Access');?>
<li class="nav-header"> <?php echo  __('Login'); ?> </li>
<li class="divider"></li>
<li> <?php echo action::anchor('user/login', __('Login')); ?>   </li>
<li> <?php echo action::anchor('user/register', __('Register')); ?>   </li>
<li> <?php echo action::anchor('user/forgot_pass', __('Reset Password')); ?>   </li>