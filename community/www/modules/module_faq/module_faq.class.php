<?php

class module_faq extends EfrontModule {


    // Mandatory functions required for module function
    public function getName() {
        return _FAQ;
    }

    public function getPermittedRoles() {
        return array("professor","student");
    }

 public function isLessonModule() {
  return true;
 }

    // Optional functions
    // What should happen on installing the module
    public function onInstall() {
        eF_executeNew("drop table if exists module_faq");
        return eF_executeNew("CREATE TABLE module_faq (
                          id int(11) NOT NULL auto_increment,
                          lessons_ID int(11) not null,
                          unit_ID int(11) default NULL,
                          question text not null,
                          answer text not null,
                          PRIMARY KEY (id)
                        ) DEFAULT CHARSET=utf8;");
    }

    // And on deleting the module
    public function onUninstall() {
        return eF_executeNew("DROP TABLE module_faq;");
    }

    // On deleting a lesson
    public function onDeleteLesson($lessonId) {
        return eF_deleteTableData("module_faq", "lessons_ID=".$lessonId);
    }

    // On exporting a lesson
    public function onExportLesson($lessonId) {
        $data = eF_getTableData("module_faq", "*","lessons_ID=".$lessonId);
        return $data;
    }

    // On importing a lesson
    public function onImportLesson($lessonId, $data) {
//pr($data);
        foreach ($data as $record) {
            unset($record['id']);
            $record['lessons_ID'] = $lessonId;
//            pr($record);
            eF_insertTableData("module_faq",$record);
        }
        return true;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
            return array('title' => 'FAQ',
                         'image' => $this -> moduleBaseLink.'images/unknown32.png',
                         'link' => $this -> moduleBaseUrl);
        }
    }


    public function getSidebarLinkInfo() {
        $link_of_menu_clesson = array (array ('id' => 'other_link_id1',
                                              'title' => _FAQ_LESSONLINK,
                                              'image' => $this -> moduleBaseLink.'images/unknown16',
                                              'eFrontExtensions' => '1',
                                              'link' => $this -> moduleBaseUrl));

        return array ( "current_lesson" => $link_of_menu_clesson);
    }

    public function getNavigationLinks() {
     $smarty = $this -> getSmartyVar();
        $currentUser = $this -> getCurrentUser();
  $currentLesson = $this -> getCurrentLesson();
        return array ( array ('title' => _HOME, 'link' => $smarty->get_template_vars('T_HOME_LINK')),
      array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
                      array ('title' => _FAQ, 'link' => $this -> moduleBaseUrl));
    }

    public function getLinkToHighlight() {
        return 'other_link_id1';
    }

    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {

        // Get smarty variable
        $smarty = $this -> getSmartyVar();

        if (isset($_GET['delete_faq']) && eF_checkParameter($_GET['delete_faq'], 'id')) {
            eF_deleteTableData("module_faq", "id=".$_GET['delete_faq']);
            eF_redirect("". $this -> moduleBaseUrl ."&message=".urlencode(_FAQ_SUCCESFULLYDELETEDFAQENTRY)."&message_type=success");
        } else if (isset($_GET['add_faq']) || (isset($_GET['edit_faq']) && eF_checkParameter($_GET['edit_faq'], 'id'))) {

            $load_editor = true; //TODO

            $form = new HTML_QuickForm("faq_entry_form", "post", $_SERVER['REQUEST_URI'], "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('textarea', 'question', null, 'class = "simpleEditor" style = "width:100%;height:5em;"');
            $form -> addElement('textarea', 'answer', null, 'class = "simpleEditor" style = "width:100%;height:25em;"');

            $currentLesson = $this -> getCurrentLesson();
   $units = eF_getTableDataFlat("content", "id, name", "lessons_ID = " . $currentLesson -> lesson['id']);

   //$units['id'] = array_merge(array("0"), $units['id']);
   //$units['name'] = array_merge(array(_FAQ_GENERAL_LESSON), $units['name']);

   sizeof($units) > 0 ? $units = array(0 => _FAQ_GENERAL_LESSON) + array_combine($units['id'], $units['name']) : $units = array("0" => _FAQ_GENERAL_LESSON);
   $form -> addElement('select', 'related_content', _CONTENT, $units, 'class = "inputSelectLong"');


            $form -> addElement('submit', 'submit_faq', _SUBMIT, 'class = "flatButton"');

            if (isset($_GET['edit_faq'])) {
                $faq_entry = eF_getTableData("module_faq", "*", "id=".$_GET['edit_faq']);
                $form -> setDefaults(array('related_content' => $faq_entry[0]['unit_ID'],
                         'question' => $faq_entry[0]['question'],
                                           'answer' => $faq_entry[0]['answer']));
            }

            if ($form -> isSubmitted() && $form -> validate()) {
                $fields = array('lessons_ID' => $_SESSION['s_lessons_ID'],
                    'unit_ID' => $form ->exportValue('related_content'),
                                'question' => $form -> exportValue('question'),
                                'answer' => $form -> exportValue('answer'));
                if (isset($_GET['edit_faq'])) {
                    if (eF_updateTableData("module_faq", $fields, "id=".$_GET['edit_faq'])) {
                        eF_redirect("".$this -> moduleBaseUrl. "&message=".urlencode(_FAQ_SUCCESFULLYUPDATEDFAQENTRY)."&message_type=success");
                    } else {
                        $this -> setMessageVar(_FAQ_PROBLEMUPDATINGFAQENTRY, 'failure');
                    }
                } else {
                    if (eF_insertTableData("module_faq", $fields)) {
                        eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_FAQ_SUCCESFULLYINSERTEDFAQENTRY)."&message_type=success");
                    } else {
                        $this -> setMessageVar(_FAQ_PROBLEMINSERTINGFAQENTRY, 'failure');
                    }
                }
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);

            $smarty -> assign('T_FAQ_FORM', $renderer -> toArray());
        } else {
            $currentLesson = $this -> getCurrentLesson();
            $faq = eF_getTableDataFlat("module_faq", "*", "lessons_ID=".$currentLesson -> lesson['id']);

            $currentUser = $this -> getCurrentUser();
            $smarty -> assign("T_FAQUSERLESSONROLE", $currentUser -> getRole($currentLesson));

            $smarty -> assign("T_FAQ", $faq);
            $smarty -> assign("T_QUESTIONS_FOUND", sizeof($faq));
        }

        return true;

    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_FAQ_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_FAQ_MODULE_BASEURL" , $this -> moduleBaseUrl);
   $smarty -> assign("T_FAQ_MODULE_BASELINK" , $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        // Get smarty variable
        $smarty = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();

        $faq = eF_getTableData("module_faq", "*", "lessons_ID=".$currentLesson -> lesson['id']);
        $inner_table_options = array(array('text' => _FAQ_GOTOFAQPAGE, 'image' => $this -> moduleBaseLink."images/redo.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_FAQ_INNERTABLE_OPTIONS", $inner_table_options);
        $smarty -> assign("T_FAQ_INNERTABLE", $faq);

        return true;
    }

    public function getControlPanelSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_FAQ_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_FAQ_MODULE_BASEURL" , $this -> moduleBaseUrl);
  $smarty -> assign("T_FAQ_MODULE_BASELINK" , $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getControlPanelModule() {
        // Get smarty variable
        $smarty = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();

        $faq = eF_getTableData("module_faq", "*", "lessons_ID =" . $currentLesson -> lesson['id']);
        $inner_table_options = array(array('text' => _FAQ_GOTOFAQPAGE, 'image' => $this -> moduleBaseLink."images/redo.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_FAQ_INNERTABLE_OPTIONS", $inner_table_options);
        $smarty -> assign("T_FAQ_INNERTABLE", $faq);

        return true;

    }

    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_FAQ_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_FAQ_MODULE_BASEURL" , $this -> moduleBaseUrl);
  $smarty -> assign("T_FAQ_MODULE_BASELINK" , $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }

    /***** Lesson content module pages *******/
    public function getContentSideInfo() {
        // Get smarty variable
        $smarty = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();
  $currentUnit = $this -> getCurrentUnit();

  if($currentUnit['id'] != "") {
   $faq = eF_getTableData("module_faq", "*", "unit_ID = " . $currentUnit['id']);
  }
        $inner_table_options = array(array('text' => _FAQ_GOTOFAQPAGE, 'image' => $this -> moduleBaseLink."images/redo.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_FAQ_INNERTABLE_OPTIONS", $inner_table_options);
        $smarty -> assign("T_FAQ_INNERTABLE", $faq);

        return true;
    }

    public function getContentSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_FAQ_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_FAQ_MODULE_BASEURL" , $this -> moduleBaseUrl);
  $smarty -> assign("T_FAQ_MODULE_BASELINK" , $this -> moduleBaseLink);
  $smarty -> assign("T_FAQ_IN_UNIT_CONTENT", true);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }

    public function getModuleIcon() {
        return $this -> moduleBaseLink.'images/unknown32.png';
    }
}
?>
