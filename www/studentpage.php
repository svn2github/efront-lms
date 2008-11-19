<?php

//session_cache_limiter('none');
//session_start();
//print_r($_SESSION);
$path = "../libraries/";

/** The configuration file.*/
require_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$initwidth = eF_getTableData("configuration", "value", "name = 'sidebar_width'");
if (empty($initwidth)) {
    $sideframe_width = 175;
} else {
    $sideframe_width = $initwidth[0]['value'];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
    <meta http-equiv = "Content-Type"     content = "text/html; charset = utf-8"/>
    <link rel="shortcut icon" href="images/favicon.ico" >
</head>
<script>
var global_sideframe_width = <?php echo $sideframe_width; ?>;
</script>

<frameset framespacing = "0" frameborder = "0" border="no" id = "framesetId" cols = "<?php echo $sideframe_width;?>, *">
  <frame name = "sideframe" src ="new_sidebar.php" scrolling="no"/>
<?php
    if (isset($_GET['message']) && isset($_GET['message_type'])) {
        echo '<frame name = "mainframe" src ="student.php?message='.$_GET['message'].'&message_type='.$_GET['message_type'].'"/>';
    } else {
        echo '<frame name = "mainframe" src ="student.php"/>';
    }
?>  
</frameset>
</html>