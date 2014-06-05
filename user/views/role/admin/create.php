<div id="content-wrapper">  
<div id="contentnav"> 
    <ul class="left">
        <li>   </li>
    </ul>
</div> <!-- contentnav -->
<div class="content-box">
<div class="title"> 
    <h1> <?php echo __('Admin Roles') ?> </h1>
    <div id="metatag"> 
        <span>  </span>
    </div> <!-- metatag -->
</div> <!-- title -->

<div class="content-form">
<ul>
<?php
     $form = new Appform($errors, $default);
     echo $form->open();
     echo $form->hidden('id');

     echo $form->label('name');
     echo $form->input('name');

     echo $form->label('type');
     echo $form->select('type', Drkwolf_Model_Role::$types);

     echo $form->textarea('description');
	 if ($default->loaded()) {
		 echo $form->submit(NULL, __('Create'));
	 }
	 else {
		 echo $form->submit(NULL, __('Update'));
	 }
     

     echo $form->close();
?>
</ul>

</div> <!-- content-data -->
</div> <!-- content-box -->
</div> <!-- content-wrapper -->

