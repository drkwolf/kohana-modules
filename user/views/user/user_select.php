<?php defined('SYSPATH') OR die('No Direct Script Access'); ?>


<script type="text/javascript">
window.grp = "<?php echo $default->id;?>";
<?php //include kohana::find_file('media','js/user', 'js'); ?>
</script>

<style type="text/css">
.selected-true {
	/*font-weight: bold;*/
	color: green;
}
</style>

<?php 
/**
 * popup select user and return 
 * @param model
 * @param 
 */
?>

<div class='container-fluid' ng-controller="UserList">
	
    <input type="text" ng-model="inputUser" typeahead="state for state in typeaheadlist | filter:$viewValue">
	<input type="hidden" ng-model="grp"/>
	
	<button class="btn" ng-click="insertUsers()"><i class="icon-plus" ></i><?php echo __('Add');  ?></button>
	<a href="#modal-user-list" role="button" class="btn" data-toggle="modal"><i class="icon-user"></i><?php echo __('List');  ?></a>
	  <pre>Model:{{postUrl+'/'+grp+'/'+inputUser}}</pre>
	
</div>

<div id="modal-user-list" class="modal hide fade">
	<div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3><?php echo __('User List') ?></h3>
	</div>	

<div class="modal-body" ng-controller="UserList">
	<table class="table">
	<thead>
		<tr> <th/>
			<th><?php echo __('User');  ?></th>
		</tr>
	</thead>
	<tbody>
	<tr ng-repeat="user in ngrp_users">
		<td>
			<input name="users[{{user.id}}]" type="checkbox" ng-model="user.selected"> 
		</td>
		<td><span class="selected-{{user.selected}}">{{user.username}}</span></td>
	</tr>
	</tbody>
	</table>
</div> <!-- modal body-->

  <div class="modal-footer">	
    <button class="btn" ng-click="close()"><?php echo __('Close') ?></button>
    <button class="btn btn-primary" ng-click="insert()"><?php echo __('Add Users') ?></button>
  </div>
</div> <!--	end Modal -->
<div ng-controller="UserList">
<table id="sub_group" class="table">
	<thead>
		<tr>
			<th></th>
			<th><?php echo __('User');  ?></th>
		</tr>
	</thead>
	<tbody>
	<tr ng-repeat="user in grp_users">
		<td>
			<input name="users[{{user.id}}]" type="checkbox" ng-model="user.selected"> 
		</td>
		<td><span class="selected-{{user.selected}}">{{user.username}}</span></td>
	</tr>
	</tbody>
</table>
<button class="btn" ng-click="removeUsers()"><i class="icon-trash"></i><?php echo __('Remove') ?></button>
</div>