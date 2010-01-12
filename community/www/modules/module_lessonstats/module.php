<?php

if (is_file("lang-".$_SESSION['s_language'].".php")) {
    include "lang-".$_SESSION['s_language'].".php";
} else {
    include "lang-english.php";
}

?>