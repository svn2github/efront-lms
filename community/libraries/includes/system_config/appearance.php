<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$themeSettingsTools = array(array('text' => _THEMES, 'image' => "16x16/layout.png", 'href' => basename($_SERVER['PHP_SELF']).'?ctg=themes'));
$smarty -> assign ("T_THEMES_LINK", $themeSettingsTools);
$loginRedirectArray = array();
foreach ($loadedModules as $module) {
 $className = $module -> className;
 if (isset($loadedModules[$className]) && $loadedModules[$className] -> getLandingPageSmartyTpl()) {
  $loginRedirectArray[$className] = $className;
 }
}
$loginRedirectArray['lesson_catalog'] = _LESSONSCATALOG;
$loginRedirectArray['user_dashboard'] = _USERDASHBOARD;
//Added in case current module is inactive or not loaded for administrator
if (array_key_exists($GLOBALS['configuration']['login_redirect_page'], $loginRedirectArray) === false) {
 $loginRedirectArray[$GLOBALS['configuration']['login_redirect_page']] = $GLOBALS['configuration']['login_redirect_page'];
}

$appearanceMainForm = new Html_QuickForm("appearance_main_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=appearance&tab=main", "", null, true);
$appearanceMainForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$appearanceMainForm -> addElement("advcheckbox", "show_footer", _SHOWFOOTER, null, 'class = "inputCheckBox"', array(0, 1));
$appearanceMainForm -> addElement("textarea", "additional_footer", _EDITFOOTER, 'style = "height:100px;width:500px;"');
$appearanceMainForm -> addElement("text", "site_name", _SITENAME, 'class = "inputText"');
$appearanceMainForm -> addElement("text", "site_motto", _SITEMOTO, 'class = "inputText"');
$appearanceMainForm -> addElement("advcheckbox", "motto_on_header", _SHOWMOTTOONHEADER, null, 'class = "inputCheckBox"', array(0,1));
$appearanceMainForm -> addElement("select", "collapse_catalog", _COLLAPSECATALOG, array(_NO, _YES), 'class = "inputCheckBox"');
$appearanceMainForm -> addElement("advcheckbox", "display_empty_blocks", _SHOWEMPTYBLOCKS, null, 'class = "inputCheckBox"', array(0,1));
$appearanceMainForm -> addElement("select", "lessons_directory", _VIEWDIRECTORY, array(_NO, _YES, _YESAFTERLOGIN), 'class = "inputSelect"');
$appearanceMainForm -> addElement("select", "login_redirect_page", _LOGINREDIRECTPAGE, $loginRedirectArray, 'class = "inputCheckBox"');
$appearanceMainForm -> addElement("text", "logout_redirect", _LOGOUTREDIRECT, 'class = "inputText"');
$appearanceMainForm -> addElement("advcheckbox", "load_videojs", _LOADVIDEOJS, null, 'class = "inputCheckBox"', array(0,1));

$appearanceMainForm -> setDefaults($GLOBALS['configuration']);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $appearanceMainForm -> freeze();
} else {
 $appearanceMainForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($appearanceMainForm -> isSubmitted() && $appearanceMainForm -> validate()) { //If the form is submitted and validated
  $values = $appearanceMainForm -> exportValues();
  unset($values['submit']);
  foreach ($values as $key => $value) {
   $result = EfrontConfiguration :: setValue($key, $value);
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=appearance&tab=main&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_APPEARANCE_MAIN_FORM", $appearanceMainForm -> toArray());

$appearanceLogoForm = new Html_QuickForm("appearance_logo_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=appearance&tab=logo", "", null, true);
$appearanceLogoForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
try {
 $file = new EfrontFile($configuration['site_logo']);
 $appearanceLogoForm -> addElement("static", "formelement", '<img src = "images/logo/'.$file['name'].'" alt = "'._SITELOGO.'" title = "'._SITELOGO.'"/>');
} catch (Exception $e) {}
$appearanceLogoForm -> addElement('file', 'site_logo', _UPLOADSITELOGO);
$appearanceLogoForm -> addElement("static", "", _EACHFILESIZEMUSTBESMALLERTHAN.' <b>'.FileSystemTree::getUploadMaxSize().'</b> '._KB);
//Don't show normalization if GD isn't set.
if (extension_loaded('gd') || extension_loaded('gd2')) {
 $smarty -> assign("T_GD_LOADED", true);
 $appearanceLogoForm -> addElement("text", "logo_max_width", _LOGOWIDTH, 'size = "5"');
 $appearanceLogoForm -> addElement("text", "logo_max_height", _LOGOHEIGHT, 'size = "5"');
 $appearanceLogoForm -> addRule('logo_max_width', _LOGODIMENSIONSMUSTBEPOSITIVE , 'callback', create_function('$a', 'return ($a > 0);'));
 $appearanceLogoForm -> addRule('logo_max_height', _LOGODIMENSIONSMUSTBEPOSITIVE , 'callback', create_function('$a', 'return ($a > 0);'));
 $appearanceLogoForm -> addElement("advcheckbox", "normalize_dimensions", _NORMALIZEDIMENSIONS, null, 'class = "inputCheckBox"', array(0, 1));
 $appearanceLogoForm -> setDefaults(array('normalize_dimensions' => 1));
}
$appearanceLogoForm -> addElement("select", "use_logo", _USELOGO, array(0 => _DEFAULTLOGO, 1 => _SITELOGO, 2 => _THEMELOGO));
$appearanceLogoForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize()*1024);

try {
 // Get current dimensions
 list($width, $height) = getimagesize($GLOBALS['logoFile']['path']);
 $appearanceLogoForm -> setDefaults(array('logo_max_width' => $width, 'logo_max_height' => $height));
} catch (EfrontFileException $e) {
 $appearanceLogoForm -> setDefaults(array('logo_max_width' => 200, 'logo_max_height' => 150));
}

$appearanceLogoForm -> setDefaults($GLOBALS['configuration']);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $appearanceLogoForm -> freeze();
} else {
 $appearanceLogoForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($appearanceLogoForm -> isSubmitted() && $appearanceLogoForm -> validate()) {
  try {
   $values = $appearanceLogoForm -> exportValues();
   unset($values['MAX_FILE_SIZE']);
   unset($values['submit']);
   unset($values['logo']); //This is set separately, otherwise settings replace its value
   foreach ($values as $key => $value) {
    $result = EfrontConfiguration :: setValue($key, $value);
   }

   if ($values['use_logo'] == 0) {
    EfrontConfiguration :: setValue('logo', '');
   } elseif ($values['use_logo'] == 1) {
    EfrontConfiguration :: setValue('logo', $GLOBALS['configuration']['site_logo']);
   }
   $logoDirectory = new EfrontDirectory(G_LOGOPATH);
   $filesystem = new FileSystemTree(G_LOGOPATH);
   try {
    $logoFile = $filesystem -> uploadFile('site_logo', $logoDirectory);
    if (strpos($logoFile['mime_type'], 'image') === false) {
     throw new EfrontFileException(_NOTANIMAGEFILE, EfrontFileException::NOT_APPROPRIATE_TYPE);
    }
    EfrontConfiguration :: setValue('site_logo', $logoFile['id']);
   } catch (EfrontFileException $e) {
    if ($e -> getCode() != UPLOAD_ERR_NO_FILE) {throw $e;} //Don't halt if no file was uploaded (errcode = 4). Otherwise, throw the exception
   }
   // Normalize avatar picture to the dimensions set in the System Configuration menu. NOTE: the picture will be modified to match existing settings. Future higher settings will be disregarded, while lower ones might affect the quality of the displayed image
   if ($values["normalize_dimensions"] == 1) {
    eF_normalizeImage(G_LOGOPATH . $logoFile['name'], $logoFile['extension'], $values["logo_max_width"], $values["logo_max_height"]);
   } else {
    list($width, $height) = getimagesize(G_LOGOPATH . $logoFile['name']);
    eF_createImage(G_LOGOPATH . $logoFile['name'], $logoFile['extension'], $width, $height, $values["logo_max_width"], $values["logo_max_height"]);
   }

   eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=appearance&tab=logo&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
  } catch (Exception $e) {
   handleNormalFlowExceptions($e);
  }
 }
}
$smarty -> assign("T_APPEARANCE_LOGO_FORM", $appearanceLogoForm -> toArray());

$appearanceFaviconForm = new Html_QuickForm("appearance_favicon_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=appearance&tab=favicon", "", null, true);
$appearanceFaviconForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$appearanceFaviconForm -> addElement('file', 'favicon', _FILENAME);
$appearanceFaviconForm -> addElement("static", "", _EACHFILESIZEMUSTBESMALLERTHAN.' <b>'.FileSystemTree::getUploadMaxSize().'</b> '._KB);
$appearanceFaviconForm -> addElement("advcheckbox", "default_favicon", _USEDEFAULTFAVICON, null, 'class = "inputCheckBox"  id = "set_default_favicon" onclick = "$(\'favicon_settings\').select(\'input\').each(function(s) {if (s.type != \'submit\' && s.id != \'set_default_favicon\') s.disabled ? s.disabled = \'\' : s.disabled = \'disabled\' })"', array(0, 1));
$appearanceFaviconForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize()*1024);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $appearanceFaviconForm -> freeze();
} else {
 $appearanceFaviconForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($appearanceFaviconForm -> isSubmitted() && $appearanceFaviconForm -> validate()) {
  try { //If the form is submitted and validated
   if ($appearanceFaviconForm -> exportValue('default_favicon')) {
    EfrontConfiguration :: setValue('favicon', '');
   } else {
    $faviconDirectory = new EfrontDirectory(G_LOGOPATH);
    $filesystem = new FileSystemTree(G_LOGOPATH);

    try {
     $faviconFile = $filesystem -> uploadFile('favicon', $logoDirectory);
     if (strpos($faviconFile['mime_type'], 'image') === false) {
      throw new EfrontFileException(_NOTANIMAGEFILE, EfrontFileException::NOT_APPROPRIATE_TYPE);
     }
     EfrontConfiguration :: setValue('favicon', $faviconFile['id']);
     clearTemplatesCache();
    } catch (Exception $e) {
     if ($e -> getCode() != UPLOAD_ERR_NO_FILE) {throw $e;}
    }
   }
   eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=appearance&tab=favicon&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
  } catch (Exception $e) {
   handleNormalFlowExceptions($e);
  }
 }
}
$smarty -> assign("T_APPEARANCE_FAVICON_FORM", $appearanceFaviconForm -> toArray());
