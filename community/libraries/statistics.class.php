<?php
/**
* EfrontStats Class file
*
* @package eFront
* @version 3.5.0
*/

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/**
 * Statistics exceptions
 *
 * @package eFront
 */
class EfrontStatsException extends Exception
{
 const INVALID_ID = 1001;
 const INVALID_PARAMETER = 1002;

}


/**
 * This class is used to handle statistics
 *
 * @package eFront
 */
class EfrontStats
{
    /**
     * Get user seen content in lesson
     *
     * This function calulates the content done by the specified student(s) in the
     * specified lesson. If $users is not specified, then information on all users and/or lessons
     * is calculated.
     * <br/>Example:
     * <code>
     * EfrontStats :: getStudentsSeenContent(3, 'jdoe');                            //Get statistics for user jdoe in lesson 3
     * EfrontStats :: getStudentsSeenContent(3, array('jdoe', 'george'));           //Get statistics for users george and jdoe in lesson 3
     * EfrontStats :: getStudentsSeenContent(3);                                    //Get statistics for all users in lesson 3
     * EfrontStats :: getStudentsSeenContent();                                     //Get statistics for all users in all lessons
     * </code>
     * The resulting array is of the form array(lesson id => array(login => array(done content))):
     * <code>
     *  Array
     *  (
     * 		[32] => Array
     * 			(
     *      	[jdoe] => Array
     *       	   (
     *              	[1415] =>
     *          	    [1417] =>
     *      	        [1416] => 50
     *  	            [1412] =>
     *	                [1411] =>
     *              	[1413] =>
     *          	    [1420] => 100
     *      	    )
     *  	    [george] => Array
     *	           (
     *              	[1415] =>
     *              	[1417] =>
     *          	    [1416] => 66
     *      	        [1412] =>
     *  	            [1408] =>
     *	                [1409] =>
     *              	[1410] =>
     *          	    [1420] => 30
     *      	   )
     *  	 )
     * )
     * </code>
     *
     * This means that: very lesson is an array, where every student has a corresponding array, in which the keys represent the units he has
     * completed. For content units, the array values are empty. For test units, the array values contain the
     * users's score.
     *
     * @param mixed $lessons An array of lesson ids or EfrontLesson objects. If false, all lessons are considered
     * @param mixed $users One or more optional user logins
     * @return array The seen content per user login
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getStudentsSeenContent($lessons = false, $users = false, $options = array()) {
        if ($lessons == false) {
            $lessons = eF_getTableDataFlat("lessons", "id");
            $lessons = $lessons['id'];
        } else if (!is_array($lessons)) {
            $lessons = array($lessons);
        }

        foreach ($lessons as $key => $lesson) {
            if ($lesson instanceof EfrontLesson) {
                $lessons[$key] = $lesson -> lesson['id'];
            } else if (!eF_checkParameter($lesson, 'id')) {
                throw new EfrontLessonException(_INVALIDID, EfrontLessonException :: INVALID_ID);
            } else {
                $lessons[$key] = $lesson;
            }
        }

        if ($users != false) {
            !is_array($users) ? $users = array($users) : null; //Convert single login to array
  } else {
    $users = eF_getTableDataFlat("users", "login");
    $users = $users['login'];
  }
        foreach ($users as $key => $user) {
            if ($user instanceof EfrontUser) {
                $users[$key] = $user -> user['login'];
            } else if (!eF_checkParameter($user, 'login')) {
                throw new EfrontLessonException(_INVALIDLOGIN, EfrontUserException :: INVALID_LOGIN);
            } else {
                $users[$key] = $user;
            }
        }

        if (sizeof($lessons) > 0 && sizeof($users) > 0) {
         $doneTests = array();
         //debug_print_backtrace();
         if (!isset($options['notests']) || !$options['notests']) {
          $result = eF_getTableData("completed_tests ct, tests t", "ct.archive, ct.status, ct.score, ct.users_LOGIN, t.lessons_ID, t.content_ID, t.keep_best", "ct.status != 'deleted' and ct.status != 'incomplete' and t.id=ct.tests_ID and t.lessons_ID in (".implode(",", $lessons).")");

          foreach ($result as $value) {
           if ($value['keep_best'] && $value['status'] == 'passed') {
               $doneTests[$value['lessons_ID']][$value['users_LOGIN']][$value['content_ID']] = $value['score'];
           } elseif ($value['status'] != 'failed' && !$value['archive']) {
               $doneTests[$value['lessons_ID']][$value['users_LOGIN']][$value['content_ID']] = $value['score'];
           }
          }
//pr($doneTests);exit;		        
         }
         $temp = eF_getTableData("user_types", "*");
   if (sizeof($temp) == 0) {
    $result = eF_getTableData("users u, users_to_lessons ul", "u.login, ul.lessons_ID, ul.done_content", "ul.archive=0 and ul.user_type = 'student' and u.login = ul.users_LOGIN and u.login in ('".implode("','", $users)."') and ul.lessons_ID in (".implode(",", $lessons).")") ;
   } else {
    $result = eF_getTableData("users u, users_to_lessons ul, user_types as ut", "u.login, ul.lessons_ID, ul.done_content", "ul.archive=0 and (ul.user_type = 'student' OR (ul.user_type=ut.id AND ut.basic_user_type = 'student')) and u.login = ul.users_LOGIN and u.login in ('".implode("','", $users)."') and ul.lessons_ID in (".implode(",", $lessons).")");
      }
   //$result           = eF_getTableData("users u, users_to_lessons ul, user_types as ut", "u.login, ul.lessons_ID, ul.done_content", "(ul.user_type = 'student' OR (ul.user_type=ut.id AND ut.basic_user_type = 'student')) and u.login = ul.users_LOGIN");
         //$result           = eF_getTableData("users u, users_to_lessons ul", "u.login, ul.lessons_ID, ul.done_content", "ul.user_type = 'student' and u.login = ul.users_LOGIN");

   $usersDoneContent = array();

         foreach ($result as $value) {
             $usersDoneContent[$value['lessons_ID']][$value['login']] = unserialize($value['done_content']);
         }

         //Get lessons content, in case a done unit is not part of a lesson anymore or is inactive
         $result = eF_getTableData("content c", "id, lessons_ID", "lessons_ID in (".implode(",", $lessons).") and active=1");
         $lessonContent = array();
         foreach ($result as $value) {
             $lessonContent[$value['lessons_ID']][] = $value['id'];
         }

         $resultScorm = eF_getTableData("scorm_data sd, content c", "c.ctg_type, c.lessons_ID, content_ID, users_LOGIN, lesson_status, score, minscore, maxscore, masteryscore", "c.id=sd.content_ID and c.lessons_ID in (".implode(",", $lessons).") and sd.users_LOGIN in ('".implode("','", $users)."')");

         foreach ($resultScorm as $key => $value) {
             if ($value['lesson_status'] == 'passed' || $value['lesson_status'] == 'completed') {
                 if ($value['ctg_type'] == 'scorm') {
                     $scormDoneContent[$value['lessons_ID']][$value['users_LOGIN']][$value['content_ID']] = '';
                 } elseif ($value['ctg_type'] == 'scorm_test') {
                     if (is_numeric($value['minscore']) && is_numeric($value['maxscore'])) {
                         $value['score'] = 100 * $value['score'] / ($value['minscore'] + $value['maxscore']);
                     } else {
                         $value['score'] = $value['score'];
                     }
                     $scormDoneContent[$value['lessons_ID']][$value['users_LOGIN']][$value['content_ID']] = $value['score'];
                 }
             } else {
                 //Remove this unit from the seen contents unit, since it is failed
                 if (isset($usersDoneContent[$value['lessons_ID']][$value['users_LOGIN']][$value['content_ID']])) {
                     unset($usersDoneContent[$value['lessons_ID']][$value['users_LOGIN']][$value['content_ID']]);
                 }
             }
         }
        }

        foreach ($lessons as $lessonId) {
            !isset($usersDoneContent[$lessonId]) ? $usersDoneContent[$lessonId] = array() : null;

            foreach ($usersDoneContent[$lessonId] as $key => $value) { //Unserialize and preprocess values. This way, only the array keys contain the content id, while array values contain test scores (when the unit is a test). This way we may use array_sum to calculate the mean score at once)
                //if ($value) {
                !isset($usersDoneContent[$lessonId][$key]) || !is_array(($usersDoneContent[$lessonId][$key])) ? $usersDoneContent[$lessonId][$key] = array() : null;
                foreach ($usersDoneContent[$lessonId][$key] as $k => $id) {
                    if (!in_array($id, $lessonContent[$lessonId])) {
                        unset($usersDoneContent[$lessonId][$key][$k]);
                    } else {
                        $usersDoneContent[$lessonId][$key][$k] = '';
                    }
                }

                if (isset($doneTests[$lessonId][$key])) {
                    is_array($usersDoneContent[$lessonId][$key]) ? $usersDoneContent[$lessonId][$key] = ($doneTests[$lessonId][$key] + $usersDoneContent[$lessonId][$key]) : $usersDoneContent[$lessonId][$key] = $doneTests[$lessonId][$key]; //We cannot use + for arrays, if one of them is not set.
                }
                if (isset($scormDoneContent[$lessonId][$key])) {
                    is_array($usersDoneContent[$lessonId][$key]) ? $usersDoneContent[$lessonId][$key] = ($scormDoneContent[$lessonId][$key] + $usersDoneContent[$lessonId][$key]) : $usersDoneContent[$lessonId][$key] = $scormDoneContent[$lessonId][$key];
                }
            }

            foreach ($usersDoneContent[$lessonId] as $key => $value) {
                if (!$value || ($users != false && !in_array($key, $users))) { //Filter out empty results or results not specified within $users array
                    unset($usersDoneContent[$lessonId][$key]);
                }
            }

            if (empty($usersDoneContent[$lessonId])) {
                unset($usersDoneContent[$lessonId]);
            }
        }

        return $usersDoneContent;
    }



    /**
     * Get user done tests in lesson
     *
     * This function finds the done tests of the specified users.
     * If $users is not specified, then information on all users and/or lessons
     * is calculated.
     * <br/>Example:
     * <code>
     * EfrontStats :: getStudentsDoneTests(3, 'jdoe');                          //Get statistics for user jdoe in lesson 3
     * EfrontStats :: getStudentsDoneTests(3, array('jdoe', 'george'));         //Get statistics for users george and jdoe in lesson 3
     * EfrontStats :: getStudentsDoneTests(3);                                  //Get statistics for all users in lesson 3
     * </code>
     * The resulting array is of the form array(login => array(content id => array(results))):
     * <code>
     * Array
     * (
     *     [jdoe] => Array
     *         (
     *             [30] => Array
     *                 (
     *                     [lessons_ID] => 78
     *                     [name] => Maya History Test
     *                     [content_ID] => 30
     *                     [done_tests_ID] => 1
     *                     [tests_ID] => 2
     *                     [score] => 1
     *                     [comments] =>
     *                     [users_LOGIN] => jdoe
     *                 )
     *             [2] => Array
     *                 (
     *                     [lessons_ID] => 77
     *                     [name] => General concepts test
     *                     [content_ID] => 2
     *                     [done_tests_ID] => 2
     *                     [tests_ID] => 1
     *                     [score] => 0.333333
     *                     [comments] =>
     *                     [users_LOGIN] => jdoe
     *                 )
     *         )
     *     [george] => Array
     *         (
     *             [2] => Array
     *                 (
     *                     [lessons_ID] => 77
     *                     [name] => General concepts test
     *                     [content_ID] => 2
     *                     [done_tests_ID] => 3
     *                     [tests_ID] => 1
     *                     [score] => 1
     *                     [comments] =>
     *                     [users_LOGIN] => george
     *                 )
     *         )
     * )
     * </code>
     *
     * @param mixed $lessons Either the lesson id or an EfrontLesson object, or an array of such
     * @param mixed $users A single user login, an array of user logins or nothing for all users
     * @return array The done tests per user
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getStudentsDoneTests($lessons = false, $users = false) {
        if (!$users) {
            $users = eF_getTableDataFlat("users", "login");
            $users = $users['login'];
        } elseif (!(is_array($users))) {
            $users = array($users);
        }

        if ($lessons !== false) {
            if (!is_array($lessons)) {
                $lessons = array($lessons);
            }
            foreach ($lessons as $key => $lesson) {
                $lesson instanceOf EfrontLesson ? $lessons[$key] = $lesson -> lesson['id'] : null;
            }
            $lessonId = implode(",", $lessons);
        }
/*
        $usersDoneTests = eF_getTableData("tests t, content c, done_tests dt", "c.lessons_ID, c.name, c.active, t.content_ID, dt.id as done_tests_ID, dt.tests_ID, dt.score, dt.comments, dt.users_LOGIN, dt.timestamp", "dt.tests_ID = t.id and t.content_ID = c.id".($lessonId ? " and c.lessons_ID in ($lessonId)" : ""));
        $doneTests      = array();
        foreach ($usersDoneTests as $doneTest) {
            if (!$users || in_array($doneTest['users_LOGIN'], $users)) {
                $doneTests[$doneTest['users_LOGIN']][$doneTest['content_ID']] = $doneTest;
            }
        }
*/
        $doneTests = array();
        //We create a 10-fold loop for memory efficiency
        for ($i = 0; $i < sizeof($users); $i += 20) {
         $temp = EfrontStats :: getDoneTestsPerUser(array_slice($users, $i, 10));
        }

        //@todo: This is for compatibility with previous version and should be removed in the future
        foreach ($temp as $user => $value) {
            foreach ($value as $testId => $testData) {
             if (is_array($testData)) {
              foreach ($testData as $done_tests_ID => $test) {
      if (is_numeric($done_tests_ID)) {
                         //$unit = $test -> getUnit();
                         $stats = array('lessons_ID' => $test['lessons_ID'],
                                        'name' => $test['name'],
                                        'active' => $test['active'],
                                        'content_ID' => $test['content_ID'],
                                        'done_tests_ID' => $done_tests_ID,
                                        'tests_ID' => $test['id'],
                                        'score' => $test['score'],
                               'active_score' => $testData['active_score'],
                               'active_test_id' => $testData['active_test_id'],
                               'status' => $test['status'],
                                        //'comments'   => $test -> completedTest['feedback'],
                                        'users_LOGIN'=> $user,
                                        'timestamp' => $test['time_end']);
                         if ($dt['archive'] == 0 && $test['status'] != 'incomplete' && $test['status'] != '' && ($lessons === false || in_array($stats['lessons_ID'], $lessons)) && in_array($stats['users_LOGIN'], $users)) {
                             $doneTests[$user][$test['content_ID']] = $stats;
                         }
              }
                 }
             }
            }
        }

        $usersDoneScormTests = eF_getTableData("content c, scorm_data sd", "c.lessons_ID, c.name, c.active, sd.masteryscore, sd.lesson_status, sd.content_ID, sd.score, sd.minscore, sd.maxscore, sd.users_LOGIN, sd.timestamp", "sd.lesson_status != 'incomplete' and sd.content_ID = c.id and c.ctg_type = 'scorm_test' and sd.users_LOGIN != ''".($lessonId ? " and c.lessons_ID in ($lessonId)" : ""));

        foreach ($usersDoneScormTests as $doneScormTest) {
            if (!$users || in_array($doneScormTest['users_LOGIN'], $users)) {

                if (is_numeric($doneScormTest['minscore']) && is_numeric($doneScormTest['maxscore'])) {
                    $doneScormTest['score'] = 100 * $doneScormTest['score'] / ($doneScormTest['minscore'] + $doneScormTest['maxscore']);
                } else {
                    $doneScormTest['score'] = $doneScormTest['score'];
                }

                $doneScormTest['active_score'] = $doneScormTest['score'];
                $doneScormTest['status'] = $doneScormTest['lesson_status'];

                $doneScormTest['scorm'] = true;
                $doneTests[$doneScormTest['users_LOGIN']][$doneScormTest['content_ID']] = $doneScormTest;
            }
        }

        return $doneTests;
    }


 /**
	 * Get users' done tests
	 *
	 * This function is used to get the users' done tests.
	 * <br/>Example:
	 * <code>
	 * $doneTests = EfrontStats :: getDoneTestsPerUser();				//Get done instances of all tests for all users
	 * $doneTests = EfrontStats :: getDoneTestsPerUser('jdoe');		//Get done instances of all tests for user 'jdoe'
	 * $doneTests = EfrontStats :: getDoneTestsPerUser(false, 23);		//Get done instances of test with id 23 for all users
	 * $doneTests = EfrontStats :: getDoneTestsPerUser('jdoe', 23);	//Get done instances of test with id 23 for user 'jdoe'
	 * //$doneTests now contains an array of the form:
	 * Array
	 * (
	 * 		[jdoe] => Array
	 * 			(
	 * 				[23] => Array
	 * 					(
	 * 						[1] => Array
	 * 							(
	 * 								//Completed test with id 1...
	 * 							)
	 * 						[2] => Array
	 * 							(
	 * 								//Completed test with id 2...
	 * 							)
	 * 					)
	 *			)
	 * )
	 * </code>
	 *
	 * @param mixed $user The user to get tests for, or all if false. Can be either the user login, or an EfrontUser object
	 * @param mixed $test The test to get done tests for, or all if false. Can be either the test id, or an EfrontTest object
	 * @return array The list of done tests, in a user => test id => tests form
	 * @since 3.5.2
	 * @access public
	 * @static
	 */
 public static function getDoneTestsPerUser($users = false, $test = false, $lesson = false) {
     if ($users !== false) {
         if (is_array($users)) {
             foreach ($users as $key => $user) {
                 if (!eF_checkParameter($user, 'login')) {
                     unset($users[$key]);
                 }
             }
             if (sizeof($users) == 0) {
                 throw new EfrontUserException(_INVALIDLOGIN.': '.implode(",", $users), EfrontUserException :: INVALID_LOGIN);
             }
         } else if ($users instanceof EfrontUser) {
             $users = array($users -> user['login']);
         } else if (!eF_checkParameter($users, 'login')) {
             throw new EfrontUserException(_INVALIDLOGIN.': '.$users, EfrontUserException :: INVALID_LOGIN);
         } else {
          $users = array($users);
         }
         $user = implode("','", $users);
     }

     if ($test !== false) {
         if ($test instanceof EfrontTest) {
             $test = $test -> test['id'];
         } else if (!eF_checkParameter($test, 'id')) {
             throw new EfrontTestException(_INVALIDID.': '.$test, EfrontTestException :: INVALID_ID);
         }
     }

     $sql = '';
     if ($lesson) {
      $sql = ' and t.lessons_ID='.$lesson;
     }

     if ($user && $test) {
         $result = eF_getTableData("completed_tests ct, tests t", "ct.id, ct.users_LOGIN, ct.tests_ID, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending, t.content_ID, t.lessons_ID, t.name, t.active, t.keep_best", "ct.status != 'deleted' and ct.tests_ID=t.id and ct.tests_ID=$test and ct.users_LOGIN in ('$user') $sql");
     } else if ($user) {
         $result = eF_getTableData("completed_tests ct, tests t", "ct.id, ct.users_LOGIN, ct.tests_ID, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending, t.content_ID, t.lessons_ID, t.name, t.active, t.keep_best", "ct.status != 'deleted' and ct.tests_ID=t.id and ct.users_LOGIN in ('$user') $sql");
     } else if ($test) {
         $result = eF_getTableData("completed_tests ct, tests t", "ct.id, ct.users_LOGIN, ct.tests_ID, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending, t.content_ID, t.lessons_ID, t.name, t.active, t.keep_best", "ct.status != 'deleted' and ct.tests_ID=t.id and ct.tests_ID=$test $sql");
     } else {
         $result = eF_getTableData("completed_tests ct, tests t", "ct.id, ct.users_LOGIN, ct.tests_ID, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending, t.content_ID, t.lessons_ID, t.name, t.active, t.keep_best", "ct.status != 'deleted' and ct.tests_ID=t.id $sql");
     }

     $testResults = array();
     foreach ($result as $value) {
         //$value['test'] = unserialize($value['test']);
         $testResults[$value['users_LOGIN']][$value['tests_ID']][$value['id']] = $value;
     }

     //Loop through objects, so that a per lesson/per test array can be constructed, with statistics for each
        foreach ($testResults as $user => $tests) {
            $averageScores = array();
            foreach ($tests as $testId => $doneTests) {
                foreach ($doneTests as $doneTestId => $doneTest) {
                    $testResults[$user][$testId]['scores'][$doneTest['id']] = $doneTest['score'];
                    $doneTest['archive'] == 0 ? $testResults[$user][$testId]['last_test_id'] = $doneTest['id'] : null;
                    $testResults[$user][$testId][$doneTestId] = $doneTest;
                }
                if (!isset($testResults[$user][$testId]['last_test_id'])){
                    end($testResults[$user][$testId]['scores']);
                    $testResults[$user][$testId]['last_test_id'] = key($testResults[$user][$testId]['scores']);
                }
                $testResults[$user][$testId]['average_score'] = round(array_sum($testResults[$user][$testId]['scores']) / sizeof($doneTests), 2);
                $testResults[$user][$testId]['max_score'] = max($testResults[$user][$testId]['scores']);
                $testResults[$user][$testId]['min_score'] = min($testResults[$user][$testId]['scores']);
                $testResults[$user][$testId]['times_done'] = sizeof($doneTests);
                if ($doneTest['keep_best']) {
                 $testResults[$user][$testId]['active_score'] = $testResults[$user][$testId]['max_score'];
                 $maxScoreId = array_search($testResults[$user][$testId]['max_score'], $testResults[$user][$testId]['scores']);
                 $testResults[$user][$testId]['active_test_id'] = $maxScoreId;
                } else {
                 $testResults[$user][$testId]['active_score'] = $testResults[$user][$testId]['scores'][$testResults[$user][$testId]['last_test_id']];
                 $testResults[$user][$testId]['active_test_id'] = $testResults[$user][$testId]['last_test_id'];
                }
                $averageScores[] = $testResults[$user][$testId]['average_score'];
            }
            $testResults[$user]['average_score'] = round(array_sum($averageScores) / sizeof($averageScores), 2);
        }
        //pr($testResults);
//exit;
     return $testResults;
 }


 /**
	 * Get users' done tests per test
	 *
	 * This function is used to get the users' done tests, on a per-test basis.
	 * <br/>Example:
	 * <code>
	 * $doneTests = EfrontStats :: getDoneTests();				//Get done instances of all tests for all users
	 * $doneTests = EfrontStats :: getDoneTests('jdoe');		//Get done instances of all tests for user 'jdoe'
	 * $doneTests = EfrontStats :: getDoneTests(false, 23);		//Get done instances of test with id 23 for all users
	 * $doneTests = EfrontStats :: getDoneTests('jdoe', 23);	//Get done instances of test with id 23 for user 'jdoe'
	 * //$doneTests now contains an array of the form:
	 * Array
	 * (
	 * 		[23] => Array
	 * 			(
	 * 				[jdoe] => Array
	 * 					(
	 * 						[1] => Array
	 * 							(
	 * 								//Completed test with id 1...
	 * 							)
	 * 						[2] => Array
	 * 							(
	 * 								//Completed test with id 2...
	 * 							)
	 * 					)
	 *			)
	 * )
	 * </code>
	 *
	 * @param mixed $user The user to get tests for, or all if false. Can be either the user login, or an EfrontUser object
	 * @param mixed $test The test to get done tests for, or all if false. Can be either the test id, or an EfrontTest object
	 * @return array The list of done tests, in a user => test id => tests form
	 * @since 3.5.2
	 * @access public
	 * @static
	 */
 public static function getDoneTestsPerTest($users = false, $test = false, $from = false, $to = false, $lesson = false) {

  if ($from !== false && $to !== false) {
   $timeString = " and ct.timestamp between $from and $to ";
  }
     if ($users !== false) {
         if (is_array($users)) {
          foreach ($users as $key => $user) {
                 if (!eF_checkParameter($user, 'login')) {
                     unset($users[$key]);
                 }
             }
             if (sizeof($users) == 0) {
                 throw new EfrontUserException(_INVALIDLOGIN.': '.implode(",", $users), EfrontUserException :: INVALID_LOGIN);
             }
         } else if ($users instanceof EfrontUser) {
             $users = array($users -> user['login']);
         } else if (!eF_checkParameter($users, 'login')) {
             throw new EfrontUserException(_INVALIDLOGIN.': '.$users, EfrontUserException :: INVALID_LOGIN);
         }
         $user = implode("','", $users);
     }
     if ($test !== false) {
         if ($test instanceof EfrontTest) {
             $test = $test -> test['id'];
         } else if (!eF_checkParameter($test, 'id')) {
             throw new EfrontTestException(_INVALIDID.': '.$test, EfrontTestException :: INVALID_ID);
         }
     }
     if ($lesson) {
      $sql = ' and t.lessons_ID='.$lesson;
     }

     if ($user && $test) {
         $result = eF_getTableData("completed_tests ct, tests t", "t.keep_best, t.mastery_score, ct.id, ct.users_LOGIN, ct.tests_ID, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending", "ct.status != 'deleted' and ct.tests_ID = t.id and ct.status != '' and ct.status != 'incomplete'".$timeString." and ct.tests_ID=$test and ct.users_LOGIN in ('$user') $sql");
     } else if ($user) {
         $result = eF_getTableData("completed_tests ct, tests t", "t.keep_best, t.mastery_score, ct.id, ct.users_LOGIN, ct.tests_ID, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending", "ct.status != 'deleted' and ct.tests_ID = t.id and ct.status != '' and ct.status != 'incomplete'".$timeString." and ct.users_LOGIN in ('$user') $sql");
         //$result = eF_getTableData("completed_tests ct, tests t", "ct.users_LOGIN, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending", "ct.status != 'deleted' and ct.tests_ID = t.id and ct.status != '' and ct.status != 'incomplete'".$timeString." and ct.users_LOGIN in ('$user') $sql");
     } else if ($test) {
         $result = eF_getTableData("completed_tests ct, tests t", "t.keep_best, t.mastery_score, ct.id, ct.users_LOGIN, ct.tests_ID, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending", "ct.status != 'deleted' and ct.tests_ID = t.id and ct.status != '' and ct.status != 'incomplete'".$timeString." and ct.tests_ID=$test $sql");
     } else {
         $result = eF_getTableData("completed_tests ct, tests t", "t.keep_best, t.mastery_score, ct.id, ct.users_LOGIN, ct.tests_ID, ct.status, ct.timestamp, ct.archive, ct.time_start, ct.time_end, ct.time_spent, ct.score, ct.pending", "ct.status != 'deleted' and ct.tests_ID = t.id and ct.status != '' and ct.status != 'incomplete'".$timeString." $sql");
     }

     //Unserialize EfrontCompletedTest objects
     $testResults = array();

     foreach ($result as $value) {
         //$value['test'] = unserialize($value['test']);
         $testResults[$value['tests_ID']][$value['users_LOGIN']][$value['id']] = $value;
     }

     //Loop through objects, so that a per lesson/per test array can be constructed, with statistics for each
        foreach ($testResults as $testId => $logins) {
            $averageScores = array();
         foreach ($logins as $user => $doneTests) {
                foreach ($doneTests as $doneTestId => $doneTest) {
                    $testResults[$testId][$user]['scores'][$doneTest['id']] = $doneTest['score'] ? $doneTest['score'] : 0;
                    $doneTest['archive'] == 0 ? $testResults[$testId][$user]['last_test_id'] = $doneTest['id'] : null;
                    //$doneTest['test']                         = serialize($doneTest['test']);
                    $testResults[$testId][$user][$doneTestId] = $doneTest;

                }
                if (!isset($testResults[$testId][$user]['last_test_id'])){
                    end($testResults[$testId][$user]['scores']);
                    $testResults[$testId][$user]['last_test_id'] = key($testResults[$testId][$user]['scores']);
                }
                $testResults[$testId][$user]['average_score'] = round(array_sum($testResults[$testId][$user]['scores']) / sizeof($doneTests), 2);
                $testResults[$testId][$user]['max_score'] = max($testResults[$testId][$user]['scores']) ? max($testResults[$testId][$user]['scores']) : 0;
                $testResults[$testId][$user]['min_score'] = min($testResults[$testId][$user]['scores']) ? min($testResults[$testId][$user]['scores']) : 0;
                $testResults[$testId][$user]['times_done'] = sizeof($doneTests);
                if ($doneTest['keep_best']) {
                 $testResults[$testId][$user]['active_score'] = $testResults[$testId][$user]['max_score'];
                 $maxScoreId = array_search($testResults[$testId][$user]['max_score'], $testResults[$testId][$user]['scores']);
                 $testResults[$testId][$user]['active_test_id'] = $maxScoreId;
                } else {
                 $testResults[$testId][$user]['active_score'] = $testResults[$testId][$user]['scores'][$testResults[$testId][$user]['last_test_id']];
                 $testResults[$testId][$user]['active_test_id'] = $testResults[$testId][$user]['last_test_id'];
                }
                $averageScores[] = $testResults[$testId][$user]['average_score'];
            }
            $testResults[$testId]['average_score'] = round(array_sum($averageScores) / sizeof($averageScores), 2);
        }

     return $testResults;
 }

    /**
     * Get user login time in lessons
     *
     * This function calculates the total time each student spent in any lesson
     * <br/>Example:
     * <code>
     * EfrontStats :: getUsersTimeAll();                          			//Get statistics for all users in all lessons
     * EfrontStats :: getUsersTimeAll(1259135220, 1259394420);             //Get statistics for all users in all lessons from 1259135220 to 1259394420
     * </code>
     * The results are of the form:
     * <code>
     *   Array
     *   (
     *   	[3] => Array
     *   	{
     *       [jdoe] => Array
     *           (
     *               [minutes] => 0
     *               [seconds] => 42
     *               [hours] => 0
     *               [total_seconds] => 42
     *               [accesses] => 5
     *           )
     *      }
     *   )
     * </code>
     * Accesses are the times the user accessed content on the lesson. total_seconds is the sum of time spent in the lesson in seconds
     *
     * @param int $fromTimestamp The time to calculate statistics from
     * @param int $toTimestamp The time to calculate statistics to
     * @param array $lessons The lessons to retrieve statistics for
     * @param array $users The users to retrieve statistics for
     * @return array The total time per lesson/user
     * @since 3.6.0
     * @access public
     * @static
     */
    public static function getUsersTimeAll($fromTimestamp = false, $toTimestamp = false, $lessons = false, $users = false) {
     //pr($users);exit;
        !$fromTimestamp ? $fromTimestamp = mktime(0, 0, 0, 1, 1, 2000) : null;
        !$toTimestamp ? $toTimestamp = time() : null;
        !$users || empty($users) ? $usersSql = '' : $usersSql = "users_LOGIN in (\"".implode('","', $users)."\") and";

        $result = eF_getTableData("logs", "*", "$usersSql timestamp between $fromTimestamp and $toTimestamp order by timestamp");

        foreach ($result as $value) {
            $logResults[$value['users_LOGIN']][] = $value;
            $resultLessons[$value['lessons_ID']] = $value['lessons_ID'];
        }
        unset($resultLessons[0]);
        if (!$lessons) {
         $lessons = $resultLessons;
        }

        $result = eF_getTableData("logs", "lessons_ID, users_LOGIN, count(id) as accesses", "$usersSql timestamp between $fromTimestamp and $toTimestamp group by lessons_ID, users_LOGIN order by users_LOGIN");
        foreach ($result as $value) {
            $accessResults[$value['lessons_ID']][$value['users_LOGIN']]= $value['accesses'];
        }

        $userTimes = array();

        foreach ($lessons as $lessonId) {
   $users = array_keys($logResults);

         foreach ($users as $login) {
             $totalTime = array('minutes' => 0, 'seconds' => 0, 'hours' => 0, 'total_seconds' => 0);
             $lessonStart = 0;
             $inLesson = 0;
             if (isset($logResults[$login])) {
                 foreach ($logResults[$login] as $value) {
                     if ($inLesson) {
                         if ($value['timestamp'] - $lessonStart >= 0) {
                             $interval = eF_convertIntervalToTime($value['timestamp'] - $lessonStart);
                         } else {
                             $interval = eF_convertIntervalToTime(0); //This is to avoid negative times
                         }

                         if ($interval['hours'] == 0 && $interval['minutes'] <= 30) { //not with $GLOBALS['configuration']['autologout_time'] because it may be changed
                             $totalTime['minutes'] += $interval['minutes'];
                             $totalTime['seconds'] += $interval['seconds'];
                         }

                         if ($value['lessons_ID'] != $lessonId) {
                             $inLesson = 0;
                         } else {
                             $lessonStart = $value['timestamp'];
                         }
                     } else if ($value['lessons_ID'] == $lessonId) {
                         $inLesson = 1;
                         $lessonStart = $value['timestamp'];
                     }
                 }
             }

             $sec = $totalTime['seconds'];

             if ($sec >= 60) {
                 $totalTime['seconds'] = $sec % 60;;
                 $totalTime['minutes'] += floor($sec / 60);;
             }
             if ($totalTime['minutes'] >= 60) {
                 $totalTime['hours'] = floor($totalTime['minutes']/60);;
                 $totalTime['minutes'] = $totalTime['minutes'] % 60;;
             }

             $totalTime['total_seconds'] = $totalTime['hours'] * 3600 + $totalTime['minutes'] * 60 + $totalTime['seconds'];
             $userTimes[$lessonId][$login] = $totalTime;
             isset($accessResults[$lessonId][$login]) ? $userTimes[$lessonId][$login]['accesses'] = $accessResults[$lessonId][$login] : $userTimes[$lessonId][$login]['accesses'] = 0;

         }
        }

        return $userTimes;
    }


    /**
     * Get user login time in a lesson
     *
     * This function calculates the total time each student spent in the lesson
     * If $users is not specified, then information on all users is calculated.
     * <br/>Example:
     * <code>
     * EfrontStats :: getUsersTime(3, 'jdoe');                          //Get statistics for user jdoe in lesson 3
     * EfrontStats :: getUsersTime(3, array('jdoe', 'george'));         //Get statistics for users george and jdoe in lesson 3
     * EfrontStats :: getUsersTime(3);                                  //Get statistics for all users in lesson 3
     * </code>
     * The results are of the form:
     * <code>
     *   Array
     *   (
     *       [jdoe] => Array
     *           (
     *               [minutes] => 0
     *               [seconds] => 42
     *               [hours] => 0
     *               [total_seconds] => 42
     *               [accesses] => 5
     *           )
     *   )
     * </code>
     * Accesses are the times the user accessed content on the lesson. total_seconds is the sum of time spent in the lesson in seconds
     *
     * @param int $lesson Either the lesson id or the equivalent EfrontLesson object
     * @param mixed $users One or more optional user logins
     * @return array The total time per user
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getUsersTime($lesson, $users = false, $fromTimestamp = false, $toTimestamp = false) {
        if (!($lesson instanceof EfrontLesson)) {
            $lesson = new EfrontLesson($lesson);
        }
        $lessonId = $lesson -> lesson['id'];

        if (!$users) {
            $users = array_keys($lesson -> getUsers());
        } else if (!is_array($users)) {
            $users = array($users);
        }

        !$fromTimestamp ? $fromTimestamp = mktime(0, 0, 0, 1, 1, 1970) : null;
        !$toTimestamp ? $toTimestamp = time() : null;

        $result = eF_getTableData("logs", "id, timestamp, lessons_ID, users_LOGIN", "users_LOGIN in (\"".implode('","', $users)."\") and timestamp between $fromTimestamp and $toTimestamp order by timestamp");
        foreach ($result as $value) {
            $logResults[$value['users_LOGIN']][] = $value;
        }
        $result = eF_getTableData("logs", "users_LOGIN, count(id) as accesses", "users_LOGIN in (\"".implode('","', $users)."\") and lessons_ID = $lessonId and timestamp between $fromTimestamp and $toTimestamp group by users_LOGIN order by users_LOGIN");
        foreach ($result as $value) {
            $accessResults[$value['users_LOGIN']]= $value['accesses'];
        }

        $userTimes = array();
        foreach ($users as $login) {
            $totalTime = array('minutes' => 0, 'seconds' => 0, 'hours' => 0, 'total_seconds' => 0);
            $lessonStart = 0;
            $inLesson = 0;
            if (isset($logResults[$login])) {
                foreach ($logResults[$login] as $value) {
                    if ($inLesson) {
                        if ($value['timestamp'] - $lessonStart >= 0) {
                            $interval = eF_convertIntervalToTime($value['timestamp'] - $lessonStart);
                        } else {
                            $interval = eF_convertIntervalToTime(0); //This is to avoid negative times
                        }

                        if ($interval['hours'] == 0 && $interval['minutes'] <= 30) { //not with $GLOBALS['configuration']['autologout_time'] because it may be changed
                            $totalTime['minutes'] += $interval['minutes'];
                            $totalTime['seconds'] += $interval['seconds'];
                        }

                        if ($value['lessons_ID'] != $lessonId) {
                            $inLesson = 0;
                        } else {
                            $lessonStart = $value['timestamp'];
                        }
                    } else if ($value['lessons_ID'] == $lessonId) {
                        $inLesson = 1;
                        $lessonStart = $value['timestamp'];
                    }
                }
            }

            $sec = $totalTime['seconds'];

            if ($sec >= 60) {
                $totalTime['seconds'] = $sec % 60;;
                $totalTime['minutes'] += floor($sec / 60);;
            }
            if ($totalTime['minutes'] >= 60) {
                $totalTime['hours'] = floor($totalTime['minutes']/60);;
                $totalTime['minutes'] = $totalTime['minutes'] % 60;;
            }

            $totalTime['total_seconds'] = $totalTime['hours'] * 3600 + $totalTime['minutes'] * 60 + $totalTime['seconds'];
            $userTimes[$login] = $totalTime;
            isset($accessResults[$login]) ? $userTimes[$login]['accesses'] = $accessResults[$login] : $userTimes[$login]['accesses'] = 0;
        }

        return $userTimes;
    }

    /**
     * Get user assigned projects in lesson
     *
     * This function finds the assigned projects to the specified users.
     * If $users is not specified, then information on all users
     * is calculated. If the project is done, done information is included also.
     * <br/>Example:
     * <code>
     * EfrontStats :: getStudentsAssignedProjects(3, 'jdoe');                           //Get statistics for user jdoe in lesson 3
     * EfrontStats :: getStudentsAssignedProjects(3, array('jdoe', 'george'));          //Get statistics for users george and jdoe in lesson 3
     * EfrontStats :: getStudentsAssignedProjects(3);                                   //Get statistics for all users in lesson 3
     * </code>
     *
     * @param mixed $lessons Either a single lesson id or ana array of lesson ids (or EfrontLesson objects)
     * @param mixed $users One or more optional user logins
     * @return array The assigned projects per user login
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getStudentsAssignedProjects($lessons = false, $users = false) {
        if (!$users) {
            $users = eF_getTableDataFlat("users", "login");
            $users = $users['login'];
        }
        if (!(is_array($users))) {
            $users = array($users);
        }

        if ($lessons === false) {
            $usersAssignedProjects = eF_getTableData("projects p, users_to_projects up", "p.title, p.lessons_ID, up.projects_ID, up.grade, up.upload_timestamp, up.users_LOGIN as login, up.comments", "up.projects_ID = p.id");
        } else {
            if (!is_array($lessons)) {
                $lessons = array($lessons);
            }
            foreach ($lessons as $key => $lesson) {
                $lesson instanceOf EfrontLesson ? $lessons[$key] = $lesson -> lesson['id'] : null;
            }
            if (sizeof($lessons) > 0) {
                $usersAssignedProjects = eF_getTableData("projects p, users_to_projects up", "p.title, p.lessons_ID, up.projects_ID, up.grade, up.upload_timestamp, up.users_LOGIN as login, up.comments", "up.projects_ID = p.id and p.lessons_ID in (".implode(",", $lessons).")");
            } else {
                $usersAssignedProjects = array();
            }
        }

        $asignedProjects = array();
        foreach ($usersAssignedProjects as $project) {
            $login = $project['login'];
            if (!$users || in_array($login, $users)) {
                $asignedProjects[$login][$project['projects_ID']] = $project;
            }
        }
        return $asignedProjects;
    }


    public static function getUsersForumPosts($lessonId, $users = false) {
        //pr($lessonId);
  $total_posts = array();
        $result = eF_getTableData("f_messages fm, f_topics ft, f_forums ff", "fm.users_LOGIN as login, count(*) as cnt", "fm.f_topics_ID = ft.id and ft.f_forums_ID = ff.id and ff.lessons_ID = ".$lessonId. " group by fm.users_LOGIN");
    //pr($resul);
  foreach ($result as $data) {
            $total_posts[$data['login']] = $data['cnt'];
        }
        foreach ($users as $login) {
            if (!isset($total_posts[$login])) {
                $total_posts[$login] = 0;
            }
        }
        return $total_posts;
    }

    public static function getUsersComments($lessonId, $users = false) {
        $total_comments = array();
        $result = eF_getTableData("comments cm, content c", "cm.users_LOGIN as login, count(*) as cnt", "cm.content_id = c.id and c.lessons_ID = ".$lessonId. " group by cm.users_LOGIN");
        foreach ($result as $data) {
            $total_comments[$data['login']] = $data['cnt'];
        }
        foreach ($users as $login) {
            if (!isset($total_comments[$login])) {
                $total_comments[$login] = 0;
            }
        }
        return $total_comments;
    }


    /**
     * Get lesson(s) status for user(s)
     *
     * This function is used to calculate the specified lessons status for the specified users
     * It calculates the progress, percentages, scores etc that describe the users' status
     * to the lesson.
     * <br/>Example:
     * <code>
     * $status = EfrontStats :: getUsersLessonStatus(34, 'jdoe');		//Get the status for user jdoe in lesson with id 34
     * $status = EfrontStats :: getUsersLessonStatus(false, 'jdoe');	//Get the status for user jdoe in all lessons
     * $status = EfrontStats :: getUsersLessonStatus(34);				//Get the status for all users in lesson with id 34
     * $status = EfrontStats :: getUsersLessonStatus();					//Get the status for all users in all lessons
     * </code>
     * Note: This function is designed so that there is never the need to call it inside a loop
     * Since it is database-intensive, make sure that it is NEVER called inside a loop!
     *
     * @param array $lessons an array of lesson ids or EfrontLesson objects
     * @param array $users An array of user logins
     * @return array The lesson status
     * @since 3.5.0
     * @access public
     */
    public static function getUsersLessonStatus($lessons = false, $users = false, $options = array()) {

        if ($lessons === false) {
            $lessons = eF_getTableData("lessons", "*");
        } else if (!is_array($lessons)) {
            $lessons = array($lessons);
        }

        if ($users != false) {
            !is_array($users) ? $users = array($users) : null; //Convert single login to array
        } else {
            $users = eF_getTableDataFlat("users", "login", "user_type != 'administrator'");
            $users = $users['login'];
        }

        foreach ($lessons as $lesson) {
            foreach ($users as $user) {
             if ($lesson instanceOf EfrontLesson) {
              $lessonId = $lesson -> lesson['id'];
             } else {
              $lessonId = $lesson;
             }
             $lessonStatus[$lessonId][$user] = self :: getUserLessonStatus($lesson, $user, $options);
            }
        }

        return $lessonStatus;
    }

    /**
     * Get user(s) status in course(s)
     *
     * This function is used to calculate the user's status in the course, ie the score, completed
     * etc. It also calculates statistics for all lessons inside the course
     * <br>Example:
     * <code>
     * $status = EfrontStats :: getUsersCourseStatus(34, 'jdoe');		//Get the status for user jdoe in course with id 34
     * $status = EfrontStats :: getUsersCourseStatus(false, 'jdoe');	//Get the status for user jdoe in all courses
     * $status = EfrontStats :: getUsersCourseStatus(34);				//Get the status for all users in course with id 34
     * $status = EfrontStats :: getUsersCourseStatus();					//Get the status for all users in all courses
     *</code>
     * Note: This function is designed so that there is never the need to call it inside a loop
     * Since it is database-intensive, make sure that it is NEVER called inside a loop!
     *
     * @param mixed $courses an array of course ids or EfrontCourse objects
     * @param mixed $users an array of users logins
     * @return array The user status in courses
     * @since 3.5.0
     * @access public
     */
    public static function getUsersCourseStatus($courses = false, $users = false, $options = array()) {

        if ($courses === false) {
            $courses = eF_getTableData("courses", "*");
        } else if (!is_array($courses)) {
            $courses = array($courses);
        }
        $coursesLessons = array();

        foreach ($courses as $key => $course) {
            if (!($course instanceof EfrontCourse)) {
                $course = new EfrontCourse($course);
            }
            $coursesLessons = $course -> getCourseLessons() + $coursesLessons;
            $temp[$course -> course['id']] = $course;
        }
        $courses = $temp;

        if ($users != false) {
            !is_array($users) ? $users = array($users) : null; //Convert single login to array
        } else {
            $users = eF_getTableDataFlat("users", "login", "user_type != 'administrator'");
            $users = $users['login'];
        }

        foreach ($courses as $course) {
            foreach ($users as $user) {
                $courseStatus[$course -> course['id']][$user] = self :: getUserCourseStatus($course, $user, $options);
            }
        }

        return $courseStatus;
    }

    public function getUserCourseStatus($course, $user, $options) {
        $cacheKey = 'user_course_status:';
        $course instanceOf EfrontCourse ? $cacheKey .= 'course:'.$course -> course['id'] : $cacheKey .= 'course:'.$course;
        $user instanceOf EfrontUser ? $cacheKey .= 'user:'.$user -> user['login'] : $cacheKey .= 'user:'.$user;
/*
        if ($status = Cache::getCache($cacheKey)) {
            return unserialize($status);
        } else  {
            $storeCache = true;
        }
*/
        if (!($user instanceOf EfrontUser)) {
            $user = EfrontUserFactory :: factory($user);
            $user = $user -> user;
        }
        if (!($course instanceof EfrontCourse)) {
            $course = new EfrontCourse($course);
        }
        $roles = EfrontLessonUser :: getLessonsRoles();
        foreach ($roles as $key => $value) {
         $value == 'student' ? $studentLessonRoles[] = $key : null;
        }
        $courseLessons = $course -> getCourseLessons();
        $lessonsStatus = self :: getUsersLessonStatus($courseLessons, $user['login'], $options);

        $result = eF_getTableData("users_to_courses", "*", "courses_ID = ".$course -> course['id']." and users_LOGIN='".$user['login']."'");
        if (sizeof($result) > 0) {
            if ($course -> course['duration'] && $result[0]['from_timestamp']) {
                $result[0]['remaining'] = $result[0]['from_timestamp'] + $course -> course['duration']*3600*24 - time();
            } else {
                $result[0]['remaining'] = null;
            }
            //Check whether the course registration is expired. If so, set $result[0]['from_timestamp'] to false, so that the effect is to appear disabled
            if ($course -> course['duration'] && $result[0]['from_timestamp'] && $course -> course['duration'] * 3600 * 24 + $result[0]['from_timestamp'] < time()) {
                $course -> removeUsers($result[0]['users_LOGIN']);
            } else {
                $usersCourses[$result[0]['courses_ID']][$result[0]['users_LOGIN']] = $result[0];
                $usersCoursesTypes[$result[0]['courses_ID']][$result[0]['users_LOGIN']] = $roles[$result[0]['user_type']]; //Handy since we need to know whether a course has any students
            }
        }

        $courseStatus = array();
        //transpose and filter s statistics array, for convenience, from  lesson id => login to login => lesson id
        foreach ($lessonsStatus as $lessonId => $info) {
            if (in_array($lessonId, array_keys($courseLessons))) {
                foreach ($info as $login => $stats) {
                    $userLessonStatus[$lessonId] = $stats;
                }
            }
        }

        if (sizeof($usersCourses[$course -> course['id']]) > 0) {
            foreach ($usersCourses[$course -> course['id']] as $login => $value) {
                $courseStatus = array('login' => $login,
                                                                            'name' => $user['name'],
                                                                            'surname' => $user['surname'],
                                                                            'basic_user_type' => $user['user_type'],
                                                                            'user_type' => $value['user_type'], //User type in course
                                                                            'user_types_ID' => $user['user_types_ID'],
                                                                         'different_role' => (!$users[$login]['user_types_ID'] && $value['user_type'] != $users[$login]['user_type']) || ($users[$login]['user_types_ID'] && $value['user_type'] != $users[$login]['user_types_ID']), //Whether the user has a role different than the default in this lesson
                               'active' => $user['active'],
                                                                            'course_name' => $course -> course['name'],
                                                                            'from_timestamp' => $value['from_timestamp'],
                                                                            'remaining' => $value['remaining']);
                //Student - specific information
                if ($roles[$value['user_type']] == 'student') {
                    $courseStatus['completed'] = $value['completed'];
                    $courseStatus['score'] = $value['score'];
                    $courseStatus['comments'] = $value['comments'];
                    $courseStatus['issued_certificate'] = $value['issued_certificate'];
                    $courseStatus['total_lessons'] = sizeof($course -> countCourseLessons());
                    //Count completed lessons
                    $completedLessons = 0;
                    if (isset($userLessonStatus)) {
                        foreach ($userLessonStatus as $lesson) {
                            if ($lesson['completed']) {
                                $completedLessons++;
                            }
                        }
                    }
                    $courseStatus['completed_lessons'] = $completedLessons;
                }
                //Append the course's lessons information
                $courseStatus['lesson_status'] = $userLessonStatus;
            }
        }


        if ($storeCache) {
         //Cache::setCache($cacheKey, serialize($courseStatus));
        }

        return $courseStatus;
    }

    public function getUserLessonStatus($lesson, $user, $options) {
/*
    	$cacheKey = 'user_lesson_status:';
        $lesson instanceOf EfrontLesson ? $cacheKey .= 'lesson:'.$lesson -> lesson['id'] : $cacheKey .= 'lesson:'.$lesson;
        $user   instanceOf EfrontUser   ? $cacheKey .= 'user:'.$user -> user['login']    : $cacheKey .= 'user:'.$user;
        if ($status = Cache::getCache($cacheKey)) {
            return unserialize($status);
        } else  {
            $storeCache = true;
        }
*/
     $times = new EfrontTimes();
     $usersTimesInLessonContent = array();
     if ($lesson instanceOf EfrontLesson) {
      $lessonId = $lesson->lesson['id'];
     } else {
      $lessonId = $lesson;
     }
     foreach ($times->getUsersSessionTimeInLessonContent($lessonId) as $value) {
      $usersTimesInLessonContent[$value['users_LOGIN']] = $value['time'];
     }
     $usersDoneContent = EfrontStats :: getStudentsSeenContent($lesson, $user, $options); //Calculate the done content for users in this lesson
     $usersAssignedProjects = array();
     if (!isset($options['noprojects']) || !$options['noprojects']) {
   $usersAssignedProjects = EfrontStats :: getStudentsAssignedProjects($lesson, $user);
     }
     $usersDoneTests = array();
     if (!isset($options['notests']) || !$options['notests']) {
      $usersDoneTests = EfrontStats :: getStudentsDoneTests($lesson, $user);
     }

        $roles = EfrontLessonUser :: getLessonsRoles();
        //transpose projects array, from (login => array(project id => project)) to array(lesson id => array(login => array(project id => project)))
        $temp = array();
        foreach ($usersAssignedProjects as $login => $userProjects) {
            foreach ($userProjects as $projectId => $project) {
                $temp[$project['lessons_ID']][$login][$projectId] = $project;
            }
        }
        $usersAssignedProjects = $temp;

        //transpose tests array, from (login => array(test id => test)) to array(lesson id => array(login => array(test id => test)))
        $temp = array();
        foreach ($usersDoneTests as $login => $userTests) {
            foreach ($userTests as $contentID => $test) {
                $temp[$test['lessons_ID']][$login][$contentID] = $test;
            }
        }
        $usersDoneTests = $temp;

        if (!($user instanceOf EfrontUser)) {
            $user = EfrontUserFactory :: factory($user);
            $user = $user -> user;
        }
        if (!($lesson instanceof EfrontLesson)) {
            $lesson = new EfrontLesson($lesson);
        }

        $result = eF_getTableData("users_to_lessons", "*", "users_LOGIN ='".$user['login']."' and lessons_ID = ".$lesson -> lesson['id']);
        if (sizeof($result[0]['users_LOGIN']) > 0) {
            if ($lesson -> lesson['duration'] && $result[0]['from_timestamp']) {
                $result[0]['remaining'] = $result[0]['from_timestamp'] + $lesson -> lesson['duration']*3600*24 - time();
            } else {
                $result[0]['remaining'] = null;
            }
            //Check whether the lesson registration is expired. If so, set $result[0]['from_timestamp'] to false, so that the effect is to appear disabled
            if ($lesson -> lesson['duration'] && $result[0]['from_timestamp'] && $lesson -> lesson['duration'] * 3600 * 24 + $result[0]['from_timestamp'] < time()) {
                $lesson -> removeUsers($result[0]['users_LOGIN']);
            } else {
                $usersLessons[$result[0]['lessons_ID']][$result[0]['users_LOGIN']] = $result[0];
                $usersLessonsTypes[$result[0]['lessons_ID']][$result[0]['users_LOGIN']] = $roles[$result[0]['user_type']]; //Handy since we need to know whether a lesson has any students
            }
        }

        //Build a caching set for conditions, so that we avoid looping queries inside $lesson -> getConditions();
        $result = eF_getTableData("lesson_conditions", "*", "lessons_ID=".$lesson -> lesson['id']);
  $conditions = array();
        foreach ($result as $value) {
         $conditions[$value['lessons_ID']][] = $value;
        }
        $lessonStatus = array();

            if (in_array('student', $usersLessonsTypes[$lesson -> lesson['id']])) { //Calculate these statistics only if the lesson has students
    !isset($conditions[$lesson -> lesson['id']]) ? $conditions[$lesson -> lesson['id']] = array() : null;
                $lessonConditions = $lesson -> getConditions($conditions[$lesson -> lesson['id']]);
                $lessonContent = new EfrontContentTree($lesson);
                $doneContent = isset($usersDoneContent[$lesson -> lesson['id']]) ? $usersDoneContent[$lesson -> lesson['id']] : array();
                $doneTests = isset($usersDoneTests[$lesson -> lesson['id']]) ? $usersDoneTests[$lesson -> lesson['id']] : array();

                $assignedProjects = isset($usersAssignedProjects[$lesson -> lesson['id']]) ? $usersAssignedProjects[$lesson -> lesson['id']] : array();

                $visitableContentIds = array();
                $visitableExampleIds = array();
                $visitableTestIds = array();
                $testIds = array();

                foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST))) as $key => $value) {
                    switch($value -> offsetGet('ctg_type')) {
                     case 'theory':
                     case 'scorm':
                      $visitableContentIds[$key] = $key; //Get the not-test unit ids for this content
                      break;
                     case 'examples':
                      $visitableExampleIds[$key] = $key; //Get the not-test unit ids for this content
                      break;
                     case 'tests':
                     case 'scorm_test':
                      $visitableTestIds[$key] = $key; //Get the scorm test unit ids for this content
                      $testIds[$key] = $key; //Get the test unit ids for this content
                      break;
                    }
                }

                $visitableUnits = $visitableContentIds + $visitableExampleIds + $visitableTestIds;
            }

            foreach ($usersLessons[$lesson -> lesson['id']] as $login => $value) {

                $lessonStatus = array('login' => $login,
                                                                        'name' => $user['name'],
                                                                        'surname' => $user['surname'],
                                                                        'basic_user_type' => $user['user_type'],
                                                                        'user_type' => $value['user_type'], //The user's role in the lesson
                              'user_types_ID' => $user['user_types_ID'],
                                                                        'different_role' => $value['user_type'] != $user['user_type'] && $value['user_type'] != $user['user_types_ID'], //Whether the user has a role different than the default in this lesson
                                                                        'different_role' => (!$users[$login]['user_types_ID'] && $value['user_type'] != $users[$login]['user_type']) || ($users[$login]['user_types_ID'] && $value['user_type'] != $users[$login]['user_types_ID']), //Whether the user has a role different than the default in this lesson
                                                                        'active' => $user['active'],
                                                                        'lesson_name' => $lesson -> lesson['name'],
                                                                        'from_timestamp' => $value['from_timestamp'],
                                                                        'remaining' => $value['remaining']);
                //Student - specific information
                if ($roles[$value['user_type']] == 'student') {
                    !isset($doneContent[$login]) ? $doneContent[$login] = array() : null;
                    !isset($assignedProjects[$login]) ? $assignedProjects[$login] = array() : null;

                    list($conditionsMet, $lessonPassed) = self :: checkConditions($doneContent[$login], $lessonConditions, $visitableUnits, $visitableTestIds, $usersTimesInLessonContent[$login]);

                    //Content progress is theory and examples units seen
                    $contentProgress = 0;
                    if (isset($doneContent[$login]) && sizeof($doneContent[$login]) > 0 && (sizeof($visitableContentIds) > 0 || sizeof($visitableExampleIds) > 0)) {
                        $contentProgress = round(100 * sizeof(array_diff_key($doneContent[$login], $visitableTestIds)) / (sizeof($visitableContentIds) + sizeof($visitableExampleIds)), 2);
                    }

                    //Calculate tests average score and progress
                    $testsProgress = 0;
                    $numCompletedTests = 0;
                    $testsAvgScore = array();
                    if (sizeof($testIds) > 0 && isset($doneTests[$login]) && sizeof($doneTests[$login]) > 0) {
                        foreach ($doneTests[$login] as $doneTest) {
                            $testsAvgScore[] = $doneTest['score'];
                        }

                        $testsAvgScore = array_sum($testsAvgScore) / sizeof($testsAvgScore);

                        $numCompletedTests = 0;
                        if (!isset($doneTest[$login['lesson_status']]) || $doneTest[$login['lesson_status']] == 'passed' || $doneTest[$login['lesson_status']] == 'completed') {
                             $numCompletedTests++;
                        }
                        $testsProgress = round(100 * $numCompletedTests / sizeof($visitableTestIds), 2);
                    } else {
                        $testsAvgScore = 0;
                    }

                    //Calculate projects average score and build done projects list, since we don't have this automatically
                    $doneProjects = array();
                    $projectsAvgScore = array();
                    $projectsProgress = 0;
                    if (sizeof($assignedProjects[$login]) > 0) {
                        foreach ($assignedProjects[$login] as $id => $project) {
                            if ($project['grade'] !== '' || $project['upload_timestamp']) {
                                $doneProjects[$id] = $project;
                                $projectsAvgScore[] = $project['grade'];
                            }
                        }
                        sizeof($doneProjects) > 0 ? $projectsAvgScore = array_sum($projectsAvgScore) / sizeof($projectsAvgScore) : $projectsAvgScore = 0;

                        $projectsProgress = round(100 * sizeof($doneProjects) / sizeof($assignedProjects[$login]), 2);
                    } else {
                        $projectsAvgScore = 0;
                    }

                    //Calculate overall progress, the number of done content + done (passed for SCORM) tests divided with the total units number
                    $overallProgress = 0;

                    if (sizeof($visitableUnits) > 0) {
                        $overallProgress = round(100 * (sizeof(array_intersect(array_keys($visitableUnits), array_keys($doneContent[$login])))) / sizeof($visitableUnits), 2);
                    }

                    $lessonStatus['assigned_projects'] = $assignedProjects[$login]; //the total assigned projects to the user, with information for each one.
                    $lessonStatus['projects_progress'] = $projectsProgress; //the projects percentage done
                    $lessonStatus['projects_avg_score'] = $projectsAvgScore; //the projects average score
                    $lessonStatus['tests_progress'] = $testsProgress; //the tests percentage done
                    $lessonStatus['tests_avg_score'] = $testsAvgScore; //the tests average score
                    $lessonStatus['content_progress'] = $contentProgress; //the content (theory_examples) percentage done
                    $lessonStatus['overall_progress'] = $overallProgress; //the total percentage done, including content and tests
                    $lessonStatus['lesson_passed'] = $lessonPassed;
                    $lessonStatus['total_conditions'] = sizeof($lessonConditions);
                    $lessonStatus['conditions_passed']= array_sum($conditionsMet);
                    $lessonStatus['completed'] = $value['completed'];
                    $lessonStatus['score'] = $value['score'];
                    $lessonStatus['comments'] = $value['comments'] ? $value['comments'] : 0;
                }
            }

/*
        if ($storeCache) {
        	//Cache::setCache($cacheKey, serialize($lessonStatus));
        }
*/
        return $lessonStatus;
    }


    /**
     * Check if student meets conditions
     *
     * This function is used to check user progress against lesson condtions.
     * Every condition is checked and an array is returned, each with the condition
     * id and whether it is met or not.
     * <br/>Example:
     * <code>
     * $seenUnits = EfrontStats::getStudentsSeenContent(3, 'jdoe');     //Calculate seen content for user jdoe in lesson with id 3
     * $conditions = $currentLesson -> getConditions();                 //Get conditions for current lesson
     * $conditionsMet = self :: checkConditions(seenUnits, $conditions, $visitableContentIds, $visitableTestIds);  //visitableContentIds is a list of units that are "visitable", that is they are active and non-empty. $visitableTestIds is the test units
     * </code>
     *
     * @param array $seenUnits The units that the user has seen
     * @param array $conditions The conditions to check against
     * @param array $visitableContentIds The visitable content ids that make up the lesson
     * @return array An array with condition ids and true/false values depending on whether they are met
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function checkConditions($seenUnits, $conditions, $visitableContentIds, $visitableTestIds, $userTimesInLessonContent) {
        !$seenUnits ? $seenUnits = array() : null;
        $notSeenUnits = array_diff_key($visitableContentIds, $seenUnits); //The units that the user has yet to see
        $conditionsMet = array();
        $conditionsRelation = array();
        foreach ($conditions as $conditionId => $condition) {
            switch ($condition['type']) {
                case 'all_units':
                    sizeof($notSeenUnits) == 0 && sizeof($seenUnits) > 0 ? $passed = 1 : $passed = 0;
                    break;
                case 'percentage_units':
                    $percentageSeen = round(100 * (sizeof($visitableContentIds) - sizeof($notSeenUnits)) / sizeof($visitableContentIds));
                    $percentageSeen >= $condition['options'][0] ? $passed = 1 : $passed = 0;
                    break;
                case 'specific_unit':
                    in_array($condition['options'][0], array_keys($seenUnits)) || !in_array($condition['options'][0], $visitableContentIds) ? $passed = 1 : $passed = 0;
                    break;
                case 'all_tests':
                    $passed = 1;
                    foreach ($visitableTestIds as $id) {
                     if (isset($seenUnits[$id])) {
                         $score = $seenUnits[$id];
                         $score < $condition['options'][0] ? $passed = 0 : null;
                     } else {
                      $passed = 0;
                     }
                    }
                    break;
                case 'mean_all_tests':
                    $meanScore = array_sum($seenUnits) / array_sum(array_count_values($seenUnits)); //array_count_values does not take into account false entries. So, in this expression, the denominator equals the number of tests the user has done
                    $meanScore >= $condition['options'][0] ? $passed = 1 : $passed = 0;
                    break;
                case 'specific_test':
                    in_array($condition['options'][0], array_keys($seenUnits)) || !in_array($condition['options'][0], $visitableTestIds) ? $passed = 1 : $passed = 0;
                    break;
                case 'time_in_lesson':
                 if ($userTimesInLessonContent >= $condition['options'][0]*60) {
                  $passed = 1;
                 } else {
                  $passed = 0;
                 }
                 break;
                default:
                    break;
            }
            $conditionsRelation[] = $condition['relation'];
            $conditionsRelation[] = $passed;
            $conditionsMet[$conditionId] = $passed;
        }
        if (sizeof($conditionsRelation) > 0) {
            $conditionsRelation[0] == 'or' ? $initial = 0 : $initial = 1; //Since the first condition is either 'or' or 'and', an initial value must be considered. If the first condition is "or", then the initial value is 0. Otherwise, it's 1 (since 0 OR x = x, 1 AND x = x)
            $passed = eval('return '.$initial.' '.implode(" ", $conditionsRelation).';');
        } else {
            $passed = 0;
        }
        return array($conditionsMet, $passed);
    }

    /**
     * Get user communication info
     *
     * This returns cimmmunication info for a user
     * <br/>Example:
     * <code>
     * $info = EfrontStats :: getUserCommunicationInfo('jdoe');                   //Get information for user jdoe
     * </code>
     * @param mixed $user Either a user login or a EfrontUser object
     * @return array the users' basic info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getUserCommunicationInfo($user) {
        if (! ($user instanceof EfrontUser)) {
            $user = EfrontUserFactory :: factory($user);
        }
        $info = array();

        $forum_info = eF_getTableData("f_messages", "*", "users_LOGIN='".$user -> login."'", "timestamp desc");
        $forum_messages = array();
        foreach ($forum_info as $message) {
            $forum_messages[$message['id']] = $message;
        }

        $personal_messages_info = eF_getTableData("f_personal_messages", "*", "users_LOGIN='".$user -> login."'", "timestamp desc");
        $personal_messages = array();
        foreach ($personal_messages_info as $message) {
            $personal_messages[$message['id']] = $message;
        }

        $personal_folders_info = eF_getTableData("f_folders", "*", "users_LOGIN='".$user -> login."'");
        $personal_folders = array();
        foreach ($personal_folders_info as $folder) {
            $personal_folders[$folder['id']] = $folder;
        }

        $file_info = eF_getTableData("files", "*", "users_LOGIN='".$user -> login."'");
        $files = array();
        $size = 0;
        foreach ($file_info as $file) {
            $size += filesize($file['file']);
        }

        $chat_messages_info = eF_getTableData("chatmessages", "*", "users_LOGIN='".$user -> login."'", "timestamp desc");
        $chat_messages = array();
        foreach ($chat_messages_info as $message) {
            $chat_messages[$message['id']] = $message;
        }

        $comments_info = eF_getTableData("comments", "*", "users_LOGIN='".$user -> login."'", "timestamp desc");
        $comments = array();
        foreach ($comments_info as $comment) {
            $comments[$comment['id']] = $comment;
        }

        $info['forum_messages'] = $forum_messages;
        if (sizeof($info['forum_messages']) > 0) {
            $info['last_message'] = current($info['forum_messages']);
        }
        $info['personal_messages'] = $personal_messages;
        $info['personal_folders'] = $personal_folders;
        $info['files'] = $files;
        $info['total_size'] = $size;

        if ($GLOBALS['configuration']['chat_enabled']) {
         $info['chat_messages'] = $chat_messages;
         if (sizeof($info['chat_messages']) > 0) {
             $info['last_chat'] = current($info['chat_messages']);
         }
        }
        $info['comments'] = $comments;

        return $info;
    }


    /**
     * Get user usage info
     *
     * This returns usage info for a user
     * <br/>Example:
     * <code>
     * $info = EfrontStats :: getUserUsageInfo('jdoe');                   //Get usage information for user jdoe
     * </code>
     * @param mixed $user Either a user login or a EfrontUser object
     * @return array the users' basic info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getUserUsageInfo($user) {
        if (! ($user instanceof EfrontUser)) {
            $user = EfrontUserFactory :: factory($user);
        }
        $info = array();
        $login_info = eF_getTableData("logs", "*", "users_LOGIN='".$user -> login."' and action = 'login'", "timestamp desc");
  $info['last_ip'] = eF_decodeIP($login_info[0]['session_ip']);
        $logins = array();
        foreach ($login_info as $login) {
            $logins[$login['id']] = $login;
        }

        $month_login_info = eF_getTableData("logs", "*", "users_LOGIN='".$user -> login."' and action = 'login' and timestamp > ".(time() - 2592000)."");
        $month_logins = array();
        foreach ($month_login_info as $login) {
            $month_logins[$login['id']] = $login;
        }

        $week_login_info = eF_getTableData("logs", "*", "users_LOGIN='".$user -> login."' and action = 'login' and timestamp > ".(time() - 604800)."");
        $week_logins = array();
        foreach ($week_login_info as $login) {
            $week_logins[$login['id']] = $login;
        }


        $timeReport = new EfrontTimes();
        $mean_duration = round($timeReport -> getUserMeanSessionTime($user -> login)/60);
        $timeReport = new EfrontTimes(array(time() - 2592000, time()));
        $month_mean_duration = round($timeReport -> getUserMeanSessionTime($user -> login)/60);
        $timeReport = new EfrontTimes(array(time() - 604800, time()));
        $week_mean_duration = round($timeReport -> getUserMeanSessionTime($user -> login)/60);
/*
        $temp = self :: getUserTimes($user -> login);
        sizeof($temp['duration']) > 0 ? $mean_duration = ceil((array_sum($temp['duration']) / sizeof($temp['duration'])) / 60) : $mean_duration = 0;
        $temp = self :: getUserTimes($user -> login, array('from' => time() - 2592000, 'to' => time()));
        sizeof($temp['duration']) > 0 ? $month_mean_duration = ceil((array_sum($temp['duration']) / sizeof($temp['duration']) / 60)) : $month_mean_duration = 0;
        $temp = self :: getUserTimes($user -> login, array('from' => time() - 604800, 'to' => time()));
        sizeof($temp['duration']) > 0 ? $week_mean_duration = ceil((array_sum($temp['duration']) / sizeof($temp['duration']) / 60)) : $week_mean_duration = 0;
*/

        $info['logins'] = $logins;
        if (sizeof($info['logins']) > 0) {
            $info['last_login'] = current($info['logins']);
        }
        $info['month_logins'] = $month_logins;
        $info['week_logins'] = $week_logins;
        $info['mean_duration'] = $mean_duration;
        $info['month_mean_duration'] = $month_mean_duration;
        $info['week_mean_duration'] = $week_mean_duration;

        return $info;
    }


    /**
     * Get statistic information about tests
     *
     * This returns statistic info for a test
     * <br/>Example:
     * <code>
     * $tests = array(2, 4);
     * $info = EfrontStats :: getTestInfo($tests);                   //Get information for tests 2,4
     * </code>
     * @param mixed $tests Either an array of tests id or false (request information for all existing tests)
     * @param mixed $categories denotes in how many categories will the scores from 0-100% be divided (if not false)
     * @param mixed $show_all: denotes whether the function should return the stats for all the times a user took a test (default=no: just return for the active test)
     * @return array the tests' statistinc info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getTestInfo($tests = false, $categories = false, $show_all = false, $lesson = false) {
        if ($tests == false) {
            $tests = eF_getTableDataFlat("tests, content", "tests.id", "tests.content_ID=content.id and content.ctg_type = 'tests' and tests.lessons_ID != 0"); //This way we get tests that have a corresponding unit
            $tests = $tests['id'];
        } elseif (!is_array($tests)) {
            $tests = array($tests);
        }

        $lessonNames = eF_getTableDataFlat("lessons", "id,name");
        sizeof($lessonNames) > 0 ? $lessonNames = array_combine($lessonNames['id'], $lessonNames['name']) : $lessonNames = array();

        if ($lesson) {
         $lessonUsers = eF_getTableDataFlat("users_to_lessons ul, users u", "ul.users_LOGIN", "u.login=ul.users_LOGIN and u.archive=0 and ul.lessons_ID=$lesson and ul.archive=0");
         $users = array_combine($lessonUsers['users_LOGIN'], $lessonUsers['users_LOGIN']);
        } else {
         $result = eF_getTableData("users", "name, surname, login");
         $users = array();
         foreach ($result as $user) {
          $users[$user['login']] = $user;
         }

        }

        if ($users) {
         if (sizeof($tests) == 1) {
          $doneTests = EfrontStats :: getDoneTestsPerTest(array_keys($users), current($tests), false, false, $lesson);
         } else {
          $doneTests = EfrontStats :: getDoneTestsPerTest(array_keys($users), false, false, false, $lesson);
         }
        }

        foreach ($tests as $id) {
            $testInfo = array();
            $test = new EfrontTest($id);
            //$unit      = $test -> getUnit();                     
            $testInfo['general']['id'] = $id;
            //$testInfo['general']['name']            = $unit -> offsetGet('name');
            //$testInfo['general']['content_ID']      = $unit -> offsetGet('id');      
            $testInfo['general']['name'] = $test -> test['name'];
            $testInfo['general']['content_ID'] = $test -> test['content_ID'];
            $testInfo['general']['lesson_name'] = $lessonNames[$test-> test['lessons_ID']];
            $testInfo['general']['duration'] = $test -> options['duration'];
            $testInfo['general']['duration_str'] = eF_convertIntervalToTime($test -> options['duration']);
            $testInfo['general']['redoable'] = $test -> options['redoable'];
            $testInfo['general']['redoable_str'] = $test -> options['redoable'] >= 1 ? _YES : _NO;
            $testInfo['general']['onebyone'] = $test -> options['onebyone'];
            $testInfo['general']['onebyone_str'] = $test -> options['onebyone'] == 1 ? _YES : _NO;
            $testInfo['general']['answers'] = $test -> options['answers'];
            $testInfo['general']['answers_str'] = $test -> options['answers'] == 1 ? _YES : _NO;
            $testInfo['general']['description'] = $test -> test['description'];
            //$testInfo['general']['timestamp']       = $unit -> offsetGet('timestamp');
            //$testInfo['general']['timestamp_str']   = strftime('%d-%m-%Y, %H:%M:%S', $testInfo['general']['timestamp']);
            $testInfo['general']['scorm'] = 0;

            $testInfo['questions']['total'] = 0;
            $testInfo['questions']['raw_text'] = 0;
            $testInfo['questions']['multiple_one'] = 0;
            $testInfo['questions']['multiple_many'] = 0;
            $testInfo['questions']['true_false'] = 0;
            $testInfo['questions']['match'] = 0;
            $testInfo['questions']['empty_spaces'] = 0;
            $testInfo['questions']['drag_drop'] = 0;
            $testInfo['questions']['low'] = 0;
            $testInfo['questions']['medium'] = 0;
            $testInfo['questions']['high'] = 0;

            $questions = $test -> getQuestions(true);
            foreach ($questions as $question) {
                $testInfo['questions']['total']++;
                $testInfo['questions'][$question -> question['type']]++;
                $testInfo['questions'][$question -> question['difficulty']]++;
            }

            //@todo: Compatibility status with old versions, need to change
            $testInfo['done'] = array();

            // Create results score categories
            if ($categories) {
             $testInfo['score_categories'] = array();
             $step = 100 / $categories;

             for ($i = 0; $i < $categories; $i++) {
     $testInfo['score_categories'][$i] = array("from" => $i * $step, "to" => ($i+1) * $step, "count" => 0);
     if ($i == ($categories - 1)) {
      $testInfo['score_categories'][$i]["to"] = 100;
     }
             }
            }
            foreach ($doneTests[$id] as $user => $done) {

                foreach ($done as $key => $dt) {
                 // Check that this $dt refers to a test occurence - and not average scores etc
                 if (eF_checkParameter($key,"id") && ($show_all || $dt['archive'] == 0) && $dt['status'] != 'incomplete' && $dt['status'] != '') {
                        $done_test = array('id' => $done['active_score'],
                                           'users_LOGIN' => $dt['users_LOGIN'],
                                           'name' => $users[$dt['users_LOGIN']]['name'],
                                           'surname' => $users[$dt['users_LOGIN']]['surname'],
                               'score' => $dt['score'],
                               'active_score' => $done['active_score'],
                               'active_test_id'=> $done['active_test_id'],
                                           'timestamp' => $dt['time_end'],
                                           'mastery_score' => $dt['mastery_score'],
                                           'status' => $dt['status']);
                        $testInfo['done'][] = $done_test;

                        // Get the user's score in the correct stats category
                        if ($categories) {
                         $stat_cat = $dt['score'] / $step;
         $testInfo['score_categories'][($stat_cat >= $categories)?($categories-1):$stat_cat]["count"]++;
                        }
                    }
                }
            }

            // Create results score categories
            if ($categories) {
             $doneTestsCount = sizeof($testInfo['done']);
             $sum_count = $doneTestsCount; // counts how many users have score equal or above each score_category

             if ($sum_count > 0) {
              foreach ($testInfo['score_categories'] as $key => $score) {
              $testInfo['score_categories'][$key]['percent'] = round(100 * ($testInfo['score_categories'][$key]['count'] / $doneTestsCount), 2);
              $testInfo['score_categories'][$key]['sum_count'] = $sum_count;
              $testInfo['score_categories'][$key]['sum_count_percent'] = round(100 * ($sum_count / $doneTestsCount), 2);
              $sum_count -= $testInfo['score_categories'][$key]['count'];
              }
             }
            }
            $testsInfo[$id] = $testInfo;

        }

        return $testsInfo;
    }


    /**
     * Get statistic information about scorm tests
     *
     * This returns statistic info for a scorm test
     * <br/>Example:
     * <code>
     * $tests = array(2, 4);
     * $info = EfrontStats :: getScomTestInfo($tests);                   //Get information for tests 2,4
     * </code>
     * @param mixed $tests Either an array of tests id or false (request information for all existing tests)
     * @return array the tests' statistic info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getScormTestInfo($tests = false, $categories = false, $show_all = false) {

        $tests_info = array();
        if ($tests === false) {
            $tests = eF_getTableDataFlat("content","id", "ctg_type='scorm_test'");
   $tests = $tests['id'];
        } else if (!is_array($tests)) {
            $tests = array($tests);
        }

        $lessonNames = eF_getTableDataFlat("lessons", "id,name");
        sizeof($lessonNames) > 0 ? $lessonNames = array_combine($lessonNames['id'], $lessonNames['name']) : $lessonNames = array();
        $result = eF_getTableData("users", "name, surname, login");
        $users = array();
        foreach ($result as $user) {
            $users[$user['login']] = $user;
        }

        foreach ($tests as $id) {
            $testInfo = array();
            $unit = new EfrontUnit($id);

            $result = eF_getTableData("scorm_data", "*", "content_ID=$id and (users_LOGIN is null or users_LOGIN='')");

            $testInfo['general']['content_ID'] = $id;
            $testInfo['general']['id'] = $id;
            $testInfo['general']['name'] = $unit -> offsetGet('name');
            $testInfo['general']['scorm'] = 1;
            $testInfo['general']['lesson_name'] = $lessonNames[$unit -> offsetGet('lessons_ID')];
            if ($result[0]['maxtimeallowed']) {
                $time_parts = explode(":", $result[0]['maxtimeallowed']);
                $testInfo['general']['duration'] = $time_parts[0]*3600+$time_parts[1]*60+$time_parts[2];
                $testInfo['general']['duration_str']= eF_convertIntervalToTime($testInfo['general']['duration']);
            } else {
                $testInfo['general']['duration'] = '';
                $testInfo['general']['duration_str']= eF_convertIntervalToTime($testInfo['general']['duration']);
            }

            // Create results score categories
            if ($categories) {
             $testInfo['score_categories'] = array();
             $step = 100 / $categories;

             for ($i = 0; $i < $categories; $i++) {
     $testInfo['score_categories'][$i] = array("from" => $i * $step, "to" => ($i+1) * $step, "count" => 0);
     if ($i == ($categories - 1)) {
      $testInfo['score_categories'][$i]["to"] = 100;
     }
             }
            }

            $testInfo['done'] = array();
            $done_info = eF_getTableData("scorm_data d, users u", "d.users_LOGIN, u.name, u.surname, d.score, d.timestamp, d.lesson_status, d.masteryscore, d.minscore, d.maxscore","d.lesson_status != 'incomplete' and d.users_LOGIN = u.LOGIN and d.content_ID = $id");

            foreach ($done_info as $done) {

                $done_test = array();
                $done_test['users_LOGIN'] = $done['users_LOGIN'];
                $done_test['name'] = $done['name'];
                $done_test['surname'] = $done['surname'];
                $done_test['timestamp'] = $done['timestamp'];
                $done_test['status'] = $done['lesson_status'];
                $done_test['mastery_score'] = $done['masteryscore'];

                if (is_numeric($done['minscore']) && is_numeric($done['maxscore'])) {
                    $done_test['score'] = 100 * $done['score'] / ($done['minscore'] + $done['maxscore']);
                } else {
                    $done_test['score'] = $done['score'];
                }
                //$done_test['score']         = $done['score'];

                $testInfo['done'][] = $done_test;

                if ($categories) {
                    $stat_cat = $done_test['score'] / $step;
                    $testInfo['score_categories'][($stat_cat >= $categories)?($categories-1):$stat_cat]["count"]++;
                }

            }


            if ($categories) {
             $doneTestsCount = sizeof($testInfo['done']);
             $sum_count = $doneTestsCount; // counts how many users have score equal or above each score_category

             if ($sum_count > 0) {
              foreach ($testInfo['score_categories'] as $key => $score) {
              $testInfo['score_categories'][$key]['percent'] = round(100 * ($testInfo['score_categories'][$key]['count'] / $doneTestsCount), 2);
              $testInfo['score_categories'][$key]['sum_count'] = $sum_count;
              $testInfo['score_categories'][$key]['sum_count_percent'] = round(100 * ($sum_count / $doneTestsCount), 2);
              $sum_count -= $testInfo['score_categories'][$key]['count'];
              }
             }
            }
            $tests_info[$id] = $testInfo;
        }

        return $tests_info;
    }

    /**
     * Get statistic information about questions
     *
     * This returns statistic info for a set of questions
     * <br/>Example:
     * <code>
     * $questions = array(2, 4);
     * $info = EfrontStats :: getQuestionInfo($questions);                   //Get information for questions 2,4
     * </code>
     * @param mixed $questions Either an array of question id or false (request information for all existing questions)
     * @return array the questions' statistic info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getQuestionInfo($questions = false, $lesson = false) {
        $questions_info = array();
        if ($questions == false) {
            $questions = eF_getTableData("questions", "id");
        }
        foreach ($questions as $question_id) {
            $question_info = array();
            $question = QuestionFactory :: factory($question_id);
            $question_info['general']['id'] = $question_id;
            $question_info['general']['text'] = $question -> question['text'];
            $question_info['general']['reduced_text']= $question -> question['plain_text'];
            $question_info['general']['type'] = $question -> question['type'];
            $question_info['general']['difficulty'] = $question -> question['difficulty'];
            $question_info['general']['content_ID'] = $question -> question['content_ID'];
            $question_info['general']['explanation'] = $question -> question['explanation'];
            $question_info['general']['options'] = $question -> question['options'];
            $question_info['general']['answer'] = $question -> question['answer'];
            $question_info['general']['timestamp'] = $question -> question['timestamp'];
            $question_info['done']['times_done'] = 0;
            $question_info['done']['avg_score'] = 0;

            $questions_info[$question_id] = $question_info;

            $questionIds[$question_id] = $question_id;
        }

        if ($lesson) {
         $sql = ' and lessons_ID='.$lesson;
        }
        $completedTests = EfrontCompletedTest::retrieveCompletedTest("tests, completed_tests", "*", "tests.id=completed_tests.tests_ID and status != 'deleted' $sql");
        foreach ($completedTests as $test) {
            $test['test'] = unserialize($test['test']);
            $testQuestions = $test['test'] -> questions;
            $temp = array_intersect(array_keys($testQuestions), $questionIds);
            if (sizeof($temp) > 0) {
                foreach ($temp as $id) {
                    $questions_info[$id]['done']['avg_score'] += $testQuestions[$id] -> score;
                    $questions_info[$id]['done']['times_done']++;
                }
            }

        }

        foreach ($questions as $id) {
            $questions_info[$id]['done']['avg_score'] = $questions_info[$id]['done']['avg_score'] / $questions_info[$id]['done']['times_done'];
        }

        return $questions_info;
    }


    /**
     * Get statistic information about projects
     *
     * This returns statistic info for a set of projects
     * <br/>Example:
     * <code>
     * $projects = array(2, 4);
     * $info = EfrontStats :: getProjectInfo($questions);                   //Get information for projects 2,4
     * </code>
     * @param mixed $projects Either an array of project ids or false (request information for all existing projects)
     * @return array the project' statistic info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getProjectInfo($projects = false) {
        $projects_info = array();
        if ($projects == false) {
            $projects = eF_getTableData("projects","id");
        }

        foreach ($projects as $project_id) {
            $project_info = array();
            $project = new EfrontProject($project_id);
            $project_info['general']['id'] = $project_id;
            $project_info['general']['title'] = $project -> project['title'];
            $project_info['general']['data'] = $project -> project['data'];
            $project_info['general']['deadline'] = $project -> project['deadline'];
            $project_info['general']['auto_assign'] = $project -> project['auto_assign'];

            $project_info['done'] = array();
            $assigned_data = eF_getTableData("users u, users_to_projects up, projects p, users_to_lessons ul", "u.LOGIN, u.name, u.surname, up.grade, up.upload_timestamp, up.status, up.comments", "p.id=up.projects_ID and ul.lessons_ID=p.lessons_ID and ul.users_LOGIN=u.login and ul.archive=0 and u.archive=0 and u.LOGIN = up.users_LOGIN and up.projects_ID=".$project_id);
            foreach ($assigned_data as $data) {
                $done_project = array();
                $done_project['users_LOGIN'] = $data['LOGIN'];
                $done_project['name'] = $data['name'];
                $done_project['surname'] = $data['surname'];
                $done_project['grade'] = $data['grade'] ? $data['grade'] : 0;
                $done_project['upload_timestamp'] = $data['upload_timestamp'];
                $done_project['status'] = $data['status'];
                $done_project['comments'] = $data['comments'];
                $project_info['done'][] = $done_project;
            }
            $projects_info[$project_id] = $project_info;
        }
        return $projects_info;
    }


    /**
     * Derive statistics for a single test
     *
     * This function is used to calculate statistics on users completion of a single
     * test.
     * <br/>Example:
     * <code>
     * $information = EfrontStats :: doneTestInfo(32);										//Get information for test with id 32
     *
     * $results = eF_getTableData("completed_tests", "*");									//Alternatively, you may pre-collect the required data from the database...
     * foreach ($results as $value) {
     *     $doneTests[$value['tests_ID']][$value['id']] = $value;
     * }
     * $information = EfrontStats :: doneTestInfo($doneTests[32]);							//...And give them ready to the database, thus eliminating the need for queries
     * </code>
     * The returned data are structured as follows:
     * <code>
     * Array
     * (
     *     [users] => Array
     *         (
     *             [jdoe] => Array
     *                 (
     *                     [score] => Array
     *                         (
     *                             [3] => 0
     *                             [25] => 40
     *                         )
     *                     [result] => Array
     *                         (
     *                             [3] => completed
     *                             [25] => completed
     *                         )
     *                     [timestamp] => Array
     *                         (
     *                             [3] => 1222342766
     *                             [25] => 1222435116
     *                         )
     *                     [timesDone] => 2
     *                     [meanScore] => 20
     *                 )
     *             [djoe] => Array
     *                 (
     *                     [score] => Array
     *                         (
     *                             [4] => 0
     *                             [11] => 20
     *                             [16] => 57.33
     *                         )
     *                     [result] => Array
     *                         (
     *                             [4] => completed
     *                             [11] => completed
     *                             [16] => completed
     *                         )
     *                     [timestamp] => Array
     *                         (
     *                             [4] => 1222343353
     *                             [11] => 1222348221
     *                             [16] => 1222349886
     *                             [17] => 1222350800
     *                             [18] => 1222350975
     *                         )
     *                     [timesDone] => 10
     *                     [meanScore] => 18.37
     *                 )
     *         )
     *     [stats] => Array
     *         (
     *             [timesDone] => 12
     *             [meanScore] => 18.64
     *             [meanMeanScore] => 19.19
     *             [lastTimesMeanScore] => 27.5
     *         )
     * )
     * </code>
     *
     * @param mixed $testInfo Either an array with keys the completed test instances ids and values the corresponding values, or a test id
     * @return array The structured statistics information
     * @since 3.5.2
     * @access public
     */
    public static function doneTestInfo($testInfo) {
        if (is_array($testInfo)) {
            $doneTests = $testInfo;
        } elseif (eF_checkParameter($testInfo, 'id')) {
            $results = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "*", "status != 'deleted' and tests_ID=$testInfo");
            foreach ($results as $value) {
                $value['test'] = unserialize($value['test']);
                $doneTests[$value['id']] = $value;
            }
        } else {
            throw new EfrontStatsException(_INVALIDPARAMETER.': '.$testInfo, EfrontStatsException :: INVALID_PARAMETER);
        }

        //Initialize statistics array
        $stats = array('timesDone' => 0, 'meanScore' => 0, 'meanMeanScore' => 0, 'lastTimesMeanScore' => 0);

        //Calculate statistics per user
        foreach ($doneTests as $doneTestId => $doneTest) {
            if (!($doneTest['test'] instanceof EfrontCompletedTest)) { //Unserialize test parameter, only if needed (otherwise it is already unserialized)
                $doneTest['test'] = unserialize($doneTest['test']);
                $doneTests[$doneTestId]['test'] = $doneTest['test'];
            }

            $user = $doneTest['users_LOGIN'];

            $testUsers[$user]['score'][$doneTestId] = $doneTest['test']['score'];
            $testUsers[$user]['result'][$doneTestId] = $doneTest['test']['status'];
            $testUsers[$user]['timestamp'][$doneTestId] = $doneTest['test']['time_end'];
            $stats['timesDone']++;
            $stats['meanScore'] += $doneTest['test']['score'];
        }

        //Caclulate accumulative statistics per user
        foreach ($testUsers as $user => $value) {
            $testUsers[$user]['timesDone'] = sizeof($value['score']);
            $testUsers[$user]['meanScore'] = round(array_sum($value['score']) / sizeof($value['score']), 2);
            $stats['meanMeanScore'] += $testUsers[$user]['meanScore'];
            $stats['lastTimesMeanScore'] += end($value['score']);
        }

        //Calulate accumulative statistics for the test
        $stats['meanScore'] = round($stats['meanScore'] / $stats['timesDone'], 2); //This is the mean score of all test executions
        $stats['meanMeanScore'] = round($stats['meanMeanScore'] / sizeof($testUsers), 2); //This is the mean score of mean scores
        $stats['lastTimesMeanScore'] = round($stats['lastTimesMeanScore'] / sizeof($testUsers), 2); //This is the mean score of the last execution of each test

        return array('users' => $testUsers, 'stats' => $stats);
    }

 /**
	 * Get questions statistics for test
	 *
	 * This function analyzes the user's performance in a test's questions.
	 * <br/>Example:
	 * <code>
	 * $result = eF_getTableData("completed_tests", "*", "id=32");
	 * $completedTest = unserialize($result['test']);
	 * $stats = EfrontStats :: getQuestionsUnitStatistics($completedTest -> questions);
	 * </code>
	 * The $stats array above is structured like the example below:
	 * <code>
	 * Array
	 * (
	 *     [questionStats] => Array
	 *         (
	 *             [partialSuccess] => 1
	 *             [success] => 2
	 *             [failed] => 1
	 *             [pending] => 1
	 *         )
	 *     [unitStats] => Array
	 *         (
	 *             [0] => Array
	 *                 (
	 *                     [content_ID] => 0
	 *                     [partialSuccess] => 1
	 *                     [scores] => Array
	 *                         (
	 *                             [21] => 50
	 *                             [18] => 100
	 *                             [15] => 0
	 *                         )
	 *                     [success] => 1
	 *                     [failed] => 1
	 *                     [pending] => 1
	 *                     [meanScore] => 50
	 *                 )
	 *             [34] => Array
	 *                 (
	 *                     [content_ID] => 34
	 *                     [success] => 1
	 *                     [scores] => Array
	 *                         (
	 *                             [19] => 100
	 *                         )
	 *                     [meanScore] => 100
	 *                 )
	 *         )
	 * )
	 * </code>
	 *
	 * @param array $questions An array of EfrontQuestion objects, taken from a completed test
	 * @return array An array with statistics for the questions
	 * @since 3.5.2
	 * @access public
	 */
 public static function getQuestionsUnitStatistics($questions) {
     $questionsStats = array();
     $unitStats = array();
     foreach ($questions as $id => $question) {
         if (!isset($unitsData[$question -> question['content_ID']])) { //Initialize data array
             $unitsData[$question -> question['content_ID']] = array('pending' => 0, 'success' => 0, 'failed' => 0, 'partialSuccess' => 0);
         }
         $unitsData[$question -> question['content_ID']]['content_ID'] = $question -> question['content_ID'];
         $unitsData[$question -> question['content_ID']]['total']++;
         if ($question -> pending) {
             $questionsStats['pending']++;
             $unitsData[$question -> question['content_ID']]['pending']++;
         } elseif ($question -> score == 100) {
             $questionsStats['success']++;
             $unitsData[$question -> question['content_ID']]['success']++;
         } elseif ($question -> score == 0) {
             $questionsStats['failed']++;
             $unitsData[$question -> question['content_ID']]['failed']++;
         } else {
             $questionsStats['partialSuccess']++;
               $unitsData[$question -> question['content_ID']]['partialSuccess']++;
         }

         if (!$question -> pending) {
             $unitsData[$question -> question['content_ID']]['scores'][$question -> question['id']] = $question -> score;
         }
     }

     foreach ($unitsData as $contentId => $data) {
         $unitsData[$contentId]['meanScore'] = round(array_sum($data['scores']) / sizeof($data['scores']), 2);
     }

     return array('questionStats' => $questionsStats, 'unitStats' => $unitsData);
 }


 /**
	 * Calculate statistics for done questions
	 *
	 * This function calculates statistics for questions, such as min max and average scores, times
	 * it is done etc.
	 * <br>Example:
	 * <code>
	 * $stats = EfrontStats :: getQuestionsStatistics(23);
	 * </code>
	 * The $stats array above is structured like the example below:
	 * <code>
	 * Array => (
	 *   [10] => Array
     *   (
     *       [score] => Array
     *           (
     *               [0] => 100
     *           )
     *       [completed_test] => Array
     *           (
     *               [0] => 2
     *           )
     *       [test] => Array
     *           (
     *               [0] => 4
     *           )
     *       [login] => Array
     *           (
     *               [0] => student
     *           )
	 *            [timestamp] => Array
	 *                (
	 *                    [0] => 1224580026
	 *                )
	 *            [times_done] => 1
	 *            [avg_score] => 100
	 *            [max_score] => 100
	 *            [min_score] => 100
	 *        )
	 *    [11] => Array
	 *        (
	 *            [score] => Array
	 *                (
	 *                    [0] => 100
	 *                )
	 *            [completed_test] => Array
	 *                (
	 *                    [0] => 2
	 *                )
	 *            [test] => Array
	 *                (
	 *                    [0] => 4
	 *                )
	 *            [login] => Array
	 *                (
	 *                    [0] => student
	 *                )
	 *            [timestamp] => Array
	 *                (
	 *                    [0] => 1224580026
	 *                )
	 *            [times_done] => 1
	 *            [avg_score] => 100
	 *            [max_score] => 100
	 *            [min_score] => 100
	 *        )
	 *   )
	 * </code>
	 *
	 * @param mixed $test Either a test id or and EfrontTest object. If it is left blank, statistics are calculated for all tests
	 * @return array $questionStats The questions statistics
	 * @since 3.5.2
	 * @access public
	 */
 public static function getQuestionsStatistics($test = false) {
     if (!$test) {
         $result = EfrontCompletedTest::retrieveCompletedTest("completed_tests ct, users u", "ct.*", "ct.users_LOGIN=u.login and u.archive=0 and ct.status != 'deleted' and ct.status != 'incomplete'");
     } else {
         $test instanceof EfrontTest ? $testId = $test -> test['id'] : $testId = $test;
         if (!eF_checkParameter($testId, 'id')) {
             throw new EfrontTestException(_INVALIDID, EfrontLessonException :: INVALID_ID);
         }
         $result = EfrontCompletedTest::retrieveCompletedTest("completed_tests ct, users u", "ct.*", "ct.users_LOGIN=u.login and u.archive=0 and ct.status != 'deleted'  and ct.status != 'incomplete' and tests_ID = $testId");
     }

     $questionStats = array();

     foreach ($result as $value) {
         $completedTest = unserialize($value['test']);

         foreach ($completedTest -> questions as $id => $question) {
             $questionStats[$id]['score'][] = $question -> score;
             $questionStats[$id]['completed_test'][] = $value['id'];
             $questionStats[$id]['test'][] = $value['tests_ID'];
             $questionStats[$id]['login'][] = $value['users_LOGIN'];
             $questionStats[$id]['timestamp'][] = $completedTest -> time['end'];
             $questionStats[$id]['correct'][] = ($question -> score == 100)?100:0;

          if (!isset($questionStats[$id]['answers_per_option'])) {
            $questionStats[$id]['answers_per_option'] = array();
          }

             if (!is_array($question -> results)) {

              // Single answer MultChoiceQ, True/false
              $selected_option = $question -> userAnswer;

              if ($selected_option !== false) {
               if (!isset($questionStats[$id]['answers_per_option'][$selected_option])) {
                $questionStats[$id]['answers_per_option'][$selected_option] = 100;
               } else {
                $questionStats[$id]['answers_per_option'][$selected_option] += 100;
               }
              }
             } else {
              // Emtpy spaces
              foreach ($question -> results as $selected_option => $result) {
               if (!isset($questionStats[$id]['answers_per_option'][$selected_option])) {
                $questionStats[$id]['answers_per_option'][$selected_option] = ($result)?100:0;
               } else {
                $questionStats[$id]['answers_per_option'][$selected_option] += 100 * ($result)?100:0;
               }
              }

             }
         }
     }

     foreach ($questionStats as $id => $question) {
         $questionStats[$id]['times_done'] = sizeof($question['score']);
         $questionStats[$id]['avg_score'] = array_sum($question['score']) / sizeof($question['score']);
         $questionStats[$id]['max_score'] = max($question['score']) ? max($question['score']) : 0;
         $questionStats[$id]['min_score'] = min($question['score']) ? min($question['score']) : 0;
         $questionStats[$id]['percent_per_option'] = array();
         foreach ($questionStats[$id]['answers_per_option'] as $key => $optionAnswers) {
          $questionStats[$id]['percent_per_option'][$key] = round($optionAnswers / sizeof($question['score']),2);
         }

         $questionStats[$id]['correct_percent'] = round(array_sum($question['correct']) / sizeof($question['score']),2);
     }


     return $questionStats;
 }


 /**
	 * Get user online times
	 *
	 * This function calculates the time a user spent online. If the optional interval parameter is set, then
	 * statistics are calculated only for this time period.
	 * <br/>Example:
	 * <code>
	 * $interval = array('from' => time()-86400, 'to' => time());        //Calculate statistics for the last 24 hours
	 * $times = EfrontStats :: getUserTimes('john', $interval);
	 * print_r($times);
	 * //Returns:
	 *Array
	 *(
	 *    [duration] => Array
	 *        (
	 *            [0] => 19
	 *            [1] => 120
	 *            [2] => 63
	 *        )
	 *
	 *    [times] => Array
	 *        (
	 *           [0] => 1118770769
	 *           [1] => 1118824615
	 *           [2] => 1118824760
	 *        )
	 *)
	 * </code>
	 *
	 * @param string $login The user login name
	 * @param array $interval The time interval to calculate statistics for
	 * @return array The login times and durations (in seconds)
	 * @version 1.0 27/10/2005
	 */
 public static function getUserTimes($login, $interval = false) {
     $times = array('duration' => array(), 'time' => array(), 'session_ip' => array());

     if (isset($interval['from']) && eF_checkParameter($interval['from'], 'timestamp') && isset($interval['to']) && eF_checkParameter($interval['to'], 'timestamp')) {
         $result = eF_getTableDataFlat("logs", "timestamp, action, session_ip", "timestamp > ".$interval['from']." and timestamp < ".$interval['to']." and users_LOGIN='".$login."' and (action='login' or action = 'logout')", "timestamp");
     } else {
         $result = eF_getTableDataFlat("logs", "timestamp, action, session_ip", "users_LOGIN='".$login."' and (action='login' or action = 'logout')", "timestamp");
     }

     if (sizeof($result) > 0) {
         for ($i = 0; $i < sizeof($result['action']) - 1; $i++) { //The algorithm goes like this: We search for the 'login' actions in the log. When one is found, then we search either for the next 'login' or 'logout' action, if there are no other actions, or the last non-login or logout action. This way, we calculate the true time spent inside the system. If we calculated only the logout-login times, then when a user had closed a window without logging out first, the online time would be reported falsely
             if ($result['action'][$i] == 'login') {
                 $count = $i + 1;
                 $end_action = $result['timestamp'][$count];
                 while ($result['action'][$count] != 'logout' && $result['action'][$count] != 'login' && $count < sizeof($result['action'])) {
                     $end_action = $result['timestamp'][$count];
                     $count++;
                 }
                 if ($end_action - $result['timestamp'][$i] <= 3600){ //only take into account intervals less than one hour
                     $times['duration'][] = $end_action - $result['timestamp'][$i];
                     $times['time'][] = $result['timestamp'][$i];
                     $times['session_ip'][] = eF_decodeIP($result['session_ip'][$i]);
                 }
             }
         }
     }

     return $times;
 }



 /**
	 * Get participation statistics
	 *
	 * This function uses the events to calculate the partification for users to parts of lessons for a specific time period
	 * <br/>Example:
	 * <code>
	 * $participation = EfrontStats :: getParticipationStatistics($users, $from, $to);
	 * //Returns:
	 *[professor] => Array
     *   (
     *      [13] => Array   //lessons_ID
     *           (
     *               [100] => Array   //event type
     *                   (
     *                       [users_LOGIN] => professor
     *                       [lessons_name] => physics
     *                       [lessons_ID] => 13
     *                       [type] => 100
     *                       [count] => 1
     *                   )
	 *
     *           )
	 *	)
	 * </code>
	 *
	 * @param array $users Users return reports for
	 * @param timestamp $from Starting timestamp
	 * @param timestamp $to Ending timestamp
	 * @param array $interval The time interval to calculate statistics for
	 * @return array as described above
	 * @version 1.0 10/12/2009
	 */

 public static function getParticipationStatistics($users, $from, $to) {
  $logins = array_keys($users);
  $eventTypes = array(27,38,30,31,75,77,100,101,103);
  $logins_string = "'".implode("','", $logins)."'";
  $event_types_string = implode(",", $eventTypes);
  $result = eF_getTableData("events", "users_LOGIN,lessons_name,lessons_ID,type,count(*) as count", "timestamp between $from and $to and type IN (".$event_types_string.") group by users_LOGIN,lessons_ID,type");
  $participation = array();
  foreach ($result as $key => $value) {
   $participation[$value['users_LOGIN']][$value['lessons_ID']][$value['type']] = $value;
  }

     return $participation;
 }



 public static function saveAdvancedUserReports($report) {
  $report['rules']['conditions'] = array_values($report['rules']['conditions']); //reindex array
  $report['rules']['columns'] = array_values($report['rules']['columns']); //reindex array
  eF_updateTableData("advanced_user_reports", array('rules' => serialize($report['rules'])), "id=".$report['id']);
 }



 public static function getQuestionResponseDetails($testStats) {

  $userQuestions = array();
  foreach ($testStats as $value) {
   foreach ($value as $user => $testAttempts) {
    foreach ($testAttempts as $testAttempt) {
     if (is_array($testAttempt) && isset($testAttempt['id'])) {
      $result = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "*", "id=".$testAttempt['id']);

      $test = unserialize($result[0]['test']);
      foreach ($test -> questions as $question) {
       $answers = array();
       if (($question instanceOf MultipleManyQuestion)) {
        foreach ($question -> order as $index) {
         if ($question -> userAnswer[$index]) {
          $answers[] = $question -> options[$index];
         }
        }
       } elseif (($question instanceOf MatchQuestion) || ($question instanceOf DragDropQuestion)) {
        foreach ($question -> order as $index) {
         $answers[] = $question -> options[$index].'&nbsp;&rarr;&nbsp;'.$question -> answer[$question -> userAnswer[$index]];
        }
       } elseif ($question instanceOf TrueFalseQuestion) {
        if ($question -> userAnswer === false) {
         $answers[] = _NORESPONSE;
        } else {
         $answers[] = $question -> userAnswer ? _TRUE : _FALSE ;
        }
       } elseif ($question instanceOf EmptySpacesQuestion) {
        $occurences = preg_match_all("/###(\d*)/", $question -> question['plain_text'], $matches);
        for ($i = 0; $i < $occurences; $i++) {
         $question -> question['plain_text'] = preg_replace("/###(\d*)/", "<b>".$question -> userAnswer[$i]."</b>", $question -> question['plain_text'], 1);
        }
        $answers[] = $question -> question['plain_text'];
       } elseif (($question instanceOf MultipleOneQuestion)) {
        foreach ($question -> order as $index) {
         if ($question -> userAnswer == $index) {
          if ($question -> userAnswer === false) {
           $answers[] = _NORESPONSE;
          } else {
           $answers[] = $question -> options[$index];
          }
         }
        }
       } elseif (($question instanceOf RawTextQuestion)) {
        $answers[] = $question -> userAnswer;
       }
       $userQuestions[$question -> question['id']][$user][] = implode("<br/>", $answers);
      }
     }
    }
   }
  }

  return $userQuestions;
 }

}
