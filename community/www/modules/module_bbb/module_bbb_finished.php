<?php
if (isset($_GET['finished_meeting'])) {
    header("location:../../professor.php?ctg=module&op=module_BBB&finished_meeting=".$_GET['finished_meeting']);
}
?>
