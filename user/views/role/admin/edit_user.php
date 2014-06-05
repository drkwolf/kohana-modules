<?php defined('SYSPATH') OR die('No Direct Script Access');?>



<div id="content-wrapper">  
<div class="tabtable"> 
    <ul class="nav nav-tabs">
        <li> <?php echo Action::create('role') ?> </li>
    </ul>
</div> <!-- contentnav -->
<div class="content-box">
<div class="title"> 
    <h3> <?php echo __('Edit Roles '). $role->name ?> </h3>
    <div id="metatag"> 
    </div> <!-- metatag -->
</div> <!-- title -->

<div class="content-data">

<input class="input-large" type="text"/>
<button class="btn"><i class="icon-plus"></i><?php echo __('Add');  ?></button>
<button class="btn" methd="select_users()"><i class="icon-user"></i><?php echo __('List');  ?></button>


<table class="table" ng-controller="UserList">
<thead>
	<tr>
	<th></th>
	<th><?php echo __('User');  ?></th>
	</tr>
</thead>
<tbody>
<tr ng-repeat="user in users">
	<td>
		<input id="{user.id}" type="checkbox" model="user.selected"> 
	</td>
	<td><span class="select-{{user.selected}}}">{{user.username}}</span></td>
</tr>
</tbody>
</table>
<button class="btn" ng-click="submit()"><i class="icon-plus"></i> <?php echo __('Submit'); ?></button>


</div> <!-- content-data -->
</div> <!-- content-box -->
</div> <!-- content-wrapper -->
