<?php
if (isset($_GET['finished_meeting'])) {
    eF_redirect(" ../../professor.php?ctg=module&op=module_dimdim&finished_meeting=".$_GET['finished_meeting']);
}
?>