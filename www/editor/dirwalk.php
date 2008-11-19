<?php
/**
* Directory and other function for the editor
* 
* This file is used by the editor, and includes various functions, mostly filesystem-related.
* @package eFront
* @version 1.0
*/

//General initializations and parameters
session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";

eF_printHeader(); 


/**
* Utility function. Returns the path minus root folder path
* Also takes out the leading "/"
*/
function cut_root_folder($sub_folder){
    if (mb_strlen($sub_folder) > mb_strlen(G_LESSONSPATH)){
        $fld = str_replace(G_LESSONSPATH, '', $sub_folder);
        $fld = mb_ereg_replace("^/+", "", $fld);
        return $fld;
    } else {
        return "";
    }
}

/**
 Utility function. print a file's size
*/
function print_filesize($file){
    $s = filesize($file);
    if ($s > 1024){
        $s = round($s / 1024);
        return "$s Kb";
    }
    if ($s > 1024*1024){
        $s = round($s / (1024 * 1024));
        return "$s Mb";
    }
    return "$s b";
}


/**
 display the contents of a directory
*/
function display_directory($dir, $valid_file_types, $type, $lessons_ID) 
{
    //$dir = urldecode($dir);
    $dir = mb_ereg_replace("/+", "/", "$dir");
    $dir = mb_ereg_replace("/$", "", $dir);
    $dir = stripslashes($dir);
//echo $dir;   

    if (!($d = dir($dir))) {
        mkdir($dir);
        if (!($d = dir($dir))) {
            echo "\t "._CANNOTOPENFOLDER." - [$dir]";
            return;
        }
    }
    
    if ($type == "all") {
        $parentpage = "add_files.php";
        $target     = 'target = "_parent"';
        echo '<a href = "javascript:void(0)" onClick = "popUp(\'/add_files.php?op=createfolder&dir='.urlencode(str_replace(G_LESSONSPATH, '', $dir)).'\', 300, 300)"><img hspace = "2" src = "icons/close_folder.png" alt = "'._CREATEFOLDER.'" border = "0">'._CREATEFOLDER.'</a><br>';
    } else {
        $parentpage = "editor/browse.php";
    }

    // Εάν υπάρχει λόγω import/export σβήστο;
    if (is_file(G_LESSONSPATH.$lessons_ID."/data.tgz")) {
        @unlink(G_LESSONSPATH.$lessons_ID."/data.tgz");
    }
    
    if($lessons_ID != ""){
        if (G_LESSONSPATH.$lessons_ID != $dir) {                                                //This means that we are in a different folder (namely a subfolder) than the lesson root folder
            if ($_SESSION['s_type'] != "student") {

                $parts = explode('/', $dir);                                        //Make directories array
                unset($parts[sizeof($parts) - 1]);                                  //Unset the last one
                $previousdir = implode('/', $parts);                                //Rebuild path, which is now withot the last part of it
                $previousdir = str_replace(G_LESSONSPATH, '', $previousdir);        //Remove path information from dir

                echo '
                    <a href = "'.$parentpage.'?lessons_ID='.$lessons_ID.'&dir='.urlencode($previousdir).'&for_type='.$type.'" '.$target.'>
                    <img hspace = "2" src = "icons/close_folder.png" border = "0">&laquo;'._BACK.'</a>
                    <br><br/>';
            }
            echo '<br/><img hspace = "2" src = "icons/open_folder.png" border = "0"><B>/'.mb_substr($dir, mb_strlen(G_LESSONSPATH.$lessons_ID) + 1).'</B>';
        }
    }else{
        //echo $dir;
        if (G_ADMINPATH != $dir."/") {                                                //This means that we are in a different folder (namely a subfolder) than the admin root folder

            if ($_SESSION['s_type'] == "administrator") {

                $parts = explode('/', $dir);                                        //Make directories array
                unset($parts[sizeof($parts) - 1]);                                  //Unset the last one
                $previousdir = implode('/', $parts);                                //Rebuild path, which is now withot the last part of it
                $previousdir = str_replace(G_ADMINPATH, '/', $previousdir."/");        //Remove path information from dir

                echo '
                    <a href = "'.$parentpage.'?dir='.urlencode($previousdir).'&for_type='.$type.'" '.$target.'>
                    <img hspace = "2" src = "icons/close_folder.png" border = "0">&laquo;'._BACK.'</a>
                    <br><br/>';
            }
            echo '<br/><img hspace = "2" src = "icons/open_folder.png" border = "0"><B>/'.mb_substr($dir, mb_strlen(G_ADMINPATH) + 1).'</B>';
        }
        
    }
    
    echo "<hr/>";
    $first_time = true;
    while ($entry = $d->read()){
        if (is_file("$dir/$entry")) {
            $ext = eF_getFileExtension($entry);
            if (!is_file("icons/$ext.png")) {
                $ext = "unknown";
            }

            if ($type == "all") {
                echo "<img hspace = \"2\" src = \"icons/$ext.png\" alt = \"\" border = \"0\">\n";
                print_file_name("$dir/$entry", $entry);
                echo " (", print_filesize("$dir/$entry"), ")";
                echo ' (<a href = "javascript:void(0)" onClick = "if (confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')) popUp(\'/add_files.php?op=delete&filename='.urlencode(str_replace(G_LESSONSPATH, '', $dir).'/'.$entry).'\', 300, 300)">'._DELETE.'</a>)<br>'."\n";
            } elseif (in_array($ext, $valid_file_types) || sizeof($valid_file_types) == 0) {
                if ($_SESSION['s_type'] != "student") {
                    echo "<img hspace = \"2\" src = \"icons/$ext.png\" alt = \"\" border = \"0\">\n";
                }
                if ($type == "image") {
 //echo "<br>".$dir."<br>".$entry;                
                    print_copy_link_image("$dir/$entry", $entry);
                } elseif ($type == "files" || $type == 'all_files') { 
                    if ($_SESSION['s_type'] == "student") {
                        if ($first_time) {
                            print '<table width = "100%">';
                            $first_time = false;
                        }
                        print_download_link("$dir/$entry", $entry, $ext);
                    } else {
                        print_copy_link("$dir/$entry", $entry);
                    }
                } elseif ($type == "flash") {
                    print_copy_link_flash("$dir/$entry", $entry);
                } elseif($type == "java") {
                    print_copy_link_java("$dir/$entry", $entry);
                } elseif($type == "videomusic") {
                    print_copy_link_videomusic("$dir/$entry", $entry);
                }elseif($type == "media") {   
                    print_copy_link_videomusic("$dir/$entry", $entry);
                }
                
                if ($_SESSION['s_type'] != "student") {
                    echo " (", print_filesize("$dir/$entry"), ")<br>\n";
                }
            }
        }
        

        if (is_dir("$dir/$entry") && $entry != '.' && $entry != '..') {
            $contents = eF_getDirContents($dir.'/'.$entry.'/');
            if (sizeof($contents) > 0) {
                $confirm_msg = _THISFOLDERCONTAINS.' '.sizeof($contents).' '._FILESANDSUBFOLDERS.'! ';
            }
//echo $dir."<br>".$entry."<br>".$target;   
//echo $dir."<br>";
//echo $entry."<br>";   
//echo $target; 
            if($lessons_ID != ""){

                printf("<a href = \"".$parentpage."?lessons_ID=".$lessons_ID."&dir=%s&for_type=".$type."\" ".$target.">", urlencode(str_replace(G_LESSONSPATH, '', $dir)."/".$entry));
            }elseif($_SESSION['s_type'] == "administrator"){
                printf("<a href = \"".$parentpage."?dir=%s&for_type=".$type."\" ".$target.">", urlencode(str_replace(G_ADMINPATH, '', $dir."/")."/".$entry));  
            }
            printf("<img hspace = \"2\" src = \"icons/close_folder.png\" alt = \""._OPENFOLDER."\" border = \"0\">%s</a>", $entry);
            if ($_SESSION['s_type'] != 'student') {
                echo ' (<a href = "javascript:void(0)" onClick = "if (confirm(\''.$confirm_msg._IRREVERSIBLEACTIONAREYOUSURE.'\')) popUp(\'/add_files.php?op=deletefolder&filename='.urlencode(str_replace(G_LESSONSPATH, '', $dir).'/'.$entry).'\', 300, 300)">'._DELETE.'</a>)<br>'."\n";
            }
        }
    }
    
    if ($_SESSION['s_type'] == "student") {
        print "</table>";
    }
}

/**
 main process...
 NOTE: In order to add a file to the supported list below, apart from adding its extension to the list, you *MUST* supply a corresponding
 icon, and put it in the editor/icons folder
*/
function main_process($dir, $type, $lessons_ID)
{
    if ($type == "image") {
        $valid_file_types_temp =  array("jpg", "gif", "png", "bmp");
    } elseif ($type == "files") {
        $valid_file_types_temp =  array("zip", "html", "htm", "pdf", "doc", "xls", "ppt", "pps", "txt", "jpg", "gif", "png", "exe", "zip", "m", "mp3", "wav", "ra", "avi", "mov", "mpeg", "mid", "wma", "bmp", "wmv");
        if($_SESSION['s_type'] == "administrator"){
            $valid_file_types_temp[] = "php";   
        }
    } elseif ($type == "flash") {
        $valid_file_types_temp =  array("swf");
    } elseif ($type == "java") {
        $valid_file_types_temp =  array("class");
    } elseif ($type == "videomusic") {
        $valid_file_types_temp =  array("mp3", "wav", "ra", "avi", "mov", "mpeg", "mpg", "mid", "wma", "wmv");
    } elseif ($type == 'all_files') {
        $valid_file_types_temp = array();
    } elseif($type == "media"){
         $valid_file_types_temp =  array("mp3", "wav", "ra", "avi", "mov", "mpeg", "mpg", "mid", "wma", "swf", "wmv","avi");    
    }
    
    $valid_file_types = $valid_file_types_temp; // prostheto kai tis antistoixes katalikseis me kefalaia. makriria
    for($i=0; $i<sizeof($valid_file_types_temp); $i++){
        $valid_file_types[] = mb_strtoupper($valid_file_types_temp[$i]);
    }
    
//    $dir = mb_ereg_replace(G_RELATIVELESSONSLINK, "", $dir);
//echo $dir;
    if (!$dir) {    
        $dir = G_LESSONSPATH.$_SESSION['s_lessons_ID'];
    }
    display_directory($dir, $valid_file_types, $type, $lessons_ID);
}


function print_copy_link_image($path, $name) 
{
    
    $imgsize = GetImageSize($path);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $path    = mb_ereg_replace("/+", "/", $path);
//echo $path."<hr>";    
    $path    = str_replace(G_LESSONSPATH, "/".G_RELATIVELESSONSLINK, $path);
    $path    = str_replace(G_ADMINPATH, "/".G_RELATIVEADMINLINK, $path);
    $path    = mb_ereg_replace("/+", "/", $path);
//echo $path;    
    echo "<a href = \"#\" onClick = \"top.document.getElementById('src').value = '".$path."';\">".$name."</a>";
}


function print_download_link($path,  $name,  $ext) 
{
    $filepath = $path;
    $path     = mb_ereg_replace("/+", "/", $path);
    $path     = str_replace(G_LESSONSPATH, "/".G_RELATIVELESSONSLINK, $path);
    $path    = str_replace(G_ADMINPATH, "/".G_RELATIVEADMINLINK, $path);
    $path    = mb_ereg_replace("/+", "/", $path);
    //echo "<tr><td><img hspace = \"2\" src = \"icons/$ext.png\" alt = \"\" border = \"0\" align = middle><a href = \"".$path."\">".$name."</a></td><td>".print_filesize("$filepath")."</td><td>".strftime("%d/%m/%Y", fileatime("$filepath"))."</td></tr>";
    echo "<tr><td width = \"1\"><img hspace = \"2\" src = \"icons/$ext.png\" alt = \"\" border = \"0\" align = middle></td><td><a href = \"view_file.php?file=".$name."&action=download\">".$name."</a></td><td>".print_filesize("$filepath")."</td><td>".strftime("%d/%m/%Y", fileatime("$filepath"))."</td></tr>";
}

function print_copy_link($path,  $name) 
{
    $path = mb_ereg_replace("/+", "/", $path);
    $path = str_replace(G_LESSONSPATH, "/".G_RELATIVELESSONSLINK, $path);
    $path    = str_replace(G_ADMINPATH, "/".G_RELATIVEADMINLINK, $path);
    $path    = mb_ereg_replace("/+", "/", $path);
    echo "<a href = \"#\" onClick = \"top.document.getElementById('href').value = '".$path."';";
    echo "top.document.getElementById('title').value = '".$name."';\">".$name."</a>";
}

function print_copy_link_flash($path, $name) 
{
    $imgsize = GetImageSize($path);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $path    = mb_ereg_replace("/+", "/", $path);
    $path    = str_replace(G_LESSONSPATH, "/".G_RELATIVELESSONSLINK, $path);
    $path    = str_replace(G_ADMINPATH, "/".G_RELATIVEADMINLINK, $path);
    $path    = mb_ereg_replace("/+", "/", $path);
    echo "<a href = \"#\" onClick = \"top.document.forms[0].elements['f_url'].value = '".$path."';";
    echo "top.document.forms[0].elements['f_width'].value = ".$width.";";
    echo "top.document.forms[0].elements['f_height'].value = ".$height.";";
    echo "\">".$name."</a>";
}

function print_copy_link_java($path, $name) 
{
    $path = mb_ereg_replace("/+", "/", $path);
    $path = str_replace(G_LESSONSPATH, "/".G_RELATIVELESSONSLINK, $path);
    $path    = str_replace(G_ADMINPATH, "/".G_RELATIVEADMINLINK, $path);
    $path    = mb_ereg_replace("/+", "/", $path);
    echo "<a href = \"#\" onClick = \"top.document.getElementById('file').value = '".mb_substr(mb_strrchr($path, "/"), 1)."';";
    echo "top.document.getElementById('codebase').value = '".strrev(mb_substr(mb_strstr(strrev($path), "/"), 1))."';";
    echo "\">".$name."</a>";
}//changed

function print_copy_link_videomusic($path, $name) 
{
    $path = mb_ereg_replace("/+", "/", $path);
    $path = str_replace(G_LESSONSPATH, "/".G_RELATIVELESSONSLINK, $path);
    $path    = str_replace(G_ADMINPATH, "/".G_RELATIVEADMINLINK, $path);
    $path    = mb_ereg_replace("/+", "/", $path);
    echo "<a href = \"#\" onClick = \"top.document.getElementById('src').value = '".$path."';";
    echo "\">".$name."</a>";
}

function print_file_name($path,  $name) 
{
    $path = mb_ereg_replace("/+", "/", $path);
    $path = str_replace(G_LESSONSPATH, "/".G_RELATIVELESSONSLINK, $path);
    $path    = str_replace(G_ADMINPATH, "/".G_RELATIVEADMINLINK, $path);
    $path    = mb_ereg_replace("/+", "/", $path);
    echo $name;
}
?>