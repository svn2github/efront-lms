<?php
/**

 * View Resource

 *

 * This file offers the user the ability to access many efront resources, in a unified way

 * Usage:

 * view_resource.php&type=<type>&id=<identifier>

 *

 * @package eFront

 * @version 3.5.0

 */
//General initialization and parameters
session_cache_limiter('none');
session_start();
$path = "../libraries/";
/** Configuration file.*/
include_once $path."configuration.php";
try {
    switch ($_GET['type']) {
        case 'content':
            $unit = new EfrontUnit($_GET['id']);
            if (!$unit['options']['indexed']) {
                throw new Exception(_RESOURCEISNOTACCESSIBLEFROMOUTSIDE);
            }
            if (!$unit['active']) {
                throw new Exception(_RESOURCEISNOTAVAILABLE);
            }
            echo $unit['data'];
            break;
        default: break;
    }
} catch (Exception $e) {
    eF_redirect("student.php?message=".$e -> getMessage());
}
?>
