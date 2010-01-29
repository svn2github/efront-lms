<?php

class module_thumbnail extends EfrontModule {


    // Mandatory functions required for module function
    public function getName() {
        return _THUMBNAIL;
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
        eF_executeNew("drop table if exists module_thumbnail");
        return eF_executeNew("CREATE TABLE module_thumbnail (
                          id int(11) NOT NULL auto_increment,
                          filename varchar(255) NOT NULL,
                          lessons_ID int(11) NOT NULL,
                          title varchar(255) NOT NULL,
                          PRIMARY KEY  (id)
                        ) DEFAULT CHARSET=utf8;");
    }

	// Auxiliary function for deleting the module_thumbnail directories in each lesson folder
	public function deleteDir($dirname) {
		if (is_dir($dirname)) {
			$dir_handle = opendir($dirname);
		}

		if (!$dir_handle) {
			return false;
		}
		while($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname."/".$file)) {
					unlink($dirname."/".$file);
				} else {
					deleteDir($dirname.'/'.$file);
				}
			}
		}

		closedir($dir_handle);
		rmdir($dirname);
		return true;
	}

    // And on deleting the module
    public function onUninstall() {
        $data = eF_getTableData("module_thumbnail", "distinct lessons_ID","");
		foreach ($data as $lesson_with_module) {
			$this -> deleteDir(G_LESSONSPATH . $lesson_with_module['lessons_ID'] . "/module_thumbnail/");
		}
        return eF_executeNew("DROP TABLE module_thumbnail;");
    }

    // On exporting a lesson
    public function onDeleteLesson($lessonId) {
		return eF_deleteTableData("module_thumbnail", "lessons_ID='".$lessonId."'");
	}

    // On exporting a lesson
    public function onExportLesson($lessonId) {
        $data = eF_getTableData("module_thumbnail", "*","lessons_ID=".$lessonId);
        return $data;
    }

    // On importing a lesson
    public function onImportLesson($lessonId, $data) {

         foreach ($data as $link_record) {
             // Keep the old id
             unset($link_record['id']);
             $link_record['lessons_ID'] = $lessonId;
             $new_meeting_id = eF_insertTableData("module_thumbnail", $link_record);
         }

        return true;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
            return array('title' => _THUMBNAIL,
                         'image' =>  $this -> moduleBaseLink.'images/photo.png',
                         'link'  => $this -> moduleBaseUrl);
        }
    }


    public function getCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "administrator") {
            return array('title' => _THUMBNAIL,
                         'image' => $this -> moduleBaseLink.'images/photo.png',
                         'link'  => $this -> moduleBaseUrl);
        }
    }

    public function getNavigationLinks() {

        $currentUser = $this -> getCurrentUser();
		$currentLesson = $this -> getCurrentLesson();
		
        $basicNavArray = array (array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
								array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
        		    			array ('title' => _THUMBNAIL, 'link'  => $this -> moduleBaseUrl));
        if (isset($_GET['edit_thumbnail'])) {
        	$basicNavArray[] = array ('title' => _THUMBNAIL_MANAGEMENT, 'link'  => $this -> moduleBaseUrl . "&edit_thumbnail=". $_GET['edit_thumbnail']);
        } else if (isset($_GET['add_thumbnail'])) {
         	$basicNavArray[] = array ('title' => _THUMBNAIL_THUMBNAILVIDEODATA, 'link'  => $this -> moduleBaseUrl . "&add_thumbnail=1");
        }
        return $basicNavArray;

    }

    public function getSidebarLinkInfo() {

        $link_of_menu_clesson = array (array ('id' => 'thumbnail_link_id1',
                                              'title' => _THUMBNAIL,
                                              'image' => $this -> moduleBaseDir . 'images/thumbnail16',
                                              'eFrontExtensions' => '1',
                                              'link'  => $this -> moduleBaseUrl));

        return array ( "current_lesson" => $link_of_menu_clesson);

    }


    public function getLinkToHighlight() {
        return 'thumbnail_link_id1';
    }

    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {

        $currentUser = $this -> getCurrentUser();
        // Get smarty global variable
        $smarty = $this -> getSmartyVar();

        if (isset($_GET['delete_thumbnail']) && eF_checkParameter($_GET['delete_thumbnail'], 'id')) {
            $thumbnail = eF_getTableData("module_thumbnail", "filename, lessons_ID", "id=".$_GET['delete_thumbnail']);
            unlink(G_LESSONSPATH . $thumbnail[0]['lessons_ID'] . "/module_thumbnail/" . $thumbnail[0]['filename']);
            eF_deleteTableData("module_thumbnail", "id=".$_GET['delete_thumbnail']);

            $other_thumbs = eF_getTableData("module_thumbnail", "filename", "lessons_ID=".$thumbnail[0]['lessons_ID']);

			// If no pictures remain then delete the folder
            if (empty($other_thumbs)) {
            	$this->deleteDir(G_LESSONSPATH . $thumbnail[0]['lessons_ID'] . "/module_thumbnail");
            }

            eF_redirect("". $this -> moduleBaseUrl ."&message=".urlencode(_THUMBNAIL_SUCCESFULLYDELETEDTHUMBNAILENTRY)."&message_type=success");
        } else if (isset($_GET['add_thumbnail']) || (isset($_GET['edit_thumbnail']) && eF_checkParameter($_GET['edit_thumbnail'], 'id'))) {

			// Create the form
            $form = new HTML_QuickForm("thumbnail_entry_form", "post", $_SERVER['REQUEST_URI'], "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('text', 'title', _THUMBNAIL_NAME, 'class = "inputText"');
            $form -> addRule('title', _THUMBNAILTHEFIELDNAMEISMANDATORY, 'required', null, 'client');

			$form -> addElement('file', 'file_upload', _IMAGEFILE, 'class = "inputText"');
            $form -> addElement('submit', 'submit_thumbnail', _SUBMIT, 'class = "flatButton"');

            if (isset($_GET['edit_thumbnail'])) {
                $thumbnail_entry = eF_getTableData("module_thumbnail", "*", "id=".$_GET['edit_thumbnail']);
                $smarty -> assign("T_THUMBNAIL_MODULE_IMAGE", $thumbnail_entry[0]);
                $form -> setDefaults(array('title'       => $thumbnail_entry[0]['title']));
            }

            if ($form -> isSubmitted() && $form -> validate()) {
				$currentLesson = $this -> getCurrentLesson();
// check if image file
				$lessonImgsDir = $currentLesson -> getDirectory() . "module_thumbnail";
				if (!is_dir($lessonImgsDir)) {
					mkdir($lessonImgsDir, 0755);
				}

				try {
					if ($_GET['add_thumbnail']) {
						$filesystem   = new FileSystemTree($lessonImgsDir);
						$uploadedFile = $filesystem -> uploadFile('file_upload', $lessonImgsDir);

						$fields = array('title'   => $form -> exportValue('title'),
								'filename'		  => $uploadedFile['name'],
								'lessons_ID'      => $currentLesson -> lesson['id']);

						if (eF_insertTableData("module_thumbnail", $fields)) {
							eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_THUMBNAIL_SUCCESFULLYINSERTEDTHUMBNAILENTRY)."&message_type=success");
						} else {
							unlink($initial_filename);
							eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_THUMBNAIL_PROBLEMINSERTINGTHUMBNAILENTRY)."&message_type=failure");
						}
					} else {
						$fields = array('title'   => $form -> exportValue('title'));

						if (eF_updateTableData("module_thumbnail",$fields,"id = '".$_GET['edit_thumbnail']."'")) {
							eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_THUMBNAIL_SUCCESFULLYUPDATEDTHUMBNAILENTRY)."&message_type=success");
						} else {
							eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_THUMBNAIL_PROBLEMINSERTINGTHUMBNAILENTRY)."&message_type=failure");
						}
					}
				} catch (Exception $e) {
					$smarty = $this -> getSmartyVar();
					$smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());

					$message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
					eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_THUMBNAIL_PROBLEMINSERTINGTHUMBNAILENTRY)."&message_type=failure");
				}
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);

            $smarty -> assign('T_THUMBNAIL_FORM', $renderer -> toArray());
        } else {
            $currentUser = $this -> getCurrentUser();
            $currentLesson = $this -> getCurrentLesson();

            $thumbnail = eF_getTableData("module_thumbnail", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'");

            $smarty -> assign("T_THUMBNAIL_CURRENTLESSONTYPE", $currentUser -> getRole($this -> getCurrentLesson()));

            $smarty -> assign("T_THUMBNAIL", $thumbnail);
            $smarty -> assign("T_USERINFO",$currentUser -> user);
        }

        return true;

    }

    public function addScripts() {
        if (isset($_GET['edit_thumbnail'])) {
            return array("scriptaculous/prototype", "scriptaculous/effects");
        } else {
            return array();
        }
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_THUMBNAIL_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_THUMBNAIL_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_THUMBNAIL_MODULE_BASELINK" , $this -> moduleBaseLink);

        return $this -> moduleBaseDir . "module.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        $currentUser = $this -> getCurrentUser();
            // Get smarty variable
            $smarty = $this -> getSmartyVar();
            $currentLesson = $this -> getCurrentLesson();

            $thumbnail = eF_getTableData("module_thumbnail", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'");

            $smarty -> assign("T_MODULE_THUMBNAIL_INNERTABLE_OPTIONS", array(array('text' => _THUMBNAIL_THUMBNAILLIST,   'image' => "16x16/go_into.png", 'href' => $this -> moduleBaseUrl)));
            $smarty -> assign("T_THUMBNAIL_INNERTABLE", $thumbnail);
            return true;

    }

    public function getLessonSmartyTpl() {
        $currentUser = $this -> getCurrentUser();
            $smarty = $this -> getSmartyVar();
            $smarty -> assign("T_THUMBNAIL_MODULE_BASEDIR" , $this -> moduleBaseDir);
            $smarty -> assign("T_THUMBNAIL_MODULE_BASEURL" , $this -> moduleBaseUrl);
            $smarty -> assign("T_THUMBNAIL_MODULE_BASELINK" , $this -> moduleBaseLink);

            $smarty -> assign("T_USERINFO",$currentUser -> user);

            return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }

    public function getModuleJS() {
        return $this -> moduleBaseDir . "highslide/highslide-with-gallery.js";
    }

    public function getModuleCSS() {
        return $this -> moduleBaseDir . "style.css";
    }
}
?>