<?php
/**
* Browses directory contents
* 
* This file is used by the editor, to display a lesson's folder contents
* @package eFront
* @version 1.0
*/

//General initializations and parameters
session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";

    $loadScripts = array('scriptaculous/prototype', 'EfrontScripts', 'drag-drop-folder-tree');

    $contentTree = new EfrontContentTree($_SESSION['s_lessons_ID']);
    $visitableIterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($contentTree -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
    $html = $contentTree -> toHTML($visitableIterator, false, array('noclick' => true, 'onclick' => 'setLink(this)'));        

    $smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));
    $smarty -> assign("T_CONTENT_TREE", $html);
    
    
    $smarty -> display("browsecontent.tpl");
    
?>


