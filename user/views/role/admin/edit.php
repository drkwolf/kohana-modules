<div id="content-wrapper">  
<div id="tabtable"> 
    <ul class="nav nav-tabs">
        <li id="active"> <?php echo Action::create('role') ?> </li>
        <li id="collapse-content-page"> <a id="collapse-content-link" href="#"> <span class="ui-icon ui-icon-triangle-1-e"> </span> </a> </li>
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
     $form = new AppForm($errors, $default);
     echo $form->open();
     echo $form->hidden('id');

     echo $form->label('name');
     echo $form->input('name');

     // echo $form->label('type');
     // echo $form->select('type', Model_Role::$types);

     echo $form->textarea('description', NULL, array('class' => 'meditor input-xxlarge'));
	 
	 if (isset($default)) {
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

