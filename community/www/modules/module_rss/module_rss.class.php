<?php

/**

 * RSS class

 *

 * This class implements the RSS eFront module

 * @version 0.1

 */
class module_rss extends EfrontModule
{
 public $feedProviderModes = array('system' => _SYSTEM,
           'lesson' => _LESSON);
 public $providedFeeds = array('announcements' => _ANNOUNCEMENTS,
                    //'history' 		 => _HISTORY,
                    'catalog' => _COURSECATALOG,
                    'calendar' => _CALENDAR,
           'forum' => _FORUM);
 public $lessonProvidedFeeds = array('announcements' => _ANNOUNCEMENTS,
                    'structure' => _LESSONSTRUCTURE,
                    //'history' 		 => _HISTORY,
                    'calendar' => _CALENDAR,
           'forum' => _FORUM,
                    //'comments'		 => _COMMENTS
                    );
 public $feedLimit = 10;
    public function getName() {
        return "RSS";
    }
    public function getPermittedRoles() {
        return array("administrator", "professor", "student");
    }
 public function getModuleJs() {
  return $this->moduleBaseDir."rss_reader.js";
 }
    public function onUpgrade() {
        try {
         eF_executeNew("CREATE TABLE if not exists module_rss_provider(id int(11) not null auto_increment primary key,
                    mode varchar(255),
                    type varchar(255),
                    active int(11) not null default 1,
                    lessons_ID int(11) default 0)");
         eF_executeNew("alter table module_rss_feeds add (only_summary int(11) default 1)");
        } catch (Exception $e) {}
    }

    public function onInstall() {
        eF_executeNew("drop table if exists module_rss_feeds");
        eF_executeNew("CREATE TABLE module_rss_feeds(id int(11) not null auto_increment primary key,
                    title varchar(255),
                    url text not null,
                    active int(11) not null default 1,
                    only_summary int(11) default 0,
                    lessons_ID int(11) default -1)");
  eF_insertTableData("module_rss_feeds", array('title' => 'eFront news', 'url' => 'http://www.efrontlearning.net/product/efront-news?format=feed&type=rss&install=1', 'active' => 1, 'lessons_ID' => -1));
        eF_executeNew("drop table if exists module_rss_provider");
        eF_executeNew("CREATE TABLE module_rss_provider(id int(11) not null auto_increment primary key,
                    mode varchar(255),
                    type varchar(255),
                    active int(11) not null default 1,
                    lessons_ID int(11) default 0)");
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

        $smarty -> assign("T_RSS_PROVIDED_FEEDS_MODES", $this -> feedProviderModes);
        $smarty -> assign("T_RSS_PROVIDED_FEEDS_TYPES", $this -> providedFeeds);
        $smarty -> assign("T_RSS_PROVIDED_FEEDS_LESSON_TYPES", $this -> lessonProvidedFeeds);

        if (isset($_GET['delete_feed']) && eF_checkParameter($_GET['delete_feed'], 'id')) {
            try {
             if ($_GET['type'] == 'provider') {
              eF_deleteTableData("module_rss_provider", "id=".$_GET['delete_feed']);
             } else {
                 eF_deleteTableData("module_rss_feeds", "id=".$_GET['delete_feed']);
             }
            } catch (Exception $e) {
             handleAjaxExceptions($e);
            }
            exit;
        } elseif (isset($_GET['deactivate_feed']) && eF_checkParameter($_GET['deactivate_feed'], 'id')) {
            try {
             if ($_GET['type'] == 'provider') {
              eF_updateTableData("module_rss_provider", array("active" => 0), "id=".$_GET['deactivate_feed']);
             } else {
              eF_updateTableData("module_rss_feeds", array("active" => 0), "id=".$_GET['deactivate_feed']);
             }
             echo 0;
            } catch (Exception $e) {
             handleAjaxExceptions($e);
            }
            exit;
        } elseif (isset($_GET['activate_feed']) && eF_checkParameter($_GET['activate_feed'], 'file')) {
            //Although db operations do not support exceptions (yet), we leave this here for future support
            try {
             if ($_GET['type'] == 'provider') {
              eF_updateTableData("module_rss_provider", array("active" => 1), "id=".$_GET['activate_feed']);
             } else {
              eF_updateTableData("module_rss_feeds", array("active" => 1), "id=".$_GET['activate_feed']);
             }
             echo 1;
            } catch (Exception $e) {
             handleAjaxExceptions($e);
            }
            exit;
        } else if (isset($_GET['add_feed']) || (isset($_GET['edit_feed']) && eF_checkParameter($_GET['edit_feed'], 'id'))) {
         if ($_SESSION['s_lesson_user_type']) {
          $type = $_SESSION['s_lesson_user_type'];
         } else {
          $type = $this -> getCurrentUser() -> getType();
         }
         $smarty -> assign("T_RSS_USERTYPE", $type);

         $feeds = $this -> getFeeds();

            $lessons = array(-1 => _RSS_NONE, 0 => _ALLLESSONS);
            $result = EfrontLesson :: getLessons();
            foreach ($result as $key => $lesson) {
                $lessons[$key] = $lesson['name'];
            }

            isset($_GET['add_feed']) ? $postTarget = "&add_feed=1" : $postTarget = "&edit_feed=".$_GET['edit_feed'];
            $form = new HTML_QuickForm("add_feed_form", "post", $this -> moduleBaseUrl.$postTarget, "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
            $form -> addElement('text', 'title', _RSS_FEEDTITLE, 'class = "inputText"');
            $form -> addElement('text', 'url', _RSS_FEEDURL, 'class = "inputText"');
            $form -> addElement('select', 'lessons_ID', _LESSON, $lessons);
            if ($type != 'administrator' && $_SESSION['s_lessons_ID']) {
             $form -> setDefaults(array('lessons_ID' => $_SESSION['s_lessons_ID']));
             $form -> freeze(array('lessons_ID'));
            }

            $form -> addElement("advcheckbox", "active", _RSS_ACTIVE, null, 'class = "inputCheckBox"', array(0, 1));
            $form -> setDefaults(array('active' => 1));
            $form -> addElement("advcheckbox", "only_summary", _RSS_ONLYSUMMARY, null, 'class = "inputCheckBox"', array(0, 1));
            $form -> addRule('title', _THEFIELD.' "'._RSS_FEEDTITLE.'" '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('url', _THEFIELD.' "'._RSS_FEEDURL.'" '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('title', _INVALIDFIELDDATA, 'checkParameter', 'text');

            $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
            if (isset($_GET['edit_feed'])) {
                $editFeed = $feeds[$_GET['edit_feed']];
                $form -> setDefaults($editFeed);
            }

            if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
                $values = $form -> exportValues();
                $fields = array("title" => $values['title'],
                    "url" => $values['url'],
                    "active" => $values['active'],
                    "only_summary" => $values['only_summary'],
                    "lessons_ID" => $values['lessons_ID']);

                if (isset($_GET['add_feed'])) {
                    eF_insertTableData("module_rss_feeds", $fields);
                    $smarty -> assign("T_RSS_RSS_MESSAGE", _RSS_SUCCESSFULLYADDEDFEED);
                } else {
                    eF_updateTableData("module_rss_feeds", $fields, "id=".$_GET['edit_feed']);
                    $smarty -> assign("T_RSS_RSS_MESSAGE", _RSS_SUCCESSFULLYEDITEDFEED);
                    Cache::resetCache('rss_cache:'.$_GET['edit_feed']);
                }
            }
            $smarty -> assign("T_RSS_ADD_RSS_FORM", $form -> toArray());
        } else if (isset($_GET['add_feed_provider']) || (isset($_GET['edit_feed_provider']) && eF_checkParameter($_GET['edit_feed_provider'], 'id'))) {
         if ($_SESSION['s_lesson_user_type']) {
          $type = $_SESSION['s_lesson_user_type'];
         } else {
          $type = $this -> getCurrentUser() -> getType();
         }
         $smarty -> assign("T_RSS_USERTYPE", $type);

         $feeds = $this -> getProvidedFeeds();

         isset($_GET['add_feed_provider']) ? $postTarget = "&add_feed_provider=1" : $postTarget = "&edit_feed_provider=".$_GET['edit_feed_provider'];
   !isset($_GET['lesson']) OR $postTarget .= '&lesson=1';
            $form = new HTML_QuickForm("add_feed_provider_form", "post", $this -> moduleBaseUrl.$postTarget.'&tab=rss_provider', "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
   if ($_GET['lesson']) {
             $lessons = array(0 => _ALLLESSONS);
             $result = EfrontLesson :: getLessons();
             foreach ($result as $key => $lesson) {
                 $lessons[$key] = $lesson['name'];
             }
             $form -> addElement('select', 'feeds_provided', _RSS_PROVIDEDFEEDS, $this->lessonProvidedFeeds);
             $form -> addElement('select', 'lessons_ID', _LESSON, $lessons);
             if ($type != 'administrator' && $_SESSION['s_lessons_ID']) {
              $form -> setDefaults(array('lessons_ID' => $_SESSION['s_lessons_ID']));
              $form -> freeze(array('lessons_ID'));
             }
   } else {
    $form -> addElement('select', 'feeds_provided', _RSS_PROVIDEDFEEDS, $this->providedFeeds);
   }

            $form -> addElement("advcheckbox", "active", _RSS_ACTIVE, null, 'class = "inputCheckBox"', array(0, 1));
            $form -> setDefaults(array('active' => 1));

            $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
            if (isset($_GET['edit_feed_provider'])) {
                $editFeed = $feeds[$_GET['edit_feed_provider']];
                $form -> setDefaults($editFeed);
            }

            try {
             if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
              $values = $form -> exportValues();
              $fields = array("mode" => $_GET['lesson'] ? 'lesson' : 'system',
                    "type" => $values['feeds_provided'],
                    "active" => $values['active'],
                    "lessons_ID" => $values['lessons_ID']);

              foreach ($feeds as $feed) {
               if ($feed['type'] == $fields['type'] && $feed['mode'] == $fields['mode'] && $feed['lessons_ID'] == $fields['lessons_ID']) {
                throw new Exception(_FEEDALREADYEXISTS);
               }
              }

              if (isset($_GET['add_feed_provider'])) {
               eF_insertTableData("module_rss_provider", $fields);
               $smarty -> assign("T_RSS_RSS_MESSAGE", _RSS_SUCCESSFULLYADDEDFEED);
              } else {
               eF_updateTableData("module_rss_provider", $fields, "id=".$_GET['edit_feed_provider']);
               $smarty -> assign("T_RSS_RSS_MESSAGE", _RSS_SUCCESSFULLYEDITEDFEED);
              }
             }
            } catch (Exception $e) {
             $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
             $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
             $message_type = 'failure';
            }
            $smarty -> assign("T_RSS_PROVIDE_RSS_FORM", $form -> toArray());
        } else {
            if (isset($_GET['ajax'])) {
    echo $this -> getRssFeeds($_GET['refresh']);
    //echo $this -> getRssFeeds(true);
    exit;
            } else {
             $lessons = array(0 => _ALLLESSONS);
          $result = EfrontLesson :: getLessons();
          foreach ($result as $key => $lesson) {
              $lessons[$key] = $lesson['name'];
          }

             $smarty -> assign("T_LESSON_NAMES", $lessons);
    if ($_SESSION['s_lesson_user_type']) {
     $type = $_SESSION['s_lesson_user_type'];
     $smarty -> assign("T_RSS_PROVIDED_FEEDS", $this -> getProvidedFeeds($_SESSION['s_lessons_ID']));
              $smarty -> assign("T_RSS_FEEDS", $this -> getFeeds(false, $_SESSION['s_lessons_ID']));
    } else {
     $type = $this -> getCurrentUser() -> getType();
     $smarty -> assign("T_RSS_PROVIDED_FEEDS", $this -> getProvidedFeeds());
              $smarty -> assign("T_RSS_FEEDS", $this -> getFeeds());
    }

    $smarty -> assign("T_RSS_USERTYPE", $type);
            }
        }

        $this -> setMessageVar($message, $message_type);

        return $this -> moduleBaseDir . "module_rss.tpl";

    }

    private function getRssFeeds($refresh = false) {
     $feedTitle = '';
     $feeds = $this -> getFeeds(true);
     foreach ($feeds as $key => $feed) {
      $str = '';
      if ($feed['lessons_ID'] && $_SESSION['s_lessons_ID'] && $feed['lessons_ID'] != $_SESSION['s_lessons_ID']) {
       unset ($feeds[$key]);
      } else {
       if (!$refresh && $str = Cache::getCache('rss_cache:'.$key)) {
        $rssStrings[] = $str;
       } else {
        if ($feed['title'] != $feedTitle) {
         $feedTitle = '<li style = "display:none;font-weight:bold" onmouseover = "pauseList()" onmouseout = "continueList()"> '._RSS_NEWSFROM.' &quot;<span style = "font-style:italic">'.$feed['title'].'</span>&quot;</li>';
         $str .= $feedTitle;
        }
        $response = $this -> parseFeed($feed);
        foreach ($response as $value) {
         if (!$feed['only_summary']) {
          $description = strip_tags($value['description']);
          if (mb_strlen($description) > 100) {
           $description = mb_substr($description, 0, 100).'...';
          }
          $description = '<div style = "font-style:italic;margin-left:10px">'.$description.'</div>';
         }
         $str .= '<li style = "display:none" onmouseover = "pauseList()" onmouseout = "continueList()"> '.formatTimestamp($value['timestamp']).' <a href = "'.$value['link'].'" target = "_NEW">'.$value['title'].'</a>'.$description.'</li>';
        }
        $rssStrings[] = $str;

        Cache::setCache('rss_cache:'.$key, $str, 3600*24);
       }
      }
     }
     $rssString = implode("", $rssStrings);

     return $rssString;
    }

    public function getCenterLinkInfo() {
        $optionArray = array('title' => 'RSS',
                             'image' => $this -> moduleBaseLink.'images/rss32.png',
                             'link' => $this -> moduleBaseUrl);
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
            return array (array ('title' => _HOME, 'link' => $currentUser -> getType() . ".php?ctg=control_panel"),
                          array ('title' => _RSS_RSS, 'link' => $this -> moduleBaseUrl));
        } else {
   $currentLesson = $this -> getCurrentLesson();
            return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
       array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getType() . ".php?ctg=control_panel"),
       array ('title' => _RSS_RSS, 'link' => $this -> moduleBaseUrl));
        }
    }

    public function getControlPanelModule() {
        return true;
    }

    public function getControlPanelSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_RSS_MODULE_BASEURL", $this -> moduleBaseUrl);
  //pr($this -> moduleBaseLink);
        $smarty -> assign("T_RSS_MODULE_BASELINK", $this -> moduleBaseLink);

        $options[] = array('text' => _SHOWALL, 'image' => $this -> moduleBaseLink."images/arrow_down_blue.png", 'href' => "javascript:void(0)", onClick => "showHideAll(this)");
        $options[] = array('text' => _RSS_REFRESH, 'image' => $this -> moduleBaseLink."images/refresh.png", 'href' => "javascript:void(0)", onClick => "getFeeds(true)");
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
            $iterator = new SimpleXMLIterator($xmlString);
            foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator :: SELF_FIRST) as $key => $value) {
                if ($key == 'item') {
                    $data = array('title' => (string)$value -> title, 'link' => (string)$value -> link, 'description' => (string)$value -> description);
                    if ($value -> pubDate) {
                     $data['timestamp'] = strtotime((string)$value -> pubDate);
                    } else if ($value -> date) {
                     $data['timestamp'] = (string)$value -> date;
                    }
                    $rss[] = $data;
                }
            }
        } catch (Exception $e) {
            $rss[] = array('title' => '<span class = "emptyCategory">'._CONNECTIONERROR.'</span>', 'link' => 'javascript:void(0)');
        }
        $rss = array_slice($rss, 0, 5);

        return $rss;
    }

    public function getFeeds($onlyActive = false, $lessonId = false) {
        if ($onlyActive) {
            $result = eF_getTableData("module_rss_feeds", "*", "active=1");
        } else {
            $result = eF_getTableData("module_rss_feeds", "*");
        }
        $feeds = array();

        foreach ($result as $value) {
         if (!$lessonId || ($value['lessons_ID'] == $lessonId) || ($value['lessons_ID'] == 0 && $value['active'])) {
             $feeds[$value['id']] = $value;
         }
        }

        return $feeds;
    }

    public function getProvidedFeeds($lessonId = false) {
     try {
      $result = eF_getTableData("module_rss_provider", "*");
     } catch (Exception $e) {
      $this -> onUpgrade();
      $result = eF_getTableData("module_rss_provider", "*");
     }
        $feeds = array();
        foreach ($result as $key => $value) {
         if (!$lessonId || $value['lessons_ID'] == $lessonId || ($value['lessons_ID'] === '0' && $value['active'])) {
             $feeds[$value['id']] = $value;
         }
        }

        return $feeds;
    }

    public function createRssFeed($source, $mode, $lesson) {
     $data = $this -> getRssSource($source, $mode, $lesson);
     $rss = $this -> createEnvelop($data);
     $this -> showRss($rss);
    }

    private function getRssSource($source, $mode, $lesson) {
     $feeds = $this -> getProvidedFeeds();
     foreach ($feeds as $value) {
      if ($value['active'] && $value['mode'] == 'system') {
       $systemFeeds[$value['type']] = $value;
      } else if ($value['active'] && $value['mode'] == 'lesson') {
       $lessonFeeds[$value['type']] = $value;
      }
     }

     if ($mode == 'system' && !in_array($source, array_keys($systemFeeds))) {
      return array();
     } elseif ($mode == 'lesson' && !in_array($source, array_keys($lessonFeeds))) {
      return array();
     }

     $data = array();
     switch ($source) {
      case 'announcements':
       if ($mode == 'system') {
        $news = news::getNews(0, true);
       } elseif ($mode == 'lesson') {
        if ($lesson) {
      $news = news :: getNews($lesson, true);
        } else {
         $lessons = eF_getTableDataFlat("lessons", "id, name");
         $lessonNames = array_combine($lessons['id'], $lessons['name']);
         $news = news :: getNews($lessons['id'], true);
        }
       }
       $count = 1;
       foreach ($news as $value) {
        if ($mode == 'lesson' && !$lesson) {
         $value['title'] = $lessonNames[$value['lessons_ID']].': '.$value['title'];
         $link = G_SERVERNAME.'userpage.php?lessons_ID='.$value['lessons_ID'].'&amp;ctg=news&amp;view='.$value['id'];
        } else {
         $link = G_SERVERNAME.'userpage.php?ctg=news&amp;view='.$value['id'];
        }
        $data[] = array('title' => $value['title'],
               'link' => $link,
              'description' => $value['data']);
/*

    				if ($count++ == $this -> feedLimit) {

    					break;

    				}

*/
       }
       break;
      case 'catalog':
       $constraints = array("return_objects" => false, 'archive' => false, 'active' => true);
       $result = EfrontCourse :: getAllCourses($constraints);
       $directionsTree = new EfrontDirectionsTree();
       $directionPaths = $directionsTree -> toPathString();
       foreach ($result as $value) {
        $pathString = $directionPaths[$value['directions_ID']].'&nbsp;&rarr;&nbsp;'.$value['name'];
        $data[] = array('title' => $pathString,
              'link' => G_SERVERNAME.'index.php?ctg=lesson_info&amp;courses_ID='.$value['id'],
              'description' => implode("<br>", unserialize($value['info'])));
       }
       $result = eF_getTableData("lessons", "id,name,directions_ID, info","archive=0 and instance_source = 0 and active=1 and course_only=0", "name");
       foreach ($result as $value) {
        $pathString = $directionPaths[$value['directions_ID']].'&nbsp;&rarr;&nbsp;'.$value['name'];
        $data[] = array('title' => $pathString,
              'link' => G_SERVERNAME.'index.php?ctg=lesson_info&amp;lessons_ID='.$value['id'],
              'description' => implode("<br>", unserialize($value['info'])));
       }
       $data = array_values(eF_multisort($data, 'title', 'asc')); //Sort results based on path string
       break;
      case 'calendar':
       if ($mode == 'system') {
        $result = eF_getTableData("calendar c LEFT OUTER JOIN lessons l ON c.lessons_ID = l.ID", "c.id, c.timestamp, c.data, l.name, c.users_login", "c.active=1", "timestamp desc");
       } elseif ($mode == 'lesson') {
        if ($lesson) {
         $result = eF_getTableData("calendar c LEFT OUTER JOIN lessons l ON c.lessons_ID = l.ID", "c.id, c.timestamp, c.data, l.name, c.users_login", "c.active=1 and l.id=".$lesson, "timestamp desc");
        } else {
         $lessons = eF_getTableDataFlat("lessons", "id, name");
         //$lessonNames = array_combine($lessons['id'], $lessons['name']);
         $result = eF_getTableData("calendar c LEFT OUTER JOIN lessons l ON c.lessons_ID = l.ID", "c.id, c.timestamp, c.data, l.name, c.users_login", "c.active=1 and l.id in (".implode(",", $lessons['id']).")", "timestamp desc");
        }
       }

    foreach ($result as $value) {
     $value['name'] ? $title = formatTimestamp($value['timestamp']).' ('.$value['name'].')' : $title = formatTimestamp($value['timestamp']);
        $data[] = array('title' => $title,
              'link' => G_SERVERNAME.'userpage.php?ctg=calendar&amp;view_calendar='.$value['timestamp'].'&amp;type=0',
              'description' => $value['data']);
    }

       break;
/*

    		case 'history':

    			$currentUser = $this -> getCurrentUser();



				$eventObjects = array();

    			$result = eF_getTableData("events", "*", "", "timestamp DESC limit 100");

				foreach ($result as $value) {

					$eventObject = new EfrontEvent($value);

					$eventObject -> createMessage();

					pr($eventObject);

				}



    			break;

*/
      case 'structure':
       if ($lesson) {
        $contentTree = new EfrontContentTree($lesson);
        $contentPath = $contentTree -> toPathStrings();
        foreach ($contentPath as $key => $value) {
         $data[] = array('title' => $value,
               'link' => G_SERVERNAME.'userpage.php?lessons_ID='.$lesson.'&amp;unit='.$key,
               'description' => $value);
        }
       }
       break;
      case 'forum':
       if ($mode == 'system') {
        $result = eF_getTableData("f_messages fm JOIN f_topics ft JOIN f_forums ff LEFT OUTER JOIN lessons l ON ff.lessons_ID = l.id", "ff.title as forum_name, fm.body, fm.title, fm.id, ft.id as topic_id, fm.users_LOGIN, fm.timestamp, l.name as lessons_name, lessons_id as show_lessons_id", "ft.f_forums_ID=ff.id AND fm.f_topics_ID=ft.id ", "fm.timestamp desc LIMIT 100");
       } elseif ($mode == 'lesson') {
        if ($lesson) {
         $result = eF_getTableData("f_messages fm JOIN f_topics ft JOIN f_forums ff LEFT OUTER JOIN lessons l ON ff.lessons_ID = l.id", "ff.title as forum_name, fm.body, fm.title, fm.id, ft.id as topic_id, fm.users_LOGIN, fm.timestamp, l.name as lessons_name, lessons_id as show_lessons_id", "ft.f_forums_ID=ff.id AND fm.f_topics_ID=ft.id AND ff.lessons_ID = '".$lesson."'", "fm.timestamp desc LIMIT 100");
        } else {
         $result = eF_getTableData("f_messages fm JOIN f_topics ft JOIN f_forums ff LEFT OUTER JOIN lessons l ON ff.lessons_ID = l.id", "ff.title as forum_name, fm.body, fm.title, fm.id, ft.id as topic_id, fm.users_LOGIN, fm.timestamp, l.name as lessons_name, lessons_id as show_lessons_id", "ft.f_forums_ID=ff.id AND fm.f_topics_ID=ft.id AND ff.lessons_ID != 0", "fm.timestamp desc LIMIT 100");
        }
       }
       foreach ($result as $value) {
     $value['title'] = $value['forum_name'].' >> '.$value['title'];
        if (($mode == 'system' && $value['lessons_name']) || ($mode == 'lesson' && !$lesson)) {
         $value['title'] = $value['lessons_name'].': '.$value['title'];
        }
        $data[] = array('title' => $value['title'],
              'link' => G_SERVERNAME.'userpage.php?ctg=forum&amp;topic='.$value['topic_id'],
              'description' => $value['body']);
       }
       break;
      default:
       break;
     }
     return $data;
    }
    private function createEnvelop($data) {
  $xml = '';
  foreach ($data as $value) {
   $xml .= '
   <item>
    <title><![CDATA['. $value["title"] .']]></title>
    <link>'. $value["link"] .'</link>
    <description><![CDATA['. $value["description"] .']]></description>
    <date>'.time().'</date>
   </item>';
  }
  return $xml;
 }
 private function showRss($rss) {
  $rss = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">
 <channel>
  <title>'. $GLOBALS['configuration']['site_name'] .'</title>
  <link>'. G_SERVERNAME .'</link>
  <description>'. $GLOBALS['configuration']['site_motto'] .'</description>
  <language>'. $GLOBALS['configuration']['default_language'] .'</language>
  '.$rss.'
 </channel>
</rss>';
  header("Content-Type: application/xml; charset="._CHARSET);
  echo $rss;
  exit;
 }
}
?>
