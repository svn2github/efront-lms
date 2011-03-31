<?php
//pr($currentUser -> coreAccess);
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
if (isset($currentUser -> coreAccess['themes']) && $currentUser -> coreAccess['themes'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}

!isset($currentUser -> coreAccess['themes']) || $currentUser -> coreAccess['themes'] == 'change' ? $_change_ = 1 : $_change_ = 0;
$smarty -> assign("_change_", $_change_);

$loadScripts[] = 'scriptaculous/dragdrop';
$loadScripts[] = 'includes/themes';
$loadScripts[] = 'includes/entity';


$themeSettingsTools = array(array('text' => _APPEARANCE, 'image' => "16x16/layout.png", 'href' => basename($_SERVER['PHP_SELF']).'?ctg=system_config&op=appearance'));
$smarty -> assign ("T_APPEARANCE_LINK", $themeSettingsTools);

try {
    try {
        $currentSetTheme = new themes($GLOBALS['configuration']['theme']);
    } catch (Exception $e) {
        //EfrontConfiguration :: setValue('theme', 1);
        $currentSetTheme = new themes('default');
    }

    !isset($currentUser -> coreAccess['themes']) || $currentUser -> coreAccess['themes'] == 'change' ? $_change_ = 1 : $_change_ = 0;
    $smarty -> assign("_change_", $_change_);

    $themes = themes :: getAll("themes");
 $allBrowsers = themes :: $browsers;
    $usedBrowsers = array();

    foreach ($themes as $value) {
        $themeNames[] = $value['name'];
    }

    $filesystem = new FileSystemTree(G_THEMESPATH, true);
    foreach (new EfrontDirectoryOnlyFilterIterator(new ArrayIterator($filesystem -> tree)) as $key => $value) {
        //Automatically import themes that don't have an equivalent database representation
        if (!in_array($value['name'], $themeNames)) {
            try {
                $file = new EfrontFile($value['path']."/theme.xml");
                $xmlValues = themes :: parseFile($file);
                $newTheme = themes :: create($xmlValues);
                //$themes[$newTheme -> themes['id']] = $newTheme -> themes;
            } catch (Exception $e) {/*Don't halt for themes that can't be processed*/}
        }
    }
    $themes = themes :: getAll("themes");

    foreach ($themes as $value) {
        $themeNames[] = $value['name'];
        //$browserThemes[$value['id']] = $value['options']['browsers'];
     foreach ($allBrowsers as $browser => $foo) {
         if (isset($value['options']['browsers'][$browser])) {
             $usedBrowsers[$browser] = $value['id'];
             unset($allBrowsers[$browser]);
         }
     }
    }
    foreach ($allBrowsers as $key => $foo) {
        $currentSetTheme -> options['browsers'][$key] = 1;
        $themes[$currentSetTheme -> themes['id']]['options']['browsers'][$key] = 1;
    }

    $legalValues = array_merge(array_keys($themes), $themeNames);

    if (!isset($_GET['theme'])) {
        $_GET['theme'] = $currentSetTheme -> {$currentSetTheme -> entity}['id'];
    }
    $smarty -> assign("T_LAYOUT_SETTINGS", $currentSetTheme);

    if ((!isset($currentSetTheme -> remote) || !$currentSetTheme -> remote) && is_dir(G_EXTERNALPATH)) {
        /********** CMS / External pages from here over ************/
        $default_page = $GLOBALS['configuration']['cms_page'];
        $filesystem = new FileSystemTree(G_EXTERNALPATH);
        $pages = array();
        foreach (new EfrontFileTypeFilterIterator(new ArrayIterator($filesystem -> tree), array('php')) as $key => $value) {
            $pages[] = basename($key, '.php');
        }
        $smarty -> assign('T_CMS_PAGES', $pages);
        $smarty -> assign('T_DEFAULT_PAGE', $default_page);

        try {
            if ($_change_ && isset($_GET['delete']) && in_array($_GET['delete'], $pages)) {
                if (unlink (G_EXTERNALPATH."".$_GET['delete'].".php")) {
                    if ($GLOBALS['configuration']['page'] == $_GET['delete']) {
                        EfrontConfiguration :: setValue("cms_page", "");
                    }
                    exit;
                } else {
                    throw new Exception(_PAGECOULDNOTBEDELETED);
                }
            } elseif ($_change_ && isset($_GET['use_none'])) {
                EfrontConfiguration :: setValue("cms_page", "");
                exit;
            } elseif ($_change_ && isset($_GET['set_page'])) {
                if (!in_array($_GET['set_page'], $pages)) {
                    throw new Exception (_INVALIDPAGE);
                }
                EfrontConfiguration :: setValue('cms_page', $_GET['set_page']);
                echo 1;//means a page was set, used for putting the green pin.
                exit;
            }
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
    }
    /************ Change logo/favicon part *************/
    if (isset($currentSetTheme -> remote) && $currentSetTheme -> remote) {
        $smarty -> assign ("T_REMOTE_THEME", 1);
    } else {
    }


    /*Layout part from here over*/
    if (isset($_GET['theme_layout']) && in_array($_GET['theme_layout'], $legalValues)) {
        $layoutTheme = new themes($_GET['theme_layout']);
    } else {
        $layoutTheme = $currentSetTheme;
    }
    $smarty -> assign("T_LAYOUT_THEME", $layoutTheme);
    isset($layoutTheme -> layout['custom_blocks']) && is_array($layoutTheme -> layout['custom_blocks']) ? $customBlocks = $layoutTheme -> layout['custom_blocks'] : $customBlocks = array();

    if (isset($_GET['add_block']) || isset($_GET['edit_header']) || isset($_GET['edit_footer']) || (isset($_GET['edit_block']) && in_array($_GET['edit_block'], array_keys($customBlocks)))) {
        //$basedir = G_EXTERNALPATH;
  $basedir = G_THEMESPATH.$layoutTheme -> themes['path'].'external/';
        try {
            if (!is_dir($basedir) && !mkdir($basedir, 0755)) {
                throw new EfrontFileException(_COULDNOTCREATEDIRECTORY.': '.$fullPath, EfrontFileException :: CANNOT_CREATE_DIR);
            }
            $smarty -> assign("T_EDITOR_PATH", $basedir); //This is used for the browse.php method to know where to look

            $filesystem = new FileSystemTree($basedir);
            $filesystem -> handleAjaxActions($currentUser);

            if (isset($_GET['edit_block'])) {
                $url = basename($_SERVER['PHP_SELF']).'?ctg=themes&theme='.$layoutTheme -> {$layoutTheme -> entity}['id'].'&edit_block='.$_GET['edit_block'];
            } else {
                $url = basename($_SERVER['PHP_SELF']).'?ctg=themes&theme='.$layoutTheme -> {$layoutTheme -> entity}['id'].'&add_block=1';
            }
            $options = array('share' => false);
            $extraFileTools = array(array('image' => 'images/16x16/arrow_right.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
            include "file_manager.php";

        } catch (Exception $e) {
         handleNormalFlowExceptions($e);
        }


        //These are the entities that will be automatically replaced in custom header/footer
        $systemEntities = array('logo.png',
            '#siteName',
           '#siteMoto',
           '#languages',
           '#path',
            '#version');
        $smarty -> assign("T_SYSTEM_ENTITIES", $systemEntities);
        //And these are the replacements of the above entities
        $systemEntitiesReplacements = array('{$T_LOGO}',
                    '{$T_CONFIGURATION.site_name}',
           '{$T_CONFIGURATION.site_motto}',
           '{$smarty.capture.header_language_code}',
           '{$title}',
                 '{$smarty.const.G_VERSION_NUM}');

        $load_editor = true;

        $layout_form = new HTML_QuickForm("add_block_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=themes&theme=".$layoutTheme -> {$layoutTheme -> entity}['id'].(isset($_GET['edit_block']) ? '&edit_block='.$_GET['edit_block'] : '&add_block=1').(isset($_GET['theme_layout']) ? '&theme_layout='.$_GET['theme_layout'] : ''), "", null, true);
        $layout_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');

        $layout_form -> addElement('text', 'title', _BLOCKTITLE, 'class = "inputText"');
        $layout_form -> addElement('textarea', 'content', _BLOCKCONTENT, 'id="editor_data" class = "mceEditor" style = "width:100%;height:300px;"');
        $layout_form -> addElement('submit', 'submit_block',_SAVE, 'class = "flatButton"');
        $layout_form -> addRule('title', _THEFIELD.' "'._BLOCKTITLE.'" '._ISMANDATORY, 'required', null, 'client');

        if (isset($_GET['edit_block'])) {
            $customBlocks[$_GET['edit_block']]['content'] = file_get_contents($basedir.$customBlocks[$_GET['edit_block']]['name'].'.tpl');
            $layout_form -> setDefaults($customBlocks[$_GET['edit_block']]);
            $layout_form -> freeze(array('name'));
        }

        if ($layout_form -> isSubmitted() && $layout_form -> validate()) {
            $values = $layout_form -> exportValues();
            if (isset($_GET['edit_block'])) { // not rename blocks by editing. It created many unused files
             $values['name'] = $customBlocks[$_GET['edit_block']]['name'];
            } else {
             $values['name'] = time(); //Use the timestamp as name
            }
            $block = array('name' => $values['name'],
                     'title' => $values['title']);
            file_put_contents($basedir.$values['name'].'.tpl', $values['content']);

            if (isset($_GET['edit_block'])) {
                $customBlocks[$_GET['edit_block']] = $block;
            } else {
                sizeof($customBlocks) > 0 ? $customBlocks[] = $block : $customBlocks = array($block);
            }
            $layoutTheme -> layout['custom_blocks'] = $customBlocks;
            $layoutTheme -> persist();

            eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=themes&theme=".$layoutTheme -> {$layoutTheme -> entity}['id'].(isset($_GET['theme_layout']) ? '&theme_layout='.$_GET['theme_layout'] : ''));
        }

        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $layout_form -> accept($renderer);
        $smarty -> assign('T_ADD_BLOCK_FORM', $renderer -> toArray());

    } else {
        $form = new HTML_QuickForm("import_settings_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=themes&theme='.$layoutTheme -> {$layoutTheme -> entity}['id'].(isset($_GET['theme_layout']) ? '&theme_layout='.$_GET['theme_layout'] : ''), "", null, true);

        $form -> addElement('file', 'file_upload', _SETTINGSFILE, 'class = "inputText"'); //Lesson file
        $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB
        $form -> addElement('submit', 'submit_import', _SUBMIT, 'class = "flatButton"');

        $smarty -> assign("T_MAX_FILESIZE", FileSystemTree :: getUploadMaxSize());

        if ($form -> isSubmitted() && $form -> validate()) {
            try {
                $values = $form -> exportValues();
                $basedir = G_THEMESPATH.$layoutTheme -> themes['path'].'external/';
                $filesystem = new FileSystemTree($basedir);
                $uploadedFile = $filesystem -> uploadFile('file_upload', $basedir);
                $uploadedFile -> uncompress();
                $uploadedFile -> delete();

                $settings = file_get_contents($basedir.'layout_settings.php.inc');
                if ($settings = unserialize($settings)) {
                    $layoutTheme -> layout = $settings;
                    $layoutTheme -> persist();
                }

                eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=themes&theme=".$layoutTheme -> {$layoutTheme -> entity}['id'].(isset($_GET['theme_layout']) ? '&theme_layout='.$_GET['theme_layout'] : '')."&message=".rawurlencode(_SETTINGSIMPORTEDSUCCESFULLY)."&message_type=success");
                //$message      = _SETTINGSIMPORTEDSUCCESFULLY;
                //$message_type = 'success';
            } catch (Exception $e) {
             handleNormalFlowExceptions($e);
            }
        }

        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $smarty -> assign('T_IMPORT_SETTINGS_FORM', $renderer -> toArray());


        $blocks = array('login' => _LOGINENTRANCE,
                        'online' => _USERSONLINE,
                        'lessons' => _LESSONS,
                        'selectedLessons' => _SELECTEDLESSONS,
                        'news' => _SYSTEMNEWS,
                        'links' => _MENU,
            'checker' => _OPTIONSCHECKER);

        foreach ($customBlocks as $key => $block) {
            $blocks[$key] = htmlspecialchars($block['title'], ENT_QUOTES);
        }
        $smarty -> assign("T_BLOCKS", json_encode($blocks));
        $currentPositions = $layoutTheme -> layout['positions'] ? $layoutTheme -> layout['positions'] : false;
        $smarty -> assign("T_POSITIONS", json_encode($currentPositions));
//pr($layoutTheme);
//pr($layoutTheme -> layout);
        try {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'set_layout') {

                parse_str($_POST['leftList']);
                parse_str($_POST['centerList']);
                parse_str($_POST['rightList']);
                mb_internal_encoding('utf-8'); //This must be put here due to PHP bug #48697

                !isset($leftList) ? $leftList = array() : null;
                !isset($centerList) ? $centerList = array() : null;
                !isset($rightList) ? $rightList = array() : null;

                array_pop($leftList);array_pop($rightList);array_pop($centerList); //Remove emmpty values, that are the 'bogus' li element

                $layoutTheme -> layout['positions']['leftList'] = $leftList;
                $layoutTheme -> layout['positions']['centerList'] = $centerList;
                $layoutTheme -> layout['positions']['rightList'] = $rightList;
                $layoutTheme -> layout['positions']['layout'] = $_POST['layout'];

                $layoutTheme -> persist();

                echo "set";
                exit;
            } else if (isset($_GET['ajax']) && $_GET['ajax'] == 'reset_layout') {
                $layoutTheme -> applySettings('layout');
                echo "reset";
                exit;
            } else if (isset($_GET['delete_block'])) {
                //Remove the block's file
                if (is_file($file = G_EXTERNALPATH.$customBlocks[$_GET['delete_block']]['name'].'.tpl')) {
                 $file = new EfrontFile($file);
                 $file -> delete();
                }

                //Remove the block from the custom blocks list
                unset($customBlocks[$_GET['delete_block']]);
                $layoutTheme -> layout['custom_blocks'] = $customBlocks;

                //Remove the deleted block from any position it may occupy
                foreach ($layoutTheme -> layout['positions'] as $key => $value) {
                 if (is_array($value) && ($offset = array_search($_GET['delete_block'], $value)) !== false) {
                     array_splice($layoutTheme -> layout['positions'][$key], $offset, 1);
                 }
                }

                $layoutTheme -> persist();
                exit;
            } else if (isset($_GET['toggle_block'])) {
                if (isset($layoutTheme -> layout['positions']['enabled'][$_GET['toggle_block']])) {
                   unset($layoutTheme -> layout['positions']['enabled'][$_GET['toggle_block']]);
                   echo json_encode(array('enabled' => false));
                } else {
                    $layoutTheme -> layout['positions']['enabled'][$_GET['toggle_block']] = true;
                    echo json_encode(array('enabled' => true));
                }
                //pr($layoutTheme -> layout['positions']);
                $layoutTheme -> persist();
                //pr($_GET['toggle_block']);
                exit;
            } else if (isset($_GET['export_layout'])) {
                file_put_contents(G_THEMESPATH.$layoutTheme -> themes['path'].'external/layout_settings.php.inc', serialize($layoutTheme -> layout));
                $directory = new EfrontDirectory(G_THEMESPATH.$layoutTheme -> themes['path'].'external/');
                $tempDir = $currentUser -> getDirectory().'temp/';
                if (!is_dir($tempDir) && !mkdir($tempDir, 0755)) {
                    throw new EfrontFileException(_COULDNOTCREATEDIRECTORY.': '.$tempDir, EfrontFileException :: CANNOT_CREATE_DIR);
                }
                //pr($tempDir.'layout.zip');debug();
                $file = $directory -> compress(false, false);
                $file -> rename($tempDir.$layoutTheme -> {$layoutTheme -> entity}['name'].'_layout.zip', true);

                echo json_encode(array('file' => $file['path']));
                exit;
            }
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
    }

    //Themes list and add/edit/delete operations
    $smarty -> assign("T_THEMES", $themes);
    $smarty -> assign("T_CURRENT_THEME", $currentSetTheme);
    $smarty -> assign("T_MAX_FILESIZE", FileSystemTree :: getUploadMaxSize());
    $smarty -> assign("T_BROWSERS", themes :: $browsers);

    $entityName = 'themes';
    require("entity.php");

    if (isset($_GET['set_browser']) && in_array($_GET['set_browser'], $legalValues) && isset($_GET['browser']) && in_array($_GET['browser'], array_keys(themes :: $browsers))) {
        try {
         unset($_SESSION['s_theme']);
            $theme = new themes($_GET['set_browser']);
            foreach ($themes as $key => $value) {
                $value = new themes($value['id']);
                unset($value -> options['browsers'][$_GET['browser']]);
                $value -> persist();
            }
            $theme -> options['browsers'][$_GET['browser']] = 1;
            $theme -> persist();
            $url = '';
            if (detectBrowser() == $_GET['browser']) {
             if ($theme -> options['sidebar_interface'] > 0) {
              $url = basename($_SERVER['PHP_SELF']).'?ctg=themes&tab=set_theme';
             } else {
                 $url = basename($_SERVER['PHP_SELF'], '.php').'page.php?ctg=themes&tab=set_theme';
             }
            }
            echo json_encode(array('status' => 1, 'browser' => $_GET['browser'], 'url' => $url));

        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        exit;
    }
    if (isset($_GET['set_theme']) && in_array($_GET['set_theme'], $legalValues)) {
        try {
         unset($_SESSION['s_theme']);
            $cacheTree = new FileSystemTree(G_THEMECACHE, true);
            foreach (new EfrontDirectoryOnlyFilterIterator($cacheTree -> tree) as $value) {
                $value -> delete();
            }
            EfrontConfiguration::setValue('theme', $_GET['set_theme']);
            foreach ($themes as $key => $value) {
                $value = new themes($value['id']);
                unset($value -> options['browsers']);
                $value -> persist();
            }
            $theme = new themes($_GET['set_theme']);
            if ($theme -> options['sidebar_interface'] > 0) {
                echo basename($_SERVER['PHP_SELF']).'?ctg=themes&tab=set_theme';
            } else {
                echo basename($_SERVER['PHP_SELF'], '.php').'page.php?ctg=themes&tab=set_theme';
            }

            if (!isset($_GET['ajax'])) {
                eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=themes");
            }

        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        exit;
    }
    if (isset($_GET['reset_theme']) && $_GET['reset_theme'] == $currentSetTheme -> {$currentSetTheme -> entity}['id']) {
        try {
            $currentSetTheme -> applySettings();
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        exit;
    }
    if (isset($_GET['export_theme']) && in_array($_GET['export_theme'], $legalValues)) {
        try {
            $theme = new themes($_GET['export_theme']);
            if ($theme -> options['locked']) {
                throw new EfrontThemesException(_THEMELOCKED, EfrontThemesException::THEME_LOCKED);
            }
            $file = $theme -> export();
            echo $file['path'];
            //$theme -> applySettings();
            //EfrontConfiguration::setValue('theme', $_GET['set_theme']);
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        exit;
    }
} catch (Exception $e) {
 handleNormalFlowExceptions($e);
}
