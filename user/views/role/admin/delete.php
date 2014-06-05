<div class="block"> 
 <h1>Delete Role?</h1> 
 <div class="content"> 
<?php 
 
echo Form::open('admin_role/delete/'.$id, array('style' => 'display: inline;')); 
 
echo Form::hidden('id', $id); 
 
echo '<p>'.__('Are you sure you want to delete Role ":role"', array(':role' => $name)).'</p>'; 
 
echo '<p>'.Form::radio('confirmation', 'Y', false, array('id' => 'conf_y')).' <label for="conf_y" style="display: inline;">'.__('Yes').'</label><br/>'; 
echo Form::radio('confirmation', 'N', true, array('id' => 'conf_n')).' <label for="conf_n" style="display: inline;">'.__('No').'</label><br/></p>'; 
echo Form::submit(NULL, __('Delete')); 
echo Form::close(); 
 
echo Form::open('role/index', array('style' => 'display: inline; padding-left: 10px;')); 
echo Form::submit(NULL, __('Cancel')); 
echo Form::close(); 
?> 
   </div> 
</div> 
