<div id="content-wrapper"> 
   
<div class="tabtable"> 
    <ul class="nav nav-tabs">
        <li> <?php echo HTML::anchor('/user/login', __('Login')); ?> </li>
        <li> <?php echo HTML::anchor('/user/register', __('Register')); ?> </li>
        <li> <?php echo HTML::anchor('/user/forgot_pass', __('Forgot Password')); ?> </li>
    </ul>
</div> <!-- contentnav -->

<div class="content-box">
<div class="content-form" id="knowledge-box">
<ul>
<?php 
    $form = new Appform($errors, $default);
    echo $form->open();

    echo $form->label('token');
    echo $form->input('reset_token');

    echo $form->submit('send', __('Send'));
    echo $form->close(); 
?>

</ul>
</div> <!-- content-form -->
</div> <!-- content-box -->
</div> <!-- content-wrapper -->
