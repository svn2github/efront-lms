<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$loadScripts[] = 'includes/lessons_list';
try {
    if (isset($_GET['op']) && $_GET['op'] == 'tests') {

        if (isset($_GET['solve_test'])) {
            if (isset($_GET['confirm'])) {
                $form = new HTML_QuickForm("test_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&op=tests&solve_test='.$_GET['solve_test'].'&confirm=1', "", null, true);

                if ($form -> isSubmitted() && $form -> validate()) {

                    // The skillgap test has been solved and submitted
                    $result = eF_getTableData("completed_tests", "*", "tests_id = '".$_GET['solve_test']."' AND users_LOGIN = '".$currentUser -> user['login']."'");
                    $testInstance = unserialize($result[0]['test']);

                    $testString = $testInstance -> toHTMLQuickForm($form);
                    $testString = $testInstance -> toHTML($testString, $remainingTime);

                    $questions = $form -> exportValues('question');
                    $questions= $form->getSubmitValue('question');

                    $testInstance -> completedTest['status'] = 'completed';
                    $testInstance -> complete($questions);

                    eF_updateTableData("users_to_skillgap_tests", array("solved" => "1"), "tests_ID = '".$_GET['solve_test']."' AND users_LOGIN = '".$currentUser -> user['login']."'");

                    // Check if you should automatically assign lessons and courses to the student
                    if ($testInstance -> options['automatic_assignment']) {
                        $analysisResults = $testInstance -> analyseSkillGapTest();

                        foreach ($analysisResults['lessons'] as $lesson) {
                            $currentUser -> addLessons($lesson['lesson_ID']);
                        }
                        foreach ($analysisResults['courses'] as $course) {
                            $currentUser -> addCourses($course['courses_ID']);

                        }
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=lessons&op=tests&message=". urlencode(_SKILLGAPTESTCOMPLETEDSUCCESSFULLYANDTHECORRESPONDING) . " " . sizeof($analysisResults['lessons'] ) . " " . _LESSONS . " " . _AND . " ". sizeof($analysisResults['courses']). " " . _COURSES . " " . _HAVEBEENASSIGNED . "&message_type=success");
                    } else {
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=lessons&op=tests&message=". urlencode(_SKILLGAPTESTCOMPLETEDSUCCESSFULLY). ". " . _YOURRESULTSHAVEBEENSENTTOYOURPROFESSORSWHOWILLASSIGNLESSONSACCORDINGTOYOURNEEDS . "&message_type=success");
                    }

                    exit;
                }

                //HACK to remove incomplete tests
                eF_deleteTableData("completed_tests", "tests_id = '".$_GET['solve_test']."' AND users_LOGIN = '".$currentUser -> user['login']."'");
                $test = new EfrontTest($_GET['solve_test']);
                $testInstance = $test -> start($currentUser -> user['login']);

                // Hard coded to disallow pause test
                $testInstance -> options['pause_test'] = 0;

                $testString = $testInstance -> toHTMLQuickForm($form);
                $testString = $testInstance -> toHTML($testString, $remainingTime);

                $form -> addElement('hidden', 'time_start', $timeStart); //This element holds the time the test started, so we know the remaining time even if the user left the system
                $form -> addElement('submit', 'submit_test', _SUBMITTEST, 'class = "flatButton" onclick = "if (typeof(checkedQuestions) != \'undefined\' && (unfinished = checkQuestions())) return confirm(\''._YOUHAVENOTCOMPLETEDTHEFOLLOWINGQUESTIONS.': \'+unfinished+\'. '._AREYOUSUREYOUWANTTOSUBMITTEST.'\');"');
                if ($testInstance -> options['pause_test']) {
                    $form -> addElement('submit', 'pause_test', _PAUSETEST, 'class = "flatButton"');
                }

                $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                $form -> accept($renderer);

                $smarty -> assign('T_TEST_FORM', $renderer -> toArray());
                //                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=lessons&op=tests&");
            } else {
                $form = new HTML_QuickForm("test_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&op=tests', "", null, true);
                $test = new EfrontTest($_GET['solve_test']);
                $testInstance = $test;
                $test -> getQuestions(); //This way the test's questions are populated, and we will be needing this information
                $testInstance -> options['random_pool'] && $testInstance -> options['random_pool'] >= sizeof($testIn) ? $questionsNumber = $testInstance -> options['random_pool'] : $questionsNumber = sizeof($testInstance -> questions);
                $smarty -> assign("T_SHOW_CONFIRMATION", true);
            }

            if (isset($_GET['ajax'])) {
                $testInstance -> handleAjaxActions();
            }

            //Calculate total questions. If it's already set, then we are visiting an unsolved test, and the questions number is already calculated (and may be different that the $testInstance -> questions size)
            if (!isset($questionsNumber)) {
                $questionsNumber = sizeof($testInstance -> questions);
            }

            //$smarty -> assign("T_REMAINING_TIME", $remainingTime);
            $smarty -> assign("T_TEST_QUESTIONS_NUM", $questionsNumber);
            $smarty -> assign("T_TEST_DATA", $testInstance);
            $smarty -> assign("T_TEST", $testString);
            $smarty -> assign("T_TEST_STATUS", $status);

        } else {
            $tests = $currentUser -> getSkillgapTests();
            $test_array = array();
            foreach ($tests as $test) {
                if ($test['solved']) {
                    $test_array[] = array('text' => $test['name'], 'image' => "32x32/success.png", 'href' => 'javascript:void(0);');
                } else {
                    $test_array[] = array('text' => $test['name'], 'image' => "32x32/tests.png", 'href' => $_SESSION['s_type'] . ".php?ctg=lessons&op=tests&solve_test=" . $test['id']);
                }

            }

            // Present a list of tests
            if (!empty($test_array)) {
                $smarty -> assign("T_TESTS", $test_array);

            }
        }





    } elseif (isset($_GET['export']) && $_GET['export'] == 'rtf') {
    } elseif (isset($_GET['course']) && in_array($_GET['course'], array_keys($currentUser -> getCourses()))) {
        $userCourses = $currentUser -> getCourses();
        if ($roles[$userCourses[$_GET['course']]] != 'professor') {
            throw new Exception(_UNAUTHORIZEDACCESS);
        }
        $currentCourse = new EfrontCourse($_GET['course']);
        $baseUrl = 'ctg=lessons&course='.$currentCourse -> course['id'];
        $smarty -> assign("T_BASE_URL", $baseUrl);
        $smarty -> assign("T_CURRENT_COURSE", $currentCourse);
        require_once 'course_settings.php';
    } elseif (isset($_GET['op']) && $_GET['op'] == 'search') {
        /**Functions to perform searches*/
        require_once "module_search.php";
    } else {
        $directionsTree = new EfrontDirectionsTree();
        if (isset($_GET['catalog'])) {
            $loadScripts[] = 'includes/catalog';
         //The courses catalog 
         if (isset($_GET['ajax']) && $_GET['ajax'] == 'cart') {
             try {
                 include "catalog.php";
             } catch (Exception $e) {
                 header("HTTP/1.0 500 ");
                 echo _INVALIDVOUCHER;
             }
             exit;
         }
         /**Handles cart and catalog*/
         include "catalog.php";
         if (!isset($_GET['checkout'])) {
             //Get available languages
             $languages = array();
             foreach (EfrontSystem :: getLanguages() as $key => $value) {
                 if ($value['active']) {
                     $languages[$key] = $value['translation'];
                 }
             }
             ksort($languages);
             $smarty -> assign("T_LANGUAGES", $languages);
             if (isset($_GET['info_lesson'])) {
                 $lesson = new EfrontLesson($_GET['info_lesson']);
                 $lesson -> lesson['price_string'] = formatPrice($lesson -> lesson['price'], array($lesson -> options['recurring'], $lesson -> options['recurring_duration']), true);
                 $smarty -> assign("T_LESSON", $lesson);
                 $lessonInformation = $lesson -> getInformation();
                 $content = new EfrontContentTree($lesson);
                 if (sizeof($content -> tree) > 0) {
                     $smarty -> assign("T_CONTENT_TREE", $content -> toHTML(false, 'dhtml_content_tree', array('noclick' => 1)));
                 }
                 $lessonInfo = new LearningObjectInformation(unserialize($lesson -> lesson['info']));
                 $smarty -> assign("T_LESSON_INFO", $lessonInfo);
                 $additionalInfo = $lesson -> getInformation();
                 $smarty -> assign("T_ADDITIONAL_LESSON_INFO", $additionalInfo);
                 if ($lesson -> lesson['course_only']) {
                     $smarty -> assign("T_LESSON_COURSES", $lesson -> getCourses());
                     if (isset($_GET['from_course']) && $_GET['from_course']) {
                         $course = new EfrontCourse($_GET['from_course']);
                         $smarty -> assign ("T_COURSE", $course);
                         $smarty -> assign("T_HAS_COURSE", in_array($course -> course['id'], array_keys($userCourses)));
                     }
                 }
             } else if ($_GET['info_course']) {
                 $course = new EfrontCourse($_GET['info_course']);
                 $course -> course['price_string'] = formatPrice($course -> course['price'], array($course -> options['recurring'], $course -> options['recurring_duration']), true);
                 $smarty -> assign("T_COURSE", $course);
                 $lessons = $course -> getLessons();
                 $smarty -> assign("T_COURSE_LESSONS", $lessons);
                 $courseInfo = new LearningObjectInformation(unserialize($course -> course['info']));
                 $smarty -> assign("T_COURSE_INFO", $courseInfo);
                 $additionalInfo = $course -> getInformation();
                 $smarty -> assign("T_ADDITIONAL_COURSE_INFO", $additionalInfo);
             }
                $smarty -> assign("T_LAYOUT_CLASS", $currentTheme -> options['toolbar_position'] == "left" ? "hideRight" : "hideLeft"); //Whether to show the sidemenu on the left or on the right
             $options = array('lessons_link' => basename($_SERVER['PHP_SELF']).'?ctg=lessons&catalog=1&info_lesson=',
                              'courses_link' => basename($_SERVER['PHP_SELF']).'?ctg=lessons&catalog=1&info_course=',
                        'search' => true,
                              'catalog' => true,
                        'url' => $_SERVER['PHP_SELF'],
         'collapse' => $GLOBALS['configuration']['collapse_catalog'],
         'buy_link' => true);
             include("directions_tree.php");
             $smarty -> assign("T_CART", cart :: prepareCart());
         }
        } else {
            $options = array('noprojects' => 1, 'notests' => 1);
            $userLessons = $currentUser -> getLessons(true);
            $userLessonProgress = EfrontStats :: getUsersLessonStatus($userLessons, $currentUser -> user['login'], $options);
            $userLessons = array_intersect_key($userLessons, $userLessonProgress); //Needed because EfrontStats :: getUsersLessonStatus might remove automatically lessons, based on time constraints 
   //this must be here (before $userCourses assignment) in order to revoke a certificate if it is expired and/or re-assign a course to a student if needed
   $userCourses = $currentUser -> getCourses(true, false, $options);
            $userCourseProgress = EfrontStats :: getUsersCourseStatus($userCourses, $currentUser -> user['login'], $options);
            $userCourses = array_intersect_key($userCourses, $userCourseProgress); //Needed because EfrontStats :: getUsersCourseStatus might remove automatically courses, based on time constraints 
            /*Assign progress in a per-lesson fashion*/
            $temp = array();
            foreach ($userLessonProgress as $lessonId => $user) {
                $temp[$lessonId] = $user[$currentUser -> user['login']];
            }
            $userProgress['lessons'] = $temp;
            /*Assign progress in a per-course fashion*/
            $temp = array();
            foreach ($userCourseProgress as $courseId => $user) {
                $temp[$courseId] = $user[$currentUser -> user['login']];
            }
            $userProgress['courses'] = $temp;
            $options = array('lessons_link' => '#user_type#.php?lessons_ID=',
                                  'courses_link' => false,
                                  'catalog' => true,
                                  //'search'       => 1,
                                  //'url'          => $_SERVER['PHP_SELF']."?ctg=lessons"
                                  );
            if (sizeof ($userLessons) > 0 || sizeof($userCourses) > 0) {
                $smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, $userLessons, $userCourses, $userProgress, $options));
             //include("directions_tree.php");
            }
            // Find all unsolved user skillgap tests
            $only_found_solved = 0;
            if ($_student_) {
                $userSkillgapTests = $currentUser -> getSkillgapTests();
                foreach($userSkillgapTests as $skid => $skillGap) {
                    if ($skillGap['solved']) {
                        unset($userSkillgapTests[$skid]);
                        $only_found_solved = 1;
                    }
                }
                if (!empty($userSkillgapTests)) {
                    $labelText = _NEWSKILLGAPTESTS . ":&nbsp;";
                    if (sizeof($userSkillgapTests) > 1) {
                        $labelText .= "<br>";
                    }
                    foreach($userSkillgapTests as $skillGap) {
                        $labelText .= $skillGap['name'];
                    }
                    $smarty -> assign("T_SKILLGAP_TESTS", $labelText);
                } else if ($only_found_solved) {
                    $smarty -> assign("T_SKILLGAP_TESTS_SOLVED", 1);
                }
            }
        }
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
?>
