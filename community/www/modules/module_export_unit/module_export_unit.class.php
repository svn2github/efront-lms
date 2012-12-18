<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_export_unit extends EfrontModule {
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
    public function getName() {
     //This is a language tag, defined in the file lang-<your language>.php
        return _MODULE_EXPORT_UNIT_EXPORTUNIT;
    }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
    public function getPermittedRoles() {
        return array("professor"); //This module will be available to administrators
    }
    public function isLessonModule() {
     return true;
    }
    public function getContentToolsLink() {
     return '<a href = "'.$_SERVER['PHP_SELF'].'?ctg=module&op=module_export_unit&unit_id='.$_GET['view_unit'].'" target = "_NEW" title = "'._MODULE_EXPORT_UNIT_EXPORTUNIT.'">'._MODULE_EXPORT_UNIT_EXPORTUNIT.'</a>';
    }
    public function getModule() {
     $currentLesson = $this->getCurrentLesson();
     $unit = new EfrontUnit($_GET['unit_id']);
     if ($unit['lessons_ID'] != $currentLesson->lesson['id']) {
      throw new Exception ("You cannot export units from other lessons");
     }
     $compressedFile = $this->unitExport($unit);
     $compressedFile->sendFile(true);
    }
    public function unitExport(EfrontUnit $unit) {
     $currentLesson = $this->getCurrentLesson();
     $unitExportFolder = $this->moduleBaseDir."assets/";
     try {
      $dir = new EfrontDirectory($unitExportFolder);
      $dir -> delete();
     } catch (Exception $e) {
     }
     $htmlExportFolder = $unitExportFolder.'html/';
     $filesExportFolder = $unitExportFolder.'html/files/';
     is_dir($filesExportFolder) OR mkdir($filesExportFolder, 0755, true);
     //is_dir($htmlExportFolder)  OR mkdir($htmlExportFolder, 0755, true);
     $filelist = array();
     $unitFiles = $unit->getFiles(true);
     $units[] = $unit;
     $data = $unit['data'];
     foreach ($unitFiles as $file) {
      $filePath = str_replace($currentLesson->getDirectory(), "/", EfrontFile :: encode($file['path']));
      //Added this line in case of a space in path(urlencode makes it + and rawurlencode convers also slashes ) (#2143)
      $data = str_replace("content/lessons/".($currentLesson -> lesson['share_folder'] ? $currentLesson -> lesson['share_folder'] : $currentLesson -> lesson['id']).str_replace(' ','%20', $filePath), "files".str_replace(' ','%20', $filePath), $data);

      $data = str_replace("content/lessons/".($currentLesson -> lesson['share_folder'] ? $currentLesson -> lesson['share_folder'] : $currentLesson -> lesson['id']).$filePath, "files".$filePath, $data);
      $data = str_replace("view_file.php?file=".$file['id'], "files".$filePath, $data);
     }
     $unitContent = $data;
     $unitFilename = $htmlExportFolder.$unit['name'].".html";
     file_put_contents(EfrontFile :: encode($unitFilename), $unitContent);

     $filelist = array_merge($filelist, $unitFiles);

     foreach ($filelist as $file) {
      $filePath = (str_replace($currentLesson->getDirectory(), "", $file['path']));
      if (!is_dir($filesExportFolder.dirname($filePath))) {
       mkdir($filesExportFolder.dirname($filePath), 0755, true);
      }
      $file -> copy($filesExportFolder.$filePath);
     }

     $unitDirectory = new EfrontDirectory($unitExportFolder);

     if (eF_checkParameter($unit['name'], 'file')) {
      $filename = $unit['name'].'.zip';
     } else {
      $filename = 'unit.zip';
     }
     $compressedFile = $unitDirectory -> compress($filename, false);
     //$unitDirectory -> delete();

     return $compressedFile;
    }
}
