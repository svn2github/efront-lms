<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_###NAME### extends EfrontModule {
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
    public function getName() {
     //This is a language tag, defined in the file lang-<your language>.php
        return "###TITLE###";
    }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
    public function getPermittedRoles() {
        return array(###ROLES###); //This module will be available to administrators
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCenterLinkInfo()

     */
    public function getCenterLinkInfo() {
     return array('title' => $this -> getName(),
                     'image' => $this -> moduleBaseLink . 'img/logo.png',
                     'link' => $this -> moduleBaseUrl);
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
        $this->getForm();
        if (###FILE_MANAGER###) {
          $this->_setupFileManager();
        }
        try {
         if (isset($_GET['ajax']) && $_GET['ajax'] == '###NAME###Table') {
          $this -> getAjaxResults();
          $smarty -> display($this -> moduleBaseDir . "module.tpl");
          exit;
         }
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        return true;
    }
    private function getForm() {
     $smarty = $this -> getSmartyVar();
     $form = new HTML_QuickForm("module_form", "post", $this -> moduleBaseUrl."&tab=###NAME###_form", "", null, true);
     $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
     ###FIELDS###
     $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
     if ($form -> isSubmitted() && $form -> validate()) {
      try {
       $values = $form -> exportValues();
       $message = _OPERATIONCOMPLETEDSUCCESSFULLY;
       $this -> setMessageVar($message, 'success');
      } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $this -> setMessageVar($message, 'failure');
   }
     }
     $smarty -> assign("T_FORM", $form -> toArray());
     return true;
    }
    protected function _setupFileManager() {
     $smarty = $this->getSmartyVar();
     $url = $this -> moduleBaseUrl;
     $basedir = $this -> moduleBaseDir.'assets';
     /**The file manager*/
     include "file_manager.php";
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
    private function getAjaxResults() {
     $smarty = $this -> getSmartyVar();
     //$data = eF_getTableData("module_demo_data", "*");
     ###GRID_DATA### //sample data, safe to remove and replace with some query's result
     isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
     if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
      $sort = $_GET['sort'];
      isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
     } else {
      $sort = 'login';
     }
     $data = eF_multiSort($data, $sort, $order);
     $smarty -> assign("T_TABLE_SIZE", sizeof($data));
     if (isset($_GET['filter'])) {
      $data = eF_filterData($data, $_GET['filter']);
     }
     if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
      isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
      $data = array_slice($data, $offset, $limit);
     }
     $smarty -> assign("T_DATA_SOURCE", $data);
    }
}
