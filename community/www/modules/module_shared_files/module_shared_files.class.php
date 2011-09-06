<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_shared_files extends EfrontModule {
 /**

	 * Class constructor.

	 *

	 * Normally you don't have to call this function yourself, nor do you need to

	 * implement it in your modules

	 *

	 * @see libraries/EfrontModule#__construct()

	 */
 public function __construct($defined_moduleBaseUrl , $defined_moduleFolder) {
  parent::__construct($defined_moduleBaseUrl , $defined_moduleFolder);
 }
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
 public function getName() {
  //This is a language tag, defined in the file lang-<your language>.php
  return _MODULE_SHARED_FILES_SHAREDFILES;
 }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
 public function getPermittedRoles() {
  return array("professor", "student"); //This module will be available to all roles
 }
 /**

	 * Whether this module will be related to a lesson

	 *

	 * @see libraries/EfrontModule#isLessonModule()

	 */
 public function isLessonModule() {
  return true;
 }
 /**

	 * Put any installation commands here, usually creating database tables

	 *

	 * @see libraries/EfrontModule#onInstall()

	 */
 public function onInstall() {
  eF_executeQuery("drop table if exists module_shared_files");
  eF_executeQuery("CREATE TABLE module_shared_files(id int(11) not null auto_increment primary key, lessons_ID  mediumint(8) unsigned default 0, path text NOT NULL)");
  return true;
 }
 /**

	 * Put any uninstallation operations here, usually deleting database tables

	 *

	 * @see libraries/EfrontModule#onUninstall()

	 */
 public function onUninstall() {
  eF_executeQuery("drop table if exists module_shared_files");
  return true;
 }
 /**

	 * The main functionality

	 *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModule()

	 */
 public function getModule() {
  $currentLesson = $this -> getCurrentLesson();
  $smarty = $this -> getSmartyVar();
  $smarty -> assign("T_MODULE_BASEDIR", $this -> moduleBaseDir);
  $smarty -> assign("T_MODULE_BASELINK", $this -> moduleBaseLink);
  $smarty -> assign("T_MODULE_BASEURL", $this -> moduleBaseUrl);
  if ($currentLesson && !$currentLesson->options['digital_library']) {
   $result = eF_getTableDataFlat("module_shared_files", "path", "lessons_ID=".$currentLesson -> lesson['id']);
   $lessonSharedFiles = $result['path'];
   $basedir = new EfrontDirectory($currentLesson -> getDirectory());
   if ($_GET['other']) {
    $directory = new EfrontDirectory($_GET['other']);
    if (strpos($directory['path'], $basedir['path']) !== false && strcmp($directory['path'], $basedir['path'])) {
     $basedir = $directory;
     $smarty -> assign("T_PARENT_DIR", dirname($basedir['path']));
    }
   }
   $smarty -> assign("T_CURRENT_DIR", str_replace($currentLesson -> getDirectory(), "", $basedir['path']));
   $filesystem = new FileSystemTree($basedir, true);
   $files = $directories = array();
   foreach ($filesystem->tree as $key => $value) {
    $value['image'] = $value->getTypeImage();
                if (strpos($value['mime_type'], "image") !== false ||
                    strpos($value['mime_type'], "text") !== false ||
                    strpos($value['mime_type'], "pdf") !== false ||
                    strpos($value['mime_type'], "html") !== false ||
                    strpos($value['mime_type'], "flash") !== false) {
                     $value['preview'] = true;
                }
    if (in_array($key, $lessonSharedFiles)) {
     $value['module_shared_files_status'] = true;
    }
    if ($value instanceOf EfrontFile) {
     $files[$key] = (array)$value;
    } elseif ($value instanceOf EfrontDirectory) {
     $value['size'] = 0;
     $directories[$key] = (array)$value;
    }
   }
   $tableName = "sharedFilesTable";
   $dataSource = array_merge($directories, $files);
   list($tableSize, $dataSource) = filterSortPage($dataSource);
   $smarty -> assign("T_TABLE_SIZE", $tableSize);
   if (!empty($dataSource)) {
    $smarty -> assign("T_DATA_SOURCE", $dataSource);
   }
   try {
    if (isset($_GET['ajax']) && isset($_GET['share_file'])) {
     try {
      $entity = new EfrontFile(urldecode($_GET['share_file']));
     } catch (Exception $e) {
      $entity = new EfrontDirectory(urldecode($_GET['share_file']));
     }
     if (in_array($entity['path'], $lessonSharedFiles)) {
      eF_deleteTableData("module_shared_files", "path='".$entity['path']."' and lessons_ID=".$currentLesson -> lesson['id']);
      $added = false;
      if ($entity instanceOf EfrontDirectory) {
       $subTree = new FileSystemTree($entity, true);
       $insertValues = array();
       foreach ($subTree -> tree as $value) {
        eF_deleteTableData("module_shared_files", "path='".$value['path']."' and lessons_ID=".$currentLesson -> lesson['id']);
       }
      }
     } else {
      eF_insertTableData("module_shared_files", array("path" => $entity['path'], 'lessons_ID' => $currentLesson -> lesson['id']));
      $added = true;
      if ($entity instanceOf EfrontDirectory) {
       $subTree = new FileSystemTree($entity, true);
       $insertValues = array();
       foreach ($subTree -> tree as $value) {
        $insertValues[] = array("path" => $value['path'], 'lessons_ID' => $currentLesson -> lesson['id']);
       }
       if (!empty($insertValues)) {
        eF_insertTableDataMultiple("module_shared_files", $insertValues);
       }
      }
     }
     echo json_encode(array('status' => 1, 'added' => $added));
     exit;
    }
   } catch (Exception $e) {
    handleAjaxExceptions($e);
   }
   return true;
  } else if ($currentLesson) {
   $smarty -> assign("T_SHARED_FILES_ENABLED", true);
  }
 }
 /**

	 * Specify which file to include for template

	 *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSmartyTpl()

	 */
 public function getSmartyTpl() {
  return $this -> moduleBaseDir."module_shared_files_page.tpl";
 }
 /**

	 * Code to execute on the lesson page

	 *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonModule()

	 */
 public function getLessonModule() {
  $currentLesson = $this -> getCurrentLesson();
  if ($currentLesson && !$currentLesson->options['digital_library']) {
   $smarty = $this -> getSmartyVar();
   $smarty -> assign("T_MODULE_BASEDIR", $this -> moduleBaseDir);
   $smarty -> assign("T_MODULE_BASELINK", $this -> moduleBaseLink);
   $smarty -> assign("T_MODULE_BASEURL", $this -> moduleBaseUrl);
    $result = eF_getTableDataFlat("module_shared_files", "path", "lessons_ID=".$currentLesson -> lesson['id']);
    $lessonSharedFiles = $result['path'];
    $basedir = new EfrontDirectory($currentLesson -> getDirectory());
    if ($_GET['other']) {
     $directory = new EfrontDirectory($_GET['other']);
     if (strpos($directory['path'], $basedir['path']) !== false && strcmp($directory['path'], $basedir['path'])) {
      $basedir = $directory;
      $smarty -> assign("T_PARENT_DIR", dirname($basedir['path']));
     }
    }
    $smarty -> assign("T_CURRENT_DIR", str_replace($currentLesson -> getDirectory(), "", $basedir['path']));
    $filesystem = new FileSystemTree($basedir, true);
    $files = $directories = array();
    foreach ($filesystem->tree as $key => $value) {
     if (in_array($key, $lessonSharedFiles)) {
      $value['image'] = $value->getTypeImage();
      if (strpos($value['mime_type'], "image") !== false ||
      strpos($value['mime_type'], "text") !== false ||
      strpos($value['mime_type'], "pdf") !== false ||
      strpos($value['mime_type'], "html") !== false ||
      strpos($value['mime_type'], "flash") !== false) {
       $value['preview'] = true;
      }
      if ($value instanceOf EfrontFile) {
       $files[$key] = (array)$value;
      } elseif ($value instanceOf EfrontDirectory) {
       $value['size'] = 0;
       $directories[$key] = (array)$value;
      }
     }
    }
    $tableName = "sharedFilesTable";
    $dataSource = array_merge($directories, $files);
    if (isset($_GET['ajax']) && $_GET['ajax'] == "sharedFilesTable") {
     list($tableSize, $dataSource) = filterSortPage($dataSource);
     $smarty -> assign("T_TABLE_SIZE", $tableSize);
     if (!empty($dataSource)) {
      $smarty -> assign("T_DATA_SOURCE", $dataSource);
     }
    }
    return true;
  }
 }
 /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonSmartyTpl()

	 */
 public function getLessonSmartyTpl() {
  return $this -> moduleBaseDir."module_shared_files_lessonpage.tpl";
 }
 /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonCenterLinkInfo()

	 */
 public function getLessonCenterLinkInfo() {
  $roles = EfrontUser::getRoles();
  if ($roles[$_SESSION['s_lesson_user_type']] == 'professor') {
   return array('title' => $this -> getName(),
                      'image' => $this -> moduleBaseLink . 'folders.png',
                      'link' => $this -> moduleBaseUrl);
  }
 }
 public function getModuleJS() {
  return $this->moduleBaseDir."module_shared_files.js";
 }
 public function getNavigationLinks() {
  $currentUser = $this -> getCurrentUser();
  $currentLesson = $this -> getCurrentLesson();
  return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
  array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getType() . ".php?ctg=control_panel"),
  array ('title' => $this->getName(), 'link' => $this -> moduleBaseUrl));
 }
    public function getModuleIcon() {
        return $this -> moduleBaseLink.'folders.png';
    }
}
?>
