<div id="content-wrapper">  
<div class="tabtable"> 
    <ul class="nav nav-tabs">
    </ul>
</div> <!-- contentnav -->
<div class="content-box">
<div class="title"> 
 <h3> <?php echo __('Admin Roles') ?> </h3>
  <div id="metatag"> 
        <span>  </span>
    </div> <!-- metatag -->
</div> <!-- title -->

<div class="content-data">
    <strong><?php echo $role->name; ?></strong>
    <p> <?php echo $role->description ?> </p>

    <h2><?php echo __('List of Roles\'s users') ?></h2>
    <p>  <?php echo __('Total Users ') .': '.count($users) ?>
<?php
    foreach ($users as $user) {
        echo '<p> username : '.Action::profile($user) .'</p>';
    }
?>
</div><!-- content-data -->
</div> <!-- content-box -->
</div> <!-- content-wrapper -->

