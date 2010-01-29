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
        mkdir($dir, 0755);
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
            $ext = pathinfo($entry, PATHINFO_EXTENSION);
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
        $valid_file_types_temp =  array("zip", "html", "htm", "pdf", "doc", "xls", "ppt", "pps", "txt", "jpg", "gif", "png", "exe", "zip", "m", "mp3", "wav", "ra", "avi", "mov", "mpeg", "mid", "wma", "bmp", "wmv", "mp4");
        if($_SESSION['s_type'] == "administrator"){
            $valid_file_types_temp[] = "php";   
        }
    } elseif ($type == "flash") {
        $valid_file_types_temp =  array("swf");
    } elseif ($type == "java") {
        $valid_file_types_temp =  array("class");
    } elseif ($type == "videomusic") {
        $valid_file_types_temp =  array("mp3", "wav", "ra", "avi", "mov", "mpeg", "mpg", "mid", "wma", "wmv", "mp4");
    } elseif ($type == 'all_files') {
        $valid_file_types_temp = array();
    } elseif($type == "media"){
         $valid_file_types_temp =  array("mp3", "wav", "ra", "avi", "mov", "mpeg", "mpg", "mid", "wma", "swf", "wmv", "avi", "mp4");    
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


/**
* Returns the contents of a directory
*
* This function accepts a directory name and returns an array where the elements are 
* the full paths to every file in it, recursively. If the second parameter is specified, 
* then only files of the specified type are returned. If no argument is specified, it searches 
* the current directory and returns every file in it.
* <br/>Example:
* <code>
* $file_list = eF_getDirContents();                 //return current directory contents
* $file_list = eF_getDirContents('/tmp');           //return /tmp directory contents
* $file_list = eF_getDirContents(false, 'php');     //return files with extension php in the current directory and subdirectories
* $file_list = eF_getDirContents(false, array('php', 'html'));     //return files with extension php or html in the current directory and subdirectories
* </code>
*
* @param string $dir The directory to recurse into
* @param mixed $ext Return only files with extension $ext, or in array $ext
* @param bool $get_dir If false, do not append directory information to files
* @param bool $recurse Whether to recurse into subdirectories
* @return array An array with every file and directory inside the directory specified
* @version 1.8
* Changes from version 1.7 to 1.8 (2007/08/10 - peris):
* - Exclude .svn folder from return list
* Changes from version 1.6 to 1.7 (2007/07/30 - peris):
* - Now, it returns directory names along with file names
* Changes from version 1.5 to 1.6 (2007/07/26 - peris):
* - Added $recurse parameter
* Changes from version 1.3 to 1.4 (2007/03/26 - peris):
* - Added $get_dir parameter
* Changes from version 1.2 to 1.3 (2007/03/25 - peris):
* - Changed data type of $ext from string to mixed. Now, $ext can be an array of possible extensions. Also, minor bug fix, in $ext and directories handling
* Changes from version 1.1 to 1.2 (2007/03/05 - peris):
* - Fixed recursion bug (Added $ext parameter to recurse call)
* Changes from version 1.0 to 1.1 (22/12/2005):
* - Added $ext parameter
*/
function eF_getDirContents($dir = false, $ext = false, $get_dir = true, $recurse = true)
{
    if ($dir) {
        $handle = opendir($dir);
    } else {
        $handle = opendir(getcwd());
    }
    
    $filelist = array();
    while (false !== ($file = readdir($handle))) {
        if ($file != "." AND $file != ".." AND $file != '.svn') {
            if (is_dir($dir.$file) && $recurse) {//echo "!$dir . $file@<br>";
                $temp = eF_getDirContents($dir.$file.'/', $ext, $get_dir);
                $get_dir ? $filelist[] = $dir.$file.'/' : $filelist[] = $file.'/';
                if (!$ext) {                      //It is put here for empty directories (when $ext is not specified), or, if $ext is specified, to not return directories
                    $filelist = array_merge($filelist, $temp);
                }
            } else {
                if ($ext) {
                    if (is_array($ext)) {
                        if (in_array(pathinfo($file, PATHINFO_EXTENSION), $ext)) {
                            $get_dir ? $filelist[] = $dir.$file : $filelist[] = $file;
                        }
                    } else {
                        if (pathinfo($file, PATHINFO_EXTENSION) == $ext) {
                            $get_dir ? $filelist[] = $dir.$file : $filelist[] = $file;
                        }
                    }
                } else {
                    $get_dir ? $filelist[] = $dir.$file : $filelist[] = $file;
                }
            }
        }
    }   
    return $filelist;
}



/**
* Handles any file uploading
*
* This function is used to simplify handling and error reporting when we are uploading files.
* <br/>Example:
* <code>
* $timestamp = time();
* list($ok, $upload_messages, $upload_messages_type, $filename) = eF_handleUploads("file_upload", "uploads/", $timestamp."_");  //This will upload all the files specified in the "file_upload" form field, move them to the "uploads" directory and append to their name the current timestamp. 
* //$uploaded_messages is an array with the error or succes message corresponding to each of the uploaded files 
* //$upload_messages_type is an array holding the correspnding message types
* //$filename is an array holding the uploaded files filenames 
* </code>
*
* @param string $field_name The upload file form field name
* @param string $target_dir The directory to put uploaded files into
* @param string $prefix A prefix that the uploaded files will be prepended with
* @param string $ext The extension that is only allowed for the files. If it is false, then we allow all the allowed_extensions
* @param string $target_filename The filename that the uploaded file will have (doesn't work if multiple files uploaded)
* @return array The results array.
* @todo handle better single uploads
* @version 0.9
*/
function eF_handleUploads($field_name, $target_dir, $prefix = '', $target_filename = '', $ext=false) {

    $ok = false;
    $upload_messages = array();
    
    if ($target_dir[mb_strlen($target_dir) - 1] != '/') {
        $target_dir = $target_dir.'/';
    }
    
    if ($prefix) {
        $prefix = $prefix.'_prefix_';
    }
    
    if ($target_filename && sizeof($_FILES[$field_name]['name']) > 1) {
        $target_filename = '';
    }
    
    $allowed_extensions    = eF_getTableData("configuration", "value", "name='allowed_extensions'");
    $disallowed_extensions = eF_getTableData("configuration", "value", "name='disallowed_extensions'");
    if (sizeof($allowed_extensions) == 0 || $allowed_extensions[0]['value'] == '') {
        unset ($allowed_extensions);
    }
    if (sizeof($disallowed_extensions) == 0 || $disallowed_extensions[0]['value'] == '') {
        unset ($disallowed_extensions);
    }    
    if ($ext == false){
        unset($ext);
    }
    
    foreach ($_FILES[$field_name]['name'] as $count => $value) {
        $message_type = 'failure';
        
        $file['tmp_name'] = $_FILES[$field_name]['tmp_name'][$count];
        $file['name']     = $_FILES[$field_name]['name'][$count];
        $file['error']    = $_FILES[$field_name]['error'][$count];
        $file['size']     = $_FILES[$field_name]['size'][$count];

        if ($file['error']) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE : 
                    $upload_messages[$count] = _THEFILE." ".($count + 1)." "._MUSTBESMALLERTHAN." ".ini_get('upload_max_filesize')."<br/>";
                    break;
                case UPLOAD_ERR_FORM_SIZE :
                    $upload_messages[$count] = _THEFILE." ".($count + 1)." "._MUSTBESMALLERTHAN." ".sprintf("%.0f", $_POST['MAX_FILE_SIZE']/1024)." "._KILOBYTES."<br/>";
                    break;
                case UPLOAD_ERR_PARTIAL :
                    $upload_messages[$count] = _PROBLEMUPLOADINGFILE." ".($count + 1);
                    break;
                case UPLOAD_ERR_NO_FILE :
                    //$upload_messages[$count] = _PROBLEMUPLOADINGFILE." ".($count + 1);
                    break;
                case UPLOAD_ERR_NO_TMP_DIR :
                    $upload_messages[$count] = _PROBLEMUPLOADINGFILE." ".($count + 1);
                    break;
                default:
                    $upload_messages[$count] = _PROBLEMUPLOADINGFILE." ".($count + 1);
                    break;
            }
        } else {
            $path_parts = pathinfo($file['name']);
            if ($file['size'] == 0) {
                $upload_messages[] = _FILEDOESNOTEXIST;
            } elseif ((isset($disallowed_extensions) && in_array($path_parts['extension'], explode(",", preg_replace("/\s+/", "", $disallowed_extensions[0]['value'])))) || $path_parts['extension'] == 'php') {           //php files NEVER upload!!!
                $upload_messages[$count] = _YOUCANNOTUPLOADFILESWITHTHISEXTENSION.': .'.$path_parts['extension'].' ('.$file['name'].')<br/>';
            } elseif (isset($allowed_extensions) && $path_parts && !in_array($path_parts['extension'], explode(",", preg_replace("/\s+/", "", $allowed_extensions[0]['value'])))) {
                $upload_messages[$count] = _YOUMAYONLYUPLOADFILESWITHEXTENSION.': '.$allowed_extensions[0]['value'].'<br/>';
            } elseif (!eF_checkParameter($file['name'], 'filename')) {
                $upload_messages[$count] = _INVALIDFILENAME;
            } else if ( isset($ext) && $path_parts && !in_array($path_parts['extension'], explode(",", preg_replace("/\s+/", "", $ext)))){
                $upload_messages[$count] = _YOUMAYONLYUPLOADFILESWITHEXTENSION.': '.$ext.'<br/>';
            } else {
                $new_name    = explode('.', $path_parts['basename']);                                                           //These 3 lines translate greek characters to greeklish characters
                if (!$target_filename) {
                    $new_name[0] = $prefix.$new_name[0];
                    //$new_name[0] = $prefix.iconv('UTF-8', 'ISO-8859-7', $new_name[0]);
                } else {
                    $new_name[0] = $prefix.$target_filename;
                }
                $new_name    = implode('.', $new_name);
                if (move_uploaded_file($file['tmp_name'], $target_dir.$new_name)) {
                    $upload_messages[$count]      = _THEFILE." ".($count + 1)." "._HASBEENSEND."<br/>";
                    $upload_messages_type[$count] = 'success';
                    $ok = true;
                } else {
                    $upload_messages[$count]      = _THEFILE." ".($count + 1)." "._COULDNOTBESEND."<br/>";
                    $upload_messages_type[$count] = 'failure';
                    $ok = false;
                }
            }
            $filename[$count] = $target_dir.$new_name;
        }
    }

    if ($ok) {
        return array($ok, $upload_messages, $upload_messages_type, $filename);
    } else {
        return array($ok, $upload_messages, $upload_messages_type, false);
    }
}
?>