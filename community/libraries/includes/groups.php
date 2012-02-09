<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


$loadScripts[] = 'includes/groups';
    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    if (isset($_GET['delete_user_group']) && eF_checkParameter($_GET['delete_user_group'], 'id')) {
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        try {
            $group = new EfrontGroup($_GET['delete_user_group']);
            $group -> delete();
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        exit;
    } elseif (isset($_GET['deactivate_user_group']) && eF_checkParameter($_GET['deactivate_user_group'], 'id')) {
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $group = new EfrontGroup($_GET['deactivate_user_group']);
            $group -> group['active'] = 0;
            $group -> persist();
            echo "0";
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        exit;
    } elseif (isset($_GET['activate_user_group']) && eF_checkParameter($_GET['activate_user_group'], 'id')) {
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $group = new EfrontGroup($_GET['activate_user_group']);
            $group -> group['active'] = 1;
            $group -> persist();
            echo "1";
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        exit;
    } elseif (isset($_GET['add_user_group']) || ( isset($_GET['edit_user_group']) && eF_checkParameter($_GET['edit_user_group'], 'id')) ) {

        if (isset($_GET['edit_user_group'])) {
         $currentGroup = new EfrontGroup($_GET['edit_user_group']);
         $smarty -> assign("T_CURRENT_GROUP", $currentGroup);
   $smarty -> assign ("T_STATS_LINK", array(array('text' => _STATISTICS, 'image' => "16x16/reports.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=statistics&option=groups&sel_group=" . $_GET['edit_user_group'], 'target' => '_self')));
         if ($currentGroup -> group['key_max_usage'] > 0) {
          $remainingKeyUsagesLabel = '('._REMAINING.' '.($currentGroup -> group['key_max_usage'] - $currentGroup -> group['key_current_usage']).'/'.$currentGroup -> group['key_max_usage'].')';
         }
        }

        isset($_GET['add_user_group']) ? $postTarget = 'add_user_group=1' : $postTarget = "edit_user_group=".$_GET['edit_user_group'];
        $form = new HTML_QuickForm("add_group_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=user_groups&$postTarget", "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');

        $roles = EfrontLessonUser :: getStudentRoles(true);
  array_unshift($roles, _DONTUSEDEFAULTGROUP);

        $form -> addElement('text', 'name', _NAME, 'class = "inputText"');
        $form -> addElement('text', 'description', _DESCRIPTION, 'class = "inputText"');
        $form -> addElement('static', 'sidenote', '<img src = "images/16x16/wizard.png" class = "ajaxHandle" alt = "'._AUTOMATICALLYGENERATEGROUPKEY.'" title = "'._AUTOMATICALLYGENERATEGROUPKEY.'" onclick = "$(\'unique_key_id\').value = \''.md5(time()).'\';"/>');// timestamp guarantess uniqueness
        $form -> addElement('text', 'unique_key', _UNIQUEGROUPKEY, 'class = "inputText" id="unique_key_id"');
        $form -> addElement('static', 'note', _UNIQUEGROUPKEYINFO);
        $form -> addElement('text', 'key_max_usage', _MAXGROUPKEYUSAGE, 'class = "inputText"');
        $form -> addElement('static', 'note', _MAXGROUPKEYUSAGEINFO.' '.$remainingKeyUsagesLabel);
        $form -> addElement('select', 'user_types_ID' , _DEFAULTLEARNERTYPE, $roles, 'class = "inputText"');
        $form -> addElement('static', 'note', _DEFAULTLEARNERTYPEINFO.' '.$remainingKeyUsagesLabel);
        $form -> addRule('name', _THEFIELD.' '._TYPENAME.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
        $form -> addRule('unique_key', _INVALIDFIELDDATA, 'checkParameter', 'alnum_general');
        $form -> addRule('key_max_usage',_INVALIDFIELDDATAFORFIELD.' "'._MAXGROUPKEYUSAGE.'"','numeric');
        $form -> addRule('key_max_usage', _INVALIDFIELDDATAFORFIELD.' "'._MAXGROUPKEYUSAGE.'"', 'callback', create_function('$a', 'return ($a >= 0);'));
        if (isset($_GET['edit_user_group'])) {
            $form -> setDefaults($currentGroup -> group);
        }
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_type', _SUBMIT, 'class = "flatButton"');
            if ($form -> isSubmitted() && $form -> validate()) {
             try {
              $values = $form -> exportValues();
              $fields['name'] = $values['name'];
              $fields['description'] = $values['description'];
              $fields['user_types_ID'] = $values['user_types_ID'];
              $fields['unique_key'] = $values['unique_key'];
              $fields['is_default'] = $values['is_default'];
              $fields['self_enroll'] = $values['self_enroll'];
              $fields['key_max_usage'] = $values['key_max_usage'] ? $values['key_max_usage'] : 0;
              if (isset($_GET['edit_user_group'])) {
               $values['key_max_usage'] OR $fields['key_current_usage'] = 0;
               $currentGroup -> group = array_merge($currentGroup -> group, $fields);
               $currentGroup -> persist();
              } else {
               $currentGroup = EfrontGroup::create($fields);
              }
              eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=user_groups&edit_user_group=".$currentGroup -> group['id']."&message=".urlencode(_OPERATIONCOMPLETEDSUCCESSFULLY)."&message_type=success");
             } catch (Exception $e){
              handleNormalFlowExceptions($e);
             }
            }
        }
        $smarty -> assign('T_USERGROUPS_FORM', $form -> toArray());
        if (isset($_GET['edit_user_group'])) {
            try {
             if (isset($_GET['ajax']) && $_GET['ajax'] == "usersTable") {
              $roles = EfrontUser :: getRoles(true);
              $smarty -> assign("T_ROLES", $roles);
              $constraints = array('archive' => false, 'return_objects' => false) + createConstraintsFromSortedTable();
              $users = $currentGroup -> getGroupUsersIncludingUnassigned($constraints);
              $totalEntries = $currentGroup -> countGroupUsersIncludingUnassigned($constraints);
              $dataSource = $users;
              $tableName = $_GET['ajax'];
              $alreadySorted = 1;
              $smarty -> assign("T_TABLE_SIZE", $totalEntries);
              include("sorted_table.php");
             }
             if (isset($_GET['ajax']) && $_GET['ajax'] == "lessonsTable") {
              $groupLessons = $currentGroup -> getLessons();
              $result = EfrontLesson::getStandAloneLessons(true);
              $lessons = array();
              foreach ($result as $value) {
               $lesson = $value -> lesson;
               $lesson['in_group'] = false;
               $lesson['user_type'] = 'student';
               if (in_array($lesson['id'], array_keys($groupLessons))) {
                $lesson['in_group'] = true;
                $lessons[$lesson['id']] = $lesson;
                $lessons[$lesson['id']]['user_type'] = $groupLessons[$lesson['id']]['user_type'];
               } else if ($lesson['active']) {
                $lessons[$lesson['id']] = $lesson;
               }
              }
              $dataSource = $lessons;
              $tableName = $_GET['ajax'];
              include("sorted_table.php");
             }
             if (isset($_GET['ajax']) && ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable')) {
     $_GET['sort'] != 'null' OR $_GET['sort'] = 'has_course';
              if ($_GET['ajax'] == 'coursesTable') {
               $constraints = array('archive' => false, 'instance' => false) + createConstraintsFromSortedTable();
              }
              if ($_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
               $constraints = array('archive' => false, 'instance' => $_GET['instancesTable_source']) + createConstraintsFromSortedTable();
              }
              $courses = $currentGroup -> getGroupCoursesIncludingUnassigned($constraints);
              $totalEntries = $currentGroup -> countGroupCoursesIncludingUnassigned($constraints);
              $dataSource = EfrontCourse :: convertCourseObjectsToArrays($courses);
              $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'num_students', 'num_lessons', 'num_skills', 'has_course'));
              $smarty -> assign("T_TABLE_SIZE", $totalEntries);
              $alreadySorted = 1;
              $tableName = $_GET['ajax'];
              include("sorted_table.php");
             }
             if (isset($_GET['postAjaxRequest'])) {
              if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
               $user = EfrontUserFactory::factory($_GET['login']);
               if ($currentGroup -> hasUser($user)) {
                $currentGroup -> removeUsers($user);
               } else {
                $currentGroup -> addUsers($user, $user -> user['user_types_ID'] ? $user -> user['user_types_ID'] : $user -> user['user_type']);
               }
              } else if (isset($_GET['addAll']) && $_GET['table'] == "usersTable") {
               $constraints = array('archive' => false, 'return_objects' => false);// + createConstraintsFromSortedTable();
               $usersToAdd = array();
               foreach ($currentGroup -> getGroupUsersIncludingUnassigned($constraints) as $key => $user) {
                if (!$user['has_group']) {
                 $usersToAdd[] = $user;
                 $user['user_types_ID'] ? $userTypes[] = $user['user_types_ID'] : $userTypes[] = $user['user_type'];
                }
               }
               $currentGroup -> addUsers($usersToAdd, $userTypes);
              } else if (isset($_GET['removeAll'])) {
               $currentGroup -> removeAllUsers();
              } else if (isset($_GET['lessons_ID']) && eF_checkParameter($_GET['lessons_ID'], 'id')) {
               if ($_GET['insert'] == "1") {
                $currentGroup -> addLesson($_GET['lessons_ID']);
               } else {
                $currentGroup -> removeLessons($_GET['lessons_ID']);
               }
              } else if (isset($_GET['addAll']) && $_GET['table'] == "lessonsTable") {
               isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
               foreach ($lessons as $lesson) {
                if (!$lesson['in_group']) {
                 $currentGroup -> addLesson($lesson['id'], 'student');
                 echo "Added lesson ".$lesson['id']." to group";
                }
               }
              } else if (isset($_GET['removeAll']) && $_GET['table'] == "lessonsTable") {
               //isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
               eF_deleteTableData("lessons_to_groups", "groups_ID=".$_GET['edit_user_group']);
               echo "All lessons where deleted from group";
              } else if (isset($_GET['courses_ID']) && eF_checkParameter($_GET['courses_ID'], 'id')) {
               if ($_GET['insert'] == 1) {
                $currentGroup -> addCourse($_GET['courses_ID']);
               } else {
                $currentGroup -> removeCourses($_GET['courses_ID']);
               }
              } else if (isset($_GET['addAll']) && $_GET['table'] == "coursesTable") {
               isset($_GET['filter']) ? $courses = eF_filterData($courses, $_GET['filter']) : null;
               foreach ($courses as $course) {
                if (!$course['in_group']) {
                 $currentGroup -> addCourse($course['id'], 'student');
                 echo "Added course ".$course['id']." to group";
                }
               }
              } else if (isset($_GET['removeAll']) && $_GET['table'] == "coursesTable") {
               //isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
               eF_deleteTableData("courses_to_groups", "groups_ID=".$_GET['edit_user_group']);
               echo "All lessons where deleted from group";
              } else if (isset($_GET['assign_to_all_users']) && $_GET['assign_to_all_users'] == "courses") {
               $groupUsers = $currentGroup -> getGroupUsers();
               if ($currentGroup -> group['user_types_ID'] == '0') {
                foreach ($groupUsers as $key => $user) {
                 if ($user -> user['user_type'] == 'administrator') {
                  unset($groupUsers[$key]);
                 } else {
                  $user -> user['user_types_ID'] ? $userRoles[$key] = $user -> user['user_types_ID'] : $userRoles[$key] = $user -> user['user_type'];
                 }
                }
               } else {
                $userRoles = $currentGroup -> group['user_types_ID'];
               }
               $result2 = eF_getTableData("users_to_courses", "users_LOGIN,courses_ID,user_type");
                  $usersTocourses = array();
               foreach ($result2 as $value) {
                $usersTocourses[$value['courses_ID']][$value['users_LOGIN']] = $value['user_type'];
               }
               foreach ($currentGroup -> getGroupCourses() as $key => $course) {
                $coursetypes = $userRoles;
                foreach ($groupUsers as $login => $user) {
                 $index = array_search($login, array_keys($usersTocourses[$key]));
                 if ($index !== false) {
                  unset($groupUsers[$login]);
                  unset($coursetypes[$login]);
                 }
                }
            if (!empty($groupUsers)) {
                 $course -> addUsers($groupUsers, $coursetypes, true);
            }
               }
              } else if (isset($_GET['assign_to_all_users']) && $_GET['assign_to_all_users'] == "lessons") {
               $groupUsers = $currentGroup -> getUsers();
               $groupUsers = array_merge($groupUsers['professor'], $groupUsers['student']);
               $groupLessons = $currentGroup -> getLessons();
               $lessonIds = array_keys($groupLessons);
                     $result = eF_getTableData("users_to_lessons", "users_LOGIN,lessons_ID,user_type");
               $usersTolessons = array();
               foreach ($result as $value) {
                $usersTolessons[$value['users_LOGIN']][$value['lessons_ID']] = $value['user_type'];
               }
               foreach ($groupUsers as $user) {
        $lessons = $lessonIds;
                $user = EfrontUserFactory :: factory($user);
                if ($user -> getType() != 'administrator') {
                 if ($_GET['assign_to_all_users'] == "lessons") {
                  if ($currentGroup -> group['user_types_ID'] == '0') {
                   $user -> user['user_types_ID'] ? $userType = $user -> user['user_types_ID'] : $userType = $user -> user['user_type'];
                  } else {
                   $userType = $currentGroup -> group['user_types_ID'];
                  }
                  foreach ($lessons as $key => $lesson) {
                   $index = array_search($lesson, array_keys($usersTolessons[$user]));
                   if ($index !== false) {
                    unset($lessons[$index]);
                   }
                  }
                  if (!empty($lessons)) {
                   $user -> addLessons($lessons, $userType, 1); //active lessons
                  }
                 }
                }
               }
              }
              exit;
             }
            } catch (Exception $e) {
             handleAjaxExceptions($e);
            }
        }
    } else {
        if (isset($_GET['ajax']) && $_GET['ajax'] == "groupsTable") {
         $dataSource = eF_getTableData("groups g LEFT OUTER JOIN (select ug.groups_ID from users_to_groups ug, users u where u.login=ug.users_LOGIN and u.archive=0) c ON g.id=c.groups_ID", "g.*, count(c.groups_ID) as num_users", "g.dynamic=0", "","g.name");
         $tableName = $_GET['ajax'];
         include("sorted_table.php");
        }
        //$smarty -> assign("T_USERGROUPS", $result);
    }
