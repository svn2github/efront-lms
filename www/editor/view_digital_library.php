<?php 
/**
* View digital library
* 
* This file is used to show the contents of the digital library
* @package eFront
* @version 1.0
*/

/**The viewer functions*/
include_once "dirwalk.php"; 

print '
<table border="0" width="100%">
    <tr><td>';
 
//$dir = urlencode(addslashes(G_LESSONSPATH.$_SESSION['s_lessons_ID']."/Digital Library"));
$dir = G_LESSONSPATH.$_SESSION['s_lessons_ID']."/Digital Library";
main_process($dir, "all_files", $_SESSION['s_lessons_ID']); 

print '
    </td></tr>
</table>
';
?>