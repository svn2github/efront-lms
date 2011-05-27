<?php
/**

* Efront Test Class file

*

* @package eFront

* @version 3.6.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * Test exceptions

 *

 * @package eFront

 */
class EfrontTestException extends Exception
{
    const INVALID_ID = 801;
    const QUESTION_NOT_EXISTS = 802;
    const TEST_NOT_EXISTS = 803;
    const NOT_DONE_TEST = 804;
    const INVALID_LOGIN = 805;
    const DONE_QUESTION_NOT_EXISTS = 806;
    const INVALID_TYPE = 807;
    const INVALID_SCORE = 808;
    const DATABASE_ERROR = 809;
    const ERROR_CREATING_DIRECTORY = 810;
    const CORRUPTED_TEST = 811;
    const RANDOM_POOL_LESS = 812;
}
/**

 * Class for tests

 *

 * @package eFront

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
    public $options = array('duration' => 0,
                            'master_score' => 50,
                            'redoable' => 0,
                            'onebyone' => 0,
                            'answers' => 0,
                            'shuffle_questions' => 0,
                            'shuffle_answers' => 0,
                            'given_answers' => 1,
          'show_answers_if_pass' => 1,
                            'random_pool' => 0,
                            'user_configurable' => 0,
          'show_incomplete' => 0,
                            'maintain_history' => 5,
                            'display_list' => 0,
                            'pause_test' => 1,
                            'display_weights' => 1,
          'only_forward' => 0,
       'answer_all' => 0,
       'test_password' => 0,
          'redo_wrong' => 0,
       'redirect' => 0);
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
            if ($isContentId) {
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
            $newOptions = array_diff_key($this -> options, $options); //$newOptions are test options that were added to the EfrontTest object AFTER the test options serialization took place
            $this -> options = $options + $newOptions; //Set test options
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
        eF_deleteTableData("completed_tests", "tests_ID=".$this -> test['id']);
        eF_deleteTableData("content", "id=".$this -> test['content_ID']);
        eF_deleteTableData("tests", "id=".$this -> test['id']);
        Cache::resetCache('test:'.$this -> test['id']);
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
        $fields = array('active' => $this -> test['active'],
                        'content_ID' => $this -> test['content_ID'],
                        'options' => serialize($this -> options),
                        'description' => $this -> test['description'],
                        'mastery_score' => $this -> test['mastery_score'],
                        'name' => $this -> test['name'],
                        'lessons_ID' => $this -> test['lessons_ID'],
                        'publish' => $this -> test['publish'],
            'keep_best' => $this -> test['keep_best']);
        Cache::resetCache('test:'.$this -> test['id']);
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
         //Get content unit names, to be assigned to questions for easy access
            $contentNames = eF_getTableDataFlat("content", "id, name", "lessons_ID=".$this -> test['lessons_ID']);
            if (sizeof($contentNames) > 0) {
             $contentNames = array_combine($contentNames['id'], $contentNames['name']);
            }
            //Get test questions
         $result = eF_getTableData("tests_to_questions tq, questions q", "q.*, tq.weight, tq.previous_question_ID", "tq.questions_ID=q.id and tq.tests_ID=".$this -> test['id']);
            $rejected = array();
            if (sizeof($result) > 0) {
                foreach ($result as $value) {
                    $value['type_icon'] = Question :: $questionTypesIcons[$value['type']];
                    $value['content_name'] = $contentNames[$value['content_ID']];
                    $questions[$value['id']] = $value;
              if (!isset($previousQuestions[$value['previous_question_ID']])) {
                  $previousQuestions[$value['previous_question_ID']] = $value;
              } else {
                  $rejected[$value['id']] = $value; //$rejected holds cut off units, which do not have a valid previous_content_ID
              }
                }
                //Sorting algorithm, based on previous_question_ID. the algorithm is copied from EfrontContentTree :: reset() and is the same with the one applied for content
                $node = 0;
                $count = 0;
                $nodes = array(); //$count is used to prevent infinite loops
                while (sizeof($previousQuestions) > 0 && isset($previousQuestions[$node]) && $count++ < 1000) {
                    $nodes[$previousQuestions[$node]['id']] = $previousQuestions[$node];
                    $newNode = $previousQuestions[$node]['id'];
                    unset($previousQuestions[$node]);
                    $node = $newNode;
                }
          if (sizeof($previousQuestions) > 0) { //If $previousNodes is not empty, it means there are invalid (orphan) units in the array, so append them to the $rejected list
              $previousNode = end($nodes);
           foreach ($previousQuestions as $value) {
                  $value['previous_question_ID'] = $previousNode['id'];
            $nodes[$value['id']] = $value;
                  $previousNode = $value;
              }
          }
          if (sizeof($rejected) > 0) {
           foreach ($rejected as $id => $value) {
            $value['previous_question_ID'] = $previousNode['id'];
            $nodes[$id] = $value;
            $previousNode = $value;
            eF_updateTableData("tests_to_questions", array("previous_question_ID" => $previousNode['id']), "questions_ID = ".$value['id']." and tests_ID=".$this -> test['id']);
           }
          }
                $this -> questions = $nodes;
            } else {
                $this -> questions = array();
            }
        }
        $questions = array();
        foreach ($this -> questions as $key => $value) {
            if (($value instanceof Question)) {
             $returnObjects ? $questions[$key] = $value : $questions[$key] = $value -> question;
            } else if (is_array($value)) {
                $returnObjects ? $questions[$key] = QuestionFactory :: factory($value) : $questions[$key] = $value;
            }
        }
//pr($questions);
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
        $lesson = $this -> getLesson();
        $testQuestions = $this -> getQuestions();
        //Get content unit names, to be assigned to questions for easy access
        $contentNames = eF_getTableDataFlat("content", "id, name", "lessons_ID=".$this -> test['lessons_ID']);
        if (sizeof($contentNames) > 0) {
         $contentNames = array_combine($contentNames['id'], $contentNames['name']);
        }
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
         $value['content_name'] = $contentNames[$value['content_ID']]; //This is needed here in order for filtering to work properly: the Question object has to have the content name inside it
   $returnObjects ? $nonQuestions[$value['id']] = QuestionFactory :: factory($value) : $nonQuestions[$value['id']] = $value;
        }
        return $nonQuestions;
    }
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
        Cache::resetCache('test:'.$this -> test['id']);
        $testQuestions = $this -> getQuestions();
        $nonTestQuestions = $this -> getNonQuestions();
        //getQuestions returns sorted questions
        if (sizeof($testQuestions) > 0) {
            $lastQuestion = end($testQuestions);
            $previousId = $lastQuestion['id'];
        } else {
            $previousId = 0;
        }
        foreach ($questions as $id => $weight) {
            $fields = array("tests_ID" => $this -> test['id'],
                            "questions_ID" => $id,
                            "weight" => $weight && is_numeric($weight) ? $weight : 1);
            if (!in_array($id, array_keys($testQuestions))) { //We are adding a new question
                $fields["previous_question_ID"] = $previousId;
                eF_insertTableData("tests_to_questions", $fields);
                $previousId = $id;
            } else { //We are changing a question's weight
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
     Cache::resetCache('test:'.$this -> test['id']);
        if ($questionIds === false) {
            eF_deleteTableData("tests_to_questions", "tests_ID = ".$this -> test['id']);
            $this -> questions = false; //Reset questions information
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
            $this -> questions = false; //Reset questions information
            return $this -> getQuestions(); //Return new questions list
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
/*

        $result = eF_getTableData("lessons, content", "lessons.id, lessons.name", "lessons.id=content.lessons_ID and content.id=".$this -> test['content_ID']);

        if ($result[0]['id']) {

            $this -> lesson = array($result[0]['id'] =>  $result[0]['name']);

        } else {

            $this -> lesson = array();

        }

*/
        // This check is used to discriminate skill gap tests
     if ($this -> test['lessons_ID'] != 0) {
         $this -> lesson = $this -> test['lessons_ID'];
         $result = eF_getTableData("lessons", "*", "id=".$this -> lesson);
         if (sizeof($result) == 0) {
             throw new EfrontLessonException(_LESSONDOESNOTEXIST.": ".$this -> lesson, EfrontLessonException :: LESSON_NOT_EXISTS);
         }
         if ($returnObjects) {
             $lesson = new EfrontLesson($result[0]);
         } else {
             $lesson = array($this -> lesson => $result[0]['name']);
         }
     } else {
         $lesson = array(); // used to denote skill gap tests
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
   $test['lessons_ID'] = $unit['lessons_ID'];
        } else {
            $unit = $content;
            $test['content_ID'] = $unit['id'];
   $test['lessons_ID'] = $unit['lessons_ID'];
        }
        unset($test['id']);
        if ($newId = eF_insertTableData("tests", $test)) {
         if ($test['lessons_ID'] != 0) {
          EfrontEvent::triggerEvent(array("type" => EfrontEvent::TEST_CREATION, "users_LOGIN" => $GLOBALS['currentUser'] -> user['login'], "lessons_ID" => $test['lessons_ID']));
         } else {
          // Special treatment for skillgap tests
    EfrontEvent::triggerEvent(array("type" => EfrontEvent::TEST_CREATION, "users_LOGIN" => $GLOBALS['currentUser'] -> user['login'], "lessons_ID" => 0, "lessons_name" => _SKILLGAPTESTS));
         }
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
    private static $auto_assigned = false;
    public static function getAutoAssignedTests() {
        // Skillgap tests have lessons_ID equal to zero by default
        if (!$auto_assigned) {
         $all_skillgaps = eF_getTableData("tests", "id, options", "lessons_ID = 0");
         $auto_assigned = array();
         foreach ($all_skillgaps as $skillgap) {
             $options = unserialize($skillgap['options']);
             if ($options['assign_to_new']) {
                 $auto_assigned[] = $skillgap['id'];
             }
         }
        }
        return $auto_assigned;
    }
    /**

     * Return the subset that meets the requirements for time and number

     *

     * This function tries to compute a subset that meets the specified requirements

     * for the total time, as well as the total number of elements, based on the following

     * algorithm:

     *

     * 1. Normalize times and number

     * 2. Repeat for N times:

     *   2.1 Randomize the array

     *   2.2 Pick a part of the array, 'subarray', of random size

     *   2.3 Calculate the distance of this subarray to the requirements

     * 3. Pick the subset that minimizes this distance

     *

     * For example, let's say you have the following array, representing question ids and

     * corresponding times (in seconds - empty times are allowed, they equal to zero):

	 *  $times = Array

	 *		(

	 *			[6] => 90

	 *			[9] =>

	 *			[4] => 30

	 *			[7] => 110

	 *			[5] => 130

	 *		    [8] => 60

	 *		)

	 *  We wish to get a subset that has 4 questions in total, with duration 300 seconds.

     *  The algorithm would then go as (pseudo-code):

     *  1. Normalization: $size = 4/6, $time = 300/420  (wanted questions/total questions, wanted time/times sum)

     *  2. Repeat for N (say 10) times:

     *     2.1 Randomize array: shuffle($times)

     *     2.2 Pick a subarray of random size: $subset = array_slice($times, 0, $random)  ($random is a random number from 1 to total size)

     *     2.3 Calculate the (euclidean) distance: $dist = sqrt(pow(sizeof($subset)/6 - 4/6, 2) + pow(array_sum($subset)/420 - 300/420, 2))

     *  3. Pick the minimum subset: sort($dist), and take the first element

     *

     * @param $parameters

     * @param $reqs

     * @return array

     * @since 3.5.5

     * @access public

     */
    public function randomize($parameters, $reqs = false) {
  //Get all questions and assign them to an array where keys are their ids. At the same time,
  //calculate the time estimates sum for questions, which will be used in normalization.
     $result = eF_getTableData("questions", "*", "lessons_ID=".$this -> test['lessons_ID']);
     $sum = 0;
     foreach ($result as $value) {
      $questions[$value['id']] = QuestionFactory::factory($value);
      $sum += $value['estimate'];
     }
     //Normalize initial parameters
     $size = sizeof($questions);
     isset($parameters['multitude']) && $parameters['multitude'] ? $multitude = $parameters['multitude'] / $size : $multitude = false;
     isset($parameters['duration']) && $parameters['duration'] ? $duration = $parameters['duration'] / $sum : $duration = false;
  //Balance is a coefficient that weighs multitude versus duration
     if (isset($parameters['balance']) && is_numeric($parameters['balance']) && $parameters['balance'] >= 0 && $parameters['balance'] <= 100) {
      $balance = array($parameters['balance'], 100 - $parameters['balance']);
     }
     for ($i = 1; $i < 1000; $i++) {
      //Get a subset from the $times array, based on specific requirements
      $subsets[$i] = self :: getSubset($questions, $parameters, $reqs);
      //If either number or time is not specified, eliminate corresponding parameter so that it is not taken into
      //account when calculating distance. If neither parameter is specified, make a completely random test
      if ((!isset($multitude) || !$multitude) && (!isset($duration) || !$duration)) {
       $point = array(0, 0); //Neither duration nor multitude specified, so this is a completely random test
      } else if (!isset($multitude) || !$multitude) {
       $point = array(0, array_sum($subsets[$i])/$sum); //Multitude is not specified, only duration, so we minimize the distance to the preferred duration, no matter how many questions there will be
      } else if (!isset($duration) || !$duration) {
       $point = array(sizeof($subsets[$i])/$size, 0); //This time duration is not specified, only multitude, so we minimize the distance to the preferred multitude, no matter how long will the test take
      } else {
       $point = array(sizeof($subsets[$i])/$size, array_sum($subsets[$i])/$sum); //Both multitude and duration are specified, so we minimize the distance with this pair (multitude,distance)
      }
      //Calculate euclidean distance
      $distance[$i] = sqrt($balance[0]*pow($point[0] - $multitude, 2) + $balance[1]*pow($point[1] - $duration, 2));
     }
     //Sort the distances, so that the smaller comes first
     asort($distance);
     //Get the first array element
     $selected = $subsets[key($distance)];
     //Remove current questions from the test, and assign the new ones, all with the same weight (1)
     $this -> removeQuestions();
     $this -> addQuestions(array_combine(array_keys($selected), array_fill(0, sizeof($selected), 1)));
     //Return the array holding the questions that where finally selected
  foreach ($selected as $value => $v) {
   $selectedQuestions[$value] = $questions[$value];
  }
     return $selectedQuestions;
    }
    /**

     * Get statistics for test's questions

     *

     * This function retrieves some general information on a test's question

     * <br>Example:

     * <code>

     * $test = new EfrontTest(4);

     * $stats = $test -> questionsInfo();

     * </code>

     * The result holds the following information:

     * - multitude (integer)<br>

     * - duration (integer, seconds)<br>

     * - difficulties (array)<br>

     * - types(array)<br>

     * - percentage(array) <br>

     *

     * @return array The information array

     * @since 3.5.4

     * @access public

     */
    public function questionsInfo($questions = false) {
     if (!$questions) {
      $questions = $this -> getQuestions();
     }
     $stats = array('multitude' => sizeof($questions),
           'total_duration' => 0,
           'difficulties' => array(),
           'types' => array(),
           'percentage' => array());
     foreach ($questions as $question) {
      if ($question instanceOf Question) {
       $question = $question -> question;
      }
      $stats['total_duration'] += $question['estimate'];
      $stats['difficulties'][$question['content_ID']][$question['difficulty']]++;
      $stats['types'][$question['content_ID']][$question['type']]++;
      $stats['percentage'][$question['content_ID']] += 10/$stats['multitude'];
     }
     foreach ($stats['percentage'] as $key => $value) {
      $stats['percentage'][$key] = round($value, 2);
     }
     return $stats;
    }
 /**

     * Get questions subset

     *

     * This function is used be randomize() in order to produce a random set of questions,

     * which will be the base of a random test. The function is called multiple times,

     * and the results are based on the constraints specified in $parameters and $reqs

     *

     * @param $questions The questions set to select from

     * @param $parameters The random test parameters, such as multitude and duration

     * @param $reqs The requirements, such as "must have 4 multiple choice questions"

     * @return array The array of selected questions

     * @since 3.5.4

     * @access protected

     */
    protected static function getSubset($questions, $parameters, $reqs = false) {
     $questions = self::ashuffle($questions);
     $times = array();
     //If there aren't any parameters, just return a random array
     if (!$reqs) {
      //If there is multitude but no duration set, return a random array of fixed size. Otherwise, the random array has random size
      if ($parameters['multitude'] && !$parameters['duration']) {
       $subset = array_slice($questions, 0, $parameters['multitude'], true);
      } else {
       $subset = array_slice($questions, 0, rand(1, sizeof($questions)), true);
      }
      foreach ($subset as $key => $value) {
       $times[$key] = $value -> question['estimate'];
      }
     } else if (isset($reqs['difficulty'])) {
   $units = array_keys($reqs['difficulty']);
   $selected = $any = array();
      foreach ($questions as $key => $question) {
       $unit = $question -> question['content_ID'];
       $difficulty = $question -> question['difficulty'];
    if (in_array($unit, $units) && in_array($difficulty, array_keys($reqs['difficulty'][$unit]))) {
     if ($reqs['difficulty'][$unit][$difficulty] > 0) {
      $selected[] = $key;
      $reqs['difficulty'][$unit][$difficulty]--;
     } else if ($reqs['difficulty'][$unit][$difficulty] === 'any') {
      $any[] = $key;
     }
    }
      }
      //Merge $selected questions, with a random slice of $any questions
      $selected = array_merge($selected, (array_slice($any, 0, rand(0, sizeof($any)))));
      foreach ($selected as $value) {
       $times[$value] = $questions[$value] -> question['estimate'];
      }
     } else if (isset($reqs['type'])) {
   $units = array_keys($reqs['type']);
   $selected = $any = array();
   foreach ($questions as $key => $question) {
       $unit = $question -> question['content_ID'];
       $type = $question -> question['type'];
    if (in_array($unit, $units) && in_array($type, array_keys($reqs['type'][$unit]))) {
     if ($reqs['type'][$unit][$type] > 0) {
      $selected[] = $key;
      $reqs['type'][$unit][$type]--;
     } else if ($reqs['type'][$unit][$type] === 'any') {
      $any[] = $key;
     }
    }
      }
      //Merge $selected questions, with a random slice of $any questions
      $selected = array_merge($selected, (array_slice($any, 0, rand(0, sizeof($any)))));
      foreach ($selected as $value) {
       $times[$value] = $questions[$value] -> question['estimate'];
      }
     } else if (isset($reqs['percentage'])) {
   $units = array_keys($reqs['percentage']);
   //Get a random-size slice of the questions
   $questions = array_slice($questions, 0, rand(1, sizeof($questions)), true);
   //Count the total questions each unit has
      foreach ($questions as $key => $question) {
       $total[$question -> question['content_ID']]++;
      }
      //Calculate the how many questions we should get from each unit, based on percentages
      foreach ($reqs['percentage'] as $unit => $percentage) {
       $unitQuestions[$unit] = round($percentage*$total[$unit]/100);
      }
      //Get these questions from the total questions
      foreach ($questions as $key => $question) {
       $unit = $question -> question['content_ID'];
       if (in_array($unit, $units) && $unitQuestions[$unit] > 0) {
        $selected[] = $key;
        $unitQuestions[$unit]--;
       }
      }
      foreach ($selected as $value) {
       $times[$value] = $questions[$value] -> question['estimate'];
      }
     }
     return $times;
    }
    /**

     * Shuffle an array preserving keys

     *

     * This function is used to shuffle an array, but preserving keys.

     * <br>Example:

     * <code>

     * $arr = array(1 => 'aaa', 5 => 'eee', 10 => 'kkk', 26 => 'zzz', 'me' => 'jdoe');

     * EfrontTest :: ashuffle($arr);	//Puts the array's elements in random order, preserving their keys

     * </code>

     *

     * @param array $array The original array to shuffle

     * @return array the shuffled version of the array

     * @since 3.5.5

     * @access protected

     */
    protected static function ashuffle($array) {
     $randomizer = range(0, sizeof($array) - 1, 1);
     shuffle($randomizer);
     $keys = array_keys($array);
     $values = array_values($array);
     foreach ($randomizer as $r) {
       $shuffled[$keys[$r]] = $values[$r];
     }
     return $shuffled;
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
        if (sizeof($result) > 0) { //Get the done information for this test
            $this -> doneInfo = $result[0];
            $this -> doneInfo['score'] = round(100 * ($this -> doneInfo['score']), 2) / 100;
            unserialize($this -> doneInfo['answers_order']) !== false ? $this -> doneInfo['answers_order'] = unserialize($this -> doneInfo['answers_order']) : null;
            unserialize($this -> doneInfo['questions_order']) !== false ? $this -> doneInfo['questions_order'] = unserialize($this -> doneInfo['questions_order']) : null;
            $result = eF_getTableDataFlat("done_questions", "distinct questions_ID", "score = -1 and done_tests_ID=".$this -> doneInfo['id']);
            if (sizeof($result) > 0) {
                foreach ($result['questions_ID'] as $id) {
                    $potentialScore += $this -> getQuestionWeight($id);
                }
                $this -> doneInfo['potential_score'] = round(100 * ($this -> doneInfo['score'] + $potentialScore), 2) / 100;
            }
        } else { //Otherwise, just find out how many times the user has done this test, which is an information kept always (even if a test is reset)
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
                $directory = new EfrontDirectory(G_UPLOADPATH.$login.'/tests/'.$this -> test['id'].'/');
                $directory -> rename(G_UPLOADPATH.$login.'/tests/completed_'.$this -> completedTest['id'].'/');
            } catch (EfrontFileException $e) {}
        }
        //Set the unit as "not seen"
        if (!($user instanceof EfrontUser)) {
            $user = EfrontUserFactory :: factory($login, false, 'student');
        }
        $user -> setSeenUnit($this -> test['content_ID'], key($this -> getLesson()), 0);
  $check_redoOnlyWrong = EfrontCompletedTest::retrieveCompletedTest("completed_tests","test","archive=0 AND tests_ID=".($this -> test['id'])." and users_LOGIN='".$login."'");
  $testObject = unserialize($check_redoOnlyWrong[0]['test']);
  if ($testObject -> redoOnlyWrong == 1) {
   unset($testObject -> redoOnlyWrong);
   EfrontCompletedTest::updateCompletedTest("completed_tests", array("test" => serialize($testObject), "archive" => 1), "archive=0 AND tests_ID=".($this -> test['id'])." and users_LOGIN='".$login."'");
  } else {
   EfrontCompletedTest::updateCompletedTest("completed_tests", array("archive" => 1), "tests_ID=".($this -> test['id'])." and users_LOGIN='".$login."'");
  }
    }
 public function redoOnlyWrong($user) {
        if ($user instanceof EfrontUser) {
            $login = $user -> user['login'];
        } elseif (eF_checkParameter($user, 'login')) {
            $login = $user;
        } else {
            throw new EfrontTestException(_INVALIDLOGIN.': '.$user, EfrontTestException :: INVALID_LOGIN);
        }
        if (is_dir(G_UPLOADPATH.$login.'/tests/'.$this -> test['id'])) {
            try {
                $directory = new EfrontDirectory(G_UPLOADPATH.$login.'/tests/'.$this -> test['id'].'/');
                $directory -> rename(G_UPLOADPATH.$login.'/tests/completed_'.$this -> completedTest['id'].'/');
            } catch (EfrontFileException $e) {}
        }
        //Set the unit as "not seen"
        if (!($user instanceof EfrontUser)) {
            $user = EfrontUserFactory :: factory($login, false, 'student');
        }
        $user -> setSeenUnit($this -> test['content_ID'], key($this -> getLesson()), 0);
  $result = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "test", "archive=0 AND tests_ID=".($this -> test['id'])." and users_LOGIN='".$login."'");
  $testObject = unserialize($result[0]['test']);
  $testObject -> redoOnlyWrong = true;
        EfrontCompletedTest::updateCompletedTest("completed_tests", array("test" => serialize($testObject), "archive" => 1), "archive=0 AND tests_ID=".($this -> test['id'])." and users_LOGIN='".$login."'");
    }
    /**

     * Delete done test information

     *

     * This function is used to delete the done information for the specified

     * user.

     * <br/>Example:

     * <code>

     * $test -> undo('jdoe');                           //Delete test information for user jdoe

     * $test -> undo('jdoe', 43);                       //Delete test information for user jdoe and completed test 43

     * </code>

     *

     * @param mixed $user The user to delete test for, either a user login or an EfrontUser instance

     * @param int $instance A specific completedTest instance to delete. If it's ommited, all completed tests from this user will be deleted

     * @since 3.5.2

     * @access public

     */
    public function undo($user, $instance = false) {
        if ($user instanceof EfrontUser) {
            $login = $user -> user['login'];
        } elseif (eF_checkParameter($user, 'login')) {
            $login = $user;
        } else {
            throw new EfrontTestException(_INVALIDLOGIN.': '.$user, EfrontTestException :: INVALID_LOGIN);
        }
        if (!$instance) {
         $result = eF_getTableData("completed_tests", "id", "users_LOGIN='".$login."' and tests_ID=".$this -> test['id']);
   foreach ($result as $value) {
          if (is_dir(G_UPLOADPATH.$login.'/tests/'.$value['id'])) {
              try {
                  $directory = new EfrontDirectory(G_UPLOADPATH.$login.'/tests/'.$value['id'].'/');
                  $directory -> delete();
              } catch (EfrontFileException $e) {}
          }
   }
         //Set the unit as "not seen"
         if (!($user instanceof EfrontUser)) {
             $user = EfrontUserFactory :: factory($login, false, 'student');
         }
         $user -> setSeenUnit($this -> test['content_ID'], key($this -> getLesson()), 0);
         eF_deleteTableData("completed_tests", "users_LOGIN='".$login."' and tests_ID=".$this -> test['id']);
        } else {
         if (!eF_checkParameter($instance, 'id')) {
          throw new EfrontTestException(_INVALIDID.': '.$instance, EfrontTestException :: INVALID_ID);
         }
         $result = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "*", "users_LOGIN='".$login."' and id = ".$instance);
         if (sizeof($result) == 0) {
          throw new EfrontTestException(_USERHASNOTDONETEST.': '.$login, EfrontTestException :: NOT_DONE_TEST);
         }
         $completedTest = unserialize($result[0]['test']);
         if (!$completedTest) {
          throw new EfrontTestException(_TESTCORRUPTEDORNOTACOMPLETEDTEST, EfrontTestException::CORRUPTED_TEST);
         }
         if (is_dir(G_UPLOADPATH.$login.'/tests/'.$instance)) {
             try {
                 $directory = new EfrontDirectory(G_UPLOADPATH.$login.'/tests/'.$instance.'/');
                 $directory -> delete();
             } catch (EfrontFileException $e) {}
         }
         //If the test is the last one (the 'active'), set it as not seen.
         //If it doesn't have a content id, it is a skill-gap test
         if ($completedTest -> completedTest['archive'] == 0 && $this -> test['content_ID']) {
          if (!($user instanceof EfrontUser)) {
           $user = EfrontUserFactory :: factory($login, false, 'student');
          }
          $user -> setSeenUnit($this -> test['content_ID'], key($this -> getLesson()), 0);
         }
         eF_deleteTableData("completed_tests", "id=".$instance);
        }
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
         //If a question exists in the order, but not it the test itself
         if (in_array($value, array_keys($this -> questions))) {
             $temp[$value] = $this -> questions[$value];
         }
        }
        $this -> questions = $temp;
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
        $completedTest = new EfrontCompletedTest($this, $login);
        $completedTest -> time['start'] = time(); //The time that this test has started
        $completedTest -> time['resume'] = time(); //Initialize time that this test has 'resumed'
        $completedTest -> time['end'] = null; //The time that this test ends
        $completedTest -> time['spent'] = 0; //Initialize the time spent
        $completedTest -> completedTest['status'] = 'incomplete'; //The test just started; So set its status to 'incomplete'
//        $completedTest -> completedTest['archive'] = '0';                              //The test just started; So set its status to 'incomplete'
        $testQuestions = $this -> getQuestions(true);
  // lines added for redo only wrong questions
  $resultCompleted = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "test", "archive=1 AND users_LOGIN='".$_SESSION['s_login']."' AND tests_ID=".$this -> test['id'], "timestamp desc");
  $recentlyCompleted = unserialize($resultCompleted[0]['test']);
        //1. Get the random pool questions
        if ($this -> options['random_pool']) {
            if ($recentlyCompleted -> redoOnlyWrong == false) {
    sizeof($testQuestions) >= $this -> options['random_pool'] ? $poolSize = $this -> options['random_pool'] : $poolSize = sizeof($testQuestions);
    shuffle($testQuestions); //shuffle available questions so that we don't take the same always
    if ($this->options['show_incomplete']) {
     $alreadyCompletedQuestions = array();
     foreach ($resultCompleted as $value) {
      $previouslyCompletedTest = unserialize($value['test']);
      if ($previouslyCompletedTest instanceOf EfrontCompletedTest) {
       $alreadyCompletedQuestions = array_merge($alreadyCompletedQuestions, array_keys($previouslyCompletedTest -> questions));
      }
     }
     $alreadyCompletedQuestions = array_unique($alreadyCompletedQuestions);
     $incompleteQuestions = array_diff(array_keys($this -> questions), $alreadyCompletedQuestions); //Find out which questions haven't been answered yet
     //Keep only incomplete questions
     foreach ($testQuestions as $key => $value) {
      if (!in_array($value->question['id'], $incompleteQuestions) && sizeof($testQuestions) > $poolSize) {
       unset($testQuestions[$key]);
      }
     }
    }
    $testQuestions = array_slice($testQuestions, 0, $poolSize);
    $temp = array();
    foreach ($testQuestions as $value) { //Shuffling reindexed array, so we need to put back the correct keys
     $temp[$value -> question['id']] = $value;
    }
    $completedTest -> questions = $temp;
   } else {
    $completedTest -> questions = $recentlyCompleted -> questions; //when redoing wrong answered, same questions must be selected
   }
        } else {
            $completedTest -> questions = $testQuestions;
        }
        //2. Shuffle answers inside questions
        foreach ($completedTest -> questions as $key => $question) {
            if ($this -> options['shuffle_answers']) {
                $question -> shuffle();
            }
            $completedTest -> questions[$key] = $question;
        }
        //3. Set questions in order
        if ($this -> options['shuffle_questions']) {
            $completedTest -> orderQuestions();
        }
        //4. Get additional information that might be useful
        $completedTest -> getUnit();
        $completedTest -> getLesson();
  //5. When redo only wrong, set it
  if ($recentlyCompleted -> redoOnlyWrong == true) {
   $completedTest -> correctPrevious = true;
  }
  //6. Store test
        $completedTest -> save();
        try {
         $lesson = new EfrontLesson($this ->test['lessons_ID']);
         $lesson_name = $lesson -> lesson['name'];
        } catch (EfrontLessonException $e) {
         $lesson_name = _SKILLGAPTESTS;
        }
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::TEST_START,
                "users_LOGIN" => $login,
                "lessons_ID" => $this ->test['lessons_ID'],
                "lessons_name" => $lesson_name,
                "entity_ID" => $this -> test['id'],
                "entity_name" => $this -> test['name']));
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
            $result = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "*", "status != '' and status != 'incomplete' and status != 'deleted' and users_LOGIN = '$login' and tests_ID=".($this -> test['id']), "id");
        } else {
            $result = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "*", "status != 'deleted' and users_LOGIN = '$login' and tests_ID=".($this -> test['id']), "id");
        }
        $timesDone = eF_getTableData("completed_tests", "count(*)", "users_LOGIN = '$login' and tests_ID=".($this -> test['id']), "id");
        $timesDone = $timesDone[0]['count(*)'];
        $status = '';
        $lastTest = false;
        $completedTest = '';
        $testIds = array();
        $timestamps = array();
        foreach ($result as $value) {
            if (!$id && $value['archive'] == 0) {
                $status = $value['status'];
                $completedTest = $value;
            } elseif ($id == $value['id']) {
                $status = $value['status'];
                $completedTest = $value;
            }
            if ($value['archive'] == 0) {
                $lastTest = $value['id'];
            }
            $testIds[] = $value['id'];
            $timestamps[] = $value['timestamp'];
   $testObject = unserialize($value['test']);
   $correctPrevious[] = $testObject -> correctPrevious;
        }
        if ($this -> options['redoable']) {
            $timesLeft = $this -> options['redoable'] - $timesDone;
        } else {
            $timesLeft = false;
        }
        $status = array('status' => $status,
                        'timesDone' => $timesDone,
                        'timesLeft' => $timesLeft,
                        'lastTest' => $lastTest,
                        'completedTest' => $completedTest,
                        'testIds' => $testIds,
                        'timestamps' => $timestamps,
      'correctPrevious' => $correctPrevious);
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

     * @param boolean $nocache Whether to skip caching this time

     * @since 3.5.0

     * @access public

     */
    public function toHTMLQuickForm(& $form = false, $questionId = false, $done = false, $editHandles = false, $nocache = false, $isFeedback = false) {
     $storeCache = false;
     if (!$questionId && !$done && !$this -> options['random_pool'] && !$this -> options['shuffle_questions'] && !$this -> options['shuffle_answers'] && !$nocache) {
      if ($testString = Cache::getCache('test:'.$this -> test['id'])) {
          return $testString;
      } else {
       $storeCache = true;
      }
     }
        $this -> getQuestions(); //Initialize questions information, it case it isn't
        if (!$form) {
            $form = new HTML_QuickForm("questionForm", "post", "", "", null, true); //Create a sample form
        }
        $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize()*1024);
        $allTestQuestions = $this -> getQuestions(true);
  //$allTestQuestionsFilter = $allTestQuestions;
  // lines added for redo only wrong questions
  $allTestQuestionsFilter = array();
  $resultCompleted = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "test", "archive=1 AND users_LOGIN='".$_SESSION['s_login']."' AND tests_ID=".$this -> test['id'], "timestamp desc");
  $recentlyCompleted = unserialize($resultCompleted[0]['test']);
  if ($recentlyCompleted -> redoOnlyWrong == true && !$done) {
   foreach ($recentlyCompleted -> questions as $key => $value) {
    if($value -> score != 100) {
     $value -> userAnswer = false;
     $allTestQuestionsFilter[$key] = $value;
    }
   }
   $allTestQuestions = $allTestQuestionsFilter;
  }
        // If we have a random pool of question then get a random sub-array of the questions
        if ($this -> options['random_pool'] > 0 && $this -> options['random_pool'] < sizeof($allTestQuestions)) {
   $rand_questions = array_rand($allTestQuestions, $this -> options['random_pool']);
            $testQuestions = array();
            foreach ($rand_questions as $question) {
                $testQuestions[$question] = $allTestQuestions[$question];
            }
        } else {
            $testQuestions = $allTestQuestions;
        }
        $questionId && in_array($questionId, array_keys($testQuestions)) ? $testQuestions = $testQuestions[$questionId] : null; //If $questionId is specified, keep only this question
        $this -> options['display_list'] ? $testString = '<style type = "text/css">span.orderedList{float:left;}</style>' : $testString = '<style type = "text/css">span.orderedList{display:none;}</style>';
        $count = 1;
        if ($this -> test['content_ID']) {
            //Get unit names and ids
            $content = new EfrontContentTree(key($this -> getLesson()));
            foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
                $units[$key] = $value['name'];
            }
        }
  $currentLesson = $this -> getLesson(true);
        foreach ($testQuestions as $id => $question) {
            if ($done) {
                switch ($question -> score) {
                    case '' :
                    case 0 : $image = 'error_delete.png'; $alt = _INCORRECTQUESTION; $title = _INCORRECTQUESTION; break;
                    case '100' : $image = 'success.png'; $alt = _QUESTIONISCORRECT; $title = _QUESTIONISCORRECT; break;
                    default : $image = 'semi_success.png'; $alt = _PARTIALLYCORRECTQUESTION; $title = _PARTIALLYCORRECTQUESTION; break;
                }
                if ($question -> pending) {
                    $image = 'exclamation.png';
                    $alt = _CORRECTIONPENDING;
                    $title = _CORRECTIONPENDING;
                }
            }
            $weight = round(10000 * $this -> getQuestionWeight($question -> question['id'])) / 100;
   if ($question -> time) {
             $timeSpent = eF_convertIntervalToTime($question -> question['estimate'] - $question -> time);
             $timeSpentString = '';
    $timeSpent['hours'] ? $timeSpentString .= $timeSpent['hours']._HOURSSHORTHAND.'&nbsp;' : null;
             $timeSpent['minutes'] ? $timeSpentString .= $timeSpent['minutes']._MINUTESSHORTHAND.'&nbsp;' : null;
             $timeSpent['seconds'] ? $timeSpentString .= $timeSpent['seconds']._SECONDSSHORTHAND.'&nbsp;' : null;
    $timeSpentString ? $timeSpentString = _TIMESPENT.': '.$timeSpentString : null;
   }
   //The hidden span below the div is used in a js down() so as to know which question we are looking at
            $testString .= '
              <div id = "question_'.$count.'" '.(!$done && $this -> options['onebyone'] ? 'style = "display:none"' : '').'>
                    <span id = "question_content_'.$question -> question['id'].'" style = "display:none">'.$question -> question['id'].'</span>
                    <table width = "100%">
                        <tr><td class = "questionWeight" style = "vertical-align:middle;">
        <span style = "float:right">'.$timeSpentString.'</span>';
   if(!$isFeedback) {
    $testString .= '<img src = "images/32x32/'.($done ? $image : 'unit.png').'" style = "vertical-align:middle" alt = "'.($done ? $alt : _QUESTION).'" title = "'.($done ? $title : _QUESTION).'"/>&nbsp;';
            }
   $testString .= '<span style = "vertical-align:middle;font-weight:bold">'._QUESTION.'&nbsp;'. ($count++).'</span>
                                '.($this -> options['display_weights'] || $done && !$isFeedback ? '<span style = "vertical-align:middle;margin-left:10px">('._WEIGHT.'&nbsp;'.$weight.'%)</span>' : '').'
                                '.($units[$question -> question['content_ID']] && $done ? '<span style = "vertical-align:middle;margin-left:10px">'._UNIT.' "'.$units[$question -> question['content_ID']].'"</span>' : '').'
        '.(($_SESSION['s_type'] == "student" && $currentLesson -> options['content_report'] == 1)? '<a href = "content_report.php?ctg=tests&edit_question='.$question -> question['id'].'&question_type='.$question -> question['type'].'&lessons_Id='.$_SESSION['s_lessons_ID'].'" onclick = "eF_js_showDivPopup(\''._CONTENTREPORT.'\', 1)" target = "POPUP_FRAME"><img src = "images/16x16/warning.png" border=0 style = "vertical-align:middle" alt = "'._CONTENTREPORT.'" title = "'._CONTENTREPORT.'"/></a>' : '').'
       </td></tr>
                    </table>';
   if ($done) {
    if ($this -> options['answers']) {
     $showCorrectAnswers = true;
    } else if ($this -> options['show_answers_if_pass'] && ($this->completedTest['status'] == 'passed' || $this->completedTest['status'] == 'completed')) {
     $showCorrectAnswers = true;
    } else {
     $showCorrectAnswers = false;
    }
    $testString .= $question -> toHTMLSolved(new HTML_QuickForm(), $showCorrectAnswers, $this -> options['given_answers']);
   } else {
    $testString .= $question -> toHTML($form);
   }
   $testString .= '<br/></div>';
            if ($done && !$isFeedback) {
                    $testString .= '
                        <table style = "width:100%" >
                            <tr><td>
                                <span style = "font-weight:bold;" id = "question_'.$id.'_score_span">
                                    '._SCORE.': <span style = "vertical-align:middle" id = "question_'.$id.'_score">'.$question -> score.'%</span>
                                    '.($editHandles ? '<a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_score_span\').hide();$(\'edit_question_'.$id.'_score_span\').show();"><img src = "images/16x16/edit.png" title = "'._CHANGESCORE.'" alt = "'._CHANGESCORE.'" style = "vertical-align:middle" border = "0"/></a>' : '').'
                                    <span id = "question_'.$id.'_pending">'.($question -> pending ? '&nbsp;('._THISQUESTIONCORRECTEDPROFESSOR.')' : '').'</span>
                                </span>
                                <span id = "edit_question_'.$id.'_score_span" style = "display:none;">
                                    <input type = "text" name = "edit_question_'.$id.'_score" id = "edit_question_'.$id.'_score" value = "'.$question -> score.'" style = "vertical-align:middle"/>
                                    <a href = "javascript:void(0)" onclick = "editQuestionScore(this, '.$id.')">
                                        <img src = "images/16x16/success.png" alt = "'._SUBMIT.'" title = "'._SUBMIT.'" border = "0" style = "vertical-align:middle"/>
                                    </a>
                                    <a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_score_span\').show();$(\'edit_question_'.$id.'_score_span\').hide();">
                                        <img src = "images/16x16/error_delete.png" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0" style = "vertical-align:middle"/>
                                    </a>
                                </span>
                                <span style = "border-left:1px solid black;margin-left:5px;padding-left:5px">'._SCOREINTEST.': <span id = "question_'.$id.'_score_coefficient">'.$question -> score.'</span>% &#215; '.$weight.' = <span id = "question_'.$id.'_scoreInTest">'.$question -> scoreInTest.'</span>%</span>
                            ';
                    if ($editHandles) {
                        $testString .= '
                            <span style = "border-left:1px solid black;margin-left:5px;padding-left:5px">';
                        if ($question -> feedback) {
                            $testString .= '
                                            <img src = "images/16x16/edit.png" alt = "'._EDITFEEDBACK.'" title = "'._EDITFEEDBACK.'" border = "0" style = "vertical-align:middle">
                                            <a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_feedback_div\').toggle();$(\'edit_question_'.$id.'_feedback_div\').toggle()">'._EDITFEEDBACK.'</a>';
                        } else {
                            $testString .= '
                                            <img src = "images/16x16/add.png" alt = "'._ADDFEEDBACK.'" title = "'._ADDFEEDBACK.'" border = "0" style = "vertical-align:middle">
                                            <a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_feedback_div\').toggle();$(\'edit_question_'.$id.'_feedback_div\').toggle()">'._ADDFEEDBACK.'</a>';
                        }
                        $testString .= '
                           </span>
                       </td></tr>
                       <tr><td>
                                    <div id = "question_'.$id.'_feedback_div" '.($question -> feedback ? 'class = "feedback_test"' : '').' >
                                        <span id = "question_'.$id.'_feedback">'.$question -> feedback.'</span>
                                    </div>
                                    <div id = "edit_question_'.$id.'_feedback_div" style = "display:none;">
                                        <textarea id = "edit_question_'.$id.'_feedback" style = "vertical-align:middle;width:90%;height:50px">'.$question -> feedback.'</textarea>
                                        <a href = "javascript:void(0)" onclick = "editQuestionFeedback(this, '.$id.')" style = "vertical-align:middle">
                                            <img src = "images/16x16/success.png" alt = "'._SUBMIT.'" title = "'._SUBMIT.'" border = "0" style = "vertical-align:middle" />
                                        </a>
                                        <a href = "javascript:void(0)" onclick = "$(\'question_'.$id.'_feedback_div\').toggle();$(\'edit_question_'.$id.'_feedback_div\').toggle()">
                                            <img src = "images/16x16/error_delete.png" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0" style = "vertical-align:middle" />
                                        </a>
                                    </div>
                            </td></tr>';
                    } else {
                        $testString .= '
                                    <div id = "question_'.$id.'_feedback_div" '.($question -> feedback ? 'class = "feedback_test"' : '').' >
                                        <span id = "question_'.$id.'_feedback">'.$question -> feedback.'</span>
                                    </div>';
                    }
                    $testString .= '
                            </table><br/>';
            }
        }
//pr($testQuestions);
        if (!$done && $this -> options['onebyone']) {
             $testString .= '
                         <table width = "100%">
                             <tr><td style = "text-align:center;vertical-align:middle;padding-top:50px">
                                 <img src = "images/32x32/arrow_left.png" alt = "'._PREVIOUSQUESTION.'" title = "'._PREVIOUSQUESTION.'" border = "0" id = "previous_question_button" onclick = "showTestQuestion(\'previous\')" style = "vertical-align:middle;margin-right:10px;'.($this -> options['only_forward'] ? 'visibility:hidden' : '').'" />
                                 <select id = "goto_question" name = "goto_question" style = "vertical-align:middle;'.($this -> options['only_forward'] ? 'display:none' : '').'" onchange = "showTestQuestion(this.options[this.selectedIndex].value)">';
             for ($i = 1; $i <= sizeof($testQuestions); $i++) {
                 $testString .= '
                                     <option value = "'.$i.'">'.$i.'</option>';
             }
             $testString .= '
                                 </select>&nbsp;
                                 <img src = "images/32x32/arrow_right.png" alt = "'._NEXTQUESTION.'" title = "'._NEXTQUESTION.'" border = "0" id = "next_question_button" onclick = "showTestQuestion(\'next\')" style = "vertical-align:middle"/>
                             </td></tr>
                         </table>';
             $testString .= "
                        <script>
                            var total_questions = ".sizeof($testQuestions).";
                            var current_question = ".($this -> currentQuestion ? $this -> currentQuestion : 1).";
                            //showTestQuestion(current_question);
                        </script>";
        }
        if (sizeof($this -> questions) > 0) {
   if ($this -> options['answer_all']) {
    $testString .= "
    <script>
    var force_answer_all = 1;
             translations['youhavenotcompletedquestions'] = '"._YOUHAVENOTCOMPLETEDTHEFOLLOWINGQUESTIONS."';
             translations['youhavetoanswerallquestions'] = '"._YOUHAVETOANSWERALLQUESTIONS."';</script>";
   } else {
    $testString .= "
    <script>
    var force_answer_all = 0;
             translations['youhavenotcompletedquestions'] = '"._YOUHAVENOTCOMPLETEDTHEFOLLOWINGQUESTIONS."';
             translations['areyousureyouwanttosubmittest'] = '"._AREYOUSUREYOUWANTTOSUBMITTEST."';</script>";
   }
  }
/*

        if ($this -> options['shuffle_questions'] && !$form -> isSubmitted()) {

            $form -> addElement("hidden", "answers_order", serialize($shuffleOrder));       //The questions' answers order is hold at a hidden element, so that it can be stored when the test is complete

        }

*/
        if ($storeCache) {
         Cache::setCache('test:'.$this -> test['id'], $testString);
        }
        return $testString;
    }
    /**

     * Display unsolved HTML version of test

     *

     * This function is used to display the HTML version of the unsolved test, along

     * with the count-down timer and the test  header.

     * <br/>Example:

     * <code>

     * $showTest = new EfrontTest(43, true);

     * echo $showTest -> toHTML($showTest -> toHTMLQuickForm())

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
     $str = '<script>
                    var questionHours = new Array();
                    var questionMinutes = new Array();
                    var questionSeconds = new Array();
                    var questionDuration = new Array();
                    var questionMin = new Array();
                    var questionSec = new Array();
                    var showtest=1;
     </script>';
            $str .= '
            <table class = "doneTestHeader">
                <tr><td id = "doneTestImage">
                        <img src = "images/32x32/tests.png" title = "'._TEST.'" alt = "'._TEST.'" />
                    </td>
                    <td>
                        <table class = "doneTestInfo">
                            <tr><td id = "testName">'.$this -> test['name'].'</td></tr>
                            <tr><td>'._NUMOFQUESTIONS.': '.($this -> options['random_pool'] ? min(sizeof($this -> getQuestions()), $this -> options['random_pool']) : sizeof($this -> getQuestions())).'</td></tr>
                            <tr><td>'.$this -> test['description'].'</td></tr>
                        </table>
                    </td></tr>
            </table>';
        if ($this -> options['duration']) {
            if (!$remainingTime) {
                $remainingTime = eF_convertIntervalToTime($this -> options['duration']);
            } else {
                $remainingTime = eF_convertIntervalToTime($remainingTime);
            }
/*

            $duration        = eF_convertIntervalToTime($this -> options['duration']);

            $durationString .= _TESTSHOULDCOMPLETEIN.' ';

            $duration['hours']   ? $durationString .= $duration['hours']._HOURSSHORTHAND.'&nbsp;'     : null;

            $duration['minutes'] ? $durationString .= $duration['minutes']._MINUTESSHORTHAND.'&nbsp;' : null;

            $duration['seconds'] ? $durationString .= $duration['seconds']._SECONDSSHORTHAND.'&nbsp;' : null;

            //$durationString .= '.';

*/
            $str .= '
                <table class = "doneTestHeader">
                 <tr><td rowspan = "2"><img src = "images/32x32/clock.png" title = "'._TIMELEFT.'" alt = "'._TIMELEFT.'"/>&nbsp;</td>
                        <td><span id = "time_left"></span>&nbsp;('._REMAININGTESTTIME.')</td></tr>
                    <tr><td>';
            foreach ($this -> questions as $question) {
             ($question instanceof Question) ? $question = $question -> question : null;
                           $str .= '<span id = "question_'.$question['id'].'_time_left" style = "display:none"></span>
                              <input type = "hidden" name = "question_time['.$question['id'].']" id = "question_'.$question['id'].'_time_left_input">';
            }
   $str .= '
                 </td></tr>
                </table>
                <script language = "JavaScript" type = "text/javascript">
                var timeup = "'._YOURTIMEISUP.'!";var remainingtime = "'._REMAININGQUESTIONTIME.'";
                function initTimer() {
                    hours = "'.$remainingTime['hours'].'";
                    minutes = "'.$remainingTime['minutes'].'";
                    seconds = "'.$remainingTime['seconds'].'";
                    duration = "'.$this -> options['duration'].'";
                ';
            if ($freeze) {
                $str .= '
                            min = minutes.toString();
                            sec = seconds.toString()
                            if (min.length == 1) {min = "0" + min;}
                            if (sec.length == 1) {sec = "0" + sec;}
                            $("time_left").update(hours + ":" + min + ":" + sec);
                        }';
            } else {
                $str .= '
                    var min = new String(3);
                    var sec = new String(3);
                    eF_js_printTimer();
                }';
            }
        }
        $str .= '
                </script>
                <table class = "formElements" style = "width:100%">
                    <tr><td colspan = "2">'.$testString.'</td></tr>
                </table>';
        return $str;
    }
}
/**

 * Class representing a completed test

 *

 * @package eFront

 */
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
    public $time = array('start' => '',
                         'end' => '',
                         'spent' => '',
                         'pause' => '',
                         'resume' => '');
    public $completedTest = array('id' => '',
                                  'login' => '',
                                  'archive' => '',
                                  'status' => '',
             'pending' => '',
                                  'testsId' => '',
                                  'score' => '',
                                  'feedback' => '');
    /**

     * Class constructor

     *

     * This class instantiates the object, based on an EfrontTest object and

     * the specified user

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
        $this -> test = $sourceTest -> test;
        $this -> options = $sourceTest -> options;
        $this -> completedTest['login'] = $login;
        $this -> completedTest['testsId'] = $this -> test['id'];
        if ($this -> options['duration']) {
            $this -> convertedDuration = eF_convertIntervalToTime($this -> options['duration']);
        }
    }
    /**

     * Get done test directory

     *

     * This function is used to get the done test's directory. This is the directory

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
        $this -> time['spent'] = $this -> time['spent'] + time() - $this -> time['resume']; //Add the time passed since the last pause to the total test time
        $this -> time['pause'] = time(); //Set the resume time to now
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
  $resultCompleted = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "test", "archive=1 AND users_LOGIN='".$_SESSION['s_login']."' AND tests_ID=".$this -> test['id'], "timestamp desc", "", "1");
  $recentlyCompleted = unserialize($resultCompleted[0]['test']);
  //Assign user answers to each question object, as a member
        foreach ($userAnswers as $id => $answer) {
            $this -> questions[$id] -> userAnswer = $answer;
        }
  if ($recentlyCompleted -> redoOnlyWrong == 1) {
   foreach ($recentlyCompleted -> questions as $key => $value) {
    if($value -> score == 100) {
     $this -> questions[$key] = $value;
    }
   }
  }
        //Correct each question and handle uploaded files, if any (@todo)
  $this -> completedTest['score'] = 0; //Added to check EC-73
        foreach ($this -> questions as $id => $question) {
         $results = $question -> correct(); //Get the results, which is the score and the right/wrong answers
         if ($question -> question['type'] == 'raw_text') {
          $question -> handleQuestionFiles($this -> getDirectory());
          if (!$question -> settings['force_correct'] || $question -> settings['force_correct'] == 'manual') {
     //changed to mark as pending the test again when redoOnlyWrong is set and this question was not 100% correct before	
     if ($results['score'] != 1) {
      $this -> completedTest['pending'] = 1;
      $question -> pending = 1;
     }
    } elseif ($question -> settings['force_correct'] == 'none' || $question -> settings['force_correct'] == 1) { //1 is for backwards compatibility
     $results['score'] = 1;
    }
            }
   $question -> score = round($results['score'] * 100, 2);
            $question -> results = $results['correct'];
            $this -> completedTest['score'] += $results['score'] * $this -> getQuestionWeight($id); //the total test score
            $question -> scoreInTest = round($question -> score * $this -> getQuestionWeight($id), 3); //Score in test is the question score, weighted with the question's weight in the test
        }
        $this -> completedTest['score'] > 1 ? $this -> completedTest['score'] = 100 : $this -> completedTest['score'] = round($this -> completedTest['score'] * 100, 2); //Due to roundings, overall score may go slightly above 100. so, truncate it to 100
        //Set the test status
        if ($this -> test['mastery_score'] && $this -> completedTest['score'] >= $this -> test['mastery_score']) {
            $this -> completedTest['status'] = 'passed';
        } else if ($this -> test['mastery_score'] && $this -> completedTest['score'] < $this -> test['mastery_score']) {
            $this -> completedTest['status'] = 'failed';
        } else {
            $this -> completedTest['status'] = 'completed';
        }
        $this -> time['spent'] = $this -> time['spent'] + time() - $this -> time['resume'];
        try {
         $lesson = new EfrontLesson($this ->test['lessons_ID']);
         $lesson_name = $lesson -> lesson['name'];
        } catch (EfrontLessonException $e) {
         $lesson_name = _SKILLGAPTESTS;
        }
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::TEST_COMPLETION,
                "users_LOGIN" => $this -> completedTest['login'],
                "lessons_ID" => $this ->test['lessons_ID'],
                "lessons_name" => $lesson_name,
                "entity_ID" => $this -> test['id'],
                "entity_name" => $this -> test['name']));
        if ($this -> options['duration'] && $this -> time['spent'] > $this -> options['duration']) {
            $this -> time['spent'] = $this -> options['duration']; //MAke sure that the spent time does not appear longer than the test duration
        }
        $this -> time['end'] = time();
        $this -> save(); //Save the test
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

     *

     * @since 3.5.2

     * @access public

     */
    public function save() {
        if ($this -> completedTest['id']) {
            $fields = array('test' => serialize($this),
                            'status' => $this -> completedTest['status'],
                            'timestamp' => time(),
                'time_start' => $this -> time['start'] ? $this -> time['start'] : null,
                'time_end' => $this -> time['end'] ? $this -> time['end'] : null,
                'time_spent' => $this -> time['spent'] ? $this -> time['spent'] : null,
                'pending' => $this -> completedTest['pending'] ? $this -> completedTest['pending'] : 0,
                'score' => $this -> completedTest['score'] ? $this -> completedTest['score'] : null);
            EfrontCompletedTest::updateCompletedTest("completed_tests", $fields, "id=".$this -> completedTest['id']);
            if ($this -> options['maintain_history'] !== '') {
          $result = eF_getTableDataFlat("completed_tests", "id", "status != 'incomplete' and status != 'paused' and users_LOGIN = '".$this -> completedTest['login']."' and tests_ID=".$this -> completedTest['testsId'], "timestamp desc");
          if (sizeof($result['id']) > $this -> options['maintain_history']) {
              $deleteThreshold = $result['id'][$this -> options['maintain_history']];
              EfrontCompletedTest::updateCompletedTest("completed_tests", array("test" => '', 'status' => 'deleted'), "status != 'incomplete' and status != 'paused' and users_LOGIN = '".$this -> completedTest['login']."' and tests_ID=".$this -> completedTest['testsId']." and id <= $deleteThreshold and id != ".$this -> completedTest['id']);
          }
            }
        } else {
         $fields = array('tests_ID' => $this -> completedTest['testsId'],
                            'users_LOGIN' => $this -> completedTest['login'],
                            'test' => serialize($this),
                            'status' => $this -> completedTest['status'],
                'time_start' => $this -> time['start'] ? $this -> time['start'] : null,
                'time_end' => $this -> time['end'] ? $this -> time['end'] : null,
                'time_spent' => $this -> time['spent'] ? $this -> time['spent'] : null,
                'pending' => $this -> completedTest['pending'] ? $this -> completedTest['pending'] : 0,
             'score' => $this -> completedTest['score'] ? $this -> completedTest['score'] : null);
         //$id = eF_insertTableData("completed_tests", $fields);
         $id = EfrontCompletedTest::storeCompletedTest("completed_tests", $fields);
         $this -> completedTest['id'] = $id;
         $this -> save();
        }
    }
    /**

     * Get potential score

     *

     * This function calculates the potential maximum score, when taking into account pending free text

     * questions. If there are no pending free-text questions in the test, potential score equals test score.

     * <br/>Example:

     * <code>

     * $completedTest -> getPotentialScore();

     * </code>

     *

     * @return float The potential score

     * @since 3.5.2

     * @access public

     */
    public function getPotentialScore() {
        foreach ($this -> questions as $id => $question) {
            if ($question -> pending) {
                $potentialScores[$id] = $this -> getQuestionWeight($id);
            }
        }
        if (sizeof($potentialScores) > 0) {
            $potentialTestScore = round(100 * array_sum($potentialScores), 2) + $this -> completedTest['score'];
        } else {
            $potentialTestScore = $this -> completedTest['score'];
        }
        if ($potentialTestScore > 100) {
         $potentialTestScore = 100;
        }
        return $potentialTestScore;
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
    public function toHTMLSolved($testString, $editHandles = false, $isFeedback = false) {
//      if (!$url) {
            $url = htmlspecialchars_decode(basename($_SERVER['PHP_SELF']).'?'.http_build_query($_GET));//$_SERVER['QUERY_STRING'];
//      }
        $parentTest = new EfrontTest($this -> test['id']);
        $currentStatus = $parentTest -> getStatus($this -> completedTest['login']); //Get the current test status, to check whether the student is undergoing the test right now
  $status = $parentTest -> getStatus($this -> completedTest['login'], $this -> completedTest['id'], true); //Get the completed tests status
        $potentialScore = $this -> getPotentialScore(); //Get the potential score for the test, taking into account pending questions
        $str = '
        <table class = "doneTestHeader">
            <tr><td id = "doneTestImage">';
        if ($this -> test['mastery_score'] && ($status['status'] == 'failed' || $status['status'] == 'pending')) {
            if ($potentialScore < $this -> test['mastery_score']) {
                $str .= '
                    <img src = "images/32x32/close.png" title = "'._FAILED.'" alt = "'._FAILED.'" id = "statusImage" />';
                $completeMessage = '<span class = "failure" id = "statusMessage">'._FAILED.'</span>';
            } else {
                $str .= '
                    <img src = "images/32x32/exclamation.png" title = "'._OUTCOMEPENDING.'" alt = "'._OUTCOMEPENDING.'" id = "statusImage" />';
                $completeMessage = '<span class = "pending" id = "statusMessage">'._OUTCOMEPENDING.'</span>';
            }
        } else {
            $str .= '
                <img src = "images/32x32/success.png" title = "'._PASSED.'" alt = "'._PASSED.'" id = "statusImage" />';
            if ($this -> test['mastery_score'] && $status['status'] == 'passed') {
                $completeMessage = '<span class = "success" id = "statusMessage">'._PASSED.'</span>';
            }
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
        $timeSpent = eF_convertIntervalToTime($this -> time['spent']);
        $completedString = ' '._ANDUSERDIDITIN.' ';
        $timeSpent['hours'] ? $completedString .= $timeSpent['hours']._HOURSSHORTHAND : null;
        $timeSpent['minutes'] ? $completedString .= $timeSpent['minutes']._MINUTESSHORTHAND.' ' : null;
        $timeSpent['seconds'] ? $completedString .= $timeSpent['seconds']._SECONDSSHORTHAND : null;
        if ($status['timesDone'] > 1 && $this -> options['maintain_history'] !== '0') {
            $jumpString = '
                '._JUMPTOEXECUTION.':
                <select style = "vertical-align:middle" onchange = "location.toString().match(/show_solved_test/) ? location = location.toString().replace(/show_solved_test=\d+/, \'show_solved_test=\'+this.options[this.selectedIndex].value) : location = location + \'&show_solved_test=\'+this.options[this.selectedIndex].value">';
            foreach ($status['testIds'] as $count => $testId) {
                $jumpString .= '<option value = "'.$testId.'" '.($this -> completedTest['id'] == $testId ? "selected" : "").'>#'.($count + 1).' - '.formatTimestamp($status['timestamps'][$count], 'time').' '.($status['correctPrevious'][$count] ? _TESTREDONE : null) .'</option>';
            }
            $jumpString .= '</select>';
        }
        $editHandlesString = '';
        if ($status['lastTest'] && ($status['timesLeft'] > 0 || $status['timesLeft'] === false)) {
            if (!$editHandles) {
    $editHandlesString .= '
                        <span id = "redoLink">
                            <img src = "images/16x16/undo.png" alt = "'._USERREDOTEST.'" title = "'._USERREDOTEST.'" border = "0" style = "vertical-align:middle">
                            <a href = "javascript:void(0)" id="redoLinkHref" onclick = "redoTest(this)" style = "vertical-align:middle">'._USERREDOTEST.'</a></span>';
   }
  }
   if ($this -> options['maintain_history'] !== '0') {
         $editHandlesString .= '
                        <span>
                            <img src = "images/16x16/arrow_right.png" alt = "'._TESTANALYSIS.'" title = "'._TESTANALYSIS.'" border = "0" style = "vertical-align:middle">
                            <a href = "'.$url.'&test_analysis=1" id="testAnalysisLinkHref" style = "vertical-align:middle">'._TESTANALYSIS.'</a></span>';
   }
        if ($editHandles) {
            if ($this -> completedTest['feedback']) {
                $editHandlesString .= '
                            <span>
                                <img src = "images/16x16/edit.png" alt = "'._EDITFEEDBACK.'" title = "'._EDITFEEDBACK.'" border = "0" style = "vertical-align:middle">
                                <a href = "javascript:void(0)" onclick = "$(\'test_feedback_div\').toggle();$(\'edit_test_feedback_div\').toggle()" style = "vertical-align:middle">'._EDITFEEDBACK.'</a></span>';
            } else {
                $editHandlesString .= '
                            <span>
                                <img src = "images/16x16/add.png" alt = "'._ADDFEEDBACK.'" title = "'._ADDFEEDBACK.'" border = "0" style = "vertical-align:middle">
                                <a href = "javascript:void(0)" onclick = "$(\'test_feedback_div\').toggle();$(\'edit_test_feedback_div\').toggle()" style = "vertical-align:middle">'._ADDFEEDBACK.'</a></span>';
            }
            $editHandlesString .= '
                            <span>
                                <img src = "images/16x16/printer.png" alt = "'._PRINT.'" title = "'._PRINT.'" border = "0" style = "vertical-align:middle">
                                <a id = "printLink" href = "'.$url.'&print=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup(\''._PRINT.'\', 2)" style = "vertical-align:middle">'._PRINT.'</a></span>
                <span>
                                <img src = "images/16x16/error_delete.png" alt = "'._RESETTESTSTATUS.'" title = "'._RESETTESTSTATUS.'" border = "0" style = "vertical-align:middle">
                                <a id = "deleteLink" href = "javascript:void(0)" onclick = "if (confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')) {deleteDoneTest(this)}" style = "vertical-align:middle">'._RESETTESTSTATUS.'</a></span>
                            <span>
                                <img src = "images/16x16/error_delete.png" alt = "'._RESETALLTESTSSTATUS.'" title = "'._RESETALLTESTSSTATUS.'" border = "0" style = "vertical-align:middle">
                                <a id = "deleteLink" href = "javascript:void(0)" onclick = "if (confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')) {deleteDoneTest(this, true)}" style = "vertical-align:middle">'._RESETALLTESTSSTATUS.'</a></span>';
        }
        $str .= '
                </td>
                <td>
                    <table class = "doneTestInfo">
                        <tr><td>'.$jumpString.'</td></tr>
                        <tr><td>'. ($isFeedback ? _FEEDBACKSTARTEDAT :_TESTSTARTEDAT).' '.formatTimestamp($this -> time['start'], 'time').' '._ANDCOMPLETEDAT.' '.formatTimestamp($this -> time['end'], 'time').'. '.$completedString.'.</td></tr>';
        if (!$isFeedback) {
   $str .= '<tr><td>
                                '._THETESTISDONE.' '.$status['timesDone'].' '._TIMES.'
                                '.($this -> options['redoable'] ? _ANDCANBEDONE.' '.($status['timesLeft'] > 0 ? $status['timesLeft'] : 0).' '._TIMESMORE : '').'
                            </td></tr>';
  }
        if ($currentStatus['status'] == 'incomplete') {
            $unsolvedTest = unserialize($currentStatus['completedTest']['test']);
            $str .= '
                        <tr><td style = "font-weight:bold">'._THEUSERUNDERGOINGTESTSTARTEDAT.':&nbsp;'.formatTimestamp($unsolvedTest -> time['start'], 'time').'</td></tr>';
        }
  if (!$isFeedback) {
   $str .= '
       <tr><td>
         <span style = "vertical-align:middle">'._TESTSCOREIS.':&nbsp;</span>';
   if ($editHandles) {
    $str .= '
         <span style = "font-weight:bold" id = "test_score_span">
          <span id = "test_score" style = "vertical-align:middle">'.$this -> completedTest['score'].'%&nbsp;</span>'.($potentialScore != $this -> completedTest['score'] ? '<span style = "vertical-align:middle" id = "potential_score">- '.$potentialScore.'%</span>' : null).'
          <a href = "javascript:void(0)" onclick = "$(\'test_score_span\').hide();$(\'edit_test_score_span\').show();">
           <img src = "images/16x16/edit.png" alt = "'._CHANGESCORE.'" title = "'._CHANGESCORE.'" border = "0" style = "vertical-align:middle"/>
          </a>
         </span>
         <span id = "edit_test_score_span" style = "display:none">
          <input type = "text" name = "edit_test_score" id = "edit_test_score" value = "'.$this -> completedTest['score'].'" style = "vertical-align:middle"/>
          <a href = "javascript:void(0)" onclick = "editScore(this)">
           <img src = "images/16x16/success.png" alt = "'._SUBMIT.'" title = "'._SUBMIT.'" border = "0" style = "vertical-align:middle"/>
          </a>
          <a href = "javascript:void(0)" onclick = "$(\'test_score_span\').show();$(\'edit_test_score_span\').hide();">
           <img src = "images/16x16/error_delete.png" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0" style = "vertical-align:middle"/>
          </a>
         </span>';
   } else {
    $str .= '
         <span id = "test_score" style = "vertical-align:middle">'.$this -> completedTest['score'].'%&nbsp;</span>'.($potentialScore != $this -> completedTest['score'] ? '<span style = "vertical-align:middle">- '.$potentialScore.'%</span>' : null);
   }
   $str .= '
        &nbsp;'.$completeMessage.'</td></tr>
       <tr><td><div class = "headerTools">'.$editHandlesString.'</div></td></tr>
       <tr><td>';
  }
        $str .= '
                            <div id = "test_feedback_div" '.($this -> completedTest['feedback'] ? 'class = "feedback_test"' : '').' >
                                <span id = "test_feedback">'.$this -> completedTest['feedback'].'</span>
                            </div>
                            <div id = "edit_test_feedback_div" style = "display:none;">
                                <textarea id = "edit_test_feedback" style = "vertical-align:middle;width:90%;height:50px">'.$this -> completedTest['feedback'].'</textarea>
                                <a href = "javascript:void(0)" onclick = "editFeedback(this)" style = "vertical-align:middle">
                                    <img src = "images/16x16/success.png" alt = "'._SUBMIT.'" title = "'._SUBMIT.'" border = "0" style = "vertical-align:middle" />
                                </a>
                                <a href = "javascript:void(0)" onclick = "$(\'test_feedback_div\').toggle();$(\'edit_test_feedback_div\').toggle()">
                                    <img src = "images/16x16/error_delete.png" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0" style = "vertical-align:middle" />
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
                url = "'.preg_replace("/&show_solved_test=\d+/", "", $url).'&ajax=1&redo_test='.$status['lastTest'].'";
                if ($("redo_progress_img")) {
                    $("redo_progress_img").writeAttribute("src", "images/others/progress1.gif").show();
                } else {
                    el.up().insert(new Element("img", {id:"redo_progress_img", src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                }
                new Ajax.Request(url, {
                    method:"get",
                    asynchronous:true,
                    onFailure: function (transport) {
                        $("redo_progress_img").writeAttribute({src:"images/16x16/error_delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("redo_progress_img"));
                        window.setTimeout(\'Effect.Fade("redo_progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("redo_progress_img").hide().setAttribute("src", "images/16x16/success.png");
                        new Effect.Appear($("redo_progress_img"));
                        window.setTimeout(\'Effect.Fade("redo_progress_img")\', 2500);
                        '.($editHandles ? 'window.setTimeout(\'Effect.Fade("redoLink")\', 2500);' : 'window.setTimeout(\'Effect.Fade("redoLink");location="'.preg_replace(array("/&show_solved_test=\d+/", "/&new_lesson_id=\d+/", "/&ctg=content/"), "", $url).'"\', 1000);').'
                    }
                });
            }
   function redoWrongTest(el) {
                Element.extend(el);
                url = "'.preg_replace("/&show_solved_test=\d+/", "", $url).'&ajax=1&redo_wrong_test='.$status['lastTest'].'";
                if ($("redo_progress_img")) {
                    $("redo_progress_img").writeAttribute("src", "images/others/progress1.gif").show();
                } else {
                    el.up().insert(new Element("img", {id:"redo_progress_img", src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                }
                new Ajax.Request(url, {
                    method:"get",
                    asynchronous:true,
                    onFailure: function (transport) {
                        $("redo_progress_img").writeAttribute({src:"images/16x16/error_delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("redo_progress_img"));
                        window.setTimeout(\'Effect.Fade("redo_progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("redo_progress_img").hide().setAttribute("src", "images/16x16/success.png");
                        new Effect.Appear($("redo_progress_img"));
                        window.setTimeout(\'Effect.Fade("redo_progress_img")\', 2500);
                        '.($editHandles ? 'window.setTimeout(\'Effect.Fade("redoWrongLink")\', 2500);' : 'window.setTimeout(\'Effect.Fade("redoWrongLink");location="'.preg_replace(array("/&show_solved_test=\d+/", "/&new_lesson_id=\d+/", "/&ctg=content/"), "", $url).'"\', 1000);').'
                    }
                });
            }
        </script>';
        if ($editHandles) {
            $str .= '
        <script>
            function deleteDoneTest(el, all) {
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
                        $("progress_img").writeAttribute({src:"images/16x16/error_delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img"));
                        window.setTimeout(\'Effect.Fade("progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                     if (window.location.toString().match("show_solved_test")) {
                         window.location = "'.basename($_SERVER['PHP_SELF']).'?ctg=tests&test_results='.$this -> test['id'].'";
                        } else {
                         parent.location.reload();
                        }
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
                        $("progress_img").writeAttribute({src:"images/16x16/error_delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img"));
                        window.setTimeout(\'Effect.Fade("progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("test_score").update($("edit_test_score").value + "%&nbsp;");
                        $("test_score_span").show();
                        $("edit_test_score_span").hide();
                        if (transport.responseText == "passed") {
                             $("statusMessage").update("'._PASSED.'").className = "success";
                             setImageSrc($("statusImage"), 32, "success");
                             //$("statusImage").src = "images/32x32/success.png";
                        } else if (transport.responseText == "failed") {
                            $("statusMessage").update("'._FAILED.'").className = "failure";
                            //$("statusImage").src = "images/32x32/close.png";
                            setImageSrc($("statusImage"), 32, "close");
                        } else if (transport.responseText == "pending") {
                            $("statusMessage").update("'._OUTCOMEPENDING.'").className = "pending";
                            //$("statusImage").src = "images/32x32/exclamation.png";
                            setImageSrc($("statusImage"), 32, "exclamation");
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
                        $("progress_img").writeAttribute({src:"images/16x16/error_delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img"));
                        window.setTimeout(\'Effect.Fade("progress_img")\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("test_feedback").update(transport.responseText);
                        transport.responseText ? $("test_feedback_div").toggle().className = "feedback_test" : $("test_feedback_div").toggle().className = "";
                        $("edit_test_feedback_div").toggle();
                        $("progress_img").hide().setAttribute("src", "images/16x16/success.png");
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
                        $("progress_img_"+id).writeAttribute({src:"images/16x16/error_delete.png", title:transport.responseText}).hide();
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
                             $("statusImage").src = "images/32x32/success.png";
                        } else if (transport.responseText.evalJSON().status == "failed") {
                            $("statusMessage").update("'._FAILED.'").className = "failure";
                            $("statusImage").src = "images/32x32/close.png";
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
                        $("progress_img_"+id).writeAttribute({src:"images/16x16/error_delete.png", title:transport.responseText}).hide();
                        new Effect.Appear($("progress_img_"+id));
                        window.setTimeout(\'Effect.Fade("progress_img_"+id)\', 10000);
                    },
                    onSuccess: function (transport) {
                        $("question_" + id + "_feedback").update(transport.responseText);
                        transport.responseText ? $("question_" + id + "_feedback_div").toggle().className = "feedback_test" : $("question_" + id + "_feedback_div").toggle().className = "";
                        $("edit_question_" + id + "_feedback_div").toggle();
                        $("progress_img_"+id).hide().setAttribute("src", "images/16x16/success.png");
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
        foreach ($this -> questions as $id => $question) {
         if ($question -> pending) {
          $this -> questions[$id] -> pending = 0;
          $this -> questions[$id] -> score = $this -> completedTest['score'];
         }
        }
        if ($this -> test['mastery_score'] && $this -> test['mastery_score'] > $this -> completedTest['score']) {
         $this -> completedTest['status'] = 'failed';
        } else if ($this -> test['mastery_score'] && $this -> test['mastery_score'] <= $this -> completedTest['score']) {
         $this -> completedTest['status'] = 'passed';
        }
        $this -> completedTest['pending'] = 0;
     $result = eF_getTableData("completed_tests", "archive", "id=".$this->completedTest['id']);
     if (!$result[0]['archive']) {
      $testUser = EfrontUserFactory::factory($this->completedTest['login']);
      if ($this -> completedTest['status'] == 'failed') {
       $testUser -> setSeenUnit($this->test['content_ID'], $this->test['lessons_ID'], 0);
      } else {
       $testUser -> setSeenUnit($this->test['content_ID'], $this->test['lessons_ID'], 1);
      }
     }
        $this -> save();
        echo $this -> completedTest['status'];
       } else {
        throw new EfrontTestException(_INVALIDSCORE.': '.$_GET['test_score'], EfrontTestException :: INVALID_SCORE);
       }
       exit;
      } else if (isset($_GET['test_feedback'])) {
       $this -> completedTest['feedback'] = $_GET['test_feedback'];
       $this -> save();
       echo $_GET['test_feedback'];
       exit;
      } else if (isset($_GET['redo_test']) && eF_checkParameter($_GET['redo_test'], 'id')) {
       $result = eF_getTableData("completed_tests", "tests_ID, users_LOGIN", "id=".$_GET['redo_test']);
       $test = new EfrontTest($result[0]['tests_ID']);
       $test -> redo($result[0]['users_LOGIN']);
       exit;
      } else if (isset($_GET['redo_wrong_test']) && eF_checkParameter($_GET['redo_wrong_test'], 'id')) {
       $result = eF_getTableData("completed_tests", "tests_ID, users_LOGIN", "id=".$_GET['redo_wrong_test']);
       $test = new EfrontTest($result[0]['tests_ID']);
       $test -> redoOnlyWrong($result[0]['users_LOGIN']);
       exit;
      } else if (isset($_GET['delete_done_test'])) {
       if (isset($_GET['all'])) {
        $this -> undo($this -> completedTest['login']);
        //eF_deleteTableData("completed_tests", "users_LOGIN='".$this -> completedTest['login']."' and tests_ID=".$this -> completedTest['testsId']);
       } else {
        $this -> undo($this -> completedTest['login'], $this -> completedTest['id']);
        //eF_deleteTableData("completed_tests", "id=".$this -> completedTest['id']);
       }
       exit;
      } else if (isset($_GET['question_score'])) {
       if (in_array($_GET['question'], array_keys($this -> questions))) {
        if (is_numeric($_GET['question_score']) && $_GET['question_score'] <= 100 && $_GET['question_score'] >= 0) {
         $this -> questions[$_GET['question']] -> score = $_GET['question_score'];
         $this -> questions[$_GET['question']] -> scoreInTest = round($_GET['question_score'] * $this -> getQuestionWeight($_GET['question']), 3);
         $this -> questions[$_GET['question']] -> pending = 0;
         $score = 0;
         foreach ($this -> questions as $question) {
          $this -> completedTest['scoreInTest'][$question -> question['id']] = $question -> scoreInTest;
          $score += $question -> scoreInTest;
         }
         $this -> completedTest['score'] = round($score, 2);
         $testUser = EfrontUserFactory::factory($this -> completedTest['login']);
         if ($this -> test['mastery_score'] && $this -> test['mastery_score'] > $this -> completedTest['score']) {
          if ($this -> getPotentialScore() < $this -> test['mastery_score']) {
           $this -> completedTest['status'] = 'failed';
           $testUser -> setSeenUnit($this -> test['content_ID'], $this -> test['lessons_ID'], 0);
          }
         } else if ($this -> test['mastery_score'] && $this -> test['mastery_score'] <= $this -> completedTest['score']) {
          $this -> completedTest['status'] = 'passed';
          $testUser -> setSeenUnit($this -> test['content_ID'], $this -> test['lessons_ID'], 1);
         }
         $this -> completedTest['pending'] = 0;
         foreach ($this -> getQuestions(true) as $question) {
          if ($question -> pending) {
           $this -> completedTest['pending'] = 1;
          }
         }
         $this -> save();
         echo json_encode($this -> completedTest);
        } else {
         throw new EfrontTestException(_INVALIDSCORE.': '.$_GET['test_score'], EfrontTestException :: INVALID_SCORE);
        }
       } else {
        throw new EfrontTestException(_INVALIDID.': '.$_GET['question'], EfrontTestException :: QUESTION_NOT_EXISTS);
       }
       exit;
      } else if (isset($_GET['question_feedback'])) {
       if (in_array($_GET['question'], array_keys($this -> questions))) {
        $this -> questions[$_GET['question']] -> feedback = $_GET['question_feedback'];
        $this -> save();
        echo $_GET['question_feedback'];
       } else {
        throw new EfrontTestException(_INVALIDID.': '.$_GET['question'], EfrontTestException :: QUESTION_NOT_EXISTS);
       }
       exit;
      } else if (isset($_GET['delete_file'])) {
       $file = new EfrontFile($_GET['delete_file']);
       $testDirectory = $this -> getDirectory();
       if (strpos($file['path'], $testDirectory['path']) !== false) {
        $file -> delete();
       }
       exit;
      }
     } catch (Exception $e) {
      handleAjaxExceptions($e);
     }
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
        if (isset($_GET['entity']) && ($_GET['entity'])) {
            $temp = $content -> seekNode($_GET['entity']);
            $tree[0] = new EfrontUnit(array('id' => 0, 'name' => _NOUNIT, 'active' => 1, $temp['id'] => $temp)); //Add a bogus unit to hold questions which do not belong to a unit
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($tree), RecursiveIteratorIterator :: SELF_FIRST));
        } else {
            $tree = $content -> tree;
            $tree[0] = new EfrontUnit(array('id' => 0, 'name' => _NOUNIT, 'active' => 1)); //Add a bogus unit to hold questions which do not belong to a unit
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content -> tree), RecursiveIteratorIterator :: SELF_FIRST));
        }
        foreach ($iterator as $key => $value) {
            if ($key != 0) {
                foreach ($content -> getNodeAncestors($key) as $id => $foo) {
                    $parentScores[$foo['id']]['score'] += $questionIds[$key]['score'];
                    $parentScores[$foo['id']]['total'] += $questionIds[$key]['total'];
                    $parentScores[$foo['id']]['correct'] += $questionIds[$key]['correct'];
                }
                $parentScores[$key]['this_score'] += $questionIds[$key]['score'];
                $parentScores[$key]['this_total'] += $questionIds[$key]['total'];
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
                                <img src = "images/16x16/information.png" style = "vertical-align:middle" alt = "" title = "'._THISLEVEL.': '.$parentScores[$id]['this_percentage'].'% ['.$value['this_correct'].'/'.$value['this_total'].']';
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
        $options['noclick'] = true;
        //$options['tree_root'] = array('name' => _BACKTOTOP, 'class' => 'examples', 'onclick' => "$('analysis_frame').src = $('analysis_frame').src.replace(/selected_unit=(\d*)/, 'selected_unit='+Element.extend(this).up().id.replace(/node/, ''));");
        //$options['onclick']   = "re = new RegExp(this.up().id.replace(/node/, ''), 'g');if(treeObj.getNodeOrders().match(re).length > 1) $('analysis_frame').src = $('analysis_frame').src.replace(/selected_unit=(\d*)/, 'selected_unit='+Element.extend(this).up().id.replace(/node/, ''));";
        $options['onclick'] = "showGraph($('proto_chart'), 'graph_test_analysis', Element.extend(this).up().id.replace(/node/, ''));";
        return array($parentScores, $content -> toHTML($iterator, false, $options));
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
        // SUB-COMPONENT 2: Make the skill-gap analysis
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
            if ($skill_item['score'] < $this->options['general_threshold']) { // TOCHANGE: with the threshold of each separate test
                $skills_missing[] = $skill_item['id'];
                $all_skills .= "&".$skill_item['id'] . "=1";
            } else {
                $all_skills .= "&".$skill_item['id'] . "=0";
            }
        }
        // This smarty variable will denote all missing and existing skills
        $analysisResults['missingSkills'] = $all_skills;
        $skills_missing = implode("','", $skills_missing);
        $user = EfrontUserFactory :: factory($this -> completedTest['login']);
        // SUB-COMPONENT 3: Propose lessons and courses
        $lessons_attending = implode("','", array_keys($user -> getLessons()));
        $analysisResults['lessons'] = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID","module_hcd_lesson_offers_skill.lesson_ID, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$lessons_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");
        $courses_attending = implode("','", array_keys($user -> getUserCourses()));
        $analysisResults['courses'] = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID","module_hcd_course_offers_skill.courses_ID, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.courses_ID NOT IN ('".$courses_attending."')","","module_hcd_course_offers_skill.courses_ID ORDER BY skills_offered DESC");
        return $analysisResults;
    }
    public static function retrieveCompletedTest($table, $fields = "*", $where = "", $order = "", $group = "", $limit = "") {
     $result = eF_getTableData($table, $fields, $where, $order, $group, $limit);
     foreach ($result as $key => $value) {
      if ($GLOBALS['configuration']['compress_tests'] && function_exists('gzinflate') && isset($value['test'])) {
       if ($inflated = gzinflate($value['test'])) {
        $result[$key]['test'] = $inflated;
       }
      }
     }
     return $result;
    }
    public static function storeCompletedTest($table, $fields) {
     if ($GLOBALS['configuration']['compress_tests'] && function_exists('gzdeflate') && isset($fields['test'])) {
   $fields['test'] = gzdeflate($fields['test']);
     }
     $id = eF_insertTableData($table, $fields);
     return $id;
    }
    public static function updateCompletedTest($table, $fields, $where) {
     if ($GLOBALS['configuration']['compress_tests'] && function_exists('gzdeflate') && isset($fields['test'])) {
   $fields['test'] = gzdeflate($fields['test']);
     }
     $id = eF_updateTableData($table, $fields, $where);
     return $id;
    }
    public static function compressTests() {
     if (function_exists('gzdeflate')) {
      $completedTests = eF_getTableData("completed_tests", "id", "test != ''");
      foreach ($completedTests as $value) {
       $result = eF_getTableData("completed_tests", "test", "id=".$value['id']);
       if (unserialize($result[0]['test'])) {
        eF_updateTableData("completed_tests", array("test" => gzdeflate($result[0]['test'])), "id=".$value['id']);
       }
      }
     }
    }
    public static function uncompressTests() {
     if (function_exists('gzinflate')) {
      $completedTests = eF_getTableData("completed_tests", "id", "test != ''");
      foreach ($completedTests as $value) {
       $result = eF_getTableData("completed_tests", "test", "id=".$value['id']);
       if (unserialize(gzinflate($result[0]['test']))) {
        eF_updateTableData("completed_tests", array("test" => gzinflate($result[0]['test'])), "id=".$value['id']);
       }
      }
     }
    }
}
/**

 * MultipleOneQuestion Class

 *

 * This class is used to manipulate a multiple choice / single answer question

 * @package eFront

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
            $index = $this -> order[$k]; //$index is used to reorder question options, in case it was shuffled
            $form -> addElement("radio", "question[".$this -> question['id']."]", $this -> options[$index], htmlspecialchars($this -> options[$index]), $index, "class = inputRadio"); //Add a radio for each option
            //$elements[$k]   = $form -> createElement("radio", "question[".$this -> question['id']."]", $this -> options[$index], $this -> options[$index], $index, "class = inputRadio");    //Add a radio for each option
            //$separators[] = "<br><span class = 'orderedList'>[".($k + 2)."]&nbsp;</span>";
        }
        //$form -> addGroup($elements, "question[".$this -> question['id']."]", "<span class = 'orderedList'>[1]&nbsp;</span>", $separators, false);        //Create a group with the above radio buttons
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
/*

        $questionString = '

                    <table class = "unsolvedQuestion">

                        <tr><td>'.$this -> question['text'].'</td></tr>

                        <tr><td>

                        		'.$formArray['question'][$this -> question['id']]['label'].$formArray['question'][$this -> question['id']]['html'].'

                            </td></tr>

                    </table>';

*/
        $questionString = '
                    <table class = "unsolvedQuestion multipleOneQuestion">
                        <tr><td>'.$this -> question['text'].' '.$this -> getCounter().'</td></tr>
                        <tr><td>';
        foreach ($formArray['question'][$this -> question['id']] as $key => $value) {
            $questionString .= "<br><span class = 'orderedList'>[".($key+1)."]&nbsp;</span>".$value['html'];
        }
        $questionString .= '
            </td></tr>
                    </table>';
        return $questionString;
    }
    /**

     * Display question with correct answer

     *

     * This function is used to display the question, together

     * with its correct answer.

     * <br/>Example:

     * <code>

     * $question = new MultipleOneQuestion(3);                                      //Instantiate question

     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form

     * echo $question -> preview($form);                               		        //Output solved question HTML code

     * </code>

     *

     * @param HTML_QuickForm $form The form to add fields to and display

     * @param boolean $questionStats

     * @return string The HTML code of the solved question

     * @since 3.6.0

     * @access public

     */
    public function preview(&$form, $questionStats = false, $hideAnswerStatus = false) {
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $form -> setDefaults(array("question[".$this -> question['id']."]" => null));
        $form -> freeze(); //Freeze the form elements
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '';
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $index = $this -> order[$k]; //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($this -> answer[0] == $index) {
    if (!$hideAnswerStatus) {
     $innerQuestionString .= '<span class = "correctAnswer">'.$formArray['question'][$this -> question['id']][$index]['label'];
    } else {
     $innerQuestionString .= '<span>'.$formArray['question'][$this -> question['id']][$index]['label'];
    }
                if ($questionStats[$this -> question['id']]['percent_per_option'][$index]) {
                 $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][$index] . "%)";
                } elseif ($questionStats !== false) {
                 $innerQuestionString .= "   (0%)";
                }
            } else {
    if (!$hideAnswerStatus) {
     $innerQuestionString .= '<span class = "wrongAnswer">'.$formArray['question'][$this -> question['id']][$index]['label'];
    } else {
     $innerQuestionString .= '<span>'.$formArray['question'][$this -> question['id']][$index]['label'];
    }
                if ($questionStats[$this -> question['id']]['percent_per_option'][$index]) {
                 $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][$index] . "%)";
                } elseif ($questionStats !== false) {
                 $innerQuestionString .= "   (0%)";
                }
            }
            $innerQuestionString .= '</span><br>';
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
                                '.$innerQuestionString.'
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        if ($showGivenAnswers && $this -> userAnswer !== false) { //If the user's given answers should be shown, assign them as defaults in the form
            $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        } else {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => null));
        }
  if ($showCorrectAnswers) {
   $correctAnswerClass = 'class = "correctAnswer"';
   $wrongAnswerClass = 'class = "wrongAnswer"';
  }
        $form -> freeze(); //Freeze the form elements
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '';
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $index = $this -> order[$k]; //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($this -> answer[0] == $index) {
                $innerQuestionString .= '<span '.$correctAnswerClass.' >'.$formArray['question'][$this -> question['id']][$index]['html'].'';
            } else {
                $innerQuestionString .= '<span '.$wrongAnswerClass.' >'.$formArray['question'][$this -> question['id']][$index]['html'].'';
            }
            if ($showGivenAnswers && $showCorrectAnswers && $this -> userAnswer == $index && $this->userAnswer != "") {
             $results['correct'] ? $innerQuestionString .= '&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._CORRECTANSWER : $innerQuestionString .= '&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._WRONGANSWER;
            }
            $innerQuestionString .= '</span>'.($this -> answers_explanation[$index] ? '<span class = "questionExplanation">'.$this -> answers_explanation[$index].'</span>' : '').'<br>';
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
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
        is_numeric($this -> answer[0]) && is_numeric($this -> userAnswer) && $this -> answer[0] == $this -> userAnswer ? $results = array('correct' => true, 'score' => 1) : $results = array('correct' => false, 'score' => 0);//We put the is_numeric() here, because the results might be strings or ints, which should be equal, or false or '', which are always wrong
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
        $order != false ? $this -> order = $order : null;
    }
}
/**

 * MultipleManyQuestion Class

 *

 * This class is used to manipulate a multiple choice / many answers question

 * @package eFront

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
            $index = $this -> order[$k]; //$index is used to reorder question options, in case it was shuffled
            //$elements[]   = $form -> createElement("advcheckbox", "question[".$this -> question['id']."][".$index."]", $this -> options[$index], $this -> options[$index], 'class = "inputCheckbox"', array(0, 1));
            //$separators[] = "<br><span class = 'orderedList'>[".($k + 2)."]&nbsp;</span>";
            $form -> addElement("advcheckbox", "question[".$this -> question['id']."][".$index."]", $this -> options[$index], htmlspecialchars($this -> options[$index]), 'class = "inputCheckbox"', array(0, 1));
            if ($this -> userAnswer !== false) {
                $form -> setDefaults(array("question[".$this -> question['id']."][".$index."]" => $this -> userAnswer[$index]));
            }
        }
        //$form -> addGroup($elements, "question[".$this -> question['id']."]", "<span class = 'orderedList'>[1]&nbsp;</span>", $separators, false);        //Create a group with the above checkboxes
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
/*

        $questionString = '

                    <table class = "unsolvedQuestion">

                        <tr><td>'.$this -> question['text'].'</td></tr>

                        <tr><td>

                                '.$formArray['question'][$this -> question['id']]['label'].$formArray['question'][$this -> question['id']]['html'].'

                            </td></tr>

                    </table>';

*/
        $questionString = '
                    <table class = "unsolvedQuestion multipleManyQuestion">
                        <tr><td>'.$this -> question['text'].' '.$this -> getCounter().'</td></tr>
                        <tr><td>';
        foreach ($formArray['question'][$this -> question['id']] as $key => $value) {
            $questionString .= "<br><span class = 'orderedList'>[".($key+1)."]&nbsp;</span>".$value['html'];
        }
        $questionString .= '
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        for ($k = 0; $k < sizeof($this -> options); $k++) {
            if ($showGivenAnswers) { //If the user's given answers should be shown, assign them as defaults in the form
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            } else {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => ''));
            }
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> freeze(); //Freeze the form elements
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '';
  if ($showCorrectAnswers) {
   $correctAnswerClass = 'class = "correctAnswer"';
   $wrongAnswerClass = 'class = "wrongAnswer"';
  }
  for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $index = $this -> order[$k]; //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($this -> answer[$index]) {
                $innerQuestionString .= '<span '.$correctAnswerClass.' >'.$formArray['question'][$this -> question['id']][$index]['html'];
            } else {
                $innerQuestionString .= '<span '.$wrongAnswerClass.' >'.$formArray['question'][$this -> question['id']][$index]['html'];
            }
            if ($showGivenAnswers && $showCorrectAnswers && $this -> userAnswer[$index]) {
             $results['correct'][$index] ? $innerQuestionString .= '&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._CORRECTANSWER : $innerQuestionString .= '&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._WRONGANSWER;
                //$innerQuestionString .= '<span class = "correctAnswer">&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._RIGHTANSWER.": ".($this -> answer[$k] ? _CHECKED : _NOTCHECKED)."</span>";
            }
            $innerQuestionString .= '</span>'.($this -> answers_explanation[$index] ? '<span class = "questionExplanation">'.$this -> answers_explanation[$index].'</span><br/>' : '').'<br>';
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
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
            $results['correct'][$i] = true; //Use this variable in order for the template to know how to color the answers (green/red)
            if (isset($this -> answer[$i]) && $this -> userAnswer[$i] == 1) {
                $nc++;
            } elseif (!isset($this -> answer[$i]) && $this -> userAnswer[$i] == 1) {
                $results['correct'][$i] = false; //Use this variable in order for the template to know how to color the answers (green/red)
                $nf++;
            } elseif (isset($this -> answer[$i]) && $this -> userAnswer[$i] == 0) {
                $results['correct'][$i] = false; //Use this variable in order for the template to know how to color the answers (green/red)
             //$nf++;			//Used for taking into account false questions as well
            } elseif (!isset($this -> answer[$i]) && $this -> userAnswer[$i] == 0) {
                //$nc++;			//Used for taking into account false questions as well
            }
        }
        $c = sizeof($this -> answer);
        $f = sizeof($this -> userAnswer) - sizeof($this -> answer);
        //$results['score'] = max(0, $nc / ($c+$f) - $nf / max($c, $f));			//Used for taking into account false questions as well
  if ($this -> settings['answers_logic'] == 'or' || $this -> settings['answers_or'] == 1) { //$this -> settings['answers_or'] == 1 is here for compatibility reasons
   $nc > 0 && $nf == 0 ? $results['score'] = 1 : $results['score'] = 0;
  } else if ($this -> settings['answers_logic'] == 'and') {
   $nc == $c && $nf == 0 ? $results['score'] = 1 : $results['score'] = 0;
  } else {
   $results['score'] = max(0, $nc / $c - $nf / max($c, $f));
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
        $order != false ? $this -> order = $order : null;
    }
    /**

     * Display question with correct answer

     *

     * This function is used to display the question, together

     * with its correct answer.

     * <br/>Example:

     * <code>

     * $question = new MultipleOneQuestion(3);                                      //Instantiate question

     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form

     * echo $question -> preview($form);                               		        //Output solved question HTML code

     * </code>

     *

     * @param HTML_QuickForm $form The form to add fields to and display

     * @param boolean $questionStats

     * @return string The HTML code of the solved question

     * @since 3.6.0

     * @access public

     */
    public function preview(&$form, $questionStats = false, $hideAnswerStatus = false) {
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        for ($k = 0; $k < sizeof($this -> options); $k++) {
            $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => ''));
        }
        $form -> freeze(); //Freeze the form elements
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '';
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $index = $this -> order[$k]; //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($this -> answer[$index]) {
    if (!$hideAnswerStatus) {
     $innerQuestionString .= '<span class = "correctAnswer">'.$formArray['question'][$this -> question['id']][$index]['label'];
    } else {
     $innerQuestionString .= '<span>'.$formArray['question'][$this -> question['id']][$index]['label'];
    }
                if ($questionStats[$this -> question['id']]['percent_per_option'][$index]) {
     if (!$hideAnswerStatus) {
      $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][$index] . "%)";
     } else { //means it is feeback temporary fix
      $innerQuestionString .= "   (". (100 - $questionStats[$this -> question['id']]['percent_per_option'][$index]) . "%)";
     }
                } elseif ($questionStats !== false) {
     if (!$hideAnswerStatus) {
      $innerQuestionString .= "   (0%)";
     } else { //means it is feeback temporary fix
      $innerQuestionString .= "   (100%)";
     }
                }
            } else {
    if (!$hideAnswerStatus) {
     $innerQuestionString .= '<span class = "wrongAnswer">'.$formArray['question'][$this -> question['id']][$index]['label'];
    } else {
     $innerQuestionString .= '<span>'.$formArray['question'][$this -> question['id']][$index]['label'];
    }
                if ($questionStats[$this -> question['id']]['percent_per_option'][$index]) {
                 if (!$hideAnswerStatus) {
      $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][$index] . "%)";
     } else {
      $innerQuestionString .= "   (". (100 - $questionStats[$this -> question['id']]['percent_per_option'][$index]) . "%)";
     }
                } elseif ($questionStats !== false) {
     if (!$hideAnswerStatus) {
      $innerQuestionString .= "   (0%)";
     } else {
      $innerQuestionString .= "   (100%)";
     }
                }
            }
            $innerQuestionString .= '</span><br>';
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
                                '.$innerQuestionString.'
                            </td></tr>
                    </table>';
        return $questionString;
    }
}
/**

 * TrueFalseQuestion Class

 *

 * This class is used to manipulate a true / false answers question

 * @package eFront

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
        //$elements[] = $form -> createElement("radio", "question[".$this -> question['id']."]", _FALSE, _FALSE, 0, "class = inputRadio");
        //$elements[] = $form -> createElement("radio", "question[".$this -> question['id']."]", _TRUE, _TRUE, 1, "class = inputRadio");
     $form -> addElement("radio", "question[".$this -> question['id']."]", _FALSE, _FALSE, 0, "class = inputRadio");
        $form -> addElement("radio", "question[".$this -> question['id']."]", _TRUE, _TRUE, 1, "class = inputRadio");
        //$form -> addGroup($elements, "question[".$this -> question['id']."]", null, "<br/>", false);
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
/*

        $questionString = '

                    <table class = "unsolvedQuestion">

                        <tr><td>'.$this -> question['text'].'</td></tr>

                        <tr><td>

                                '.$formArray['question'][$this -> question['id']]['html'].'

                            </td></tr>

                    </table>';

*/
        $questionString = '
                    <table class = "unsolvedQuestion trueFalseQuestion">
                        <tr><td>'.$this -> question['text'].' '.$this -> getCounter().'</td></tr>
                        <tr><td>
                                '.$formArray['question'][$this -> question['id']][1]['html'].'<br/>
                                '.$formArray['question'][$this -> question['id']][0]['html'].'
                            </td></tr>
                    </table>';
        return $questionString;
    }
    /**

     * Display question with correct answer

     *

     * This function is used to display the question, together

     * with its correct answer.

     * <br/>Example:

     * <code>

     * $question = new TrueFalseQuestion(3);                                      //Instantiate question

     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form

     * echo $question -> preview($form);                               		        //Output solved question HTML code

     * </code>

     *

     * @param HTML_QuickForm $form The form to add fields to and display

     * @param boolean $questionStats

     * @return string The HTML code of the solved question

     * @since 3.6.0

     * @access public

     */
    public function preview(&$form, $questionStats = false, $hideAnswerStatus = false) {
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $form -> setDefaults(array("question[".$this -> question['id']."]" => null));
        $form -> freeze(); //Freeze the form elements
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '';
        $innerQuestionString .= '<span class = "'.($this -> answer[0] == 0 ? 'correctAnswer' : 'wrongAnswer').'" >'.$formArray['question'][$this -> question['id']][0]['label'];
        if ($questionStats[$this -> question['id']]['percent_per_option'][0]) {
         $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][0] . "%)";
        } elseif ($questionStats !== false) {
         $innerQuestionString .= "   (0%)";
        }
        $innerQuestionString .= '<br><span class = "'.($this -> answer[0] == 1 ? 'correctAnswer':'wrongAnswer').'" >'.$formArray['question'][$this -> question['id']][1]['label'];
        if ($questionStats[$this -> question['id']]['percent_per_option'][1]) {
          $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][1] . "%)";
        } elseif ($questionStats !== false) {
         $innerQuestionString .= "   (0%)";
        }
        $innerQuestionString .= '</span> <br/>';
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
                                '.$innerQuestionString.'
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        $results['correct'] ? $class = 'correctAnswer' : $class = 'wrongAnswer';
        $form -> freeze(); //Freeze the form elements
        if ($showGivenAnswers && $this -> userAnswer !== false) { //If the user's given answers should be shown, assign them as defaults in the form
            $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        } else {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => null));
        }
        //$showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
     if ($showCorrectAnswers) {
   $innerQuestionString .= '<span class = "'.($this -> answer[0] == 1 ? 'correctAnswer' : 'wrongAnswer').'">'.$formArray['question'][$this -> question['id']][1]['html'];
        } else {
   $innerQuestionString .= '<span>'.$formArray['question'][$this -> question['id']][1]['html'];
  }
  if ($showGivenAnswers && $showCorrectAnswers && $this -> userAnswer == 1) {
         $results['correct'] ? $innerQuestionString .= '&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._CORRECTANSWER : $innerQuestionString .= '&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._WRONGANSWER;
        }
  if ($showCorrectAnswers) {
   $innerQuestionString .= '<br><span class = "'.($this -> answer[0] == 0 ? 'correctAnswer' : 'wrongAnswer').'">'.$formArray['question'][$this -> question['id']][0]['html'];
  } else {
   $innerQuestionString .= '<br><span>'.$formArray['question'][$this -> question['id']][0]['html'];
  }
  if ($showGivenAnswers && $showCorrectAnswers && $this -> userAnswer == 0) {
         $results['correct'] ? $innerQuestionString .= '&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._CORRECTANSWER : $innerQuestionString .= '&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._WRONGANSWER;
        }
        $innerQuestionString .= '</span> <br/>';
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
                            '.$innerQuestionString.'</td></tr>
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
        is_numeric($this -> answer) && is_numeric($this -> userAnswer) && $this -> answer == $this -> userAnswer ? $results = array('correct' => true, 'score' => 1) : $results = array('correct' => false, 'score' => 0);//We put the is_numeric() here, because the results might be strings or ints, which should be equal, or false or '', which are always wrong
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

 * @package eFront

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
        //$inputLabels  = explode('###', $this -> question['text']);
     $inputLabels = preg_split('/###(\d*)/', $this -> question['text']);
     preg_match_all('/###(\d*)/', $this -> question['text'], $matches);
        $questionText = '';
        for ($k = 0; $k < sizeof($this -> answer); $k++) {
         $alternatives = explode("|", $this->answer[$k]);
         shuffle($alternatives);
         $alternatives = array_combine($alternatives, $alternatives);
            $elements[] = $form -> addElement("static", null, null, $inputLabels[$k]);
            if (sizeof($alternatives) > 1 && $this -> settings['select_list']) {
          $alternatives = array('' => '') + $alternatives;
             //$elements[] = $form -> addElement("text", "question[".$this -> question['id']."][$k]", $inputLabels, 'style = "width:'.(200+(strlen($matches[0][$k])-3)*20).'px" autocomplete="off" onfocus = "startAutoCompleter(this, \''.$this->question['id'].'_'.$k.'\', \''.urlencode(json_encode($alternatives)).'\');" onclick = "startAutoCompleter(this, \''.$this->question['id'].'_'.$k.'\', \''.urlencode(json_encode($alternatives)).'\');"');
             $elements[] = $form -> addElement("select", "question[".$this -> question['id']."][$k]", $inputLabels, $alternatives, 'autocomplete="off" class = "emptySpacesField"');
            } else {
             $elements[] = $form -> addElement("text", "question[".$this -> question['id']."][$k]", $inputLabels, 'class = "emptySpacesField" style = "width:'.($matches[1][$k] ? $matches[1][$k] : 250).'px" autocomplete="off"');
            }
            if ($this -> userAnswer !== false) {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            }
        }
        $elements[] = $form -> addElement("static", null, null, $inputLabels[$k]);
        //$form -> addGroup($elements, "question[".$this -> question['id']."]", $inputLabels[0], null, false);
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
/*

        $questionString = '

                    <table class = "unsolvedQuestion">

                        <tr><td>

                                '.$formArray['question'][$this -> question['id']]['html'].'

                            </td></tr>

                    </table>';

*/
        $questionString = '
                    <table class = "unsolvedQuestion emptySpacesQuestion">
                     <tr><td>'.$this -> getCounter().'</td></tr>
                        <tr><td>';
        foreach ($formArray['question'][$this -> question['id']] as $key => $value) {
            $questionString .= $value['label'][$key].' '.$value['html'];
        }
        $questionString .= $value['label'][$key + 1].'
                            </td></tr>
                    </table>';
        return $questionString;
    }
   /**

     * Display question with correct answer

     *

     * This function is used to display the question, together

     * with its correct answer.

     * <br/>Example:

     * <code>

     * $question = new EmptySpacesQuestion(3);                                      //Instantiate question

     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form

     * echo $question -> preview($form);                               		        //Output solved question HTML code

     * </code>

     *

     * @param HTML_QuickForm $form The form to add fields to and display

     * @param boolean $questionStats

     * @return string The HTML code of the solved question

     * @since 3.6.0

     * @access public

     */
    public function preview(&$form, $questionStats = false, $hideAnswerStatus = false) {
        $inputLabels = preg_split('/###(\d*)/', $this -> question['text']);
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        for ($k = 0; $k < sizeof($this -> answer); $k++) {
            if ($showGivenAnswers) { //If the user's given answers should be shown, assign them as defaults in the form
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            } else {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => '###'));
            }
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> freeze(); //Freeze the form elements
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = $inputLabels[0];
        for ($k = 0; $k < sizeof($this -> answer); $k++) {
            $innerQuestionString .= '[<span class = "correctAnswer">'. $this -> answer [$k] ;
            if ($questionStats[$this -> question['id']]['percent_per_option'][$k]) {
          $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][$k] . "%)";
         } elseif ($questionStats !== false) {
          $innerQuestionString .= "   (0%)";
         }
         $innerQuestionString .= '</span>]' . $inputLabels[$k+1];
        }
        $innerQuestionString .= '<br/><br/>';
        $questionString = '
                    <table width = "100%" class = "solvedQuestion">
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
                                '.$innerQuestionString.'
                            </td></tr>
                        '.($this -> question['explanation'] && $explanation ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
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

     * @param boolean $showGivenAnswers Whether to show the given answers

     * @param boolean $explanation Whether to show the explanation

     * @return string The HTML code of the solved question

     * @since 3.5.0

     * @access public

     */
    public function toHTMLSolved(&$form, $showCorrectAnswers = true, $showGivenAnswers = true, $explanation = true) {
        $inputLabels = preg_split('/###(\d*)/', $this -> question['text']);
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        for ($k = 0; $k < sizeof($this -> answer); $k++) {
            if ($showGivenAnswers) { //If the user's given answers should be shown, assign them as defaults in the form
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            } else {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => '###'));
            }
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> freeze(); //Freeze the form elements
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        if ($showGivenAnswers) {
         $innerQuestionString = $inputLabels[0];
   if ($showCorrectAnswers) {
    $correctAnswerClass = 'class = "correctAnswer"';
    $wrongAnswerClass = 'class = "wrongAnswer"';
   }
         for ($k = 0; $k < sizeof($this -> answer); $k++) {
             //$showGivenAnswers && $showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display
             if ($results['correct'][$k]) {
                 $innerQuestionString .= '<span '.$correctAnswerClass.' >'.$formArray['question'][$this -> question['id']][$k]['html'].'</span>'.$inputLabels[$k + 1];
             } else {
                 $innerQuestionString .= '<span '.$wrongAnswerClass.' >'.$formArray['question'][$this -> question['id']][$k]['html'].'</span>'.$inputLabels[$k + 1];
             }
         }
         $innerQuestionString .= '<br/><br/>';
        }
        if ($showCorrectAnswers) {
            $innerQuestionString .= '<span class = "correctAnswer">'._RIGHTANSWER.':</span><br/>'.$inputLabels[0];
            for ($k = 0; $k < sizeof($this -> answer); $k++) {
              $formattedAnswer = explode("|", $this->answer[$k]);
              $formattedAnswer = $formattedAnswer[0];
                $innerQuestionString .= '<span class = "correctAnswer">'.$formattedAnswer.'</span>'.$inputLabels[$k + 1];
            }
        }
        $questionString = '
                    <table width = "100%" class = "solvedQuestion">
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
                                '.$innerQuestionString.'
                            </td></tr>
                        '.($this -> question['explanation'] && $explanation ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
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
        $factor = 1 / sizeof($this -> userAnswer); //If the question has 4 options, then the factor is 1/4.
        //pr($this -> userAnswer);exit;
        for ($i = 0; $i < sizeof($this -> userAnswer); $i++) {
         $userAnswer = mb_strtolower(trim($this -> userAnswer[$i]));
            //$this -> answer[$i] = explode("|", $this -> answer[$i]);
            $answers = explode("|", $this -> answer[$i]); //Create a copy so that mb_strtolower does not alter the original version
            array_walk($answers, create_function('&$v, $k', '$v = mb_strtolower(trim($v));'));
            if ($this -> settings['select_list']) {
             $answers = array_slice($answers, 0, 1);
            }
            $results['correct'][$i] = false;
            if (isset($this -> answer[$i])) {
             if (in_array($userAnswer, $answers)) {
                 $results['score'] += $factor;
                 $results['correct'][$i] = true; //Use this variable in order for the template to know how to color the answers (green/red)
             } else {
              foreach ($answers as $value) {
               $matches = array();
               if (preg_match('/^(.*)\*$/', $value, $matches) && mb_substr($userAnswer, 0, mb_strlen($matches[1])) == $matches[1]) {
                $results['score'] += $factor;
                $results['correct'][$i] = true; //Use this variable in order for the template to know how to color the answers (green/red)
               }
              }
             }
            }
            //$this -> answer[$i] = implode(" "._OR." ", $this -> answer[$i]);
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

 * @package eFront

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
        $options = $this -> options;
        array_walk($options, 'htmlspecialchars');
        for ($k = 0; $k < sizeof($this -> options); $k++) {
         $answers = array();
   $shar = array_keys($this -> answer);
   shuffle($shar);
         foreach ($this -> answer as $key => $value) {
          $answers[$shar[$key]] = $this -> answer[$shar[$key]];
         }
         array_walk($answers, 'htmlspecialchars');
         $index = $this -> order[$k]; //$index is used to reorder question options, in case it was shuffled
            $elements[] = $form -> addElement("static", null, null, $this -> options[$index]);
            $elements[] = $form -> addElement("select", "question[".$this -> question['id']."][".$index."]", $options, $answers);
            if ($this -> userAnswer !== false) {
                 $form -> setDefaults(array("question[".$this -> question['id']."][$index]" => $this -> userAnswer[$index]));
            }
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
/*

        $questionString = '

                    <table class = "unsolvedQuestion">

                        <tr><td>'.$this -> question['text'].'</td></tr>

                        <tr><td>

                                '.$formArray['question'][$this -> question['id']]['label'].$formArray['question'][$this -> question['id']]['html'].'

                            </td></tr>

                    </table>';

*/
        $questionString = '
                    <table class = "unsolvedQuestion matchQuestion">
                        <tr><td>'.$this -> question['text'].' '.$this -> getCounter().'</td></tr>
                        <tr><td>';
        foreach ($formArray['question'][$this -> question['id']] as $key => $value) {
            $questionString .= "<span class = 'orderedList'>[".($key + 1)."]&nbsp;</span>".$value['label'][$key].'&nbsp;&rarr;&nbsp;'.$value['html']."<br>";
        }
        $questionString .= '
                            </td></tr>
                    </table>';
        return $questionString;
    }
 /**

     * Display question with correct answer

     *

     * This function is used to display the question, together

     * with its correct answer.

     * <br/>Example:

     * <code>

     * $question = new MatchQuestion(3);                                      //Instantiate question

     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form

     * echo $question -> preview($form);                               		        //Output solved question HTML code

     * </code>

     *

     * @param HTML_QuickForm $form The form to add fields to and display

     * @param boolean $questionStats

     * @return string The HTML code of the solved question

     * @since 3.6.0

     * @access public

     */
    public function preview(&$form, $questionStats = false, $hideAnswerStatus = false) {
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => ''));
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> freeze(); //Freeze the form elements
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '<table width="5%">';
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
                                   //The question color must not change in case the user's answers should not display
            $index = $this -> order[$k]; //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            $innerQuestionString .= '<tr><td class = "correctAnswer" >'.$this -> options[$index].'</td><td>&nbsp;&rarr;&nbsp;</td><td class = "correctAnswer" >'.$this -> answer[$index] . '</td><td>';
            if ($questionStats[$this -> question['id']]['percent_per_option'][$k]) {
          $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][$k] . "%)";
         } elseif ($questionStats !== false) {
          $innerQuestionString .= "   (0%)";
         }
            $innerQuestionString .= '</td><td>'.($this -> answers_explanation[$index] ? '<span class = "questionExplanation">'.$this -> answers_explanation[$index].'</span>': '').'</td><td width="*">&nbsp;</td></tr>';
        }
        $innerQuestionString .= '</table>';
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
                                '.$innerQuestionString.'
                            </td></tr>
                        '.($this -> question['explanation'] ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            if ($showGivenAnswers) { //If the user's given answers should be shown, assign them as defaults in the form
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => $this -> userAnswer[$k]));
            } else {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => ''));
            }
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> freeze(); //Freeze the form elements
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '';
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            //$showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display
            if ($showCorrectAnswers) {
    $correctAnswerClass = 'class = "correctAnswer"';
    $wrongAnswerClass = 'class = "wrongAnswer"';
   }
   $index = $this -> order[$k]; //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($results['correct'][$index]) {
                $innerQuestionString .= '<span '.$correctAnswerClass.' >'.$this -> options[$index].'&nbsp;&rarr;&nbsp;'.$formArray['question'][$this -> question['id']][$index]['html'];
             if ($showCorrectAnswers) {
                 $innerQuestionString .= (!$showGivenAnswers ? ' (<span class = "emptyCategory">'._ANSWERNOTVISIBLE.'</span>) ' : '').'&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._CORRECTANSWER;
             }
            } else {
                $innerQuestionString .= '<span '.$wrongAnswerClass.' >'.$this -> options[$index].'&nbsp;&rarr;&nbsp;'.$formArray['question'][$this -> question['id']][$index]['html'];
             if ($showCorrectAnswers) {
                 $innerQuestionString .= (!$showGivenAnswers ? ' (<span class = "emptyCategory">'._ANSWERNOTVISIBLE.'</span>) ' : '').'&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._WRONGANSWER.'. '._RIGHTANSWER.": ".$this -> answer[$index];
             }
            }
            $innerQuestionString .= '</span>'.($this -> answers_explanation[$index] ? '<span class = "questionExplanation">'.$this -> answers_explanation[$index].'</span>': '').'<br/>';
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
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
        $factor = 1 / sizeof($this -> userAnswer); //If the question has 4 options, then the factor is 1/4.
        $answerKeys = array_keys($this -> answer);
        for ($i = 0; $i < sizeof($this -> userAnswer); $i++) {
            if ($this -> userAnswer[$i] == $answerKeys[$i] || $this -> answer[$this -> userAnswer[$i]] == $this -> answer[$i]) {
                $results['score'] += $factor;
                $results['correct'][$i] = true; //Use this variable in order for the template to know how to color the answers (green/red)
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
        $order != false ? $this -> order = $order : null;
    }
}
/**

 * RawTextQuestion Class

 *

 * This class is used to manipulate a raw text question

 * @package eFront

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
     global $load_editor;
  $load_editor = true;
        $elements[] = $form -> createElement("textarea", "question[".$this -> question['id']."]", null, 'class = "simpleEditor" style = "width:100%;height:100px;"');
        $elements[] = $form -> createElement("file", "file_".$this -> question['id'].'[0]', null, 'class = "inputText" id = "file_'.$this -> question['id'].'[0]" style = "display:none"');
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        foreach ($this -> files as $file) {
            try {
                $file = new EfrontFile($file);
                $filesString .= '<br/><span id = "file_'.$file['id'].'">'._UPLOADEDFILE.': <a href = "view_file.php?file='.$file['id'].'&action=download" style = "font-weight:bold">'.$file['name'].'</a>&nbsp;<a href = "javascript:void(0)" onclick = "deleteFile(this, '.$file['id'].')"><img src = "images/16x16/error_delete.png" title = "'._DELETE.'" alt = "'._DELETE.'" style = "vertical-align:middle" ></a></span>';
            } catch (Exception $e) {}
        }
        $questionString = '
                    <table class = "unsolvedQuestion rawTextQuestion">
                        <tr><td>'.$this -> question['text'].' '.$this -> getCounter().'</td></tr>
                        <tr><td>
                                '.$formArray['question'][$this -> question['id']]['html'].'<div></div>&nbsp;<img id = "add_another_'.$this -> question['id'].'" src = "images/16x16/add.png" alt = "'._ADDANOTHERFILE.'" title = "'._ADDANOTHERFILE.'" style = "display:none" onclick = "addAnotherFile(this)">
                        </td></tr>
                        <tr><td>
                                <a href = "javascript:void(0)" onclick = "Element.extend(this).hide();$(\'file_'.$this -> question['id'].'[0]\').show();$(\'add_another_'.$this -> question['id'].'\').show()">('._SENDFILEASANSWER.')</a>
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
                                    el.down().writeAttribute({src:"images/16x16/error_delete.png", title:transport.responseText}).hide();
                                    new Effect.Appear(el.down());
                                    window.setTimeout("Effect.Fade("+el.down().identify()+")", 10000);
                                },
                                onSuccess: function (transport) {
                                el.down().hide();
                                el.down().src = "images/16x16/success.png";
                                new Effect.Appear(el.down());
                                window.setTimeout("Effect.Fade(\'file_"+id+"\')", 1000);
                                }
                            });
                        }
                    </script>';
        return $questionString;
    }
 /**

     * Display question with correct answer

     *

     * This function is used to display the question, together

     * with its correct answer.

     * <br/>Example:

     * <code>

     * $question = new RawTextQuestion(3);                                      //Instantiate question

     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form

     * echo $question -> preview($form);                               		        //Output solved question HTML code

     * </code>

     *

     * @param HTML_QuickForm $form The form to add fields to and display

     * @param boolean $questionStats

     * @return string The HTML code of the solved question

     * @since 3.6.0

     * @access public

     */
    public function preview(&$form, $questionStats = false, $hideAnswerStatus = false) {
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $filesString = '';
        foreach ($this -> files as $file) {
            try {
                $file = new EfrontFile($file);
                $filesString .= '<br/><b>'._UPLOADEDFILE.': <a href = "view_file.php?file='.$file['id'].'&action=download">'.$file['name'].'</a></b>';
            } catch (Exception $e) {}
        }
        $results = $this -> correct(); //Correct question
        $form -> setDefaults(array("question[".$this -> question['id']."]" => ''));
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> freeze(); //Freeze the form elements
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $filesString = '';
        foreach ($this -> files as $file) {
            try {
                $file = new EfrontFile($file);
                $filesString .= '<br/><b>'._UPLOADEDFILE.': <a href = "view_file.php?file='.$file['id'].'&action=download">'.$file['name'].'</a></b>';
            } catch (Exception $e) {}
        }
        $results = $this -> correct(); //Correct question
        if ($showGivenAnswers) {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => $this -> userAnswer));
        } else {
            $form -> setDefaults(array("question[".$this -> question['id']."]" => ''));
        }
        //$renderer           = new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        //$form               -> freeze();                                           //Freeze the form elements
        //$form               -> accept($renderer);                                  //Render the form
        //$formArray           = $renderer -> toArray();                             //Get the rendered form fields
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle">
                                '.$this->userAnswer.'
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
  if ($this -> settings['force_correct'] == 'auto') {
   $strippedAnswer = strip_tags($this->userAnswer);
   $splitAnswerWords = preg_split("/\p{Z}|\p{P}|\n/m", $strippedAnswer, -1, PREG_SPLIT_NO_EMPTY);
   array_walk($splitAnswerWords, create_function('&$v', '$v=trim($v);'));
   $totalScore = 0;
   foreach($this -> settings['autocorrect'] as $value) {
    $addScore = false;
    foreach ($value['words'] as $word) {
     $word = preg_quote($word);//pr(htmlentities($this->userAnswer));
     $found = preg_match("/^$word$/mu", $strippedAnswer) ||
        preg_match("/^$word(\p{Z}|\p{P}|\n|\r)/mu", $strippedAnswer) ||
        preg_match("/(\p{Z}|\p{P}|\n|\r)$word$/mu", $strippedAnswer) ||
        preg_match("/(\p{Z}|\p{P}|\n|\r)$word(\p{Z}|\p{P}|\n|\r)/mu", $strippedAnswer);
     preg_match("/^$word(\p{Z}|\p{P}|\n|\r)/mu", $strippedAnswer, $matches);
     if ($found) {
      if ($value['contains']) {
       $addScore = true;
      }
     } else {
      if (!$value['contains']) {
       $addScore = true;
      }
     }
    }
    if ($addScore) {
     $totalScore +=$value['score'];
    }
   }
   if ($totalScore >= $this->settings['threshold']) {
    $results = array('correct' => '', 'score' => 1);
   } else {
    $results = array('correct' => '', 'score' => 0);
   }
  } elseif ($this -> score) {
            $results = array('correct' => '', 'score' => round($this -> score /100,2));
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
class DragDropQuestion extends Question implements iQuestion
{
/*

	public function __construct($question) {

		parent :: __construct($question);

		shuffle($this -> order);

	}

*/
    /**

     * Convert question to HTML_QuickForm

     *

     * This function is used to convert the question to HTML_QuickForm fields.

     * <br/>Example:

     * <code>

     * $question = new DragDropQuestion(3);                                        //Instantiate question

     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form

     * $question -> toHTMLQuickForm($form);                                         //Add fields to form

     * </code>

     *

     * @param HTML_QuickForm $form The form to add fields to

     * @since 3.5.0

     * @access public

     */
    public function toHTMLQuickForm(&$form) {
        //$random = range(0, sizeof($this -> answer) - 1);                                                   //$random is a temporary array used only for creating a random ordering
  //shuffle($random);
        $options = $this -> options;
        array_walk($options, 'htmlspecialchars');
        for ($k = 0; $k < sizeof($this -> options); $k++) {
            $index = $this -> order[$k]; //$index is used to reorder question options, in case it was shuffled
            $elements[] = $form -> addElement("text", "question[".$this -> question['id']."][".$index."]", $options[$index], 'style = "display:none" id = "drag_'.$this -> question['id'].'_'.$k.'"');
            //$elements[]   = $form -> addElement("static", "question[".$this -> question['id']."][".$index."]", );
            //$elements[] = $form -> addElement("static", null, null, $this -> answer[$random[$k]]);
            if ($this -> userAnswer !== false) {
                 $form -> setDefaults(array("question[".$this -> question['id']."][$index]" => $this -> userAnswer[$index]));
            } else {
             //$form -> setDefaults(array("question[".$this -> question['id']."][$index]" => htmlspecialchars($this -> answer[$k])));
             //$form -> freeze(array("question[".$this -> question['id']."][$index]"));
            }
        }
        //$form -> addGroup($elements, "question[".$this -> question['id']."]", "<span class = 'orderedList'>[1]&nbsp;</span>", $separators, false);
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

     * $question = new DragDropQuestion(3);                                        //Instantiate question

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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $random = range(0, sizeof($this -> answer) - 1); //$random is a temporary array used only for creating a random ordering
  shuffle($random);
        $questionString = "
        <script>
         if (typeof(dragDropQuestions) == 'undefined') {
          dragDropQuestions = new Array();
         }
         if (typeof(dragDropQuestionKeys) == 'undefined') {
          dragDropQuestionKeys = new Array();
         }
         dragDropQuestionKeys[".$this -> question['id']."] = new Array();
         dragDropQuestions.push(".$this -> question['id'].");
         //var questionId = '".$this -> question['id']."';
        </script>";
        $questionString .= '
                    <table class = "unsolvedQuestion dragDropQuestion" style = "width:auto">
                        <tr><td colspan = "3">'.$this -> question['text'].' '.$this -> getCounter().'</td></tr>';
        foreach ($formArray['question'][$this -> question['id']] as $key => $value) {
         $questionString .= "
            <tr><td style = 'width:30%;' class = 'droppable' id = 'secondlist_".$this -> question['id']."_$key'>
              <input type = 'hidden' value = '".$random[$key]."'>
              ".$formArray['question'][$this -> question['id']][$random[$key]]['label']."
              <script>dragDropQuestionKeys[".$this -> question['id']."].push($key);
               //Droppables.add('secondlist_".$this -> question['id']."_$key', {accept:'draggable', onDrop:handleDrop});
              </script>
             </td>
             <td style = 'width:20%;' class = 'dragDropTarget'></td>
             <td style = 'width:20%;height:100%;' id = 'source_".$this -> question['id']."_$key'>
              <div class = 'draggable' id = 'firstlist_".$this -> question['id']."_$key'>".$this -> answer[$key].$value['html']."</div>
                 <script>
                  //new Draggable('firstlist_".$this -> question['id']."_$key', {revert:'failure', onStart:handleDrag});
                 </script>
             </td>
            </tr>
            <tr><td colspan = '3' style = 'height:25px'></td></tr>";
        }
        $questionString .= '</table>';
        return $questionString;
    }
 /**

     * Display question with correct answer

     *

     * This function is used to display the question, together

     * with its correct answer.

     * <br/>Example:

     * <code>

     * $question = new DragDropQuestion(3);                                      //Instantiate question

     * $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);      //Create a form

     * echo $question -> preview($form);                               		        //Output solved question HTML code

     * </code>

     *

     * @param HTML_QuickForm $form The form to add fields to and display

     * @param boolean $questionStats

     * @return string The HTML code of the solved question

     * @since 3.6.0

     * @access public

     */
    public function preview(&$form, $questionStats = false, $hideAnswerStatus = false) {
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => ''));
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> freeze(); //Freeze the form elements
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '<table width="5%">';
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            $index = $this -> order[$k]; //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            //$innerQuestionString .= '<span class = "correctAnswer" style = "'.$style.'">'.$this -> options[$index].'&nbsp;<b>'.$formArray['question'][$this -> question['id']][$index]['html'].'</b>';
            $innerQuestionString .= '<tr><td class = "correctAnswer" >'.$this -> options[$index].'</td><td>&nbsp;&rarr;&nbsp;</td><td class = "correctAnswer" >'.$this -> answer[$index] . '</td><td>';
            if ($questionStats[$this -> question['id']]['percent_per_option'][$k]) {
          $innerQuestionString .= "   (". $questionStats[$this -> question['id']]['percent_per_option'][$k] . "%)";
         } elseif ($questionStats !== false) {
          $innerQuestionString .= "   (0%)";
         }
            $innerQuestionString .= '</td><td>'.($this -> answers_explanation[$index] ? '<span class = "questionExplanation">'.$this -> answers_explanation[$index].'</span>': '').'</td></tr>';
            //$innerQuestionString .= '</span>'.($this -> answers_explanation[$index] ? '<span class = "questionExplanation">'.$this -> answers_explanation[$index].'</span>' : '').'<br>';
        }
        $innerQuestionString .= "</table>";
        $questionString = '
                    <table width = "30%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px" width="30%">
                                '.$innerQuestionString.'
                            </td></tr>
                        '.($this -> question['explanation'] ? '<tr><td class = "questionExplanation">'._EXPLANATION.': '.$this -> question['explanation'].'</td></tr>' : '').'
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

     * $question = new DragDropQuestion(3);                                        //Instantiate question

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
        $this -> toHTMLQuickForm($form); //Assign proper elements to the form
        $results = $this -> correct(); //Correct question
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            if ($showGivenAnswers) { //If the user's given answers should be shown, assign them as defaults in the form
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => is_numeric($this -> userAnswer[$k]) ? $this -> answer[$this -> userAnswer[$k]] : ''));
            } else {
                $form -> setDefaults(array("question[".$this -> question['id']."][$k]" => ''));
            }
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($foo); //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        $form -> freeze(); //Freeze the form elements
        $form -> accept($renderer); //Render the form
        $formArray = $renderer -> toArray(); //Get the rendered form fields
        $innerQuestionString = '';
        for ($k = 0; $k < sizeof($this -> options); $k++) { //Display properly each option. The group can't be used, since we will display each option differently, depending on whether it is correct or not
            //$showCorrectAnswers ? $style = '' : $style = "color:black";                                          //The question color must not change in case the user's answers should not display
            if ($showCorrectAnswers) {
    $correctAnswerClass = 'class = "correctAnswer"';
    $wrongAnswerClass = 'class = "wrongAnswer"';
   }
   $index = $this -> order[$k]; //$index is used to recreate the answers order, for a done test, or to apply the answers shuffle, for an unsolved test
            if ($results['correct'][$index]) {
                $innerQuestionString .= '<span '.$correctAnswerClass.' >'.$this -> options[$index].'&nbsp;<b>'.$formArray['question'][$this -> question['id']][$index]['html'].'</b>';
             if ($showCorrectAnswers) {
                 $innerQuestionString .= (!$showGivenAnswers ? ' (<span class = "emptyCategory">'._ANSWERNOTVISIBLE.'</span>) ' : '').'&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._CORRECTANSWER;
             }
            } else {
                $innerQuestionString .= '<span '.$wrongAnswerClass.' >'.$this -> options[$index].'&nbsp;<b>'.$formArray['question'][$this -> question['id']][$index]['html'].'</b>';
             if ($showCorrectAnswers) {
                 $innerQuestionString .= (!$showGivenAnswers ? ' (<span class = "emptyCategory">'._ANSWERNOTVISIBLE.'</span>) ' : '').'&nbsp;&nbsp;&nbsp;&larr;&nbsp;'._WRONGANSWER.'. '._RIGHTANSWER.": ".$this -> answer[$index];
             }
            }
            $innerQuestionString .= '</span>'.($this -> answers_explanation[$index] ? '<span class = "questionExplanation">'.$this -> answers_explanation[$index].'</span>' : '').'<br>';
        }
        $questionString = '
                    <table width = "100%">
                        <tr><td>'.$this -> question['text'].'</td></tr>
                        <tr><td style = "vertical-align:middle;padding-bottom:10px">
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

     * $question = new DragDropQuestion(3);                                            //Instantiate question

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
        $factor = 1 / sizeof($this -> userAnswer); //If the question has 4 options, then the factor is 1/4.
        $answerKeys = $this -> answer;
        for ($i = 0; $i < sizeof($this -> userAnswer); $i++) {
            if ($this -> userAnswer[$i] == $answerKeys[$i] || $this -> answer[$this -> userAnswer[$i]] == $this -> answer[$i]) {
                $results['score'] += $factor;
                $results['correct'][$i] = true; //Use this variable in order for the template to know how to color the answers (green/red)
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

     * $question = new DragDropQuestion(3);                                        //Instantiate question

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
        $order != false ? $this -> order = $order : null;
    }
}
/**

 * Questions interface

 *

 * @package eFront

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

 * Class for questions

 *

 * @package eFront

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
    public static $questionTypes = array('empty_spaces' => _EMPTYSPACES,
                                         'raw_text' => _FREETEXT,
                                         'multiple_one' => _MULTIPLEONE,
                                         'multiple_many' => _MULTIPLEMANY,
                                         'match' => _MATCH,
                                         'true_false' => _TRUEFALSE,
                                         'drag_drop' => _DRAGNDROP);
    /**

     * The available question types icons

     *

     * @var array

     * @since 3.5.0

     * @access public

     */
    public static $questionTypesIcons = array('empty_spaces' => 'images/16x16/question_type_empty_spaces.png',
                                              'raw_text' => 'images/16x16/question_type_free_text.png',
                                              'multiple_one' => 'images/16x16/question_type_one_correct.png',
                                              'multiple_many' => 'images/16x16/question_type_multiple_correct.png',
                                              'match' => 'images/16x16/question_type_match.png',
                                              'true_false' => 'images/16x16/question_type_true_false.png',
                'drag_drop' => 'images/16x16/question_type_drag_drop.png');
    /**

     * The available question difficulties

     *

     * @var array

     * @since 3.5.0

     * @access public

     */
    public static $questionDifficulties = array('low' => _LOW,
                                                'medium' => _MEDIUM,
                                                'high' => _HARD,
               'very_high' => _VERYHARD);
    /**

     * The available question difficulties icons

     *

     * @var array

     * @since 3.5.0

     * @access public

     */
    public static $questionDifficultiesIcons = array('low' => 'images/16x16/flag_green.png',
                                                     'medium' => 'images/16x16/flag_blue.png',
                                                     'high' => 'images/16x16/flag_yellow.png',
                 'very_high' => 'images/16x16/flag_red.png');
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
 public $settings = array('force_correct' => 'manual',
        'answers_logic' => '');
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
        @unserialize($this -> question['answer']) !== false ? $this -> answer = unserialize($this -> question['answer']) : $this -> answer = $this -> question['answer'];
  @unserialize($this -> question['settings'])!== false ? $this -> settings = unserialize($this -> question['settings']): $this -> settings = $this -> question['settings'];
        is_array($this -> options) ? $this -> order = array_keys($this -> options) : null;
        $this -> question['type_icon'] = Question :: $questionTypesIcons[$this -> question['type']];
        $plainText = trim(strip_tags($this -> question['text']));
        if (mb_strlen($plainText) > self :: maxQuestionText) {
            $plainText = mb_substr($plainText, 0, self :: maxQuestionText).'...';
        }
        $this -> question['plain_text'] = $plainText;
        $this -> question['estimate_interval'] = eF_convertIntervalToTime($this -> question['estimate']);
        if ($this -> question['answers_explanation']) {
         $this -> answers_explanation = unserialize($this -> question['answers_explanation']);
        }
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
                        "content_ID" => $this -> question['content_ID'],
                        "lessons_ID" => $this -> question['lessons_ID'],
            "difficulty" => $this -> question['difficulty'],
                        "options" => $this -> question['options'],
                        "answer" => $this -> question['answer'],
            "estimate" => $this -> question['estimate'],
            "explanation" => $this -> question['explanation'],
            "answers_explanation" => $this -> question['answers_explanation'],
      "settings" => $this -> question['settings']);
        foreach ($this -> getTests() as $id => $test) {
         Cache::resetCache('test:'.$id);
        }
        $result = eF_updateTableData("questions", $fields, "id=".$this -> question['id']);
        return $result;
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
        $tests = array();
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
        $uploadedFiles = array();
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

     * Create question counter

     *

     * This function is used to print a small count-down counter for the question's remaining time

     * <br/>Example:

     * <code>

     * $counterString = $question -> getCounter();

     * </code>

     *

     * @return string The count-down counter code

     * @since 3.5.4

     * @access public

     */
    public function getCounter() {
     if ($this -> question['estimate']) {
      $timeInterval = $this -> question['estimate_interval'];
      $duration = $this -> question['estimate'];
      if (isset($this -> time)) {
       $timeInterval = eF_convertIntervalToTime($this -> time); //The time spent in this question
       $duration = $this -> time;
      }
      $counterStr = '
                <script language = "JavaScript" type = "text/javascript">
                 questionHours['.$this -> question['id'].'] = "'.$timeInterval['hours'].'";
                    questionMinutes['.$this -> question['id'].'] = "'.$timeInterval['minutes'].'";
                    questionSeconds['.$this -> question['id'].'] = "'.$timeInterval['seconds'].'";
                    questionDuration['.$this -> question['id'].'] = "'.$duration.'";
                    questionMin['.$this -> question['id'].'] = new String(3);
                    questionSec['.$this -> question['id'].'] = new String(3);
                    //eF_js_printQuestionTimer('.$this -> question['id'].');
    </script>';
      return $counterStr;
     } else {
      return false;
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
   EfrontSearch :: insertText(eF_addSlashes($question['text']), $newId, "questions", "title");
         return QuestionFactory :: factory($newId);
        } else {
            return false;
        }
    }
    /**

     * Clear duplicate questions

     *

     * There are times that the system may end up with duplicate questions, like when

     * copying content. This function is used to effectively eliminate duplicates.

     * <br/>Example:

     * <code>

     * Question :: clearDuplicates($currentLesson);

     * </code>

     *

     * @param mixed $lesson a lesson id or an EfrontLesson object

     * @access public

     * @static

     * @since 3.5.4

     */
    public static function clearDuplicates($lesson) {
     if ($lesson instanceOf EfrontLesson) {
      $lessonId = $lesson -> lesson['id'];
     } elseif (eF_checkParameter($lesson, 'id')) {
      $lessonId = $lesson;
     } else {
      throw new EfrontLessonException(_INVALIDID.": $lesson", EfrontLessonException :: INVALID_ID);
     }
     $result = eF_getTableData("questions", "*", "lessons_ID=".$lessonId, "id");
     foreach ($result as $value) {
   $id = $value['id'];
   unset($value['id']);
   unset($value['content_ID']);
      $checksums[$id] = md5(serialize($value));
     }
     $uniques = array_unique($checksums);
     $duplicates = array_diff_key($checksums, $uniques);
     foreach ($duplicates as $key => $value) {
      $original = array_search($value, $uniques);
      eF_updateTableData("tests_to_questions", array("questions_ID" => $original), "questions_ID=".$key);
      eF_deleteTableData("questions", "id=".$key);
     }
    }
}
/**

 * Factory class for instantiating question objects

 *

 * @package eFront

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
            case 'raw_text' : $factory = new RawTextQuestion($question); break;
            case 'multiple_one' : $factory = new MultipleOneQuestion($question); break;
            case 'multiple_many' : $factory = new MultipleManyQuestion($question); break;
            case 'empty_spaces' : $factory = new EmptySpacesQuestion($question); break;
            case 'match' : $factory = new MatchQuestion($question); break;
            case 'true_false' : $factory = new TrueFalseQuestion($question); break;
            case 'drag_drop' : $factory = new DragDropQuestion($question); break;
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

 * @package eFront

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
