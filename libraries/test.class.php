<?php


/**
 *
 */
class EfrontTestException extends Exception
{
    const INVALID_ID          = 801;
    const QUESTION_NOT_EXISTS = 802;
    const TEST_NOT_EXISTS     = 803;
    const NOT_DONE_TEST       = 804;
    const INVALID_LOGIN       = 805;
    const DONE_QUESTION_NOT_EXISTS = 806;
    const INVALID_TYPE        = 807;
    const INVALID_SCORE       = 808;
    const DATABASE_ERROR      = 809;
    const ERROR_CREATING_DIRECTORY = 810;
}

/**
 *
 */
class EfrontTest
{
    /**
     * The test fields
     *
     * @var array
     * @access public
     * @since 3.5.0
     */
    public $test = array();

    /**
     * The content unit representing the test
     *
     * @var EfrontUnit
     * @access protected
     * @since 3.5.0
     */
    protected $unit = false;

    /**
     * The questions order
     *
     * @var array
     * @access protected
     * @since 3.5.0
     */
    protected $questionsOrder = false;

    /**
     * The questions in this test
     *
     * @var array
     * @access public
     * @since 3.5.0
     */
    public $questions = false;

    /**
     * Information for done test
     *
     * @var array
     * @access protected
     * @since 3.5.0
     */
    public $doneInfo = false;


    public $options = array('duration'          => 0,
                            'redoable'          => 0,
                            'onebyone'          => 0,
                            'answers'           => 1,
                            'shuffle_questions' => 0,
                            'shuffle_answers'   => 0,
                            'given_answers'     => 1,
                            'random_pool'       => 0,
                            'display_list'      => 0,
                            'pause_test'        => 1,
                            'display_weights'   => 1);

    /**
     * Class constructor
     *
     * This function is used to instantiate a test  object.
     * If an id is used, then the test is instantiate based on
     * database information. Alternatively, the test array itself
     * may be provided, thus overriding database query.
     * <br/>Example:
     * <code>
     * $test   = new EfrontTest(4);                         //Instantiate test using test id
     *
     * $result = eF_getTableData("tests", "*", "id=4");
     * $test   = new EfrontTest($result[0]);                //Instantiate test using test array
     *
     * $test = new EfrontTest(54, true);                    //Instantiate test, only this time specify content id
     * </code>
     *
     * @param mixed $test Either a test id, a content id or a test array
     * @param boolean $isContentId Whether the id specified is actually a content id and not a test id
     * @since 3.5.0
     * @access public
     */
    function __construct($test, $isContentId = false) {
        if (is_array($test)) {
            $this -> test = $test;
        } elseif (!eF_checkParameter($test, 'id')) {
            throw new EfrontTestException(_INVALIDID.': '.$test, EfrontTestException :: INVALID_ID);
        } else {
            if ($isContentId){
                $result = eF_getTableData("tests t, content c", "t.*, c.name", "c.id = t.content_ID and content_ID=".$test);
            } else {
                $result = eF_getTableData("tests t, content c", "t.*, c.name", "c.id=t.content_ID and t.id=".$test);
                if (sizeof($result) == 0) {
                    $result = eF_getTableData("tests", "*", "id=".$test);
                }
            }

            if (sizeof($result) == 0) {
                throw new EfrontTestException(_INVALIDID.': '.$test, EfrontTestException :: TEST_NOT_EXISTS);
            } else {
                $this -> test = $result[0];
            }
        }

        if ($this -> test['options'] && $options = unserialize($this -> test['options'])) {
            $newOptions      = array_diff_key($this -> options, $options);    //$newOptions are test options that were added to the EfrontTest object AFTER the test options serialization took place
            $this -> options = $options + $newOptions;                        //Set test options
        }

        if ($this -> options['duration']) {
            $this -> convertedDuration = eF_convertIntervalToTime($this -> options['duration']);
        }

    }

    /**
     * Delete test
     *
     * This function is used to delete the current
     * test. All data associated with this test will
     * be erased.
     * <br/>Example:
     * <code>
     * </code>
     *
     * @return boolean true if the test was deleted successfully
     * @since 3.5.0
     * @access public
     */
    public function delete() {
        eF_deleteTableData("tests_to_questions", "tests_ID=".$this -> test['id']);
        eF_deleteTableData("done_tests", "tests_ID=".$this -> test['id']);
        eF_deleteTableData("content", "id=".$this -> test['content_ID']);
        eF_deleteTableData("tests", "id=".$this -> test['id']);

        return true;
    }

    /**
     * Activate test
     *
     * This function is used to activate the current test.
     * The function also activates the corresponding content
     * unit, if it is not already activated
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(32);          //Instantiate test object
     * $test -> activate();                 //Activate test
     * </code>
     */
    public function activate() {
        $this -> test['active'] = 1;
        $this -> persist();

        $unit = new EfrontUnit($this -> test['content_ID']);
        if (!$unit['active']) {
            $unit -> activate();
        }
        $this -> unit = new EfrontUnit($this -> test['content_ID']);
    }

    /**
     * Deactivate test
     *
     * This function is used to deactivate the current test.
     * The function also deactivates the corresponding content
     * unit, if it is not already deactivated
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(32);          //Instantiate test object
     * $test -> deactivate();               //Deactivate test
     * </code>
     */
    public function deactivate() {
        $this -> test['active'] = 0;
        $this -> persist();

        $unit = new EfrontUnit($this -> test['content_ID']);
        if ($unit['active']) {
            $unit -> deactivate();
        }
        $this -> unit = new EfrontUnit($this -> test['content_ID']);
    }

    /**
     * Persist test changes
     *
     * This function is used to persist changes made to the
     * current test object.
     * <br/>Example:
     * <code>
     * $test -> options['duration'] = 100;          //Update test duration
     * $test -> persist();                      //Persist changed value with database.
     * </code>
     *
     * @return boolean true if everything is ok
     * @since 3.5.0
     * @access public
     */
    public function persist() {
        $fields = array('active'        => $this -> test['active'],
                        'content_ID'    => $this -> test['content_ID'],
                        'options'       => serialize($this -> options),
                        'description'   => $this -> test['description'],
                        'mastery_score' => $this -> test['mastery_score'],
                        'name'          => $this -> test['name'],
                        'lessons_ID'    => $this -> test['lessons_ID'],
                        'publish'       => $this -> test['publish']);        
        return eF_updateTableData("tests", $fields, "id=".$this -> test['id']) && eF_updateTableData("content", array("publish" => $this -> test['publish']), "id=".$this -> test['content_ID']);
    }

    /**
     * Get test questions
     *
     * This function returns a list with all the test's questions. If $returnObjects is true,
     * then Question objects are returned.
     * Note that this function returns the questions in the order specified for the specific test.
     * <br/>Example:
     * <code>
     * $questions = $this -> getQuestions();
     * </code>
     *
     * @param boolean $returnObjects Whether to return Question objects
     * @return array An array of questions
     * @since 3.5.0
     * @access public
     */
    public function getQuestions($returnObjects = false) {
        if ($this -> questions === false) {
            $result = eF_getTableData("tests_to_questions tq, questions q", "q.*, tq.weight, tq.previous_question_ID", "tq.questions_ID=q.id and tq.tests_ID=".$this -> test['id']);
            
            if (sizeof($result) > 0) {
                foreach ($result as $value) {
                    $value['type_icon']      = Question :: $questionTypesIcons[$value['type']];
                    $questions[$value['id']] = $value;
                    $previousQuestions[$value['previous_question_ID']] = $value;
                }

                //Sorting algorithm, based on previous_question_ID. the algorithm is copied from EfrontContentTree :: reset() an is the same with the one applied for content
                $node  = 0;
                $count = 0;
                $nodes = array();                                                                          //$count is used to prevent infinite loops
                while (sizeof($previousQuestions) > 0 && isset($previousQuestions[$node]) && $count++ < 1000) {
                    $nodes[$previousQuestions[$node]['id']] = $previousQuestions[$node];
                    $newNode = $previousQuestions[$node]['id'];
                    unset($previousQuestions[$node]);
                    $node    = $newNode;
                }
                $this -> questions      = $nodes;
            } else {
                $this -> questions = array();
            }
        }

        $questions = array();
        foreach ($this -> questions as $key => $value) {
            if (!($value instanceof Question)) {
                $returnObjects ? $questions[$key] = QuestionFactory :: factory($value) : $questions[$key] = $value;
            } else {
                $returnObjects ? $questions[$key] = $value : $questions[$key] = $value -> question;
            }
        }

        return $questions;
    }

    /**
     * Get potential test questions
     *
     * This function is used to return all questions that could be, but are not
     * part of this test.
     * <br/>Example:
     * <code>
     * $nonQuestions = $test -> getNonQuestions();
     * </code>
     *
     * @param boolean $returnObjects Whether to return an array of Question objects
     * @return array The questions that could, but don't belong to the test
     * @since 3.5.0
     * @access public
     */
    public function getNonQuestions($returnObjects = false) {
        $lesson        = $this -> getLesson();
        $testQuestions = $this -> getQuestions();

        if (sizeof($testQuestions) > 0) {
            // The check is here to include skill gap test management with lesson_ID = 0
            if (!empty($lesson)) {
                $result = eF_getTableData("questions", "*", "id not in (".implode(",", array_keys($testQuestions)).") and lessons_ID=".key($lesson));
            } else {
                $result = eF_getTableData("questions", "*", "id not in (".implode(",", array_keys($testQuestions)).")");
            }
        } else {
            if (!empty($lesson)) {
                $result = eF_getTableData("questions", "*", "lessons_ID=".key($lesson));
            } else {
                $result = eF_getTableData("questions", "*", "");
            }
        }

        $nonQuestions = array();
        foreach ($result as $value) {
            $returnObjects ? $nonQuestions[$value['id']] = QuestionFactory :: factory($value) : $nonQuestions[$value['id']] = $value;
        }

        return $nonQuestions;
    }

    /**
     * Get skill gap test questions
     *
     * This function returns a list with all skill gap tests questions.
     * <br/>Example:
     * <code>
     * $questions = $this -> getQuestions();
     * </code>
     *
     * @return array An array of questions
     * @since 3.5.0
     * @access public
   
    public function getQuestions($returnObjects = false) {
    
$testQuestions = $currentTest -> getSkillgapQuestions();
                $result = eF_getTableData("tests_to_questions tq, questions q", "q.*, tq.weight, tq.previous_question_ID", "q.type <> 'raw_type' AND tq.questions_ID=q.id and tq.tests_ID=".$currentTest -> test['id']);
	            if (sizeof($result) > 0) {
	                foreach ($result as $value) {
	                    $value['type_icon']      = Question :: $questionTypesIcons[$value['type']];
	                    $questions[$value['id']] = $value;
	                    $testQuestions[$value['previous_question_ID']] = $value;
	                }
                }    
      */
    /**
     * Assign questions to test
     *
     * This function is used to add questions to the current
     * test. The $questions array has question ids as keys
     * and question weights as values.
     * <br/>Example:
     * <code>
     * $questions = array(54 => 5, 62 => 1, 76 => 1, 85 => 10);         //question with id 54 will have weight 5, id 62 will have weight 1 etc
     * $test -> addQuestions($questions);
     * </code>
     *
     * @param $questions The questions list
     * @return array The new questions list for the test
     * @since 3.5.0
     * @access public
     */
    public function addQuestions($questions) {
        $testQuestions    = $this -> getQuestions();
        $nonTestQuestions = $this -> getNonQuestions();

        //getQuestions returns sorted questions
        if (sizeof($testQuestions) > 0) {
            $lastQuestion = end($testQuestions);
            $previousId   = $lastQuestion['id'];
        } else {
            $previousId = 0;
        }
        foreach ($questions as $id => $weight) {
            $fields = array("tests_ID"             => $this -> test['id'],
                            "questions_ID"         => $id,
                            "weight"               => $weight && is_numeric($weight) ? $weight : 1);
            if (!in_array($id, array_keys($testQuestions))) {        //We are adding a new question
                $fields["previous_question_ID"] = $previousId;
                eF_insertTableData("tests_to_questions", $fields);
                $previousId = $id;
            } else {                                                //We are changing a question's weight
                eF_updateTableData("tests_to_questions", $fields, "tests_ID=".$this -> test['id']." and questions_ID=".$id);
            }
        }
        //In order to refresh questions
        $this -> questions = false;
        return $this -> getQuestions();
    }

    /**
     * Remove questions from test
     *
     * This function is used to remove questions from the test
     * The question ids are specified in the $questionIds array.
     * If this parameter is ommited, all questions are deleted.
     * <br/>Example:
     * <code>
     * $questionIds = array(54, 2, 7);
     * $test -> $removeQuestions($questionIds);                         //Remove questions with ids 54, 2 and 7 from test
     * $test -> $removeQuestions();                                     //Remove all questions from test
     * </code>
     *
     * @param array $questionIds The question ids
     * @return array The new questions list for the test
     * @since 3.5.0
     * @access public
     */
    public function removeQuestions($questionIds = false) {
        if ($questionIds === false) {
            eF_deleteTableData("tests_to_questions", "tests_ID = ".$this -> test['id']);
            $this -> questions = false;                //Reset questions information
            return array();
        } else {
            $testQuestions = $this -> getQuestions();
            foreach ($questionIds as $id) {
                if (in_array($id, array_keys($testQuestions))) {
                    $previousQuestion = $testQuestions[$id]['previous_question_ID'];
                    eF_deleteTableData("tests_to_questions", "tests_ID = ".$this -> test['id']." and questions_ID=$id");
                    eF_updateTableData("tests_to_questions", array("previous_question_ID" => $previousQuestion), "tests_ID = ".$this -> test['id']." and previous_question_ID=".$id);
                }
            }
            $this -> questions = false;                //Reset questions information
            return $this -> getQuestions();            //Return new questions list
        }
    }

    /**
     * Get the content unit corresponding to this test
     * <br/>Example:
     * <code>
     * $unit = $test -> getUnit();
     * </code>
     *
     * @return EfrontUnit The unit of this test
     * @since 3.5.0
     * @access public
     */
    public function getUnit() {
        if ($this -> unit === false && $this -> test['lessons_ID']) {
            $this -> unit = new EfrontUnit($this -> test['content_ID']);
        }
        return $this -> unit;
    }

    /**
     * Return this test's lesson
     *
     * This function returns the lesson that the current test
     * belongs to.
     * <br/>Example:
     * <code>
     * $test -> getLesson();            //returns something like array(3 => 'A lesson')
     * $test -> getLesson(true);        //returns the EfrontLesson object
     * </code>
     *
     * @param $returnObjects Whether to return a simple id => name array, or the full lesson object
     * @return mixed Either an array with an $id => $name pair, or an EfrontLesson object
     * @since 3.5.0
     * @access public
     */
    public function getLesson($returnObjects = false) {
        $result = eF_getTableData("lessons, content", "lessons.id, lessons.name", "lessons.id=content.lessons_ID and content.id=".$this -> test['content_ID']);

        if ($result[0]['id']) {
            $this -> lesson = array($result[0]['id'] =>  $result[0]['name']);
        } else {
            $this -> lesson = array();
        }

        if ($returnObjects) {
            $lesson = new EfrontLesson($result[0]['id']);
        } else {
            $lesson = $this -> lesson;
        }

        return $lesson;
    }

    /**
     * Create test
     *
     * This function is used to create a new test.
     * In order to create the test, it firsts creates
     * a unit to hold it.
     * <br/>Example:
     * <code>
     * $contentFields = new array('name' => 'new unit');
     * $testFields    = new array('duration' => 100);
     * $test = EfrontTest :: createTest($contentFields, $testFields);
     * </code>
     *
     * @param mixed $content The content unit fields or an existing unitObject. If it's false, then the test will not be associated to a unit
     * @param array $test The test fields
     * @return EfrontTest the new test object
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function createTest($content, $test) {
        if ($content === false) {
            $test['content_ID'] = 0;
        } elseif (! ($content instanceof EfrontUnit)){
            $unit = EfrontUnit :: createUnit($content);
            $test['content_ID'] = $unit['id'];
        } else {
            $unit = $content;
            $test['content_ID'] = $unit['id'];
        }

        unset($test['id']);
        if ($newId = eF_insertTableData("tests", $test)) {
            return new EfrontTest($newId);
        } else {
            return false;
        }
    }

    /**
     * Get all automatically assigned skillgap tests
     *
     * This function is used to returned the skillgap tests
     * that are defined to be assigned automatically to each new user
     *
     * <br/>Example:
     * <code>
     * $tests = EfrontTest :: getAutoAssignedTests();
     * foreach ($tests as $test) {
     *      eF_insertTableData("users_to_skillgap_tests", array("users_LOGIN" => $user -> user['login'], "tests_ID" => $test['id']));
     * }
     * </code>
     * @return array with tests
     * @since 3.5.2
     * @access public
     * @static
     */
    public static function getAutoAssignedTests() {
        // Skillgap tests have lessons_ID equal to zero by default
        $all_skillgaps = eF_getTableData("tests", "id, options", "lessons_ID = 0");
        $auto_assigned = array();
        foreach ($all_skillgaps as $skillgap) {
            $options = unserialize($skillgap['options']);
            if ($options['assign_to_new']) {
                $auto_assigned[] = $skillgap['id'];
            }
        }
        return $auto_assigned;
    }


    /**
     * Get the question weight
     *
     * This function returns the weighted factor for the specified
     * question in this test context. For example, If there are 4
     * questions in the test, all with the same weight (for example 1),
     * then the function retunrs 0.25
     * <br/>Example:
     * <code>
     * $test -> getQuestionWeight(6);               //Rreturns the weight factor for this question, which is a number between 0 and 1 (excluding 0)
     * </code>
     *
     * @param int $questionId The question id
     * @return float The question weight
     * @since 3.5.0
     * @access public
     */
    public function getQuestionWeight($questionId) {
       $testQuestions = $this -> getQuestions();
       if (!in_array($questionId, array_keys($testQuestions))) {
           throw new EfrontTestException(_INVALIDID.': '.$questionId, EfrontTestException :: INVALID_ID);
       }

       foreach ($testQuestions as $id => $question) {
           $weights[$id] = $question['weight'];
       }

       $questionWeight = $weights[$questionId] / array_sum($weights);
       return $questionWeight;
    }

    /**
     * Get the absolute question weight
     *
     * This function returns the absolute (integer) factor for the specified
     * question in this test context.
     * <br/>Example:
     * <code>
     * $test -> getAbsoluteQuestionWeight(6);               //Rreturns the weight factor for this question, which is a number between 0 and 1 (excluding 0)
     * </code>
     *
     * @param int $questionId The question id
     * @return int The question weight
     * @since 3.5.0
     * @access public
     */
    public function getAbsoluteQuestionWeight($questionId) {
       $testQuestions = $this -> getQuestions();
       if (!in_array($questionId, array_keys($testQuestions))) {
           throw new EfrontTestException(_INVALIDID.': '.$questionId, EfrontTestException :: INVALID_ID);
       }

       foreach ($testQuestions as $id => $question) {
           $weights[$id] = $question['weight'];
       }

       $questionWeight = $weights[$questionId];
       return $questionWeight;
    }

    /**
     * Set test done information
     *
     * If this test is done by some student, say 'jdoe', using this function
     * the relevant information is retrieved and stored at the $doneInfo class
     * member.
     * <br/>Example:
     * <code>
     * $test     = new EfrontTest(1);                   //Instantiate test object
     * $doneInfo = $test -> setDone('jdoe');            //Retrieve the done test information for user 'jdoe';
     * </code>
     *
     * @param mixed $user The user login to get information for, or an EfrontUser object
     * @return array The done information
     * @since 3.5.0
     * @access public
     * @deprecated
     */
    public function setDone($user) {
        if ($user instanceof EfrontUser) {
            $login = $user -> user['login'];
        } elseif (!eF_checkParameter($user, 'login')) {
            throw new EfrontTestException(_INVALIDLOGIN.': '.$user, EfrontTestException :: INVALID_LOGIN);
        } else {
            $login = $user;
        }

        $result = eF_getTableData("done_tests dt, users_to_done_tests udt, users u", "dt.*, u.name as user_name, u.surname as user_surname, udt.times, udt.answers_order, udt.questions_order", "u.login = udt.users_LOGIN and udt.users_LOGIN=dt.users_LOGIN and dt.users_LOGIN = '$login' and dt.tests_ID = udt.tests_ID and dt.tests_ID=".$this -> test['id']."");

        if (sizeof($result) > 0) {                  //Get the done information for this test
            $this -> doneInfo = $result[0];
            $this -> doneInfo['score'] = round(100 * ($this -> doneInfo['score']), 2) / 100;
            unserialize($this -> doneInfo['answers_order'])   !== false ? $this -> doneInfo['answers_order']   = unserialize($this -> doneInfo['answers_order'])   : null;
            unserialize($this -> doneInfo['questions_order']) !== false ? $this -> doneInfo['questions_order'] = unserialize($this -> doneInfo['questions_order']) : null;

            $result         = eF_getTableDataFlat("done_questions", "distinct questions_ID", "score = -1 and done_tests_ID=".$this -> doneInfo['id']);
            if (sizeof($result) > 0) {
                foreach ($result['questions_ID'] as $id) {
                    $potentialScore += $this -> getQuestionWeight($id);
                }
                $this -> doneInfo['potential_score'] = round(100 * ($this -> doneInfo['score'] + $potentialScore), 2) / 100;
            }
        } else {                                    //Otherwise, just find out how many times the user has done this test, which is an information kept always (even if a test is reset)
            $result = eF_getTableData("users_to_done_tests", "times", "users_LOGIN = '$login' and tests_ID=".$this -> test['id']);
            if (sizeof($result) > 0) {
                $this -> doneInfo = $result[0];
            } else {
                throw new EfrontTestException(_USERHASNOTDONETEST.': '.$login, EfrontTestException :: NOT_DONE_TEST);
            }
        }
        return $this -> doneInfo;
    }
    /**
     * Reset done test information
     *
     * This function is used to reset the done information for the specified
     * user.
     * <br/>Example:
     * <code>
     * $test -> redo('jdoe');                           //Reset test information for user jdoe
     * </code>
     *
     * @param mixed $user The user to reset test for, either a user login or an EfrontUser instance
     * @since 3.5.2
     * @access public
     */
    public function redo($user) {
        if ($user instanceof EfrontUser) {
            $login = $user -> user['login'];
        } elseif (eF_checkParameter($user, 'login')) {
            $login = $user;
        } else {
            throw new EfrontTestException(_INVALIDLOGIN.': '.$user, EfrontTestException :: INVALID_LOGIN);
        }
        if (is_dir(G_UPLOADPATH.$login.'/tests/'.$this -> test['id'])) {
            try {
                $directory  = new EfrontDirectory(G_UPLOADPATH.$login.'/tests/'.$this -> test['id'].'/');
                $directory -> rename(G_UPLOADPATH.$login.'/tests/completed_'.$this -> completedTest['id'].'/');
            } catch (EfrontFileException $e) {}
        }
        //Set the unit as "not seen"
        if (!($user instanceof EfrontUser)) {
            $user = EfrontUserFactory :: factory($login, false, 'student');
        }
        $user -> setSeenUnit($this -> test['content_ID'], key($this -> getLesson()), 0);
        eF_updateTableData("completed_tests", array("archive" => 1), "tests_ID=".($this -> test['id'])." and users_LOGIN='".$login."'");
    }
    
    /**
     * Delete done test information
     *
     * This function is used to delete the done information for the specified
     * user.
     * <br/>Example:
     * <code>
     * $test -> undo('jdoe');                           //Delete test information for user jdoe
     * </code>
     *
     * @param mixed $user The user to delete test for, either a user login or an EfrontUser instance
     * @since 3.5.2
     * @access public
     */
    public function undo($user) {
        if ($user instanceof EfrontUser) {
            $login = $user -> user['login'];
        } elseif (eF_checkParameter($user, 'login')) {
            $login = $user;
        } else {
            throw new EfrontTestException(_INVALIDLOGIN.': '.$user, EfrontTestException :: INVALID_LOGIN);
        }
        if (is_dir(G_UPLOADPATH.$login.'/tests/'.$this -> test['id'])) {
            try {
                $directory  = new EfrontDirectory(G_UPLOADPATH.$login.'/tests/'.$this -> test['id'].'/');
                $directory -> delete();
            } catch (EfrontFileException $e) {}
        }
        //Set the unit as "not seen"
        if (!($user instanceof EfrontUser)) {
            $user = EfrontUserFactory :: factory($login, false, 'student');
        }
        $user -> setSeenUnit($this -> test['content_ID'], key($this -> getLesson()), 0);
        eF_deleteTableData("completed_tests", "users_LOGIN='".$login."' and tests_ID=".$this -> test['id']);
    }    

    /**
     * Order test questions
     *
     * This function is used to order randomly (Shuffle) the test questions and return the
     * order used. If the ordering array is provided, then the questions are
     * rearranged based on the specified order.
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(1);                                       //Instantiate test form
     * $questionsOrder = $test -> orderQuestions();                     //Shuffle the test questions and return order
     * $questionsOrder = $test -> orderQuestions($order);               //Rearrange test questions based on $order, which is an array of question ids
     * </code>
     *
     * @param array $order The order to apply to the questions. If omitted, the questions are ordered randomly
     * @return array The order applied (useful only when $order is ommitted)
     * @since 3.5.0
     * @access protected
     */
    protected function orderQuestions($order = false) {
        if (!$order) {
            $order = array_keys($this -> questions);
            shuffle($order);
        }

        foreach ($order as $value) {
            $temp[$value] = $this -> questions[$value];
        }
        $this -> questions      = $temp;
        $this -> questionsOrder = array_keys($this -> questions);

        return $this -> questionsOrder;
    }

    /**
     * Start test
     *
     * This function is used to start a test. It gathers all the required information, creates
     * a new EfrontTest instance and stores it in the database.
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(23);              //Instantiate object for test with id 23
     * $testInstance = $test -> start('jdoe);   //start() returns a new EfrontTest object, different that the initial, for the user 'jdoe'
     * $testInstance -> ...                     //Do whatever with the new object.
     * </code>
     *
     * @param string $login The user to start test for
     * @return EfrontCompletedTest a new EfrontCompletedTest instance, which represents the test (to be) completed
     * @since 3.5.2
     * @access public
     */
    public function start($login) {
        $completedTest                             = new EfrontCompletedTest($this, $login);
        $completedTest -> time['start']            = time();                                    //The time that this test has started
        $completedTest -> time['resume']           = time();                                    //Initialize time that this test has 'resumed'
        $completedTest -> time['spent']            = 0;                                         //Initialize the time spent
        $completedTest -> completedTest['status']  = 'incomplete';                              //The test just started; So set its status to 'incomplete'
//        $completedTest -> completedTest['archive'] = '0';                              //The test just started; So set its status to 'incomplete'
        $testQuestions  = $this -> getQuestions(true);

        //1. Get the random pool questions
        if ($this -> options['random_pool']) {
            sizeof($testQuestions) >= $this -> options['random_pool'] ? $poolSize = $this -> options['random_pool'] : $poolSize = sizeof($testQuestions);
            shuffle($testQuestions);
            $testQuestions = array_slice($testQuestions, 0, $poolSize);
            $temp          = array();
            foreach ($testQuestions as $value) {                        //Shuffling reindexed array, so we need to put back the correct keys
                $temp[$value -> question['id']] = $value;
            }
            $completedTest -> questions = $temp;
        } else {
            $completedTest -> questions = $testQuestions;
        }

        //2. Shuffle answers inside questions
        foreach ($completedTest -> questions as $key => $question) {
            if ($this -> options['shuffle_answers']) {
                $question      -> shuffle();
            }
            $completedTest -> questions[$key] = $question;
        }

        //3. Set questions in order
        if ($this -> options['shuffle_answers']) {
            $completedTest -> orderQuestions();
        }

        //4. Get additional information that might be useful
        $completedTest -> getUnit();
        $completedTest -> getLesson();

        //5. Store test
        $completedTest -> save();

        return $completedTest;
    }

    /**
     * Get test status for user
     *
     * This function is used to get the user's status in the current test.
     * The status consists of an array with the following fields:
     * <br/> 'status': Can be 'completed', 'incomplete', 'passed', 'failed' or '' (empty)
     * <br/> 'timesDone': The number of times the user has done this test
     * <br/> 'timesLeft': The number of times the user can do the test. If there is no such restriction, this is false
     * <br/> 'lastTest': The id of the last test, that is non-archived (and thus can be directly previewed, and sets the user to have it 'done'
     * <br/> 'completedTest': The database result, containing all the completed test information
     * <br/> 'testIds': An array with the ids of the done instances for this test
     * <br/> 'timestamps': An array with the timestamps that the done instances for this test ended on
     * <br/>Example:
     * </code>
     * $test = new EfrontTest(23);
     * $status = $test -> getStatus('jdoe');
     * if ($status['status'] == 'passed') {
     *     $completedTest = unserialize($status['completedTest']['test']);
     * }
     * </code>
     *
     * @param mixed $user The user that the status is requested for, can be an EfrontUser object, or a string with the login
     * @param int $id Get the status for the completed test with the specified id
     * @param boolean $onlySolved If set, only solved tests are considered
     * @return array The status of the user in the test
     * @since 3.5.2
     * @access public
     */
    public function getStatus($user, $id = false, $onlySolved = false) {
        if ($user instanceof EfrontUser) {
            $login = $user -> user['login'];
        } elseif (!eF_checkParameter($user, 'login')) {
            throw new EfrontTestException(_INVALIDLOGIN.': '.$user, EfrontTestException :: INVALID_LOGIN);
        } else {
            $login = $user;
        }

        if ($onlySolved) {
            $result = eF_getTableData("completed_tests", "*", "status != '' and status != 'incomplete' and users_LOGIN = '$login' and tests_ID=".($this -> test['id']));
        } else {
            $result = eF_getTableData("completed_tests", "*", "users_LOGIN = '$login' and tests_ID=".($this -> test['id']));
        }
        $timesDone     = sizeof($result);
        $status        = '';
        $lastTest      = false;
        $completedTest = '';
        $testIds       = array();
        $timestamps    = array();
        foreach ($result as $value) {
            if (!$id && $value['archive'] == 0) {
                $status        = $value['status'];
                $completedTest = $value;
            } elseif ($id == $value['id']) {
                $status        = $value['status'];
                $completedTest = $value;
            }
            if ($value['archive'] == 0) {
                $lastTest      = $value['id'];
            }
            $testIds[]    = $value['id'];
            $timestamps[] = $value['timestamp'];
        }
        if ($this -> options['redoable']) {
            $timesLeft = $this -> options['redoable'] - $timesDone;
        } else {
            $timesLeft = false;
        }

        $status = array('status'        => $status,
                        'timesDone'     => $timesDone,
                        'timesLeft'     => $timesLeft,
                        'lastTest'      => $lastTest,
                        'completedTest' => $completedTest,
                        'testIds'       => $testIds,
                        'timestamps'    => $timestamps);

        return $status;
    }

    /**
     * Populate the test form
     *
     * This function is used to populate the test form and create the
     * test html code.
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(1);                               //Instantiate test form
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create the test form
     * echo $test -> toHTMLQuickForm($form);                    //Populates the form and returns the equivalent HTML code
     * echo $test -> toHTMLQuickForm($form, 2);                 //Populates the form and returns the equivalent HTML code, but displays only question with id 2
     * $test -> setDone('jdoe');                                //Get the done test information for user 'jdoe';
     * echo $test -> toHTMLQuickForm($form, false, true);       //Populates the form and returns the equivalent HTML code, but the mode is set to display the done test
     * </code>
     *
     * @param HTML_QuickForm $form The form to populate
     * @param int $questionId If set, it displays only the designated question
     * @param boolean $done If set to true and the test has done information (previously acquired with setDone()), then it displays the done test
     * @param boolean $editHandles Whether to display correction handles, to update questions scores and feedback
     * @since 3.5.0
     * @access public
     */
    public function toHTMLQuickForm(& $form = false, $questionId = false, $done = false, $editHandles = false) {
        $this -> getQuestions();                                                                //Initialize questions information, it case it isn't
        if (!$form) {
            $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);     //Create a sample form
        }

        $allTestQuestions = $this -> getQuestions(true);
        
        // If we have a random pool of question then get a random sub-array of the questions
        if ($this -> options['random_pool'] > 0) {
            $rand_questions = array_rand($allTestQuestions, $this -> options['random_pool']);
            $testQuestions = array();
            foreach ($rand_questions as $question) {
                $testQuestions[$question] = $allTestQuestions[$question];
            }
        } else {
            $testQuestions = $allTestQuestions;
        }
        
        $questionId && in_array($questionId, array_keys($testQuestions)) ? $testQuestions = $testQuestions[$questionId] : null;    //If $questionId is specified, keep only this question

        $this -> options['display_list'] ? $testString = '<style type = "text/css">span.orderedList{float:left;}</style>' : $testString = '<style type = "text/css">span.orderedList{display:none;}</style>'; 
        $count      = 1;
        
        if ($this -> test['content_ID']) {
            //Get unit names and ids
            $content = new EfrontContentTree(key($this -> getLesson()));
            foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
                $units[$key] = $value['name'];
            }
        }
		$currentLesson  = new EfrontLesson($_SESSION['s_lessons_ID']);
        foreach ($testQuestions as $id => $question) {            
            if ($done) {
                switch ($question -> score) {
                    case ''    :
                    case 0     : $image = 'delete.png'; $alt = _INCORRECTQUESTION;        $title = _INCORRECTQUESTION;        break;
                    case '100' : $image = 'checks.png'; $alt = _QUESTIONISCORRECT;        $title = _QUESTIONISCORRECT;        break;
                    default    : $image = 'check.png';  $alt = _PARTIALLYCORRECTQUESTION; $title = _PARTIALLYCORRECTQUESTION; break;
                }
                if ($question -> pending) {
                    $image = 'information.png';
                    $alt   = _CORRECTIONPENDING;
                    $title = _CORRECTIONPENDING;
                }
            }

            $weight = round(100 * $this -> getQuestionWeight($question -> question['id'])) / 100;

			//echo $currentLesson -> options['content_report'];
            $testString .= '
                    <div id = "question_'.$count.'" onclick = "checkedQuestions['.($count - 1).'] = 1;" style = "'.(!$done && $this -> options['onebyone'] ? 'display:none' : '').'">
                    <table width = "100%">
                        <tr><td class = "questionWeight" style = "vertical-align:middle;">
                                <img src = "images/16x16/'.($done ? $image : 'text.png').'" style = "vertical-align:middle" alt = "'.($done ? $alt : _QUESTION).'" title = "'.($done ? $title : _QUESTION).'"/>&nbsp;
                                <span style = "vertical-align:middle;font-weight:bold">'._QUESTION.'&nbsp;'. ($count++).'</span>
                                '.($this -> options['display_weights'] || $done ? '<span style = "vertical-align:middle;margin-left:10px">('._WEIGHT.'&nbsp;'.$weight.')</span>' : '').'
                                '.($units[$question -> question['content_ID']] && $done ? '<span style = "vertical-align:middle;margin-left:10px">'._UNIT.' "'.$units[$question -> question['content_ID']].'"</span>' : '').'
								'.(($_SESSION['s_type'] == "student" && $currentLesson -> options['content_report'] == 1)? '<a href = "content_report.php?ctg=tests&edit_question='.$question -> question['id'].'&question_type='.$question -> question['type'].'&lessons_Id='.$_SESSION['s_lessons_ID'].'" onclick = "eF_js_showDivPopup(\''._CONTENTREPORT.'\', new Array(\'500px\',\'300px\'))" target = "POPUP_FRAME"><img src = "images/16x16/warning.png" border=0 style = "vertical-align:middle" alt = "'._CONTENTREPORT.'" title = "'._CONTENTREPORT.'"/></a>' : '').'
							</td></tr>
                    </table>'.($done ? $question -> toHTMLSolved($form, $this -> options['answers'], $this -> options['given_answers']) : $question -> toHTML($form)).'<br/></div>';            
            if ($done) {
                    $testString .= '
                        <table style = "width:100%" >
                            <tr><td>
                                <span style = "font-weight:bold" id = "question_'.$id.'_score_span">
                                    '._SCORE.': <span style = "vertical-align:middle" id = "question_'.$id.'_score">'.$question -> score.'%</span>
                                    '.($editHandles ? '<a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_score_span\').hide();$(\'edit_question_'.$id.'_score_span\').show();"><img src = "images/16x16/edit.png" title = "'._CHANGESCORE.'" alt = "'._CHANGESCORE.'"  style = "vertical-align:middle" border = "0"/></a>' : '').'
                                    <span id = "question_'.$id.'_pending">'.($question -> pending ? '&nbsp;('._THISQUESTIONCORRECTEDPROFESSOR.')' : '').'</span>
                                </span>
                                <span id = "edit_question_'.$id.'_score_span" style = "display:none">
                                    <input type = "text" name = "edit_question_'.$id.'_score" id = "edit_question_'.$id.'_score" value = "'.$question -> score.'" style = "vertical-align:middle"/>
                                    <a href = "javascript:void(0)" onclick = "editQuestionScore(this, '.$id.')">
                                        <img src = "images/16x16/check2.png" alt = "'._SUBMIT.'" title = "'._SUBMIT.'" border = "0"  style = "vertical-align:middle"/>
                                    </a>
                                    <a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_score_span\').show();$(\'edit_question_'.$id.'_score_span\').hide();">
                                        <img src = "images/16x16/delete2.png" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0"  style = "vertical-align:middle"/>
                                    </a>
                                </span>
                                <br/>'._SCOREINTEST.': <span id = "question_'.$id.'_score_coefficient">'.$question -> score.'</span>% &#215; '.$weight.' = <span id = "question_'.$id.'_scoreInTest">'.$question -> scoreInTest.'</span>%
                            </td></tr>';
                    if ($editHandles) {
                        $testString .= '
                            <tr><td>';
                        if ($question -> feedback) {
                            $testString .= '
                                            <img src = "images/16x16/edit.png" alt = "'._EDITFEEDBACK.'" title = "'._EDITFEEDBACK.'" border = "0" style = "vertical-align:middle">
                                            <a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_feedback_div\').toggle();$(\'edit_question_'.$id.'_feedback_div\').toggle()">'._EDITFEEDBACK.'</a>';
                        } else {
                            $testString .= '
                                            <img src = "images/16x16/add2.png" alt = "'._ADDFEEDBACK.'" title = "'._ADDFEEDBACK.'" border = "0" style = "vertical-align:middle">
                                            <a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_feedback_div\').toggle();$(\'edit_question_'.$id.'_feedback_div\').toggle()">'._ADDFEEDBACK.'</a>';
                        }

                        $testString .= '
                                    <div id = "question_'.$id.'_feedback_div" '.($question -> feedback ? 'class = "feedback"' : '').' >
                                        <span id = "question_'.$id.'_feedback">'.$question -> feedback.'</span>
                                    </div>
                                    <div id = "edit_question_'.$id.'_feedback_div" style = "display:none;">
                                        <textarea id = "edit_question_'.$id.'_feedback" style = "vertical-align:middle;width:90%;height:50px">'.$question -> feedback.'</textarea>
                                        <a href = "javascript:void(0)" onclick = "editQuestionFeedback(this, '.$id.')" style = "vertical-align:middle">
                                            <img src = "images/16x16/check2.png" alt = "'._SUBMIT.'" title = "'._SUBMIT.'" border = "0" style = "vertical-align:middle" />
                                        </a>
                                        <a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_feedback_div\').toggle();$(\'edit_question_'.$id.'_feedback_div\').toggle()">
                                            <img src = "images/16x16/delete2.png" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0" style = "vertical-align:middle" />
                                        </a>
                                    </div>
                            </td></tr>';
                    } else {
                        $testString .= '
                                    <div id = "question_'.$id.'_feedback_div" '.($question -> feedback ? 'class = "feedback"' : '').' >
                                        <span id = "question_'.$id.'_feedback">'.$question -> feedback.'</span>
                                    </div>';
                    }
                    $testString .= '
                            </table><br/>';

            }            
        }
        
        if (!$done && $this -> options['onebyone']) {
            $testString .= '
                        <table width = "100%">
                            <tr><td style = "text-align:center;vertical-align:middle;padding-top:50px">
                                <img src = "images/24x24/arrow_left_blue.png"  alt = "'._PREVIOUSQUESTION.'" title = "'._PREVIOUSQUESTION.'" border = "0" id = "previous_question_button" onclick = "showTestQuestion(\'previous\')" style = "vertical-align:middle;margin-right:10px"/>
                                <select id = "goto_question" name = "goto_question" style = "vertical-align:middle" onchange = "showTestQuestion(this.options[this.selectedIndex].value)">';
            for ($i = 1; $i <= sizeof($testQuestions); $i++) {
                $testString .= '
                                    <option value = "'.$i.'">'.$i.'</option>';
            }
            $testString .= '
                                </select>&nbsp;
                                <img src = "images/24x24/arrow_right_blue.png" alt = "'._NEXTQUESTION.'" title = "'._NEXTQUESTION.'" border = "0" id = "next_question_button" onclick = "showTestQuestion(\'next\')" style = "vertical-align:middle"/>
                            </td></tr>
                        </table>';

            $testString .= "

                        <script>
                        <!--
                            var total_questions  = ".sizeof($testQuestions).";
                            var current_question = ".($this -> currentQuestion ? $this -> currentQuestion : 1).";
                            showTestQuestion(current_question);

                            function showTestQuestion(question_num) {
                                if (question_num == 'next') {
                                    current_question < total_questions ? question_num = parseInt(current_question) + 1 : question_num = current_question;
                                } else if (question_num == 'previous') {
                                    current_question > 1 ? question_num = parseInt(current_question) - 1 : question_num = current_question;
                                }

                                $('question_' + current_question).hide();
                                $('question_' + question_num).show();
                                current_question = question_num;
                                current_question <= 1               ? $('previous_question_button').hide() : $('previous_question_button').show();
                                current_question >= total_questions ? $('next_question_button').hide()     : $('next_question_button').show();
                                $('goto_question').selectedIndex = current_question - 1;
                            }
                        //-->
                        </script>";
        }

        $testString .= "
                        <script>
                        <!--
                            var checkedQuestions = new Array(".sizeof($testQuestions).");";
        $count = 0;
        foreach ($this -> questions as $question) {
            if ($question -> userAnswer !== false) {
                $testString .= "checkedQuestions[".$count++."] = 1;";
            }
        }
        $testString .= "
                            function checkQuestions() {
                                var unfinished = new Array();
                                var sum = 0;
                                for (var i = 0; i < checkedQuestions.length; i++) {
                                    if (checkedQuestions[i]) {
                                        sum++;
                                    } else {
                                        unfinished.push(i + 1);
                                    }
                                }
                                if (sum < checkedQuestions.length) {
                                    return unfinished;
                                } else {
                                    return false;
                                }
                            }
                        //-->
                        </script>";

        if ($this -> options['shuffle_questions'] && !$form -> isSubmitted()) {
            $form -> addElement("hidden", "answers_order", serialize($shuffleOrder));       //The questions' answers order is hold at a hidden element, so that it can be stored when the test is complete
        }

        return $testString;
    }

    /**
     * Display unsolved HTML version of test
     *
     * This function is used to display the HTML version of the unsolved test, along
     * with the cuont-down timer and the test  header.
     * <br/>Example:
     * <code>
     * $showTest = new EfrontTest(43, true);
     * echo $showTest -> toHTML(false, $showTest -> toHTMLQuickForm())
     * </code>
     *
     *
     * @param string $testString The test core, consisting of the questions section
     * @param int $remainingTime the time remaining, that is used to display the count-down timer
     * @param boolean $freeze Whether to freeze the timer
     * @return string The HTML code of the unsolved test
     * @since 3.5.2
     * @access public
     */
    public function toHTML($testString, $remainingTime = false, $freeze = false) {
        if (!$this -> options['duration']) {
            $str = '
            <table class = "doneTestHeader">
                <tr><td id = "doneTestImage">
                        <img src = "images/48x48/desktop.png" title = "'._TEST.'" alt = "'._TEST.'" />
                    </td>
                    <td>
                        <table class = "doneTestInfo">
                            <tr><td id = "testName">'.$this -> test['name'].'</td></tr>
                            <tr><td>'.$this -> test['decription'].'</td></tr>
                        </table>
                    </td></tr>
            </table>';
        } else {
            if (!$remainingTime) {
                $remainingTime = eF_convertIntervalToTime($this -> options['duration']);
            } else {
                $remainingTime = eF_convertIntervalToTime($remainingTime);
            }
            $duration        = eF_convertIntervalToTime($this -> options['duration']);
            $durationString .= _TESTSHOULDCOMPLETEIN.' ';
            $duration['hours']   ? $durationString .= $duration['hours'].'h '             : null;
            $duration['minutes'] ? $durationString .= $duration['minutes'].'&#039; '      : null;
            $duration['seconds'] ? $durationString .= $duration['seconds'].'&#039;&#039;' : null;
            $durationString .= '.';

            $str = '
                <table class = "doneTestHeader">
                    <tr><td id = "doneTestImage">
                            <img src = "images/48x48/desktop.png" title = "'._TEST.'" alt = "'._TEST.'" />
                        </td>
                        <td>
                            <table class = "doneTestInfo">
                                <tr><td id = "testName">'.$this -> test['name'].'</td></tr>
                                <tr><td>'.$durationString.'</td>
                                <tr><td>'.$this -> test['decription'].'</td></tr>
                            </table>
                        </td>
                        <td id = "timer">
                            <img src = "images/48x48/stopwatch.png" title = "'._TIMELEFT.'" alt = "'._TIMELEFT.'"/>&nbsp;
                            <span id = "time_left"></span>
                        </td></tr>
                    </tr>
                </table>
                <script language = "JavaScript" type = "text/javascript">
                    var hours   = "'.$remainingTime['hours'].'";
                    var minutes = "'.$remainingTime['minutes'].'";
                    var seconds = "'.$remainingTime['seconds'].'";
                ';
            if ($freeze) {
                $str .= '
                            min = minutes.toString();
                            sec = seconds.toString()
                            if (min.length == 1) {min = "0" + min;}
                            if (sec.length == 1) {sec = "0" + sec;}
                            $("time_left").update(hours + ":" + min + ":" + sec);';
            } else {
                $str .= '
                    var min     = new String(3);
                    var sec     = new String(3);
                    function eF_js_printTimer() {
                        if (hours == 0 && minutes == 0 && seconds == 0) {
                            document.test_form.submit();
                            alert("'._YOURTIMEISUP.'!");
                        } else {
                            if (seconds >= 1) {seconds--;}
                            else {
                                if (seconds == 0 ) {seconds = 59;}
                                if (minutes >= 1)  {minutes--;}
                                else {
                                    if (minutes == 0) {minutes = 59;}
                                    if (hours >= 1)   {hours--;}
                                    else              {hours = 0;}
                                }
                            }
                            min = minutes.toString();
                            sec = seconds.toString()
                            if (min.length == 1) {min = "0" + min;}
                            if (sec.length == 1) {sec = "0" + sec;}

                            $("time_left").update(hours + ":" + min + ":" + sec);
                            setTimeout("eF_js_printTimer()", 1000);
                        }
                    }
                    eF_js_printTimer();';
            }
        }
        $str .= '
                </script>
                <table class = "formElements" style = "width:100%">
                    <tr><td colspan = "2">'.$testString.'</td></tr>
                </table>';
        return $str;

    }

    /**
     * Upgrade data
     *
     * This function is used to upgrade the class data from one version to another
     * <br/>Example:
     * <code>
     * EfrontTest :: upgrade('3.5.1');          //Upgrade from version 3.5.1 to the current
     * </code>
     *
     * @param string $from The version to upgrade from
     * @since 3.5.2
     * @access public
     * @static
     */
    public static function upgrade($from) {
        if (version_compare($from, '3.5.1') <= 0) {

            //Update questions from 3.5.1 and below to include correct lessons_ID
            $questions = eF_getTableData("questions", "*");
            if (sizeof($questions) > 0) {
                $result          = eF_getTableDataFlat("content", "id, lessons_ID");
                $contentToLesson = array_combine($result['id'], $result['lessons_ID']);
                foreach ($questions as $value) {
                    $fields['lessons_ID'] = $contentToLesson[$value['content_ID']];
                    if ($fields['lessons_ID']) {
                        eF_updateTableData("questions", $fields, "id=".$value['id']);
                    }
                }
            }

            $result = eF_getTableData("done_tests", "*");
            foreach ($result as $value) {
                $doneTests[$value['id']] = $value;
            }
            $result = eF_getTableData("users_to_done_tests udt, done_tests dt", "udt.*, dt.id as done_tests_ID", "dt.users_LOGIN=udt.users_LOGIN and dt.tests_ID=udt.tests_ID");
            foreach ($result as $value) {
                $usersDoneTests[$value['done_tests_ID']] = $value;
            }
            //pr($usersDoneTests);
            $result = eF_getTableData("done_questions", "*");
            foreach ($result as $value) {
                $doneQuestions[$value['done_tests_ID']][$value['questions_ID']] = $value;
            }

            foreach ($doneTests as $id => $doneTest) {
                $test = new EfrontTest($doneTest['tests_ID']);
                $test = new EfrontCompletedTest($test, $test -> login);
                $test -> questions = $test -> getQuestions(true);
                if ($order = unserialize($usersDoneTests[$id]['questions_order'])) {
                    $test -> orderQuestions($order);
                }
                foreach ($test -> questions as $key => $question) {
                    if ($order = unserialize($usersDoneTests[$id]['answers_order'])) {
                        $test -> questions[$key] -> order = $order[$key];
                    }
                    $test -> questions[$key] -> userAnswer  = unserialize($doneQuestions[$id][$key]['answer']);
                    $test -> questions[$key] -> score       = round($doneQuestions[$id][$key]['score'] * 100, 2);
                    $test -> questions[$key] -> scoreInTest = round(100 * $doneQuestions[$id][$key]['score'] * $test -> getQuestionWeight($key), 2);
                }
                $test -> overallScore = round($doneTest['score'] * 100, 2);
                $test -> status       = 'completed';
                $test -> login        = $doneTest['users_LOGIN'];
                $test -> timeStart    = $doneTest['timestamp'];
                $test -> timeSpent    = $doneTest['duration'];

                $fields = array("users_LOGIN" => $doneTest['users_LOGIN'],
                                "tests_ID"    => $doneTest['tests_ID'],
                                "test"        => serialize($test),
                                "status"      => 'completed',
                                "timestamp"   => $doneTest['timestamp'],
                                "archive"     => 0);
                $id = eF_insertTableData("completed_tests", $fields);
                $test -> completedTest['id'] = $id;
                $fields = array("users_LOGIN" => $doneTest['users_LOGIN'],
                                "tests_ID"    => $doneTest['tests_ID'],
                                "test"        => serialize($test),
                                "status"      => 'completed',
                                "timestamp"   => $doneTest['timestamp'],
                                "archive"     => 0);
                eF_updateTableData("completed_tests", $fields, "id=".$id);
            }
            eF_executeNew("drop table done_tests");
            eF_executeNew("drop table done_questions");
            eF_executeNew("drop table users_to_done_tests");
        }
        if (version_compare($from, '3.5.2') <= 0) {
            $result = eF_getTableData("content c, tests t", "t.*, c.name, c.lessons_ID, c.active", "t.content_ID=c.id");
            foreach($result as $value) {
                eF_updateTableData("tests", array('name' => $value['name'], 'lessons_ID' => $value['lessons_ID'], 'active' => $value['active']), "id=".$value['id']);
            }
        }
    }

}


class EfrontCompletedTest extends EfrontTest
{
    /**
     * Times for the test.
     *
     * This array holds important timestamps for the test:
     * <br/>- 'start': The time that the test started
     * <br/>- 'end': The time that the test ended
     * <br/>- 'spent': The total time that the user actually spent on this test (not including the time between pauses)
     * <br/>- 'resume': The time that the user last resumed the test
     * @var array
     * @since 3.5.2
     * @access public
     */
    public $time = array('start'  => '',
                         'end'    => '',
                         'spent'  => '',
                         'pause'  => '',
                         'resume' => '');

    public $completedTest = array('id'       => '',
                                  'login'    => '',
                                  'archive'  => '',
                                  'status'   => '',
                                  'testsId'  => '',
                                  'score'    => '',
                                  'feedback' => '');

    /**
     * Class constructor
     *
     * This class instantiates the object, based on an EfrontTest object and
     * the speicifed user
     * <br/>Example:
     * <code>
     * $test = new EFrontTest(34);
     * $testInstance = new EfrontCompletedTest($test, 'jdoe');
     * </code>
     *
     * @param EfrontTest $sourceTest The test that the object is based on
     * @param string $login The user login
     * @since 3.5.2
     * @access public
     */
    public function __construct(EfrontTest $sourceTest, $login) {
        $this -> test    = $sourceTest -> test;
        $this -> options = $sourceTest -> options;
        $this -> completedTest['login']   = $login;
        $this -> completedTest['testsId'] = $this -> test['id'];

        if ($this -> options['duration']) {
            $this -> convertedDuration = eF_convertIntervalToTime($this -> options['duration']);
        }
    }

    /**
     * Get done test directory
     *
     * This function is used to get the done test's directory. Tjis is the directory
     * where questions' files are uploaded.
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(54);
     * $testInstance = new EfrontCompletedTest($test, 'jdoe');
     * $testInstance -> getDirectory();
     * </code>
     *
     * @return EfrontDirectory The test's directory
     * @since 3.5.2
     * @access public
     */
    public function getDirectory() {
        $testDirectory = G_UPLOADPATH.$this -> completedTest['login'].'/tests/';
        if (!is_dir($testDirectory) && !mkdir($testDirectory, 0755)) {
            throw new EfrontTestException(_COULDNOTCREATETESTSDIRECTORY.': '.$testDirectory, EfrontTestException :: ERROR_CREATING_DIRECTORY);
        }
        $testDirectory = new EfrontDirectory($testDirectory);
        $uploadDirectory = G_UPLOADPATH.$this -> completedTest['login'].'/tests/'.$this -> completedTest['id'].'/';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755)) {
            throw new EfrontTestException(_COULDNOTCREATETESTSDIRECTORY.': '.$uploadDirectory, EfrontTestException :: ERROR_CREATING_DIRECTORY);
        }
        $uploadDirectory = new EfrontDirectory($uploadDirectory);
        return $uploadDirectory;
    }



    /**
     * Pause the test
     *
     * This function takes the current test values and stores them,
     * so that the test can be later resumed
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(54);
     * $testInstance = new EfrontCompletedTest($test, 'jdoe');
     * $testInstance -> pause($userAnswers);
     * </code>
     *
     * @param array $userAnswers The user answers, in an array where keys are question ids and values are answers
     * @since 3.5.2
     * @access public
     */
    public function pause($userAnswers, $currentQuestion = false) {
        foreach ($userAnswers as $id => $answer) {
            $this -> questions[$id] -> userAnswer = $answer;
        }
        foreach ($this -> questions as $id => $question) {
            if ($question -> question['type'] == 'raw_text') {
                $question -> handleQuestionFiles($this -> getDirectory());
            }
        }

        $this -> time['spent'] = $this -> time['spent'] + time() - $this -> time['resume'];        //Add the time passed since the last pause to the total test time
        $this -> time['pause'] = time();                                                           //Set the resume time to now
        if ($currentQuestion) {
            $this -> currentQuestion = $currentQuestion;
        }
        $this -> save();
    }

    /**
     * Complete test
     *
     * This function is used to complete the current test. The user answers are
     * submitted, the test is graded and the results are stored.
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(23);
     * $testInstance = new EfrontCompletedTest($test, 'jdoe');
     * $testInstance -> complete($userAnswers);
     * </code>
     *
     * @param array $userAnswers The user answers, in an array where keys are question ids and values are answers
     * @since 3.5.2
     * @access public
     * @todo check if folder exists, handle uploaded files
     */
    public function complete($userAnswers) {

        //Assign user answers to each question object, as a member
        foreach ($userAnswers as $id => $answer) {
            $this -> questions[$id] -> userAnswer = $answer;
        }
        //Correct each question and handle uploaded files, if any (@todo)
        foreach ($this -> questions as $id => $question) {
            $results   = $question -> correct();                                    //Get the results, which is the score and the right/wrong answers
            $question -> score   = round($results['score'] * 100, 2);
            $question -> results = $results['correct'];
            $this     -> completedTest['score'] += $results['score'] * $this -> getQuestionWeight($id);        //the total test score

            if ($question -> question['type'] == 'raw_text') {
                $question -> pending = 1;
                $question -> handleQuestionFiles($this -> getDirectory());
            }
            $question -> scoreInTest = round($question -> score * $this -> getQuestionWeight($id), 2);        //Score in test is the question score, weighted with the question's weight in the test
        }
        $this -> completedTest['score'] > 1 ? $this -> completedTest['score'] = 100 : $this -> completedTest['score'] = round($this -> completedTest['score'] * 100, 2);    //Due to roundings, overall score may go slightly above 100. o, truncate it to 100
        //Set the test status
        if ($this -> test['mastery_score'] && $this -> completedTest['score'] >= $this -> test['mastery_score']) {
            $this -> completedTest['status'] = 'passed';
        } else if ($this -> test['mastery_score'] && $this -> completedTest['score'] < $this -> test['mastery_score']) {
            $this -> completedTest['status'] = 'failed';
        } else {
            $this -> completedTest['status'] = 'completed';
        }
        $this -> time['spent'] = $this -> time['spent'] + time() - $this -> time['resume'];
        if ($this -> options['duration'] && $this -> time['spent'] > $this -> options['duration']) {
            $this -> time['spent'] = $this -> options['duration'];        //MAke sure that the spent time does not appear longer than the test duration
        }
        $this -> time['end']   = time();
        $this -> save();                                              //Save the test
    }

    /**
     * Save test
     *
     * This function is used to save the current test status
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(23);
     * $completedTest = $test -> start('jdoe');
     * $completedTest -> pause($answers);
     * $completedTest -> save();
     * </code>
     * Note: This function adds slashes to the data
     *
     * @param string $status A textual representation of the test, status, for example 'incomplete', 'failed', 'passed'
     * @since 3.5.2
     * @access public
     */
    public function save() {

        if ($this -> completedTest['id']) {
            $fields = array('test'      => addslashes(serialize($this)),
                            'status'    => $this -> completedTest['status'],
                            'timestamp' => time());
            eF_updateTableData("completed_tests", $fields, "id=".$this -> completedTest['id']);
        } else {
            $fields = array('tests_ID'    => $this -> completedTest['testsId'],
                            'users_LOGIN' => $this -> completedTest['login'],
                            'test'        => addslashes(serialize($this)),
                            'status'      => $this -> completedTest['status'],
                            'timestamp'   => time());
            if ($id = eF_insertTableData("completed_tests", $fields)) {
                $this -> completedTest['id'] = $id;
                $this -> save();
            } else {
                throw new EfrontTestException(_SOMEPROBLEMOCCURED, EfrontTestException :: DATABASE_ERROR);
            }
        }
    }

    /**
     * Print HTML version of test, along with header information
     *
     * This function enhances the toHTMLQuickForm() output, in that it adds
     * a header with test infor mation to the HTML code.
     * <br/>Example:
     * <code>
     * $result = eF_getTableData("completed_tests", "*", "id=32");
     * $showTest = unserialize($result[0]['test']);
     * $testString = $showTest -> toHTMLQuickForm(new HTML_Quickform(), false, true);
     * $url = basename($_SERVER['PHP_SELF']).'?ctg=tests';
     * $testString = $showTest -> toHTMLSolved($testString, true);
     * echo $testString;
     * </code>
     *
     * @param string $testString The test's HTML code, produced by toHTMLQuickForm()
     * @param boolean $editHandles Whether to display update score and feedback handles
     * @return string The HTML code of the test
     * @since 3.5.2
     * @access public
     * @see EfrontTest :: toHTMLQuickForm()
     */
    public function toHTMLSolved($testString, $editHandles = false) {
//      if (!$url) {
            $url = basename($_SERVER['PHP_SELF']).'?'.$_SERVER['QUERY_STRING'];
//      }
        $currentStatus = $this -> getStatus($this -> completedTest['login']);                                            //Get the current test status, to check whether the student is undergoing the test right now
        $status        = $this -> getStatus($this -> completedTest['login'], $this -> completedTest['id'], true);        //Get the completed tests status

        $str = '
        <table class = "doneTestHeader">
            <tr><td id = "doneTestImage">';
        if ($this -> test.mastery_score && $status['status'] == 'passed') {
            $str .= '
                <img src = "images/48x48/checks.png" title = "'._PASSED.'" alt = "'._PASSED.'" id = "statusImage"  />';
            $completeMessage = '<span class = "success" id = "statusMessage">'._PASSED.'</span>';
        } else if ($this -> test.mastery_score && $status['status'] == 'failed') {
            $str .= '
                <img src = "images/48x48/error.png"  title = "'._FAILED.'" alt = "'._FAILED.'" id = "statusImage" />';
            $completeMessage = '<span class = "failure" id = "statusMessage">'._FAILED.'</span>';
        } else {
            $str .= '
                <img src = "images/48x48/desktop.png"  title = "'._SOLVEDTEST.'" alt = "'._SOLVEDTEST.'" />';
        }
/*
        $durationString = '';
        if ($this -> options['duration']) {
            $duration  = eF_convertIntervalToTime($this -> options['duration']);
            $durationString .= _HASMAXIMUMDURATION.' ';
            $duration['hours']   ? $durationString .= $duration['hours'].'h '             : null;
            $duration['minutes'] ? $durationString .= $duration['minutes'].'&#039; '      : null;
            $duration['seconds'] ? $durationString .= $duration['seconds'].'&#039;&#039;' : null;
            $durationString .= '.';
        }
*/
        $timeSpent       = eF_convertIntervalToTime($this -> time['spent']);
        $completedString = ' '._ANDUSERDIDITIN.' ';
        $timeSpent['hours']   ? $completedString .= $timeSpent['hours']._HOURSSHORTHAND         : null;
        $timeSpent['minutes'] ? $completedString .= $timeSpent['minutes']._MINUTESSHORTHAND.' ' : null;
        $timeSpent['seconds'] ? $completedString .= $timeSpent['seconds']._SECONDSSHORTHAND     : null;

        if ($status['timesDone'] > 1) {
            $jumpString = '
                <span>&nbsp;('._JUMPTOEXECUTION.':
                <select  style = "vertical-align:middle" onchange = "location.toString().match(/show_solved_test/) ? location = location.toString().replace(/show_solved_test=\d+/, \'show_solved_test=\'+this.options[this.selectedIndex].value) : location = location + \'&show_solved_test=\'+this.options[this.selectedIndex].value">';
            foreach ($status['testIds'] as $count => $testId) {
                $jumpString .= '<option value = "'.$testId.'" '.($this -> completedTest['id'] == $testId ? "selected" : "").'>#'.($count + 1).' - '.formatTimestamp($status['timestamps'][$count], 'time').'</option>';
            }
            $jumpString .= '</select>)</span>';
        }

        $editHandlesString = '';
        if ($status['lastTest'] && ($status['timesLeft'] > 0 || $status['timesLeft'] === false)) {
            $editHandlesString .= '
                        <span id = "redoLink">
                            <img src = "images/16x16/undo.png" alt = "'._USERREDOTEST.'" title = "'._USERREDOTEST.'" border = "0" style = "vertical-align:middle">
                            <a href = "javascript:void(0)" id="redoLinkHref" onclick = "redoTest(this)" style = "vertical-align:middle">'._USERREDOTEST.'</a></span>';
        }
        $editHandlesString .= '
                        <span>
                            <img src = "images/16x16/arrow_right_blue.png" alt = "'._TESTANALYSIS.'" title = "'._TESTANALYSIS.'" border = "0" style = "vertical-align:middle">
                            <a href = "'.$url.'&test_analysis=1" id="testAnalysisLinkHref" style = "vertical-align:middle">'._TESTANALYSIS.'</a></span>';
        if ($editHandles) {
            if ($this -> completedTest['feedback']) {
                $editHandlesString .= '
                            <span>
                                <img src = "images/16x16/edit.png" alt = "'._EDITFEEDBACK.'" title = "'._EDITFEEDBACK.'" border = "0" style = "vertical-align:middle">
                                <a href = "javascript:void(0)" onclick = "$(\'test_feedback_div\').toggle();$(\'edit_test_feedback_div\').toggle()" style = "vertical-align:middle">'._EDITFEEDBACK.'</a></span>';
            } else {
                $editHandlesString .= '
                            <span>
                                <img src = "images/16x16/add2.png" alt = "'._ADDFEEDBACK.'" title = "'._ADDFEEDBACK.'" border = "0" style = "vertical-align:middle">
                                <a href = "javascript:void(0)" onclick = "$(\'test_feedback_div\').toggle();$(\'edit_test_feedback_div\').toggle()" style = "vertical-align:middle">'._ADDFEEDBACK.'</a></span>';
            }
            $editHandlesString .= '
                            <span>
                                <img src = "images/16x16/delete.png" alt = "'._RESETTESTSTATUS.'" title = "'._RESETTESTSTATUS.'" border = "0" style = "vertical-align:middle">
                                <a id = "deleteLink" href = "javascript:void(0)" onclick = "if (confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')) {deleteTest(this)}" style = "vertical-align:middle">'._RESETTESTSTATUS.'</a></span>
                            <span>
                                <img src = "images/16x16/delete.png" alt = "'._RESETALLTESTSSTATUS.'" title = "'._RESETALLTESTSSTATUS.'" border = "0" style = "vertical-align:middle">
                                <a id = "deleteLink" href = "javascript:void(0)" onclick = "if (confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')) {deleteTest(this, true)}" style = "vertical-align:middle">'._RESETALLTESTSSTATUS.'</a></span>';
        }


        $str .= '
                </td>
                <td>
                    <table class = "doneTestInfo">
                        <tr><td><span id = "testName">'.$this -> test['name'].'</span>'.$jumpString.'</td></tr>
                        <tr><td>'._TESTSTARTEDAT.' '.formatTimestamp($this -> time['start'], 'time').' '._ANDCOMPLETEDAT.' '.formatTimestamp($this -> time['end'], 'time').'. '.$completedString.'.</td></tr>
                        <tr><td>
                                '._THETESTISDONE.' '.$status['timesDone'].' '._TIMES.'
                                '.($this -> options['redoable'] ? _ANDCANBEDONE.' '.($status['timesLeft'] > 0 ? $status['timesLeft'] : 0).' '._TIMESMORE : '').'
                            </td></tr>';
        if ($currentStatus['status'] == 'incomplete') {
            $unsolvedTest = unserialize($currentStatus['completedTest']['test']);
            $str .= '
                        <tr><td style = "font-weight:bold">'._THEUSERUNDERGOINGTESTSTARTEDAT.':&nbsp;'.formatTimestamp($unsolvedTest -> time['start'], 'time').'</td></tr>';
        }
        $str .= '
                        <tr><td>
                                <span style = "vertical-align:middle">'._TESTSCOREIS.':&nbsp;</span>';
        if ($editHandles) {
            $str .= '
                                <span style = "font-weight:bold" id = "test_score_span">
                                    <span id = "test_score"  style = "vertical-align:middle">'.$this -> completedTest['score'].'%&nbsp;</span>
                                    <a href = "javascript:void(0)" onclick = "$(\'test_score_span\').hide();$(\'edit_test_score_span\').show();">
                                        <img src = "images/16x16/edit.png" alt = "'._CHANGESCORE.'" title = "'._CHANGESCORE.'" border = "0" style = "vertical-align:middle"/>
                                    </a>
                                </span>
                                <span id = "edit_test_score_span" style = "display:none">
                                    <input type = "text" name = "edit_test_score" id = "edit_test_score" value = "'.$this -> completedTest['score'].'" style = "vertical-align:middle"/>
                                    <a href = "javascript:void(0)" onclick = "editScore(this)">
                                        <img src = "images/16x16/check2.png" alt = "'._SUBMIT.'" title = "'._SUBMIT.'" border = "0"  style = "vertical-align:middle"/>
                                    </a>
                                    <a href = "javascript:void(0)" onclick = "$(\'test_score_span\').show();$(\'edit_test_score_span\').hide();">
                                        <img src = "images/16x16/delete2.png" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0"  style = "vertical-align:middle"/>
                                    </a>
                                </span>';
        } else {
            $str .= '
                                <span id = "test_score"  style = "vertical-align:middle">'.$this -> completedTest['score'].'%&nbsp;</span>';
        }
        $str .= '
                            &nbsp;'.$completeMessage.'</td></tr>
                        <tr><td><div class = "headerTools">'.$editHandlesString.'</div></td></tr>
                        <tr><td>';
        $str .= '
                            <div id = "test_feedback_div" '.($this -> completedTest['feedback'] ? 'class = "feedback"' : '').' >
                                <span id = "test_feedback">'.$this -> completedTest['feedback'].'</span>
                            </div>
                            <div id = "edit_test_feedback_div" style = "display:none;">
                                <textarea id = "edit_test_feedback" style = "vertical-align:middle;width:90%;height:50px">'.$this -> completedTest['feedback'].'</textarea>
                                <a href = "javascript:void(0)" onclick = "editFeedback(this)" style = "vertical-align:middle">
                                    <img src = "images/16x16/check2.png" alt = "'._SUBMIT.'" title = "'._SUBMIT.'" border = "0" style = "vertical-align:middle" />
                                </a>
                                <a href = "javascript:void(0)" onclick = "$(\'test_feedback_div\').toggle();$(\'edit_test_feedback_div\').toggle()">
                                    <img src = "images/16x16/delete2.png" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0" style = "vertical-align:middle" />
                                </a>
                            </div>
                        </td></tr>';
        $str .= '
                    </table>
                    </td></tr>
                </table>
        <table style = "width:100%">
            <tr><td>'.$testString.'</td></tr>
        </table>
        <script>
            function redoTest(el) {
                Element.extend(el);
                url = "'.$url.'&ajax=1&redo_test='.$status['lastTest'].'";

                if ($("redo_progress_img")) {
                    $("redo_progress_img").writeAttribute("src", "images/others/progress1.gif").show();
                } else {
                    el.up().insert(new Element("img", {id:"redo_progress_img", src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                }

                new Ajax.Request(url, {
                    method:"get",
                    asynchronous:true,
                    onFailure: function (transport) {
                        $("redo_progress_img").writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("redo_progress_img"));
                        window.setTimeout(\'Effect.Fade("redo_progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("redo_progress_img").hide().setAttribute("src", "images/16x16/check.png");
                        new Effect.Appear($("redo_progress_img"));
                        window.setTimeout(\'Effect.Fade("redo_progress_img")\', 2500);
                        '.($editHandles ? 'window.setTimeout(\'Effect.Fade("redoLink")\', 2500);' : 'window.setTimeout(\'Effect.Fade("redoLink");location.reload()\', 1000);').'
                    }
                });
            }
        </script>';

        if ($editHandles) {
            $str .= '
        <script>
            function deleteTest(el, all) {
                Element.extend(el);
                if (all) {
                    url = "'.$url.'&ajax=1&delete_done_test='.$this -> completedTest['id'].'&all=1";
                } else {
                    url = "'.$url.'&ajax=1&delete_done_test='.$this -> completedTest['id'].'";
                }

                if ($("progress_img")) {
                    $("progress_img").writeAttribute("src", "images/others/progress1.gif").show();
                } else {
                    el.up().insert(new Element("img", {id:"progress_img", src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                }
                new Ajax.Request(url, {
                    method:"get",
                    asynchronous:true,
                    onFailure: function (transport) {
                        $("progress_img").writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img"));
                        window.setTimeout(\'Effect.Fade("progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                        window.location = "'.basename($_SERVER['PHP_SELF']).'?ctg=tests&test_results='.$this -> test['id'].'";
                    }
                });
            }

            function editScore(el) {
                Element.extend(el);
                url = "'.$url.'&ajax=1&test_score=" + $("edit_test_score").value;

                if ($("progress_img")) {
                    $("progress_img").writeAttribute("src", "images/others/progress1.gif").show();
                } else {
                    el.up().insert(new Element("img", {id:"progress_img", src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                }

                new Ajax.Request(url, {
                    method:"get",
                    asynchronous:true,
                    onFailure: function (transport) {
                        $("progress_img").writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img"));
                        window.setTimeout(\'Effect.Fade("progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("test_score").update($("edit_test_score").value + "%&nbsp;");
                        $("test_score_span").show();
                        $("edit_test_score_span").hide();
                        if (transport.responseText == "passed") {
                             $("statusMessage").update("'._PASSED.'").className = "success";
                             $("statusImage").src = "images/48x48/checks.png";
                        } else if (transport.responseText == "failed") {
                            $("statusMessage").update("'._FAILED.'").className = "failure";
                            $("statusImage").src = "images/48x48/error.png";
                        }
                        $("progress_img").hide();
                    }
                });
            }
            function editFeedback(el) {
                Element.extend(el);
                url = "'.$url.'&ajax=1&test_feedback=" + $("edit_test_feedback").value;

                if ($("progress_img")) {
                    $("progress_img").writeAttribute("src", "images/others/progress1.gif").show();
                } else {
                    el.up().insert(new Element("img", {id:"progress_img", src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                }

                new Ajax.Request(url, {
                    method:"get",
                    asynchronous:true,
                    onFailure: function (transport) {
                        $("progress_img").writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img"));
                        window.setTimeout(\'Effect.Fade("progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("test_feedback").update(transport.responseText);
                        transport.responseText ? $("test_feedback_div").toggle().className = "feedback" : $("test_feedback_div").toggle().className = "";
                        $("edit_test_feedback_div").toggle();
                        $("progress_img").hide().setAttribute("src", "images/16x16/check.png");
                        new Effect.Appear($("progress_img"));
                        window.setTimeout(\'Effect.Fade("progress_img")\', 2500);
                    }
                });
            }
            function editQuestionScore(el, id) {
                Element.extend(el);
                url = "'.$url.'&ajax=1&question=" + id + "&question_score=" + $("edit_question_" + id + "_score").value;

                if ($("progress_img_"+id)) {
                    $("progress_img_"+id).writeAttribute("src", "images/others/progress1.gif").show();
                } else {
                    el.up().insert(new Element("img", {id:"progress_img_"+id, src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                }

                new Ajax.Request(url, {
                    method:"get",
                    asynchronous:true,
                    onFailure: function (transport) {
                        $("progress_img_"+id).writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img_"+id));
                        window.setTimeout(\'Effect.Fade("progress_img_"+id)\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("question_" + id + "_score").update($("edit_question_" + id + "_score").value + "%&nbsp;");
                        $("question_" + id + "_score_coefficient").update($("edit_question_" + id + "_score").value);
                        $("test_score").update(transport.responseText.evalJSON().score + "%&nbsp;");
                        $("edit_test_score").value = transport.responseText.evalJSON().score;
                        $("question_" + id + "_scoreInTest").update(transport.responseText.evalJSON().scoreInTest[id]);
                        $("question_" + id + "_score_span").show();
                        $("edit_question_" + id + "_score_span").hide();
                        $("question_" + id + "_pending").hide();
                        if (transport.responseText.evalJSON().status == "passed") {
                             $("statusMessage").update("'._PASSED.'").className = "success";
                             $("statusImage").src = "images/48x48/checks.png";
                        } else if (transport.responseText.evalJSON().status == "failed") {
                            $("statusMessage").update("'._FAILED.'").className = "failure";
                            $("statusImage").src = "images/48x48/error.png";
                        }
                        $("progress_img_"+id).hide();
                    }
                });
            }
            function editQuestionFeedback(el, id) {
                Element.extend(el);
                url = "'.$url.'&ajax=1&question=" + id + "&question_feedback=" + $("edit_question_"+id+"_feedback").value;

                if ($("progress_img_"+id)) {
                    $("progress_img_"+id).writeAttribute("src", "images/others/progress1.gif").show();
                } else {
                    el.up().insert(new Element("img", {id:"progress_img_"+id, src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                }

                new Ajax.Request(url, {
                    method:"get",
                    asynchronous:true,
                    onFailure: function (transport) {
                        $("progress_img_"+id).writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img_"+id));
                        window.setTimeout(\'Effect.Fade("progress_img_"+id)\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("question_" + id + "_feedback").update(transport.responseText);
                        transport.responseText ? $("question_" + id + "_feedback_div").toggle().className = "feedback" : $("question_" + id + "_feedback_div").toggle().className = "";
                        $("edit_question_" + id + "_feedback_div").toggle();
                        $("progress_img_"+id).hide().setAttribute("src", "images/16x16/check.png");
                        new Effect.Appear($("progress_img_"+id));
                        window.setTimeout(\'Effect.Fade("progress_img_"+id)\', 2500);
                    }
                });
            }
        </script>';
        }
        return $str;

    }

    /**
     * Handle AJAX actions
     *
     * This function is used to perform the necessary ajax actions,
     * that may be used in tests
     * <br/>Example:
     * <code>
     * $result     = eF_getTableData("completed_tests", "*", "id=".$_GET['show_solved_test']);
     * $showTest   = unserialize($result[0]['test']);
     * $status     = $showTest -> getStatus($result[0]['users_LOGIN']);
     * $testString = $showTest -> toHTMLQuickForm(new HTML_Quickform(), false, true, true);
     * $testString = $showTest -> toHTMLSolved($testString, true);
     * if (isset($_GET['ajax'])) {
     *     $showTest -> handleAjaxActions();
     * }
     * </code>
     *
     * @since 3.5.2
     * @access public
     */
    public function handleAjaxActions() {
        try {
            if (isset($_GET['test_score'])) {
                if (is_numeric($_GET['test_score']) && $_GET['test_score'] <= 100 && $_GET['test_score'] >= 0) {
                    $this -> completedTest['score'] = $_GET['test_score'];
                    if ($this -> test['mastery_score'] && $this -> test['mastery_score'] > $this -> completedTest['score']) {
                        $this -> completedTest['status'] = 'failed';
                    } else if ($this -> test['mastery_score'] && $this -> test['mastery_score'] <= $this -> completedTest['score']) {
                        $this -> completedTest['status'] = 'passed';
                    }
                    $this -> save();
                    echo $this -> completedTest['status'];
                } else {
                    throw new EfrontTestException(_INVALIDSCORE.': '.$_GET['test_score'], EfrontTestException :: INVALID_SCORE);
                }
            } else if (isset($_GET['test_feedback'])) {
                $this -> completedTest['feedback'] = $_GET['test_feedback'];
                $this -> save();
                echo $_GET['test_feedback'];
            } else if (isset($_GET['redo_test']) && eF_checkParameter($_GET['redo_test'], 'id')) {
                $result = eF_getTableData("completed_tests", "tests_ID, users_LOGIN", "id=".$_GET['redo_test']);
                $test   = new EfrontTest($result[0]['tests_ID']);
                $test -> redo($result[0]['users_LOGIN']);
            } else if (isset($_GET['delete_done_test'])) {
                if (isset($_GET['all'])) {
                    eF_deleteTableData("completed_tests", "users_LOGIN='".$this -> completedTest['login']."' and tests_ID=".$this -> completedTest['testsId']);
                } else {
                    eF_deleteTableData("completed_tests", "id=".$this -> completedTest['id']);
                }
            } else if (isset($_GET['question_score'])) {
                if (in_array($_GET['question'], array_keys($this -> questions))) {
                    if (is_numeric($_GET['question_score']) && $_GET['question_score'] <= 100 && $_GET['question_score'] >= 0) {
                        $this -> questions[$_GET['question']] -> score       = $_GET['question_score'];
                        $this -> questions[$_GET['question']] -> scoreInTest = round($_GET['question_score'] * $this -> getQuestionWeight($_GET['question']), 2);
                        $this -> questions[$_GET['question']] -> pending     = 0;
                        $score = 0;
                        foreach ($this -> questions as $question) {
                            $this -> completedTest['scoreInTest'][$question -> question['id']] = $question -> scoreInTest;
                            $score += $question -> scoreInTest;
                        }
                        $this -> completedTest['score'] = round($score, 2);
                        if ($this -> test['mastery_score'] && $this -> test['mastery_score'] > $this -> completedTest['score']) {
                            $this -> completedTest['status'] = 'failed';
                        } else if ($this -> test['mastery_score'] && $this -> test['mastery_score'] <= $this -> completedTest['score']) {
                            $this -> completedTest['status'] = 'passed';
                        }
                        $this -> save();
                        echo json_encode($this -> completedTest);
                    } else {
                        throw new EfrontTestException(_INVALIDSCORE.': '.$_GET['test_score'], EfrontTestException :: INVALID_SCORE);
                    }
                } else {
                    throw new EfrontTestException(_INVALIDID.': '.$_GET['question'], EfrontTestException :: QUESTION_NOT_EXISTS);
                }
            } else if (isset($_GET['question_feedback'])) {
                if (in_array($_GET['question'], array_keys($this -> questions))) {
                    $this -> questions[$_GET['question']] -> feedback = $_GET['question_feedback'];
                    $this -> save();
                    echo $_GET['question_feedback'];
                } else {
                    throw new EfrontTestException(_INVALIDID.': '.$_GET['question'], EfrontTestException :: QUESTION_NOT_EXISTS);
                }
            } else if (isset($_GET['delete_file'])) {
                $file = new EfrontFile($_GET['delete_file']);
                $testDirectory = $this -> getDirectory();
                if (strpos($file['path'], $testDirectory['path']) !== false) {
                    $file -> delete();
                }
            }
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    }

	/**
	 * Analyse completed test
	 * 
	 * This function is used to analyse completed test. Scores are calculated for
	 * each unit and subunit, based on the corresponding questions performance.
	 * <br/>Example:
	 * <code>
	 * list($parentScores, $analysisCode) = $completedTest -> analyseTest();
	 * </code>
	 * The function returns an array with 2 separate elements: The first element is the array 
	 * of scores per unit, which is needed in order to display the chart. The second element
	 * is the content tree, where the scores per unit are depicted.
	 *
	 * @return array A results array.
	 * @since 3.5.2
	 * @access public
	 */
    public function analyseTest() {
        $parentScores = array();
        
        foreach ($this -> questions as $question) {
            $questionIds[$question -> question['content_ID']]['score'] += $question -> score;
            $questionIds[$question -> question['content_ID']]['total'] ++;
            $question -> score > 0 ? $questionIds[$question -> question['content_ID']]['correct'] += $question -> score/100 : null;
        }
        $questionsStats = EfrontStats :: getQuestionsUnitStatistics($this -> questions);

        //Get unit names and ids
        $content = new EfrontContentTree(key($this -> getLesson()));

        if (isset($_GET['selected_unit']) && ($_GET['selected_unit'])) {
            $temp     = $content -> seekNode($_GET['selected_unit']);
            $tree[0]  = new EfrontUnit(array('id' => 0, 'name' => _NOUNIT, 'active' => 1, $temp['id'] => $temp));    //Add a bogus unit to hold questions which do not belong to a unit
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($tree), RecursiveIteratorIterator :: SELF_FIRST));
        } else {
            $tree     = $content -> tree;
            $tree[0]  = new EfrontUnit(array('id' => 0, 'name' => _NOUNIT, 'active' => 1));    //Add a bogus unit to hold questions which do not belong to a unit
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content -> tree), RecursiveIteratorIterator :: SELF_FIRST));
        }
        
        foreach ($iterator as $key => $value) {
            if ($key != 0) {
                foreach ($content -> getNodeAncestors($key) as $id => $foo) {
                    $parentScores[$foo['id']]['score']   += $questionIds[$key]['score'];
                    $parentScores[$foo['id']]['total']   += $questionIds[$key]['total'];
                    $parentScores[$foo['id']]['correct'] += $questionIds[$key]['correct'];
                }
                $parentScores[$key]['this_score']   += $questionIds[$key]['score'];
                $parentScores[$key]['this_total']   += $questionIds[$key]['total'];
                $parentScores[$key]['this_correct'] += $questionIds[$key]['correct'];
                $parentScores[$key]['name'] = $value['name'];
                
                // Check if this chapter is a parent one.
                if (isset($content -> tree[$key])) {
                    $parentScores[$key]['top_level'] = 1;
                } else {
                    $parentScores[$key]['top_level'] = 0;
                }
            }
        }

        foreach ($parentScores as $id => $value) {
            $parentScores[$id]['percentage'] = round($value['score']/$value['total'], 2);
            if ($value['this_total']) {
                $parentScores[$id]['this_percentage'] = round($value['this_score']/$value['this_total'], 2);
            }
            if ($value['total']) {
                $options['custom'][$id] = '
                                <span style = "margin-left:20px;color:gray">'.$parentScores[$id]['percentage'].'% ['.$value['correct'].'/'.$value['total'].']</span>
                                <img src = "images/16x16/about.png" style = "vertical-align:middle" alt = "" title = "'._THISLEVEL.': '.$parentScores[$id]['this_percentage'].'% ['.$value['this_correct'].'/'.$value['this_total'].']';

                if ($value['total'] - $value['this_total'] > 0) {
                    $options['custom'][$id] .= '/ '._BELOWLEVELS.': '.round(($value['score'] - $value['this_score'])/($value['total'] - $value['this_total']), 2).'% ['.($value['correct'] - $value['this_correct']).'/'.($value['total'] - $value['this_total']).'] ';
                }    
                $options['custom'][$id] .= '">';
            } else {
                unset($parentScores[$id]);
            }
        }
        
        $iterator = new analyseTestFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content -> tree), RecursiveIteratorIterator :: SELF_FIRST)), array_keys($parentScores));
        $options['show_hide'] = false;
        $options['noclick']   = true;
        //$options['tree_root'] = array('name' => _BACKTOTOP, 'class' => 'examples', 'onclick' => "$('analysis_frame').src = $('analysis_frame').src.replace(/selected_unit=(\d*)/, 'selected_unit='+Element.extend(this).up().id.replace(/node/, ''));");
        $options['onclick']   = "re = new RegExp(this.up().id.replace(/node/, ''), 'g');if(treeObj.getNodeOrders().match(re).length > 1) $('analysis_frame').src = $('analysis_frame').src.replace(/selected_unit=(\d*)/, 'selected_unit='+Element.extend(this).up().id.replace(/node/, ''));";
        
        return array($parentScores, $content -> toHTML($iterator, false, $options));
    }
    
    /**
     * Display chart
     * 
     * This function is used to display the HTML code needed to show
     * the chart of the test analysis
     * <br>Example:
     * <code>
     *   if (isset($_GET['display_chart'])) {
     *       $url = basename($_SERVER['PHP_SELF']).'?ctg=tests&show_solved_test='.$completedTest -> completedTest['id'].'&test_analysis=1&selected_unit='.$_GET['selected_unit'].'&show_chart=1';
     *       echo $completedTest -> displayChart($url);
     *       exit;
     *   }
     * </code>
     *
     * @param string $url The url where the data source will come from
     * @return string The HTML code that displays the chart
     * @since 3.5.2
     * @access public
     * @see analyseTest
     * @see calculateChart
     */
    public function displayChart($url) {
        $str = '
                <html>
                <head>
                <script type="text/javascript" src="charts/js/swfobject.js"></script>
                <script type="text/javascript">
                    swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", "700px", "500px", "9.0.0", "expressInstall.swf", {"data-file": encodeURIComponent("'.$url.'")} );
                </script>
                <body>
                <div id="my_chart"></div>
                <a style = "display:block" href = "javascript:void(0)" onclick = "location = location.toString().replace(/selected_unit=(\d*)/, \'\')"><img src = "images/16x16/undo.png" alt = "'._RESETGRAPH.'" title = "'._RESETGRAPH.'" border = "0"></a>                
                </body>
                </html>';
        return $str;
    }

    /**
     * Calculate cart data
     * 
     * This function is used to calculate the data needed to build the test analysis chart
     * <br/>Example:
     * <code>
     *  list($parentScores, $analysisCode) = $completedTest -> analyseTest();
     *  if (isset($_GET['display_chart'])) {
     * 		$url = basename($_SERVER['PHP_SELF']).'?ctg=tests&show_solved_test='.$completedTest -> completedTest['id'].'&test_analysis=1&selected_unit='.$_GET['selected_unit'].'&show_chart=1';
     *      echo $completedTest -> displayChart($url);
     *      exit;
     *  } elseif (isset($_GET['show_chart'])) {
     *  	echo $completedTest -> calculateChart($parentScores);
     *      exit;
     *  }
	 * </code>
     *
     * @param array $parentScores The data source, an array of scores per unit
     * @since 3.5.2
     * @access public
     * @see displayChart
     * @see analyseTest
     */
    public function calculateChart($parentScores) {
        $scores = array();
        $names  = array();

        $names[]  = null;
        $scores[] = null;
        foreach ($parentScores as $key => $value) {
            if (isset($value['percentage'])) {
                //if (isset($_GET['selected_unit']) && $_GET['selected_unit']) {
                //if (isset($_GET['selected_unit']) && $_GET['selected_unit']) {
                
                
                if (isset($_GET['selected_unit']) && $_GET['selected_unit']) {
                    $names[]  = $value['name'];
                    $scores[] = $value['this_percentage'];
                } else {
                    // Only the top level chapters should appear on the basic lesson test graph
	                if ($value['top_level'] == 1) {
	                    $names[]  = $value['name'];
	                    $scores[] = $value['percentage'];
	                }                                        
                }
            }
        }
        $names[]  = null;
        $scores[] = null;

        $line_1 = new line_dot();
        $line_1 -> set_values($scores);
        $line_1 -> set_colour( '#DFC329' );
        $line_1 -> set_key(_SCOREINEACHUNIT, 12);

        $line_2 = new line();
        $line_2 -> set_values(array_fill(0, sizeof($scores), (double)$this -> completedTest['score']));
        $line_2 -> set_colour( '#6363AC' );
        $line_2 -> set_key(_SCOREINTEST, 12);

        if (isset($_GET['selected_unit']) && $_GET['selected_unit']) {
            $line_3 = new line();
            $line_3 -> set_values(array_fill(0, sizeof($scores), $parentScores[$_GET['selected_unit']]['percentage']));
            $line_3 -> set_colour( '#5E4725' );
            $line_3 -> set_key(_SCOREINUNIT, 12);
        }

        $y = new y_axis();
        $y -> set_range(0, 119);
        $y -> set_steps(20);

        $x = new x_axis();
        $x_labels = new x_axis_labels();
        $x_labels -> set_vertical();
        $x_labels -> set_labels($names);
        $x -> set_labels($x_labels);

        $chart = new open_flash_chart();
        $chart -> add_element( $line_1 );
        $chart -> add_element( $line_2 );
        if (isset($_GET['selected_unit']) && $_GET['selected_unit']) {
            $chart -> add_element( $line_3 );
        }
        $chart -> set_y_axis( $y );
        $chart -> set_x_axis( $x );
        echo $chart -> toPrettyString();        
    }
    
    /**
     * Analyse a completed test as a skill gap test
     *
     * This function is used to perform skill-gap analysis
     *
     * <br/>Example:
     * <code>
     * $result     = eF_getTableData("completed_tests", "*", "id=".$_GET['show_solved_test']);
     * $showTest   = unserialize($result[0]['test']);
     * $analysisResults = $showTest -> analyseSkillGapTest();
     * $lessonsProposed = $analysisResults['lessons'];
     * $coursesProposed = $analysisResults['courses'];
     * </code>
     *
     * @return array containing the proposed all test related skills, all user missing skills, lesson and courses proposed for assignment in the form array( "testSkills" => array(...) , "missingSkills" => array(...), "lessons" => array(...), "courses" => array(...) )
     * @since 3.5.2
     * @access public
     */
    public function analyseSkillGapTest() {

        // SUB-COMPONENT 1: Creation of the skill-gap matrix
        $questionsAnswered = array();

        foreach ($this -> questions as $qid => $question) {
            $questionsAnswered[$qid] = ($question -> score/100);
        }

        // Acquire from the DB all skills related to the questions so that you can do the all the analysis based on the resulting skills table
        $all_related_skills = eF_getTableData("module_hcd_skills JOIN questions_to_skills ON skills_ID = skill_ID", "*", "questions_ID IN ('".implode("','", array_keys($questionsAnswered))."')");

        $skills = array();
        foreach ($questionsAnswered as $qid => $questions) {
            foreach ($all_related_skills as $skill) {
                if ($qid == $skill['questions_id']) {
                    if (isset($skills[$skill['skill_ID']] )) {
                        $skills[$skill['skill_ID']]['correct'] = $skills[$skill['skill_ID']]['correct'] + ($questions* $skill['relevance']) ;
                        $skills[$skill['skill_ID']]['total'] = $skills[$skill['skill_ID']]['total']+ $skill['relevance'] ;
                    } else {
                        $skills[$skill['skill_ID']] = array("id" => $skill['skill_ID'], "skill" => $skill['description'], "correct" => $questions * $skill['relevance'], "total" => $skill['relevance']);
                    }
                }

            }
        }

        foreach ($skills as $skid => $skill) {
            $skills[$skid]['score'] = 100*$skill['correct'] / $skill['total'];
        }

        // SUB-COMPONENT 2: Make the skill-gap analysis Todo: Make functions out of it
        $analysisResults = array();
        $analysisResults['testSkills'] = $skills;

        // Get this test's general threshold
        $testOrig = eF_getTableData("tests", "options","id = '".$this->completedTest['testsId']."'");            
        $temp = unserialize($testOrig[0]['options']);
        $this->options['general_threshold'] = $temp['general_threshold']; 

        // Get the missing skills according to the analysis
        $skills_missing = array();
        $all_skills = "";
        foreach ($skills as $skill_item) {
            if ($skill_item['score'] < $this->options['general_threshold']) {        // TOCHANGE: with the threshold of each separate test
                $skills_missing[] = $skill_item['id'];
                $all_skills .= "&".$skill_item['id'] . "=1";
            } else {
                $all_skills .= "&".$skill_item['id'] . "=0";
            }
        }

        // This smarty variable will denote all missing and existing skills
        $analysisResults['missingSkills'] = $all_skills;

        $skills_missing = implode("','",  $skills_missing);

        $user = EfrontUserFactory :: factory($this -> completedTest['login']);

        // SUB-COMPONENT 3: Propose lessons and courses
        $lessons_attending = implode("','",  array_keys($user -> getLessons()));
        $analysisResults['lessons'] = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID","module_hcd_lesson_offers_skill.lesson_ID, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$lessons_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");

        $courses_attending = implode("','",  array_keys($user -> getCourses()));
        $analysisResults['courses'] = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID","module_hcd_course_offers_skill.courses_ID, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.courses_ID NOT IN ('".$courses_attending."')","","module_hcd_course_offers_skill.courses_ID ORDER BY skills_offered DESC");

        return $analysisResults;

    }
}

/**
 * MultipleOneQuestion Class
 *
 * This class is used to manipulate a multiple choice / single answer question
 */
class MultipleOneQuestion extends Question implements iQuestion
{

    /**
     * Convert question to HTML_QuickForm
     *
     * This function is used to convert the question to HTML_QuickForm fields.
     * <br/>Example:
     * <code>
     * $question = new MultipleOneQuestion(3);                                      //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> toHTMLQuickForm($form);                                         //Add fields to form
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to
     * @since 3.5.0
     * @access public
     */
    public function toHTMLQuickForm(&$form) {
        for ($k = 0; $k < sizeof($this -> options); $k++) {
            $index        = $this -> order[$k];                                                               //$index is used to reorder question options, in case it was shuffled
            $elements[]   = $form -> createElement("radio", "question[".$this -> question['id']."]", $this -> options[$index], $this -> options[$index], $index, "class = inputRadio");    //Add a radio for each option
            $separators[] = "<br><span class = 'orderedList'>[".($k + 2)."]&nbsp;</span>";
        }
        $form -> addGroup($elements, "question[".$this -> question['id']."]", "<span class = 'orderedList'>[1]&nbsp;</span>", $separators, false);        //Create a group with the above radio buttons
        if ($this -> userAnswer !== false) {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        }
    }

    /**
     * Create HTML version of unsolved question
     *
     * This function is used to create the HTML code corresponding
     * to the question. The HTML is created using the question form
     * fields, so the proper form must be specified. A form renderer
     * is used to output the fields. The function calls internally
     * toHTMLQuickForm()
     * <br/>Example:
     * <code>
     * $question = new MultipleOneQuestion(3);                                      //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * echo $question -> toHTML($form);                                             //Output question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @return string The HTML code for the question
     * @since 3.5.0
     * @access public
     */
    public function toHTML(&$form) {
        
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        
        $form          -> accept($renderer);                                       //Render the form
        $formArray      = $renderer -> toArray();                                  //Get the rendered form fields
        
        $questionString = '
                    <table class = "unsolvedQuestion">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td>
                        		'.$formArray['question'][$this -> question['id']]['label'].$formArray['question'][$this -> question['id']]['html'].'
                            </td></tr>
                    </table>';
        
        return $questionString;
    }

    /**
     * Display solved question
     *
     * This function is used to display the solved version of the
     * question. In order to display it, setDone() must have been
     * called before.
     * <br/>Example:
     * <code>
     * $question = new MultipleOneQuestion(3);                                      //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> setDone($answer, $score, $order);                               //Set question to be done
     * echo $question -> toHTMLSolved($form);                                       //Output solved question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @param boolean $showCorrectAnswers Whether to show the correct answers
     * @param boolean $$showGivenAnswers Whether to show the given answers
     * @return string The HTML code of the solved question
     * @since 3.5.0
     * @access public
     */
    public function toHTMLSolved(&$form, $showCorrectAnswers = true, $showGivenAnswers = true) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form

        $results = $this -> correct();                                             //Correct question
        if ($showGivenAnswers) {                                                   //If the user's given answers should be shown, assign them as defaults in the form
            $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        } else {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => null));
        }
        $showGivenAnswers && $showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display

        $form               -> freeze();                                           //Freeze the form elements
        $renderer           =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form               -> accept($renderer);                                  //Render the form
        $formArray           = $renderer -> toArray();                             //Get the rendered form fields
        $innerQuestionString = '';
        for ($k = 0; $k < sizeof($this -> options); $k++) {                        //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $index = $this -> order[$k];                                           //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($results['correct']) {
                $innerQuestionString .= '<span class = "correctAnswer" style = "'.$style.'">'.$formArray['question'][$this -> question['id']][$index]['html'].'</span><br/>';
            } else {
                $innerQuestionString .= '<span class = "wrongAnswer" style = "'.$style.'">'.$formArray['question'][$this -> question['id']][$index]['html'].'</span>';
                if ($showCorrectAnswers && $this -> answer[0] == $index) {
                    $innerQuestionString .= '<span class = "correctAnswer">&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._RIGHTANSWER."</span>";
                }
                $innerQuestionString .= '<br/>';
            }
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle">
                                '.$innerQuestionString.'
                            </td></tr>
                        '.($this -> question['explanation'] ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
                    </table>';

        return $questionString;
    }

    /**
     * Shuffle question options
     *
     * This function is used to shuffle the question options,
     * so that they are displayed in a random order.
     * <br/>Example:
     * <code>
     * $question = new MultipleOneQuestion(3);                                      //Instantiate question
     * $newOrder = $question -> shuffle();                                          //Shuffle question options
     * </code>
     *
     * @return array The new question options order
     * @since 3.5.0
     * @access public
     */
    public function shuffle() {
        $shuffleOrder = range(0, sizeof($this -> options) - 1);
        shuffle($shuffleOrder);
        $this -> order = $shuffleOrder;

        return $shuffleOrder;
    }

    /**
     * Correct question
     *
     * This function is used to correct the question. In order to correct it,
     * setDone() must already have been called, so that the user answer
     * is present.
     * <br/>Example:
     * <code>
     * $question = new MultipleOneQuestion(3);                                      //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * $results = $question -> correct();                                           //Correct question
     * </code>
     *
     * @return array The correction results
     * @since 3.5.0
     * @access public
     */
    public function correct() {
        $this -> answer[0] === $this -> userAnswer ? $results = array('correct' => true, 'score' => 1) : $results = array('correct' => false, 'score' => 0);
        return $results;
    }

    /**
     * Set question done information
     *
     * This function is used to set its done information. This information consists of
     * the user answer, the score and the answers order.
     * <br/>Example:
     * <code>
     * $question = new MultipleOneQuestion(3);                                      //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * </code>
     *
     * @param array $userAnswer The user answer
     * @param float score The user's score in this question
     * @param array $order the question options order
     * @since 3.5.0
     * @access public
     * @deprecated
     */
    public function setDone($userAnswer, $score = false, $order = false) {
        $this -> userAnswer = $userAnswer;
        $score !== false ? $this -> score = $score : null;
        $order !=  false ? $this -> order = $order : null;
    }
}

/**
 * MultipleManyQuestion Class
 *
 * This class is used to manipulate a multiple choice / many answers question
 */
class MultipleManyQuestion extends Question implements iQuestion
{

    /**
     * Convert question to HTML_QuickForm
     *
     * This function is used to convert the question to HTML_QuickForm fields.
     * <br/>Example:
     * <code>
     * $question = new MultipleManyQuestion(3);                                     //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> toHTMLQuickForm($form);                                         //Add fields to form
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to
     * @since 3.5.0
     * @access public
     */
    public function toHTMLQuickForm(&$form) {
        for ($k = 0; $k < sizeof($this -> options); $k++) {
            $index        = $this -> order[$k];                                                               //$index is used to reorder question options, in case it was shuffled
            $elements[]   = $form -> createElement("advcheckbox", "question[".$this -> question['id']."][".$index."]", $this -> options[$index], $this -> options[$index], 'class = "inputCheckbox"', array(0, 1));
            $separators[] = "<br><span class = 'orderedList'>[".($k + 2)."]&nbsp;</span>";
            if ($this -> userAnswer !== false) {
                $form -> setDefaults(array("question[".$this -> question['id']."][".$index."]" => $this -> userAnswer[$index]));
            }
        }

        $form -> addGroup($elements, "question[".$this -> question['id']."]", "<span class = 'orderedList'>[1]&nbsp;</span>", $separators, false);        //Create a group with the above checkboxes
    }

    /**
     * Create HTML version of unsolved question
     *
     * This function is used to create the HTML code corresponding
     * to the question. The HTML is created using the question form
     * fields, so the proper form must be specified. A form renderer
     * is used to output the fields. The function calls internally
     * toHTMLQuickForm()
     * <br/>Example:
     * <code>
     * $question = new MultipleManyQuestion(3);                                     //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * echo $question -> toHTML($form);                                             //Output question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @return string The HTML code for the question
     * @since 3.5.0
     * @access public
     */
    public function toHTML(&$form) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html

        $form          -> accept($renderer);                                       //Render the form
        $formArray      = $renderer -> toArray();                                  //Get the rendered form fields

        $questionString = '
                    <table class = "unsolvedQuestion">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td>
                                '.$formArray['question'][$this -> question['id']]['label'].$formArray['question'][$this -> question['id']]['html'].'
                            </td></tr>
                    </table>';

        return $questionString;
    }

    /**
     * Display solved question
     *
     * This function is used to display the solved version of the
     * question. In order to display it, setDone() must have been
     * called before.
     * <br/>Example:
     * <code>
     * $question = new MultipleManyQuestion(3);                                     //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> setDone($answer, $score, $order);                               //Set question to be done
     * echo $question -> toHTMLSolved($form);                                       //Output solved question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @param boolean $showCorrectAnswers Whether to show the correct answers
     * @param boolean $$showGivenAnswers Whether to show the given answers
     * @return string The HTML code of the solved question
     * @since 3.5.0
     * @access public
     */
    public function toHTMLSolved(&$form, $showCorrectAnswers = true, $showGivenAnswers = true) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form

        $results = $this -> correct();                                             //Correct question

        for ($k = 0; $k < sizeof($this -> options); $k++) {
            if ($showGivenAnswers) {                                               //If the user's given answers should be shown, assign them as defaults in the form
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            } else {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => null));
            }
        }

        $renderer           =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form               -> freeze();                                           //Freeze the form elements
        $form               -> accept($renderer);                                  //Render the form
        $formArray           = $renderer -> toArray();                             //Get the rendered form fields
        $innerQuestionString = '';

        for ($k = 0; $k < sizeof($this -> options); $k++) {                        //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $showGivenAnswers && $showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display
            $index = $this -> order[$k];                                           //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($results['correct'][$index]) {
                $innerQuestionString .= '<span class = "correctAnswer" style = "'.$style.'">'.$formArray['question'][$this -> question['id']][$index]['html'].'</span><br/>';
            } else {
                $innerQuestionString .= '<span class = "wrongAnswer" style = "'.$style.'">'.$formArray['question'][$this -> question['id']][$index]['html'].'</span>';
                if ($showCorrectAnswers) {
                    $innerQuestionString .= '<span class = "correctAnswer">&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._RIGHTANSWER.": "._TRUE."</span>";
                }
                $innerQuestionString .= '<br/>';
            }
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle">
                                '.$innerQuestionString.'
                            </td></tr>
                        '.($this -> question['explanation'] ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
                    </table>';

        return $questionString;
    }

    /**
     * Shuffle question options
     *
     * This function is used to shuffle the question options,
     * so that they are displayed in a random order.
     * <br/>Example:
     * <code>
     * $question = new MultipleManyQuestion(3);                                     //Instantiate question
     * $newOrder = $question -> shuffle();                                          //Shuffle question options
     * </code>
     *
     * @return array The new question options order
     * @since 3.5.0
     * @access public
     */
    public function shuffle() {
        $shuffleOrder = range(0, sizeof($this -> options) - 1);
        shuffle($shuffleOrder);
        $this -> order = $shuffleOrder;

        return $shuffleOrder;
    }

    /**
     * Correct question
     *
     * This function is used to correct the question. In order to correct it,
     * setDone() must already have been called, so that the user answer
     * is present.
     * <br/>Example:
     * <code>
     * $question = new MultipleManyQuestion(3);                                     //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * $results = $question -> correct();                                           //Correct question
     * </code>
     *
     * @return array The correction results
     * @since 3.5.0
     * @access public
     */
    public function correct() {
        $nc = 0; $nf = 0;
        for ($i = 0; $i < sizeof($this -> userAnswer); $i++) {
            $results['correct'][$i] = true;                                                //Use this variable in order for the template to know how to color the answers (green/red)
            if (isset($this -> answer[$i]) && $this -> userAnswer[$i] == 1) {
                $nc++;
            } elseif (!isset($this -> answer[$i]) && $this -> userAnswer[$i] == 1) {
                $results['correct'][$i] = false;                                                //Use this variable in order for the template to know how to color the answers (green/red)
                $nf++;
            } elseif (isset($this -> answer[$i]) && $this -> userAnswer[$i] == 0) {
                $results['correct'][$i] = false;                                                //Use this variable in order for the template to know how to color the answers (green/red)
            }
        }
        $c = sizeof($this -> answer);
        $f = sizeof($this -> userAnswer) - sizeof($this -> answer);

        $results['score'] = max(0, $nc / $c - $nf / max($c, $f));

        return $results;
    }

    /**
     * Set question done information
     *
     * This question is used to set its done information. This information consists of
     * the user answer, the score and the answers order.
     * <br/>Example:
     * <code>
     * $question = new MultipleManyQuestion(3);                                     //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * </code>
     *
     * @param array $userAnswer The user answer
     * @param float score The user's score in this question
     * @param array $order the question options order
     * @since 3.5.0
     * @access public
     */
    public function setDone($userAnswer, $score = false, $order = false) {
        $this -> userAnswer = $userAnswer;
        $score !== false ? $this -> score = $score : null;
        $order !=  false ? $this -> order = $order : null;
    }
}

/**
 * TrueFalseQuestion Class
 *
 * This class is used to manipulate a true / false answers question
 */
class TrueFalseQuestion extends Question implements iQuestion
{
    /**
     * Convert question to HTML_QuickForm
     *
     * This function is used to convert the question to HTML_QuickForm fields.
     * <br/>Example:
     * <code>
     * $question = new TrueFalseQuestion(3);                                        //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> toHTMLQuickForm($form);                                         //Add fields to form
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to
     * @since 3.5.0
     * @access public
     */
    public function toHTMLQuickForm(&$form) {
        $elements[] = $form -> createElement("radio", "question[".$this -> question['id']."]", _FALSE, _FALSE, 0, "class = inputRadio");
        $elements[] = $form -> createElement("radio", "question[".$this -> question['id']."]", _TRUE, _TRUE, 1, "class = inputRadio");
        $form -> addGroup($elements, "question[".$this -> question['id']."]", null, "<br/>", false);
        if ($this -> userAnswer !== false) {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        }
    }

    /**
     * Create HTML version of unsolved question
     *
     * This function is used to create the HTML code corresponding
     * to the question. The HTML is created using the question form
     * fields, so the proper form must be specified. A form renderer
     * is used to output the fields. The function calls internally
     * toHTMLQuickForm()
     * <br/>Example:
     * <code>
     * $question = new TrueFalseQuestion(3);                                        //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * echo $question -> toHTML($form);                                             //Output question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @return string The HTML code for the question
     * @since 3.5.0
     * @access public
     */
    public function toHTML(&$form) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html

        $form          -> accept($renderer);                                       //Render the form
        $formArray      = $renderer -> toArray();                                  //Get the rendered form fields

        $questionString = '
                    <table class = "unsolvedQuestion">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td>
                                '.$formArray['question'][$this -> question['id']]['html'].'
                            </td></tr>
                    </table>';

        return $questionString;
    }

    /**
     * Display solved question
     *
     * This function is used to display the solved version of the
     * question. In order to display it, setDone() must have been
     * called before.
     * <br/>Example:
     * <code>
     * $question = new TrueFalseQuestion(3);                                        //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> setDone($answer, $score, $order);                               //Set question to be done
     * echo $question -> toHTMLSolved($form);                                       //Output solved question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @param boolean $showCorrectAnswers Whether to show the correct answers
     * @param boolean $$showGivenAnswers Whether to show the given answers
     * @return string The HTML code of the solved question
     * @since 3.5.0
     * @access public
     */
    public function toHTMLSolved(&$form, $showCorrectAnswers = true, $showGivenAnswers = true) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form

        $results = $this -> correct();                                             //Correct question
        $results['correct'] ? $class = 'correctAnswer' : $class = 'wrongAnswer';

        $form     -> freeze();                                           //Freeze the form elements
        if ($showGivenAnswers) {                                               //If the user's given answers should be shown, assign them as defaults in the form
            $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        } else {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => null));
        }
        $showGivenAnswers && $showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form     -> accept($renderer);                                  //Render the form
        $formArray = $renderer -> toArray();                             //Get the rendered form fields

        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle">
                                <span class = "'.$class.'" style = "'.$style.'">'.$formArray['question'][$this -> question['id']][0]['html'].'</span>'.($showCorrectAnswers && $this -> answer == 0 ? '<span class = "correctAnswer">&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._RIGHTANSWER.'</span>' : '').'<br/>
                                <span class = "'.$class.'" style = "'.$style.'">'.$formArray['question'][$this -> question['id']][1]['html'].'</span>'.($showCorrectAnswers && $this -> answer == 1 ? '<span class = "correctAnswer">&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._RIGHTANSWER.'</span>' : '').'<br/>
                            </td></tr>
                        '.($this -> question['explanation'] ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
                    </table>';

        return $questionString;
    }

    /**
     * Shuffle question options
     *
     * This function is not used for this type of question
     *
     * @since 3.5.0
     * @access public
     */
    public function shuffle() {
        return true;
    }

    /**
     * Correct question
     *
     * This function is used to correct the question. In order to correct it,
     * setDone() must already have been called, so that the user answer
     * is present.
     * <br/>Example:
     * <code>
     * $question = new TrueFalseQuestion(3);                                        //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * $results = $question -> correct();                                           //Correct question
     * </code>
     *
     * @return array The correction results
     * @since 3.5.0
     * @access public
     */
    public function correct() {
        $this -> answer == $this -> userAnswer ? $results = array('correct' => true, 'score' => 1) : $results = array('correct' => false, 'score' => 0);
        return $results;
    }

    /**
     * Set question done information
     *
     * This question is used to set its done information. This information consists of
     * the user answer, the score and the answers order.
     * <br/>Example:
     * <code>
     * $question = new TrueFalseQuestion(3);                                        //Instantiate question
     * $question -> setDone($answer, $score);                                       //Set done question information
     * </code>
     *
     * @param array $userAnswer The user answer
     * @param float score The user's score in this question
     * @param array $order the question options order, not applicable to this question type
     * @since 3.5.0
     * @access public
     */
    public function setDone($userAnswer, $score = false, $order = false) {
        $this -> userAnswer = $userAnswer;
        $score !== false ? $this -> score = $score : null;
        //$order !== false ? $this -> order = $order : null;
    }
}

/**
 * EmptySpacesQuestion Class
 *
 * This class is used to manipulate a empty spaces question
 */
class EmptySpacesQuestion extends Question implements iQuestion
{

    /**
     * Convert question to HTML_QuickForm
     *
     * This function is used to convert the question to HTML_QuickForm fields.
     * <br/>Example:
     * <code>
     * $question = new EmptySpacesQuestion(3);                                      //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> toHTMLQuickForm($form);                                         //Add fields to form
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to
     * @since 3.5.0
     * @access public
     */
    public function toHTMLQuickForm(&$form) {
        $inputLabels  = explode('###', $this -> question['text']);
        $questionText = '';
        for ($k = 0; $k < sizeof($this -> answer); $k++) {
            $elements[] = $form -> addElement("static", null, null, $inputLabels[$k]);
            $elements[] = $form -> addElement("text", "question[".$this -> question['id']."][$k]", null, '');
            if ($this -> userAnswer !== false) {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            }
        }
        $elements[] = $form -> addElement("static", null, null, $inputLabels[$k]);

        $form -> addGroup($elements, "question[".$this -> question['id']."]", $inputLabels[0], null, false);
    }

    /**
     * Create HTML version of unsolved question
     *
     * This function is used to create the HTML code corresponding
     * to the question. The HTML is created using the question form
     * fields, so the proper form must be specified. A form renderer
     * is used to output the fields. The function calls internally
     * toHTMLQuickForm()
     * <br/>Example:
     * <code>
     * $question = new EmptySpacesQuestion(3);                                      //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * echo $question -> toHTML($form);                                             //Output question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @return string The HTML code for the question
     * @since 3.5.0
     * @access public
     */


    public function toHTML(&$form) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html

        $form          -> accept($renderer);                                       //Render the form
        $formArray      = $renderer -> toArray();                                  //Get the rendered form fields

        $questionString = '
                    <table class = "unsolvedQuestion">
                        <tr><td>
                                '.$formArray['question'][$this -> question['id']]['html'].'
                            </td></tr>
                    </table>';

        return $questionString;
    }

    /**
     * Display solved question
     *
     * This function is used to display the solved version of the
     * question. In order to display it, setDone() must have been
     * called before.
     * <br/>Example:
     * <code>
     * $question = new EmptySpacesQuestion(3);                                      //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> setDone($answer, $score, $order);                               //Set question to be done
     * echo $question -> toHTMLSolved($form);                                       //Output solved question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @param boolean $showCorrectAnswers Whether to show the correct answers
     * @param boolean $$showGivenAnswers Whether to show the given answers
     * @return string The HTML code of the solved question
     * @since 3.5.0
     * @access public
     */
    public function toHTMLSolved(&$form, $showCorrectAnswers = true, $showGivenAnswers = true) {
        $inputLabels  = explode('###', $this -> question['text']);

        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form

        $results = $this -> correct();                                             //Correct question

        for ($k = 0; $k < sizeof($this -> answer); $k++) {
            if ($showGivenAnswers) {                                               //If the user's given answers should be shown, assign them as defaults in the form
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            } else {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => null));
            }
        }
        $renderer           =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form               -> freeze();                                           //Freeze the form elements
        $form               -> accept($renderer);                                  //Render the form
        $formArray           = $renderer -> toArray();                             //Get the rendered form fields
        $innerQuestionString = $inputLabels[0];
        for ($k = 0; $k < sizeof($this -> answer); $k++) {
            $showGivenAnswers && $showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display
            if ($results['correct'][$k]) {
                $innerQuestionString .= '<span class = "correctAnswer" style = "'.$style.'">'.$formArray['question'][$this -> question['id']][$k]['html'].'</span>'.$inputLabels[$k + 1].'<br/>';
            } else {
                $innerQuestionString .= '<span class = "wrongAnswer" style = "'.$style.'">'.$formArray['question'][$this -> question['id']][$k]['html'].'</span>'.$inputLabels[$k + 1].'<br/>';
            }
        }

        if ($showCorrectAnswers) {
            $innerQuestionString .= '<br/><br/><span class = "correctAnswer">'._RIGHTANSWER.':</span><br/>'.$inputLabels[0];
            for ($k = 0; $k < sizeof($this -> answer); $k++) {
                $innerQuestionString .= '<span class = "correctAnswer">'.$this -> answer[$k].'</span>'.$inputLabels[$k + 1];
            }
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td style = "vertical-align:middle">
                                '.$innerQuestionString.'
                            </td></tr>
                        '.($this -> question['explanation'] ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
                    </table>';

        return $questionString;
    }

    /**
     * Shuffle question options
     *
     * This function is not used for this type of question
     *
     * @since 3.5.0
     * @access public
     */
    public function shuffle() {
        return true;
    }

    /**
     * Correct question
     *
     * This function is used to correct the question. In order to correct it,
     * setDone() must already have been called, so that the user answer
     * is present.
     * <br/>Example:
     * <code>
     * $question = new EmptySpacesQuestion(3);                                      //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * $results = $question -> correct();                                           //Correct question
     * </code>
     *
     * @return array The correction results
     * @since 3.5.0
     * @access public
     */
    public function correct() {
        $results['score'] = 0;
        $factor           = 1 / sizeof($this -> userAnswer);                                        //If the question has 4 options, then the factor is 1/4.
        for ($i = 0; $i < sizeof($this -> userAnswer); $i++) {
            $this -> answer[$i] = explode("|", $this -> answer[$i]);

            $answers = $this -> answer[$i];    //Create a copy so that mb_strtolower does not alter the original version
            array_walk($answers, create_function('&$v, $k', '$v = mb_strtolower(trim($v));'));

            if (isset($this -> answer[$i]) && in_array(mb_strtolower($this -> userAnswer[$i]), $answers)) {
                $results['score']      += $factor;
                $results['correct'][$i] = true;                                                //Use this variable in order for the template to know how to color the answers (green/red)
            } else {
                $results['correct'][$i] = false;
            }
            $this -> answer[$i] = implode(" "._OR." ", $this -> answer[$i]);
        }

        return $results;
    }

    /**
     * Set question done information
     *
     * This question is used to set its done information. This information consists of
     * the user answer, the score and the answers order.
     * <br/>Example:
     * <code>
     * $question = new EmptySpacesQuestion(3);                                      //Instantiate question
     * $question -> setDone($answer, $score);                                       //Set done question information
     * </code>
     *
     * @param array $userAnswer The user answer
     * @param float score The user's score in this question
     * @param array $order the question options order, not applicable for this question type
     * @since 3.5.0
     * @access public
     */
    public function setDone($userAnswer, $score = false, $order = false) {
        $this -> userAnswer = $userAnswer;
        $score !== false ? $this -> score = $score : null;
        //$order !== false ? $this -> order = $order : null;
    }
}

/**
 * MatchQuestion Class
 *
 * This class is used to manipulate a match question
 */
class MatchQuestion extends Question implements iQuestion
{

    /**
     * Convert question to HTML_QuickForm
     *
     * This function is used to convert the question to HTML_QuickForm fields.
     * <br/>Example:
     * <code>
     * $question = new MatchQuestion(3);                                        //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> toHTMLQuickForm($form);                                         //Add fields to form
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to
     * @since 3.5.0
     * @access public
     */
    public function toHTMLQuickForm(&$form) {
        $random = range(0, sizeof($this -> answer) - 1);                                                   //$random is a temporary array used only for creating a random ordering
        for ($k = 0; $k < sizeof($this -> options); $k++) {
            shuffle($random);                                                                              //Shuffle an array with the same size as the answers
            $answers = array();                                                                            //$answers array will be used in place of $this -> answer
            foreach ($random as $value) {                                                                  //Populate $answers array, so that it is a permutated version of this -> answer array
                $answers[$value] = $this -> answer[$value];
            }
            $index        = $this -> order[$k];                                                               //$index is used to reorder question options, in case it was shuffled
            $elements[]   = $form -> addElement("static", null, null, $this -> options[$index]);
            $elements[]   = $form -> addElement("select", "question[".$this -> question['id']."][".$index."]", $this -> options, $answers);
            $separators[] = "&nbsp;&rarr;&nbsp;";
            $separators[] = "<br><span class = 'orderedList'>[".($k + 2)."]&nbsp;</span>";
            if ($this -> userAnswer !== false) {
                 $form -> setDefaults(array("question[".$this -> question['id']."][$index]" => $this -> userAnswer[$index]));
            }
        }
        $form -> addGroup($elements, "question[".$this -> question['id']."]", "<span class = 'orderedList'>[1]&nbsp;</span>", $separators, false);
    }

    /**
     * Create HTML version of unsolved question
     *
     * This function is used to create the HTML code corresponding
     * to the question. The HTML is created using the question form
     * fields, so the proper form must be specified. A form renderer
     * is used to output the fields. The function calls internally
     * toHTMLQuickForm()
     * <br/>Example:
     * <code>
     * $question = new MatchQuestion(3);                                        //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * echo $question -> toHTML($form);                                             //Output question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @return string The HTML code for the question
     * @since 3.5.0
     * @access public
     */
    public function toHTML(&$form) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html

        $form          -> accept($renderer);                                       //Render the form
        $formArray      = $renderer -> toArray();                                  //Get the rendered form fields

        $questionString = '
                    <table class = "unsolvedQuestion">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td>
                                '.$formArray['question'][$this -> question['id']]['label'].$formArray['question'][$this -> question['id']]['html'].'
                            </td></tr>
                    </table>';

        return $questionString;
    }

    /**
     * Display solved question
     *
     * This function is used to display the solved version of the
     * question. In order to display it, setDone() must have been
     * called before.
     * <br/>Example:
     * <code>
     * $question = new MatchQuestion(3);                                        //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> setDone($answer, $score, $order);                               //Set question to be done
     * echo $question -> toHTMLSolved($form);                                       //Output solved question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @param boolean $showCorrectAnswers Whether to show the correct answers
     * @param boolean $$showGivenAnswers Whether to show the given answers
     * @return string The HTML code of the solved question
     * @since 3.5.0
     * @access public
     */
    public function toHTMLSolved(&$form, $showCorrectAnswers = true, $showGivenAnswers = true) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form

        $results = $this -> correct();                                             //Correct question
        for ($k = 0; $k < sizeof($this -> options); $k++) {                        //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            if ($showGivenAnswers) {                                               //If the user's given answers should be shown, assign them as defaults in the form
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            }  else {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => null));
            }
        }
        $renderer           =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form               -> freeze();                                           //Freeze the form elements
        $form               -> accept($renderer);                                  //Render the form
        $formArray           = $renderer -> toArray();                             //Get the rendered form fields
        $innerQuestionString = '';

        for ($k = 0; $k < sizeof($this -> options); $k++) {                        //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $showGivenAnswers && $showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display
            $index = $this -> order[$k];                                           //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($results['correct'][$index]) {
                $innerQuestionString .= '<span class = "correctAnswer" style = "'.$style.'">'.$this -> options[$index].'&nbsp;&rarr;&nbsp;'.$formArray['question'][$this -> question['id']][$index]['html'].'</span>';
            } else {
                $innerQuestionString .= '<span class = "wrongAnswer" style = "'.$style.'">'.$this -> options[$index].'&nbsp;&rarr;&nbsp;'.$formArray['question'][$this -> question['id']][$index]['html'].'</span>';
            }
            if ($showCorrectAnswers) {
                $innerQuestionString .= '<span class = "correctAnswer">&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._RIGHTANSWER.": ".$this -> answer[$index]."</span>";
            }
            $innerQuestionString .= '<br/>';
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle">
                                '.$innerQuestionString.'
                            </td></tr>
                        '.($this -> question['explanation'] ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
                    </table>';

        return $questionString;
    }

    /**
     * Shuffle question options
     *
     * This function is used to shuffle the question options,
     * so that they are displayed in a random order.
     * <br/>Example:
     * <code>
     * $question = new MultipleManyQuestion(3);                                     //Instantiate question
     * $newOrder = $question -> shuffle();                                          //Shuffle question options
     * </code>
     *
     * @return array The new question options order
     * @since 3.5.0
     * @access public
     */
    public function shuffle() {
        $shuffleOrder = range(0, sizeof($this -> options) - 1);
        shuffle($shuffleOrder);
        $this -> order = $shuffleOrder;

        return $shuffleOrder;
    }

    /**
     *
     * Correct question
     *
     * This function is used to correct the question. In order to correct it,
     * setDone() must already have been called, so that the user answer
     * is present.
     * <br/>Example:
     * <code>
     * $question = new MatchQuestion(3);                                            //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * $results = $question -> correct();                                           //Correct question
     * </code>
     *
     * @return array The correction results
     * @since 3.5.0
     * @access public
     */
    public function correct() {
        $results['score'] = 0;
        $factor            = 1 / sizeof($this -> userAnswer);                                        //If the question has 4 options, then the factor is 1/4.
        $answerKeys        = array_keys($this -> answer);
        for ($i = 0; $i < sizeof($this -> userAnswer); $i++) {
            if ($this -> userAnswer[$i] == $answerKeys[$i] || $this -> answer[$this -> userAnswer[$i]] == $this -> answer[$i]) {
                $results['score']      += $factor;
                $results['correct'][$i] = true;                                                //Use this variable in order for the template to know how to color the answers (green/red)
            } else {
                $results['correct'][$i] = false;
            }
        }

        return $results;
    }

    /**
     * Set question done information
     *
     * This question is used to set its done information. This information consists of
     * the user answer, the score and the answers order.
     * <br/>Example:
     * <code>
     * $question = new MatchQuestion(3);                                        //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * </code>
     *
     * @param array $userAnswer The user answer
     * @param float score The user's score in this question
     * @param array $order the question options order
     * @since 3.5.0
     * @access public
     */
    public function setDone($userAnswer, $score = false, $order = false) {
        $this -> userAnswer = $userAnswer;
        $score !== false ? $this -> score = $score : null;
        $order !=  false ? $this -> order = $order : null;
    }
}

/**
 * RawTextQuestion Class
 *
 * This class is used to manipulate a raw text question
 */
class RawTextQuestion extends Question implements iQuestion
{

    /**
     * Convert question to HTML_QuickForm
     *
     * This function is used to convert the question to HTML_QuickForm fields.
     * <br/>Example:
     * <code>
     * $question = new RawTextQuestion(3);                                      //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> toHTMLQuickForm($form);                                         //Add fields to form
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to
     * @since 3.5.0
     * @access public
     */
    public function toHTMLQuickForm(&$form) {
        $elements[] = $form -> createElement("textarea", "question[".$this -> question['id']."]", null, 'class = "simpleEditor" style = "width:100%;height:100px;"');
        $elements[] = $form -> createElement("file",     "file_".$this -> question['id'].'[0]', null, 'class = "inputText" id = "file_'.$this -> question['id'].'[0]" style = "display:none"');
        if ($this -> userAnswer !== false) {
             $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        }
        $form -> addGroup($elements, "question[".$this -> question['id']."]", null, "<br/>", false);
    }

    /**
     * Create HTML version of unsolved question
     *
     * This function is used to create the HTML code corresponding
     * to the question. The HTML is created using the question form
     * fields, so the proper form must be specified. A form renderer
     * is used to output the fields. The function calls internally
     * toHTMLQuickForm()
     * <br/>Example:
     * <code>
     * $question = new RawTextQuestion(3);                                          //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * echo $question -> toHTML($form);                                             //Output question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @return string The HTML code for the question
     * @since 3.5.0
     * @access public
     */
    public function toHTML(&$form) {

        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html

        $form          -> accept($renderer);                                       //Render the form
        $formArray      = $renderer -> toArray();                                  //Get the rendered form fields

        foreach ($this -> files as $file) {
            try {
                $file         = new EfrontFile($file);
                $filesString .= '<br/><span id = "file_'.$file['id'].'">'._UPLOADEDFILE.': <a href = "view_file.php?file='.$file['id'].'&action=download" style = "font-weight:bold">'.$file['name'].'</a>&nbsp;<a href = "javascript:void(0)" onclick = "deleteFile(this, '.$file['id'].')"><img src = "images/16x16/delete.png" title = "'._DELETE.'" alt = "'._DELETE.'" style = "vertical-align:middle" ></a></span>';
            } catch (Exception $e) {}
        }

        $questionString = '
                    <table class = "unsolvedQuestion">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td>
                                '.$formArray['question'][$this -> question['id']]['html'].'<div></div>&nbsp;<img id = "add_another_'.$this -> question['id'].'" src = "images/16x16/add2.png" alt = "'._ADDANOTHERFILE.'" title = "'._ADDANOTHERFILE.'" style = "display:none" onclick = "addAnotherFile(this)">
                        </td></tr>
                        <tr><td>
                                <br/><a href = "javascript:void(0)" onclick = "Element.extend(this).hide();$(\'file_'.$this -> question['id'].'[0]\').show();$(\'add_another_'.$this -> question['id'].'\').show()">('._SENDFILEASANSWER.')</a>
                                <br/>'.$filesString.'
                            </td></tr>
                    </table>
                    <script>
                        function addAnotherFile(el) {
                            Element.extend(el);
                            el.up().select("input").each(function (s) {matches = s.name.match(/file_'.$this -> question['id'].'\[(\d*)\]/); next = parseInt(matches[1]) + 1});
                            el.previous().insert(new Element("div").insert(new Element("input", {type: "file", name: "file_'.$this -> question['id'].'["+next+"]"})));
                        }
                        function deleteFile(el, id) {
                            Element.extend(el);
                            url = location+"&ajax=1&delete_file="+id;

                            el.down().src = "images/others/progress1.gif";

                            new Ajax.Request(url, {
                                method:"get",
                                asynchronous:true,
                                onFailure: function (transport) {
                                    el.down().writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                                    new Effect.Appear(el.down());
                                    window.setTimeout("Effect.Fade("+el.down().identify()+")", 10000);
                                },
                                onSuccess: function (transport) {
                                el.down().hide();
                                el.down().src = "images/16x16/check.png";
                                new Effect.Appear(el.down());
                                window.setTimeout("Effect.Fade(\'file_"+id+"\')", 1000);
                                }
                            });
                        }
                    </script>';

        return $questionString;
    }

    /**
     * Display solved question
     *
     * This function is used to display the solved version of the
     * question. In order to display it, setDone() must have been
     * called before.
     * <br/>Example:
     * <code>
     * $question = new RawTextQuestion(3);                                          //Instantiate question
     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form
     * $question -> setDone($answer, $score, $order);                               //Set question to be done
     * echo $question -> toHTMLSolved($form);                                       //Output solved question HTML code
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to and display
     * @param boolean $showCorrectAnswers Whether to show the correct answers
     * @param boolean $$showGivenAnswers Whether to show the given answers
     * @return string The HTML code of the solved question
     * @since 3.5.0
     * @access public
     */
    public function toHTMLSolved(&$form, $showCorrectAnswers = true, $showGivenAnswers = true) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form

        $filesString = '';
        foreach ($this -> files as $file) {
            try {
                $file         = new EfrontFile($file);
                $filesString .= '<br/><b>'._UPLOADEDFILE.': <a href = "view_file.php?file='.$file['id'].'&action=download">'.$file['name'].'</a></b>';
            } catch (Exception $e) {}
        }

        $results = $this -> correct();                                             //Correct question
        if ($showGivenAnswers) {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        } else {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => null));
        }

        $renderer           =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form               -> freeze();                                           //Freeze the form elements
        $form               -> accept($renderer);                                  //Render the form
        $formArray           = $renderer -> toArray();                             //Get the rendered form fields

        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle">
                                '.$formArray['question'][$this -> question['id']]['html'].'
                                '.($showCorrectAnswers  && $this -> answer ? '<span class = "correctAnswer"><br/>'._EXAMPLEANSWER.':<br/> '.$this -> answer.'</span>' : '').'
                                '.$filesString.'
                            </td></tr>
                        '.($this -> question['explanation'] ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
                    </table>';

        return $questionString;
    }

    /**
     * Shuffle question options
     *
     * This function is not used for this type of question
     *
     * @since 3.5.0
     * @access public
     */
    public function shuffle() {
        return true;
    }

    /**
     * Correct question
     *
     * This function is used to correct the question. In order to correct it,
     * setDone() must already have been called, so that the user answer
     * is present.
     * <br/>Example:
     * <code>
     * $question = new RawTextQuestion(3);                                          //Instantiate question
     * $question -> setDone($answer, $score, $order);                               //Set done question information
     * $results = $question -> correct();                                           //Correct question
     * </code>
     *
     * @return array The correction results
     * @since 3.5.0
     * @access public
     */
    public function correct() {
        if ($this -> score) {
            $results = array('correct' => '', 'score' => $this -> score);
        } else {
            $results = array('correct' => '', 'score' => 0);
        }

        return $results;
    }

    /**
     * Set question done information
     *
     * This question is used to set its done information. This information consists of
     * the user answer, the score and the answers order.
     * <br/>Example:
     * <code>
     * $question = new RawTextQuestion(3);                                          //Instantiate question
     * $question -> setDone($answer, $score);                                       //Set done question information
     * </code>
     *
     * @param array $userAnswer The user answer
     * @param float score The user's score in this question
     * @param array $order the question options order, not applicable to this question type
     * @since 3.5.0
     * @access public
     */
    public function setDone($userAnswer, $score = false, $order = false) {

        $this -> userAnswer = $userAnswer;
        $score !== false ? $this -> score = $score : null;
        //$order !== false ? $this -> order = $order : null;
    }
}

/**
 *
 */
interface iQuestion
{
    public function toHTMLQuickForm(&$form);
    public function toHTML(&$form);
    public function toHTMLSolved(&$form, $showCorrectAnswers = true, $showGivenAnswers = true);
    public function shuffle();
    public function correct();
    public function setDone($userAnswer, $score = false, $order = false);
}

/**
 *
 */
abstract class Question
{
    /**
     * The available question types
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public static $questionTypes = array('empty_spaces'  => _EMPTYSPACES,
                                         'raw_text'      => _DEVELOPMENT,
                                         'multiple_one'  => _MULTIPLEONE,
                                         'multiple_many' => _MULTIPLEMANY,
                                         'match'         => _MATCH,
                                         'true_false'    => _TRUEFALSE);

    /**
     * The available question types icons
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public static $questionTypesIcons = array('empty_spaces'  => 'images/16x16/dot-chart.png',
                                              'raw_text'      => 'images/16x16/pens.png',
                                              'multiple_one'  => 'images/16x16/branch_element.png',
                                              'multiple_many' => 'images/16x16/branch.png',
                                              'match'         => 'images/16x16/component.png',
                                              'true_false'    => 'images/16x16/yinyang.png');

    /**
     * The available question difficulties
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public static $questionDifficulties = array('easy'   => _LOW,
                                                'medium' => _MEDIUM,
                                                'hard'   => _HARD);
    /**
     * The available question difficulties icons
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public static $questionDifficultiesIcons = array('easy'   => 'images/16x16/flag_green.png',
                                                     'medium' => 'images/16x16/flag_blue.png',
                                                     'hard'   => 'images/16x16/flag_red.png',);

    /**
     * The question fields
     *
     * @var array
     * @access public
     * @since 3.5.0
     */
    public $question = array();

    /**
     * Question options
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public $options = array();

    /**
     * Question's answer(s)
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public $answer = array();

    /**
     * The user's answer, if the question is done
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public $userAnswer = false;

    /**
     * The user's score, if the question is done
     *
     * @var float
     * @since 3.5.0
     * @access public
     */
    public $score = false;

    /**
     * The questions's answers order
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public $order = array();


    /**
     * An array of file ids, that were uploaded along with the question
     *
     * @var array
     * @since 3.5.2
     * @access public
     */
    public $files = array();

    /**
     * Whether this question should be corrected by the professor
     *
     * @var int
     * @since 3.5.2
     * @access public
     */
    public $pending = 0;

    /**
     * The maximum question text length, when displayed not in tests
     *
     * @since 3.5.0
     *@access public
     */
    const maxQuestionText = 50;

    /**
     * Class constructor
     *
     * This function is used to instantiate a test question object.
     * If an id is used, then the question is instantiate based on
     * database information. Alternatively, the question array itself
     * may be provided, thus overriding database query.
     * <br/>Example:
     * <code>
     * $question = QuestionFactory :: factory(4);                   //Instantiate question using question id
     * $result   = eF_getTableData("questions", "*", "id=4");
     * $question = QuestionFactory :: factory($result[0]);          //Instantiate question using question array
     * </code>
     *
     * @param mixed $question Either a question id or a question array
     * @param array $testOptions specific test options that have impact on the question rendering
     * @since 3.5.0
     * @access public
     */
    function __construct($question) {
        if (is_array($question)) {
            $this -> question = $question;
        } elseif (!eF_checkParameter($question, 'id')) {
            throw new EfrontTestException(_INVALIDID.': '.$question, EfrontTestException :: INVALID_ID);
        } else {
            $result = eF_getTableData("questions", "*", "id=".$question);
            if (sizeof($result) == 0) {
                throw new EfrontTestException(_INVALIDID.': '.$question, EfrontTestException :: QUESTION_NOT_EXISTS);
            } else {
                $this -> question = $result[0];
            }
        }

        @unserialize($this -> question['options']) !== false ? $this -> options = unserialize($this -> question['options']) : $this -> options = $this -> question['options'];
        @unserialize($this -> question['answer'])  !== false ? $this -> answer  = unserialize($this -> question['answer'])  : $this -> answer  = $this -> question['answer'];

        is_array($this -> options) ? $this -> order = array_keys($this -> options) : null;
        $this -> question['type_icon'] = Question :: $questionTypesIcons[$this -> question['type']];
        $plainText = trim(strip_tags($this -> question['text']));
        if (mb_strlen($plainText) > self :: maxQuestionText) {
            $plainText = mb_substr($plainText, 0, self :: maxQuestionText).'...';
        }
        $this -> question['plain_text'] = $plainText;
        //$testOptions ? $this -> testOptions = array_merge($this -> testOptions, $testOptions) : null;            //Merge arrays, thus only overwriting values that exist in both arrays
    }

    /**
     * Delete question
     *
     * This function is used to delete the current question
     * <br/>Example:
     * <code>
     * $question -> delete();
     * </code>
     *
     * @return boolean True if the delete was successful
     * @since 3.5.0
     * @access public
     */
    public function delete() {
        eF_deleteTableData("questions", "id=".$this -> question['id']);
        eF_deleteTableData("done_questions", "id=".$this -> question['id']);
        
        eF_deleteTableData("tests_to_questions", "questions_ID=".$this -> question['id']);
        eF_deleteTableData("questions_to_skills", "questions_ID=".$this -> question['id']);
        return true;
    }

    /**
     * Persist question changes
     *
     * This function is used to store changed question attributes to
     * the database.
     * <br>Example:
     * <code>
     * $question -> question['text'] = 'new title';             //Change question title
     * $question -> persist();                                  //Persist changed value
     * </code>
     *
     * @since 3.5.0
     * @access public
     */
    public function persist() {
        $fields = array("text" => $this -> question['text'],
                        "type" => $this -> question['type'],
                        "answer" => $this -> question['answer'],
                        "content_ID" => $this -> question['content_ID'],
                        "difficulty" => $this -> question['difficulty'],
                        "options" => $this -> question['options'],
                        "explanation" => $this -> question['explanation']);
        eF_updateTableData("questions", $fields, "id=".$this -> question['id']);
    }

    /**
     * This question's tests
     *
     * This function returns a list of all the tests that it is
     * assigned to.
     * <br/>Example:
     * <code>
     * $question -> getTests();
     * </code>
     *
     * @return array The array of the tests this question is in
     * @since 3.5.0
     * @access public
     */
    public function getTests() {
        $result = eF_getTableData("tests_to_questions tq, tests t", "t.*", "t.id=tq.tests_ID and tq.questions_ID=".$this -> question['id']);
        $tests  = array();
        foreach ($result as $value) {
            $tests[$value['id']] = $value;
        }
        return $tests;
    }

    /**
     * Handle uploaded file associated with question
     *
     * This function is used to handle any uploaded files that have to do with the current question
     * <br/>Example:
     * <code>
     * $test = new EfrontTest(23);
     * foreach ($test -> questions as $id => $question) {
     *     if ($question -> question['type'] == 'raw_text') {
     *         $question -> handleQuestionFiles($this -> getDirectory());
     *     }
     * }
     * </code>
     *
     * @param mixed $uploadDirectory The directory to upload the file to, a string or an EfrontDirectory object
     * @since 3.5.2
     * @access public
     */
    public function handleQuestionFiles($uploadDirectory) {
        $uploadedFiles   = array();
        if (!($uploadDirectory instanceof EfrontDirectory) && !is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755)) {
            throw new EfrontTestException(_COULDNOTCREATETESTSDIRECTORY.': '.$uploadDirectory, EfrontTestException :: ERROR_CREATING_DIRECTORY);
        } else {
            $filesystem = new FileSystemTree($uploadDirectory);
            foreach ($_FILES as $key => $value) {
                foreach ($value['name'] as $offset => $filename) {
                    if ($filename && str_replace('file_', '', $key) == $this -> question['id']) {
                        $uploadedFile = $filesystem -> uploadFile($key, $uploadDirectory, $offset);
                        $this -> files[] = $uploadedFile['id'];
                    }
                }
            }
        }

    }

    /**
     * Create a new question
     *
     * This function is used to create a new question
     * <br/>Example:
     * <code>
     * $fields = array('text' => 'new questions', 'type' => 'multiple_one', 'content_ID' => 10);
     * $question = Question :: createQuestion($fields);
     * </code>
     *
     * @param array $question The new question attributes
     * @return Question the new question object or false
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function createQuestion($question) {
        !isset($question['difficulty']) ? $question['difficulty'] = 'medium' : null;
        if ($newId = eF_insertTableData("questions", $question)) {
            return QuestionFactory :: factory($newId);
        } else {
            return false;
        }
    }

}

/**
 *
 */
class QuestionFactory
{
    /**
     * Construct question object
     *
     * This function is used to construct a question object, based on the question type.
     * Specifically, it creates an TrueFalseQuestion, MultipleOneQuestion, MultipleManyQuestion etc
     * If $question is an id, the function queries the database. Alternatively, it may
     * use a prepared question array, which is mostly convenient when having to perform
     * multiple initializations
     * <br/>Example :
     * <code>
     * $question = QuestionFactory :: factory(43);                      //Use factory function to instantiate question object with id 43
     * $questionData = eF_getTableData("questions", "*", "id=43");
     * $question = QuestionFactory :: factory($$questionData[0]);      //Use factory function to instantiate user object using prepared data
     * </code>
     *
     * @param mixed $question A question id or an array holding question data
     * @return Question an object of a class extending Question
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function factory($question) {
        if (!is_array($question)) {
            if (eF_checkParameter($question, 'id')) {
                $result = eF_getTableData("questions", "*", "id='".$question."'");
                if (sizeof($result) == 0) {
                    throw new EfrontTestException(_INVALIDID.': '.$question, EfrontTestException :: QUESTION_NOT_EXISTS);
                }
                $question = $result[0];
            } else {
                throw new EfrontTestException(_INVALIDID.': '.$question, EfrontTestException :: INVALID_ID);
            }
        }

        switch ($question['type']) {
            case 'raw_text'      : $factory = new RawTextQuestion($question);      break;
            case 'multiple_one'  : $factory = new MultipleOneQuestion($question);  break;
            case 'multiple_many' : $factory = new MultipleManyQuestion($question); break;
            case 'empty_spaces'  : $factory = new EmptySpacesQuestion($question);  break;
            case 'match'         : $factory = new MatchQuestion($question);        break;
            case 'true_false'    : $factory = new TrueFalseQuestion($question);    break;
            default: throw new EfrontTestException(_INVALIDQUESTIONTYPE.': "'.$question['type'].'"', EfrontTestException :: INVALID_TYPE); break;
        }

        return $factory;
    }
}

/**
 * Analyse test filter
 * 
 * This filter is used for test analysis, to filter out units that have not questions
 * associated with them
 *
 * @since 3.5.2
 */
class analyseTestFilterIterator extends FilterIterator
{
    /**
     * Class constructor
     * 
     * Initialise filter, using the parent units scores
     *
     * @param ArrayIterator $it The iterator
     * @param array $parentScores The parent units scores
     * @since 3.5.2
     * @access public
     */
    function __construct($it, $parentScores) {
        parent :: __construct($it);
        $this -> parentScores = $parentScores;
        $this -> count = 0;
    }
    
    /**
     * Filter out nodes
     * 
     * This function filters out units that do not have completed questions associated
     * with them.
     *
     * @return boolean true if the unit should have a score
     * @since 3.5.2
     * @access public
     */
    function accept() {
        if (in_array($this -> current() -> offsetGet('id'), $this -> parentScores)) {
            return true;
        }
    }
}

?>