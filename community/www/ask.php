<?php
/**

 * Respond to ajax query returing a list

 *

 * @package eFront

 */
session_cache_limiter('none');
session_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$path = "../libraries/";
/** Configuration file.*/
include_once $path."configuration.php";


try {
 $currentUser = EfrontUser :: checkUserAccess();
} catch (Exception $e) {
 echo "<script>parent.location = 'index.php?message=".urlencode($e -> getMessage().' ('.$e -> getCode().')')."&message_type=failure'</script>"; //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
 exit;
}


switch ($_GET['ask_type']) {
 case 'users': askUsers(); break;
 case 'tests': askTests(); break;
 case 'feedback': askFeedback(); break;
 case 'projects': askProjects(); break;
 case 'lessons': askLessons(); break;
 case 'groups': askGroups(); break;
 case 'courses': askCourses(); break;
 //case 'chat': 		askChat();		  break;
 case 'branches': askBranches(); break;
 //case 'information': askInformation(); break;

 default: break;
}

function highlightSearch($search_results, $search_criteria, $bgcolor='Yellow'){
 $start_tag = '<span style="background-color: '.$bgcolor.'">';
 $end_tag = '</span>';
 $search_results = str_ireplace($search_criteria, $start_tag . $search_criteria . $end_tag, $search_results);
 return $search_results;
}

function askUsers() {
//	$_POST['preffix'] = "%";	// Useful for debugging
 if (isset($_POST['preffix'])) {
  if (mb_strpos($_POST['preffix'], ";") === false) {
   $user = $_POST['preffix'];
  } else {
   $user = mb_substr(strrchr($_POST['preffix'], ";"), 1);
  }
 }
 //pr($_SESSION);
 $users = array();
 if (isset($user) && $user) {
  $preffix = $user;

  // Return active users for statistics:
  // - admins: all
  // - supervisors: all supervised (in Enterprise)
  // - professors: students 
  if (!isset($_GET['messaging'])) {
   if ($_SESSION['s_type'] == "administrator") {
    $users = eF_getTableData("users", "login,name,surname,user_type,user_types_ID", "active = 1 and (login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%' OR user_type like '$preffix%')", "login");
   } else {
    // Get students of professor
    $user = EfrontUserFactory :: factory($_SESSION['s_login']);
    if (!$_SESSION['s_lessons_ID']) {
     $students = $user -> getProfessorStudents();
    } else {
     $lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
     $students = array_keys($lesson -> getUsers('student'));
    }

    $logins = array();
    $size = sizeof($students);
    for ($i = 0; $i < $size; $i++) {
     if (!in_array($students[$i], $logins)){
      $logins[] = $students[$i];
     }
    }
    $logins[] = $_SESSION['s_login'];
    //pr($logins);
    $students_list = "'".implode("','", $logins)."'";
    $users = eF_getTableData("users", "login,name,surname,user_type,user_types_ID", "login IN ($students_list) AND (login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%' OR user_type like '$preffix%')", "login");
   }
  // Return active users for messaging:
  // - admins: all
  // - supervisors: all
  // - users: other users with common group, lesson, course (or branch in Enterprise)			
  } else {
   if ($_SESSION['s_type'] == "administrator") {
    $users = eF_getTableData("users", "login,name,surname,user_type,user_types_ID", "login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%'", "login");
    $users[] = array('login' => "[*]",'name' => _ALLUSERS, 'surname' => _ALLUSERS);
   } else {
    $currentUser = EfrontUserFactory::factory($_SESSION['s_login']);
    $grant_full_access = false;
    if (!$grant_full_access) { // Used for correct handling in Enterprise and non-Enterprise editions
     $logins = array();
     $myGroupsIds = array_keys($currentUser -> getGroups());
//					echo "Groups<BR><BR><BR>";pr($myGroupsIds);
     if (!empty($myGroupsIds)) {
      $result = eF_getTableDataFlat("users JOIN users_to_groups", "distinct users_LOGIN", "users.login = users_to_groups.users_LOGIN AND groups_ID IN ('" . implode("','", $myGroupsIds) ."')");
      $logins = $result['users_LOGIN'];
     }
     $myLessonsIds = array_keys($currentUser -> getLessons());
//					pr($result);echo "Lessons<BR><BR><BR>";pr($myLessonsIds);
     if (!empty($myLessonsIds)) {
      $result = eF_getTableDataFlat("users JOIN users_to_lessons", "distinct users_LOGIN", "users.archive=0 and users_to_lessons.archive=0 and users.login = users_to_lessons.users_LOGIN AND lessons_ID IN ('" . implode("','", $myLessonsIds) ."')");
//						pr($result);
      foreach($result['users_LOGIN'] as $login) {
       if (!in_array($login, $logins)){
        $logins[] = $login;
       }
      }
     }
     $myCoursesIds = eF_getTableDataFlat("users_to_courses", "courses_ID", "users_LOGIN = '". $currentUser -> user['login']."'");
     $myCoursesIds = $myCoursesIds['courses_ID'];
//					echo "Courses<BR><BR><BR>";pr($myCoursesIds);
     if (!empty($myCoursesIds)) {
      $result = eF_getTableDataFlat("users JOIN users_to_courses", "distinct users_LOGIN", "users.login = users_to_courses.users_LOGIN AND courses_ID IN ('" . implode("','", $myCoursesIds) ."')");
//						pr($result);
      foreach($result['users_LOGIN'] as $login) {
       if (!in_array($login, $logins)){
        $logins[] = $login;
       }
      }
     }
//					pr($logins);
     $related_users_list = "'".implode("','", $logins)."'";
     $users = eF_getTableData("users", "distinct login,name,surname,user_type,user_types_ID", "(login IN (". $related_users_list . ") OR user_type <> 'student') AND (login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%')", "login");
    } else {
     $users = eF_getTableData("users", "distinct login,name,surname,user_type,user_types_ID", "login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%'", "login");
    }
   }
   if($_SESSION['s_type'] == "professor"){
    $users[] = array('login' => "[*]",'name' => _MYSTUDENTS, 'surname' => _MYSTUDENTS, 'user_type' => '[*]');
   }
   //pr($users);
  }
 }
 $str = '<ul>';
 for ($k = 0; $k < sizeof($users); $k++){
  /*$hilogin = highlightSearch($users[$k]['login'], $preffix);

		 $hiname = highlightSearch($users[$k]['name'], $preffix);

		 $hisurname = highlightSearch($users[$k]['surname'], $preffix);  */
  $hilogin = $users[$k]['login'];
  $hiname = $users[$k]['name'];
  $hisurname = $users[$k]['surname'];
  $hiusertype = $users[$k]['user_types_ID'] ? $users[$k]['user_types_ID'] : $users[$k]['user_type'];
  if ($users[$k]['login'] == '[*]') {
   $formattedLogins[$users[$k]['login']] = $hiname;
  } else {
   $formattedLogins[$users[$k]['login']] = formatLogin(false, array('login' => $hilogin, 'name' => $hiname, 'surname' => $hisurname, 'user_type' => $hiusertype));
  }
  //$str = $str.'<li id='.$users[$k]['login'].'>'.$formattedLogin.'</li>';
 }
 if ($GLOBALS['configuration']['username_format_resolve']) {
  $common = array_diff_assoc($formattedLogins, array_unique($formattedLogins));
  foreach ($common as $key => $value) {
   $originalKey = array_search($value, $formattedLogins);
   $formattedLogins[$originalKey] = $value.' ('.$originalKey.')';
   $formattedLogins[$key] = $value.' ('.$key.')';
  }
 }
 for ($k = 0; $k < sizeof($users); $k++){
  $str = $str.'<li id='.$users[$k]['login'].'>'.$formattedLogins[$users[$k]['login']].'</li>';
 }
 $str = $str.'</ul>';
 echo $str;
}
function askTests() {
 $preffix = $_POST['preffix'];
 $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
 if ($_SESSION['s_type'] == "administrator"){
  $tests_info = eF_getTableDataFlat("tests t,   lessons l", "t.id, t.name as test_name, l.name as lesson_name, l.originating_course ","t.active=1 and t.lessons_ID = l.id AND t.name like '%$preffix%'", "t.name");
  $scorm_tests_info = eF_getTableDataFlat("content c, lessons l", "c.id, c.name as test_name, l.name as lesson_name, l.originating_course ","c.active=1 and c.lessons_ID = l.id AND c.name like '%$preffix%' and c.ctg_type = 'scorm_test'", "c.name");
 } else {
  $tests_info = eF_getTableDataFlat("tests t,   users_to_lessons ul, lessons l", "t.id, t.name as test_name, l.name as lesson_name, l.originating_course ", "ul.archive=0 and (ul.user_type = 'professor' OR ul.user_type =".$currentUser->user['user_types_ID'].") AND t.active=1 and t.lessons_ID = l.id AND ul.users_LOGIN='".$_SESSION['s_login']."' and ul.lessons_ID=l.id AND t.name like '%$preffix%'", "t.name");
  $scorm_tests_info = eF_getTableDataFlat("content c, users_to_lessons ul, lessons l", "c.id, c.name as test_name, l.name as lesson_name, l.originating_course ", "ul.archive=0 and (ul.user_type = 'professor' OR ul.user_type =".$currentUser->user['user_types_ID'].") AND c.active=1 and c.lessons_ID = l.id AND ul.users_LOGIN='".$_SESSION['s_login']."' and ul.lessons_ID=l.id AND c.name like '%$preffix%' and c.ctg_type = 'scorm_test'", "c.name");
  $lessons = $currentUser -> getLessons(false,'professor'); //must return tests for lessons that he has a professor role
  $lessons = array_keys($lessons);
  if (!empty($lessons)) {
   $lessonsStr = implode(',', $lessons);
   $legalTests = eF_getTableDataFlat("tests t, content c","t.id","t.content_ID=c.id AND c.ctg_type!='feedback' AND t.lessons_ID IN ($lessonsStr)");
   $legalTestsId = $legalTests['id'];
   $legalScormTests = eF_getTableDataFlat("content","id","lessons_ID IN ($lessonsStr)");
   $legalScormTestsId = $legalScormTests['id'];
  }
 }
 $result = eF_getTableDataFlat("courses", "id, name");
 if (!empty($result)) {
  $courseNames = array_combine($result['id'], $result['name']);
 } else {
  $courseNames = array();
 }
 $info_array = array();
 for ($i = 0 ; $i < sizeof($tests_info['test_name']) ; $i ++){
  $hiname = highlightSearch($tests_info['test_name'][$i], $preffix);
  $path_string = $tests_info['lesson_name'][$i]."&nbsp;&raquo;&nbsp;".$hiname;
  if ($courseNames[$tests_info['originating_course'][$i]]) {
   $path_string = $courseNames[$tests_info['originating_course'][$i]].'&nbsp;&raquo;&nbsp;'.$path_string;
  }
  if (empty($legalTestsId) || in_array($tests_info['id'][$i], $legalTestsId)) {
   $info_array[] = array('id' => $tests_info['id'][$i],'name' => $tests_info['test_name'][$i],'path_string' =>$path_string);
  }
 }
 for ($i = 0 ; $i < sizeof($scorm_tests_info['test_name']) ; $i ++){
  $hiname = highlightSearch($scorm_tests_info['test_name'][$i], $preffix);
  $path_string = $scorm_tests_info['lesson_name'][$i]."&nbsp;&raquo;&nbsp;".$hiname;
  if ($courseNames[$scorm_tests_info['originating_course'][$i]]) {
   $path_string = $courseNames[$scorm_tests_info['originating_course'][$i]].'&nbsp;&raquo;&nbsp;'.$path_string;
  }
  if (empty($legalScormTestsId) || in_array($scorm_tests_info['id'][$i], $legalScormTestsId)) {
   $info_array[] = array('id' => $scorm_tests_info['id'][$i],'name' => $scorm_tests_info['test_name'][$i],'path_string' =>$path_string);
  }
 }
 $str = '<ul>';
 for ($k = 0; $k < sizeof($info_array); $k++){
  $str = $str.'<li id='.$info_array[$k]['id'].'>'.$info_array[$k]['path_string'].'</li>';
 }
 $str = $str.'</ul>';
 echo $str;
}
function askFeedback() {
 $preffix = $_POST['preffix'];
 $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
 if ($_SESSION['s_type'] == "administrator"){
  $tests_info = eF_getTableDataFlat("tests t,   lessons l", "t.id, t.name as test_name, l.name as lesson_name, l.originating_course ","t.active=1 and t.lessons_ID = l.id AND t.name like '%$preffix%'", "t.name");
  $legalTests = eF_getTableDataFlat("tests t, content c","t.id","t.content_ID=c.id AND c.ctg_type='feedback'");
  $legalTestsId = $legalTests['id'];
 } else {
  $tests_info = eF_getTableDataFlat("tests t,   users_to_lessons ul, lessons l", "t.id, t.name as test_name, l.name as lesson_name, l.originating_course ", "ul.archive=0 and (ul.user_type = 'professor' OR ul.user_type =".$currentUser->user['user_types_ID'].") AND t.active=1 and t.lessons_ID = l.id AND ul.users_LOGIN='".$_SESSION['s_login']."' and ul.lessons_ID=l.id AND t.name like '%$preffix%'", "t.name");
  $lessons = $currentUser -> getLessons(false,'professor'); //must return tests for lessons that he has a professor role
  $lessons = array_keys($lessons);
  if (!empty($lessons)) {
   $lessonsStr = implode(',', $lessons);
   $legalTests = eF_getTableDataFlat("tests t, content c","t.id","t.content_ID=c.id AND c.ctg_type='feedback' AND t.lessons_ID IN ($lessonsStr)");
   $legalTestsId = $legalTests['id'];
  }
 }
 $result = eF_getTableDataFlat("courses", "id, name");
 if (!empty($result)) {
  $courseNames = array_combine($result['id'], $result['name']);
 } else {
  $courseNames = array();
 }
 $info_array = array();
 for ($i = 0 ; $i < sizeof($tests_info['test_name']) ; $i ++){
  $hiname = highlightSearch($tests_info['test_name'][$i], $preffix);
  $path_string = $tests_info['lesson_name'][$i]."&nbsp;&raquo;&nbsp;".$hiname;
  if ($courseNames[$tests_info['originating_course'][$i]]) {
   $path_string = $courseNames[$tests_info['originating_course'][$i]].'&nbsp;&raquo;&nbsp;'.$path_string;
  }
  if (empty($legalTestsId) || in_array($tests_info['id'][$i], $legalTestsId)) {
   $info_array[] = array('id' => $tests_info['id'][$i],'name' => $tests_info['test_name'][$i],'path_string' =>$path_string);
  }
 }
 $str = '<ul>';
 for ($k = 0; $k < sizeof($info_array); $k++){
  $str = $str.'<li id='.$info_array[$k]['id'].'>'.$info_array[$k]['path_string'].'</li>';
 }
 $str = $str.'</ul>';
 echo $str;
}
function askSuggestions() {
 //header("Content-type: text/xml;charset=iso-8859-7");
 $ie = isset($_GET['ie']) ? true : false ;
 $search_results_data = array();
 $search_results_forum = array();
 $search_results_pmsgs = array();
 $results = EfrontSearch :: searchFull('');
 //$res     = eF_getTableData("users_to_lessons", "lessons_ID", "users_LOGIN='".$_SESSION['s_login']."'");
 $res = eF_getTableData("users_to_lessons,lessons", "lessons_ID", "users_to_lessons.archive=0 and lessons.archive=0 and users_LOGIN='".$_SESSION['s_login']."' and lessons.active=1 and lessons.id=users_to_lessons.lessons_ID"); // na min emfanizontai ta deactivated lessons
 for ($i = 0; $i < sizeof($res); $i++) {
  $lessons_have[] = $res[$i]['lessons_ID'];
 }
 $have_results = false;
 if ($results) {
  for ($i = 0; $i < sizeof($results); $i++) {
   if ($results[$i]['table_name'] == "comments") {
    $res1 = eF_getTableData("content,comments", "content.name AS name,content.id AS id,content.lessons_ID AS lessons_ID", "comments.content_ID=content.id AND comments.id=".$results[$i]['foreign_ID']);
    $type_str = _COMMENTS;
   } elseif ($results[$i]['table_name'] == "news") {
    $res1 = eF_getTableData($results[$i]['table_name'], "id,title AS name,lessons_ID", "id=".$results[$i]['foreign_ID']);
    $type_str = _ANNOUNCEMENTS;
   } elseif ($results[$i]['table_name'] == "content") {
    $res1 = eF_getTableData($results[$i]['table_name'], "id,name,lessons_ID,ctg_type", "id=".$results[$i]['foreign_ID']);
    $type_str = _LESSONCONTENT;
   } elseif ($results[$i]['table_name'] == "f_messages") {
    $res1 = eF_getTableData("f_messages, f_topics, f_forums", "f_forums.id as category_id, f_forums.lessons_ID, f_messages.id, f_messages.title, f_messages.f_topics_ID, f_topics.title as topic_title", "f_topics_ID = f_topics.id and f_forums.id = f_forums_ID and f_messages.id=".$results[$i]['foreign_ID']);
    $type_str = _MESSAGESATFORUM;
   } elseif ($results[$i]['table_name'] == "f_personal_messages") {
    $res1 = eF_getTableData("f_personal_messages, f_folders", "f_personal_messages.id, f_personal_messages.title, f_personal_messages.users_LOGIN, f_folders.name, f_folders.id as folder_id", "f_personal_messages.f_folders_ID = f_folders.id and f_personal_messages.id=".$results[$i]['foreign_ID']);
    $type_str = _MESSAGESATFORUM;
   }
   elseif ($results[$i]['table_name'] == "lessons") {
    $res1 = eF_getTableData($results[$i]['table_name'], "id as lessons_ID,name", "id=".$results[$i]['foreign_ID']." and active=1");
    $type_str = _LESSON;
   }
   elseif ($results[$i]['table_name'] == "f_topics") {
    $res1 = $res1 = eF_getTableData("f_messages, f_topics, f_forums", "f_forums.id as category_id, f_forums.lessons_ID, f_messages.id, f_messages.title, f_messages.f_topics_ID, f_topics.title as topic_title", "f_topics_ID = f_topics.id and f_forums.id = f_forums_ID and f_topics.id=".$results[$i]['foreign_ID']);
    $type_str = _MESSAGESATFORUM;
   }
   //print_r($res1);
   if (sizeof($res1) > 0) {
    $results[$i]['position'] == "title" ? $position_str = _TITLE : $position_str = _TEXT;
    if (isset($res1[0]['lessons_ID']) && in_array($res1[0]['lessons_ID'], $lessons_have)) {
     $lesson = eF_getTableData("lessons", "name", "id=".$res1[0]['lessons_ID']);
     if ($results[$i]['table_name'] != 'f_messages' && $results[$i]['table_name'] != 'f_topics') {
      if($results[$i]['table_name'] == "lessons"){
       $search_results_data[] = array('id' => $res1[0]['id'],
                                                               'name' => $res1[0]['name'],
                                                               'table_name' => $results[$i]['table_name'],
                                                               'lessons_ID' => $res1[0]['lessons_ID'],
                                                               'lesson_name' => $lesson[0]['name'],
                                                               'score' => sprintf("%.0f %%", $results[$i]['score'] * 100),
                                                               'type' => $type_str,
                                                               'position' => $position_str);
      }elseif ($results[$i]['table_name'] != "lessons" /*&& eF_isDoneContent($res1[0]['id'])*/) {
       //echo $res1[0]['id']."->".eF_isDoneContent($res1[0]['id']);
       $search_results_data[] = array('id' => $res1[0]['id'],
                                                               'name' => $res1[0]['name'],
                                                               'table_name' => $results[$i]['table_name'],
                                                               'lessons_ID' => $res1[0]['lessons_ID'],
                                                               'lesson_name' => $lesson[0]['name'],
                                                               'ctg_type' => $res1[0]['ctg_type'],
                                                               'score' => sprintf("%.0f %%", $results[$i]['score'] * 100),
                                                               'type' => $type_str,
                                                               'position' => $position_str);
      }
     } else {
      $search_results_forum[] = array('category_id' => $res1[0]['category_id'],
                                                        'lesson_name' => $lesson[0]['name'],
                                                        'topic_subject' => $res1[0]['topic_title'],
                                                        'topic_id' => $res1[0]['f_topics_ID'],
                                                        'message_subject' => $res1[0]['title'],
                                                        'message_id' => $res1[0]['id'],
                                                        'position' => $position_str);
     }
    } elseif ($results[$i]['table_name'] == 'f_personal_messages' && $_SESSION['s_login'] == $res1[0]['users_LOGIN']) {
     $search_results_pmsgs[] = array('message_subject' => $res1[0]['title'],
                                                    'message_id' => $res1[0]['id'],
                                                    'folder_name' => $res1[0]['name'],
                                                    'folder_id' => $res1[0]['folder_id'],
                                                    'position' => $position_str);
    }
   }
  }
 }
 echo "<?xml version=\"1.0\" ?>";
 echo "<root>";
 echo "<search_results_data>";
 foreach($search_results_data as $key => $value)
 {
  echo "<search_result_data>";
  echo "<id>".$value['id']."</id>";
  echo "<name>".$value['name']."</name>";
  echo "<table_name>".$value['table_name']."</table_name>";
  echo "<lessons_ID>".$value['lessons_ID']."</lessons_ID>";
  echo "<lesson_name>".$value['lesson_name']."</lesson_name>";
  echo "<score>".$value['score']."</score>";
  echo "<type>".$value['type']."</type>";
  echo "<position>".$value['position']."</position>";
  echo "</search_result_data>";
 }
 echo "</search_results_data>";
 /*

	 for($i=0;$i<sizeof($result);$i++)

	 {

	 $name = str_replace("&","&amp;",$result[$i]['name']);

	 $url = str_replace("&","&amp;",$result[$i]['url']);

	 $id = $result[$i]['id'];

	 echo "<bookmark>";

	 if($ie)

	 {

	 echo "<name>".$name."</name>";

	 echo "<url>".$url."</url>";

	 echo "<id>".$id."</id>";

	 }

	 else

	 {

	 echo "<name>".iconv("UTF-8","ISO-8859-7",$name)."</name>";

	 echo "<url>".iconv("UTF-8","ISO-8859-7",$url)."</url>";

	 echo "<id>".$id."</id>";

	 }

	 echo "</bookmark>";

	 }

	 echo "</bookmarks>";

	 }

	 */
 echo "</root>";
}
function askProjects() {
 $preffix = $_POST['preffix'];
 $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
 if($_SESSION['s_type'] == "administrator"){
  $projects_info = eF_getTableDataFlat("projects p, lessons l", "p.id, p.title as project_title, l.name as lesson_name ","p.lessons_ID = l.id AND p.title like '%$preffix%'", "p.title");
 } else {
  $projects_info = eF_getTableDataFlat("projects p, users_to_lessons ul, lessons l", "p.id, p.title as project_title, l.name as lesson_name ", "ul.archive=0 and (ul.user_type = 'professor' OR ul.user_type =".$currentUser->user['user_types_ID'].") AND p.lessons_ID = l.id AND ul.users_LOGIN='".$_SESSION['s_login']."' and ul.lessons_ID=l.id AND p.title like '%$preffix%'", "p.title");
 }
 $info_array = array();
 for($i = 0 ; $i < sizeof($projects_info['project_title']) ; $i ++){
  $hiname = highlightSearch($projects_info['project_title'][$i], $preffix);
  $path_string = $projects_info['lesson_name'][$i]."->".$hiname;
  $info_array[$i] = array('id' => $projects_info['id'][$i],'name' => $projects_info['project_title'][$i],'path_string' =>$path_string);
 }
 $str = '<ul>';
 for ($k = 0; $k < sizeof($info_array); $k++){
  $str = $str.'<li id='.$info_array[$k]['id'].'>'.$info_array[$k]['path_string'].'</li>';
 }
 $str = $str.'</ul>';
 echo $str;
}
function askLessons() {
 eF_checkParameter($_POST['preffix'], 'text') ? $preffix = $_POST['preffix'] : $preffix = '%';
 if ($_SESSION['s_type'] == "administrator"){
  $result = eF_getTableData("lessons", "id,name,directions_ID","archive=0 and instance_source = 0 and active=1 AND name like '%$preffix%'", "name");
 } else {
  $result = eF_getTableData("users_to_lessons ul, lessons l", "l.id, l.name,l.directions_ID", "ul.archive=0 and l.archive=0 and l.instance_source = 0 and ul.users_LOGIN='".$_SESSION['s_login']."' and ul.user_type = 'professor' and ul.lessons_ID=l.id AND l.name like '%$preffix%'", "l.name");
 }
 $lessons = array();
 $directionsTree = new EfrontDirectionsTree();
 $directionPaths = $directionsTree -> toPathString();
 for ($i = 0 ; $i < sizeof($result) ; $i ++) {
  $hiname = highlightSearch($result[$i]['name'], $preffix);
  $pathString = $directionPaths[$result[$i]['directions_ID']].'&nbsp;&rarr;&nbsp;'.$hiname;
  $lessons[$i] = array('id' => $result[$i]['id'],
          'name' => $result[$i]['name'],
          'path_string' => $pathString);
 }
 $lessons = array_values(eF_multisort($lessons, 'path_string', 'asc')); //Sort results based on path string
 $str = '<ul>';
 for ($k = 0; $k < sizeof($lessons); $k++){
  $str = $str.'<li id='.$lessons[$k]['id'].'>'.$lessons[$k]['path_string'].'</li>';
 }
 $str .= '</ul>';
 echo $str;
}
function askGroups() {
 eF_checkParameter($_POST['preffix'], 'text') ? $preffix = $_POST['preffix'] : $preffix = '%';
 if($_SESSION['s_type'] == "administrator"){
  $result = eF_getTableData("groups LEFT JOIN users_to_groups ON groups.id = users_to_groups.groups_ID", "id, name, description, count(users_LOGIN) as users_count","active=1 AND (name like '%$preffix%' OR description like '%$preffix%')", "", "groups_ID");
 }
 for ($i = 0 ; $i < sizeof($result) ; $i ++) {
  if ($result[$i]['description']) {
   $result[$i]['name'] .= "&nbsp;-" .$result[$i]['description'];
  }
  $result[$i]['name'] .= "&nbsp;(" . $result[$i]['users_count'] . ")";
  $hiname = highlightSearch($result[$i]['name'] , $preffix);
  $groups[$i] = array('id' => $result[$i]['id'],
          'name' => $result[$i]['name'],
          'path_string' => $hiname);
 }
 $groups = array_values(eF_multisort($groups, 'path_string', 'asc')); //Sort results based on path string
 $str = '<ul>';
 for ($k = 0; $k < sizeof($groups); $k++){
  $str = $str.'<li id='.$groups[$k]['id'].'>'.$groups[$k]['path_string'].'</li>';
 }
 $str .= '</ul>';
 echo $str;
}
function askCourses() {
 eF_checkParameter($_POST['preffix'], 'text') ? $preffix = $_POST['preffix'] : $preffix = '%';
 if ($_SESSION['s_type'] == "administrator") {
  //$result = eF_getTableData("courses", "id, name, directions_ID","active=1 AND name like '%$preffix%'");
  $constraints = array("return_objects" => false, 'archive' => false, 'active' => true, 'filter' => $preffix);
  $result = EfrontCourse :: getAllCourses($constraints);
  //$result 	 = EfrontCourse :: convertCourseObjectsToArrays($courses);
 } else {
  $result = eF_getTableData("courses c, users_to_courses uc", "c.id, c.name, c.directions_ID", "uc.user_type = 'professor' AND c.active=1 AND c.id = uc.courses_ID AND uc.users_LOGIN='".$_SESSION['s_login']."' AND c.name like '%$preffix%'");
 }
 $courses = array();
 $directionsTree = new EfrontDirectionsTree();
 $directionPaths = $directionsTree -> toPathString();
 foreach ($result as $value) {
  //for ($i = 0 ; $i < sizeof($result) ; $i ++) {
  $hiname = highlightSearch($value['name'], $preffix);
  $pathString = $directionPaths[$value['directions_ID']].'&nbsp;&rarr;&nbsp;'.$hiname;
  $courses[] = array('id' => $value['id'],
          'name' => $value['name'],
          'path_string' => $pathString);
 }
 $courses = array_values(eF_multisort($courses, 'path_string', 'asc')); //Sort results based on path string
 $str = '<ul>';
 for ($k = 0; $k < sizeof($courses); $k++){
  $str = $str.'<li id='.$courses[$k]['id'].'>'.$courses[$k]['path_string'].'</li>';
 }
 $str .= '</ul>';
 echo $str;
}
function askChat() {
 $special_splitter = "||||";
 if (isset($_GET['chatrooms_ID'])) {
  $chatrooms_ID = $_GET['chatrooms_ID'];
 } else if (isset($_GET['bring_chatrooms'])){
  // The chatrooms are all the ones with more than zero users and the ones you have created
  $rooms = eF_getTableData("chatrooms LEFT OUTER JOIN users_to_chatrooms ON users_to_chatrooms.chatrooms_ID = chatrooms.id", "chatrooms.id, chatrooms.name, count(users_to_chatrooms.users_LOGIN) as users, chatrooms.users_LOGIN", "chatrooms.active=1 group by id");
  //pr($rooms);
  $data = "";
  foreach ($rooms as $room) {
   if ($room['users'] > 0 || $room['users_LOGIN'] == $_SESSION['s_login']) {
    if ($room['users_LOGIN'] == $_SESSION['s_login']) {
     // The "_" after the room id means that the room is owned by the user that asked for it
     $data .= $room['id']. "_" . $special_splitter . $room['name'] .$special_splitter.$room['users']. $special_splitter;
    } else {
     $data .= $room['id']. $special_splitter . $room['name'] .$special_splitter.$room['users']. $special_splitter;
    }
   }
  }
  $data .= "-1";
  // pr($rooms);
  echo $data;
  exit;
 }
 // Delete a user from a chat room - this happens on room deactivations
 if (isset($_GET['delete_user'])) {
  eF_deleteTableData("users_to_chatrooms", "chatrooms_ID = '".$_GET['chatrooms_ID']."' AND users_LOGIN = '".$_GET['delete_user']."'");
  exit;
 }
 // Delete a chat room
 if (isset($_GET['delete_room'])) {
  // Security measures?
  // The id check is inserted for security reasons - only if this session user is the owner will the channel be deleted
  if (eF_deleteTableData("chatrooms", "users_LOGIN = '".$_SESSION['s_login']."' AND id = '".$_GET['chatrooms_ID']."'")) {
   eF_deleteTableData("users_to_chatrooms", "chatrooms_ID = '".$_GET['chatrooms_ID']."'");
   $_SESSION['last_chat_msg_id'] = 0;
  }
  exit;
 }
 // Insert into a new chatroom
 if (isset($_GET['add_user']) && isset($_GET['add_user_type'])) {
  eF_deleteTableData("users_to_chatrooms", "users_LOGIN = '".$_GET['add_user']."'");
  if ($_GET['chatrooms_ID'] != 0) {
   $userRecord = array("users_LOGIN" => $_GET['add_user'],
                         "chatrooms_ID" => $_GET['chatrooms_ID'] ,
                         "users_USER_TYPE" => $_GET['add_user_type'],
                         "timestamp" => time());
   eF_insertTableData("users_to_chatrooms", $userRecord);
  }
  // Set last_id to zero to get correct last messages
  $_SESSION['last_chat_msg_id'] = 0;
  exit;
 }
 // Get online users of current room
 if(isset($_GET['get_users'])) {
  // The room users of the eFront general room are all users-the ones currently logged in to another channel
  $data = "";
  if ($_GET['chatrooms_ID'] == 0) {
   // @performance: 2DB
   $all_users = eF_getTableDataFlat("users_online", "users_LOGIN");
   $other_room_users = eF_getTableDataFlat("users_to_chatrooms", "users_LOGIN", "");
   if (empty($other_room_users)) {
    $efront_general_users = $all_users['users_LOGIN'];
   } else {
    $efront_general_users = array_diff($all_users['users_LOGIN'], $other_room_users['users_LOGIN']);
   }
   foreach ($efront_general_users as $user) {
    $data .= $user . "<br>";
   }
  } else {
   // @performance: 1DB
   $users = eF_getTableData("users_to_chatrooms", "users_LOGIN", "chatrooms_ID = '".$_GET['chatrooms_ID']. "'");
   foreach ($users as $user) {
    $data .= $user['users_LOGIN'] . "<br>";
   }
  }
  echo $data;
  exit;
 }
 $messages_limit = 25;//The messages list limit
 $data = '';
 if (isset($_POST['submit']) || isset($_POST['chat_message']) ) { //The user posted a message. IF the user just pressed enter, in IE POST['submit'] is not set!!!!!, so we include the || clause...
  if ($_POST['chat_message']!='') {
   // Check existence of room
   if ($chatrooms_ID != 0) { // the eFront general room always exists
    $roomExists = eF_getTableData("chatrooms", "active", "id = ". $_GET['chatrooms_ID']);
    if (empty($roomExists)) {
     echo _CHATROOMDOESNOTEXIST_ERROR . $special_splitter; // notify user that room was deleted
     $_SESSION['last_chat_msg_id'] = 0;
     exit;
    } else if ($roomExists[0]['active'] == 0) {
     echo _CHATROOMISNOTENABLED_ERROR . $special_splitter; // notify user that room is not active
     $_SESSION['last_chat_msg_id'] = 0;
     exit;
    }
   }
   $fields_insert = array('users_LOGIN' => $_SESSION['s_login'],
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'content' => htmlspecialchars($_POST['chat_message'], ENT_QUOTES),
                               'timestamp' => time(),
                               'chatrooms_ID' => $chatrooms_ID);
   try {
    eF_insertTableData("chatmessages", $fields_insert); //Insert the message into the database
   } catch (Exception $e) {
    echo $e->getTraceAsString();
   }
  }
 }
 // Special treatment for the genaral eFront room
 $last_id = isset($_SESSION['last_chat_msg_id'])?$_SESSION['last_chat_msg_id']:0;
 /*

	 if ($last_id == 0 && $_GET['restart_session'] != 1) {

	 // Some asking message came in before the first one - return

	 exit;

	 }

	 */
 if ($last_id == 0 || $_GET['restart_session'] == 1) {
  $get_last_thirty_minutes = " AND timestamp > " . (time() - 1800); // 30*60 sec, last 30' minutes
  $last_id = 0;
 }
 if (!isset($_POST['chat_message'])) {
  $messages = eF_getTableData("chatmessages", "users_LOGIN, users_USER_TYPE, timestamp, content, id", "chatrooms_ID = $chatrooms_ID AND id > $last_id $get_last_thirty_minutes", "timestamp DESC,id DESC LIMIT $messages_limit"); //Retrieve the recent messages
  if (sizeof($messages)>0) {
   $new_id = $messages[0]['id'];
  } else {
   $new_id = $last_id;
  }
 } else {
  $messages = eF_getTableData("chatmessages", "users_LOGIN, users_USER_TYPE, timestamp, content, id", "chatrooms_ID = $chatrooms_ID AND id > $last_id $get_last_thirty_minutes", "timestamp DESC, id DESC"); //Retrieve the most recent message
  if (sizeof($messages) > 0) {
   $new_id = $messages[0]['id'];
  } else {
   $new_id = $sent;
  }
 }
 // If this was just a check for activity in the chat room return now
 if ($_GET['any_activity'] == 1) {
  if (sizeof($messages) > 0) {
   echo "ack";
   exit;
  } else {
   echo "noack";
   exit;
  }
 }
 $_SESSION['last_chat_msg_id'] = $new_id;
 //echo "ethesa to session iso me $new_id<br>";
 //    pr($messages);
 $new_msg = false;
 if ($_SESSION['last_message'][$chatrooms_ID] != $messages[0]['timestamp']) {
  $_SESSION['last_message'][$chatrooms_ID] = $messages[0]['timestamp'];
  $new_msg = array('user' => $messages[0]['users_LOGIN'], 'message' => $messages[0]['content']);
  if (mb_strlen($new_msg['message']) > 15) {
   $new_msg['message'] = mb_substr($new_msg['message'], 0, 25).'...';
  }
 }
 // The first value returned is the id of the chatroom
 $data = $chatrooms_ID.$special_splitter;
 foreach ($messages as $value) { //Loop through messages, so that they are displayed in different format, depending on the message poster
  if ($value['users_LOGIN'] == $_SESSION['s_login']) {
   $span_style = 'color:darkorange;'; //Own messages are displayed in darkorange
  } elseif ($value['users_USER_TYPE'] == 'professor') {
   $span_style = 'color:blue;'; //Professor messages are displayed in blue
  } elseif ($value['users_USER_TYPE'] == 'administrator') {
   $span_style = 'color:red;'; //Administrator messages are displayed in red
  } else {
   $span_style = 'color:green;'; //Other messages are displayed in default format
  }
  date("ymd", time()) == date("ymd", $value['timestamp']) ? $time_str = date("H:i:s", $value['timestamp']) : $time_str = formatTimestamp($value['timestamp'], 'time'); //for today's messages don't display date, only time.
  if ($value['content'][0] != '#' || !preg_match("/^#for_user-(\S*):(\d+)#/", $value['content'], $matches) || ($matches[1] == $_SESSION['s_login'] && $value['content'] = mb_substr($value['content'], mb_strlen($matches[0])))) { //Explanation for this line: The first part, $value['content'][0] != '#' , is a fast check for the special character #. if it does not exist, proceed and display message. Otherwise, check if the character is followed by a specific sequence, of the form: #for_user-<login>#. If so, then display only the message to the current user (where $_SESSION['s_login'] == <login>) and finally delete the special sequence from the beginning of the message (the mb_substr part). otherwise (if it starts with # but is not a special message), display the message.
   // Create links
   // Do it here for each icon
   $value['content'] = eF_convertTextToSmilies($value['content']);
   $value['content'] = eregi_replace("www[.]([^[:space:]]*)([[:alnum:]#?/&=])","http://www.\\1\\2", $value['content']);
   $value['content'] = eregi_replace("([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])","<a href=\"\\1://\\2\\3\" target=\"_blank\" > \\1://\\2\\3 </a> ", $value['content']);
   $data .= $value['users_LOGIN'].$special_splitter.$time_str.$special_splitter.$span_style.$special_splitter.$value['content'].$special_splitter; //Display the message, along with any notification message
  }
 }
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 //header("Content-type: text/html;charset="._CHARSET);
 //$data = eF_convertTextToSmilies($data);
 //$data = iconv("UTF-8",_CHARSET,$data);
 //echo $data.$special_splitter."new_limit: ".$new_limit."<br>all messages: ".$all_messages."<br>sent: ".$sent."<br>new_limit_flag: ".$new_limit_flag."-|*special_splitter*|-".$rooms_str."-|*special_splitter*|-".$new_limit."-|*special_splitter*|-".$sent;
 echo $data;
}
function askBranches() {
 include_once $path."module_hcd_tools.php";
 eF_checkParameter($_POST['preffix'], 'text') ? $preffix = $_POST['preffix'] : $preffix = '%';
 if ($_SESSION['s_type'] == "administrator") {
  $result = eF_getTableData("(module_hcd_branch LEFT OUTER JOIN (module_hcd_employee_works_at_branch JOIN users ON module_hcd_employee_works_at_branch.users_LOGIN = users.login) ON module_hcd_branch.branch_ID = module_hcd_employee_works_at_branch.branch_ID AND module_hcd_employee_works_at_branch.assigned = '1') LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID GROUP BY module_hcd_branch.branch_ID ORDER BY branch1.branch_ID", "module_hcd_branch.branch_ID, module_hcd_branch.name, module_hcd_branch.city, module_hcd_branch.address,  sum(CASE WHEN users.active=1 THEN 1 END) as employees, sum(CASE WHEN users.active=0 THEN 1 END) as inactive_employees, branch1.branch_ID as father_ID, branch1.name as father, supervisor","");
 } else {
  $result = eF_getTableData("(module_hcd_branch LEFT OUTER JOIN (module_hcd_employee_works_at_branch JOIN users ON module_hcd_employee_works_at_branch.users_LOGIN = users.login) ON module_hcd_branch.branch_ID = module_hcd_employee_works_at_branch.branch_ID AND module_hcd_employee_works_at_branch.assigned = '1') LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID WHERE module_hcd_branch.branch_ID IN (".$_SESSION['supervises_branches'].") GROUP BY module_hcd_branch.branch_ID ORDER BY branch1.branch_ID", "module_hcd_branch.name, module_hcd_branch.city, module_hcd_branch.address,  sum(CASE WHEN users.active=1 THEN 1 END) as employees, sum(CASE WHEN users.active=0 THEN 1 END) as inactive_employees,  module_hcd_branch.branch_ID, branch1.branch_ID as father_ID, branch1.name as father","");
 }
 $branches = array();
 foreach ($result as $value) {
  $branches[$value['branch_ID']] = $value;
 }
 $tree = new EfrontBranchesTree();
 foreach ($tree -> toPathString() as $key => $branch) {
  if (in_array($key, array_keys($branches))) {
   if ($preffix == '%' || stripos($branch, $preffix) !== false) {
    $hiname = highlightSearch(eF_truncatePath($branch, 80, 6, "...", "&nbsp;&rarr;&nbsp;"), $preffix);
    $branches[$key] = array('branch_ID' => $key,
            'name' => $branch,
            'path_string' => $hiname);
   }
  }
 }
 $str = '<ul>';
 foreach ($branches as $key => $branch) {
  $str = $str.'<li id='.$key.'>'.$branch['path_string'].'</li>';
 }
 $str .= '</ul>';
 echo $str;
}
function askInformation() {
 try {
  if (isset($_GET['lessons_ID']) && eF_checkParameter($_GET['lessons_ID'], 'id')) {
   $lesson = new EfrontLesson($_GET['lessons_ID']);
   $lessonInformation = $lesson -> getInformation();
   $languages = EfrontSystem::getLanguages(true);
   //$lessonInformation['language'] = $languages[$lesson -> lesson['languages_NAME']];
   if ($lessonInformation['professors']) {
    foreach ($lessonInformation['professors'] as $value) {
     $professorsString[] = $value['name'].' '.$value['surname'];
    }
    $lessonInformation['professors'] = implode(", ", $professorsString);
   }
   $lesson -> lesson['price'] ? $priceString = formatPrice($lesson -> lesson['price'], array($lesson -> options['recurring'], $lesson -> options['recurring_duration']), true) : $priceString = false;
   $lessonInformation['price_string'] = $priceString;
   //    if (!$lessonInformation['price']) {
   //        unset($lessonInformation['price_string']);
   //    }
   try {
    if ($_GET['from_course']) {
     $course = new EfrontCourse($_GET['from_course']);
     $schedule = $course -> getLessonScheduleInCourse($lesson);
     $lessonInformation['from_timestamp'] = $schedule['start_date'];
     $lessonInformation['to_timestamp'] = $schedule['end_date'];
    }
   } catch (Exception $e) {};
   foreach ($lessonInformation as $key => $value) {
    if ($value) {
     switch ($key) {
      case 'language' : $GLOBALS['configuration']['onelanguage'] OR $tooltipInfo[] = '<div class = "infoEntry"><span>'._LANGUAGE."</span><span>: $languages[$value]</span></div>"; break;
      case 'professors' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._PROFESSORS."</span><span>: $value</span></div>"; break;
      case 'content' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._CONTENTUNITS."</span><span>: $value</span></div>"; break;
      case 'tests' : $GLOBALS['configuration']['disable_tests'] != 1 ? $tooltipInfo[] = '<div class = "infoEntry"><span>'._TESTS."</span><span>: $value</span></div>" : null; break;
      case 'projects' : $GLOBALS['configuration']['disable_projects'] != 1 ? $tooltipInfo[] = '<div class = "infoEntry"><span>'._PROJECTS."</span><span>: $value</span></div>" : null; break;
      case 'course_dependency' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._DEPENDSON."</span><span>: $value</span></div>"; break;
      case 'from_timestamp' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._AVAILABLEFROM."</span><span>: ".formatTimestamp($value, 'time_nosec')."</span></div>";break;
      case 'to_timestamp' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._AVAILABLEUNTIL."</span><span>: ".formatTimestamp($value, 'time_nosec')."</span></div>"; break;
      case 'general_description': $tooltipInfo[] = '<div class = "infoEntry"><span>'._GENERALDESCRIPTION."</span><span>: $value</span></div>"; break;
      case 'assessment' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._ASSESSMENT."</span><span>: $value</span></div>"; break;
      case 'objectives' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._OBJECTIVES."</span><span>: $value</span></div>"; break;
      case 'lesson_topics' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._LESSONTOPICS."</span><span>: $value</span></div>"; break;
      case 'resources' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._RESOURCES."</span><span>: $value</span></div>"; break;
      case 'other_info' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._OTHERINFO."</span><span>: $value</span></div>"; break;
      case 'price_string' : !$lesson -> lesson['course_only'] ? $tooltipInfo[] = '<div class = "infoEntry"><span>'._PRICE."</span><span>: $value</span></div>" : null; break;
      default: break;
     }
    }
   }
   if ($string = implode("", $tooltipInfo)) {
    echo $string;
   } else {
    echo _NODATAFOUND;
   }
  } if (isset($_GET['courses_ID']) && eF_checkParameter($_GET['courses_ID'], 'id')) {
   $course = new EfrontCourse($_GET['courses_ID']);
   $courseInformation = $course -> getInformation();
   $languages = EfrontSystem::getLanguages(true);
   if ($courseInformation['professors']) {
    foreach ($courseInformation['professors'] as $value) {
     $professorsString[] = $value['name'].' '.$value['surname'];
    }
    $courseInformation['professors'] = implode(", ", $professorsString);
   }
   $course -> course['price'] ? $priceString = formatPrice($course -> course['price'], array($course -> options['recurring'], $course -> options['recurring_duration']), true) : $priceString = false;
   $courseInformation['price_string'] = $priceString;
   foreach ($courseInformation as $key => $value) {
    if ($value) {
     switch ($key) {
      case 'language' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._LANGUAGE."</span><span>: $languages[$value]</span></div>"; break;
      case 'professors' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._PROFESSORS."</span><span>: $value</span></div>"; break;
      case 'lessons_number' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._LESSONS."</span><span>: $value</span></div>"; break;
      case 'instances' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._COURSEINSTANCES."</span><span>: $value</span></div>"; break;
      case 'general_description': $tooltipInfo[] = '<div class = "infoEntry"><span>'._GENERALDESCRIPTION."</span><span>: $value</span></div>"; break;
      case 'assessment' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._ASSESSMENT."</span><span>: $value</span></div>"; break;
      case 'objectives' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._OBJECTIVES."</span><span>: $value</span></div>"; break;
      case 'lesson_topics' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._LESSONTOPICS."</span><span>: $value</span></div>"; break;
      case 'resources' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._RESOURCES."</span><span>: $value</span></div>"; break;
      case 'other_info' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._OTHERINFO."</span><span>: $value</span></div>"; break;
      case 'price_string' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._PRICE."</span><span>: $value</span></div>"; break;
      default: break;
     }
    }
   }
   if ($string = implode("", $tooltipInfo)) {
    echo $string;
   } else {
    echo _NODATAFOUND;
   }
  }
  // For eFront social
  if (isset($_GET['common_lessons']) && isset($_GET['user1']) && isset($_GET['user2'])) {
   $user1 = EfrontUserFactory::factory($_GET['user1']);
   if ($user1->getType() != "administrator") {
    $common_lessons = $user1 -> getCommonLessons($_GET['user2']);
    // pr($common_lessons);
    foreach ($common_lessons as $id => $lesson) {
     if (strlen($lesson['name'])>25) {
      $lesson['name'] = substr($lesson['name'],0,22) . "...";
     }
     $tooltipInfo[] = '<div class = "infoEntry"><span>'.$lesson['name']."</span><span></span></div>";
    }
    if ($string = implode("", $tooltipInfo)) {
     echo $string;
    } else {
     echo _NODATAFOUND;
    }
   } else {
    echo _NODATAFOUND;
   }
  }
 } catch (Exception $e) {
  header("HTTP/1.0 500");
  echo $e -> getMessage().' ('.$e -> getCode().')';
 }
}
?>
