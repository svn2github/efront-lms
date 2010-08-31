 <?php

/*

 * Class defining the new module

 * Its name should be the same as the one provided in the module.xml file

 */
class module_administrator_tools extends EfrontModule {
 /*

     * Mandatory function returning the name of the module

     * @return string the name of the module

     */
    public function getName() {
        return _MODULE_ADMINISTRATOR_TOOLS;
    }
    /*

     * Mandatory function returning an array of permitted roles from the set {"administrator", "professor", "student"}

     *

     * @return array of eFront user roles that this module applies for

     */
    public function getPermittedRoles() {
        return array("administrator");
    }
 public function getModuleJs() {
  return $this->moduleBaseDir."module_administrator_tools.js";
 }
    public function addScripts() {
     return array("scriptaculous/effects", "scriptaculous/controls");
    }
    /*

     * Function to be executed when the module is installed to an eFront system

     * Example implementation:

     *

     * public function onInstall() {

     *   return eF_executeNew("CREATE TABLE module_mymodule (

     *                    id int(11) NOT NULL auto_increment,

     *                    name text not null,

     *                    PRIMARY KEY  (id)

     *                   ) DEFAULT CHARSET=utf8;");

     * }

     * @return the result (true/false) of any module installation operations

     */
    public function onInstall() {
  return true;
    }
    /*

     * Function to be executed when the module is removed from an eFront system

     * Example implementation:

     *

     * public function onUninstall() {

     *   return eF_executeNew("DROP TABLE module_mymodule;");

     * }

     *

     * @return the result (true/false) of any removal operations

     */
    public function onUninstall() {
  //eF_executeNew("DROP TABLE module_cancellations;");
    }
    /*

     * Get Navigational links for the top of the independent module page(s)

     * Get information in an array of sub-arrays with fields:

     * 'title': the title to appear on the link

     * 'image': the image to appear (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName' -TODO

     * 'link': the url of the page to be from this link

     * Each sub-array represents a different link. Between them the "&raquo;" character is automatically inserted by the system

     * Example implementation:

     *

     *  public function getNavigationLinks() {

     *          $currentUser = $this -> getCurrentUser();

     *          return array (array ('title' => _HOME, 'link'  => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),

     *                       array ('title' => _FAQ, 'link'  => $this -> moduleBaseUrl));

     *  }

     *

     * @return array describing the header navigational links for the module pages

     */
    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
        return array (array ('title' => _HOME, 'link' => $currentUser -> getRole() . ".php"),
                      array ('title' => _MODULE_ADMINISTRATOR_TOOLS, 'link' => $this -> moduleBaseUrl));
    }
    public function getCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "administrator") {
            return array('title' => _MODULE_ADMINISTRATOR_TOOLS,
                         'image' => $this -> moduleBaseDir . 'images/tools.png',
                         'link' => $this -> moduleBaseUrl);
        }
    }
    /*

     * This is the function for the php code of the MAIN module pages (namely the ones

     * called from the url:    $this->moduleBaseUrl . "&...."

     *

     * The global smarty variable may also be used here and in conjunction

     * with the getSmartyTpl() function, thus using php+smarty to display the page

     *

     * Rules:

     * - You are not allowed to use the $_GET['ctg'] and $_GET['op'] variables

     * - You should use the $this -> moduleBaseUrl variable to reference the module basic url

     * - You should use the $this -> moduleBaseDir variable to reference the module basic directory

     *

     * Tips:

     * - Use the $this -> getSmartyVar() function to utilize the global smarty variable.

     * - Use the $this -> setMessageVar($message, $message_type) function to export information to eFront users with header messages

     *

     * @return the result of any module operations in boolean form (true/false)

     */
    public function getModule() {
     try {
   //$GLOBALS['load_editor'] = true;
   $smarty = $this -> getSmartyVar();
      $currentUser = $this -> getCurrentUser();
   $form = new HTML_QuickForm("change_login_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools", "", null, true);
   $form -> addElement('static', 'sidenote', '<img id = "module_administrator_tools_busy" src = "images/16x16/clock.png" style="display:none;" alt = "'._LOADING.'" title = "'._LOADING.'"/>');
   $form -> addElement('text', 'selection_user', _MODULE_ADMINISTRATOR_TOOLS_SELECTUSERTOCHANGELOGINFOR, 'id = "module_administrator_tools_autocomplete_users" class = "autoCompleteTextBox" style = "width:400px"' );
   $form -> addElement('static', 'autocomplete_note', _STARTTYPINGFORRELEVENTMATCHES);
   $form -> addElement('text', 'new_login', _MODULE_ADMINISTRATOR_TOOLS_NEWLOGIN, 'class = "inputText"');
   $form -> addElement('hidden', 'users_LOGIN', '' , 'id="module_administrator_tools_users_LOGIN"');
   $form -> addRule('selection_user', _THEFIELD.' "'._USER.'" '._ISMANDATORY, 'required', null, 'client');
   $form -> addRule('users_LOGIN', _MODULE_ADMINISTRATOR_TOOLS_THISUSERWASNOTFOUND, 'required', null, 'client');
   $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
   if ($form -> isSubmitted() && $form -> validate()) {
    try {
     $values = $form -> exportValues();
     $user = EfrontUserFactory::factory($values['users_LOGIN']);
     $existingTables = $GLOBALS['db'] -> GetCol("show tables");
     foreach ($existingTables as $table) {
      $fields = $GLOBALS['db'] -> GetCol("describe $table");
      foreach ($fields as $value) {
       if (stripos($value, 'login') !== false) {
        eF_updateTableData($table, array($value => $values['new_login']), "$value = '".$values['users_LOGIN']."'");
       }
      }
      if ($table == 'f_personal_messages') {
       //@todo:recipient
       eF_updateTableData($table, array("sender" => $values['new_login']), "sender = '".$values['users_LOGIN']."'");
      }
      if ($table == 'notifications' || $table == 'sent_notifications') {
       eF_updateTableData($table, array("recipient" => $values['new_login']), "recipient = '".$values['users_LOGIN']."'");
      }
      if ($table == 'surveys' || $table == 'module_hcd_events') {
       eF_updateTableData($table, array("author" => $values['new_login']), "author = '".$values['users_LOGIN']."'");
      }
     }
     $message = _OPERATIONCOMPLETEDSUCCESSFULLY;
     $message_type= 'success';
    } catch (Exception $e) {
     $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
     $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
     $message_type = 'failure';
    }
   }
   $smarty -> assign("T_TOOLS_FORM", $form -> toArray());
      try {
       if (isset($_GET['ajax']) && isset($_GET['user']) && eF_checkParameter($_GET['user'], 'login')) {
        $user = EfrontUserFactory::factory($_GET['user']);
        echo json_encode(array('status' => 1, 'supervisors' => $supervisors, 'supervisor_names' => $supervisorNames));
        exit;
       }
      } catch (Exception $e) {
       handleAjaxExceptions($e);
      }
     } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
     }
     $this -> setMessageVar($message, $message_type);
    }
    /*

     * This function is used to define a smarty template for the main module pages

     *

     * Attention: DO NOT define this function if you do not want to use smarty (and want to just create html with the php

     * code of the getModule() function)

     *

     * Example implementation:

     *

     *    public function getSmartyTpl() {

     *         // It is a good idea to define the two following smarty variables for inclusion of module images, libraries etc

     *         $smarty = $this -> getSmartyVar();

     *         $smarty -> assign("T_MYMODULE_MODULE_BASEDIR" , $this -> moduleBaseDir);

     *         $smarty -> assign("T_MYMODULE_MODULE_BASEURL" , $this -> moduleBaseUrl);

     *         return $this -> moduleBaseDir . "module.tpl";

     *     }

     * @return false or the string of the filename of the smarty template file for the module main pages

     */
    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_ADMINISTRATOR_TOOLS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_ADMINISTRATOR_TOOLS_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_ADMINISTRATOR_TOOLS_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
    }
}
?>
