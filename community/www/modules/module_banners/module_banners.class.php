<?php

class module_banners extends EfrontModule {

    // Mandatory functions required for module function
    public function getName() {
        return _BANNERS_BANNERS;
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
        eF_executeNew("CREATE TABLE module_banners (
                          id int(11) NOT NULL auto_increment,
                          lessons_ID int(11) not null,
                          image_id int(11) not null,
                          link  varchar(250) not null,
                          PRIMARY KEY  (id)
                        ) DEFAULT CHARSET=utf8;");
        return true;
    }

    // And on deleting the module
    public function onUninstall() {
        eF_executeNew("DROP TABLE module_banners;");
        return true;
    }

    // On deleting a lesson
    public function onDeleteLesson($lessonId) {
        eF_deleteTableData("module_banners", "lessons_ID=".$lessonId);
        return true;
    }


    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {
            return array('title' => _BANNERS_BANNERS,
                     'image' => $this -> moduleBaseLink.'images/banners32.png',
                     'link'  => $this -> moduleBaseUrl);
        }
    }


    public function getSidebarLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {

            $link_of_menu_clesson = array (array ('id' => 'other_link_id1',
                                                  'title' => _BANNERS_BANNERS,
                                                  'image' => $this -> moduleBaseLink . 'images/banners16',
                                                  'eFrontExtensions' => '1',
                                                  'link'  => $this -> moduleBaseUrl));

            return array ( "current_lesson" => $link_of_menu_clesson);
        } else if ($currentUser -> getType() == "student"){
            $link_of_menu_clesson = array (array ('title' => _BANNERS_BANNERS,
                                                 'image' => $this -> moduleBaseLink . 'images/banners16',
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
						array ('title' => _BANNERS_BANNERS, 'link'  => $this -> moduleBaseUrl));
    }

    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {
        $currentLesson = $this -> getCurrentLesson();
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_LESSON_ID", $currentLesson -> lesson['id']);

        if (isset($_GET['delete_banner']) && eF_checkParameter($_GET['delete_banner'], 'id')) {
            eF_deleteTableData("module_banners", "id=".$_GET['delete_banner']);
            $this -> setMessageVar(_BANNERS_SUCCESFULLYDELETEDBANNER, 'success');
            eF_redirect("". $this -> moduleBaseUrl ."&message=$message&message_type=$message_type");
        } else if (isset($_GET['add_banner']) || (isset($_GET['edit_banner']) && eF_checkParameter($_GET['edit_banner'], 'id'))) {
            try {
                $bannerFileSystemTree = new FileSystemTree($this -> moduleBaseDir . 'banners/');
                $existingImages["--"] = "--";
                foreach (new EfrontFileTypeFilterIterator(new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($bannerFileSystemTree -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('png', 'jpg', 'gif', 'jpeg', 'bmp')) as $key => $value) {
                    $existingImages[basename($key)] = basename($key);
                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            }
            
            $form = new HTML_QuickForm("banner_entry_form", "POST", $_SERVER['REQUEST_URI'], "");
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('file', 'file_upload', _BANNERS_IMAGE, 'class = "inputText"');
            $form -> addElement('select', 'existing_image', _ORSELECTONEFROMLIST, $existingImages, "id = 'select_image'");
            $form -> addElement('text', 'link', null);
            $form -> addElement('submit', 'submit_banner', _SUBMIT, 'class = "flatButton"');
            
            $element = & $form->getElement('link');
            $element->setSize(50);
        
            if (isset($_GET['edit_banner'])) {
                $banner_entry = eF_getTableData("module_banners", "*", "id=".$_GET['edit_banner']);
                $imageFile = new EfrontFile($banner_entry[0]['image_id']);
                $dname = $imageFile -> offsetGet('name');
                $form -> setDefaults(array('link'   => $banner_entry[0]['link'],
                                           'existing_image'   => $dname));
			} else {
				 $form -> setDefaults(array('link'   => "http://"));
			}
			
            if ($form -> isSubmitted() && $form -> validate()) {
                try {
                    if ($_FILES['file_upload']['size'] > 0) {
                        $filesystem   = new FileSystemTree($this -> moduleBaseDir . 'banners/');
                        $uploadedFile = $filesystem -> uploadFile('file_upload', $this -> moduleBaseDir . 'banners/');
                        $imageid = $uploadedFile['id'];
                    } else {
                        $selectedImage = $form -> exportValue('existing_image');
                        if ($selectedImage != "--"){
                            $selectedImage = $bannerFileSystemTree -> seekNode($this -> moduleBaseDir . 'banners/'.$selectedImage);
                            $lfile = new EfrontFile($selectedImage['path']);
                            $imageid = $lfile -> offsetGet('id');
                        }
                        else{
                            $imageid = 0;
                        }
                    }
                }
                catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                }
                
                
                $fields = array('lessons_ID' => $_SESSION['s_lessons_ID'],
                                'link'   => $form -> exportValue('link'),
                                'image_id'     => $imageid);
                if (isset($_GET['edit_banner'])) {
                    if (eF_updateTableData("module_banners", $fields, "id=".$_GET['edit_banner'])) {
                        $message      = _BANNERS_SUCCESFULLYUPDATEDBANNERENTRY;
                        $message_type = 'success';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_banners&message=$message&message_type=$message_type");
                    } else {
                        $message      = _BANNERS_PROBLEMUPDATINGBANNERENTRY;
                        $message_type = 'failure';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_banners&message=$message&message_type=$message_type");
                    }
                } else {
                    if (eF_insertTableData("module_banners", $fields)) {
                        $message      = _BANNERS_SUCCESFULLYINSERTEDBANNERENTRY;
                        $message_type = 'success';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_banners&message=$message&message_type=$message_type");
                    } else {
                        $message      = _BANNERS_PROBLEMINSERTINGBANNERENTRY;
                        $message_type = 'failure';
                        eF_redirect("".$_SERVER['PHP_SELF']."?ctg=module&op=module_banners&message=$message&message_type=$message_type");
                    }
                }
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
            $smarty -> assign('T_BANNERS_FORM', $renderer -> toArray());
        } else {
            $banners = eF_getTableDataFlat("module_banners", "*", "lessons_ID = ".$_SESSION['s_lessons_ID']);
            $banners['image_path'] = array();
            $bannerFileSystemTree = new FileSystemTree($this -> moduleBaseDir . 'banners/');
            for ($i = 0; $i < sizeof($banners['image_id']); $i++){
                $imageFile = new EfrontFile($banners['image_id'][$i]);
                $banners['image_path'][$i] =  'modules/module_banners/banners/'. basename($imageFile -> offsetGet('path'));
                $js_init .= 'bannerarray['.$i.']="'. $banners['image_path'][$i].'";';
                $js_init .= 'linkarray['.$i.']="'. $banners['link'][$i].'";';
            }
            $smarty -> assign("T_BANNERS", $banners);
            $smarty -> assign("T_BANNERS_JS_INIT", $js_init);
        }
        return true;
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_BANNERS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_BANNERS_BASEURL", $this -> moduleBaseUrl);
		$smarty -> assign("T_BANNERS_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        $smarty = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();
        
        $banners = eF_getTableDataFlat("module_banners", "*", "lessons_ID=".$currentLesson -> lesson['id']);
        $banners['image_path'] = array();
        $bannerFileSystemTree = new FileSystemTree($this -> moduleBaseDir . 'banners/');
        for ($i = 0; $i < sizeof($banners['image_id']); $i++){
            if ($banners['image_id'][$i] > 0) {
                $imageFile = new EfrontFile($banners['image_id'][$i]);
                $banners['image_path'][$i] = 'modules/module_banners/banners/'. basename($imageFile -> offsetGet('path'));
                $js_init .= 'bannerarray['.$i.']="'. $banners['image_path'][$i].'";';
                $js_init .= 'linkarray['.$i.']="'. $banners['link'][$i].'";';
            }
        }        
        $inner_table_options = array(array('text' => _BANNERS_GOTOBANNERSPAGE,  
         'image' => $this -> moduleBaseLink."images/redo.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_BANNERS_INNERTABLE_OPTIONS", $inner_table_options);
        $smarty -> assign("T_BANNERS", $banners);
        $smarty -> assign("T_BANNERS_JS_INIT", $js_init);
        $smarty -> assign("T_BANNERS_BASELINK", $this -> moduleBaseLink);
        return true;
    }


    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_BANNERS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_BANNERS_BASEURL" , $this -> moduleBaseUrl);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }
}
?>