<?php

/**
 * RSS class
 *
 * This class implements the RSS eFront module
 * @version 0.1
 */
class module_rss extends EfrontModule
{

    public function getName() {
        return "RSS";
    }

    public function getPermittedRoles() {
        return array("administrator", "professor", "student");
    }
	
	public function getModuleJs() {
		return $this->moduleBaseDir."rss_reader.js";
	}

    public function onInstall() {
        eF_executeNew("drop table if exists module_rss_feeds");
        eF_executeNew("CREATE TABLE module_rss_feeds(id int(11) not null auto_increment primary key, title varchar(255), url text not null, active int(11) not null default 1, lessons_ID int(11) default 0)");

        return true;
    }

    public function onUnInstall() {
        eF_executeNew("drop table module_rss_feeds");

        return true;
    }

    public function getModule() {
        return true;
    }

    public function getLessonModule() {
        return true;
    }

	public function isLessonModule() {
		return true;
	}
    
    public function getLessonSmartyTpl() {
        return $this -> getControlPanelSmartyTpl();
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_RSS_MODULE_BASEURL", $this -> moduleBaseUrl);
        $smarty -> assign("T_RSS_MODULE_BASELINK", $this -> moduleBaseLink);

        $feeds  = $this -> getFeeds();

        if (isset($_GET['delete_feed']) && eF_checkParameter($_GET['delete_feed'], 'id') && in_array($_GET['delete_feed'], array_keys($feeds))) {
            //Although db operations do not support exceptions (yet), we leave this here for future support
            try {
                eF_deleteTableData("module_rss_feeds", "id=".$_GET['delete_feed']);
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['deactivate_feed']) && eF_checkParameter($_GET['deactivate_feed'], 'id') && in_array($_GET['deactivate_feed'], array_keys($feeds))) {
            //Although db operations do not support exceptions (yet), we leave this here for future support
            try {
                eF_updateTableData("module_rss_feeds", array("active" => 0), "id=".$_GET['deactivate_feed']);
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['activate_feed']) && eF_checkParameter($_GET['activate_feed'], 'file') && in_array($_GET['activate_feed'], array_keys($feeds))) {
            //Although db operations do not support exceptions (yet), we leave this here for future support
            try {
                eF_updateTableData("module_rss_feeds", array("active" => 1), "id=".$_GET['activate_feed']);
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } else if (isset($_GET['add_feed']) || (isset($_GET['edit_feed']) && eF_checkParameter($_GET['edit_feed'], 'id') && in_array($_GET['edit_feed'], array_keys($feeds)))) {

            $lessons = array(0 => _ALLLESSONS);
            $result  = EfrontLesson :: getLessons();
            foreach ($result as $key => $lesson) {
                $lessons[$key] = $lesson['name'];
            }
            
            isset($_GET['add_feed']) ? $postTarget = "&add_feed=1" : $postTarget = "&edit_feed=".$_GET['edit_feed'];
            $form = new HTML_QuickForm("add_feed_form", "post", $this -> moduleBaseUrl.$postTarget, "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
            $form -> addElement('text', 'title', _RSS_FEEDTITLE, 'class = "inputText"');
            $form -> addElement('text', 'url', _RSS_FEEDURL, 'class = "inputText"');
            $form -> addElement('select', 'lessons_ID', _LESSON, $lessons);
            $form -> addElement("advcheckbox", "active", _RSS_ACTIVE, null, 'class = "inputCheckBox"', array(0, 1));
            $form -> setDefaults(array('active' => 1));
            $form -> addRule('title', _THEFIELD.' "'._RSS_FEEDTITLE.'" '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('url', _THEFIELD.' "'._RSS_FEEDURL.'" '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('title', _INVALIDFIELDDATA, 'checkParameter', 'text');

            $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
            if (isset($_GET['edit_feed'])) {
                $editFeed = $feeds[$_GET['edit_feed']];
                $form -> setDefaults(array('title' => $editFeed['title'], 'url' => $editFeed['url'], 'active' => $editFeed['active'], 'lessons_ID' => $editFeed['lessons_ID']));
            }

            if ($form -> isSubmitted() && $form -> validate()) {                        //If the form is submitted and validated
                $values = $form -> exportValues();
                $fields = array("title"      => $values['title'], 
                				"url"        => $values['url'], 
                				"active"     => $values['active'], 
                				"lessons_ID" => $values['lessons_ID']);

                if (isset($_GET['add_feed'])) {
                    if (eF_insertTableData("module_rss_feeds", $fields)) {
                        $smarty -> assign("T_RSS_RSS_MESSAGE", _RSS_SUCCESSFULLYADDEDFEED);
                    } else {
                        $message      = _RSS_PROBLEMADDINGFEED;
                        $message_type = 'failure';
                    }
                } else {
                    if (eF_updateTableData("module_rss_feeds", $fields, "id=".$_GET['edit_feed'])) {
                        $smarty -> assign("T_RSS_RSS_MESSAGE", _RSS_SUCCESSFULLYEDITEDFEED);
                    } else {
                        $message      = _RSS_PROBLEMEDITINGFEED;
                        $message_type = 'failure';
                    }
                }
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer
            $renderer -> setRequiredTemplate (
               '{$html}{if $required}
                    &nbsp;<span class = "formRequired">*</span>
                {/if}');

            $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
            $form -> setRequiredNote(_REQUIREDNOTE);
            $form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

            $smarty -> assign('T_RSS_ADD_RSS_FORM', $renderer -> toArray());                     //Assign the form to the template
        } else {
            if (isset($_GET['ajax'])) {
                $feeds = $this -> getFeeds(true);
                foreach ($feeds as $feed) {
                    if ($feed['lessons_ID'] && $_SESSION['s_lessons_ID'] && $feed['lessons_ID'] != $_SESSION['s_lessons_ID']) {
                        unset ($feeds[$key]);
                    } else {
                        $response = $this -> parseFeed($feed);
                        foreach ($response as $value) {
                            $str .= '<li style = "display:none" onmouseover = "pauseList()" onmouseout = "continueList()">['.$feed['title'].'] <a href = "'.$value['link'].'" target = "_NEW">'.$value['title'].'</a></li>';
                        }
                    }
                }
                echo $str;
                exit;
            }
            $smarty -> assign("T_RSS_FEEDS", $feeds);
        }

        $this -> setMessageVar($message, $message_type);

        return $this -> moduleBaseDir . "module_rss.tpl";

    }

    public function getCenterLinkInfo() {
        $optionArray = array('title' => 'RSS',
                             'image' => $this -> moduleBaseLink.'images/rss32.png',
                             'link'  => $this -> moduleBaseUrl);
        $centerLinkInfo = $optionArray;

        return $centerLinkInfo;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() != 'student') {
            return $this -> getCenterLinkInfo();
        }
    }

    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();

        if ($currentUser -> getType() == 'administrator') {
            return array (array ('title' => _HOME, 'link'  => $currentUser -> getType() . ".php?ctg=control_panel"),
                          array ('title' => _RSS_RSS, 'link'  => $this -> moduleBaseUrl));
        } else {
			$currentLesson = $this -> getCurrentLesson();
            return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
							array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getType() . ".php?ctg=control_panel"),
							array ('title' => _RSS_RSS, 'link'  => $this -> moduleBaseUrl));            
        }
    }
    

/*
     public function getSidebarLinkInfo() {
     $currentUser = $this -> getCurrentUser();

     switch ($currentUser -> getType()) {
     case 'administrator':
     $systemArray = array('id'               => 'rss_id',
     'title'            => 'RSS',
     'image'            => $this -> moduleBaseDir.'images/16x16/rss',
     'eFrontExtensions' => 1,
     'link'             => $this -> moduleBaseUrl);
     $sidebarLinkInfo = array('system' => array($systemArray));
     break;
     default: break;
     }

     return $sidebarLinkInfo;
     }
     */

    public function getControlPanelModule() {
        return true;
    }

    public function getControlPanelSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_RSS_MODULE_BASEURL", $this -> moduleBaseUrl);
		//pr($this -> moduleBaseLink);
        $smarty -> assign("T_RSS_MODULE_BASELINK", $this -> moduleBaseLink);
        
        $options[] = array('text' => _SHOWALL, 'image' => $this -> moduleBaseLink."images/arrow_down_blue.png", 'href' => "javascript:void(0)", onClick => "showHideAll(this)");
        $options[] = array('text' => _RSS_REFRESH, 'image' => $this -> moduleBaseLink."images/refresh.png", 'href' => "javascript:void(0)", onClick => "getFeeds()");
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() != 'student') {
            $options[] = array('text' => _RSS_GOTORSS, 'image' => $this -> moduleBaseLink."images/redo.png", 'href' => $this -> moduleBaseUrl);
        }
        $feeds = $this -> getFeeds(true);
        
        foreach ($feeds as $key => $feed) {
            if ($feed['lessons_ID'] && $_SESSION['s_lessons_ID'] && $feed['lessons_ID'] != $_SESSION['s_lessons_ID']) {
                unset ($feeds[$key]);
            }
        }
        $smarty -> assign("T_RSS_OPTIONS", $options);
        $smarty -> assign("T_RSS_NUM_FEEDS", sizeof($feeds));

        return $this -> moduleBaseDir . "module_rss_cpanel.tpl";
    }


    public function parseFeed($feed) {
        $xmlString = file_get_contents($feed['url']);
        try {
            $iterator  = new SimpleXMLIterator($xmlString);
            foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator :: SELF_FIRST) as $key => $value) {
                
                if ($key == 'item') {
                    $rss[] = array('title' => (string)$value -> title, 'link' => (string)$value -> link);
                }
            }
        } catch (Exception $e) {
            $rss[] = array('title' => '<span class = "emptyCategory">'._CONNECTIONERROR.'</span>', 'link' => 'javascript:void(0)');
        }
        
        return $rss;
    }

    public function getFeeds($onlyActive = false) {
        $lessons = array(0 => _ALLLESSONS);
        $result  = EfrontLesson :: getLessons();
        foreach ($result as $key => $lesson) {
            $lessons[$key] = $lesson['name'];
        }

        if ($onlyActive) {
            $result = eF_getTableData("module_rss_feeds", "*", "active=1");
        } else {
            $result = eF_getTableData("module_rss_feeds", "*");
        }
        $feeds  = array();
        foreach ($result as $value) {
            $value['lesson'] = $lessons[$value['lessons_ID']];
            $feeds[$value['id']] = $value;
        }

        
        return $feeds;
    }

}



?>