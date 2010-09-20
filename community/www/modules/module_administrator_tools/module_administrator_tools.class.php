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
     return array("scriptaculous/effects", "scriptaculous/controls", "includes/lesson_settings");
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
  //eF_executeNew("DROP TABLE ;");
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
     try {
      $existingUser = true;
      $newUser = EfrontUserFactory::factory($values['new_login']);
     } catch (Exception $e) {
      $existingUser = false;
     }
     if ($existingUser) {
      throw new Exception(_MODULE_ADMINISTRATOR_TOOLS_USERALREADYEXISTS);
     }
     $existingTables = $GLOBALS['db'] -> GetCol("show tables");
     $views = $GLOBALS['db'] -> GetCol("show tables like '%_view'");
     $errors = array();
     foreach ($existingTables as $table) {
      try {
       if (!in_array($table, $views)) {
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
      } catch (Exception $e) {
       $errors[] = $e -> getMessage();
      }
     }
     if (empty($errors)) {
      $message = _OPERATIONCOMPLETEDSUCCESSFULLY;
      $message_type= 'success';
     } else {
      $message = _MODULE_ADMINISTRATOR_TOOLS_OPERATIONCOMPLETEDSUCCESSFULLYBUTHEFOLLOWINGTABLESCOULDNOTBEUPDATED.': <br>'.implode("<br>", $errors);
      $message_type= 'failure';
     }
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
      $lessonSettings = $this -> getLessonSettings();
      $smarty -> assign("T_LESSON_SETTINGS", $lessonSettings);
      $smarty -> assign("T_LESSON_SETTINGS_GROUPS", array(1 => _LESSONOPTIONS, 2 => _LESSONMODULES, 3 => _MODULES));
      try {
       if (isset($_GET['ajax']) && isset($_GET['activate']) && in_array($_GET['activate'], array_keys($lessonSettings))) {
        $this -> toggleSetting($_GET['activate'], 1);
        exit;
       } elseif (isset($_GET['ajax']) && isset($_GET['deactivate']) && in_array($_GET['deactivate'], array_keys($lessonSettings))) {
        $this -> toggleSetting($_GET['deactivate'], 0);
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
    private function toggleSetting($setting, $enable) {
     $result = eF_getTableData("lessons", "id, options");
     foreach ($result as $value) {
      $options = unserialize($value['options']);
      $enable ? $options[$setting] = 1 : $options[$setting] = 0;
      eF_updateTableData("lessons", array("options" => serialize($options)), "id=".$value['id']);
      if ($setting == 'chat') {
       if ($enable) {
        eF_updateTableData("chatrooms", array("active" => 1), "lessons_ID = '".$value['id']."'");
       } else {
           eF_updateTableData("chatrooms", array("active" => 0), "lessons_ID = '".$value['id']."'");
       }
      }
     }
    }
    private function getLessonSettings() {
     $lessonSettings['theory'] = array('text' => _THEORY, 'image' => "32x32/theory.png", 'onClick' => 'activate(this, \'theory\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['theory']) && $currentLesson -> options['theory'] ? null : 'inactiveImage');
     $lessonSettings['examples'] = array('text' => _EXAMPLES, 'image' => "32x32/examples.png", 'onClick' => 'activate(this, \'examples\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['examples']) && $currentLesson -> options['examples'] ? null : 'inactiveImage');
     if ($GLOBALS['configuration']['disable_projects'] != 1) {
      $lessonSettings['projects'] = array('text' => _PROJECTS, 'image' => "32x32/projects.png", 'onClick' => 'activate(this, \'projects\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['projects']) && $currentLesson -> options['projects'] ? null : 'inactiveImage');
     }
     if ($GLOBALS['configuration']['disable_tests'] != 1) {
      $lessonSettings['tests'] = array('text' => _TESTS, 'image' => "32x32/tests.png", 'onClick' => 'activate(this, \'tests\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['tests']) && $currentLesson -> options['tests'] ? null : 'inactiveImage');
     }
     if ($GLOBALS['configuration']['disable_feedback'] != 1) {
      $lessonSettings['feedback'] = array('text' => _FEEDBACK, 'image' => "32x32/feedback.png", 'onClick' => 'activate(this, \'feedback\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['feedback']) && $currentLesson -> options['feedback'] ? null : 'inactiveImage');
     }
     $lessonSettings['rules'] = array('text' => _ACCESSRULES, 'image' => "32x32/rules.png", 'onClick' => 'activate(this, \'rules\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['rules']) && $currentLesson -> options['rules'] ? null : 'inactiveImage');
     if ($GLOBALS['configuration']['disable_forum'] != 1) {
      $lessonSettings['forum'] = array('text' => _FORUM, 'image' => "32x32/forum.png", 'onClick' => 'activate(this, \'forum\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['forum']) && $currentLesson -> options['forum'] ? null : 'inactiveImage');
     }
     if ($GLOBALS['configuration']['disable_comments'] != 1) {
      $lessonSettings['comments'] = array('text' => _COMMENTS, 'image' => "32x32/note.png", 'onClick' => 'activate(this, \'comments\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['comments']) && $currentLesson -> options['comments'] ? null : 'inactiveImage');
     }
     if ($GLOBALS['configuration']['disable_news'] != 1) {
      $lessonSettings['news'] = array('text' => _ANNOUNCEMENTS, 'image' => "32x32/announcements.png", 'onClick' => 'activate(this, \'news\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['news']) && $currentLesson -> options['news'] ? null : 'inactiveImage');
     }
     if ($GLOBALS['configuration']['disable_online_users'] != 1) {
      $lessonSettings['online'] = array('text' => _USERSONLINE, 'image' => "32x32/users.png", 'onClick' => 'activate(this, \'online\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['online']) && $currentLesson -> options['online'] ? null : 'inactiveImage');
     }
     if ($GLOBALS['configuration']['chat_enabled']) {
      $lessonSettings['chat'] = array('text' => _CHAT, 'image' => "32x32/chat.png", 'onClick' => 'activate(this, \'chat\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['chat']) && $currentLesson -> options['chat'] ? null : 'inactiveImage');
     }
     $lessonSettings['scorm'] = array('text' => _SCORM, 'image' => "32x32/scorm.png", 'onClick' => 'activate(this, \'scorm\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['scorm']) && $currentLesson -> options['scorm'] ? null : 'inactiveImage');
     $lessonSettings['digital_library'] = array('text' => _DIGITALLIBRARY, 'image' => "32x32/file_explorer.png", 'onClick' => 'activate(this, \'digital_library\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['digital_library']) && $currentLesson -> options['digital_library'] ? null : 'inactiveImage');
     if ($GLOBALS['configuration']['disable_calendar'] != 1) {
      $lessonSettings['calendar'] = array('text' => _CALENDAR, 'image' => "32x32/calendar.png", 'onClick' => 'activate(this, \'calendar\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['calendar']) && $currentLesson -> options['calendar'] ? null : 'inactiveImage');
     }
     if ($GLOBALS['configuration']['disable_glossary'] != 1) {
      $lessonSettings['glossary'] = array('text' => _GLOSSARY, 'image' => "32x32/glossary.png", 'onClick' => 'activate(this, \'glossary\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['glossary']) && $currentLesson -> options['glossary'] ? null : 'inactiveImage');
     }
     $lessonSettings['auto_complete'] = array('text' => _AUTOCOMPLETE, 'image' => "32x32/autocomplete.png", 'onClick' => 'activate(this, \'auto_complete\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['auto_complete']) && $currentLesson -> options['auto_complete'] ? null : 'inactiveImage');
     $lessonSettings['content_tree'] = array('text' => _CONTENTTREEFIRSTPAGE, 'image' => "32x32/content_tree.png", 'onClick' => 'activate(this, \'content_tree\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['content_tree']) && $currentLesson -> options['content_tree'] ? null : 'inactiveImage');
     $lessonSettings['lesson_info'] = array('text' => _LESSONINFORMATION, 'image' => "32x32/information.png", 'onClick' => 'activate(this, \'lesson_info\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['lesson_info']) && $currentLesson -> options['lesson_info'] ? null : 'inactiveImage');
     if ($GLOBALS['configuration']['disable_bookmarks'] != 1) {
      $lessonSettings['bookmarking'] = array('text' => _BOOKMARKS, 'image' => "32x32/bookmark.png", 'onClick' => 'activate(this, \'bookmarking\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['bookmarking']) && $currentLesson -> options['bookmarking'] ? null : 'inactiveImage');
     }
     $lessonSettings['reports'] = array('text' => _STATISTICS, 'image' => "32x32/reports.png", 'onClick' => 'activate(this, \'reports\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['reports']) && $currentLesson -> options['reports'] ? null : 'inactiveImage');
     $lessonSettings['content_report'] = array('text' => _CONTENTREPORT, 'image' => "32x32/warning.png", 'onClick' => 'activate(this, \'content_report\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['content_report']) && $currentLesson -> options['content_report'] ? null : 'inactiveImage');
     $lessonSettings['print_content'] = array('text' => _PRINTCONTENT, 'image' => "32x32/printer.png", 'onClick' => 'activate(this, \'print_content\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['print_content']) && $currentLesson -> options['print_content'] ? null : 'inactiveImage');
     $lessonSettings['start_resume'] = array('text' => _STARTRESUME, 'image' => "32x32/continue.png", 'onClick' => 'activate(this, \'start_resume\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['start_resume']) && $currentLesson -> options['start_resume'] ? null : 'inactiveImage');
     $lessonSettings['show_percentage'] = array('text' => _COMPLETIONPERCENTAGEBLOCK, 'image' => "32x32/percent.png", 'onClick' => 'activate(this, \'show_percentage\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_percentage']) && $currentLesson -> options['show_percentage'] ? null : 'inactiveImage');
     $lessonSettings['show_content_tools'] = array('text' => _UNITOPTIONSBLOCK, 'image' => "32x32/options.png", 'onClick' => 'activate(this, \'show_content_tools\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_content_tools']) && $currentLesson -> options['show_content_tools'] ? null : 'inactiveImage');
     $lessonSettings['show_right_bar'] = array('text' => _RIGHTBAR, 'image' => "32x32/hide_right.png", 'onClick' => 'activate(this, \'show_right_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_right_bar']) && $currentLesson -> options['show_right_bar'] ? null : 'inactiveImage');
     $lessonSettings['show_left_bar'] = array('text' => _LEFTBAR, 'image' => "32x32/hide_left.png", 'onClick' => 'activate(this, \'show_left_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_left_bar']) && $currentLesson -> options['show_left_bar'] ? null : 'inactiveImage');
     $lessonSettings['show_student_cpanel'] = array('text' => _STUDENTCPANEL, 'image' => "32x32/options.png", 'onClick' => 'activate(this, \'show_student_cpanel\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_student_cpanel']) && $currentLesson -> options['show_student_cpanel'] ? null : 'inactiveImage');
     $lessonSettings['show_dashboard'] = array('text' => _DASHBOARD, 'image' => "32x32/generic.png", 'onClick' => 'activate(this, \'show_dashboard\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => !isset($currentLesson -> options['show_dashboard']) || $currentLesson -> options['show_dashboard'] ? null : 'inactiveImage');
     if ($GLOBALS['currentTheme'] -> options['sidebar_interface'] == 1 || $GLOBALS['currentTheme'] -> options['sidebar_interface'] == 2) {
      $lessonSettings['show_horizontal_bar'] = array('text' => _SHOWHORIZONTALBAR, 'image' => "32x32/export.png", 'onClick' => 'activate(this, \'show_horizontal_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => !isset($currentLesson -> options['show_horizontal_bar']) || $currentLesson -> options['show_horizontal_bar'] ? null : 'inactiveImage');
     }
     foreach (eF_loadAllModules(true) as $module) {
      if ($module -> isLessonModule()) {
       // The $setLanguage variable is defined in globals.php
       if (!in_array("administrator", $module -> getPermittedRoles())) {
        $mod_lang_file = $module -> getLanguageFile($setLanguage);
        if (is_file ($mod_lang_file)) {
         require_once $mod_lang_file;
        }
       }
       // The $setLanguage variable is defined in globals.php
       if (!in_array("administrator", $module -> getPermittedRoles())) {
        $mod_lang_file = $module -> getLanguageFile($setLanguage);
        if (is_file ($mod_lang_file)) {
         require_once $mod_lang_file;
        }
       }
       $lessonSettings[$module -> className] = array('text' => $module -> getName(), 'image' => "32x32/addons.png", 'onClick' => 'activate(this, \''.$module -> className.'\')', 'title' => _CLICKTOTOGGLE, 'group' => 3, 'class' => ($currentLesson -> options[$module -> className] == 1) ? null : 'inactiveImage');
      }
     }
     $lessonSettings[$key]['onClick'] = 'activate(this, \''.$key.'\')';
     $lessonSettings[$key]['style'] = 'color:inherit';
     return $lessonSettings;
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
