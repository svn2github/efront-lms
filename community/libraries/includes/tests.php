<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

    if (isset($_GET['view_results'])) {

        // GET THE CORRECT TEST

        // Per-user analysis of the tests => skill gap analysis
        if (isset($_GET['user'])) {

            // PROPOSED LESSONS
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'proposedLessonsTable') {
                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                $directionsTree = new EfrontDirectionsTree();
                $directionsPaths = $directionsTree -> toPathString();
                $languages = EfrontSystem :: getLanguages(true);

                $skills_missing = array();
                $all_skills = "";

                foreach ($_GET as $key => $value) {
                    // all skill-related posted values are just the skill_ID ~ a uint value
                    if (eF_checkParameter($key, 'unit')) {
                        if ($value == 1) {
                            $skills_missing[] = $key;
                            $all_skills .= "&".$skill_item['id'] . "=1";
                        } else {
                            $all_skills .= "&".$skill_item['id'] . "=0";
                        }
                    }
                }
                // This smarty variable will denote all missing and existing skills
                $smarty -> assign("T_MISSING_SKILLS_URL", $all_skills);

                // check what you GET and keep only the skills
                $skills_missing = implode("','", $skills_missing);

                $user = EfrontUserFactory :: factory($_GET['user']);
                $alredy_attending = implode("','", array_keys($user -> getLessons()));

                $lessons_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID JOIN lessons ON lessons.id = module_hcd_lesson_offers_skill.lesson_ID","module_hcd_lesson_offers_skill.lesson_ID, lessons.*, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$alredy_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $lessons_proposed = eF_multiSort($lessons_proposed, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $lessons_proposed = eF_filterData($lessons_proposed, $_GET['filter']);
                }
                $smarty -> assign("T_PROPOSED_LESSONS_SIZE", sizeof($lessons_proposed));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $lessons_proposed = array_slice($lessons_proposed, $offset, $limit);
                }
                foreach ($lessons_proposed as $key => $proposed_lesson) {
                    $obj = new EfrontLesson($proposed_lesson['lesson_ID']);
                    $lessons_proposed[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$proposed_lesson['id']);
                    $lessons_proposed[$key]['direction_name'] = $directionsPaths[$proposed_lesson['directions_ID']];
                    $lessons_proposed[$key]['languages_NAME'] = $languages[$proposed_lesson['languages_NAME']];
                }
//pr($lessons_proposed);
                $smarty -> assign("T_PROPOSED_LESSONS_DATA", $lessons_proposed);

                $smarty -> display('administrator.tpl');
                exit;
            }


            // PROPOSED COURSES
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'proposedCoursesTable') {
                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                $directionsTree = new EfrontDirectionsTree();
                $directionsPaths = $directionsTree -> toPathString();
                $languages = EfrontSystem :: getLanguages(true);

                $skills_missing = array();
                $all_skills = "";

                foreach ($_GET as $key => $value) {
                    // all skill-related posted values are just the skill_ID ~ a uint value
                    if (eF_checkParameter($key, 'unit')) {
                        if ($value == 1) {
                            $skills_missing[] = $key;
                            $all_skills .= "&".$skill_item['id'] . "=1";
                        } else {
                            $all_skills .= "&".$skill_item['id'] . "=0";
                        }
                    }
                }
                // This smarty variable will denote all missing and existing skills
                $smarty -> assign("T_MISSING_SKILLS_URL", $all_skills);

                // check what you GET and keep only the skills
                $skills_missing = implode("','", $skills_missing);

                $user = EfrontUserFactory :: factory($_GET['user']);

                $alredy_attending = implode("','", array_keys($user -> getCourses()));
                $courses_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID JOIN courses ON courses.id = module_hcd_course_offers_skill.course_ID","module_hcd_course_offers_skill.course_ID, courses.*, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.course_ID NOT IN ('".$alredy_attending."')","","module_hcd_course_offers_skill.course_ID ORDER BY skills_offered DESC");

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $courses_proposed = eF_multiSort($courses_proposed, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $courses_proposed = eF_filterData($courses_proposed, $_GET['filter']);
                }
                $smarty -> assign("T_PROPOSED_COURSES_SIZE", sizeof($courses_proposed));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $courses_proposed = array_slice($courses_proposed, $offset, $limit);
                }
                foreach ($courses_proposed as $key => $proposed_course) {
                    $obj = new EfrontCourse($proposed_course['course_ID']);
                    $courses_proposed[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=courses&edit_course='.$proposed_course['id']);
                    $courses_proposed[$key]['direction_name'] = $directionsPaths[$proposed_course['directions_ID']];
                    $courses_proposed[$key]['languages_NAME'] = $languages[$proposed_course['languages_NAME']];
                }
//pr($courses_proposed);
                $smarty -> assign("T_PROPOSED_COURSES_DATA", $courses_proposed);

                $smarty -> display('administrator.tpl');
                exit;
            }

            // ASSIGNED LESSONS
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'assignedLessonsTable') {
                $directionsTree = new EfrontDirectionsTree();
                $directionPaths = $directionsTree -> toPathString();
                $lessons = EfrontLesson :: getLessons();

                $editedUser = EfrontUserFactory :: factory($_GET['user']);
                $userLessons = $editedUser -> getLessons(true);
                foreach ($lessons as $key => $lesson) {
                    $lessons[$key]['directions_name'] = $directionPaths[$lesson['directions_ID']];
                    $lessons[$key]['user_type'] = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                    $lessons[$key]['partof'] = 0;
                    if (in_array($lesson['id'], array_keys($userLessons))) {
                        $lessons[$key]['from_timestamp'] = $userLessons[$key] -> userStatus['from_timestamp'];
                        $lessons[$key]['partof'] = 1;
                        $lessons[$key]['user_type'] = $userLessons[$key] -> userStatus['user_type'];
                        $lessons[$key]['completed'] = $userLessons[$key] -> userStatus['completed'];
                        $lessons[$key]['score'] = $userLessons[$key] -> userStatus['score'];
                    } else if ($currentUser -> user['user_type'] != 'administrator' || !$lesson['active']) {
                        unset($lessons[$key]);
                    } else if ($lesson['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                        unset($lessons[$key]);
                    }
                    if ($lesson['course_only']) {
                        unset($lessons[$key]);
                    }
                }

                $roles = EfrontLessonUser :: getLessonsRoles(true);
                $smarty -> assign("T_ROLES_ARRAY", $roles);

                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $lessons = eF_filterData($lessons, $_GET['filter']);
                }
                $smarty -> assign("T_ASSIGNED_LESSONS_SIZE", sizeof($lessons));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $lessons = array_slice($lessons, $offset, $limit);
                }
                //foreach ($lessons as $key => $lesson) {
                    //$lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
                //}
                foreach ($lessons as $key => $lesson) {
                    if (!$lesson['partof']) {
                        unset($lessons[$key]);
                    } else {
                        $obj = new EfrontLesson($lesson['id']);
                        $lessons[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$lesson['id']);
                    }
                }
                $smarty -> assign("T_ASSIGNED_LESSONS_DATA", $lessons);
                //pr($lessons);
//pr($lessons);
                $smarty -> display('administrator.tpl');
                exit;
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 'assignedCoursesTable') {
                $directionsTree = new EfrontDirectionsTree();
                $directionPaths = $directionsTree -> toPathString();
                $courses = EfrontCourse :: getCourses();

                $editedUser = EfrontUserFactory :: factory($_GET['user']);
                $userCourses = $editedUser -> getUserCourses();
                foreach ($courses as $key => $course) {
                    $courses[$key]['directions_name'] = $directionPaths[$course['directions_ID']];
                    $courses[$key]['user_type'] = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                    $courses[$key]['partof'] = 0;
                    if (in_array($course['id'], array_keys($userCourses))) {
                        $courses[$key]['from_timestamp'] = $userCourses[$key] -> course['active_in_course'];
                        $courses[$key]['partof'] = 1;
                        $courses[$key]['user_type'] = $userCourses[$key] -> course['user_type'];
                        $courses[$key]['completed'] = $userCourses[$key] -> course['completed'];
                        $courses[$key]['score'] = $userCourses[$key] -> course['score'];
                    } else if ($currentUser -> user['user_type'] != 'administrator' || !$course['active']) {
                        unset($courses[$key]);
                    } else if ($course['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                        unset($courses[$key]);
                    }
                    if ($course['course_only']) {
                        unset($courses[$key]);
                    }
                }

                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $courses = eF_multiSort($courses, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $courses = eF_filterData($courses, $_GET['filter']);
                }
                $smarty -> assign("T_ASSIGNED_COURSES_SIZE", sizeof($courses));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $courses = array_slice($courses, $offset, $limit);
                }
                //foreach ($courses as $key => $course) {
                    //$courses[$key]['languages_NAME'] = $languages[$course['languages_NAME']];
                //}
                foreach ($courses as $key => $course) {
                    if (!$course['partof']) {
                        unset($courses[$key]);
                    } else {
                        $obj = new EfrontCourse($course['id']);
                        $courses[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=courses&edit_course='.$course['id']);
                    }
                }
                $smarty -> assign("T_ASSIGNED_COURSES_DATA", $courses);
                //pr($courses);
//pr($courses);
                $smarty -> display('administrator.tpl');
                exit;
            }


            if (isset($_GET['ajax']) && $_GET['ajax'] == 'coursesTable') {
                $directionsTree = new EfrontDirectionsTree();
                $directionPaths = $directionsTree -> toPathString();
                $courses = EfrontCourse :: getCourses();

                $editedUser = EfrontUserFactory :: factory($_GET['user']);
                $userCourses = $editedUser -> getUserCourses();
                foreach ($courses as $key => $course) {
                    $courses[$key]['partof'] = 0;
                    $courses[$key]['directions_name'] = $directionPaths[$course['directions_ID']];
                    $courses[$key]['user_type'] = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                    if (in_array($course['id'], array_keys($userCourses))) {
                        $courses[$key]['from_timestamp'] = $userCourses[$key] -> course['from_timestamp'];
                        $courses[$key]['partof'] = 1;
                        $courses[$key]['user_type'] = $userCourses[$key] -> course['user_type'];
                        $courses[$key]['completed'] = $userCourses[$key] -> course['completed'];
                        $courses[$key]['score'] = $userCourses[$key] -> course['score'];
                    } else if ($currentUser -> user['user_type'] != 'administrator' || !$course['active']) {
                        unset($courses[$key]);
                    } else if ($course['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                        unset($courses[$key]);
                    }
                }
                $courses = array_values($courses); //Reindex so that sorting works

                $roles = EfrontLessonUser :: getLessonsRoles(true);
                $smarty -> assign("T_ROLES_ARRAY", $roles);


                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $courses = eF_multiSort($courses, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $courses = eF_filterData($courses, $_GET['filter']);
                }
                $smarty -> assign("T_COURSES_SIZE", sizeof($courses));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $courses = array_slice($courses, $offset, $limit);
                }
                //foreach ($courses as $key => $course) {
                    //$courses[$key]['languages_NAME'] = $languages[$course['languages_NAME']];
                //}

                $smarty -> assign("T_COURSES_DATA", $courses);

                $smarty -> display($_SESSION['s_type'].'.tpl');
                exit;
            }

            $myarray = array();

            $myarray[0] = array('id'=>1, 'skill'=>'Knowledge of Greedy Algorithms', 'score'=>45);
            $myarray[1] = array('id'=>2, 'skill'=>'Knowledge of Maya Civilization', 'score'=>65);
            $myarray[2] = array('id'=>3, 'skill'=>'Knowledge of Psychology', 'score'=>75);
            $myarray[3] = array('id'=>4, 'skill'=>'Knowledge of Advanced Nanorobotics', 'score'=>25);


            //eF_getTableData("
            $smarty -> assign("T_SKILLSGAP",$myarray);

            // Get the missing skills according to the analysis
            $skills_missing = array();
            $all_skills = "";
            foreach ($myarray as $skill_item) {
                if ($skill_item['score'] < 50) {
                    $skills_missing[] = $skill_item['id'];
                    $all_skills .= "&".$skill_item['id'] . "=1";
                } else {
                    $all_skills .= "&".$skill_item['id'] . "=0";
                }
            }

            // This smarty variable will denote all missing and existing skills
            $smarty -> assign("T_MISSING_SKILLS_URL", $all_skills);
//pr($skills_missing);
            $skills_missing = implode("','", $skills_missing);
            $user = EfrontUserFactory :: factory($_GET['user']);

            $lessons_attending = implode("','", array_keys($user -> getLessons()));
            $lessons_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID","module_hcd_lesson_offers_skill.lesson_ID, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$lessons_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");


            $courses_attending = implode("','", array_keys($user -> getUserCourses()));
            $courses_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID","module_hcd_course_offers_skill.course_ID, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.course_ID NOT IN ('".$courses_attending."')","","module_hcd_course_offers_skill.course_ID ORDER BY skills_offered DESC");


        } else {
            // SHOW USERS LIST

        }



    } else {

        // SHOW TESTS LIST
    }
