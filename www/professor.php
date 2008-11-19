<?php
/**
* Professor main page
*
* This page performs all professor functions
* @package eFront
* @version 1.0
*/
$debug_TimeStart = microtime(true);     //Debugging timer - initialization

session_cache_limiter('none');          //Initialize session
session_start();

$path = "../libraries/";                //Define default path
//error_reporting(E_ALL);
//echo "AAA";exit;
/** The configuration file.*/
require_once $path."configuration.php";
$debug_InitTime = microtime(true) - $debug_TimeStart;       //Debugging timer - time spent on file inclusion

//Set headers in order to eliminate browser cache (especially IE's)'
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

//If the page is shown as a popup, make sure it remains in such mode
if (isset($_GET['popup']) || isset($_POST['popup']) || (strpos(strtolower($_SERVER['HTTP_REFERER']), 'popup') != false && !strpos(strtolower($_SERVER['HTTP_REFERER']), 'evaluation'))) {
    output_add_rewrite_var('popup', 1);
    $smarty -> assign("T_POPUP_MODE", true);
    $popup = 1;
}

$message = '';$message_type = '';                            //Initialize messages, because if register_globals is turned on, some messages will be displayed twice
$loadScripts = array('scriptaculous/prototype', 'EfrontScripts');

/*Check the user type. If the user is not valid or not a professor, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login'], false, 'professor');
        $smarty -> assign("T_CURRENT_USER", $currentUser);
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        echo "<script>parent.location = 'index.php?message=".urlencode($message)."&message_type=failure'</script>";        //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
        //header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    setcookie('c_request', $_SERVER['REQUEST_URI'], time() + 300);
    echo "<script>parent.location = 'index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure'</script>";        //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
    //header("location:index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure");
    exit;
}

if ($_COOKIE['c_request']) {
    setcookie('c_request', '', time() - 86400);
    header("location:".$_COOKIE['c_request']);
}
$roles = EfrontLessonUser :: getLessonsRoles();

/*This is the first time the professor enters this lesson, so register the lesson id to the session*/
if (isset($_GET['lessons_ID']) && eF_checkParameter($_GET['lessons_ID'], 'id') && (!isset($_SESSION['s_lessons_ID']) || $_GET['lessons_ID'] != $_SESSION['s_lessons_ID'])) {
    $userLessons = $currentUser -> getLessons();
    if (in_array($_GET['lessons_ID'], array_keys($userLessons))) {
        $_SESSION['s_lessons_ID'] = $_GET['lessons_ID'];
        $_SESSION['s_type']       = $roles[$userLessons[$_GET['lessons_ID']]];

        $smarty -> assign("T_CHANGE_LESSON", "true");
        $smarty -> assign("T_REFRESH_SIDE", "true");
    } else {
        unset($_GET['lessons_ID']);
        $message      = _YOUCANNOTACCESSTHISLESSONORITDOESNOTEXIST;
        $message_type = 'failure';
        $ctg          = 'personal';
    }
}

if (isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID']) {    //Check validity of current lesson
    $userLessons = $currentUser -> getLessons();
    if (!isset($userLessons[$_SESSION['s_lessons_ID']]) || $roles[$userLessons[$_SESSION['s_lessons_ID']]] != 'professor') {
        header("location:student.php?ctg=lessons");    //redirect to student's lessons page
    }
    try {
        $currentUser    -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]);                //Initialize user's role options for this lesson
        $currentLesson  = new EfrontLesson($_SESSION['s_lessons_ID']);                //Initialize lesson
        $smarty -> assign("T_TITLE_BAR", $currentLesson -> lesson['name']);
    } catch (Exception $e) {
        unset($_SESSION['s_lessons_ID']);
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:".basename($_SERVER['PHP_SELF'])."?message=".urlencode($message)."&message_type=failure");
    }
    try {
        $currentContent = new EfrontContentTree($_SESSION['s_lessons_ID']);           //Initialize content
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    }
}

/*Check current unit*/
if (isset($_GET['view_unit']) && eF_checkParameter($_GET['view_unit'], 'id') && $currentContent) {
    if ($currentUser -> coreAccess['content'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    try {
        $currentUnit = $currentContent -> seekNode($_GET['view_unit']);              //Initialize current unit       
        $currentUnit['ctg_type'] == 'tests' ? $_GET['ctg'] = 'tests' : $_GET['ctg'] = 'content';
    } catch (Exception $e) {
        unset($_GET['view_unit']);
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message      = $e -> getMessage().'( '.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
}

// Share the hcd value with smarty
$module_hcd_interface = MODULE_HCD_INTERFACE;
$smarty -> assign("T_MODULE_HCD_INTERFACE", $module_hcd_interface);

///MODULE1: Import
$loadedModules = $currentUser -> getModules();

$module_css_array = array();
$module_js_array = array();

// Include module languages
foreach ($loadedModules as $module) {
    // The $setLanguage variable is defined in globals.php
    $mod_lang_file = $module -> getLanguageFile($setLanguage);
    if (is_file ($mod_lang_file)) {
        require_once $mod_lang_file;
    }

    // Get module css
    if($mod_css_file = $module -> getModuleCSS()) {
        if (is_file ($mod_css_file)) {

            // Get the relative path
            if ($position = strpos($mod_css_file, "modules")) {
                $mod_css_file = substr($mod_css_file, $position);
            }
            $module_css_array[] = $mod_css_file;
        }
    }

    // Get module js
    if($mod_js_file = $module -> getModuleJS()) {
        if (is_file($mod_js_file)) {
            // Get the relative path
            if ($position = strpos($mod_js_file, "modules")) {
                $mod_js_file = substr($mod_js_file, $position);
            }

            $module_js_array[] = $mod_js_file;
        }
    }
}


/*Added Session variable for search results*/
$_SESSION['referer'] = $_SERVER['REQUEST_URI'];


/*These are the possible ctg we can have. - The three last added by HCD */
$possible_ctgs = array('control_panel', 'content', 'scheduling', 'tests', 'rules', 'calendar','module',
                       'statistics', 'survey', 'glossary', 'settings', 'lessons', 'personal',
                       'projects','module_hcd', 'users','emails','evaluations');
if (sizeof($module_ctgs) > 0) {
    $possible_ctgs = array_merge($possible_ctgs, array_keys($module_ctgs));
}
!isset($_GET['ctg']) || !in_array($_GET['ctg'], $possible_ctgs)  ? $ctg = "control_panel" : $ctg = $_GET['ctg'];    //The default ctg is 'control_panel'

if (!$_SESSION['s_lessons_ID'] && ($ctg != 'personal' && $ctg != 'statistics') && ($ctg == 'control_panel' && $_GET['op'] != "search")) {       //If there is not a lesson in the session, then the user just logged into the system. Redirect him to lessons page, except for the case he is viewing his personal information 2007/07/27 added search control. It was a problem when user had not choose a lesson.
    $ctg = 'lessons';
}

$smarty -> assign("T_CTG", $ctg);       //As soon as we derive the current ctg, assign it to smarty.
$smarty -> assign("T_OP", $_GET['op']);


/*
Control panel is the first page that the professor sees, and contains links to most of the available functions
At the control panel main page, you will find 7 sections:
- A Settings list, with icons representing available functions
- Lesson announcements
- Recent forum messages
- Recent personal messages
- A list with test questions that need to be corrected
- Recent comments
- The calendar
*/
if ($ctg == 'control_panel') {
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
    $loadScripts[] = 'scriptaculous/dragdrop';
    /*
    Insert a record into the logs table, if a lesson has been selected
    */
    if (isset($_SESSION['s_lessons_ID'])) {
        $fields_log = array ('users_LOGIN' => $_SESSION['s_login'],                                 //This is the log entry array
                             'timestamp'   => time(),
                             'action'      => 'lesson',
                             'comments'    => 0,
                             'session_ip'  => eF_encodeIP($_SERVER['REMOTE_ADDR']),
                             'lessons_ID'  => $_SESSION['s_lessons_ID']);
        eF_deleteTableData("logs", "users_LOGIN='".$_SESSION['s_login']."' AND action='lastmove'"); //Only one lastmove action interests us, so delete any other
        eF_insertTableData("logs", $fields_log);
    }


    /*
    SCORM related functions
    */
    if (isset($_GET['op']) && $_GET['op'] == 'scorm') {
        $loadScripts[] = 'drag-drop-folder-tree';

        if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
            $options = array(array('image' => '16x16/book_red.png',      'title' => _SCORMTREE,   'link' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=scorm',                'selected' => $_GET['scorm_review'] || $_GET['scorm_import'] || $_GET['scorm_export'] ? false : true),
                             array('image' => '16x16/document_text.png', 'title' => _SCORMREVIEW, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=scorm&scorm_review=1', 'selected' => !$_GET['scorm_review'] ? false : true),
                             array('image' => '16x16/import1.png',       'title' => _SCORMIMPORT, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=scorm&scorm_import=1', 'selected' => !$_GET['scorm_import'] ? false : true),
                             array('image' => '16x16/export1.png',       'title' => _SCORMEXPORT, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=scorm&scorm_export=1', 'selected' => !$_GET['scorm_export'] ? false : true));
        } else {
            $options = array(array('image' => '16x16/book_red.png',      'title' => _SCORMTREE,   'link' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=scorm',                'selected' => $_GET['scorm_review'] || $_GET['scorm_import'] || $_GET['scorm_export'] ? false : true),
                             array('image' => '16x16/document_text.png', 'title' => _SCORMREVIEW, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=scorm&scorm_review=1', 'selected' => !$_GET['scorm_review'] ? false : true));
        }
        $smarty -> assign("T_TABLE_OPTIONS", $options);

        if ($_GET['scorm_review']) {
            $iterator = new EfrontSCORMFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
            foreach ($iterator as $key => $value) {
                $scormContentIds[] = $key;
            }
            $result = eF_getTableData("scorm_data, content, users", "scorm_data.*, content.name as content_name, users.name, users.surname", "scorm_data.users_LOGIN != '' and scorm_data.content_ID IN (".implode(",", $scormContentIds).") and content_ID=content.id and users.login=scorm_data.users_LOGIN");
            foreach ($result as $value) {
                //$scormData[$value['users_LOGIN']] = $value;
            }
            $scormData = $result;
            //$smarty -> assign("T_SCORM_DATA", $scormData);
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'scormUsersTable') {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }
                $scormData = eF_multiSort($scormData, $sort, $order);
                $smarty -> assign("T_USERS_SIZE", sizeof($scormData));
                if (isset($_GET['filter'])) {
                    $scormData = eF_filterData($scormData, $_GET['filter']);
                }
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $scormData = array_slice($scormData, $offset, $limit);
                }
                $smarty -> assign("T_SCORM_DATA", $scormData);
                $smarty -> display('professor.tpl');
                exit;
            }

            foreach ($scormData as $value) {
                $scormIds[] = $value['id'];
            }

            if (isset($_GET['delete']) && in_array($_GET['delete'], $scormIds)) {
                eF_deleteTableData("scorm_data", "id=".$_GET['delete']);
                exit;
            }
        } else if ($_GET['scorm_import']) {
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }

            try {
                if (is_dir(G_SCORMPATH)) {
                    $directory = new EfrontDirectory(G_SCORMPATH);
                    $directory -> delete();
                }
                mkdir(G_SCORMPATH, 0755);
                    
                $filesystem = new FileSystemTree(G_SCORMPATH);

                $form = new HTML_QuickForm("upload_scorm_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=scorm&scorm_import=1', "", null, true);
                $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

                $form -> addElement('file', 'scorm_file', _SCORMFILEINZIPFORMAT);
                $form -> setMaxFileSize($filesystem -> getUploadMaxSize() * 1024);
                $form -> addElement('submit', 'submit_upload_scorm', _SUBMIT, 'class = "flatButton"');

                $timestamp       = time();
                $lessons_ID = $currentLesson -> lesson['id'];
                if ($form -> isSubmitted() && $form -> validate()) {
                    $scormFile       = $filesystem -> uploadFile('scorm_file', G_SCORMPATH);
                    $scormFolderName = EfrontFile :: encode(basename($scormFile['name'], '.zip'));
                    $fileList        = $scormFile  -> uncompress();

                    $total_fields = array();
                    $resources    = array();

                    $manifestFile = new EfrontFile(G_SCORMPATH.'imsmanifest.xml');
                    $manifestXML  = file_get_contents($manifestFile['path']);
                    $tagArray     = EfrontScorm :: parseManifest($manifestXML);

                    foreach($tagArray as $key => $value) {
                        $fields = array();
                        switch ($value['tag']) {
                            case 'TITLE':
                                $cur = $value['parent_index'];
                                $total_fields[$cur]['name'] = $value['value'];
                                break;

                            case 'ITEM':
                                $total_fields[$key]['lessons_ID'] = $lessons_ID;
                                $total_fields[$key]['timestamp']  = time();
                                $total_fields[$key]['ctg_type']   = 'scorm';
                                $total_fields[$key]['active']     = 1;
                                $references[$key] = $value['attributes']['IDENTIFIERREF'];
                                break;

                            case 'RESOURCE':
                                $resources[$key] = $value['attributes']['IDENTIFIER'];
                                break;

                            case 'FILE':
                                $files[$key] = $value['attributes']['HREF'];
                                break;

                            case 'ADLCP:MAXTIMEALLOWED':
                                $maxtimeallowed[$key]  = $value['value'];
                                break;
                            case 'ADLCP:TIMELIMITACTION':
                                $timelimitaction[$key] = $value['value'];
                                break;
                            case 'ADLCP:MASTERYSCORE':
                                $masteryscore[$key]    = $value['value'];
                                break;
                            case 'ADLCP:DATAFROMLMS':
                                $datafromlms[$key]     = $value['value'];
                                break;

                            case 'ADLCP:PREREQUISITES':
                                $prerequisites[$key]   = $value['value'];
                                break;

                            default:
                                break;
                        }
                    }

                    foreach ($references as $key => $value) {
                        $ref = array_search($value, $resources);
                        if ($ref) {
                            $data = file_get_contents(G_SCORMPATH."/".$tagArray[$ref]['attributes']['HREF']);

                            $primitive_hrefs[$ref] = $tagArray[$ref]['attributes']['HREF'];
                            $path_part[$ref]       = dirname($primitive_hrefs[$ref]);

                            foreach($tagArray[$ref]['children'] as $value2) {
                                if ($tagArray[$value2]['tag'] == 'DEPENDENCY') {
                                    $idx = array_search($tagArray[$value2]['attributes']['IDENTIFIERREF'], $resources);

                                    foreach ($tagArray[$idx]['children'] as $value3) {
                                        if ($tagArray[$value3]['tag'] == 'FILE')  {
                                            $data = preg_replace("#(\.\.\/(\w+\/)*)?".$tagArray[$value3]['attributes']['HREF']."#", $currentLesson -> getDirectory()."/".$scormFolderName.'/'.$path_part[$ref]."/$1".$tagArray[$value3]['attributes']['HREF'], $data);
                                        }
                                    }
                                }
                            }
                            //$total_fields[$key]['data'] = eF_postProcess(str_replace("'","&#039;",$data));
                            $total_fields[$key]['data'] = '<iframe height = "100%"  width = "100%" frameborder = "no" name = "scormFrameName" id = "scormFrameID" src = "'.G_RELATIVELESSONSLINK.$lessons_ID."/".$scormFolderName.'/'.$primitive_hrefs[$ref].'" onload = "eF_js_setCorrectIframeSize()"></iframe><iframe name = "commitFrame" frameborder = "no" id = "commitFrame" width = "1" height = "1" style = "display:none"></iframe>';
                        }
                    }
                    $lastUnit = $currentContent -> getLastNode();
                    $lastUnit ? $this_id  = $lastUnit['id'] : $this_id = 0;
                    //$this_id = $tree[sizeof($tree) - 1]['id'];
                    foreach ($total_fields as $key => $value)  {
                        if (isset($value['ctg_type']))  {
                            $total_fields[$key]['previous_content_ID'] = $this_id;

                            if (!isset($total_fields[$key]['parent_content_ID'])) {
                                $total_fields[$key]['parent_content_ID'] = 0;
                            }

                            $total_fields[$key]['options'] = serialize(array('hide_complete_unit' => 1));
                            $this_id = eF_insertTableData("content", $total_fields[$key]);

                            $tagArray[$key]['this_id'] = $this_id;
                            foreach($tagArray[$key]['children'] as $key2 => $value2) {
                                if (isset($total_fields[$value2])) {
                                    $total_fields[$value2]['parent_content_ID'] = $this_id;
                                }
                            }
                        } else  {
                            unset($total_fields[$key]);
                        }
                    }
                    $directory = new EfrontDirectory(G_SCORMPATH);
                    $directory -> copy(EfrontDirectory :: normalize($currentLesson -> getDirectory()).'/'.$scormFolderName, true);

                    //foreach ($files as $key => $value) {
                            //$newhref = $tagArray[$tagArray[$key]['parent_index']]['attributes']['XML:BASE'];
                            //copy(G_SCORMPATH."/".rtrim($newhref,"/")."/".rtrim($value,"/"), rtrim($currentLesson -> getDirectory(), "/")."/$this_id/".rtrim($newhref,"/")."/".rtrim($value,"/"));    //$this_id is put here so we can be sure that the files are put in a unique folder
                    //}

                    foreach ($timelimitaction as $key => $value) {
                        $content_ID = $tagArray[$tagArray[$key]['parent_index']]['this_id'];

                        $fields_insert[$content_ID]['content_ID']      = $content_ID;
                        $fields_insert[$content_ID]['timelimitaction'] = $value;
                    }
                    foreach ($maxtimeallowed as $key => $value) {
                        $content_ID = $tagArray[$tagArray[$key]['parent_index']]['this_id'];

                        $fields_insert[$content_ID]['content_ID']     = $content_ID;
                        $fields_insert[$content_ID]['maxtimeallowed'] = $value;
                    }
                    foreach ($masteryscore as $key => $value) {
                        $content_ID = $tagArray[$tagArray[$key]['parent_index']]['this_id'];

                        $fields_insert[$content_ID]['content_ID']   = $content_ID;
                        $fields_insert[$content_ID]['masteryscore'] = $value;
                    }
                    foreach ($datafromlms as $key => $value) {
                        $content_ID = $tagArray[$tagArray[$key]['parent_index']]['this_id'];

                        $fields_insert[$content_ID]['content_ID']  = $content_ID;
                        $fields_insert[$content_ID]['datafromlms'] = $value;
                    }

                    foreach ($fields_insert as $key => $value) {
                        eF_insertTableData("scorm_data", $value);
                        if (isset($value['masteryscore']) && $value['masteryscore']) {
                            eF_updateTableData("content", array("ctg_type" => "scorm_test"), "id=".$value['content_ID']);
                        }
                    }


                    foreach ($prerequisites as $key => $value) {
                        foreach ($tagArray as $key2 => $value2) {
                            if (isset($value2['attributes']['IDENTIFIER']) && $value2['attributes']['IDENTIFIER'] == $value) {
                                unset($fields_insert);
                                $fields_insert['users_LOGIN'] = "*";
                                $fields_insert['content_ID']  = $tagArray[$tagArray[$key]['parent_index']]['this_id'];
                                $fields_insert['rule_type']   = "hasnot_seen";
                                $fields_insert['rule_content_ID'] = $value2['this_id'];
                                $fields_insert['rule_option'] = 0;
                                eF_insertTableData("rules", $fields_insert);
                            }
                        }
                    }

                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=scorm&message=".urlencode(_SUCCESSFULLYIMPORTEDSCORMFILE)."&message_type=success");
                }
                $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                $form -> accept($renderer);
                $smarty -> assign('T_UPLOAD_SCORM_FORM', $renderer -> toArray());
            } catch (EfrontFileException $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = failure;
            }
        } else if ($_GET['scorm_export']) {
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }
            $form = new HTML_QuickForm("export_scorm_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=scorm&scorm_export=1', "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('submit', 'submit_export_scorm', _EXPORT, 'class = "flatButton"');
            if ($form -> isSubmitted() && $form -> validate()) {
                define ('SCORM_FOLDER', G_ROOTPATH."www/content/scorm_data");
                $scorm_filename = "scorm_lesson".$lessons_id.".zip";

                if (is_file(SCORM_FOLDER."/".$scorm_filename)) {
                    unlink(SCORM_FOLDER."/".$scorm_filename);
                }

                $lessons_id = $currentLesson -> lesson['id'];

                try {
                    $filesystem = new FileSystemTree($currentLesson -> getDirectory());
                    foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator($filesystem -> tree, RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
                        ($value instanceOf EfrontDirectory) ? $filelist[] = preg_replace("#".$currentLesson -> getDirectory()."#", "", $key).'/' : $filelist[] = preg_replace("#".$currentLesson -> getDirectory()."#", "", $key);
                    }

                    $lesson_entries = eF_getTableData("content", "id,name,data", "lessons_ID=" . $lessons_id . " and ctg_type!='tests' and active=1");

                    create_manifest($lessons_id, $lesson_entries, $filelist, SCORM_FOLDER);

                    $scormDirectory = new EfrontDirectory(SCORM_FOLDER  ."/lesson". $lessons_id."/");
                    $compressedFile = $scormDirectory -> compress(false, false, true);
                    $scormDirectory -> delete();


                    $smarty -> assign("T_SCORM_EXPORT_FILE", $compressedFile);
                    $smarty -> assign("T_MESSAGE", _SUCCESSFULLYEXPORTEDSCORMFILE);
                    $smarty -> assign("T_MESSAGE_TYPE", "success");
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = "failure";
                }
            }
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);

            $smarty -> assign('T_EXPORT_SCORM_FORM', $renderer -> toArray());

        } else {
            $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
            $loadScripts[] = 'scriptaculous/effects';
            $iterator = new EfrontSCORMFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));

            $iterator   -> rewind();
            $current    = $iterator -> current();
            $depth      = $iterator -> getDepth();
            $treeString = '';
            $count      = 0;                                //Counts the total number of nodes, used to signify whether the tree has content
            while ($iterator -> valid()) {
                $scormUnitIds[] = $current['id'];
                $iterator -> next();
                if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
                    if ($current['ctg_type'] == 'scorm') {
                        $toolsString = '<span><a href = "javascript:void(0)" onclick = "convertScorm(this, '.$current['id'].')"><img style = "vertical-align:middle" src = "images/16x16/scorm_to_test.png" title = "'._CONVERTTOSCORMTEST.'" alt = "'._CONVERTTOSCORMTEST.'" border = "0" /></a></span>';
                    } else {
                        $toolsString = '<span><a href = "javascript:void(0)" onclick = "convertScorm(this, '.$current['id'].')"><img style = "vertical-align:middle" src = "images/16x16/test_to_scorm.png" title = "'._CONVERTTOSCORMCONTENT.'" alt = "'._CONVERTTOSCORMCONTENT.'" border = "0" /></a></span>';
                    }
                }
                $treeString  .= '
                    <li style = "white-space:nowrap;" class = "'.($current['ctg_type'] == 'scorm' ? 'scorm' : 'scorm_test').'" id = "node'.$current['id'].'" noDrag = "true" noRename = "true" noDelete = "true">
                        <a class = "treeLink" href = "javascript:void(0)" title = "'.$current['name'].'">'.$current['name']."</a>&nbsp;".$toolsString;

                $iterator -> getDepth() > $depth ? $treeString .= '<ul>' : $treeString .= '</li>';
                for ($i = $depth; $i > $iterator -> getDepth(); $i--) {
                    $treeString .= '</ul></li>';
                }
                $current = $iterator -> current();
                $depth   = $iterator -> getDepth();
                $count++;
            }

            if (isset($_GET['set_type']) && isset($_GET['id']) && in_array($_GET['id'], $scormUnitIds)) {        //Set scorm content type through AJAX call
                $unit = new EfrontUnit($_GET['id']);
                $_GET['set_type'] == 'scorm' ? $unit['ctg_type'] = 'scorm' : $unit['ctg_type'] = 'scorm_test';
                $unit -> persist();
                exit;
            }
            //$smarty -> assign("T_SCORM_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree', array('expand' => true)));
            $smarty -> assign("T_SCORM_TREE", $treeString);
        }

        //$scormOptions[] = array('text' => _SCORMEXPORT,       'image' => "32x32/export1.png",         'href' => "scorm_export.php?lessons_ID=".$_SESSION['s_lessons_ID'], 'onClick' => "eF_js_showDivPopup('"._SCORMEXPORT."',     new Array('500px', '300px'))", 'target' => 'POPUP_FRAME');
        //$scormOptions[] = array('text' => _SCORMIMPORT,       'image' => "32x32/import1.png",         'href' => "scorm_import.php?lessons_ID=".$_SESSION['s_lessons_ID'], 'onClick' => "eF_js_showDivPopup('"._SCORMIMPORT."',     new Array('500px', '300px'))", 'target' => 'POPUP_FRAME');
        //$scormOptions[] = array('text' => _REVIEWSCORMDATA,   'image' => "32x32/document_text.png",   'href' => "scorm_review.php?lessons_ID=".$_SESSION['s_lessons_ID'], 'onClick' => "eF_js_showDivPopup('"._REVIEWSCORMDATA."', new Array('500px', '300px'))", 'target' => 'POPUP_FRAME');

    }
    /*
    Manage lesson information. There are 6 types of lesson information: General Description,
    Objectives, Assessement, Lesson Topics, Resources and Other Information. the professor may
    set whichever and as many as he wants
    */
    elseif (isset($_GET['op']) && $_GET['op'] == 'lesson_information') {
        if ($currentUser -> coreAccess['content'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
        $loadScripts[] = 'scriptaculous/effects';
        $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);
        try {
            $lessonInformation = unserialize($currentLesson -> lesson['info']);
            $information       = new LearningObjectInformation($lessonInformation);
            if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
                $smarty -> assign("T_LESSON_INFO_HTML", $information -> toHTML($form, false));
            } else {
                $smarty -> assign("T_LESSON_INFO_HTML", $information -> toHTML($form, false, false));
            }

            $lessonMetadata = unserialize($currentLesson -> lesson['metadata']);
            $metadata       = new DublinCoreMetadata($lessonMetadata);
            if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
                $smarty -> assign("T_LESSON_METADATA_HTML", $metadata -> toHTML($form));
            } else {
                $smarty -> assign("T_LESSON_METADATA_HTML", $metadata -> toHTML($form, true, false));
            }
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = "failure";
        }
/*
        $lessonAvatarForm = new HTML_QuickForm("lesson_avatar_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=lesson_info', "", null, true);
        $lessonAvatarForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
        $lessonAvatarForm -> addElement('file', 'file_upload', _IMAGEFILE, 'class = "inputText"');
        $lessonAvatarForm -> addElement('advcheckbox', 'delete_avatar', _DELETECURRENTAVATAR, null, 'class = "inputCheckbox"', array(0, 1));
        $lessonAvatarForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB
        $lessonAvatarForm -> addElement('submit', 'submit_upload_file', _APPLYAVATARCHANGES, 'class = "flatButton"');
        if ($lessonAvatarForm -> isSubmitted() && $lessonAvatarForm -> validate()) {
            
        }
*/        
        if (isset($_GET['postAjaxRequest'])) {
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                header("HTTP/1.0 500 ");
                echo (_UNAUTHORIZEDACCESS);
                exit;
            }
            if (in_array($_GET['dc'], array_keys($information -> metadataAttributes))) {
                if ($_GET['value']) {
                    $lessonInformation[$_GET['dc']] = ($_GET['value']);
                } else {
                    unset($lessonInformation[$_GET['dc']]);
                }
                $currentLesson -> lesson['info'] = addSlashes(serialize($lessonInformation));
            } elseif (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
                if ($_GET['value']) {
                    $lessonMetadata[$_GET['dc']] = ($_GET['value']);
                } else {
                    unset($lessonMetadata[$_GET['dc']]);
                }
                $currentLesson -> lesson['metadata'] = addSlashes(serialize($lessonMetadata));
            }

            try {
                $currentLesson -> persist();
                echo $_GET['value'];
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    }

    /*Include the module that is used to perform the searches*/
    elseif (isset($_GET['op']) && $_GET['op'] == 'search') {
        /**Functions to perform searches*/
        require_once "module_search.php";
    }

    /*Show the announcements (news) full page*/
    elseif (isset($_GET['op']) && $_GET['op'] == 'news') {
        if (isset($currentUser -> coreAccess['news']) && $currentUser -> coreAccess['news'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");exit;
        }
        //$news = eF_getTableData("news", "*", "lessons_ID=".$currentLesson -> lesson['id']);
        $smarty -> assign("T_NEWS", eF_getNews());
    }

    /*
    The progress section presents a list with the students that have completed the lesson
    Here the professor may set additional students' lesson status to completed and issue certificates.
    He also edits the certificate that is issued from the system.
    */
    elseif (isset($_GET['op']) && $_GET['op'] == 'progress') {
        if (isset($currentUser -> coreAccess['progress']) && $currentUser -> coreAccess['progress'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");exit;
        }
        $load_editor = true;
        if (isset($_GET['edit_user']) && eF_checkParameter($_GET['edit_user'], 'login')) {
            //$lessonUser  = EfrontUserFactory :: factory($_GET['edit_user']);

            $form = new HTML_QuickForm("edit_user_complete_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=progress&edit_user='.$_GET['edit_user'], "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

            $form -> addElement('advcheckbox', 'completed', _COMPLETED, null, 'class = "inputCheckbox"');            //Whether the user has completed the lesson
            $form -> addElement('text', 'score', _SCORE, 'class = "inputText"');                                                        //The user lesson score
            $form -> addRule('score', _THEFIELD.' "'._SCORE.'" '._MUSTBENUMERIC, 'numeric', null, 'client');                            //The score must be numeric
            $form -> addRule('score', _RATEMUSTBEBETWEEN0100, 'callback', create_function('&$a', 'return ($a >= 0 && $a <= 100);'));    //The score must be between 0 and 100

            $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputContentTextarea simpleEditor" style = "width:100%;height:5em;"');      //Comments on student's performance

            $user_data = eF_getTableData("users_to_lessons", "*", "users_LOGIN='".$_GET['edit_user']."' and lessons_ID=".$_SESSION['s_lessons_ID']);
            $userStats = EfrontStats::getUsersLessonStatus($currentLesson, $_GET['edit_user']);           
            $form -> setDefaults(array("completed" => $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['completed'],
                                       "score"     => $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['score'],
                                       "comments"  => $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['comments']));

            if (isset($currentUser -> coreAccess['progress']) && $currentUser -> coreAccess['progress'] != 'change') {
                $form -> freeze();
            } else {
                $form -> addElement('submit', 'submit_lesson_complete', _SUBMIT, 'class = "flatButton"');       //The submit button
                if ($form -> isSubmitted() && $form -> validate()) {
                    if ($form -> exportValue('completed')) {
                        $lessonUser  = EfrontUserFactory :: factory($_GET['edit_user']);
                        $lessonUser -> completeLesson($currentLesson -> lesson['id'], $form -> exportValue('score'), $form -> exportValue('comments'));
                    } else {
                        eF_updateTableData("users_to_lessons", array('completed' => 0, 'score' => 0, 'to_timestamp' => ''), "users_LOGIN = '".$_GET['edit_user']."' and lessons_ID=".$currentLesson -> lesson['id']);
                    }

                    header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=progress&message='.urlencode(_STUDENTSTATUSCHANGED).'&message_type=success');
                }
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

            $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
            $form -> setRequiredNote(_REQUIREDNOTE);
            $form -> accept($renderer);

            $smarty -> assign('T_COMPLETE_LESSON_FORM', $renderer -> toArray());
            $doneTests = EfrontStats :: getDoneTestsPerUser($_GET['edit_user']);
            $testNames = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID and c.lessons_ID=".$currentLesson -> lesson['id']);
            $testNames = array_combine($testNames['id'], $testNames['name']);

            foreach($doneTests[$_GET['edit_user']] as $key => $value) {
                if (in_array($key, array_keys($testNames))) {                
                    $lastTest = unserialize($doneTests[$_GET['edit_user']][$value['last_test_id']]);
                    $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['done_tests'][$key] = array('name' => $testNames[$key], 'score' => $value['average_score'], 'last_test_id' => $value['last_test_id'], 'last_score' => $value['scores'][$value['last_test_id']], 'times_done' => $value['times_done']);
                }
            }
            unset($userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['done_tests']['average_score']);
            //pr($userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]);
            $userTime     = EfrontStats :: getUsersTime($currentLesson -> lesson['id'], $_GET['edit_user']);
            $smarty -> assign("T_USER_LESSONS_INFO", $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]);
            $smarty -> assign("T_USER_TIME", $userTime[$_GET['edit_user']]);

        }
            
        //Get users list through ajax
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {

            $users = EfrontStats::getUsersLessonStatus($currentLesson);
            $users = $users[$currentLesson -> lesson['id']];
            foreach ($users as $key => $user) {
                if ($user['user_type'] != 'student') {
                    unset($users[$key]);
                }
            }

            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'login';
            }
            $users = eF_multiSort($users, $sort, $order);
            $smarty -> assign("T_USERS_SIZE", sizeof($users));
            if (isset($_GET['filter'])) {
                $users = eF_filterData($users, $_GET['filter']);
            }
            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $users = array_slice($users, $offset, $limit);
            }
            foreach ($users as $key => $value) {
                $users[$key]['issued_certificate'] = unserialize($value['issued_certificate']);
            }
            $smarty -> assign("T_USERS_PROGRESS", $users);
            $smarty -> display('professor.tpl');
            exit;
        }

        //$smarty -> assign("AUTO_COMPLETE", $currentLesson -> lesson['auto_complete']);
    }
     /*
    Module inclusion. If there are any modules that need to be displayed as ops in the control panel, they are included here
    */
    else if (isset($_GET['op']) && in_array($_GET['op'], array_keys($module_ctgs))) {
        $module_mandatory = eF_getTableData("modules", "mandatory", "name = '".$_GET['op']."'");
        if ($module_mandatory[0]['mandatory'] != 'false' || isset($currentLesson -> options[$_GET['op']])) {
            include(G_MODULESPATH.$_GET['op'].'/module.php');
            $smarty -> assign("T_OP_MODULE", $module_ctgs[$_GET['op']]);
        }
    }

    /*
    These are the default procedures that take place when the professor is accessing the control panel, and
    no operation is set.
    */
    else {
        $innerTableIdentifier = 'professor_cpanel';        //This is a notifier for cookies handling the show/hide status of inner tables. It affects only control panel and is considered inside printInnerTable smarty plugin
        /*Calculate element positions, so they can be rearreanged accordingly to the user selection*/
        $elementPositions = eF_getTableData("users_to_lessons", "positions", "lessons_ID=".$_SESSION['s_lessons_ID']." AND users_LOGIN='".$_SESSION['s_login']."'");
        if (sizeof($elementPositions) > 0) {
            if ($elementPositions = unserialize($elementPositions[0]['positions'])) {
                $smarty -> assign("T_POSITIONS_FIRST", $elementPositions['first']);
                $smarty -> assign("T_POSITIONS_SECOND", $elementPositions['second']);
                $smarty -> assign("T_POSITIONS", array_merge($elementPositions['first'], $elementPositions['second']));
            }
        } else {
            $smarty -> assign("T_POSITIONS", array());
        }

        if ($currentUser -> coreAccess['content'] != 'hidden') {
            $currentLesson -> options['lesson_info'] ? $lessonOptions[0]  = array('text' => _LESSONINFORMATION, 'image' => "32x32/about.png",       'href' => basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=lesson_information") : null;
            $lessonOptions[1]  = array('text' => _CONTENTMANAGEMENT, 'image' => "32x32/pencil.png",      'href' => "professor.php?ctg=content");
            if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
                $lessonOptions[5]  = array('text' => _CONTENTTREEMANAGEMENT,    'image' => "32x32/trafficlight_on.png", 'href' => "professor.php?ctg=content&op=unit_order");
                $lessonOptions[7]  = array('text' => _COPYFROMANOTHERLESSON,    'image' => "32x32/folder_into.png",     'href' => "professor.php?ctg=content&op=copy_content");
            }
            $currentLesson -> options['projects'] ? $lessonOptions[2]  = array('text' => _PROJECTS,    'image' => "32x32/exercises.png",     'href' => "professor.php?ctg=projects")  : null;
            $currentLesson -> options['tests']    ? $lessonOptions[3]  = array('text' => _TESTS,       'image' => "32x32/document_edit.png", 'href' => "professor.php?ctg=tests")     : null;
            $currentLesson -> options['rules']    ? $lessonOptions[9] = array('text' => _ACCESSRULES, 'image' => "32x32/recycle.png",       'href' => "professor.php?ctg=rules")     : null;
            $currentLesson -> options['scorm']    ? $lessonOptions[18] = array('text' => _SCORM,       'image' => "32x32/book_red.png",      'href' => "professor.php?ctg=control_panel&op=scorm") : null;
			
		}
        if ($currentUser -> coreAccess['glossary'] != 'hidden') {
            $currentLesson -> options['glossary'] ? $lessonOptions[10]  = array('text' => _GLOSSARY,    'image' => "32x32/book_open2.png",    'href' => "professor.php?ctg=glossary")  : null;
        }
        if ($currentUser -> coreAccess['statistics'] != 'hidden') {
            $lessonOptions[13]  = array('text' => _STATISTICS,        'image' => "32x32/chart.png",       'href' => "professor.php?ctg=statistics");
        }
        if ($currentUser -> coreAccess['settings'] != 'hidden') {
            $lessonOptions[12] = array('text' => _SCHEDULING,        'image' => "32x32/calendar.png",    'href' => "professor.php?ctg=scheduling");
        }
        if ($currentUser -> coreAccess['files'] != 'hidden') {
            $lessonOptions[17] = array('text' => _FILES,       'image' => "32x32/folder_view.png", 'href' => "professor.php?ctg=content&op=file_manager");
        }
        if ($currentUser -> coreAccess['settings'] != 'hidden') {
            $lessonOptions[19] = array('text' => _LESSONSETTINGS,    'image' => "32x32/gear.png",        'href' => "professor.php?ctg=settings");
        }
        if ($currentUser -> coreAccess['surveys'] != 'hidden') {
            $currentLesson -> options['survey'] ? $lessonOptions[8] = array('text' => _SURVEYS, 'image' => "32x32/form_green.png", 'href' => "professor.php?ctg=survey") : null;
        }
        if ($currentUser -> coreAccess['progress'] != 'hidden') {
            $lessonOptions[11] = array('text' => _USERSPROGRESS, 'image' => "32x32/stethoscope.png", 'href' => "professor.php?ctg=control_panel&op=progress");
        }
		if ($currentUser -> coreAccess['forum'] != 'hidden') {
			$resultForum = eF_getTableData("f_forums","id","lessons_ID=".$_SESSION['s_lessons_ID']);
			$currentLesson -> options['forum']    ? $lessonOptions[20] = array('text' => _FORUM,       'image' => "32x32/messages.png",      'href' => "forum/forum_index.php?forum=".$resultForum[0]['id']) : null;
		}

///MODULE2: Create lesson control panel sidelinks and innertable
		$innertable_modules = array();
        foreach ($loadedModules as $module) {
            // Check if the module is enabled
            if ($currentLesson -> options[$module -> className] == 1) {

                if ($centerLinkInfo = $module -> getLessonCenterLinkInfo()) {
                    $lessonOptions[] = array('text' => $centerLinkInfo['title'],  'image' => eF_getRelativeModuleImagePath($centerLinkInfo['image']),        'href' => $centerLinkInfo['link']);
                }

				unset($lessonInnertableHTML);
                $lessonInnertableHTML = $module -> getLessonModule();   //**HERE**
                // If the module has a lesson innertable
                if ($lessonInnertableHTML) {

                    // Get module html - two ways: pure HTML or PHP+smarty
                    // If no smarty file is defined then false will be returned
                    if ($module_smarty_file = $module -> getLessonSmartyTpl()) {
                        // Execute the php code -> The code has already been executed by above (**HERE**)
                        // Let smarty know to include the module smarty file
                        $innertable_modules[$module->className] = array('smarty_file' => $module_smarty_file);
                    } else {
                        // Present the pure HTML cod
                        $innertable_modules[$module->className] = array('html_code' => $lessonInnertableHTML);
                    }
                }
            }
        }

        ksort($lessonOptions);
        if (!empty($innertable_modules)) {
            $smarty -> assign("T_INNERTABLE_MODULES", $innertable_modules);
        }
        $smarty -> assign("T_LESSON_OPTIONS", $lessonOptions);

        /*Lesson announcements list*/
        $announcements = eF_getNews();
        $announcements_options[] = array('text' => _ANNOUNCEMENTGO,  'image' => "16x16/redo.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=news");
        if (!isset($currentUser -> coreAccess['news']) || $currentUser -> coreAccess['news'] == 'change') {
            $announcements_options[] = array('text' => _ANNOUNCEMENTADD, 'image' => "16x16/add2.png", 'href' => "news.php?op=insert", 'onClick' => "eF_js_showDivPopup('"._ANNOUNCEMENTADD."', 1)", 'target' => 'POPUP_FRAME');
        }
        $smarty -> assign("T_NEWS", $announcements);
        $smarty -> assign("T_NEWS_OPTIONS", $announcements_options);
        $smarty -> assign("T_NEWS_LINK", "professor.php?ctg=control_panel&op=news");

        /*Forum messages list*/
        if (!isset($currentUser -> coreAccess['forum']) || $currentUser -> coreAccess['forum'] != 'hidden') {
            $forum_messages   = eF_getForumMessages($_SESSION['s_lessons_ID'], $f_messages_limit);
            $forum_lessons_ID = eF_getTableData("f_forums", "id", "lessons_ID=".$_SESSION['s_lessons_ID']);
            $smarty -> assign("T_FORUM_MESSAGES", $forum_messages);
            $smarty -> assign("T_FORUM_LESSONS_ID", $forum_lessons_ID[0]['id']);

            if ($forum_lessons_ID[0]['id']) {
                $forum_options[] = array('text' => _GOTOFORUM, 'image' => "16x16/redo.png", 'href' => "forum/forum_index.php");
                if (!isset($currentUser -> coreAccess['forum']) || $currentUser -> coreAccess['forum'] == 'change') {
                    $forum_options[] = array('text' => _SENDMESSAGEATFORUM, 'image' => "16x16/add2.png", 'href' => "forum/forum_add.php?add_topic=1&forum_id=".$forum_lessons_ID[0]['id'], 'onClick' => "eF_js_showDivPopup('"._NEWMESSAGE."', new Array('650px', '450px'))", 'target' => 'POPUP_FRAME');
                }
            } else {
                $forum_options = array(
                array('text' => _GOTOFORUM, 'image' => "16x16/redo.png", 'href' => "forum/forum_index.php  ")
                );
            }
            $smarty -> assign("T_FORUM_OPTIONS", $forum_options);
            $smarty -> assign("T_FORUM_LINK", "forum/forum_index.php?category=".$forum_lessons_ID[0]['id']);
        }
        /*Personal messages list*/
        if (!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') {
            $personal_messages = eF_getPersonalMessages(false, 10);
            $smarty -> assign("T_PERSONAL_MESSAGES", $personal_messages);

            $personal_message_options = array(
            array('text' => _GOTOINBOX,   'image' => "16x16/redo.png", 'href' => "forum/messages_index.php"),
            array('text' => _SENDMESSAGE, 'image' => "16x16/add2.png", 'href' => "forum/new_message.php", 'onClick' => "eF_js_showDivPopup('"._NEWMESSAGE."', new Array('650px', '450px'))", 'target' => 'POPUP_FRAME')
            );
            $smarty -> assign("T_PERSONAL_MESSAGES_OPTIONS", $personal_message_options);
            $smarty -> assign("T_PERSONAL_MESSAGES_LINK",    "forum/messages_index.php");
        }
        if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] != 'hidden') {
        	/*Comments list*/
            $comments = eF_getComments(false, false, false, 10);
            $smarty -> assign("T_COMMENTS", $comments);

            /*Completed tests list*/
            $testIds = $currentLesson -> getTests(false, true);
            if (sizeof($testIds) > 0) {
                $result = eF_getTableData("completed_tests ct, tests t", "ct.id, ct.users_LOGIN, ct.timestamp, ct.status, t.name", "ct.archive = 0 and ct.tests_ID = t.id and ct.tests_ID in (".implode(",", $testIds).")", "", "ct.timestamp DESC");
                $smarty -> assign("T_COMPLETED_TESTS", $result);
            }
        }
        
        
        $today = getdate(time());                                                                     //Get current time in an array
        $today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);                      //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today
        (eF_checkParameter($_GET['view_calendar'], 'timestamp')) ? $view_calendar = $_GET['view_calendar']: $view_calendar = $today;    //If a specific calendar date is not defined in the GET, set as the current day to be today

        $smarty -> assign("T_CALENDAR_TYPE", "&type=2");
        $result = eF_getTableData("calendar","*","");
        foreach ($result as $event) {
            if (!isset($events[$event['timestamp']])) {
                $events[$event['timestamp']]['data'] = array();
                $events[$event['timestamp']]['id'] = array();
            }
            $events[$event['timestamp']]['data'][] = $event['data'];
            $events[$event['timestamp']]['id'][]   = $event['id'];
        }
        
        $smarty -> assign("T_CALENDAR_EVENTS", $events);                                                    //Assign events and specific day timestamp to smarty, to be used from calendar
        $smarty -> assign("T_VIEW_CALENDAR", $view_calendar);

        if (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['content'] == 'change') {
            $calendar_options = array(                                                                          //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
                    array('text' => _GOTOCALENDAR, 'image' => "16x16/redo.png", 'href' => "professor.php?ctg=calendar"),
                    array('text' => _ADDCALENDAR,  'image' => "16x16/add2.png", 'href' => "professor.php?ctg=calendar&add_calendar=1&view_calendar=".$view_calendar.$type_of_events, "onClick" => "eF_js_showDivPopup('"._ADDCALENDAR."', 2)", "target" => "POPUP_FRAME", "id" => "add_new_event_link"));
        } else {
            $calendar_options = array(                                                                          //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
                    array('text' => _GOTOCALENDAR, 'image' => "16x16/redo.png", 'href' => "professor.php?ctg=calendar"));
        }

        $smarty -> assign("T_CALENDAR_OPTIONS", $calendar_options);
        $smarty -> assign("T_CALENDAR_LINK", "professor.php?ctg=calendar");
        if (isset($_GET['add_another'])) {
            $smarty -> assign('T_ADD_ANOTHER', "1");
        }
    }
}
/*
Content is the page where the professor views manages the lesson content. Here, he may, add and edit units,
as well as perform content-specific operation, such as:
- Change the units order
- Upload files
- Copy content from one unit to another
- Activate, deactivate and delete units
*/
elseif ($ctg == 'content') {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    try {
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST));
        foreach ($iterator as $key => $value) {
            $contentUnits[] = $key;
        }
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }

    /*
    The system file manager
    */
    if (isset($_GET['op']) && $_GET['op'] == 'file_manager') {
        if (isset($currentUser -> coreAccess['files']) && $currentUser -> coreAccess['files'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (isset($_GET['display_metadata']) && (eF_checkParameter($_GET['display_metadata'], 'id') || strpos($_GET['display_metadata'], $currentLesson -> getDirectory()) !== false)) {
            try {
                $form         = new HTML_QuickForm("empty_form", "post", null, null, null, true);
                $file         = new EfrontFile(urldecode($_GET['display_metadata']));
                if ($file['id'] == -1) {
                    $imported = FileSystemTree :: importFiles($file['path']);
                    $file     = new EfrontFile(key($imported));
                }
                $fileMetadata = unserialize($file['metadata']);
                $metadata     = new DublinCoreMetadata($fileMetadata);
                $smarty -> assign("T_FILE_METADATA", $file);
                if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
                    $smarty -> assign("T_FILE_METADATA_HTML", $metadata -> toHTML($form));
                } else {
                    $smarty -> assign("T_FILE_METADATA_HTML", $metadata -> toHTML($form, true, false));
                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
            if (isset($_GET['postAjaxRequest'])) {
                if (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
                    if ($_GET['value']) {
                        $fileMetadata[$_GET['dc']] = $_GET['value'];
                    } else {
                        unset($fileMetadata[$_GET['dc']]);
                    }
                    $file['metadata'] = serialize($fileMetadata);
                    $file -> persist();
                }
                echo $_GET['value'];
                exit;
            }
        } else {
            $loadScripts[] = 'drag-drop-folder-tree';
            $loadScripts[] = 'scriptaculous/effects';
            try {
                $basedir    = $currentLesson -> getDirectory();
                $filesystem = new FileSystemTree($basedir);
                $filesystem -> handleAjaxActions($currentUser);

                if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
                    $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 1);
                } else {
                    $options = array('delete' => false, 'edit' => false, 'share' => false, 'upload' => false, 'create_folder' => false, 'zip' => false, 'lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 1);
                }
                $url = basename($_SERVER['PHP_SELF']).'?ctg=content&op=file_manager';

                if (isset($_GET['ajax'])) {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }

                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    }
                    isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                    isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                    $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                    echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, '', '', '', '', false);
                    exit;
                }
                $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, '', '', '', '', false));
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }
    }
    /*
    This section is used to change the order of the units, as well as delete and (de)activate units
    */
    elseif (isset($_GET['op']) && $_GET['op'] == 'unit_order') {
        $loadScripts[] = 'drag-drop-folder-tree';
        $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
        $loadScripts[] = 'scriptaculous/effects';

        //$loadScripts[] = 'context-menu';
        if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
            try {
                $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST));
                $smarty -> assign("T_UNIT_ORDER_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree', array('delete' => true, 'noclick' => true, 'activate' => true, 'drag' => true, 'expand' => true)));
                $options = array(array('image' => '16x16/undo.png', 'text' => _REPAIRTREE, 'href' => 'javascript:void(0)', 'onClick' => 'if (confirm (\''._ORDERWILLPERMANENTLYCHANGE.'\')) repairTree();'));
                $smarty -> assign("T_TABLE_OPTIONS", $options);
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = _ERRORLOADINGCONTENT." ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
            if (isset($_GET['ajax'])) {
                if (isset($_GET['delete_nodes']) && $_GET['delete_nodes']) {      //Delete nodes through AJAX call
                    $deleteNodes = explode(",", $_GET['delete_nodes']);
                    $deleteNodes = array_reverse($deleteNodes);					//Added at 2008/11/7 in order to delete a subtree
					foreach ($deleteNodes as $value) {
                        if (eF_checkParameter($value, 'id')) {
                            try {                                                //Putting the try/catch block here, makes the process to continue even if it fails for some units
                                $currentContent -> removeNode($value);
                            } catch (Exception $e) {
                                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                                $message      .= _ERRORDELETINGUNIT." ".$value.": ".$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a><br/>';
                                $message_type = 'failure';
                            }
                        }
                    }
                }
                if (isset($_GET['activate_nodes']) && $_GET['activate_nodes']) {      //Delete nodes through AJAX call
                    $activateNodes = explode(",", $_GET['activate_nodes']);
                    foreach ($activateNodes as $value) {
                        if (eF_checkParameter($value, 'id') && in_array($value, $contentUnits)) {
                            try {                                                //Putting the try/catch block here, makes the process to continue even if it fails for some units
                                $currentContent -> seekNode($value) -> activate();
                            } catch (Exception $e) {
                                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                                $message      .= _ERRORACTIVATINGUNIT." ".$value.": ".$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a><br/>';
                                $message_type = 'failure';
                            }
                            //echo "activating node $value";
                        }
                    }
                }
                if (isset($_GET['deactivate_nodes']) && $_GET['deactivate_nodes']) {      //Delete nodes through AJAX call
                    $deactivateNodes = explode(",", $_GET['deactivate_nodes']);
                    foreach ($deactivateNodes as $value) {
                        if (eF_checkParameter($value, 'id') && in_array($value, $contentUnits)) {
                            try {                                                //Putting the try/catch block here, makes the process to continue even if it fails for some units
                                $currentContent -> seekNode($value) -> deactivate();
                            } catch (Exception $e) {
                                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                                $message      .= _ERRORDEACTIVATINGUNIT." ".$value.": ".$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a><br/>';
                                $message_type = 'failure';
                            }
                            //echo "deactivating node $value";
                        }
                    }
                }
                if (isset($_GET['node_orders']) && $_GET['node_orders']) {
                    $nodeOrders        = explode(",", $_GET['node_orders']);
                    $previousContentId = 0;
                    foreach ($nodeOrders as $value) {
                        list($id, $parentContentId) = explode("-", $value);
                        $contentUnits[] = 0;                                        //Add 0 to possible content units, since both parent and previous units may be 0
                        if (eF_checkParameter($id, 'id') !== false && in_array($id, $contentUnits) && eF_checkParameter($parentContentId, 'id') !== false  && in_array($parentContentId, $contentUnits)) {
                            try {                                                //Putting the try/catch block here, makes the process to continue even if it fails for some units
                                $unit = $currentContent -> seekNode($id);
                                $unit -> offsetSet('previous_content_ID', $previousContentId);
                                $unit -> offsetSet('parent_content_ID', $parentContentId);
                                $unit -> persist();
                                $previousContentId = $id;
                            } catch (Exception $e) {
                                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                                $message      .= _ERRORDEPOSITIONINGUNIT." ".$id.": ".$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a><br/>';
                                $message_type = 'failure';
                            }
                        }
                    }
                }
                if (isset($_GET['repair_tree'])) {
                    try {
                        $currentContent -> repairTree();
                    } catch (Exception $e) {
                        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                        $message      .= _ERRORREPAIRINGTREE.": ".$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a><br/>';
                        $message_type = 'failure';
                    }
                }
                echo _TREESAVEDSUCCESSFULLY;
                exit;
            }
        } else {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
    }

    /*
    This section is used to copy units from one lesson to another
    */
    elseif (isset($_GET['op']) && $_GET['op'] == 'copy_content') {
        $loadScripts[] = 'drag-drop-folder-tree';
        $loadScripts[] = 'scriptaculous/scriptaculous';
        $loadScripts[] = 'scriptaculous/effects';
        if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
            $userLessons = $currentUser -> getLessons();
            if (isset($_GET['lesson']) && eF_checkParameter($_GET['lesson'], 'id') && isset($userLessons[$_GET['lesson']])) {    //@todo: Must be professor also, so we need a way to get the basic user type for each lesson, apart from role
                $iterator       = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST));
                if (sizeof($currentContent -> tree) == 0) {
                    $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator,       'dhtmlTargetTree', array('noclick' => true, 'drag' => false, 'tree_root' => true)));
                } else {
                    $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator,       'dhtmlTargetTree', array('noclick' => true, 'drag' => false, 'expand' => true)));
                }
                $sourceContent  = new EfrontContentTree($_GET['lesson']);
                $sourceIterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($sourceContent  -> tree), RecursiveIteratorIterator :: SELF_FIRST));
                $smarty -> assign("T_SOURCE_TREE",  $sourceContent  -> toHTML($sourceIterator, 'dhtmlSourceTree',  array('noclick' => true, 'drag' => true, 'expand' => true)));

                $currentIds[] = 0;                                    //0 is a valid parent node
                foreach ($iterator as $key => $value) {
                    $currentIds[] = $value['id'];
                }
                foreach ($sourceIterator as $key => $value) {
                    $sourceIds[] = $value['id'];
                }

                if (isset($_GET['node_orders'])) {                                    //Save new order through AJAX call
                    $nodeOrders        	= explode(",", $_GET['node_orders']);
                    $previousContentId 	= 0;
					$transferedNodes = array();
					$transferedNodesCheck = array();
					if ($_GET['transfered'] != "") {
						$transferedNodesCheck =  unserialize($_GET['transfered']);
					}
                    foreach ($nodeOrders as $value) {
                        list($id, $parentContentId) = explode("-", $value);
						if(!in_array($id, $transferedNodesCheck)) {
						  
							if (eF_checkParameter($id, 'id') !== false && eF_checkParameter($parentContentId, 'id') !== false && in_array($id, $sourceIds) && in_array($parentContentId, $currentIds)) {
								//echo "Copying $id to parent $parentContentId with previous $previousContentId\n";
								try {
									$createdUnit 		= $currentContent -> copyUnit($id, $parentContentId);
									$transferedNodes[] 	= intval($id);

								//pr($createdUnit->id);
								} catch (Exception $e) {
									//$smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
									$errorMessages[] = $e -> getMessage().' '.$e -> getCode();
								}
							}
							$previousContentId = $id;
						}
					}
                    if ($errorMessages) {
                        header("HTTP/1.0 500 ");
                        echo _ERRORSAVINGTREE."\n".implode("\n", $errorMessages);
                    } else {
                        //echo _TREESAVEDSUCCESSFULLY;
						echo serialize($transferedNodes);
                    }
                    exit;
                }
            }
            $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
            $lessons = $currentUser -> getLessons(true);
            $direction_lessons = array();
            foreach ($lessons as $lesson){
                $direction = $lesson -> getDirection();
                $direction_lessons[$direction['name']][] = array('id' => $lesson -> lesson['id'], 'name' => $lesson -> lesson['name']);
            }

            $smarty -> assign("T_USER_LESSONS", $direction_lessons);
        } else {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
    } elseif (isset($_GET['op']) && $_GET['op'] == 'metadata' && $currentUnit) {
        //If the metadata field is uninitialized, then initialize it with default values
        if (!$currentUnit['metadata']) {
            $defaultMetadata = array('title'       => $currentUnit['name'],
                                     'date'        => date("Y/m/d", $currentUnit['timestamp']));
            $currentUnit['metadata'] = serialize($defaultMetadata);
            $currentUnit -> persist();
        }
        $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
        $loadScripts[] = 'scriptaculous/effects';
        $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);
        try {
            $contentMetadata = unserialize($currentUnit['metadata']);
            $metadata        = new DublinCoreMetadata($contentMetadata);
            $smarty -> assign("T_CONTENT_METADATA_HTML", $metadata -> toHTML($form));
            $smarty -> assign("T_CURRENT_UNIT", $currentUnit);
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = "failure";
        }

        if (isset($_GET['postAjaxRequest'])) {
            if (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
                if ($_GET['value']) {
                    $contentMetadata[$_GET['dc']] = ($_GET['value']);
                } else {
                    unset($contentMetadata[$_GET['dc']]);
                }
                $currentUnit['metadata'] = serialize($contentMetadata);
            }
            try {
                $currentUnit -> persist();
                echo $_GET['value'];
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    }

    /*
     * Here we either view or edit a unit
     */
    else {
        try {
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1));
            !isset($currentUnit) ? $currentUnit = $currentContent -> getFirstNode($iterator) : null;                                               //If a unit is not specified, then consider the first content unit by default
            if ($currentUnit['ctg_type'] == 'tests' && !isset($_GET['add_unit']) && !isset($_GET['edit_unit'])) {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=tests&view_unit=".$currentUnit['id']);                                                //If the first unit is a test, display accordingly
            }
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                $smarty  -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree', array('truncateNames' => 20, 'edit' => false, 'selectedNode' => $currentUnit['id'])));
            } else {
                $smarty  -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree', array('truncateNames' => 20, 'edit' => true, 'selectedNode' => $currentUnit['id'])));
            }
            
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }

        /*The professor asked to add or update a unit*/
        if (isset($_GET['add_unit']) || (isset($_GET['edit_unit']) && eF_checkParameter($_GET['edit_unit'], 'id'))) {
        	
			if (isset($_GET['postAjaxRequest'])) {
				$file_id 		= urldecode($_GET['file_id']);
                $file_insert 	= new EfrontFile($file_id);
				if (strpos($file_insert['mime_type'] , "image") !== false) {
					$img_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
					echo "<img src=\"".$img_return."\" border=0 />";
					exit;
				} elseif (strpos($file_insert['mime_type'] , "pdf") !== false) {
					$pdf_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
					echo '<iframe src="'.$pdf_return.'"  name="pdfFrame_'.urlencode($file_insert['id']).'" width="100%" height="600"></iframe>';
					exit;
				} elseif (strpos($file_insert['mime_type'] , "flash") !== false) {
					$flash_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
					if ($_GET['editor_mode'] == "true") {
						echo '<img width="400" height="400" src="editor/tiny_mce/themes/advanced/images/spacer.gif"  title="'.$flash_return.'" alt="'.$flash_return.'" class="mceItemFlash" />';
						exit;
					} else {
						echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="400" height="400">
						<param name="src" value="'.$flash_return.'" />
						<param name="width" value="400" />
						<param name="height" value="400" />
						<embed type="application/x-shockwave-flash" src="'.$flash_return.'" width="400" height="400"></embed>
						</object>';
						exit;
					}
				} else {
					echo "<a href=view_file.php?action=download&file=".$file_id.">".$file_insert['physical_name']."</a>";
					exit;	
				
				}
            }
        
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }
            $load_editor = true;
            try {
                $optionsArray = $currentContent -> toHTMLSelectOptions();    //Get the units as an array of formated strings, that can be used to form an HTML select list

    //            $lesson_periods = eF_getTableData("periods", "*", "lessons_ID=".$_SESSION['s_lessons_ID']); //Get the lesson periods
    //            foreach ($lesson_periods as $lesson_period) {
    //                $periods[$lesson_period['id']] = $lesson_period['name'];                                //Get period and lesson names in 1 array, to use them in the form select box
    //            }

                if (isset($_GET['add_unit'])) {
                    $post_target = 'add_unit=1';
                } else {
                    $post_target = 'edit_unit='.$_GET['edit_unit'];
                    $currentUnit = $currentContent -> seekNode($_GET['edit_unit']);
                }
                if ($currentUnit) {
                    $smarty -> assign("T_PARENT_LIST", $currentContent -> getNodeAncestors($currentUnit));
                }

                $form = new HTML_QuickForm("update_content_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=content&'.$post_target, "", null, true);
                $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

                $form -> addElement('text', 'name', _UNITNAME, 'class = "inputText"');                      //The Unit name
                $form -> addRule('name', _THEFIELD.' "'._UNITNAME.'" '._ISMANDATORY, 'required', null, 'client');           //The name is mandatory
                $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');                      //The unit name must be 'text'

                if (isset($_GET['add_unit']) || ($currentUnit['ctg_type'] != 'scorm' && $currentUnit['ctg_type'] != 'scorm_test')) {                                          //SCORM units should not be edited neither its content nor its type
                    $form -> addElement('select', 'ctg_type', _CONTENTTYPE, array('theory' => _THEORY, 'examples'=> _EXAMPLES), 'class = "inputSelect"');     //A select drop down for content type.... Exercises went away in version 3 (2007/07/10) makriria
                    $form -> addRule('ctg_type', _THEFIELD.' '._CONTENTTYPE.' '._ISMANDATORY, 'required', null, 'client');       //The content type is mandatry
                    $form -> addRule('ctg_type', _INVALIDFIELDDATA, 'checkParameter', 'string');                //The content type can only be string

                    $form -> addElement('textarea', 'data', _CONTENT, 'id="editor_content_data" class = "inputContentTextarea mceEditor" style = "width:100%;height:50em;"');  //The unit content itself
                }
                
                $form -> addElement('advcheckbox', 'hide_complete_unit', _HIDECOMPLETEUNITICON, null, 'class = "inputCheckbox"', array(0, 1));
                $form -> addElement('advcheckbox', 'hide_navigation', _HIDENAVIGATION, null, 'class = "inputCheckbox"', array(0, 1));
                $form -> addElement('advcheckbox', 'indexed', _DIRECTLYACCESSIBLE, null, 'class = "inputCheckbox"', array(0, 1));

                $pathStrings = $currentContent -> toPathStrings();                
                foreach ($currentLesson -> getQuestions() as $key => $value) {
                    $plainText = trim(strip_tags($value['text']));
                    if (mb_strlen($plainText) > Question :: maxQuestionText) {
                        $plainText = mb_substr($plainText, 0, Question :: maxQuestionText).'...';
                    }
                    $pathStrings[$value['content_ID']]? $lessonQuestions[$value['id']] = $pathStrings[$value['content_ID']].'&nbsp;&raquo;&nbsp;'.$plainText : $lessonQuestions[$value['id']] = $plainText;
                }
                if ($lessonQuestions) {
                    $form -> addElement('advcheckbox', 'complete_question', _COMPLETEWITHQUESTION, null, 'class = "inputCheckbox" onclick = "$(\'complete_questions\').toggle()"', array(0, 1));
                    $form -> addElement('select', 'questions', _SELECTQUESTION, $lessonQuestions, 'id = "complete_questions" style = "display:none"');
                }
				$form -> addElement('advcheckbox', 'pdf_check', _UPLOADPDFFORCONTENT, null, 'class = "inputCheckbox" onclick="$(\'pdf_upload\').toggle();$(\'pdf_content\').toggle();$(\'nonPdfTable\').toggle();"', array(0, 1));
				$form -> addElement('text', 'pdf_content', _CURRENTPDFFILE, 'class = "inputTextInactive" readonly');
                $form -> addElement('file', 'pdf_upload', _PDFFILE, null);
                

                $form -> addElement('submit', 'submit_insert_content', _SAVECHANGES, 'class = "flatButton"');       //The submit content button

                if (isset($_GET['edit_unit'])) {
                    //pr($currentUnit['options']);
                    $form -> setDefaults(array('name'     => $currentUnit['name']));
                    $form -> setDefaults(array('ctg_type' => $currentUnit['ctg_type']));
                    $form -> setDefaults($currentUnit['options']);
                    $form -> setDefaults(array('complete_question' => $currentUnit['options']['complete_question'] ? 1 : 0,
                                               'questions'         => $currentUnit['options']['complete_question']));
                    
                    $currentUnit['options']['complete_question'] ? $form -> updateElementAttr(array('questions'), array('style' => 'display:""')) : null;
                    if ($currentUnit['ctg_type'] != 'scorm' && $currentUnit['ctg_type'] != 'scorm_test') {
						$form -> setDefaults(array('data' => $currentUnit['data']));
                        if (strpos($currentUnit['data'], "<iframe") !== false && strpos($currentUnit['data'], "pdfaccept") !== false) {
                        	$fileEnd 		= strpos($currentUnit['data'], ".pdf");
                        	$contentParts 	= explode("/", mb_substr($currentUnit['data'], 0, $fileEnd));
                        	$form -> setDefaults(array('pdf_content'   => $contentParts[sizeof($contentParts)-1].'.pdf'));
                        	$form -> setDefaults(array('pdf_check'     => 1));
                        	$smarty -> assign("T_EDITPDFCONTENT", true);
						}
                    } else {
                        $form -> addElement('text', 'scorm_size', _EXPLICITIFRAMESIZE, 'class = "inputText" style = "width:50px"');                      //Set an explicit size for the SCORM content
                        $form -> addRule('scorm_size', _INVALIDFIELDDATA, 'checkParameter', 'id');

                        preg_match("/eF_js_setCorrectIframeSize\((.*)\)/", $currentUnit['data'], $matches);
                        $form -> setDefaults(array('scorm_size' => $matches[1]));

                        $smarty -> assign("T_SCORM", true);
                    }
                } else {
                    $select_units = & HTML_QuickForm :: createElement('select', 'parent_content_ID', _UNITPARENT, null, 'class = "inputSelect"');
                    $select_units -> addOption(_UNITPARENT, 0);
                    $select_units -> addOption('-------------', 0);
                    $select_units -> loadArray($optionsArray);

                    $form -> addElement($select_units);
                    $form -> addRule('parent_content_ID', _THEFIELD.' '._UNITPARENT.' '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('parent_content_ID', _INVALIDID, 'numeric');
                    if (isset($_GET['view_unit'])) {
                        $form -> setDefaults(array('parent_content_ID' => $_GET['view_unit']));                                //Set the current content to be the selected in the list
                    }
                    $form -> setDefaults(array('hide_complete_unit' => 0, 'hide_navigation' => 0));

                }

                if ($form -> isSubmitted() && $form -> validate()) {
                    $values = eF_addSlashes($form -> exportValues(), false); 
                    if ($_FILES['pdf_upload']['name'] != "") {
						if (strpos($_FILES['pdf_upload']['name'], ".pdf") !== false) {
				    		$destinationDir = new EfrontDirectory(G_LESSONSPATH.$_SESSION['s_lessons_ID']);                    
      						$filesystem 	= new FileSystemTree(G_LESSONSPATH.$_SESSION['s_lessons_ID']);							
      						try {
        						$uploadedFile 	= $filesystem -> uploadFile('pdf_upload', $destinationDir);
        						$values['data'] = '<iframe src="'.G_RELATIVELESSONSLINK.$_SESSION['s_lessons_ID'].'/'.$uploadedFile["physical_name"].'"  name="pdfaccept" width="100%" height="600"></iframe>';
      						} catch (EfrontFileException $e) {
      				  			echo $e -> getMessage();
      						}
      					} else {
							$message = _YOUMUSTUPLOADAPDFFILE;
                        	header("location:".basename($_SERVER['PHP_SELF']).'?ctg=content&'.$post_target."&message=".urlencode($message)."&message_type=failure");exit;
						}
					}
                    if (isset($_GET['add_unit'])) {
                        $newUnit = array('name'              => $values['name'],
                                         'data'              => $values['data'],
                                         'parent_content_ID' => $values['parent_content_ID'],
                                         'lessons_ID'        => $_SESSION['s_lessons_ID'],
                                         'ctg_type'          => $values['ctg_type'],
                                         'active'            => 1,
                                         'options'           => serialize(array('hide_complete_unit' => $values['hide_complete_unit'],
                                                                                'hide_navigation'    => $values['hide_navigation'],
                                                                                'indexed'            => $values['indexed'],
                                                                                'complete_question'  => $values['complete_question'] ? $values['questions'] : 0)));

                        $currentUnit = $currentContent -> insertNode($newUnit);
                        $message     = _SUCCESFULLYINSERTEDNEWUNIT;
                        /*
                         if ($form -> elementExists('period') && $form -> exportValue('period')) {                                            //If the user specified a period, insert this information also.
                         eF_insertTableData("current_content", array("content_ID" => $new_content_ID, "periods_ID" => $form -> exportValue('period')));
                         }
                         */
                    } else {
                        if ($currentUnit['ctg_type'] != 'scorm' && $currentUnit['ctg_type'] != 'scorm_test') {
                            $currentUnit['data'] = $values['data'];
                        } else {
                            $currentUnit['data'] = preg_replace("/eF_js_setCorrectIframeSize\(.*\)/", "eF_js_setCorrectIframeSize(".$values['scorm_size'].")", $currentUnit['data']);
                        }
                        $values['ctg_type'] ? $currentUnit['ctg_type'] = $values['ctg_type'] : null;
                        $values['name']     ? $currentUnit['name']     = $values['name']     : null;
                        $currentUnit['options'] = array('hide_complete_unit' => $values['hide_complete_unit'],
                                                        'hide_navigation'    => $values['hide_navigation'],
                                                        'indexed'            => $values['indexed'],
                                                        'complete_question'  => $values['complete_question'] ? $values['questions'] : 0);

						$currentUnit -> persist();
                        $message = _SUCCESFULLYUPDATEDUNIT;
                        /*
                         if ($form -> elementExists('period') && $form -> exportValue('period')) {                                            //If the user specified a period, insert this information also.
                         eF_insertTableData("current_content", array("content_ID" => $new_content_ID, "periods_ID" => $form -> exportValue('period')));
                         }
                         */
                    }
                    header('location:professor.php?ctg=content&view_unit='.$currentUnit['id'].'&message='.urlencode($message).'&message_type=success');
                }

                $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                $renderer -> setRequiredTemplate (
                   '{$html}{if $required}
                        &nbsp;<span class = "formRequired">*</span>
                    {/if}');
                $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
                $form -> setRequiredNote(_REQUIREDNOTE);
                $form -> accept($renderer);

                $smarty -> assign('T_INSERT_CONTENT_FORM', $renderer -> toArray());

            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }

			
            $loadScripts[] = 'scriptaculous/effects';
            try {
                $basedir    = $currentLesson -> getDirectory();
                $filesystem = new FileSystemTree($basedir);
                $filesystem -> handleAjaxActions($currentUser);

                if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
                    $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
                } else {
                    $options = array('delete' => false, 'edit' => false, 'share' => false, 'upload' => false, 'create_folder' => false, 'zip' => false, 'lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
                }

                if (isset($_GET['edit_unit'])) {
                	$url = basename($_SERVER['PHP_SELF']).'?ctg=content&edit_unit='.$_GET['edit_unit'];
                }else{
                	$url = basename($_SERVER['PHP_SELF']).'?ctg=content&add_unit=1';
				}
				
                if (isset($_GET['ajax'])) {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }

                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    }
                    isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                    isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                    $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                    $extraFileTools = array(array('image' => 'images/16x16/arrow_right_green.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
                    echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, '', '', '', false);
                    exit;
                }
                $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, '', '', '', false));
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }


        /*The professor asked to view a unit*/
        } else {
            $loadScripts[] = 'drag-drop-folder-tree';           
			if ($configuration['math_content']) {
				$loadScripts[] = 'ASCIIMathML';
			}
			$loadScripts[] = 'scriptaculous/scriptaculous';
            $loadScripts[] = 'scriptaculous/sidebar_extra';

            if ($currentUnit) {
                try {
                    if ($currentLesson -> options['glossary']) {
                        $currentUnit['data'] = eF_applyGlossary($currentUnit['data']);        //If glossary is activated, transform content data accordingly
                    }
                    if ($currentUnit['ctg_type'] == 'scorm' || $currentUnit['ctg_type'] == 'scorm_test') {
                        $smarty -> assign("T_SCORM", true);
                    }
                    $currentUnit['data'] = str_replace("##EFRONTINNERLINK##", $_SESSION['s_type'], $currentUnit['data']);    //Replace inner links. Inner links are created when linking from one unit to another, so they must point either to professor.php or student.php, depending on the user viewing the content

                    $visitableIterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)); //Unlike student, the professor may visit empty units
                    $smarty -> assign("T_UNIT",          $currentUnit);
                    $smarty -> assign("T_NEXT_UNIT",     $currentContent -> getNextNode($currentUnit, $visitableIterator));
                    $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator));        //Next and previous units are needed for navigation buttons
                    $smarty -> assign("T_PARENT_LIST",   $currentContent -> getNodeAncestors($currentUnit));       //Parents are needed for printing the title
                    $smarty -> assign("T_COMMENTS",      eF_getComments($_SESSION['s_lessons_ID'], false, $currentUnit['id']));        //Retrieve any comments regarding this unit
                    $smarty -> assign("T_SHOW_TOOLS",    true);                                                    //Tools is the right upper corner table box, that lists tools such as 'upload files', 'copy content' etc
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }

        }
    }

}
/*
Scheduling pages is used to add periods to the lesson. The professor may add, change
or delete periods, as well as assign units to them. Furthermore, he may change the lesson
start date, thus shifting all periods.
*/
elseif ($ctg == 'scheduling') {
    if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    if (isset($_GET['delete_schedule']) && $_GET['delete_schedule']) {
        if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        $currentLesson -> lesson['from_timestamp'] = '';
        $currentLesson -> lesson['to_timestamp']   = '';
        $currentLesson -> lesson['shift']          = 0;

        $currentLesson -> persist();
        exit;
    }
    $form = new HTML_QuickForm("add_period_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=scheduling&", "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');

    $form -> addElement('text', 'from', _FROM, 'class = "inputText"');
    $form -> addElement('text', 'to', _TO, 'class = "inputText"');
    //$form -> addElement('advcheckbox', 'shift', _SHIFTSCHEDULEFORNEWUSERS, null, 'class = "inputCheckbox"', array(0, 1));

    if ($currentLesson -> lesson['from_timestamp']) {
        $smarty -> assign("T_FROM_TIMESTAMP", $currentLesson -> lesson['from_timestamp']);
        $smarty -> assign("T_TO_TIMESTAMP",   $currentLesson -> lesson['to_timestamp']);
    } else {
        $smarty -> assign("T_FROM_TIMESTAMP", time());
        $smarty -> assign("T_TO_TIMESTAMP",   mktime(date("H"), date("i"), date("s"), date("m")+1, date("d"), date("Y")));    //One month after
    }

    if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] != 'change') {
        $form -> freeze();
    } else {
        $form -> addElement('submit', 'submit_add_period', _SAVECHANGES, 'class = "flatButton"');           //The submit period button

        if ($form -> isSubmitted() && $form -> validate()) {
            $fromTimestamp = mktime($_POST['from_Hour'], $_POST['from_Minute'], 0, $_POST['from_Month'], $_POST['from_Day'], $_POST['from_Year']);
            $toTimestamp   = mktime($_POST['to_Hour'], $_POST['to_Minute'], 0, $_POST['to_Month'],   $_POST['to_Day'],   $_POST['to_Year']);

            if ($fromTimestamp < $toTimestamp) {
                $currentLesson -> lesson['from_timestamp'] = $fromTimestamp;
                $currentLesson -> lesson['to_timestamp']   = $toTimestamp;
                //$currentLesson -> lesson['shift']          = $form -> exportValue('shift') ? 1 : 0;

                $currentLesson -> persist();
            } else {
                $message      = _ENDDATEMUSTBEBEFORESTARTDATE;
                $message_type = 'failure';
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_ADD_PERIOD_FORM', $renderer -> toArray());

}
/*
Projects page is responsible for displaying and configuring projects.
The professor may assign students to tests as he wants. The code below
is responsible for displaying the current projects and add new ones.
*/
elseif ($ctg == 'projects') {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    try {
        $projects = $currentLesson -> getProjects(true);
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
    if (isset($_GET['delete_project']) && eF_checkParameter($_GET['delete_project'], 'id') && in_array($_GET['delete_project'], array_keys($projects))) {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $currentProject = $projects[$_GET['delete_project']];
        try {
            $currentProject -> delete();
            $message      = _PROJECTDELETEDSUCCESSFULLY;
            $message_type = 'success';
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=projects");
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _PROJECTCOULDNOTBEDELETED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    } else if (isset($_GET['compress_data']) && eF_checkParameter($_GET['compress_data'], 'id') && in_array($_GET['compress_data'], array_keys($projects))){         //download project data
        try {
            $currentProject = $projects[$_GET['compress_data']];
            $projectFiles   = $currentProject -> getFiles();

            if (!is_dir($currentUser -> user['directory'].'/projects/')) {
                mkdir($currentUser -> user['directory'].'/projects/', 0755);
            }
            $projectDir = $currentUser -> user['directory'].'/projects/'.$currentProject -> project['id'];
            if (!is_dir($projectDir)) {
                mkdir($projectDir, 0755);
            }
            $projectDirectory = new EfrontDirectory($projectDir);
            foreach ($projectFiles as $file) {
                try {
                    $projectFile = new EfrontFile($file['id']);
                    $newFileName = EfrontFile :: encode($file['surname'].'_'.$file['name'].'_'.date("d.m.Y", $file['upload_timestamp']).'_'.$projectFile['name']);
                    $projectFile -> copy($projectDir.'/'.$newFileName);
                } catch (EfrontFileException $e) {                    //Don't halt for a single file
                    $message .= $e -> getMessage().' ('.$e -> getCode().')';
                }
            }
            $zipFileName = $currentUser -> user['directory'].'/projects/'.EfrontFile :: encode($currentProject -> project['title']).'.zip';
            $zipFile     = $projectDirectory -> compress($zipFileName, false, true);
            $projectDirectory -> delete();
            header("location:view_file.php?file=".urlencode($zipFile['path'])."&action=download");

        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _FILESCOULDNOTBEDOWNLOADED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    } else if (isset($_GET['add_project']) || (isset($_GET['edit_project']) && eF_checkParameter($_GET['edit_project'], 'id')) && in_array($_GET['edit_project'], array_keys($projects))) {
    	//ajax request for inserting file in editor
    	if (isset($_GET['postAjaxRequest_insert'])) {
				$file_id 		= urldecode($_GET['file_id']);
                $file_insert 	= new EfrontFile($file_id);
				if (strpos($file_insert['mime_type'] , "image") !== false) {
					$img_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
					echo "<img src=\"".$img_return."\" border=0 />";
					exit;
				} elseif (strpos($file_insert['mime_type'] , "pdf") !== false) {
					$pdf_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
					echo '<iframe src="'.$pdf_return.'"  name="pdfFrame_'.urlencode($file_insert['id']).'" width="100%" height="600"></iframe>';
					exit;
				} elseif (strpos($file_insert['mime_type'] , "flash") !== false) {
					$flash_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
					if ($_GET['editor_mode'] == "true") {
						echo '<img width="400" height="400" src="editor/tiny_mce/themes/advanced/images/spacer.gif"  title="'.$flash_return.'" alt="'.$flash_return.'" class="mceItemFlash" />';
						exit;
					} else {
						echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="400" height="400">
						<param name="src" value="'.$flash_return.'" />
						<param name="width" value="400" />
						<param name="height" value="400" />
						<embed type="application/x-shockwave-flash" src="'.$flash_return.'" width="400" height="400"></embed>
						</object>';
						exit;
					}
				} else {
					echo "<a href=view_file.php?action=download&file=".$file_id.">".$file_insert['physical_name']."</a>";
					exit;	
				
				}
        }
  
        $load_editor = true;
        $form = new HTML_QuickForm("create_project_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=projects".(isset($_GET['add_project']) ? '&add_project=1' : '&edit_project='.$_GET['edit_project']), "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');

        $form -> addElement('text', 'title', _PROJECTTITLE, 'class = "inputText"');
        $form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('title', _INVALIDFIELDDATA, 'checkParameter', 'text');

        $form -> addElement('checkbox', 'auto_assign', _AUTOASSIGNTONEWUSERS, null, 'class = "inputCheckBox"');
        $form -> addElement('textarea', 'data', _PROJECTDATA, 'id="editor_project_data" class = "inputProjectTextarea mceEditor" style = "width:100%;height:30em;"');

        if (isset($_GET['edit_project'])) {
            $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
            $loadScripts[] = 'scriptaculous/effects';

            $currentProject = $projects[$_GET['edit_project']];
            $smarty -> assign("T_CURRENT_PROJECT", $currentProject);
            $form -> setDefaults(array('title'       => $currentProject -> project['title'],
                                       'auto_assign' => $currentProject -> project['auto_assign'],
                                       'data'        => $currentProject -> project['data']));
            $smarty -> assign('T_DEADLINE_TIMESTAMP', $currentProject -> project['deadline']);

        } else {
            $smarty -> assign('T_DEADLINE_TIMESTAMP', mktime(0, 0, 0, date("m") + 1 ,date("d"), date("Y")));
        }
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_add_project', _SUBMIT, 'class=flatButton');

            if ($form -> isSubmitted() && $form -> validate()) {
                $deadline = mktime($_POST['deadline_Hour'], $_POST['deadline_Minute'], 0, $_POST['deadline_Month'], $_POST['deadline_Day'], $_POST['deadline_Year']);
                $values   = $form -> exportValues();
                try {
                    if (isset($_GET['add_project'])) {
                        $fields = array('title'         => $values['title'],
                                        'data'          => $values['data'],
                                        'deadline'      => $deadline,
                                        'creator_LOGIN' => $currentUser -> user['login'],
                                        'lessons_ID'    => $currentLesson -> lesson['id'],
                                        'auto_assign'   => $values['auto_assign'] ? 1 : 0);
                        $newProject = EfrontProject :: createProject($fields);

                        $message      = _PROJECTCREATEDSUCCESSFULLY;
                        $message_type = 'success';
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=projects&edit_project=".$newProject -> project['id']."&tab=project_users");
                    } else {
                        $currentProject -> project['title']       = $values['title'];
                        $currentProject -> project['data']        = $values['data'];
                        $currentProject -> project['deadline']    = $deadline;
                        $currentProject -> project['auto_assign'] = $values['auto_assign'] ? 1 : 0;
                        $currentProject -> persist();

                        $message = _PROJECTUPDATEDSUCCESSFULLY;
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=projects&message=".urlencode($message)."&message_type=success");
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }
        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_ADD_PROJECT_FORM', $renderer -> toArray());
        
        //Build the project users list
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
            $users        = $currentLesson  -> getUsers('student');
            $projectUsers = $currentProject -> getUsers();
            foreach ($users as $key => $user) {
                $users[$key]['checked'] = 0;
                if (in_array($key, array_keys($projectUsers))) {            //Set the checked status, depending on whether the user has this project
                    $users[$key]['checked'] = 1;
                } else if (!$user['active']) {
                    unset($users[$key]);
                }
            }
            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'login';
            }
            $users = eF_multiSort($users, $sort, $order);
            $smarty -> assign("T_USERS_SIZE", sizeof($users));
            if (isset($_GET['filter'])) {
                $users = eF_filterData($users, $_GET['filter']);
            }
            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $users = array_slice($users, $offset, $limit);
            }

            $smarty -> assign("T_CURRENT_USER", $currentUser);
            $smarty -> assign("T_ALL_USERS", $users);
            $smarty -> display('professor.tpl');
            exit;
        }
        //ajax request to register users with project
        if (isset($_GET['postAjaxRequest'])) {
            try {
                $users        = $currentLesson  -> getUsers('student');
                $projectUsers = $currentProject -> getUsers();
                if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                    if (in_array($_GET['login'], array_keys($projectUsers))) {                    //The user has the project, so remove him
                        $currentProject -> removeUsers($_GET['login']);
                    } elseif (in_array($_GET['login'], array_keys($users))) {                     //The user doesn't have the project, so add him
                        $currentProject -> addUsers($_GET['login']);
                    }
                } else if (isset($_GET['addAll'])) {
                    $currentProject -> addUsers(array_keys($users));
                } else if (isset($_GET['removeAll'])) {
                    $currentProject -> removeUsers(array_keys($projectUsers));
                }
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    		//configure the file manager
		$loadScripts[] = 'scriptaculous/effects';
            try {
                $basedir    = $currentLesson -> getDirectory();
                $filesystem = new FileSystemTree($basedir);
                $filesystem -> handleAjaxActions($currentUser);

                if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
                    $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
                } else {
                    $options = array('delete' => false, 'edit' => false, 'share' => false, 'upload' => false, 'create_folder' => false, 'zip' => false, 'lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
                }

                if (isset($_GET['edit_project'])) {
                	$url = basename($_SERVER['PHP_SELF']).'?ctg=projects&edit_project='.$_GET['edit_project'];
                }else{
                	$url = basename($_SERVER['PHP_SELF']).'?ctg=projects&add_project=1';
				}
				
                if (isset($_GET['ajax'])) {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }

                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    }
                    isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                    isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                    $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                    $extraFileTools = array(array('image' => 'images/16x16/arrow_right_green.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
                    echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, '', '', '', false);
                    exit;
                }
                $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, '', '', '', false));
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            } 
	} else if (isset($_GET['project_results']) && eF_checkParameter($_GET['project_results'], 'id') && in_array($_GET['project_results'], array_keys($projects))) {
        $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
        $loadScripts[] = 'scriptaculous/effects';

        $currentProject = $projects[$_GET['project_results']];

        $smarty -> assign("T_CURRENT_PROJECT", $currentProject);
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
            $users          = $currentProject -> getUsers();
            //$files          = eF_getTableDataFlat("files", "id,original_name");
            sizeof($files) > 0 ? $files = array_combine($files['id'], $files['original_name']) : $files = array();
            foreach ($users as $key => $user) {
                if ($user['filename']) {
                    try {
                        $projectFile = new EfrontFile($user['filename']);
                        $users[$key]['file'] = $projectFile['name'];
                        !$user['upload_timestamp'] ? $users[$key]['upload_timestamp'] = 'empty' : null;    //Setting 'empty' here, makes possible to sort correctly onload (otherwise, empty timestamps where always put above more recent timestamps)
                    } catch (Exception $e) {
                        $users[$key]['filename']         = '';
                        $users[$key]['upload_timestamp'] = '';
                    }
                }
            }

            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'login';
            }
            $users = eF_multiSort($users, $sort, $order);
            $smarty -> assign("T_USERS_SIZE", sizeof($users));
            if (isset($_GET['filter'])) {
                $users = eF_filterData($users, $_GET['filter']);
            }
            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $users = array_slice($users, $offset, $limit);
            }

            $smarty -> assign("T_CURRENT_USER", $currentUser);
            $smarty -> assign("T_ALL_USERS", $users);
            $smarty -> display('professor.tpl');
            exit;
        }
        //ajax request to register project grades and comments
        if (isset($_GET['postAjaxRequest'])) {
            try {
                $projectUsers = $currentProject -> getUsers();
                if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login') && in_array($_GET['login'], array_keys($projectUsers))) {
                    $currentProject -> grade($_GET['login'], $_GET['grade'], $_GET['comments']);
                }
            } catch (Exception $e) {
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    } else {
        $currentProjects = array();
        $passedProjects  = array();
        foreach ($projects as &$project) {
            $projectUsers = $project -> getUsers();                                            //getUsers() initializes user information for the specified projects
            $project -> timeRemaining ? $currentProjects[] = $project : $passedProjects[] = $project;
        }
        unset($project);

        $smarty -> assign("T_CURRENT_PROJECTS", $currentProjects);
        $smarty -> assign("T_ACTIVE_COUNT", sizeof($currentProjects));

        $smarty -> assign("T_EXPIRED_PROJECTS", $passedProjects);
        $smarty -> assign("T_INACTIVE_COUNT", sizeof($passedProjects));
    }

}
/*
Tests page is responsible for displaying and configuring tests and questions
The professor may add, change and delete questions and tests
The code below is responsible for displaying tests and questions. The functions
used to configure them is included from module_addTest.php and module_addQuestion.php
*/
elseif ($ctg == 'tests') {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    $loadScripts[] = 'drag-drop-folder-tree';
	if ($configuration['math_content']) {
		$loadScripts[] = 'ASCIIMathML';
	}
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
    $loadScripts[] = 'scriptaculous/sidebar_extra';
    
    /**The tests module file*/
    include_once ('module_tests.php');


}
/*
The rules page is used to configure lesson rules and conditions.
Conditions are used to set the prerequisites that must be met so that a student
is considered to have completed the lesson succesfully. Furthermore, the professor
here defines the certificate details.
Rules are used to define a "lesson path". The user may access or not specific content
based on these rules
*/
elseif ($ctg == 'rules') {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
    try {
        $rules = $currentContent -> getRules();
        $units = array();
        foreach ($iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
            $units['id'][]     = $value['id'];
            $units['name'][]   = $value['name'];
            $units['active'][] = $value['active'];
        }

        $smarty -> assign("T_TREE_ACTIVE", array_combine($units['id'], $units['active']));
        $smarty -> assign("T_TREE_NAMES", array_combine($units['id'], $units['name']));
        $smarty -> assign("T_RULES", $rules);

        $conditions      = $currentLesson -> getConditions();
        $condition_types = array('all_units'        => _PASSEDALLUNITS,
                                 'percentage_units' => _PERCENTAGEUNITS,
                                 'specific_unit'    => _SPECIFICUNIT,
                                 'all_tests'        => _PASSEDALLTESTS,
                                 'specific_test'    => _SPECIFICTEST);
        $smarty -> assign("T_LESSON_CONDITIONS", $conditions);
        $smarty -> assign("T_CONDITION_TYPES", $condition_types);
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().'('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = "failure";
    }

    if (isset($_GET['delete_rule']) && eF_checkParameter($_GET['delete_rule'], 'id')) {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        try {
            $currentContent -> deleteRules($_GET['delete_rule']);
            $message      = _RULEDELETED;
            $message_type = 'success';
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().'('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = "failure";
        }
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=rules&message=".$message."&message_type=".$message_type);
    } elseif (isset($_GET['add_rule']) || (isset($_GET['edit_rule']) && eF_checkParameter($_GET['edit_rule'], 'id'))) {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        isset($_GET['add_rule']) ? $post_target = 'add_rule=1' : $post_target = 'edit_rule='.$_GET['edit_rule'];

        $form = new HTML_QuickForm("add_rule_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=rules&".$post_target, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
        $form -> registerRule('in_array', 'callback', 'in_array');

        $users = $currentLesson -> getUsers('student');
        sizeof($users) > 0 ? $users = array('*' => _ALLOFTHEM) + array_combine(array_keys($users), array_keys($users)) : $users = array('*' => _ALLOFTHEM);

        $form -> addElement('select', 'scope', null, $users, 'class = "inputSelect"');
        $form -> addRule('scope', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('scope', _INVALIDLOGIN, 'checkParameter', 'text');

        $testsIterator = new EfrontTestsFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));
        $testUnits = $currentContent -> toHTMLSelectOptions($testsIterator);

        $contentIterator = new EfrontContentFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));    //Get active units that are anything but tests (false negates both rules)
        $noTestUnits     = $currentContent -> toHTMLSelectOptions($contentIterator);

        $form -> addElement('select', 'exclusion_unit', null, $currentContent -> toHTMLSelectOptions(), 'class = "inputSelect"');
        $form -> addRule('exclusion_unit', _INVALIDID, null, 'numeric');

        $rule_type = array('always' => _ALWAYS, 'hasnot_seen' => _HASNOTSEENTHEUNIT, 'hasnot_passed' => _HASNOTPASSEDTHETEST);
        $form -> addElement('select', 'rule_type', null, $rule_type, 'class = "inputSelect" onchange = "selectRule(this)"');
        $form -> addRule('rule_type', _INVALIDRULE, 'in_array', array_keys($rule_type));

        $form -> addElement('select', 'rule_unit', null, $noTestUnits, 'class = "inputSelect"');
        $form -> addRule('rule_unit', _INVALIDID, 'numeric', null, 'client');

        $form -> addElement('select', 'test_unit', null, $testUnits, 'class = "inputSelect"');
        $form -> addRule('test_unit', _INVALIDID, 'numeric', null, 'client');

        $form -> addElement('text', 'score', null, 'style = "width:5em"');
        $form -> addRule('score', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('score', _INVALIDSCORE, 'numeric');

        $form -> addElement('submit', 'submit_rule', _SUBMIT, 'class = "flatButton"');

        if ($_GET['edit_rule']) {
            $form -> setDefaults(array('scope'          => $rules[$_GET['edit_rule']]['users_LOGIN'],
                                       'exclusion_unit' => $rules[$_GET['edit_rule']]['content_ID'],
                                       'rule_type'      => $rules[$_GET['edit_rule']]['rule_type'],
                                       'rule_unit'      => $rules[$_GET['edit_rule']]['rule_content_ID'],
                                       'test_unit'      => $rules[$_GET['edit_rule']]['rule_content_ID'],
                                       'score'          => $rules[$_GET['edit_rule']]['rule_option'] * 100));
            $smarty -> assign("T_CURRENT_RULE", $rules[$_GET['edit_rule']]['rule_type']);
        } else {
            $form -> setDefaults(array('score' => 50));
        }

        if ($form -> isSubmitted()) {
            if ($form -> exportValue('rule_type') == 'hasnot_passed' && ($form -> exportValue('score') < 1 || $form -> exportValue('score') > 100)) {
                $message      = _RATEMUSTBEBETWEEN1100;
                $message_type = 'failure';
            } elseif ($form -> validate()) {
                $fields = array('users_LOGIN' => $form -> exportValue('scope'),
                                'content_ID'  => $form -> exportValue('exclusion_unit'),
                                'lessons_ID'  => $currentLesson -> lesson['id']);

                switch ($form -> exportValue('rule_type')) {
                    case 'always':
                        $fields['rule_type'] = 'always';
                        break;
                    case 'hasnot_seen':
                        $fields['rule_type']       = 'hasnot_seen';
                        $fields['rule_content_ID'] = $form -> exportValue('rule_unit');
                        break;
                    case 'hasnot_passed':
                        $fields['rule_type']       = 'hasnot_passed';
                        $fields['rule_content_ID'] = $form -> exportValue('test_unit');
                        $fields['rule_option']     = round($form -> exportValue('score') / 100, 2);
                        break;
                    default:
                        break;
                }

                if (isset($_GET['edit_rule'])) {
                    if (eF_updateTableData("rules", $fields, "id=".$_GET['edit_rule'])) {
                        $message      = _SUCCESFULLYUPDATEDRULE;
                        $message_type = 'success';
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=rules&message=".$message."&message_type=".$message_type);
                    } else {
                        $message      = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                } else {
                    if (eF_insertTableData("rules", $fields)) {
                        $message      = _SUCCESFULLYINSERTEDRULE;
                        $message_type = 'success';
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=rules&message=".$message."&message_type=".$message_type);
                    } else {
                        $message      = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                }
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_ADD_RULE_FORM', $renderer -> toArray());

        $form = new HTML_QuickForm("add_ready_rule_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=rules&add_rule=1", "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
        $form -> addElement('radio', 'ready_rule', _SERIALRULE, null, 'serial', "checked");
        $form -> addElement('radio', 'ready_rule', _TREERULE, null, 'tree');
        $form -> addElement('submit', 'submit_ready_rule', _SUBMIT, 'class=flatButton');

        if ($form -> isSubmitted() && $form -> validate()) {
            $fields = array('users_LOGIN' => '*',
                            'content_ID'  => 0,
                            'lessons_ID'  => $currentLesson -> lesson['id']);
            switch ($form -> exportValue('ready_rule')) {
                case 'tree':
                    $fields['rule_type'] = 'tree';
                    break;
                case 'serial': default:
                    $fields['rule_type'] = 'serial';
                    break;
            }
            if (eF_insertTableData("rules", $fields)) {
                $message      = _SUCCESFULLYINSERTEDRULE;
                $message_type = 'success';
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=rules&message=".$message."&message_type=".$message_type);
            } else {
                $message      = _SOMEPROBLEMEMERGED;
                $message_type = 'failure';
            }

        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $smarty -> assign('T_ADD_READY_RULE_FORM', $renderer -> toArray());

    } elseif (isset($_GET['delete_condition']) && eF_checkParameter($_GET['delete_condition'], 'id')) {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        try {
            $currentLesson -> deleteConditions($_GET['delete_condition']);
            $message      = _SUCCESFULLYDELETEDCONDITION;
            $message_type = 'success';
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().'('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = "failure";
        }
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=rules&tab=conditions&message=".$message."&message_type=".$message_type);
    } elseif (isset($_GET['add_condition']) || (isset($_GET['edit_condition']) && eF_checkParameter($_GET['edit_condition'], 'id'))) {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        isset($_GET['add_condition']) ? $post_target = 'add_condition=1' : $post_target = 'edit_condition='.$_GET['edit_condition'];

        $form = new HTML_QuickForm("complete_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=rules&tab=conditions&'.$post_target, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
        $form -> registerRule('in_array', 'callback', 'in_array');

        $testsIterator = new EfrontTestsFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));
        $testUnits = $currentContent -> toHTMLSelectOptions($testsIterator);

        $contentIterator = new EfrontContentFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));    //Get active units that are anything but tests (false negates both rules)
        $noTestUnits     = $currentContent -> toHTMLSelectOptions($contentIterator);

        $form -> addElement('select', 'condition_types', null, $condition_types, 'class = "inputSelect" onchange = "eF_js_selectCondition(this)"');
        $form -> addRule('condition_types', _INVALIDCONDITION, 'in_array', array_keys($condition_types));

        $form -> addElement('text', 'percentage_units', null, 'style = "width:2.5em"');
        $form -> setDefaults(array('percentage_units' => 50));
        $form -> addRule('percentage_units', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('percentage_units', _INVALIDPERCENTAGE, 'numeric');

        $form -> addElement('select', 'specific_unit', null, $noTestUnits, 'class = "inputSelect"');
        $form -> addRule('specific_test', _INVALIDID, 'numeric', null, 'client');

        $form -> addElement('select', 'specific_test', null, $testUnits, 'class = "inputSelect"');
        $form -> addRule('test_unit', _INVALIDID, 'numeric', null, 'client');

        $form -> addElement('text', 'all_tests', null, 'style = "width:2.5em"');
        $form -> setDefaults(array('all_tests' => 50));
        $form -> addRule('all_tests', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('all_tests', _INVALIDSCORE, 'numeric');

        $form -> addElement('text', 'specific_test_score', null, 'style = "width:2.5em"');
        $form -> setDefaults(array('specific_test_score' => 50));
        $form -> addRule('specific_test_score', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('specific_test_score', _INVALIDSCORE, 'numeric');

        $form -> addElement('select', 'relation', null, array('and' => _AND, 'or' => _OR));

        $form -> addElement('submit', 'submit_complete_lesson_condition', _SUBMIT, 'class = "flatButton"');

        if (isset($_GET['edit_condition'])) {
            $smarty -> assign('T_CURRENT_CONDITION', $conditions[$_GET['edit_condition']]);
            $form -> setDefaults(array('condition_types' => $conditions[$_GET['edit_condition']]['type'], 'relation' => $conditions[$_GET['edit_condition']]['relation']));
            $form -> freeze('condition_types');

            $options = $conditions[$_GET['edit_condition']]['options'];
            switch ($conditions[$_GET['edit_condition']]['type']) {
                case 'percentage_units': $defaults = array('percentage_units' => $options[0]);                                                                                    break;
                case 'specific_unit':    $defaults = array('specific_unit'    => $options[0]);                                                                                    break;
                case 'all_tests':        $defaults = array('all_tests'        => $options[0]);                                                                                    break;
                case 'specific_test':    $defaults = array('specific_test'    => $options[0], 'specific_test_score' => $options[1]); break;
                default: break;
            }
            $form -> setDefaults($defaults);
        }

        if ($form -> isSubmitted()) {
            if ($form -> exportValue('condition_types') == 'all_tests' && ($form -> exportValue('all_tests') < 1 || $form -> exportValue('all_tests') > 100)) {
                $message      = _RATEMUSTBEBETWEEN1100;
                $message_type = 'failure';
            } elseif ($form -> exportValue('condition_types') == 'percentage_units' && ($form -> exportValue('percentage_units') < 1 || $form -> exportValue('percentage_units') > 100)) {
                $message      = _PERCENTAGEMUSTBEBETWEEN1100;
                $message_type = 'failure';
            } elseif ($form -> exportValue('condition_types') == 'specific_test' && ($form -> exportValue('specific_test_score') < 1 || $form -> exportValue('specific_test_score') > 100)) {
                $message      = _RATEMUSTBEBETWEEN1100;
                $message_type = 'failure';
            } elseif ($form -> validate()) {
                $fields = array('lessons_ID' => $_SESSION['s_lessons_ID'],
                                'type'       => $form -> exportValue('condition_types'),
                                'relation'   => $form -> exportValue('relation'));

                switch ($form -> exportValue('condition_types')) {
                    case 'percentage_units': $fields['options'] = serialize(array(0 => $form -> exportValue('percentage_units')));    break;
                    case 'specific_unit':    $fields['options'] = serialize(array(0 => $form -> exportValue('specific_unit')));       break;
                    case 'all_tests':        $fields['options'] = serialize(array(0 => $form -> exportValue('all_tests')));           break;
                    case 'specific_test':    $fields['options'] = serialize(array(0 => $form -> exportValue('specific_test'),
                                                                                  1 => $form -> exportValue('specific_test_score'))); break;
                    default: break;
                }

                if (isset($_GET['add_condition'])) {
                    if (eF_insertTableData('lesson_conditions', $fields)) {
                        $message      = _SUCCESFULLYADDEDCONDITION;
                        $message_type = 'success';
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=rules&tab=conditions&message=".$message."&message_type=".$message_type);
                    } else {
                        $message      = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                } else {
                    if (eF_updateTableData('lesson_conditions', $fields, "id=".$_GET['edit_condition'])) {
                        $message      = _SUCCESFULLYUPDATEDCONDITION;
                        $message_type = 'success';
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=rules&tab=conditions&message=".$message."&message_type=".$message_type);
                    } else {
                        $message      = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                }
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_COMPLETE_LESSON_FORM', $renderer -> toArray());
    }

    if (isset($_GET['ajax']) && isset($_GET['action']) && $_GET['action'] == 'auto_complete') {
        try {
            $currentLesson -> options['auto_complete'] ? $currentLesson -> setOptions(array('auto_complete' => 0)) : $currentLesson -> setOptions(array('auto_complete' => 1));
            echo $currentLesson -> options['auto_complete'];
            exit;
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
    }
}
/*
The statistics page displays all statistic information that concerns the professor.
This information is calculated and displayed through module_statistics.php
*/
elseif ($ctg == 'statistics') {
    if ($currentUser -> coreAccess['statistics'] != 'hidden') {
        require_once "module_statistics.php";
    } else {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
}
/**************/
/* MODULE HCD */
/**************/
elseif ($ctg == 'module_hcd' && MODULE_HCD_INTERFACE) {
   include "module_hcd.php";
}
/*
///MODULES3: Include the module code
 */
elseif ($ctg == 'module') {

//    $loadScripts = array_merge($loadScripts, array('scriptaculous/prototype', 'scriptaculous/scriptaculous'));
    $className = $_GET['op'];
    if (isset($loadedModules[$className])) {

        // Get top title navigational links
        $nav_links = $loadedModules[$className] -> getNavigationLinks();
        if ($nav_links) {
            $links_size = sizeof($nav_links);
            $count = 0;
            $module_nav_links = "";
            foreach ($nav_links as $link) {
                $module_nav_links .= '<a class="titleLink" href ="'.$link['link'].'">'.$link['title'].'</a>';
                $count++;
                if ($count < $links_size) {
                    $module_nav_links .= '&nbsp;&raquo;&nbsp;';
                }
            }
        } else {
            $module_nav_links = '<a class="titleLink" href ="'.$loadedModules[$className] -> moduleBaseUrl.'">'.$loadedModules[$className] -> getName().'</a>';
        }
        $smarty -> assign("T_MODULE_NAVIGATIONAL_LINKS", $module_nav_links);

        // Get link to highlight on the left sidebar
        $highlight = $loadedModules[$className] -> getLinkToHighlight();
        if ($highlight) {
            $highlight = $loadedModules[$className] -> className . "_" . $highlight;
        } else {
            $highlight = $loadedModules[$className] -> className;
        }
        $smarty -> assign("T_MODULE_HIGHLIGHT", $highlight);

        // Get module html - two ways: pure HTML or PHP+smarty
        // If no smarty file is defined then false will be returned
        if ($module_smarty_file = $loadedModules[$className] -> getSmartyTpl()) {
            // Execute the php code
            $loadedModules[$className] -> getModule();
            // Let smarty know to include the module smarty file
            $smarty -> assign("T_MODULE_SMARTY", $module_smarty_file);
        } else {
            // Present the pure HTML code
            $smarty -> assign("T_MODULE_PAGE", $loadedModules[$className] -> getModule());
        }
    } else {
        $message = _ERRORLOADINGMODULE;
        $message_type = "failure";
    }

}

/*
Emails is the page that is used to send email to system users.
*/
elseif ($ctg == "emails" && MODULE_HCD_INTERFACE) {
   include "emails.php";
}
/*
Users is the page that concerns EMPLOYEE administration for users with supervisor rights. It uses module_personal.php to perform most of the update functions,
since the same functions need to be performed from the professor and student as well (for themseleves)
There are 5 sub options in this page, denoted by an extra link part:
- &add_user=1                   When we are adding a new user
- &delete_user=<login>          When we want to delete user <login>
- &edit_user=<login>            When we want to edit user <login>
- &deactivate_user=<login>      When we deactivate user <login>
- &activate_user=<login>        When we activate user <login>
*/
elseif ($ctg == 'users' && MODULE_HCD_INTERFACE) {

    $unprivileged = false;                          //This variable is used to check whether the current user is elegible (based on his role) to access this area
    $currentEmployee = $currentUser -> aspects['hcd'];
    if ($_SESSION['s_type'] != "administrator" && $currentEmployee -> getType() != _SUPERVISOR && !($currentEmployee -> getType() == _EMPLOYEE && (isset($_GET['add_evaluation'])||isset($_GET['edit_evaluation']) || isset($_GET['delete_evaluation'])) && $_SESSION['s_type']=="professor" )) {
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = "failure";
        header("location:".$_SERVER['HTTP_REFERER']."&message=".$message."&message_type=".$message_type);
        exit;
    } else {

        if (isset($_GET['delete_user']) && eF_checkParameter($_GET['delete_user'], 'login') && !$unprivileged) {    //The administrator asked to delete a user
            if (eF_deleteUser($_GET['delete_user'])) {

                /** MODULE HCD: Delete the employee relevant information **/
                if ($module_hcd_interface) {
                    eF_deleteTableData("module_hcd_employees", "users_login='".$_GET['delete_user']."'");
                    eF_deleteTableData("module_hcd_employee_has_skill", "users_login='".$_GET['delete_user']."'");
                    eF_deleteTableData("module_hcd_employee_has_job_description", "users_login='".$_GET['delete_user']."'");
                    eF_deleteTableData("module_hcd_employee_works_at_branch", "users_login='".$_GET['delete_user']."'");

                    // Register user's firing into the event log
                    eF_insertTableData("module_hcd_events", array("event_code"    => $MODULE_HCD_EVENTS['FIRED'],
                                                                  "users_login"   => $_GET['delete_user'],
                                                                  "specification" => _FIRED,
                                                                  "timestamp"     => time()));
                    $message      = _EMPLOYEEDELETED;
                } else {
                    $message      = _USERDELETED;
                }
                $message_type = 'success';
            } else {
                $message      = _SOMEORALLOFTHEUSERELEMENTSCOULDNOTBEDELETED;
                $message_type = "failure";
            }
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['deactivate_user']) && eF_checkParameter($_GET['deactivate_user'], 'login') && !$unprivileged) {      //The administrator asked to deactivate a user
            if (eF_updateTableData("users", array('active' => 0), "login='".$_GET['deactivate_user']."'")) {
                $message      = _USERDEACTIVATED;
                $message_type = 'success';
            } else {
                $message      = _SOMEPROBLEMEMERGED;
                $message_type = "failure";
            }
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['activate_user']) && eF_checkParameter($_GET['activate_user'], 'login') && !$unprivileged) {          //The administrator asked to activate a user
            if (eF_updateTableData("users", array('active' => 1, 'pending' => 0), "login='".$_GET['activate_user']."'")) {
                $message      = _USERACTIVATED;
                $message_type = 'success';
            } else {
                $message      = _SOMEPROBLEMEMERGED;
                $message_type = "failure";
            }
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['add_user']) || (isset($_GET['edit_user']) && $login = eF_checkParameter($_GET['edit_user'], 'login')) && !$unprivileged) {   //The administrator asked to add a new user or to edit a user
            $smarty -> assign("T_PERSONAL", true);
            /**Include the personal settings file*/
            include "module_personal.php";                      //User addition and manipulation is done through module_personal.

        } else {                                                //The professor just asked to view the users
    //dddddddddddddddddd

            if (!MODULE_HCD_INTERFACE) {
                $result_with_lessons    = eF_getTableData("users_to_lessons, users","count( * ) AS lessons_num, users.*", "users_LOGIN = login AND users_LOGIN IN ( SELECT login FROM users) GROUP BY login");
                $result_without_lessons = eF_getTableData("users","*", "login NOT IN ( SELECT DISTINCT (users_LOGIN) FROM users_to_lessons)");
                $result = array_merge($result_with_lessons, $result_without_lessons);  //right is this: SELECT name, login, count( lessons_ID )FROM usersLEFT OUTER JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN GROUP BY login
                for ($i = 0; $i < sizeof($result); $i++) {
                    foreach ($result[$i] as $key => $value) {
                        if ($key == 'user_type') {
                            $result[$i][$key] = $TRANSLATION[$value];
                        }
                    }
                }
                $smarty -> assign("T_USERS", $result);

            } else {

                $_GET['op'] = "employees";
                include "module_hcd.php";
            }
        }
   }
}

/*
The surveys page handles the surveys section. All the corresponding code is
inside module_surveys.php
*/
elseif ($ctg == 'survey') {
    if ($currentUser -> coreAccess['surveys'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");exit;
    }
    /**This file handles surveys*/
    require_once "module_surveys.php";
}
/*
The glossary page is responsible for viewing and manipulating glossary words
*/
elseif ($ctg == 'glossary') {
    if ($currentUser -> coreAccess['glossary'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");exit;
    }
    $glossary_words = eF_getTableData("glossary_words", "id,name,info", "lessons_ID=".$_SESSION['s_lessons_ID']);

    $words = eF_getAllGlossaryWords($glossary_words);
    $smarty -> assign("T_GLOSSARY", $words);
}
/*
*/
elseif ($ctg == 'calendar') {
    if ($currentUser -> coreAccess['calendar'] != 'hidden') {
        include_once "calendar.php";
    } else {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
}
/*
Settings is the page where the professor may set configuration variables concerning the lesson and
perform actions related to the lesson, specifically:
- Set lesson information
- Set lesson which parts of the lesson will be made accessible. Possible parts are:
  Theory, Examples, Projects, Tests, Rules,
  Comments, Forum, Chat, Glossary,
  Online users, SCORM, Periods per student,
  Digital Library, Calendar, New Content (etc)
*/
elseif ($ctg == 'settings') {
    if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';

    $options = array(
                   0 => array('image' => '16x16/gear.png',    		 'title' => _LESSONOPTIONS, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=settings',                  'selected' => !isset($_GET['op'])                                  ? true : false),
                   1 => array('image' => '16x16/layout_center.png',  'title' => _LAYOUT,   	 	'link' => basename($_SERVER['PHP_SELF']).'?ctg=settings&op=lesson_layout', 'selected' => isset($_GET['op']) && $_GET['op'] == 'lesson_layout' ? true : false),
                   2 => array('image' => '16x16/refresh.png', 		 'title' => _RESTARTLESSON, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=settings&op=reset_lesson' , 'selected' => isset($_GET['op']) && $_GET['op'] == 'reset_lesson'  ? true : false),
                   3 => array('image' => '16x16/import2.png', 		 'title' => _IMPORTLESSON,  'link' => basename($_SERVER['PHP_SELF']).'?ctg=settings&op=import_lesson', 'selected' => isset($_GET['op']) && $_GET['op'] == 'import_lesson' ? true : false),
                   4 => array('image' => '16x16/export1.png', 		 'title' => _EXPORTLESSON,  'link' => basename($_SERVER['PHP_SELF']).'?ctg=settings&op=export_lesson', 'selected' => isset($_GET['op']) && $_GET['op'] == 'export_lesson' ? true : false),
                   5 => array('image' => '16x16/users3.png',  		 'title' => _LESSONUSERS,   'link' => basename($_SERVER['PHP_SELF']).'?ctg=settings&op=lesson_users',  'selected' => isset($_GET['op']) && $_GET['op'] == 'lesson_users'  ? true : false));
    //Unset values based on user's type restrictions
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        unset($options[1]);
        unset($options[2]);
        unset($options[3]);
    }
    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
        unset($options[4]);
    }
    //Reindex options so that indices are serial starting from 0 (this way they display correctly)
    $options = array_values($options);

    $smarty -> assign("T_TABLE_OPTIONS", $options);

    if ($_GET['op'] == 'reset_lesson') {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        /*Reset lesson part*/
        $form = new HTML_QuickForm("reset_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=settings&op=reset_lesson', "", null, true);
        $form -> addElement('checkbox', 'users',    null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson students
        $form -> addElement('checkbox', 'news',     null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson announcements
        $form -> addElement('checkbox', 'comments', null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson comments
        $form -> addElement('checkbox', 'rules',    null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson rules
        $form -> addElement('checkbox', 'calendar', null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson calendar
        $form -> addElement('checkbox', 'glossary', null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson glossary
        $form -> addElement('checkbox', 'tracking', null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson tracking information
        $form -> addElement('submit', 'submit_reset_lesson', _SUBMIT, 'class = "flatButton"');

        if ($form -> isSubmitted() && $form -> validate()) {
            $lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
            $lesson -> initialize(array_keys($form -> exportValues()));

            $message      = _RESTARTLESSONCOMPLETED;
            $message_type = 'success';
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $smarty -> assign('T_RESET_LESSON_FORM', $renderer -> toArray());
    } elseif ($_GET['op'] == 'import_lesson') {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        /* Import part */
        $form = new HTML_QuickForm("import_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=settings&op=import_lesson', "", null, true);
/*
        $form -> addElement('checkbox', 'content',  null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson content
        $form -> addElement('checkbox', 'periods',  null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson periods
        $form -> addElement('checkbox', 'files',    null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson files
        $form -> addElement('checkbox', 'users',    null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson students
        $form -> addElement('checkbox', 'news',     null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson announcements
        $form -> addElement('checkbox', 'comments', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson comments
        $form -> addElement('checkbox', 'rules',    null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson rules
        $form -> addElement('checkbox', 'calendar', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson calendar
        $form -> addElement('checkbox', 'calendar', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson calendar
        $form -> addElement('checkbox', 'glossary', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson glossary
        $form -> addElement('checkbox', 'tracking', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson tracking information
        $form -> addElement('checkbox', 'surveys',  null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson surveys
*/
        $form -> addElement('file', 'file_upload', null, 'class = "inputText"');                    //Lesson file
        $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB
        $form -> addElement('submit', 'submit_import_lesson', _SUBMIT, 'class = "flatButton"');

        $smarty -> assign("T_MAX_FILESIZE", FileSystemTree :: getUploadMaxSize());

        if ($form -> isSubmitted() && $form -> validate()) {
            try {
                //Everything except for users
                $deleteEntities = array('content', 'surveys', 'tracking', 'files',
                                        'conditions', 'calendar', 'periods', 'news',
                                        'glossary', 'comments', 'rules', 'questions');
                $currentLesson -> initialize($deleteEntities);
                $filesystem     = new FileSystemTree($currentLesson -> getDirectory());
                $uploadedFile   = $filesystem -> uploadFile('file_upload', $currentLesson -> getDirectory());
                $currentLesson -> import($uploadedFile);

                $smarty -> assign("T_REFRESH_SIDE", 1);

                $message      = _LESSONIMPORTEDSUCCESFULLY;
                $message_type = 'success';
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = _PROBLEMIMPORTINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $smarty -> assign('T_IMPORT_LESSON_FORM', $renderer -> toArray());
    } elseif ($_GET['op'] == 'export_lesson') {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }

        /* Export part */
        $form = new HTML_QuickForm("export_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=settings&op=export_lesson', "", null, true);
        $form -> addElement('submit', 'submit_export_lesson', _EXPORT, 'class = "flatButton"');

        try {
            $currentExportedFile = new EfrontFile($currentUser -> user['directory'].'/temp/'.EfrontFile :: encode($currentLesson -> lesson['name']).'.zip');
            $smarty -> assign("T_EXPORTED_FILE", $currentExportedFile);
        } catch (Exception $e) {}

        if ($form -> isSubmitted() && $form -> validate()) {
            try {
                $lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
                $file   = $lesson -> export('all');
                $smarty -> assign("T_NEW_EXPORTED_FILE", $file);

                $message      = _LESSONEXPORTEDSUCCESFULLY;
                $message_type = 'success';
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $smarty -> assign('T_EXPORT_LESSON_FORM', $renderer -> toArray());

    } elseif ($_GET['op'] == 'lesson_users') {
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        try {
            $lessonUsers    = $currentLesson -> getUsers();                    //Get all users that have this lesson
            unset($lessonUsers[$currentUser -> login]);                        //Remove the current user from the list, he can't set parameters for his self!
            $nonLessonUsers = $currentLesson -> getNonUsers();                 //Get all the users that can, but don't, have this lesson
            $users          = array_merge($lessonUsers, $nonLessonUsers);      //Merge users to a single array, which will be useful for displaying them

            foreach ($users as $key => $user) {
                in_array($key, array_keys($nonLessonUsers)) ? $users[$key]['in_lesson'] = false : $users[$key]['in_lesson'] = true;
            }

            $roles = eF_getTableDataFlat("user_types","name","active=1 AND basic_user_type!='administrator'");    //Get available roles
            if (sizeof($roles) > 0) {
                $roles = array_combine($roles['name'], $roles['name']);                                          //Match keys with values, it's more practical this way
            }
            $roles = array_merge(array('student' => _STUDENT, 'professor' => _PROFESSOR), $roles);                     //Append basic user types to the beginning of the array

            if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }
                $users = eF_multiSort($users, $sort, $order);
                $smarty -> assign("T_USERS_SIZE", sizeof($users));
                if (isset($_GET['filter'])) {
                    $users = eF_filterData($users, $_GET['filter']);
                }
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $users = array_slice($users, $offset, $limit);
                }

                $smarty -> assign("T_ROLES", $roles);
                $smarty -> assign("T_ALL_USERS", $users);
                $smarty -> assign("T_LESSON_USERS", array_keys($lessonUsers));                                             //We assign separately the lesson's users, to know when to display the checkboxes as "checked"
                $smarty -> assign("T_CURRENT_USER", $currentUser);
                $smarty -> display('professor.tpl');
                exit;
            }

        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }

        if (isset($_GET['postAjaxRequest'])) {
            try {
                if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                    isset($_GET['user_type']) && in_array($_GET['user_type'], array_keys($roles)) ? $userType = $_GET['user_type'] : $userType = 'student';
                    if (in_array($_GET['login'], array_keys($nonLessonUsers))) {
                        $currentLesson -> addUsers($_GET['login'], $userType);
                    }
                    if (in_array($_GET['login'], array_keys($lessonUsers))) {
                        $userType != $lessonUsers[$_GET['login']]['role'] ? $currentLesson -> setRoles($_GET['login'], $userType) : $currentLesson -> removeUsers($_GET['login']);
                    }
                } else if (isset($_GET['addAll'])) {
                    $currentLesson -> addUsers(array_keys($nonLessonUsers));
                } else if (isset($_GET['removeAll'])) {
                    $currentLesson -> removeUsers(array_keys($lessonUsers));
                }
            } catch (Exception $e) {
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    } elseif ($_GET['op'] == 'lesson_layout') {        
        $loadScripts[] = 'scriptaculous/scriptaculous';
        $loadScripts[] = 'scriptaculous/effects';
        $loadScripts[] = 'scriptaculous/dragdrop';
        $defaultPositions = unserialize($currentLesson -> options['default_positions']);
        
        $result = eF_getTableData("modules", "*");
        foreach ($result as $value) {
            $moduleInfo[$value['className']] = $value;
        }        
        $curretType = $currentUser -> user['user_type'];
        $currentUser -> user['user_type'] = 'student';
        $modules    = eF_loadAllModules();        
        foreach($modules as $key => $module) {            
            if ($module -> getLessonModule() && $currentLesson -> options[$key]) {  
                $lessonModules[$key] = $moduleInfo[$key];                
            }
        }        
        $currentUser -> user['user_type'] = $currentType;
//pr($lessonModules);
        $smarty -> assign("T_LESSON_MODULES", $lessonModules);
        
        $invalidOptions = array();
        !$currentLesson -> options['content_tree']    ? $invalidOptions['moduleContentTree']    = 1 : null;
        !$currentLesson -> options['projects']        ? $invalidOptions['moduleProjectsList']   = 1 : null;
        !$currentLesson -> options['forum']           ? $invalidOptions['moduleForumList']      = 1 : null;
        !$currentLesson -> options['comments']        ? $invalidOptions['moduleComments']       = 1 : null;
        !$currentLesson -> options['calendar']        ? $invalidOptions['moduleCalendar']       = 1 : null;
        !$currentLesson -> options['digital_library'] ? $invalidOptions['moduleDigitalLibrary'] = 1 : null;
        $smarty -> assign("T_INVALID_OPTIONS", $invalidOptions);
        
        

        $smarty -> assign("T_DEFAULT_POSITIONS", $defaultPositions);
    } else {
        $lessonSettings['theory']          = array('text' => _THEORY,            'image' => isset($currentLesson -> options['theory'])          && $currentLesson -> options['theory']          ? "32x32/book_blue.png"     : "32x32/book_blue_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'theory\')',          'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['examples']        = array('text' => _EXAMPLES,          'image' => isset($currentLesson -> options['examples'])        && $currentLesson -> options['examples']        ? "32x32/lightbulb_on.png"  : "32x32/lightbulb_on_gray.png",  'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'examples\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['projects']        = array('text' => _PROJECTS,          'image' => isset($currentLesson -> options['projects'])        && $currentLesson -> options['projects']        ? "32x32/exercises.png"     : "32x32/exercises_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'projects\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['tests']           = array('text' => _TESTS,             'image' => isset($currentLesson -> options['tests'])           && $currentLesson -> options['tests']           ? "32x32/document_edit.png" : "32x32/document_edit_gray.png", 'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'tests\')',           'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['survey']          = array('text' => _SURVEY,            'image' => isset($currentLesson -> options['survey'])          && $currentLesson -> options['survey']          ? "32x32/form_green.png"    : "32x32/form_green_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'survey\')',          'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['rules']           = array('text' => _ACCESSRULES,       'image' => isset($currentLesson -> options['rules'])           && $currentLesson -> options['rules']           ? "32x32/recycle.png"       : "32x32/recycle_gray.png",       'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'rules\')',           'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['forum']           = array('text' => _FORUM,             'image' => isset($currentLesson -> options['forum'])           && $currentLesson -> options['forum']           ? "32x32/messages.png"      : "32x32/messages_gray.png",      'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'forum\')',           'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['comments']        = array('text' => _COMMENTS,          'image' => isset($currentLesson -> options['comments'])        && $currentLesson -> options['comments']        ? "32x32/note.png"          : "32x32/note_gray.png",          'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'comments\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['online']          = array('text' => _USERSONLINE,       'image' => isset($currentLesson -> options['online'])          && $currentLesson -> options['online']          ? "32x32/users4.png"        : "32x32/users4_gray.png",        'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'online\')',          'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        
        $chat_enabled = eF_getTableData("configuration", "value", "name ='chat_enabled'");
        if ($chat_enabled[0]['value'] == 1) {
            $lessonSettings['chat']            = array('text' => _CHAT,              'image' => isset($currentLesson -> options['chat'])            && $currentLesson -> options['chat']            ? "32x32/user1_message.png" : "32x32/user1_message_gray.png", 'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'chat\')',            'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        }    
        $lessonSettings['scorm']           = array('text' => _SCORM,             'image' => isset($currentLesson -> options['scorm'])           && $currentLesson -> options['scorm']           ? "32x32/book_red.png"      : "32x32/book_red_gray.png",      'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'scorm\')',           'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        //$lessonSettings['dynamic_periods'] = array('text' => _PERIODSPERSTUDENT, 'image' => isset($currentLesson -> options['dynamic_periods']) && $currentLesson -> options['dynamic_periods'] ? "32x32/user1_time.png"    : "32x32/user1_time_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'dynamic_periods\')', 'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['digital_library'] = array('text' => _DIGITALLIBRARY,    'image' => isset($currentLesson -> options['digital_library']) && $currentLesson -> options['digital_library'] ? "32x32/disk_blue.png"     : "32x32/disk_blue_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'digital_library\')', 'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['calendar']        = array('text' => _CALENDAR,          'image' => isset($currentLesson -> options['calendar'])        && $currentLesson -> options['calendar']        ? "32x32/calendar.png"      : "32x32/calendar_gray.png",      'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'calendar\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['new_content']     = array('text' => _NEWCONTENT,        'image' => isset($currentLesson -> options['new_content'])     && $currentLesson -> options['new_content']     ? "32x32/book_blue_new.png" : "32x32/book_blue_new_gray.png", 'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'new_content\')',     'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['glossary']        = array('text' => _GLOSSARY,          'image' => isset($currentLesson -> options['glossary'])        && $currentLesson -> options['glossary']        ? "32x32/book_open2.png"    : "32x32/book_open2_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'glossary\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['auto_complete']   = array('text' => _AUTOCOMPLETE,      'image' => isset($currentLesson -> options['auto_complete'])   && $currentLesson -> options['auto_complete']   ? "32x32/book_green.png"    : "32x32/book_green_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'auto_complete\')',   'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['content_tree']    = array('text' => _CONTENTTREEFIRSTPAGE,'image' => isset($currentLesson -> options['content_tree'])  && $currentLesson -> options['content_tree']    ? "32x32/text_tree.png"     : "32x32/text_tree_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'content_tree\')',    'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['lesson_info']	   = array('text' => _LESSONINFORMATION, 'image' => isset($currentLesson -> options['lesson_info'])  	&& $currentLesson -> options['lesson_info']    	? "32x32/about.png"     	: "32x32/about_gray.png",     	  'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'lesson_info\')',     'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        $lessonSettings['bookmarking']	   = array('text' => _BOOKMARKS,		'image' => isset($currentLesson -> options['bookmarking'])  	&& $currentLesson -> options['bookmarking']    	? "32x32/bookmark.png"     	: "32x32/bookmark_gray.png",      'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'bookmarking\')',     'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
		$lessonSettings['content_report']  = array('text' => _CONTENTREPORT,	'image' => isset($currentLesson -> options['content_report'])  	&& $currentLesson -> options['content_report']  ? "32x32/warning.png"     	: "32x32/warning_gray.png",		  'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'content_report\')',     'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
		$lessonSettings['start_resume']	   = array('text' => _STARTRESUME,		'image' => isset($currentLesson -> options['start_resume'])  	&& $currentLesson -> options['start_resume']    ? "32x32/media_play.png"    : "32x32/media_play_gray.png",	  'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'start_resume\')',     'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
		//$lessonSettings['tracking']        = array('text' => _TRACKING,          'image' => isset($currentLesson -> options['tracking'])        && $currentLesson -> options['tracking']        ? "32x32/dot-chart.png"     : "32x32/dot-chart_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'tracking\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);

        ///MODULES6
        foreach ($loadedModules as $module) {
            $lessonSettings[$module -> className] = array('text' => $module -> getName(), 'image' => ($currentLesson -> options[$module -> className] == 1) ? "32x32/component_green.png"  : "32x32/component_green_gray.png", 'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \''.$module -> className.'\')', 'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
        }

        foreach ($currentLesson -> options as $key => $value) {                                               //Remove activated elements from above list
            if ($value && isset($lessonSettings[$key])) {
                $lessonSettings[$key]['onClick'] = 'activate(this, \''.$key.'\')';
                $lessonSettings[$key]['style']   = 'color:inherit';
            }
        }

        //If the professor's type restricts access to settings, unset all 'onclick' actions
        if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] != 'change') {
            foreach ($lessonSettings as $key => $value) {
                $lessonSettings[$key]['onClick'] = '';
            }
        }

        $smarty -> assign("T_LESSON_SETTINGS", $lessonSettings);

        if (!isset($currentUser -> coreAccess['settings']) || $currentUser -> coreAccess['settings'] == 'change') {
            if (isset($_GET['ajax']) && isset($_GET['activate']) && in_array($_GET['activate'], array_keys($lessonSettings))) {
                try {
                    $currentLesson -> options[$_GET['activate']] = 1;
                    if ($_GET['activate'] == 'chat') {
                        $currentLesson -> enableChatroom();
                    }
                    $currentLesson -> persist();
                    if ($currentLesson -> options['digital_library'] == 1) {                        //If the professor set a digital library, create the corresponding if folder, if it does not exist
                        if (!is_dir(G_LESSONSPATH.$currentLesson -> lesson['id']."/"."Digital Library"))
                        @mkdir(G_LESSONSPATH.$currentLesson -> lesson['id']."/"."Digital Library");
                    }
                    echo "Option activated";
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            } elseif (isset($_GET['ajax']) && isset($_GET['deactivate']) && in_array($_GET['deactivate'], array_keys($lessonSettings))) {
                try {
                    pr($_GET);
                    $currentLesson -> options[$_GET['deactivate']] = 0;
                    if ($_GET['deactivate'] == 'chat') {
                        $currentLesson -> disableChatroom();
                    }
                    $currentLesson -> persist();
                    echo "Option deactivated";
                } catch (Exception $e) {
                    header("HTTP/1.0 500");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }
        }
    }
}
/*
The lessons page is the page where the user chooses which lesson
to view
*/
elseif ($ctg == 'lessons') {
    $userLessons = $currentUser -> getLessons();                //Get user lessons
    $userCourses = $currentUser -> getCourses();
/*
    if (sizeof($userLessons) == 0) {                                //This user hasn't enrolled to any lessons
        header("location:professor.php?ctg=personal&tab=lessons");
    } elseif (sizeof($userLessons) == 1 && !$currentLesson) {                           //The user has enrolled to a single lesson
        header("location:".current($userLessons).".php?ctg=control_panel&lessons_ID=".key($userLessons)."&message=".$message."&message_type=".$message_type);    //Redirect to the lesson's first page
    }
*/
    if (isset($_GET['op']) && isset($_GET['course']) && in_array($_GET['course'], array_keys($userCourses))) {
        $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
        $loadScripts[] = 'scriptaculous/effects';

        $options = array(array('image' => '16x16/about.png',       'title' => _COURSEINFORMATION,  'link' => $_GET['op'] != 'course_info'        ? basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$_GET['course'].'&op=course_info'         : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_info'         ? false : true),
                         array('image' => '16x16/certificate.png', 'title' => _COURSECERTIFICATES, 'link' => $_GET['op'] != 'course_certificate' ? basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$_GET['course'].'&op=course_certificates' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_certificates' ? false : true),
                         array('image' => '16x16/recycle.png',     'title' => _COURSERULES,        'link' => $_GET['op'] != 'course_rules'       ? basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$_GET['course'].'&op=course_rules'        : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_rules'        ? false : true),
                         array('image' => '16x16/replace2.png',    'title' => _COURSEORDER,        'link' => $_GET['op'] != 'course_order'       ? basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$_GET['course'].'&op=course_order'        : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_order'        ? false : true),
                         array('image' => '16x16/calendar.png',    'title' => _SCHEDULING,         'link' => $_GET['op'] != 'course_scheduling'  ? basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$_GET['course'].'&op=course_scheduling'   : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_scheduling'   ? false : true));
        $smarty -> assign("T_TABLE_OPTIONS", $options);

        $currentCourse = new EfrontCourse($_GET['course']);
        $smarty -> assign("T_CURRENT_COURSE", $currentCourse);

        if ($_GET['op'] == 'course_info') {
            $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);

            $courseInformation = unserialize($currentCourse -> course['info']);
            $information       = new LearningObjectInformation($courseInformation);
            $smarty -> assign("T_COURSE_INFO_HTML", $information -> toHTML($form, false));

            $courseMetadata = unserialize($currentCourse -> course['metadata']);
            $metadata       = new DublinCoreMetadata($courseMetadata);
            $smarty -> assign("T_COURSE_METADATA_HTML", $metadata -> toHTML($form));

            if (isset($_GET['postAjaxRequest'])) {
                if (in_array($_GET['dc'], array_keys($information -> metadataAttributes))) {
                    if ($_GET['value']) {
                        $courseInformation[$_GET['dc']] = $_GET['value'];
                    } else {
                        unset($courseInformation[$_GET['dc']]);
                    }
                    $currentCourse -> course['info'] = serialize($courseInformation);
                } elseif (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
                    if ($_GET['value']) {
                        $courseMetadata[$_GET['dc']] = $_GET['value'];
                    } else {
                        unset($courseMetadata[$_GET['dc']]);
                    }
                    $currentCourse -> course['metadata'] = serialize($courseMetadata);
                }

                $currentCourse -> persist();
                echo $_GET['value'];
                exit;
            }

        } else if ($_GET['op'] == 'course_certificates') {

            $users = EfrontStats::getUsersCourseStatus($currentCourse);
            $users = $users[$currentCourse -> course['id']];
            if (isset($_GET['edit_user']) && in_array($_GET['edit_user'], array_keys($users))) {
                $userStats = $users[$_GET['edit_user']];
                $form = new HTML_QuickForm("edit_user_complete_course_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$_GET['course'].'&op=course_certificates&edit_user='.$_GET['edit_user'].'&popup=1', "", null, true);
                $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

                $form -> addElement('advcheckbox', 'completed', _COMPLETED, null, 'class = "inputCheckbox"');            //Whether the user has completed the course
                $form -> addElement('text', 'score', _SCORE, 'class = "inputText" style = "width:40px;"');                                                        //The user course score
                $form -> addRule('score', _THEFIELD.' "'._SCORE.'" '._MUSTBENUMERIC, 'numeric', null, 'client');                            //The score must be numeric
                $form -> addRule('score', _RATEMUSTBEBETWEEN0100, 'callback', create_function('&$a', 'return ($a >= 0 && $a <= 100);'));    //The score must be between 0 and 100

                $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputContentTextarea simpleEditor" style = "width:100%;height:5em;"');      //Comments on student's performance
                $form -> addElement('submit', 'submit_course_complete', _SUBMIT, 'class = "flatButton"');       //The submit button

                $totalScore = 0;
                foreach ($userStats['lesson_status'] as $stat) {
                    $totalScore += $stat['score'] / sizeof($userStats['lesson_status']);
                }

                $form -> setDefaults(array("completed" => $userStats['completed'],
                                           "score"     => $userStats['completed'] ? $userStats['score'] : round($totalScore),
                                           "comments"  => $userStats['comments']));

                if ($form -> isSubmitted() && $form -> validate()) {
                    $fields = array("completed" => $form -> exportValue('completed') ? 1 : 0,
                                    "score"     => $form -> exportValue('completed') ? $form -> exportValue('score')    : 0,
                                    "comments"  => $form -> exportValue('completed') ? $form -> exportValue('comments') : '');
                    eF_updateTableData("users_to_courses", $fields, "users_LOGIN = '".$_GET['edit_user']."' and courses_ID=".$currentCourse -> course['id']);
                    echo '<script>parent.location="'.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$_GET['course'].'&op=course_certificates&message='.urlencode(_STUDENTSTATUSCHANGED).'&message_type=success"</script>';//header('location:'.ltrim("/", basename($_SERVER['PHP_SELF'])).'?ctg=lessons&course='.$_GET['course'].'&op=course_certificates&message='.urlencode(_STUDENTSTATUSCHANGED).'&message_type=success');
                }

                $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

                $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
                $form -> setRequiredNote(_REQUIREDNOTE);
                $form -> accept($renderer);

                $smarty -> assign('T_COMPLETE_COURSE_FORM', $renderer -> toArray());
                $smarty -> assign("T_USER_PROGRESS", $userStats);
            } else if (isset($_GET['issue_certificate']) && in_array($_GET['issue_certificate'], array_keys($users))) {
                try {
                    $certificate = $currentCourse -> prepareCertificate($_GET['issue_certificate']);
                    $currentCourse -> issueCertificate($_GET['issue_certificate'], $certificate);
                    header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$_GET['course'].'&op=course_certificates&message='.urlencode(_STUDENTSTATUSCHANGED).'&message_type=success');
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _PROBLEMISSUINGCERTIFICATE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            } else if (isset($_GET['revoke_certificate']) && in_array($_GET['revoke_certificate'], array_keys($users))) {
                try {
                    $currentCourse -> revokeCertificate($_GET['revoke_certificate']);
                    header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$currentCourse -> course['id'].'&op=course_certificates&message='.urlencode(_CERTIFICATEREVOKED).'&message_type=success');
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _PROBLEMREVOKINGCERTIFICATE.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            } else if (isset($_GET['auto_complete'])) {
                if ($currentCourse -> course['auto_complete']) {
                    $currentCourse -> course['auto_complete']    = 0;
                    $currentCourse -> course['auto_certificate'] = 0;
                } else {
                    $currentCourse -> course['auto_complete'] = 1;
                }
                $currentCourse -> persist();
            } else if (isset($_GET['auto_certificate'])) {
                if ($currentCourse -> course['auto_certificate']) {
                    $currentCourse -> course['auto_certificate'] = 0;
                } else {
                    $currentCourse -> course['auto_certificate'] = 1;
                }
                $currentCourse -> persist();
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
                foreach ($users as $key => $user) {
                    if ($user['user_type'] != 'student') {
                        unset($users[$key]);
                    }
                }

                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }
                $users = eF_multiSort($users, $sort, $order);
                $smarty -> assign("T_USERS_SIZE", sizeof($users));
                if (isset($_GET['filter'])) {
                    $users = eF_filterData($users, $_GET['filter']);
                }
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $users = array_slice($users, $offset, $limit);
                }
                foreach ($users as $key => $value) {
                    $users[$key]['issued_certificate'] = $value['issued_certificate'];
                }
                $smarty -> assign("T_USERS_PROGRESS", $users);
                $smarty -> display('professor.tpl');
                exit;
            }

            if (isset($_GET['export']) && $_GET['export'] == 'rtf') {
                $result = eF_getTableData("users_to_courses", "*", "users_LOGIN = '".$_GET['user']."' and courses_ID = '".$_GET['course']."' limit 1");
                if (sizeof($result) == 1 || isset($_GET['preview'])) {
                    $course = new EfrontCourse($_GET['course']);
                    if (!isset($_GET['preview'])){
                        $certificate_tpl_id = $course -> course['certificate_tpl_id'];
                        if ($certificate_tpl_id <= 0) {
                            $cfile = new EfrontFile(G_CERTIFICATETEMPLATEPATH."certificate1.rtf");
                        } else {
                            $cfile = new EfrontFile($certificate_tpl_id);
                        }
                        $template_data = file_get_contents($cfile['path']);
                        $issued_data   = unserialize($result[0]['issued_certificate']);
                        if (sizeof($issued_data) > 1){
                            $certificate   = $template_data;
                            $certificate   = str_replace("#organization#", utf8ToUnicode($issued_data['organization']), $certificate);
                            $certificate   = str_replace("#user_name#", utf8ToUnicode($issued_data['user_name']), $certificate);
                            $certificate   = str_replace("#user_surname#", utf8ToUnicode($issued_data['user_surname']), $certificate);
                            $certificate   = str_replace("#course_name#", utf8ToUnicode($issued_data['course_name']), $certificate);
                            $certificate   = str_replace("#grade#", utf8ToUnicode($issued_data['grade']), $certificate);
                            $certificate   = str_replace("#date#", utf8ToUnicode($issued_data['date']), $certificate);
                        }                        
                    }
                    else {
                        $certificateDirectory = G_CERTIFICATETEMPLATEPATH;
                        $selectedCertificate  = $_GET['certificate_tpl'];
                        $certificate          = file_get_contents($certificateDirectory.$selectedCertificate);
                    }   
                    $filename = "certificate_".$_GET['user'].".rtf";
                    header("Content-type: application/rtf");
                    header("Content-disposition: inline; filename=$filename");
                    header("Content-length: " . strlen($certificate));
                    echo $certificate;
                    exit(0);
                }
            }
        } else if ($_GET['op'] == 'format_certificate') {

            if ($currentCourse -> course['certificate_tpl_id'] > 0){
                $certificateFile = new EfrontFile($currentCourse -> course['certificate_tpl_id']);
                $dname = $certificateFile -> offsetGet('name');
            }

            try {
                $certificateFileSystemTree = new FileSystemTree(G_CERTIFICATETEMPLATEPATH);
                foreach (new EfrontFileTypeFilterIterator(new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($certificateFileSystemTree -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('rtf')) as $key => $value) {
                    $existingCertificates[basename($key)] = basename($key);
                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            }


            $form = new HTML_QuickForm("edit_course_certificate_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$currentCourse -> course['id'].'&op=format_certificate', "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('file', 'file_upload', _CERTIFICATETEMPLATE, 'class = "inputText"');
            $form -> addElement('select', 'existing_certificate', _ORSELECTONEFROMLIST, $existingCertificates, "id = 'select_certificate'");
            $form -> addElement('button', 'preview', _PREVIEW,
            'onclick = "javascript:window.open(\''.basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$currentCourse -> course['id'].'&op=course_certificates&export=rtf&preview=1&certificate_tpl=\'+document.forms[0].existing_certificate.value)"
            title = "'._VIEWCERTIFICATE.'"');
            $form -> addElement('submit', 'submit_certificate', _SAVE, 'class = "flatButton"');
            $form -> setDefaults(array('existing_certificate' => $dname));
            $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);

            if ($form -> isSubmitted() && $form -> validate()) {
                $certificateDirectory = G_CERTIFICATETEMPLATEPATH;
                if (!is_dir($certificateDirectory)) {
                    mkdir($certificateDirectory);
                }
                $logoid = 0;
                try {
                    if ($_FILES['file_upload']['size'] > 0) {
                        $filesystem   = new FileSystemTree($certificateDirectory);
                        $uploadedFile = $filesystem -> uploadFile('file_upload', $certificateDirectory);
                        $certificateid = $uploadedFile['id'];
                    } else {
                        $selectedCertificate = $form -> exportValue('existing_certificate');
                        $certificateFile = new EfrontFile(G_CERTIFICATETEMPLATEPATH.$selectedCertificate);
                        if ($certificateFile['id'] < 0) { //if the file doesn't exist, then import it 
                            $selectedCertificate = $certificateFileSystemTree -> seekNode(G_CERTIFICATETEMPLATEPATH.$selectedCertificate);    
                            $newList             = FileSystemTree :: importFiles($selectedCertificate['path']);
                            $certificateid       = key($newList);
                        }
                        else {
                            $certificateid = $certificateFile['id'];
                        }
                    }
                    $currentCourse -> course['certificate_tpl_id'] = $certificateid;
                    $currentCourse -> persist();
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=lessons&course=".$currentCourse -> course['id']."&op=course_certificates&message=".urlencode(_SUCCESFULLYUPDATEDCERTIFICATE)."&message_type=success");
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

            $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
            $form -> setRequiredNote(_REQUIREDNOTE);
            $form -> accept($renderer);
            $smarty -> assign('T_CERTIFICATE_FORM', $renderer -> toArray());
        } else if ($_GET['op'] == 'course_rules') {
            $courseLessons = $currentCourse -> getLessons();

            $rules_form = new HTML_QuickForm("course_rules_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=lessons&course=".$currentCourse -> course['id']."&op=course_rules", "", null, true);
            $rules_form -> addElement('submit', 'submit_rule', _SUBMIT, 'class = "flatButton"');
            if ($rules_form -> isSubmitted() && $rules_form -> validate()) {
                foreach ($_POST['rules'] as $rule_lesson) {
                    if (sizeof(array_unique($rule_lesson['lesson'])) != sizeof($rule_lesson['lesson'])) {
                        $duplicate = true;
                    }
                }
                if (!isset($duplicate)) {
                    try {
                        $currentCourse -> rules = $_POST['rules'];
                        $currentCourse -> persist();
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=lessons&course=".$currentCourse -> course['id']."&op=course_rules&message=".urlencode(_SUCCESFULLYSETORDER)."&message_type=success");
                    } catch (Exception $e) {
                        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                        $message      = _PROBLEMSETTINGORDER.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                        $message_type = 'failure';
                    }
                } else {
                    $message      = _DUPLICATESARENOTALLOWED;
                    $message_type = 'failure';
                }
            }
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

            $rules_form -> accept($renderer);
            $smarty -> assign('T_COURSE_RULES_FORM', $renderer -> toArray());
            $smarty -> assign("T_COURSE_RULES", $currentCourse -> rules);
            $smarty -> assign('T_COURSE_LESSONS', $courseLessons);

            $smarty -> assign('T_COURSE', $currentCourse -> course);
            $smarty -> assign('T_COURSE_LESSONS', $courseLessons);
        } else if ($_GET['op'] == 'course_order') {
            $loadScripts[] = 'drag-drop-folder-tree';
            $courseLessons = $currentCourse -> getLessons();

            $smarty -> assign('T_COURSE', $currentCourse -> course);
            $smarty -> assign('T_COURSE_LESSONS', $courseLessons);

            if (isset($_GET['ajax']) && isset($_GET['order'])) {
                $order    = explode(",", $_GET['order']);
                $previous = 0;
                foreach ($order as $value) {
                    $result = explode("-", $value);
                    if (in_array($value, array_keys($courseLessons))) {
                        eF_updateTableData("lessons_to_courses", array("previous_lessons_ID" => $previous), "courses_ID=".$currentCourse -> course['id']." and lessons_ID=".$result[0]);
                    }
                    $previous = $result[0];
                }
                echo _TREESAVEDSUCCESSFULLY;
                exit;
            }
        } else if ($_GET['op'] == 'course_scheduling') {
            $courseLessons = $currentCourse -> getLessons();
            if (isset($_GET['set_schedule']) && in_array($_GET['set_schedule'], array_keys($courseLessons))) {
                try {
                    $lesson        = new EfrontLesson($_GET['set_schedule']);
                    $fromTimestamp = mktime($_GET['from_Hour'], $_GET['from_Minute'], 0, $_GET['from_Month'], $_GET['from_Day'], $_GET['from_Year']);
                    $toTimestamp   = mktime($_GET['to_Hour'],   $_GET['to_Minute'],   0, $_GET['to_Month'],   $_GET['to_Day'],   $_GET['to_Year']);
                    if ($fromTimestamp < $toTimestamp) {
                        $lesson -> lesson['from_timestamp'] = $fromTimestamp;
                        $lesson -> lesson['to_timestamp']   = $toTimestamp;
//                        $lesson -> lesson['shift']          = $form -> exportValue('shift') ? 1 : 0;

                        $lesson -> persist();
                        echo _FROM.' '.formatTimestamp($fromTimestamp, 'time_nosec').' '._TO.' '.formatTimestamp($toTimestamp, 'time_nosec').'&nbsp;';
                    } else {
                        header("HTTP/1.0 500");
                        echo _ENDDATEMUSTBEBEFORESTARTDATE;
                    }
                } catch (Exception $e) {
                    header("HTTP/1.0 500");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }

                exit;
            } else if (isset($_GET['delete_schedule']) && in_array($_GET['delete_schedule'], array_keys($courseLessons))) {
                try {
                    $lesson = new EfrontLesson($_GET['delete_schedule']);
                    $lesson -> lesson['from_timestamp'] = '';
                    $lesson -> lesson['to_timestamp']   = '';
                    $lesson -> lesson['shift']          = 0;

                    $lesson -> persist();
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }

            $smarty -> assign("T_COURSE_LESSONS", $courseLessons);
            //pr($courseLessons);
        }
    } else {       
        $directionsTree = new EfrontDirectionsTree();
        if (isset($_GET['directory'])) {
            $options        = array('link' => basename($_SERVER['PHP_SELF']).'?ctg=lessons&directory=1&lessons_ID=');
            $smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, false, false, false, $options)); 
        } else {
            $userLessons        = $currentUser -> getLessons(true);
            $userLessonProgress = EfrontStats :: getUsersLessonStatus($userLessons, $currentUser -> user['login']);
            $userCourses        = $currentUser -> getCourses(true);
            $userCourseProgress = EfrontStats :: getUsersCourseStatus($userCourses, $currentUser -> user['login']);            
            /*Assign progress in a per-lesson fashion*/
            $temp = array();
            foreach ($userLessonProgress as $lessonId => $user) {
                $temp[$lessonId] = $user[$currentUser -> user['login']];
            }
            $userProgress['lessons'] = $temp;
            /*Assign progress in a per-course fashion*/
            $temp = array();
            foreach ($userCourseProgress as $courseId => $user) {
                $temp[$courseId] = $user[$currentUser -> user['login']];
            }
            $userProgress['courses'] = $temp;            
            
            $options      = array('lessons_link' => '#user_type#.php?lessons_ID=',
                                  'courses_link' => false);

            if (sizeof ($userLessons) > 0 || sizeof($userCourses) > 0) {
                //$smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, $userLessons, $userCourses));
                $smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, $userLessons, $userCourses, $userProgress, $options));
            }
        }        
    }

    /*
    $directionsTree = new EfrontDirectionsTree();
    $userLessons = $currentUser -> getNonLessons(true);
    $userCourses = $currentUser -> getNonCourses(true);
    $smarty -> assign("T_DIRECTIONS_TREE2", $directionsTree -> toHTML(false, $userLessons, $userCourses));
*/
    //$directionsTree = new EfrontDirectionsTree();
    //echo $directionsTree -> toHTML();

}
/*
The personal page is used to display the professor's personal information
and provides the means to edit this information
*/
elseif ($ctg == 'evaluations' && MODULE_HCD_INTERFACE) {
    /**This part is used to display the evaluations that have been written for the employee*/

    // Administrators and supervisors will see all evaluations for the employee while employee-professors will see only their own
    if ($_SESSION['s_type'] == "administrator" || $_SESSION['employee_type'] == _SUPERVISOR) {
        $evaluations = eF_getTableData("module_hcd_events", "*", "users_login = '".$_GET['user']."' AND event_code >=10","timestamp");
        if(!empty($evaluations)) {
            $smarty -> assign("T_EVALUATION", $evaluations);
        }
    } else if ($_SESSION['s_type'] == "professor") {
        $evaluations = eF_getTableData("module_hcd_events", "*", "users_login = '".$_GET['user']."' AND author = '".$_SESSION['s_login']."' AND event_code >=10","timestamp");
        if(!empty($evaluations)) {
            $smarty -> assign("T_EVALUATION", $evaluations);
        }
    }

}
/*
The personal page is used to display the professor's personal information
and provides the means to edit this information
*/
elseif ($ctg == 'personal') {
    /**This part is used to display the user's personal information*/
    include "module_personal.php";
}
/*
At this point, we apply module functionality
*/
elseif (sizeof($modules) > 0 && in_array($ctg, array_keys($module_ctgs))) {
    $module_mandatory = eF_getTableData("modules", "mandatory", "name = '".$ctg."'");
    if ($module_mandatory[0]['mandatory'] != 'false' || isset($currentLesson -> options[$ctg])) {
        include(G_MODULESPATH.$ctg.'/module.php');
        $smarty -> assign("T_CTG_MODULE", $module_ctgs[$ctg]);
    }
}

$fields_log = array ('users_LOGIN' => $_SESSION['s_login'],                                 //This is the log entry array
                     'timestamp'   => time(),
                     'action'      => 'lastmove',
                     'comments'    => 0,
                     'session_ip'  => eF_encodeIP($_SERVER['REMOTE_ADDR']));

eF_deleteTableData("logs", "users_LOGIN='".$_SESSION['s_login']."' AND action='lastmove'"); //Only one lastmove action interests us, so delete any other

eF_insertTableData("logs", $fields_log);

$smarty -> assign("T_HEADER_EDITOR", $load_editor);                                         //Specify whether we need to load the editor

if (isset($_GET['refresh'])) {
    $smarty -> assign("T_REFRESH_SIDE","true");
}


///MODULES5
$smarty -> assign("T_MODULE_CSS", $module_css_array);
$smarty -> assign("T_MODULE_JS", $module_js_array);
foreach ($loadedModules as $module) {
    $loadScripts = array_merge($loadScripts, $module -> addScripts());
}

if ($message) {
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
}

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));                    //array_unique, so it doesn't send duplicate entries

$smarty -> assign("T_CURRENT_CTG", $ctg);
$smarty -> assign("T_MENUCTG", $ctg);
$smarty -> assign("T_MENU", eF_getMenu());

$smarty -> assign("T_QUERIES", $numberOfQueries);

$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_SEARCH_MESSAGE", $search_message);

$smarty -> assign("T_CONFIGURATION", $configuration);       //Assign global configuration values to smarty
$smarty -> assign("T_CURRENT_USER", $currentUser);
$smarty -> assign("T_CURRENT_LESSON", $currentLesson);

//$smarty -> load_filter('output', 'eF_template_formatTimestamp');
//$smarty -> load_filter('output', 'eF_template_formatLogins');

$debug_timeBeforeSmarty = microtime(true) - $debug_TimeStart;
$smarty -> load_filter('output', 'eF_template_setInnerLinks');
$smarty -> display('professor.tpl');
$debug_timeAfterSmarty = microtime(true) - $debug_TimeStart;

$debug_TotalTime = microtime(true) - $debug_TimeStart;

if (G_DEBUG) {
    echo "
    <div onclick = 'this.style.display=\"none\"' style = 'position:absolute;top:0px;right:0px;background-color:lightblue;border:1px solid black' >
    <table>
        <tr><th colspan = '100%'>Benchmarking info (click to remove)</th></tr>
        <tr><td>Initialization time: </td><td>".round($debug_InitTime, 5)." sec</td></tr>
        <tr><td>Time up to smarty: </td><td>".round($debug_timeBeforeSmarty, 5)." sec</td></tr>
        <tr><td>Database time (".$databaseQueries." q): </td><td>".($databaseTime > 100 ? 0 : round($databaseTime, 5))." sec</td></tr>
        <tr><td>Smarty overhead: </td><td>".round($debug_timeAfterSmarty - $debug_timeBeforeSmarty, 5)." sec</td></tr>
        <tr><td colspan = \"2\" class = \"horizontalSeparator\"></td></tr>
        <tr><td>Total execution time: </td><td>".round($debug_TotalTime, 5)." sec</td></tr>
        <tr><td>Execution time for this script is: </td><td>".round($debug_TotalTime - $debug_InitTime - ($debug_timeAfterSmarty - $debug_timeBeforeSmarty), 5)." sec</td></tr>
    </table>
    </div>";
}
?>