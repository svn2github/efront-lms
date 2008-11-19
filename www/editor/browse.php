<?php
/**
* This file is used to display a small files list, and is used 
* inside the "insert image" operation of the editor
*/

//General initialization and parameters
session_cache_limiter('none');
session_start();

$path = "../../libraries/";
/** Configuration file.*/
include_once $path."configuration.php";

//error_reporting(E_ALL);
try {
    if ($_SESSION['s_type'] == 'administrator') {
        $rootDir = new EfrontDirectory(G_ADMINPATH);
    } else {
        $rootDir = new EfrontDirectory(G_LESSONSPATH.$_SESSION['s_lessons_ID'].'/');
    }

    if (isset($_GET['directory'])) {
        $directory = new EfrontDirectory($_GET['directory']);
        if (strpos($directory['path'], $rootDir['path']) === false) {
            $directory = $rootDir;
        } else {
            EfrontDirectory :: normalize($directory['directory']) == EfrontDirectory :: normalize($rootDir['path']) ? $smarty -> assign("T_PARENT_DIR", '') : $smarty -> assign("T_PARENT_DIR", $directory['directory']);
        }
    } else {
        $directory = $rootDir;
    }

    $offset = str_replace(G_CONTENTPATH, '', $directory['path']);
    
    switch ($_GET['for_type']) {
        case 'image': $mode = true; $filter = array_keys(FileSystemTree :: getFileTypes('image')); break;
        case 'java' : $mode = true; $filter = array_keys(FileSystemTree :: getFileTypes('java'));  break;
        case 'media': $mode = true; $filter = array_keys(FileSystemTree :: getFileTypes('media')); break;
        case 'files': $mode = false; $filter = array(); break;
        default     : $mode = true; $filter = array(); break;        
    }

    $filesystem =  new FileSystemTree($directory['path']);
    $directory != $rootDir ? $tree = $filesystem -> seekNode($directory['path']) : $tree = $filesystem -> tree;
    foreach (new EfrontDirectoryOnlyFilterIterator(new EfrontNodeFilterIterator(new ArrayIterator($tree, RecursiveIteratorIterator :: SELF_FIRST))) as $key => $value) {
        $value['image']    = $value -> getTypeImage();
        $files[]           = $value;
    }
    foreach (new EfrontFileOnlyFilterIterator(new EfrontFileTypeFilterIterator(new EfrontNodeFilterIterator(new ArrayIterator($tree, RecursiveIteratorIterator :: SELF_FIRST)), $filter, $mode)) as $key => $value) {
        $value['image']    = $value -> getTypeImage();
        $files[]           = $value;
    }

    $smarty -> assign("T_FILES", $files);
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}

$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_OFFSET", $offset);
//pr($offset);
$smarty -> display("browse.tpl");
?>