<?php

class module_quote extends EfrontModule {

    // Mandatory functions required for module function
    public function getName() {
        return _QUOTE_QUOTEDAY;
    }

    public function getPermittedRoles() {
        return array("professor", "student");
    }

    public function isLessonModule() {
      return true;
    }

    // Optional functions
    // What should happen on installing the module
    public function onInstall() {
        eF_executeNew("CREATE TABLE module_quote (
                          id int(11) NOT NULL auto_increment,
                          lessons_ID int(11) not null,
                          quote text,
                          PRIMARY KEY  (id)
                        ) DEFAULT CHARSET=utf8;");
        return true;
    }

    // And on deleting the module
    public function onUninstall() {
        eF_executeNew("DROP TABLE module_quote;");
        return true;
    }

    // On deleting a lesson
    public function onDeleteLesson($lessonId) {
        eF_deleteTableData("module_quote", "lessons_ID=".$lessonId);
        return true;
    }

    // On exporting a lesson
    public function onExportLesson($lessonId) {
        $data = eF_getTableData("module_quote", "*", "lessons_ID = ".$lessonId);
        return $data;
    }

    // On importing a lesson
    public function onImportLesson($lessonId, $data) {
        foreach ($data as $record) {
            $record['id'] = "";
            $record['lessons_ID'] = $lessonId;
            eF_insertTableData("module_quote", $record);
        }
        return true;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {
            return array('title' => _QUOTE_QUOTEDAY,    
                     'image' => $this -> moduleBaseDir . 'images/quote32.png',
                     'link'  => $this -> moduleBaseUrl);
        }
    }


    public function getSidebarLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {

            $link_of_menu_clesson = array (array ('id' => 'other_link_id1',
                                                  'title' => _QUOTE_QUOTEDAY,
                                                  'image' => $this -> moduleBaseDir . 'images/quote16',
                                                  'eFrontExtensions' => '1',
                                                  'link'  => $this -> moduleBaseUrl));

            return array ( "current_lesson" => $link_of_menu_clesson);
        } else if ($currentUser -> getType() == "student"){
            $link_of_menu_clesson = array (array ('title' => _QUOTE_QUOTEDAY,
                                                 'image' => $this -> moduleBaseDir . 'images/quote16',
                                                 'eFrontExtensions' => '1',
                                                 'link'  => $this -> moduleBaseUrl));

            return array ( "current_lesson" => $link_of_menu_clesson);
        }
    }

    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
		$currentLesson = $this -> getCurrentLesson();
        return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
						array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getRole($currentLesson) . ".php?ctg=control_panel"),
						array ('title' => _QUOTE_QUOTEDAY, 'link'  => $this -> moduleBaseUrl));
    }

    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {
        $currentLesson = $this -> getCurrentLesson();
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_LESSON_ID", $currentLesson -> lesson['id']);

        if (isset($_GET['delete_quote']) && eF_checkParameter($_GET['delete_quote'], 'id')) {
            eF_deleteTableData("module_quote", "id=".$_GET['delete_quote']);
            $this -> setMessageVar(_QUOTE_SUCCESFULLYDELETEDQUOTE, 'success');
            eF_redirect("". $this -> moduleBaseUrl ."&message=$message&message_type=$message_type");
        } else if (isset($_GET['add_quote']) || (isset($_GET['edit_quote']) && eF_checkParameter($_GET['edit_quote'], 'id'))) {
            $form = new HTML_QuickForm("quote_entry_form", "POST", $_SERVER['REQUEST_URI'], "");
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('textarea', 'quote', null);
            $form -> addElement('submit', 'submit_quote', _SUBMIT, 'class = "flatButton"');
            $element = & $form->getElement('quote');
            $element->setCols(100);
        
            if (isset($_GET['edit_quote'])) {
                $quote_entry = eF_getTableData("module_quote", "*", "id=".$_GET['edit_quote']);
                $form -> setDefaults(array('quote' => $quote_entry[0]['quote']));
            }
        
            if ($form -> isSubmitted() && $form -> validate()) {
                $fields = array('lessons_ID'	=> $_SESSION['s_lessons_ID'],
                                'quote'   		=> $form -> exportValue('quote'));
                if (isset($_GET['edit_quote'])) {
                    if (eF_updateTableData("module_quote", $fields, "id=".$_GET['edit_quote'])) {
                        $message      = _QUOTE_SUCCESFULLYUPDATEDQUOTEENTRY;
                        $message_type = 'success';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_quote&message=$message&message_type=$message_type");
                    } else {
                        $message      = _QUOTE_PROBLEMUPDATINGQUOTEENTRY;
                        $message_type = 'failure';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_quote&message=$message&message_type=$message_type");
                    }
                } else {
                    if (eF_insertTableData("module_quote", $fields)) {
                        $message      = _QUOTE_SUCCESFULLYINSERTEDQUOTEENTRY;
                        $message_type = 'success';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_quote&message=$message&message_type=$message_type");
                    } else {
                        $message      = _QUOTE_PROBLEMINSERTINGQUOTEENTRY;
                        $message_type = 'failure';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_quote&message=$message&message_type=$message_type");
                    }
                }
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
            $smarty -> assign('T_QUOTE_FORM', $renderer -> toArray());
        } else {
            $currentUser = $this -> getCurrentUser();
            if ($currentUser -> getType() == "professor") {
                $quotes = eF_getTableDataFlat("module_quote", "*", "lessons_ID = ".$_SESSION['s_lessons_ID']);
                $smarty -> assign("T_QUOTES", $quotes);
            }
            else{
                $quotes = eF_getTableDataFlat("module_quote", "*", "lessons_ID = ".$_SESSION['s_lessons_ID']);
                $id = rand(0, sizeof($quotes) - 1);
                $smarty -> assign("T_QUOTE", $quotes['quote'][$id]);
            }
        }
        return true;
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_QUOTE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_QUOTE_BASEURL", $this -> moduleBaseUrl);
        return $this -> moduleBaseDir . "module.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        $smarty = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();

        $quotes = eF_getTableData("module_quote", "*", "lessons_ID=".$currentLesson -> lesson['id']);
        $id = rand(0, sizeof($quotes) - 1);
        $inner_table_options = array(array('text' => _QUOTE_GOTOQUOTEPAGE,  
         'image' => $this -> moduleBaseLink."images/go_into.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_QUOTE_INNERTABLE_OPTIONS", $inner_table_options);
        $smarty -> assign("T_QUOTE_INNERTABLE", $quotes[$id]['quote']);
        return true;
    }


    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_QUOTE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_QUOTE_BASEURL" , $this -> moduleBaseUrl);
		$smarty -> assign("T_QUOTE_BASELINK" , $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }
}
?>