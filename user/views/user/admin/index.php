<div id="content-wrapper">  
<div class="tabtable"> 
    <ul class="nav nav-tabs">
        <li> <?php echo Action::create('admin_user') ?>  </li>
    </ul>
</div> <!-- contentnav -->
<div class="content-box">
<?php
// format data for DataTable
$data = array();
$merge = null;
foreach ($users as $user) {
//    $row = $user->as_array();
   // reformat dates
//    $row['created'] = Helper_Format::friendly_datetime($row['created']);
//    $row['modified'] = Helper_Format::friendly_datetime($row['modified']);
   $row['username'] = HTML::anchor('admin_user/edit/'.$user->id, $user->username);
   $row['last_login'] = Date::fuzzy_span($user->last_login);
   $row['logins'] = $user->logins;
//   $row['last_failed_login'] = Helper_Format::relative_time(strtotime($row['last_failed_login']));
   // add actions
   $row['actions'] = HTML::anchor('admin_user/edit/'.$user->id, __('Edit')).' | '.HTML::anchor('admin_user/delete/'.$user->id, __('Delete'));
   // set roles
   $row['role'] = '';
   foreach($user->roles->where('name', '!=', 'login')->find_all() as $role) {
      $row['role'] .= $role->name.', ';
   }
   // remove last comma
   $row['role'] = substr($row['role'], 0, -2);
// add provider icons
//    if(!empty($providers)) {
//       $row['identities'] = '';
//       $identities = $user->user_identity->find_all();
//       if($identities->count() > 0) {
//          foreach($identities as $identity) {
//             $row['identities'] .= '<img src="/img/tiny/'.$identity->provider.'.png"> ';
//          }
//       }
//    }
   $data[] = $row;
}

$column_list = array( 'username' => array( 'label' => __('Username') ),
                       'role' => array( 'label' => __('Role(s)'), 'sortable' => false ),
                       'last_login' => array( 'label' => __('Last login') ),
                       'logins' => array( 'label' => __('# of logins') ),
                     );
// if(!empty($providers)) {
//    $column_list['identities'] = array('label' => __('Identities'), 'sortable' => false );
// }
$column_list['actions'] = array( 'label' => __('Actions'), 'sortable' => false );
$datatable = new Datatable($column_list, array('paginator' => true, 
  'class' => 'table table-bordered table-striped', 
  'sortable' => 'true', 'default_sort' => 'username'));
$datatable->values($data);
echo $datatable->render();
echo $paging->render();
?>

</div> <!-- content-box -->
</div> <!-- wrapper -->
