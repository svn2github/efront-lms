<?php

if (version_compare('5', PHP_VERSION) > 0) {
    echo "eFront has detected that your system runs PHP 4. eFront requires PHP version 5 and above (preferrably 5.2+) in order to run.";
} else {
    header("location:install.php");
}


?>