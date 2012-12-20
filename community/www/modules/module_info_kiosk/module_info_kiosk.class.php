<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_info_kiosk extends EfrontModule {
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
    public function getName() {
     //This is a language tag, defined in the file lang-<your language>.php
        return _MODULE_LEAFLET_MODULELEAFLET;
    }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
    public function getPermittedRoles() {
        return array("administrator", "professor", "student"); //This module will be available to administrators
    }
    /**

     * Pick a few of the efront scripts to be included

     *

     * (non-PHPdoc)

     * @see libraries/EfrontModule#addScripts()

     */
    public function addScripts() {
     return array('scriptaculous/controls');
    }
    public function getModuleJS() {
     return $this->moduleBaseDir."module_leaflet.js";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCenterLinkInfo()

     */
    public function getCenterLinkInfo() {
     return array('title' => $this -> getName(),
                     'image' => $this -> moduleBaseLink . 'img/information.png',
                     'link' => $this -> moduleBaseUrl);
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCenterLinkInfo()

     */
    public function getToolsLinkInfo() {
     $dir = $this -> moduleBaseDir.'assets/';
     if (!is_dir($dir)) {
      return false;
     }
     if (!defined('G_BRANCH_URL') || !G_BRANCH_URL || is_dir($dir.G_BRANCH_URL)) {
      if (count(scandir($dir)) == 2) { //directory is empty
       return false;
      }
      return array('title' => $this -> getName(),
        'image' => $this -> moduleBaseLink . 'img/information.png',
        'link' => $this -> moduleBaseUrl);
     } else {
      return false;
     }
    }
    /**

     * The main functionality

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModule()

     */
    public function getModule() {
     $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASELINK" , $this -> moduleBaseLink);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $dir = $this -> moduleBaseDir.'assets/';
        if (!is_dir($dir)) {
         mkdir($dir, 0755);
        }
        if ($_SESSION['s_type'] == 'administrator') {
         try {
          $form = new HTML_QuickForm("upload_files_form", "post", $this -> moduleBaseUrl.'&tab=upload', "", null, true);
          $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
          $form -> addElement('file', 'file', _UPLOADFILE);
          if (G_VERSIONTYPE == 'enterprise') {
           $tree = new EfrontBranchesTree();
           $pathString = $tree->toPathString();
           //$result = eF_getTableData("module_hcd_branch", "*", "url is not null and url !=''");
           $handle = '<img id = "busy" src = "images/16x16/clock.png" style = "display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/><div id = "autocomplete_leaflet_branches" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;';
           $form -> addElement('static', 'sidenote', $handle);
           $form -> addElement('text', 'leaflet_branch_autoselect', _BRANCH, 'class = "autoCompleteTextBox" id = "autocomplete"');
           $form -> addElement('hidden', 'leaflet_branch', '', 'id = "leaflet_branch_value"');
          }
          $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);
          $form -> addElement('submit', 'submit_upload', _UPLOAD, 'class = "flatButton"');
          if ($form -> isSubmitted() && $form -> validate()) {
           $values = $form -> exportValues();
           try {
            if ($values['leaflet_branch'] && eF_checkParameter($values['leaflet_branch'], 'id')) {
             $branch = new EfrontBranch($values['leaflet_branch']);
             if (!$branch->branch['url']) {
              throw new Exception("You must assign a url to the selected branch to upload files for it");
             }
             $dir = $this -> moduleBaseDir.'assets/'.$branch->branch['url'];
             mkdir($dir, 0755);
            }
            $filesystem = new FileSystemTree($dir);
            $file = $filesystem -> uploadFile("file", $dir);
           } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = failure;
            $this -> setMessageVar($message, $message_type);
           }
          }
          $smarty -> assign('T_UPLOAD_FORM', $form -> toArray());
          $url = $this -> moduleBaseUrl;
          $basedir = $dir;
          $options = array('zip' => false,
            'upload' => false,
            'create_folder' => false,
            'folders' => true);
          /**The file manager*/
          include "file_manager.php";
         } catch (Exception $e) {
          $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
          $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
          $message_type = 'failure';
          $this -> setMessageVar($message, $message_type);
         }
        } else {
         if (defined('G_BRANCH_URL') && G_BRANCH_URL) {
          try {
           $filesystem = new FileSystemTree($this -> moduleBaseDir.'assets/'.G_BRANCH_URL, true);
          } catch (Exception $e) {
           //do nothing here if the directory doesn't exist
          }
         } else {
          $filesystem = new FileSystemTree($this -> moduleBaseDir.'assets/', true);
         }
         $files = array();
         foreach ($filesystem->tree as $key => $value) {
          if ($value instanceof EfrontFile) {
           if (is_file($this -> moduleBaseDir.'ico/'.pathinfo($key, PATHINFO_EXTENSION).'.png')) {
            $icon = $this -> moduleBaseLink.'ico/'.pathinfo($key, PATHINFO_EXTENSION).'.png';
           } else {
            $icon = $this -> moduleBaseLink.'ico/unknown.png';
           }
           $files[] = array('text' => basename($key), 'image' => $icon, 'href' => $this -> moduleBaseLink.str_replace($this -> moduleBaseDir, '', $key));
          }
         }
         $smarty -> assign("T_FILES", $files);
        }
        return true;
    }
    /**

     * Specify which file to include for template

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSmartyTpl()

     */
    public function getSmartyTpl() {
     return $this -> moduleBaseDir."module.tpl";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getNavigationLinks()

     */
    public function getNavigationLinks() {
        return array (array ('title' => _HOME, 'link' => $_SERVER['PHP_SELF']),
                      array ('title' => $this -> getName(), 'link' => $this -> moduleBaseUrl));
    }
}
