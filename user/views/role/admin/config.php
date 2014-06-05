<?php defined('SYSPATH') OR die('No Direct Script Access'); ?>

<div id="content-wrapper">  
	
<div class="content-box">
<div class="content-form" id="create-law">
<ul>
<?php 
		$form = new AppForm(NULL, $default);
		// echo debug::vars($default);die;
		echo $form->label('managed', __('Managed'));
		echo $form->bcheckbox('managed');
		
		echo $form->label('required', __('Required Roles'));
		echo $form->select('required', $default['required']);
		
		echo $form->label('hidden', __('Hidden Roles'));
		echo $form->select('hidden', $default['hidden']);
		
		echo $form->label('unhide', __('show all to'));
		echo $form->select('unhide', $default['unhide']);
?>
</ul>
</div>
</div>
</div>