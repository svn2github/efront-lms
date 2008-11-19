<?php

/**
 * Statistics exceptions
 *
 */
class EfrontStatsException extends Exception 
{
	const INVALID_ID          = 1001;
	const INVALID_PARAMETER   = 1002;

}


/**
 * This class is used to handle statistics
 *
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
     *      	        [1416] => 0.5
     *  	            [1412] =>
     *	                [1411] =>
     *              	[1413] =>
     *          	    [1420] => 1
     *      	    )
     *  	    [george] => Array
     *	           (
     *              	[1415] =>
     *              	[1417] =>
     *          	    [1416] => 0.66
     *      	        [1412] =>
     *  	            [1408] =>
     *	                [1409] =>
     *              	[1410] =>
     *          	    [1420] => 0.3
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
    public static function getStudentsSeenContent($lessons = false, $users = false) {

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
            !is_array($users) ? $users = array($users) : null;                //Convert single login to array
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
        
        $doneTests = array();
        //$result    = eF_getTableData("done_tests as dt,tests as t, content as c", "c.lessons_ID, c.id, dt.users_LOGIN, dt.score", "t.content_ID = c.id AND t.id=dt.tests_ID");
        $result    = eF_getTableData("completed_tests ct, tests t, content c", "ct.test, ct.users_LOGIN, c.lessons_ID, c.id", "ct.archive = 0 and t.content_ID = c.id AND t.id=ct.tests_ID");

        foreach ($result as $value) {
            $value['test'] = unserialize($value['test']);
            $doneTests[$value['lessons_ID']][$value['users_LOGIN']][$value['id']] = $value['test'] -> completedTest['score'];
        }

        $result           = eF_getTableData("users u, users_to_lessons ul", "u.login, ul.lessons_ID, ul.done_content", "ul.user_type = 'student' and u.login = ul.users_LOGIN");        
        $usersDoneContent = array();
        foreach ($result as $value) {
            $usersDoneContent[$value['lessons_ID']][$value['login']] = unserialize($value['done_content']);
        }
        
        //Get lessons content, in case a done unit is not part of a lesson anymore or is inactive
        $result        = eF_getTableData("content c", "id, lessons_ID", "active=1");
        $lessonContent = array();
        foreach ($result as $value) {
            $lessonContent[$value['lessons_ID']][] = $value['id'];
        }
        
        $resultScorm = eF_getTableData("scorm_data, content", "content.ctg_type, content.lessons_ID, content_ID, users_LOGIN, lesson_status, score, minscore, maxscore, masteryscore", "content.id=scorm_data.content_ID and users_LOGIN != ''");
        foreach ($resultScorm as $key => $value) {
            if ($value['lesson_status'] == 'passed' || $value['lesson_status'] == 'completed') {
                if ($value['ctg_type'] == 'scorm') {
                    $scormDoneContent[$value['lessons_ID']][$value['users_LOGIN']][$value['content_ID']] = '';
                } elseif ($value['ctg_type'] == 'scorm_test') {
                    if (is_numeric($value['minscore']) || is_numeric($value['maxscore'])) {
                        $value['score'] = $value['score'] / ($value['minscore'] + $value['maxscore']);
                    } else {
                        $value['score'] = $value['score'] / 100;
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
        
        foreach ($lessons as $lessonId) {
            
            foreach ($usersDoneContent[$lessonId] as $key => $value) {        //Unserialize and preprocess values. This way, only the array keys contain the content id, while array values contain test scores (when the unit is a test). This way we may use array_sum to calculate the mean score at once)
                //if ($value) {
                    foreach ($usersDoneContent[$lessonId][$key] as $k => $id) {
                        if (!in_array($id, $lessonContent[$lessonId])) {
                            unset($usersDoneContent[$lessonId][$key][$k]);
                        } else {
                            $usersDoneContent[$lessonId][$key][$k] = '';
                        }
                    }
                    if (isset($doneTests[$lessonId][$key])) {
                        is_array($usersDoneContent[$lessonId][$key]) ? $usersDoneContent[$lessonId][$key] = ($doneTests[$lessonId][$key] + $usersDoneContent[$lessonId][$key]) : $usersDoneContent[$lessonId][$key] = $doneTests[$lessonId][$key];    //We cannot use + for arrays, if one of them is not set.
                    }
                    if (isset($scormDoneContent[$lessonId][$key])) {
                        is_array($usersDoneContent[$lessonId][$key]) ? $usersDoneContent[$lessonId][$key] = ($scormDoneContent[$lessonId][$key] + $usersDoneContent[$lessonId][$key]) : $usersDoneContent[$lessonId][$key] = $scormDoneContent[$lessonId][$key];
                    }
                //}
            }

            foreach ($usersDoneContent[$lessonId] as $key => $value) {
                if (!$value || ($users != false && !in_array($key, $users))) {            //Filter out empty results or results not specified within $users array
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
        $temp      = EfrontStats :: getDoneTestsPerUser();
        //@todo: This is for compatibility with previous version and should be removed in the future
        foreach ($temp as $user => $value) {
            foreach ($value as $testId => $testData) {
                foreach ($testData as $done_tests_ID => $dt) {
                    if (($test = unserialize($dt['test'])) instanceof EfrontCompletedTest) {
                        $unit = $test -> getUnit();
                        $stats = array('lessons_ID' => $unit['lessons_ID'],
                                       'name'       => $test -> test['name'],
                                       'active'     => $unit['active'],
                                       'content_ID' => $unit['id'],
                                       'done_tests_ID' => $done_tests_ID,
                                       'tests_ID'   => $test -> test['id'],
                                       'score'      => $test -> completedTest['score'],
                                       'comments'   => $test -> completedTest['feedback'],
                                       'users_LOGIN'=> $user,
                                       'timestamp'  => $test -> time['end']);
                        if ($dt['archive'] == 0 && $test -> completedTest['status'] != 'incomplete' && $test -> completedTest['status'] != '' && ($lessons === false || in_array($stats['lessons_ID'], $lessons)) && in_array($stats['users_LOGIN'], $users)) {
                            $doneTests[$user][$test -> test['content_ID']] = $stats;
                        }
                    }
                }
            }                    
        }

        $usersDoneScormTests = eF_getTableData("content c, scorm_data sd", "c.lessons_ID, c.name, c.active, sd.masteryscore, sd.lesson_status, sd.content_ID, sd.score, sd.minscore, sd.maxscore, sd.users_LOGIN, sd.timestamp", "sd.content_ID = c.id and c.ctg_type = 'scorm_test' and sd.users_LOGIN != ''".($lessonId ? " and c.lessons_ID in ($lessonId)" : ""));
        foreach ($usersDoneScormTests as $doneScormTest) {
            if (!$users || in_array($doneTest['users_LOGIN'], $users)) {
                $doneScormTest['score'] = $doneScormTest['score'] / ($doneScormTest['maxscore'] - $doneScormTest['minscore']);
                $doneTests[$doneTest['users_LOGIN']][$doneScormTest['content_ID']] = $doneScormTest;
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
	public static function getDoneTestsPerUser($user = false, $test = false) {
	    if ($user !== false) {
    	    if ($user instanceof EfrontUser) {
    	        $user = $user -> user['login'];
    	    } else if (!eF_checkParameter($user, 'login')) {
    	        throw new EfrontUserException(_INVALIDLOGIN.': '.$user, EfrontUserException :: INVALID_LOGIN);
    	    }
	    }
	    if ($test !== false) {
    	    if ($test instanceof EfrontTest) {
    	        $test = $test -> test['id'];
    	    } else if (!eF_checkParameter($test, 'id')) {
    	        throw new EfrontTestException(_INVALIDID.': '.$test, EfrontTestException :: INVALID_ID);
    	    }
	    }
	    
	    if ($user && $test) {
	        $result = eF_getTableData("completed_tests", "*", "tests_ID=$test and users_LOGIN='$user'");
	    } else if ($user) {
	        $result = eF_getTableData("completed_tests", "*", "users_LOGIN='$user'");
	    } else if ($test) {
	        $result = eF_getTableData("completed_tests", "*", "tests_ID=$test");
	    } else {
	        $result = eF_getTableData("completed_tests", "*");
	    }
	    //Unserialize EfrontCompletedTest objects
	    $testResults = array();
	    foreach ($result as $value) {
	        $value['test'] = unserialize($value['test']);
	        $testResults[$value['users_LOGIN']][$value['tests_ID']][$value['id']] = $value;
	    }

	    //Loop through objects, so that a per lesson/per test array can be constructed, with statistics for each
        foreach ($testResults as $user => $tests) {
            $averageScores = array();
            foreach ($tests as $testId => $doneTests) {
                foreach ($doneTests as $doneTestId => $doneTest) {
                    $testResults[$user][$testId]['scores'][$doneTest['test'] -> completedTest['id']] = $doneTest['test'] -> completedTest['score'];                    
                    $doneTest['archive'] == 0 ? $testResults[$user][$testId]['last_test_id'] = $doneTest['test'] -> completedTest['id'] : null;
                    $doneTest['test']                         = serialize($doneTest['test']);
                    $testResults[$user][$testId][$doneTestId] = $doneTest;
                }
                if (!isset($testResults[$user][$testId]['last_test_id'])){
                    end($testResults[$user][$testId]['scores']);
                    $testResults[$user][$testId]['last_test_id'] = key($testResults[$user][$testId]['scores']);
                }                
                $testResults[$user][$testId]['average_score'] = round(array_sum($testResults[$user][$testId]['scores']) / sizeof($doneTests), 2);
                $testResults[$user][$testId]['max_score']     = max($testResults[$user][$testId]['scores']);
                $testResults[$user][$testId]['min_score']     = min($testResults[$user][$testId]['scores']);
                $testResults[$user][$testId]['times_done']    = sizeof($doneTests);
                $averageScores[] = $testResults[$user][$testId]['average_score'];
            }
            $testResults[$user]['average_score'] = round(array_sum($averageScores) / sizeof($averageScores), 2);
        }
        
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
	public static function getDoneTestsPerTest($user = false, $test = false) {
	    if ($user !== false) {
    	    if ($user instanceof EfrontUser) {
    	        $user = $user -> user['login'];
    	    } else if (!eF_checkParameter($user, 'login')) {
    	        throw new EfrontUserException(_INVALIDLOGIN.': '.$user, EfrontUserException :: INVALID_LOGIN);
    	    }
	    }
	    if ($test !== false) {
    	    if ($test instanceof EfrontTest) {
    	        $test = $test -> test['id'];
    	    } else if (!eF_checkParameter($test, 'id')) {
    	        throw new EfrontTestException(_INVALIDID.': '.$test, EfrontTestException :: INVALID_ID);
    	    }
	    }
	    
	    if ($user && $test) {
	        $result = eF_getTableData("completed_tests", "*", "status != '' and status != 'incomplete' and tests_ID=$test and users_LOGIN='$user'");
	    } else if ($user) {
	        $result = eF_getTableData("completed_tests", "*", "status != '' and status != 'incomplete' and users_LOGIN='$user'");
	    } else if ($test) {
	        $result = eF_getTableData("completed_tests", "*", "status != '' and status != 'incomplete' and tests_ID=$test");
	    } else {
	        $result = eF_getTableData("completed_tests", "*", "status != '' and status != 'incomplete'");
	    }
	    //Unserialize EfrontCompletedTest objects
	    $testResults = array();
	    foreach ($result as $value) {
	        $value['test'] = unserialize($value['test']);
	        $testResults[$value['tests_ID']][$value['users_LOGIN']][$value['id']] = $value;
	    }

	    //Loop through objects, so that a per lesson/per test array can be constructed, with statistics for each
        foreach ($testResults as $testId => $logins) {
            $averageScores = array();
	        foreach ($logins as $user => $doneTests) {
                foreach ($doneTests as $doneTestId => $doneTest) {
                    $testResults[$testId][$user]['scores'][$doneTest['test'] -> completedTest['id']] = $doneTest['test'] -> completedTest['score'];
                    $doneTest['archive'] == 0 ? $testResults[$testId][$user]['last_test_id'] = $doneTest['test'] -> completedTest['id'] : null;
                    $doneTest['test']                         = serialize($doneTest['test']);
                    $testResults[$testId][$user][$doneTestId] = $doneTest;
                    
                }
                if (!isset($testResults[$testId][$user]['last_test_id'])){
                    end($testResults[$testId][$user]['scores']);
                    $testResults[$testId][$user]['last_test_id'] = key($testResults[$testId][$user]['scores']);
                }
                $testResults[$testId][$user]['average_score'] = round(array_sum($testResults[$testId][$user]['scores']) / sizeof($doneTests), 2);
                $testResults[$testId][$user]['max_score']     = max($testResults[$testId][$user]['scores']);
                $testResults[$testId][$user]['min_score']     = min($testResults[$testId][$user]['scores']);
                $testResults[$testId][$user]['times_done']    = sizeof($doneTests);
                $averageScores[] = $testResults[$testId][$user]['average_score'];
            }
            $testResults[$testId]['average_score'] = round(array_sum($averageScores) / sizeof($averageScores), 2);
        }

	    return $testResults;
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
        !$toTimestamp   ? $toTimestamp   = time()                      : null;

        $result     = eF_getTableData("logs", "id, timestamp, lessons_ID, users_LOGIN", "users_LOGIN in (\"".implode('","', $users)."\") and timestamp between $fromTimestamp and $toTimestamp");
        foreach ($result as $value) {
            $logResults[$value['users_LOGIN']][] = $value;
        }

        $result         = eF_getTableData("logs", "users_LOGIN, count(id) as accesses", "users_LOGIN in (\"".implode('","', $users)."\") and lessons_ID = $lessonId and timestamp between $fromTimestamp and $toTimestamp group by users_LOGIN order by users_LOGIN");
        foreach ($result as $value) {
            $accessResults[$value['users_LOGIN']]= $value['accesses'];
        }

        $userTimes = array();
        foreach ($users as $login) {
            $totalTime = array('minutes' => 0, 'seconds' => 0, 'hours' => 0, 'total_seconds' => 0);
            $lessonStart = 0;
            $inLesson    = 0;
            if (isset($logResults[$login])) {
                foreach ($logResults[$login] as $value) {
                    if ($inLesson) {
                        if ($value['timestamp'] - $lessonStart >= 0) {
                            $interval = eF_convertIntervalToTime($value['timestamp'] - $lessonStart);
                        } else {
                            $interval = eF_convertIntervalToTime(0);                                        //This is to avoid negative times
                        }
                        if ($interval['hours'] == 0 && $interval['minutes'] <= 30) {
                            $totalTime['minutes'] += $interval['minutes'];
                            $totalTime['seconds'] += $interval['seconds'];
                        }
                        if ($value['lessons_ID'] != $lessonId) {
                            $inLesson = 0;
                        } else {
                            $lessonStart = $value['timestamp'];
                        }
                    } else if ($value['lessons_ID'] == $lessonId) {
                        $inLesson    = 1;
                        $lessonStart = $value['timestamp'];
                    }
                }
            }

            $sec = $totalTime['seconds'];

            if ($sec >= 60) {
                $totalTime['seconds']  = $sec % 60;;
                $totalTime['minutes'] += floor($sec / 60);;
            }
            if ($totalTime['minutes'] >= 60) {
                $totalTime['hours']   = floor($totalTime['minutes']/60);;
                $totalTime['minutes'] = $totalTime['minutes'] % 60;;
            }

            $totalTime['total_seconds']    = $totalTime['hours'] * 3600 + $totalTime['minutes'] * 60 + $totalTime['seconds'];
            $userTimes[$login]             = $totalTime;
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
            $usersAssignedProjects = eF_getTableData("projects p, users_to_projects up", "p.title, p.lessons_ID, up.projects_ID, up.grade, up.upload_timestamp, up.users_LOGIN as login, up.comments", "up.projects_ID = p.id and p.lessons_ID in (".implode(",", $lessons).")");
        }

        $asignedProjects       = array();
        foreach ($usersAssignedProjects as $project) {
            $login = $project['login'];
            if (!$users || in_array($login, $users)) {
                $asignedProjects[$login][$project['projects_ID']] = $project;
            }
        }
        return $asignedProjects;
    }


    public static function getUsersForumPosts($lessonId, $users = false) {
        $total_posts = array();
        $result      = eF_getTableData("f_messages fm, f_topics ft, f_forums ff", "fm.users_LOGIN as login, count(*) as cnt", "fm.f_topics_ID = ft.id and ft.f_forums_ID = ff.id and ff.lessons_ID = ".$lesson -> lesson['id'].$sql. " group by fm.users_LOGIN");
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
        $result         = eF_getTableData("comments cm, content c", "cm.users_LOGIN as login, count(*) as cnt", "cm.content_id = c.id and c.lessons_ID = ".$lesson -> lesson['id'].$sql. " group by cm.users_LOGIN");
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
    public static function getUsersLessonStatus($lessons = false, $users = false) {
        $usersDoneContent      = EfrontStats :: getStudentsSeenContent($lessons, $users);    //Calculate the done content for users in this lesson
        $usersAssignedProjects = EfrontStats :: getStudentsAssignedProjects($lessons, $users);
        $usersDoneTests        = EfrontStats :: getStudentsDoneTests($lessons, $users);

        $roles = EfrontLessonUser :: getLessonsRoles();
        //transpose projects array, from (login => array(project id => project)) to array(lesson id => array(login => array(project id => project)))
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

        if ($lessons === false) {
            $lessons = eF_getTableData("lessons", "*");
        } else if (!is_array($lessons)) {
            $lessons = array($lessons);
        }

        foreach ($lessons as $key => $lesson) {
            if (!($lesson instanceof EfrontLesson)) {
                $lessons[$key] = new EfrontLesson($lesson);
            }
        }

        if ($users != false) {
            !is_array($users) ? $users = array($users) : null;                //Convert single login to array
        } else {
            $users = eF_getTableDataFlat("users", "login", "user_type != 'administrator'");
            $users = $users['login'];
        }

        //Assign users their information
        $result = eF_getTableData("users", "*", "login in ('".implode("','", $users)."') and user_type != 'administrator'");
        $users  = array();
        foreach ($result as $value) {
            $users[$value['login']] = $value;
        }

        //Get lessons info for users
        $result = eF_getTableData("users_to_lessons", "*");
        foreach ($result as $value) {
            if (in_array($value['users_LOGIN'], array_keys($users))) {
                $usersLessons[$value['lessons_ID']][$value['users_LOGIN']]      = $value;
                $usersLessonsTypes[$value['lessons_ID']][$value['users_LOGIN']] = $roles[$value['user_type']];        //Handy since we need to know whether a lesson has any students
            }
        }

        foreach ($lessons as $lesson) {
            if (in_array('student', $usersLessonsTypes[$lesson -> lesson['id']])) {    //Calculate these statistics only if the lesson has students
                $lessonConditions = $lesson -> getConditions();
                $lessonContent    = new EfrontContentTree($lesson);
                $doneContent      = $usersDoneContent[$lesson -> lesson['id']];
                $doneTests        = $usersDoneTests[$lesson -> lesson['id']];
                $assignedProjects = $usersAssignedProjects[$lesson -> lesson['id']];

                $visitableContentIds = array();
                foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'theory', 'active' => 1))) as $key => $value) {
                    $visitableContentIds[$key] = $key;                                                    //Get the not-test unit ids for this content
                }
                foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'scorm', 'active' => 1))) as $key => $value) {
                    $visitableContentIds[$key] = $key;                                                    //Get the scorm ids for this content
                }

                $visitableExampleIds = array();
                foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'examples', 'active' => 1))) as $key => $value) {
                    $visitableExampleIds[$key] = $key;                                                    //Get the not-test unit ids for this content
                }

                $visitableTestIds = array();
                foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'tests', 'active' => 1))) as $key => $value) {
                    $visitableTestIds[$key] = $key;                                                    //Get the test unit ids for this content
                }
                foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'scorm_test', 'active' => 1))) as $key => $value) {
                    $visitableTestIds[$key] = $key;                                                    //Get the scorm test unit ids for this content
                }
                $visitableUnits = $visitableContentIds + $visitableExampleIds + $visitableTestIds;

                //$plain testIds, as opposed to $visitableTestIds, are used for statistics, so that inactive tests done by the user are also calculated
                $testIds = array();
                foreach ($iterator = (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'tests'))) as $key => $value) {
                    $testIds[$key] = $key;                                                    //Get the test unit ids for this content
                }
                foreach ($iterator = (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'scorm_test'))) as $key => $value) {
                    $testIds[$key] = $key;                                                    //Get the scorm test unit ids for this content
                }            
            }
            
            foreach ($usersLessons[$lesson -> lesson['id']] as $login => $value) {
                $lessonStatus[$lesson -> lesson['id']][$login]  = array('login'              => $login,
                                                                        'name'               => $users[$login]['name'],
                                                                        'surname'            => $users[$login]['surname'],
                                                                        'basic_user_type'    => $users[$login]['user_type'],
                                                                        'user_type'          => $value['user_type'],                        //The user's role in the lesson
                														'user_types_ID'	     => $users[$login]['user_types_ID'],
                                                                        'different_role'     => $value['user_type'] != $users[$login]['user_type'] && $value['user_type'] != $users[$login]['user_types_ID'],    //Whether the user has a role different than the default in this lesson                
                                                                        'active'			 => $users[$login]['active'],
                                                                        'lesson_name'        => $lesson -> lesson['name'],
                                                                        'from_timestamp'     => $value['from_timestamp']);
                //Student - specific information
                if ($roles[$value['user_type']] == 'student') {
                    $conditionsMet  = self :: checkConditions($doneContent[$login], $lessonConditions, $visitableUnits, $visitableTestIds);

                    //Content progress is theory and examples units seen
                    $contentProgress = 0;
                    if (isset($doneContent[$login]) && sizeof($doneContent[$login]) > 0 && (sizeof($visitableContentIds) > 0 || sizeof($visitableExampleIds) > 0)) {
                        $contentProgress = round(100 * sizeof(array_diff_key($doneContent[$login], $visitableTestIds)) / (sizeof($visitableContentIds) + sizeof($visitableExampleIds)), 2);
                    }

                    //Calculate tests average score and progress
                    $testsProgress     = 0;
                    $numCompletedTests = 0;
                    $testsAvgScore     = array();                    
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
                    $doneProjects     = array();
                    $projectsAvgScore = array();
                    $projectsProgress = 0;                    
                    if (isset($assignedProjects[$login]) && sizeof($assignedProjects[$login]) > 0) {
                        foreach ($assignedProjects[$login] as $id => $project) {
                            if ($project['grade'] !== '' || $project['upload_timestamp']) {
                                $doneProjects[$id]  = $project;
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
                    
                    $lessonStatus[$lesson -> lesson['id']][$login]['projects_progress']  = $projectsProgress;            //the projects percentage done
                    $lessonStatus[$lesson -> lesson['id']][$login]['projects_avg_score'] = $projectsAvgScore;            //the projects average score
                    $lessonStatus[$lesson -> lesson['id']][$login]['tests_progress']   = $testsProgress;                 //the tests percentage done
                    $lessonStatus[$lesson -> lesson['id']][$login]['tests_avg_score']  = $testsAvgScore;                 //the tests average score
                    $lessonStatus[$lesson -> lesson['id']][$login]['content_progress'] = $contentProgress;               //the content (theory_examples) percentage done
                    $lessonStatus[$lesson -> lesson['id']][$login]['overall_progress'] = $overallProgress;               //the total percentage done, including content and tests
                    $lessonStatus[$lesson -> lesson['id']][$login]['lesson_passed']    = array_product($conditionsMet);
                    $lessonStatus[$lesson -> lesson['id']][$login]['total_conditions'] = sizeof($lessonConditions);
                    $lessonStatus[$lesson -> lesson['id']][$login]['conditions_passed']= array_sum($conditionsMet);
                    $lessonStatus[$lesson -> lesson['id']][$login]['completed']        = $value['completed'];
                    $lessonStatus[$lesson -> lesson['id']][$login]['score']            = $value['score'];
                    $lessonStatus[$lesson -> lesson['id']][$login]['comments']         = $value['comments'] ? $value['comments'] : 0;
                }
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
    public static function getUsersCourseStatus($courses = false, $users = false) {
        $roles = EfrontLessonUser :: getLessonsRoles();
        
        if ($courses === false) {
            $courses = eF_getTableData("courses", "*");
        } else if (!is_array($courses)) {
            $courses = array($courses);
        }

        $coursesLessons = array();
        foreach ($courses as $key => $course) {
            if (!($course instanceof EfrontCourse)) {
                $courses[$key] = new EfrontCourse($course);
            }
            $coursesLessons = $courses[$key] -> getLessons() + $coursesLessons;
        }
        
        //get statistics array
        $lessonsStatus = self :: getUsersLessonStatus(array_keys($coursesLessons), $users);

        if ($users != false) {
            !is_array($users) ? $users = array($users) : null;                //Convert single login to array
        } else {
            $users = eF_getTableDataFlat("users", "login", "user_type != 'administrator'");
            $users = $users['login'];
        }
        
        //Assign users their information
        $result = eF_getTableData("users", "*", "login in ('".implode("','", $users)."') and user_type != 'administrator'");
        $users  = array();
        foreach ($result as $value) {
            $users[$value['login']] = $value;
        }

        //Get lessons info for users
        $result = eF_getTableData("users_to_courses", "*");
        foreach ($result as $value) {
            if (in_array($value['users_LOGIN'], array_keys($users))) {
                $usersCourses[$value['courses_ID']][$value['users_LOGIN']]      = $value;
                $usersCoursesTypes[$value['courses_ID']][$value['users_LOGIN']] = $roles[$value['user_type']];        //Handy since we need to know whether a course has any students
            }
        }
        
        foreach ($courses as $course) {
            //transpose and filter lessons statistics array, for convenience, from  lesson id => login to login => lesson id
            foreach ($lessonsStatus as $lessonId => $info) {
                if (in_array($lessonId, array_keys($course -> getLessons()))) {
                    foreach ($info as $login => $stats) {
                        $userLessonStatus[$course -> course['id']][$login][$lessonId] = $stats;
                    }
                }
            }

            if (sizeof($usersCourses[$course -> course['id']]) > 0) { 
                foreach ($usersCourses[$course -> course['id']] as $login => $value) {
                    $courseStatus[$course -> course['id']][$login]  = array('login'              => $login,
                                                                            'name'               => $users[$login]['name'],
                                                                            'surname'            => $users[$login]['surname'],
                                                                            'basic_user_type'    => $users[$login]['user_type'],
                                                                            'user_type'          => $value['user_type'],                //User type in course
                                                                            'user_types_ID'	     => $users[$login]['user_types_ID'],
                                                                            'different_role'     => $value['user_type'] != $users[$login]['user_type'] && $value['user_type'] != $users[$login]['user_types_ID'],    //Whether the user has a role different than the default in this course
                    													    'active'			 => $users[$login]['active'],
                                                                            'course_name'        => $course -> course['name'],
                                                                            'from_timestamp'     => $value['from_timestamp']);
                    //Student - specific information
                    if ($roles[$value['user_type']] == 'student') {
                        $courseStatus[$course -> course['id']][$login]['completed']          = $value['completed'];
                        $courseStatus[$course -> course['id']][$login]['score']              = $value['score'];
                        $courseStatus[$course -> course['id']][$login]['comments']           = $value['comments'];
                        $courseStatus[$course -> course['id']][$login]['issued_certificate'] = $value['issued_certificate'];
                        $courseStatus[$course -> course['id']][$login]['total_lessons']      = sizeof($course -> lessons);
                        //Count completed lessons 
                        $completedLessons = 0;
                        if (isset($userLessonStatus[$course -> course['id']][$login])) {
                            foreach ($userLessonStatus[$course -> course['id']][$login] as $lesson) {
                                if ($lesson['completed']) {
                                    $completedLessons++;
                                }
                            }
                        }
                        $courseStatus[$course -> course['id']][$login]['completed_lessons'] = $completedLessons;                    
                    }
                    //Append the course's lessons information
                    $courseStatus[$course -> course['id']][$login]['lesson_status'] = $userLessonStatus[$course -> course['id']][$login];
                }
            }
        }

        return $courseStatus;        
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
    public static function checkConditions($seenUnits, $conditions, $visitableContentIds, $visitableTestIds) {
        !$seenUnits ? $seenUnits = array() : null;
        $notSeenUnits  = array_diff_key($visitableContentIds, $seenUnits);            //The units that the user has yet to see
        $conditionsMet = array();
        foreach ($conditions as $conditionId => $condition) {
            switch ($condition['type']) {
                case 'all_units':
                    sizeof($notSeenUnits) == 0 ? $passed = true : $passed = false;
                    break;
                case 'percentage_units':
                    $percentageSeen = round(100 * (sizeof($visitableContentIds) - sizeof($notSeenUnits)) / sizeof($visitableContentIds));
                    $percentageSeen >= $condition['options'][0] ? $passed = true : $passed = false;
                    break;
                case 'specific_unit':
                    in_array($condition['options'][0], array_keys($seenUnits)) ? $passed = true : $passed = false;
                    break;
                case 'all_tests':
                    $passed = true;
                    foreach ($visitableTestIds as $id) {
                        $score = $seenUnits[$id];
                        $score < $condition['options'][0] / 100 ? $passed = false : null;
                    }
                    break;
                case 'mean_all_tests':
                    $meanScore = array_sum($seenUnits) / array_sum(array_count_values($seenUnits));        //array_count_values does not take into account false entries. So, in this expression, the denominator equals the number of tests the user has done
                    $meanScore >= $condition['options'][0] / 100 ? $passed = true : $passed = false;
                    break;
                case 'specific_test':
                    in_array($condition['options'][0], array_keys($seenUnits)) && $seenUnits[$condition['options'][0]] >= $condition['options'][1] / 100 ? $passed = true : $passed = false;
                    break;
                default:
                    break;
            }
            $conditionsMet[$conditionId] = $passed;
        }
        return $conditionsMet;
    }

    /**
     * Get user communication info
     *
     * This returns cimmmunication info for a user
     * <br/>Example:
     * <code>
     * $info = EfrontStats :: getUserCommunicationInfo('jdoe');                   //Get information for user jdoe
     *
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
        $info           = array();

        $forum_info     = eF_getTableData("f_messages", "*", "users_LOGIN='".$user -> login."'", "timestamp desc");
        $forum_messages = array();
        foreach ($forum_info as $message) {
            $forum_messages[$message['id']] = $message;
        }

        $personal_messages_info = eF_getTableData("f_personal_messages", "*", "users_LOGIN='".$user -> login."'", "timestamp desc");
        $personal_messages      = array();
        foreach ($personal_messages_info as $message) {
            $personal_messages[$message['id']] = $message;
        }

        $personal_folders_info = eF_getTableData("f_folders", "*", "users_LOGIN='".$user -> login."'");
        $personal_folders      = array();
        foreach ($personal_folders_info as $folder) {
            $personal_folders[$folder['id']] = $folder;
        }

        $file_info = eF_getTableData("files", "*", "users_LOGIN='".$user -> login."' and type='file'");
        $files     = array();
        $size      = 0;
        foreach ($file_info as $file) {
            $size += filesize($file['file']);
        }

        $chat_messages_info = eF_getTableData("chatmessages", "*", "users_LOGIN='".$user -> login."'", "timestamp desc");
        $chat_messages      = array();
        foreach ($chat_messages_info as $message) {
            $chat_messages[$message['id']] = $message;
        }

        $comments_info = eF_getTableData("comments", "*", "users_LOGIN='".$user -> login."'", "timestamp desc");
        $comments      = array();
        foreach ($comments_info as $comment) {
            $comments[$comment['id']] = $comment;
        }

        $info['forum_messages']            = $forum_messages;
        if (sizeof($info['forum_messages']) > 0) {
            $info['last_message']          = current($info['forum_messages']);
        }
        $info['personal_messages']         = $personal_messages;
        $info['personal_folders']          = $personal_folders;
        $info['files']                     = $files;
        $info['total_size']                = $size;
        $info['chat_messages']             = $chat_messages;
        if (sizeof($info['chat_messages']) > 0) {
            $info['last_chat']             = current($info['chat_messages']);
        }
        $info['comments']                  = $comments;

        return $info;
    }


    /**
     * Get user usage info
     *
     * This returns usage info for a user
     * <br/>Example:
     * <code>
     * $info = EfrontStats :: getUserUsageInfo('jdoe');                   //Get usage information for user jdoe
     *
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
        $info       = array();
        $login_info = eF_getTableData("logs", "*", "users_LOGIN='".$user -> login."' and action = 'login'", "timestamp desc");
        $logins     = array();
        foreach ($login_info as $login) {
            $logins[$login['id']] = $login;
        }

        $month_login_info = eF_getTableData("logs", "*", "users_LOGIN='".$user -> login."' and action = 'login' and timestamp > ".(time() - 2592000)."");
        $month_logins     = array();
        foreach ($month_login_info as $login) {
            $month_logins[$login['id']] = $login;
        }

        $week_login_info = eF_getTableData("logs", "*", "users_LOGIN='".$user -> login."' and action = 'login' and timestamp > ".(time() - 604800)."");
        $week_logins = array();
        foreach ($week_login_info as $login) {
            $week_logins[$login['id']] = $login;
        }

        $temp = eF_getUserTimes($user -> login);
        sizeof($temp['duration']) > 0 ? $mean_duration = ceil((array_sum($temp['duration']) / sizeof($temp['duration'])) / 60) : $mean_duration = 0;
        $temp = eF_getUserTimes($user -> login, array('from' => time() - 2592000, 'to' => time()));
        sizeof($temp['duration']) > 0 ? $month_mean_duration = ceil((array_sum($temp['duration']) / sizeof($temp['duration']) / 60)) : $month_mean_duration = 0;

        $temp = eF_getUserTimes($user -> login, array('from' => time() - 604800, 'to' => time()));
        sizeof($temp['duration']) > 0 ? $week_mean_duration = ceil((array_sum($temp['duration']) / sizeof($temp['duration']) / 60)) : $week_mean_duration = 0;


        $info['logins']               = $logins;
        if (sizeof($info['logins']) > 0) {
            $info['last_login']       = current($info['logins']);
        }
        $info['month_logins']         = $month_logins;
        $info['week_logins']          = $week_logins;
        $info['mean_duration']        = $mean_duration;
        $info['month_mean_duration']  = $month_mean_duration;
        $info['week_mean_duration']   = $week_mean_duration;

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
     *
     * @param mixed $tests Either an array of tests id or false (request information for all existing tests)
     * @return array the tests' statistinc info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getTestInfo($tests = false) {
        if ($tests == false) {
            $tests = eF_getTableDataFlat("tests, content", "tests.id", "tests.content_ID=content.id and tests.lessons_ID != 0");    //This way we get tests that have a corresponding unit
            $tests = $tests['id'];
        } elseif (!is_array($tests)) {
            $tests = array($tests);
        }

        $lessonNames = eF_getTableDataFlat("lessons", "id,name");
        sizeof($lessonNames) > 0 ? $lessonNames = array_combine($lessonNames['id'], $lessonNames['name']) : $lessonNames = array();
        $result = eF_getTableData("users", "name, surname, login");
        $users  = array();
        foreach ($result as $user) {
            $users[$user['login']] = $user;
        }
        
        $doneTests = EfrontStats :: getDoneTestsPerTest();
        
        foreach ($tests as $id) {
            $testInfo = array();
            $test      = new EfrontTest($id);
            $unit      = $test -> getUnit();
            $testInfo['general']['id']              = $id;
            $testInfo['general']['name']            = $unit -> offsetGet('name');
            $testInfo['general']['content_ID']      = $unit -> offsetGet('id');
            $testInfo['general']['lesson_name']     = $lessonNames[$unit -> offsetGet('lessons_ID')];
            $testInfo['general']['duration']        = $test -> options['duration'];
            $testInfo['general']['duration_str']    = eF_convertIntervalToTime($test -> options['duration']);
            $testInfo['general']['redoable']        = $test -> options['redoable'];
            $testInfo['general']['redoable_str']    = $test -> options['redoable'] >= 1 ? _YES : _NO;
            $testInfo['general']['onebyone']        = $test -> options['onebyone'];
            $testInfo['general']['onebyone_str']    = $test -> options['onebyone'] == 1 ? _YES : _NO;
            $testInfo['general']['answers']         = $test -> options['answers'];
            $testInfo['general']['answers_str']     = $test -> options['answers'] == 1  ? _YES : _NO;
            $testInfo['general']['description']     = $test -> test['description'];
            $testInfo['general']['timestamp']       = $unit -> offsetGet('timestamp');
            $testInfo['general']['timestamp_str']   = strftime('%d-%m-%Y, %H:%M:%S', $testInfo['general']['timestamp']);
            $testInfo['general']['scorm']           = 0;

            $testInfo['questions']['total']         = 0;
            $testInfo['questions']['raw_text']      = 0;
            $testInfo['questions']['multiple_one']  = 0;
            $testInfo['questions']['multiple_many'] = 0;
            $testInfo['questions']['true_false']    = 0;
            $testInfo['questions']['match']         = 0;
            $testInfo['questions']['empty_spaces']  = 0;
            $testInfo['questions']['low']           = 0;
            $testInfo['questions']['medium']        = 0;
            $testInfo['questions']['high']          = 0;

            $questions = $test -> getQuestions(true);
            foreach ($questions as $question) {
                $testInfo['questions']['total']++;
                $testInfo['questions'][$question -> question['type']]++;
                $testInfo['questions'][$question -> question['difficulty']]++;
            }

            //@todo: Compatibility status with old versions, need to change
            $testInfo['done'] = array();

            //$done_info        = $temp[$id];
            foreach ($doneTests[$id] as $user => $done) {
                foreach ($done as $dt) {
                    if ($dt['archive'] == 0 && $dt['status'] != 'incomplete' && $dt['status'] != '' && $dt['test'] = unserialize($dt['test'])) {
                        $done_test = array('id'            => $dt['id'],
                                           'users_LOGIN'   => $dt['users_LOGIN'],
                                           'name'		   => $users[$dt['users_LOGIN']]['name'],
                                           'surname'	   => $users[$dt['users_LOGIN']]['surname'],
                        				   'score'         => $dt['test'] -> completedTest['score'],
                                           'timestamp'     => $dt['test'] -> time['end'],
                                           'mastery_score' => $dt['test'] -> test['mastery_score'],
                                           'status'        => $dt['status']);
                        $testInfo['done'][] = $done_test;
                    }
                }
            }

            $testsInfo[$id] = $testInfo;
            

/*
            $done_info = eF_getTableData("done_tests d, users u, tests t", "t.mastery_score, d.users_LOGIN, u.name, u.surname, d.score, d.timestamp, d.id", "d.tests_ID = t.id and d.users_LOGIN = u.LOGIN and t.id = $id");
            foreach ($done_info as $done) {
                $done_test = array();
                $done_test['id']          = $done['id'];
                $done_test['users_LOGIN'] = $done['users_LOGIN'];
                $done_test['name']        = $done['name'];
                $done_test['surname']     = $done['surname'];
                $done_test['score']       = $done['score'] * 100;
                $done_test['timestamp']   = $done['timestamp'];
                $done_test['mastery_score'] = $done['mastery_score'];
                $done_test['status']      = $done['mastery_score'] > $done['score'] * 100 ? 'failed' : 'passed';

                $testInfo['done'][]      = $done_test;
            }

            $testsInfo[$id] = $testInfo;
*/
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
     *
     * @param mixed $tests Either an array of tests id or false (request information for all existing tests)
     * @return array the tests' statistic info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getScormTestInfo($tests = false) {
        $tests_info = array();
        if ($tests == false) {
            $tests = eF_getTableData("tests","id");
        }
        foreach ($tests as $test_id) {
            $test_info                           = array();
            $unit                                = new EfrontUnit($test_id);
            $test_info['general']['content_id']  = $test_id;
            $test_info['general']['id']          = $test_id;
            $test_info['general']['name']        = $unit -> offsetGet('name');
            $test_info['general']['scorm']       = 1;
            $test_info['done'] = array();
            $done_info = eF_getTableData("scorm_data d, users u", "d.users_LOGIN, u.name, u.surname, d.score, d.timestamp",
                "d.users_LOGIN = u.LOGIN and d.content_ID = $test_id");
            foreach ($done_info as $done) {
                $done_test = array();
                $done_test['users_LOGIN'] = $done['users_LOGIN'];
                $done_test['name']        = $done['name'];
                $done_test['surname']     = $done['surname'];
                $done_test['score']       = $done['score'];
                $done_test['timestamp']   = $done['timestamp'];
                $test_info['done'][]      = $done_test;
            }
            $tests_info[$test_id] = $test_info;
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
     *
     * @param mixed $questions Either an array of question id or false (request information for all existing questions)
     * @return array the questions' statistic info
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getQuestionInfo($questions = false) {
        $questions_info = array();
        if ($questions == false) {
            $questions = eF_getTableData("questions", "id");
        }
        foreach ($questions as $question_id) {
            $question_info                     = array();
            $question                          = QuestionFactory :: factory($question_id);
            $question_info['general']['id']    = $question_id;
            $question_info['general']['text']  = $question -> question['text'];
            $question_info['general']['reduced_text']= $question -> question['plain_text'];
            $question_info['general']['type']        = $question -> question['type'];
            $question_info['general']['difficulty']  = $question -> question['difficulty'];
            $question_info['general']['content_ID']  = $question -> question['content_ID'];
            $question_info['general']['explanation'] = $question -> question['explanation'];
            $question_info['general']['options']     = $question -> question['options'];
            $question_info['general']['answer']      = $question -> question['answer'];
            $question_info['general']['timestamp']   = $question -> question['timestamp'];
            $question_info['done']['times_done'] = 0;
            $question_info['done']['avg_score']  = 0;

            $questions_info[$question_id] = $question_info;

            $questionIds[$question_id] = $question_id;
        }
        
        $completedTests = eF_getTableData("completed_tests", "*");
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
     *
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
            $project_info                           = array();
            $project                                = new EfrontProject($project_id);
            $project_info['general']['id']          = $project_id;
            $project_info['general']['title']       = $project -> project['title'];
            $project_info['general']['data']        = $project -> project['data'];
            $project_info['general']['deadline']    = $project -> project['deadline'];
            $project_info['general']['auto_assign'] = $project -> project['auto_assign'];

            $project_info['done'] = array();
            $assigned_data = eF_getTableData("users u, users_to_projects up", "u.LOGIN, u.name, u.surname, up.grade, up.upload_timestamp, up.status, up.comments", "u.LOGIN = up.users_LOGIN and up.projects_ID=".$project_id);
            foreach ($assigned_data as $data) {
                $done_project = array();
                $done_project['users_LOGIN']      = $data['LOGIN'];
                $done_project['name']             = $data['name'];
                $done_project['surname']          = $data['surname'];
                $done_project['grade']            = $data['grade'] ? $data['grade'] : 0;
                $done_project['upload_timestamp'] = $data['upload_timestamp'];
                $done_project['status']           = $data['status'];
                $done_project['comments']         = $data['comments'];
                $project_info['done'][]           = $done_project;
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
            $results = eF_getTableData("completed_tests", "*", "tests_ID=$testInfo");
            foreach ($results as $value) {
                $value['test']           = unserialize($value['test']);
                $doneTests[$value['id']] = $value;
            }            
        } else {
            throw new EfrontStatsException(_INVALIDPARAMETER.': '.$testInfo, EfrontStatsException :: INVALID_PARAMETER);
        }
        
        //Initialize statistics array
        $stats = array('timesDone' => 0, 'meanScore' => 0, 'meanMeanScore' => 0, 'lastTimesMeanScore' => 0);
        
        //Calculate statistics per user
        foreach ($doneTests as $doneTestId => $doneTest) {
            if (!($doneTest['test'] instanceof EfrontCompletedTest)) {        //Unserialize test parameter, only if needed (otherwise it is already unserialized) 
                $doneTest['test'] = unserialize($doneTest['test']);
                $doneTests[$doneTestId]['test'] = $doneTest['test'];                
            }

            $user = $doneTest['users_LOGIN'];
        
            $testUsers[$user]['score'][$doneTestId]     = $doneTest['test'] -> completedTest['score'];
            $testUsers[$user]['result'][$doneTestId]    = $doneTest['test'] -> completedTest['status'];
            $testUsers[$user]['timestamp'][$doneTestId] = $doneTest['test'] -> time['end'];
            $stats['timesDone']++;
            $stats['meanScore'] += $doneTest['test'] -> completedTest['score'];
        }

        //Caclulate accumulative statistics per user
        foreach ($testUsers as $user => $value) {
            $testUsers[$user]['timesDone'] = sizeof($value['score']); 
            $testUsers[$user]['meanScore'] = round(array_sum($value['score']) / sizeof($value['score']), 2);
            $stats['meanMeanScore']       += $testUsers[$user]['meanScore'];
            $stats['lastTimesMeanScore']  += end($value['score']);
        }

        //Calulate accumulative statistics for the test
        $stats['meanScore']          = round($stats['meanScore'] / $stats['timesDone'], 2);               //This is the mean score of all test executions
        $stats['meanMeanScore']      = round($stats['meanMeanScore'] / sizeof($testUsers), 2);            //This is the mean score of mean scores
        $stats['lastTimesMeanScore'] = round($stats['lastTimesMeanScore'] / sizeof($testUsers), 2);       //This is the mean score of the last execution of each test
        
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
	    $unitStats      = array();
	    foreach ($questions as $id => $question) {
	        if (!isset($unitsData[$question -> question['content_ID']])) {            //Initialize data array
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
	        $result = eF_getTableData("completed_tests", "*");
	    } else {
	        $test instanceof EfrontTest ? $testId = $test -> test['id'] : $testId = $test;
	        if (!eF_checkParameter($testId, 'id')) {
	            throw new EfrontTestException(_INVALIDID, EfrontLessonException :: INVALID_ID);
	        }
	        $result = eF_getTableData("completed_tests", "*", "tests_ID = $testId");
	    }
	    
	    $questionStats = array();
	    
	    
	    foreach ($result as $value) {
	        $completedTest = unserialize($value['test']);
	        foreach ($completedTest -> questions as $id => $question) {
	            $questionStats[$id]['score'][]          = $question -> score;
	            $questionStats[$id]['completed_test'][] = $value['id'];
	            $questionStats[$id]['test'][]           = $value['tests_ID'];
	            $questionStats[$id]['login'][]          = $value['users_LOGIN'];
	            $questionStats[$id]['timestamp'][]      = $completedTest -> time['end'];
	        }
	    }
        	    
	    foreach ($questionStats as $id => $question) {
	        $questionStats[$id]['times_done'] = sizeof($question['score']);
	        $questionStats[$id]['avg_score']  = array_sum($question['score']) / sizeof($question['score']);
	        $questionStats[$id]['max_score']  = max($question['score']);
	        $questionStats[$id]['min_score']  = min($question['score']);
	    }
	    
	    return $questionStats;
	}
    

}





?>