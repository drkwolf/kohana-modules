<div id="content-wrapper">  
<div class="tabtable"> 
    <ul class="nav nav-tabs">
        <li> <?php echo Action::create('admin_role') ?> </li>
    </ul>
</div> <!-- contentnav -->
<div class="content-box">
<?php
// format data for DataTable
$data = array();
$merge = null;
foreach ($roles as $role) {
   $row = $role->as_array();
   // reformat dates
//    $row['created'] = Helper_Format::friendly_datetime($row['created']);
//    $row['modified'] = Helper_Format::friendly_datetime($row['modified']);
//    $row['last_login'] = Helper_Format::relative_time($row['last_login']);
//   $row['last_failed_login'] = Helper_Format::relative_time(strtotime($row['last_failed_login']));
   // add actions
    $row['id'] = Action::view($role, $role->id);
   $row['description'] = Text::truncate($row['description'], 80);
   $row['actions'] = 
       HTML::anchor('admin_role/edit/'.$role->id, __('Edit')).' | '.
       HTML::anchor('admin_role/delete/'.$role->id, __('Delete')).' | '.
       HTML::anchor('admin_role/edit_users/'.$role->id, __('Edit Users'));
   $row['# users'] = HTML::anchor('admin_role/edit_users/'.$role->id , $role->users->count_all());
   // set roles
//    $row['role'] = '';
//    foreach($role->roles->where('name', '!=', 'login')->find_all() as $role) {
//       $row['role'] .= $role->name.', ';
//    }
//    // remove last comma
//    $row['role'] = substr($row['role'], 0, -2);
   $data[] = $row;
}

$column_list = array(
	'id' => array( 'label' => __('#') ), 
	'name' => array( 'label' => __('Name') ), 
	'description' => array( 'label' => __('Description'), 'sortable' => false ),
	'type' => array( 'label' => __('Type') ),
    '# users' => array( 'label' => __('# Users'), 'sortable' => false),
);
$column_list['actions'] = array( 'label' => __('Actions'), 'sortable' => false );
$datatable = new Datatable($column_list, array('paginator' => true, 'class' => 'table table-bordered table-striped', 'sortable' => 'true', 'default_sort' => 'name'));
$datatable->values($data);
echo $datatable->render();
echo $paging->render('pagination/basic');
?>

</div> <!-- content-box -->
</div> <!-- content-wrapper -->

