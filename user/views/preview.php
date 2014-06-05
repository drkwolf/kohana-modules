<html>
<head>
 <title> <?php echo $title ?></title>
    <?php echo HTML::style('public/js/qtip/jquery.qtip.min.css'); ?>
    <?php echo HTML::style('public/css/site/style.css'); ?>

 <!-- end test }}} -->
</head>
<body>
<div class="content-data">
<?php
    echo Message::display();
    if(isset($content))
    {
        echo $content;
    }
?>
</div> <!-- content  -->
</body>
</html>
