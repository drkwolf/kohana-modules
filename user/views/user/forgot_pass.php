<?php defined('SYSPATH') OR die('No Direct Script Access');?>

<div id="content-wrapper">  
<div id="tabtable tabs-below"> 
    <ul class="nav nav-tabs">
        <li> <?php echo HTML::anchor('/user/login', __('login')); ?> </li>
        <li> <?php echo HTML::anchor('/user/register', __('register')); ?> </li>
        <li class="active"> <?php echo HTML::anchor('/user/forgot_pass', __('forgot password')); ?> </li>
    </ul>
</div> <!-- contentnav -->

<div class="content-box">
<div class="content-form" id="knowledge-box">
<ul>
<?php 
    $form = new Appform($errors, $default);
    echo $form->open();

    echo $form->label('email');
    echo $form->input('email', NULL, array('placeholder' => 'Email',));

    echo $form->button('Send', __('Send'), array('class' => 'btn','type'  => 'submit') );
    echo $form->close();
?>
</ul>
</div> <!-- content-form -->
</div> <!-- content-box -->
</div> <!-- content-wrapper -->
