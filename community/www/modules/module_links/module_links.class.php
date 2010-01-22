<?php

class module_links extends EfrontModule {

    // Mandatory functions required for module function
    public function getName() {
        return _LINKS;
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
        eF_executeNew("drop table if exists module_links");
        eF_executeNew("CREATE TABLE module_links (
                          id int(11) NOT NULL auto_increment,
                          lessons_ID int(11) not null,
                          display varchar(500) not null,
                          link  varchar(500) not null,
                          description text,
                          PRIMARY KEY  (id)
                        ) DEFAULT CHARSET=utf8;");
        return true;
    }

    // And on deleting the module
    public function onUninstall() {
        eF_executeNew("DROP TABLE module_links;");
        return true;
    }

    // On deleting a lesson
    public function onDeleteLesson($lessonId) {
        eF_deleteTableData("module_links", "lessons_ID=".$lessonId);
        return true;
    }

    // On exporting a lesson
    public function onExportLesson($lessonId) {
        $data = eF_getTableData("module_links", "*", "lessons_ID = ".$lessonId);
        return $data;
    }

    // On importing a lesson
    public function onImportLesson($lessonId, $data) {
        foreach ($data as $record) {
            unset($record['id']);
            $record['lessons_ID'] = $lessonId;
            eF_insertTableData("module_links",$record);
        }
        return true;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {
            return array('title' => _LINKS,
                     'image' => $this -> moduleBaseDir . 'images/link30.png',
                     'link'  => $this -> moduleBaseUrl);
        }
    }


    public function getSidebarLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {

            $link_of_menu_clesson = array (array ('id' => 'other_link_id1',
                                                  'title' => _LINKS,
                                                  'image' => $this -> moduleBaseDir . 'images/link16',
                                                  'eFrontExtensions' => '1',
                                                  'link'  => $this -> moduleBaseUrl));

            return array ( "current_lesson" => $link_of_menu_clesson);
        } else if ($currentUser -> getType() == "student"){
            $link_of_menu_clesson = array (array ('title' => _LINKS,
                                                 'image' => $this -> moduleBaseDir . 'images/link16',
                                                 'eFrontExtensions' => '1',
                                                 'link'  => $this -> moduleBaseUrl));

            return array ( "current_lesson" => $link_of_menu_clesson);
        }
    }

    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
		$currentLesson = $this -> getCurrentLesson();
        return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
						array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getType() . ".php?ctg=control_panel"),
						array ('title' => _LINKS, 'link'  => $this -> moduleBaseUrl));
    }

    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {
        $currentLesson = $this -> getCurrentLesson();
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_LESSON_ID", $currentLesson -> lesson['id']);

        if (isset($_GET['delete_link']) && eF_checkParameter($_GET['delete_link'], 'id')) {
            eF_deleteTableData("module_links", "id=".$_GET['delete_link']);
            $this -> setMessageVar(_LINKS_SUCCESFULLYDELETEDLINK, 'success');
            eF_redirect("". $this -> moduleBaseUrl ."&message=$message&message_type=$message_type");
        } else if (isset($_GET['add_link']) || (isset($_GET['edit_link']) && eF_checkParameter($_GET['edit_link'], 'id'))) {
            $form = new HTML_QuickForm("link_entry_form", "POST", $_SERVER['REQUEST_URI'], "");
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('text', 'display', null);
            $form -> addElement('text', 'link', null);
            $form -> addElement('textarea', 'description', null);
            $form -> addElement('submit', 'submit_link', _SUBMIT, 'class = "flatButton"');
            
            $element = & $form->getElement('display');
            $element->setSize(50);
            $element = & $form->getElement('link');
            $element->setSize(50);
            $element = & $form->getElement('description');
            $element->setCols(50);
        
            if (isset($_GET['edit_link'])) {
                $link_entry = eF_getTableData("module_links", "*", "id=".$_GET['edit_link']);
                $form -> setDefaults(array('display' => $link_entry[0]['display'],
                                           'link'   => $link_entry[0]['link'],
                                           'description'   => $link_entry[0]['description']));
            } else {
				 $form -> setDefaults(array('link'   => "http://"));
			}
        
            if ($form -> isSubmitted() && $form -> validate()) {
                $fields = array('lessons_ID' => $_SESSION['s_lessons_ID'],
                                'display'   => $form -> exportValue('display'),
                                'link'   => $form -> exportValue('link'),
                                'description'     => $form -> exportValue('description'));
                if (isset($_GET['edit_link'])) {
                    if (eF_updateTableData("module_links", $fields, "id=".$_GET['edit_link'])) {
                        $message      = _LINKS_SUCCESFULLYUPDATEDLINKENTRY;
                        $message_type = 'success';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_links&message=$message&message_type=$message_type");
                    } else {
                        $message      = _LINKS_PROBLEMUPDATINGLINKENTRY;
                        $message_type = 'failure';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_links&message=$message&message_type=$message_type");
                    }
                } else {
                    if (eF_insertTableData("module_links", $fields)) {
                        $message      = _LINKS_SUCCESFULLYINSERTEDLINKENTRY;
                        $message_type = 'success';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_links&message=$message&message_type=$message_type");
                    } else {
                        $message      = _LINKS_PROBLEMINSERTINGLINKENTRY;
                        $message_type = 'failure';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_links&message=$message&message_type=$message_type");
                    }
                }
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
            $smarty -> assign('T_LINKS_FORM', $renderer -> toArray());
        } else {
            $links = eF_getTableDataFlat("module_links", "*", "lessons_ID = ".$_SESSION['s_lessons_ID']);
            $smarty -> assign("T_LINKS", $links);
        }
        return true;
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_LINKS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_LINKS_BASEURL", $this -> moduleBaseUrl);
		$smarty -> assign("T_LINKS_BASELINK" , $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        $smarty = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();

        $links = eF_getTableData("module_links", "*", "lessons_ID=".$currentLesson -> lesson['id']);
        $inner_table_options = array(array('text' => _LINKS_GOTOLINKSPAGE,  
         'image' => $this -> moduleBaseLink."images/redo.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_LINKS_INNERTABLE_OPTIONS", $inner_table_options);
        $smarty -> assign("T_LINKS_INNERTABLE", $links);
        return true;
    }


    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_LINKS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_LINKS_BASEURL" , $this -> moduleBaseUrl);
		$smarty -> assign("T_LINKS_BASELINK" , $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }
}
?>