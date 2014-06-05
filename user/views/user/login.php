<?php defined('SYSPATH') OR die('No Direct Script Access'); ?>

<div id="content-wrapper">  
<div class="tabtable"> 
    <ul class="nav nav-tabs">
        <li class="active"> <?php echo HTML::anchor('/user/login', __('Login')); ?> </li>
        <li> <?php echo HTML::anchor('/user/register', __('Register')); ?> </li>
        <li> <?php echo HTML::anchor('/user/forgot_pass', __('Forgot Password')); ?> </li>
    </ul>
</div> <!-- contentnav -->

<div class="content-box">
<div class="content-form" id="login-box">
<ul>
<?php 
$form = new Drkwolf_Form_Bootstrap($errors, $default);
echo $form->open('user/login');

echo $form->label('username');
echo $form->input('username');

echo $form->label('password');
echo $form->password('password');

echo $form->checkbox('remember', 'yes', False, array('id' => 'rememberme', 'label' => __('Stay signed in')));

echo $form->button('login', __('Login'), array('class' => 'btn','type'  => 'submit') );
echo Form::close(); 

Controller_App::js_add_onload(<<<javascript
	 if (localStorage) {
	 	 if (localStorage.rememberme == "checked") {
			$("#rememberme").attr("checked", "checked");
		 }
		 $("#rememberme").change(function(){
			localStorage["rememberme"] = $(this).attr("checked");
		 });
	 }
javascript
);

?>
</ul>
</div> <!-- content-form -->
</div> <!-- content-box -->