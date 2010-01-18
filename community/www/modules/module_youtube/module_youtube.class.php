<?php

class module_youtube extends EfrontModule {


    // Mandatory functions required for module function
    public function getName() {
        return _YOUTUBE;
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
        eF_executeNew("drop table if exists module_youtube");
        return eF_executeNew("CREATE TABLE module_youtube (
                          id int(11) NOT NULL auto_increment,
                          lessons_ID int(11) NOT NULL,
                          title varchar(255) NOT NULL,
                          link varchar(255) NOT NULL,
                          description text,
                          PRIMARY KEY (id)
                        ) DEFAULT CHARSET=utf8;");
    }

    // And on deleting the module
    public function onUninstall() {
        return eF_executeNew("DROP TABLE module_youtube;");
    }

    // On exporting a lesson
    public function onDeleteLesson($lessonId) {
        $links_to_del = eF_getTableDataFlat("module_youtube", "id","lessons_ID='".$lessonId."'");
        eF_deleteTableData("module_youtube", "lessons_ID='".$lessonId."'");

        return true;
    }

    // On exporting a lesson
    public function onExportLesson($lessonId) {
        $data = eF_getTableData("module_youtube", "*","lessons_ID=".$lessonId);
        return $data;
    }

    // On importing a lesson
    public function onImportLesson($lessonId, $data) {
        $changed_ids = array();

        foreach ($data as $link_record) {
            // Keep the old id
            unset($link_record['id']);
            $link_record['lessons_ID'] = $lessonId;
            $new_meeting_id = eF_insertTableData("module_youtube", $link_record);
        }

        return true;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
            return array('title' => _YOUTUBE,
                         'image' => $this -> moduleBaseDir . 'images/youtube32.png',
                         'link'  => $this -> moduleBaseUrl);
        }
    }


    public function getCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "administrator") {
            return array('title' => _YOUTUBE,
                         'image' => $this -> moduleBaseDir . 'images/youtube32.png',
                         'link'  => $this -> moduleBaseUrl);
        }
    }

    public function getNavigationLinks() {

        $currentUser = $this -> getCurrentUser();
		$currentLesson = $this -> getCurrentLesson();
        $basicNavArray = array (array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
								array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
        		    			array ('title' => _YOUTUBE, 'link'  => $this -> moduleBaseUrl));
        if (isset($_GET['edit_youtube'])) {
        	$basicNavArray[] = array ('title' => _YOUTUBE_MANAGEMENT, 'link'  => $this -> moduleBaseUrl . "&edit_youtube=". $_GET['edit_youtube']);
        } else if (isset($_GET['add_youtube'])) {
         	$basicNavArray[] = array ('title' => _YOUTUBE_MANAGEMENT, 'link'  => $this -> moduleBaseUrl . "&add_youtube=1");
        }
        return $basicNavArray;

    }

    public function getSidebarLinkInfo() {

        $link_of_menu_clesson = array (array ('id' => 'youtube_link_id1',
                                              'title' => _YOUTUBE,
                                              'image' => $this -> moduleBaseDir . 'images/youtube16',
                                              'eFrontExtensions' => '1',
                                              'link'  => $this -> moduleBaseUrl));

        return array ( "current_lesson" => $link_of_menu_clesson);

    }

    public function getLinkToHighlight() {
        return 'youtube_link_id1';
    }

    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {
        $currentUser = $this -> getCurrentUser();
        // Get smarty global variable
        $smarty = $this -> getSmartyVar();

		if (isset($_GET['postAjaxRequest']) && isset($_GET['id'])) {
            $currentLesson = $this -> getCurrentLesson();
            $youtube = eF_getTableData("module_youtube", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'");

			// Find the video with the requested id
			foreach ($youtube as $id => $video) {
				if ($video['id'] == $_GET['id']) {
					$idfound = $id;
					$videolink = $video['link'];
					break;
				}
			}

			// Find the previous and the next video
			if (isset($youtube[$idfound - 1])) {
				$prev = $youtube[$idfound - 1]['id'];
				$prev_tag = $youtube[$idfound - 1]['title'] .": " . $youtube[$idfound - 1]['description'];
			}
			if (isset($youtube[$idfound + 1])) {
				$next = $youtube[$idfound + 1]['id'];
				$next_tag = $youtube[$idfound + 1]['title'] .": " . $youtube[$idfound + 1]['description'];
			}


			echo '<table id="youtube_player"><tr><td colspan=\"2\"><object><param name="movie" value="http://www.youtube.com/v/'.$videolink.'"></param>';
			echo '<param name="allowFullScreen" value="true"></param><embed src="http://www.youtube.com/v/'.$videolink.'" type="application/x-shockwave-flash" allowfullscreen="true" width="400" height="323"></embed></object></td></tr><tr><td width="50%" align="left">';
			if ($prev) {
				echo '<a href = "javascript:void(0);" alt="'.$prev_tag.'" title="'.$prev_tag.'" onClick="requestVideo(\''.$prev.'\')">'._YOUTUBE_PREVIOUS.'</a>';
			}
			echo '</td><td width="50%" align="right">';
			if ($next) {
				echo '<a href = "javascript:void(0);" alt="'.$next_tag.'" title="'.$next_tag.'" onClick="requestVideo(\''.$next.'\')">'._YOUTUBE_NEXT.'</a>';
			}
			echo '</td></tr></table>';
			exit;

		}

        if (isset($_GET['delete_youtube']) && eF_checkParameter($_GET['delete_youtube'], 'id')) {
            eF_deleteTableData("module_youtube", "id=".$_GET['delete_youtube']);
            eF_deleteTableData("module_youtube_users_to_meeting", "meeting_ID=".$_GET['delete_youtube']);
            eF_redirect("". $this -> moduleBaseUrl ."&message=".urlencode(_YOUTUBE_SUCCESFULLYDELETEDYOUTUBEENTRY)."&message_type=success");
        } else if (isset($_GET['add_youtube']) || (isset($_GET['edit_youtube']) && eF_checkParameter($_GET['edit_youtube'], 'id'))) {


			$form = new HTML_QuickForm("youtube_entry_form", "post", $_SERVER['REQUEST_URI'], "", null, true);
			$form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
			$form -> addElement('text', 'title', null, 'class = "inputText"');
			$form -> addRule('title', _YOUTUBETHEFIELDNAMEISMANDATORY, 'required', null, 'client');

			$form -> addElement('text', 'link', null, 'class = "inputText"');
			$form -> addRule('link', _YOUTUBETHEFIELDLINKSISMANDATORY, 'required', null, 'client');

			$form -> addElement('textarea', 'description', null	);

			$form -> addElement('submit', 'submit_youtube', _SUBMIT, 'class = "flatButton"');

            if (isset($_GET['edit_youtube'])) {

                $youtube_entry = eF_getTableData("module_youtube", "*", "id=".$_GET['edit_youtube']);
                $timestamp_info = getdate($youtube_entry[0]['timestamp']);

                $form -> setDefaults(array('title'     		  => $youtube_entry[0]['title'],
                                           'lessons_ID'		  => $youtube_entry[0]['lessons_ID'],
                                           'link'     		  => "http://www.youtube.com/watch?v=" . $youtube_entry[0]['link'],
                                           'description'      => $youtube_entry[0]['description']));
            }

            if ($form -> isSubmitted() && $form -> validate()) {
					$pos = strpos($form -> exportValue('link'),"watch?v=");
					if ($pos) {
						$link = substr($form -> exportValue('link'), $pos + 8); //after the watch?v=
						$pos_end = strpos($link,"&");
						if ($pos_end) {
							$link = substr($link, 0, $pos_end);
						}

						$smarty = $this -> getSmartyVar();
						$currentLesson = $this -> getCurrentLesson();

						$fields = array('title'           => $form -> exportValue('title'),
										'lessons_ID'      => $currentLesson -> lesson['id'],
										'link'            => $link,
										'description'     => $form -> exportValue('description'));

						if (isset($_GET['edit_youtube'])) {
							if (eF_updateTableData("module_youtube", $fields, "id=".$_GET['edit_youtube'])) {
								eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_YOUTUBE_SUCCESFULLYUPDATEDYOUTUBEENTRY)."&message_type=success");
							} else {
								eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_YOUTUBE_PROBLEMUPDATINGYOUTUBEENTRY)."&message_type=failure");
							}
						} else {
							// The key will be the current time when the event was set concatenated with the initial timestamp for the meeting
							// If the latter changes after an event editing the key will not be changed
							if ($result = eF_insertTableData("module_youtube", $fields)) {
								eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_YOUTUBE_SUCCESFULLYINSERTEDYOUTUBEENTRY)."&message_type=success");
							} else {
								eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_YOUTUBE_PROBLEMINSERTINGYOUTUBEENTRY)."&message_type=failure");
							}
						}
					} else {
						eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_YOUTUBE_PROBLEMINSERTINGYOUTUBEENTRY)."&message_type=failure");
					}
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);

            $smarty -> assign('T_YOUTUBE_FORM', $renderer -> toArray());
        } else {
            $currentUser = $this -> getCurrentUser();
            $currentLesson = $this -> getCurrentLesson();

            $youtube = eF_getTableData("module_youtube", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'");

			$smarty -> assign("T_USERLESSONTYPE", $currentUser -> getRole($currentLesson));
            $smarty -> assign("T_YOUTUBE", $youtube);
            $smarty -> assign("T_USERINFO",$currentUser -> user);
        }

        return true;

    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_YOUTUBE_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_YOUTUBE_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_YOUTUBE_MODULE_BASELINK" , $this -> moduleBaseLink);

        return $this -> moduleBaseDir . "module.tpl";
    }

    public function addScripts() {
		$currentUser = $this -> getCurrentUser();
		if ($currentUser -> getRole($this -> getCurrentLesson()) == "student") {
		    return array("scriptaculous/prototype", "scriptaculous/effects");
        } else {
            return array();
        }
    }

	public function getdModuleJS() {
		return $this -> moduleBaseDir . "module_youtube.js";
	}


    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "student") {
            // Get smarty variable
            $smarty = $this -> getSmartyVar();
            $currentLesson = $this -> getCurrentLesson();

            $youtube = eF_getTableData("module_youtube", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'");

			if (sizeof($youtube) > 0) {
				$smarty -> assign("T_VIDEOLINK", $youtube[0]['link']);
				if (isset($youtube[1])) {
				   $smarty -> assign("T_NEXT", $youtube[1]['id']);
				   $smarty -> assign("T_NEXT_TAG", $youtube[1]['title'] .": ".$youtube[1]['description']);
				}
			}
            return true;
        } else {
            return false;
        }

    }

    public function getLessonSmartyTpl() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "student") {
            $smarty = $this -> getSmartyVar();
            $smarty -> assign("T_YOUTUBE_MODULE_BASEDIR" , $this -> moduleBaseDir);
            $smarty -> assign("T_YOUTUBE_MODULE_BASEURL" , $this -> moduleBaseUrl);
            $smarty -> assign("T_YOUTUBE_MODULE_BASELINK" , $this -> moduleBaseLink);

			$inner_table_options = array(array('text' => _YOUTUBE_YOUTUBELIST,   'image' => $this -> moduleBaseLink."images/go_into.png", 'href' => $this -> moduleBaseUrl));
			$smarty -> assign("T_YOUTUBE_INNERTABLE_OPTIONS", $inner_table_options);

            $smarty -> assign("T_USERINFO",$currentUser -> user);

            return $this -> moduleBaseDir . "module_InnerTable.tpl";
        } else {
            return false;
        }
    }
}
?>