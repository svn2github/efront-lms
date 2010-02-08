<?php

/*

 * Class defining the new module

 * Its name should be the same as the one provided in the module.xml file

 */
class module_questions_from_csv extends EfrontModule {
 // Private variables denoting the fields where values are stored
 private $mainLesson = 0;
 private $mainUnit = 1;
 private $questionOuterId = 2;
 private $questionType = 3;
 private $questionDifficulty = 4;
 private $questionAnswerTime = 5;
 private $questionText = 6;
 private $choicesLimits = array(7,8,9,10,11);
 private $correctChoicesLimits = array(12,13);
 private $emptySpacesAnswers = 14;
 private $sampleAnswer = 15;
 private $sampleAnswerFile = 16;
 private $questionFileLimits = array(17,18);
 private $otherLessonUnitsLimits = array(19,20,21,22,23,24);
 // Private variable denoting question types
 private $questionTypes = array ("?" => 'raw_text',
         "?1" => 'true_false',
         "?2" => 'multiple_one',
         "?3" => 'multiple_one',
         "?4" => 'multiple_one',
         "?5" => 'multiple_many',
         "?6" => 'multiple_many',
         "?7" => 'empty_spaces',
         "?" => 'raw_text',
         "A" => 'raw_text',
         "K1" => 'multiple_one',
         "K2" => 'multiple_one',
         "K3" => 'multiple_one',
         "K4" => 'multiple_one',
         "K5" => 'multiple_many',
         "K6" => 'multiple_many',
         "K7" => 'empty_spaces',
         "M" => 'raw_text',

         "\"A\"" => 'raw_text',
         "\"K1\"" => 'multiple_one',
         "\"K2\"" => 'multiple_one',
         "\"K3\"" => 'multiple_one',
         "\"K4\"" => 'multiple_one',
         "\"K5\"" => 'multiple_many',
         "\"K6\"" => 'multiple_many',
         "\"K7\"" => 'empty_spaces',
         "\"M\"" => 'raw_text'
 );

 // Private variable where lessons and units will be stored
 private $lessonUnits = array(); // used to check for existence of lessons
    /*

     * Mandatory function returning the name of the module

     * @return string the name of the module

     */
    public function getName() {
        return "CSV test question";
    }
    /*

     * Mandatory function returning an array of permitted roles from the set {"administrator", "professor", "student"}

     *

     * @return array of eFront user roles that this module applies for

     */
    public function getPermittedRoles() {
        return array("administrator", "professor");
    }
    public function isLessonModule() {
     return true;
    }
    /*

     * Function to be executed when the module is installed to an eFront system

     * Example implementation:

     *

     * public function onInstall() {

     *   return eF_executeNew("CREATE TABLE module_mymodule (

     *                    id int(11) NOT NULL auto_increment,

     *                    name text not null,

     *                    PRIMARY KEY  (id)

     *                   ) DEFAULT CHARSET=utf8;");

     * }

     * @return the result (true/false) of any module installation operations

     */
    public function onInstall() {
        return true;
    }
    /*

     * Function to be executed when the module is removed from an eFront system

     * Example implementation:

     *

     * public function onUninstall() {

     *   return eF_executeNew("DROP TABLE module_mymodule;");

     * }

     *

     * @return the result (true/false) of any removal operations

     */
    public function onUninstall() {
        return true;
    }
    /*

     * Left Sidebar Module Links

     * Get information in an array of arrays with fields:

     * 'menu': defines the menu(s) where links will appear "system" | "lessons" | "users" | "organization" | "tools" | "current_lesson" | "other"

     *         if "other" is selected then an additional "menuTitle" field can be defined for the Title of the menu

     *         -- multiple other menus may be defined - TODO

     * 'id': a unique id of the link within the module (and NOT within the entire eFront) framework. This id is used for link highlighting purposes

     *       with highlightLink()

     * 'title': the title to appear on the link

     * 'image': the image to appear next to the link (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName'

     * 'eFrontExtensions': you may optionally define two images for each link: one .png and .gif, which will appear under FF and IE respectively.

     *                     The filename (without the extension) and the path of the two pictures must be the same.

     *                     If 'eFrontExtensions' => 1, then do not use an extension to the image filename

     * 'link': the url of the page to be displayed in the main window

     *

     *  Example implementation:

     *

     *     public function getSidebarLinkInfo() {

     *             $link_of_menu_system = array   (array ('id'    => 'system_link_id',

     *                                                    'title' => 'System Related ModMenu 1',

     *                                                    'image' => '16x16/pens',                                 // no extension in the filename,

     *                                                    'eFrontExtensions' => '1',                               // question_type_free_text.png and pens.gif must exist in 16x16

     *                                                    'link'  => $this -> moduleBaseUrl . "&module_op=system_operation"),

     *                                             array ('id'    => 'system_link_id2',

     *                                                    'title' => 'System Related ModMenu 2',

     *                                                    'image' => '16x16/pencil2.png',

     *                                                    'link'  => $this -> moduleBaseUrl . "&module_op=system_operation"));

     *

     *             $link_of_module_menus  = array ( array ('id'    => 'other_link_id1',

     *                                                    'title' => 'Main Module',

     *                                                     'image' => $this -> moduleBaseDir . 'images/my_module_pic', // no extension in the filename

     *                                                     'eFrontExtensions' => '1',                                  // my_module_pic.gif and my_module_pic.png must exist in $this->moduleBaseDir . 'images/'

     *                                                     'link'  => $this -> moduleBaseUrl),

     *                                               array ('id'    => 'other_link_id2',

     *                                                     'title' => 'Second Module Page',

     *                                                     'image' => '16x16/attachment.png',

     *                                                     'link'  => $this -> moduleBaseUrl . '&module_operat=2'));

     *

     *             return array ( "system" => $link_of_menu_system,

     *                            "other"  => array('menuTitle' => 'My Module Menu', 'links' => $link_of_module_menus));

     *         }

     * @return array describing all module related menus that should appear on the left sidebar

     */
    public function getSidebarLinkInfo() {
        $link_of_module = array ( array ( 'id' => 'questions_from_xls',
                                                       'title' => _MODULE_QUESTIONS_TESTQUESTIONSUPLOADING,
                                                       'image' => $this -> moduleBaseLink . 'images/excel16.png', // no extension in the filename
                                                       'link' => $this -> moduleBaseUrl));
        $currentUser = $this ->getCurrentUser();
        if ($currentUser -> getType() == "administrator") {
         return array ( "lessons" => $link_of_module);
        } else {
         return array ( "current_lesson" => $link_of_module);
        }
    }
    /*

     * Get Navigational links for the top of the independent module page(s)

     * Get information in an array of sub-arrays with fields:

     * 'title': the title to appear on the link

     * 'image': the image to appear (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName' -TODO

     * 'link': the url of the page to be from this link

     * Each sub-array represents a different link. Between them the "&raquo;" character is automatically inserted by the system

     * Example implementation:

     *

     *  public function getNavigationLinks() {

     *          $currentUser = $this -> getCurrentUser();

     *          return array (array ('title' => _HOME, 'link'  => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),

     *                       array ('title' => _FAQ, 'link'  => $this -> moduleBaseUrl));

     *  }

     *

     * @return array describing the header navigational links for the module pages

     */
    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
        $currentLesson = $this -> getCurrentLesson();
        if ($currentLesson) {
            return array (array ('title' => _HOME, 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
                          array ('title' => _MODULE_QUESTIONS_TESTQUESTIONSUPLOADING, 'link' => $this -> moduleBaseUrl));
        } else {
            return array (array ('title' => _HOME, 'link' => $currentUser -> getRole() . ".php"),
                          array ('title' => _MODULE_QUESTIONS_TESTQUESTIONSUPLOADING, 'link' => $this -> moduleBaseUrl));
        }
    }
    /*

     * Get links to be highlighted

     * Each time a module independent page is displayed a different link of the left sidebar can be highlighted

     * To do this return the id of the corresponding link as defined by your getSidebarLinkInfo() returned array

     * Example implementation:

     *

     *         public function getLinkToHighlight() {

     *             if (isset($_GET['management'])) {

     *                 return 'other_link_id1';

     *             } else {

     *                 return 'other_link_id2';

     *             }

     *         }

     * @return the id of the left sidebar menu option that should be highlighted for each module page

     */
    public function getLinkToHighlight() {
        return 'questions_from_xls';
    }
 /*

	 * Checks if the inputted question has a valid type and returns the equivalent eFront type

	 */
    private function checkQuestionType($type) {
  return $this->questionTypes[trim($type)]; // if it does not exist "" will be returned
    }
 /*

	 * Checks if the inputted question belongs to an existing lesson and unit

	 */
    private function checkLessonUnit($lesson, $unit) {
  if (isset($this->lessonUnits[$lesson])) {
   // have we read the units for this lesson? if not create them now (only once in the entire procedure)
   if (!$this->lessonUnits[$lesson]['units']) {
    $this->lessonUnits[$lesson]['units'] = $this->lessonUnits[$lesson]['lessonObj']->getUnits();
   }
   if (isset($this->lessonUnits[$lesson]['units'][$unit])) {
    return true;
   } else {
    return false;
   }
  } else {
   return false;
  }
    }
 /*

	 * Checks if the inputted question has a valid value for difficulty and returns the corresponding eFront difficulty

	 */
    private function checkQuestionDifficulty($value) {
  if ($value == "") {
   return "medium";
  } else if ($value > 0 && $value < 5) {
   if ($value == 1) {
    return "low";
   } else if ($value == 4) {
    return "high";
   } else {
    return "medium";
   }
  } else {
   return -1;
  }
    }
 /*

	 * Checks if the inputted question time has a valid value

	 */
    private function checkQuestionTime($time) {
     if ($time) {
      $fixedTime = str_replace(",", ".", $time);
      if (preg_match('/[0-9]+(\.[0-9]+)?/', $fixedTime)) {
       return $fixedTime * 60;
      } else {
       return false;
      }
     } else {
      return false;
     }
  if ($value == "") {
   return 2;
  } else if ($value > 0 && $value < 4) {
   return $value;
  } else {
   return -1;
  }
    }
    private function separatorReplace($text, $separator, $debug = false) {
     $length = strlen($text);
     $quotesCount = 0;
     for ($i = 0; $i<$length; $i++) {
         if ($debug) {
             echo "*". $text[$i] . "*" . ($text[$i] == '"') . "*". ($text[$i] == ';') . "*" . $quotesCount . "<BR>";
         }
      if ($text[$i] == '"') {
          if ($quotesCount == 0) {
              $quotesCount = 1;
          } else {
              $quotesCount = 0;
          }
      } else if ($text[$i] == $separator) {
       if ($quotesCount == 1) {
           if ($debug) {
               echo "<BR>REPLACING<BR>";
           }
        //replace only if quotes are odd - this includes ;"..."; as well as ;"....""..."; scenarios
        $text = substr($text,0,$i) . "###TOKEN###" . substr($text,$i+1);
            $length = strlen($text);
       }
      }
     }
     return $text;
    }
    /*

     * Function that clears a text from superfluous spaces, opening and closing quotes

     */
    private function clearText($text) {
        $result = $text;
        $result = trim($result);
        $size = strlen($result);
        while ($result[0] == '"' && $result[$size-1] == '"') {
            $result = substr($result, 1, $size-2);
            $size -= 2;
        }
        // Removing the strange space-like prefix characters that are not removed by trim
        $i =0;
        $strange_char = ord($result[$i]);
        while ($result[$i] && ($strange_char == 194 || $strange_char == 160)) {
            $i++;
            $strange_char = ord($result[$i]);
        }
        if($i) {
            $result = substr($result, $i);
        }
  return trim($result);
    }
    private function notExistingQuestion($newQuestion, $current_questions) {
        $text = $newQuestion['text'];
        if (isset($current_questions[$text])) {
            $probably_existing = eF_getTableData("questions", "*", "id = '" . $current_questions[$text] . "'");
            foreach ($newQuestion as $key => $value) {
                if ($probably_existing[0][$key] != $value) {
                    //echo "true<BR>";
                    //pr($probably_existing);
                    //pr($newQuestion);
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }
    /*

     * This is the function for the php code of the MAIN module pages (namely the ones

     * called from the url:    $this->moduleBaseUrl . "&...."

     *

     * The global smarty variable may also be used here and in conjunction

     * with the getSmartyTpl() function, thus using php+smarty to display the page

     *

     * Rules:

     * - You are not allowed to use the $_GET['ctg'] and $_GET['op'] variables

     * - You should use the $this -> moduleBaseUrl variable to reference the module basic url

     * - You should use the $this -> moduleBaseDir variable to reference the module basic directory

     *

     * Tips:

     * - Use the $this -> getSmartyVar() function to utilize the global smarty variable.

     * - Use the $this -> setMessageVar($message, $message_type) function to export information to eFront users with header messages

     *

     * @return the result of any module operations in boolean form (true/false)

     */
    public function getModule() {
        $smarty = $this -> getSmartyVar();
        $currentUser = $this -> getCurrentUser();
        // The form definition
        $form = new HTML_QuickForm("import_hcd_data_form", "post", $this -> moduleBaseUrl, "", null, true);
        if ($currentUser -> getType() == "administrator") {
         $directionsTree = new EfrontDirectionsTree();
         $selectArray = $directionsTree ->toSelect(true,false, false); //return in HTML coloured format with SKILLGAPTESTS option and including questions number
         $smarty -> assign("T_LESSON_SELECT" , $selectArray);
        }
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
        // File to be uploaded
        $form -> addElement('file', 'hcd_file', _DATAFILE, 'class = "inputText"');
        $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB
        $form -> addRule('file', _YOUMUSTUPLOADFILE, 'uploadedfile', null, 'client');
        // Parameterization
        //$form -> addElement('text', 'login_column_title', _MODULE_QUESTIONS_LOGINCOLUMNTITLE, 'class = "inputText"');
         //$form -> addRule('login_column_title', _THEFIELD.' '._MODULE_QUESTIONS_LOGINCOLUMNTITLE.' '._ISMANDATORY, 'required', null, 'client');
        //$form  -> addElement('text', 'date_column_title', _MODULE_QUESTIONS_DATECOLUMNTITLE, 'class = "inputText"');
        $form -> addElement('select', 'hcd_ommit_users', _MODULE_QUESTIONS_HANDLINGFORNOTEXISTINGLOGINS, array("0" => _MODULE_QUESTIONS_OMMITRECORDSWHOSELOGINDOESNOTEXIST, "1" =>_MODULE_QUESTIONS_ADDNEWRECORDSWHOSELOGINDOESNOTEXIST), "");
        $form -> addElement('select', 'import_as', _MODULE_QUESTIONS_IMPORTINTO, array("0" => _MODULE_QUESTIONS_USERHISTORY, "1" => _MODULE_QUESTIONS_USEREVALUATIONS), "");
        $form -> addElement('advcheckbox', 'report_existing', _MODULE_QUESTIONS_REPORTALREADYEXISTINGQUESTIONS, null, 'class = "inputCheckbox"', array(0, 1));
  $form -> setDefaults(array("report_existing" => 1));
        $form -> addElement('submit', 'submit_hcd_import', _IMPORTDATA, 'class=flatButton');
        // On form sumbission
        if ($form -> isSubmitted()) {
            try {
             // A bit pessimistic...
             $message = _MODULE_QUESTIONS_NOQUESTIONSWEREINSERTED;
                $message_type = 'failure';
             // Upload csv file
             $uploadDir = $currentUser -> user['directory']."/temp";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755);
                }
                ($form -> exportValue('hcd_replace_users') == "replace") ? $replaceData = true : $replaceData = false;
                $filesystem = new FileSystemTree($currentUser -> user['directory']."/temp");
                $file = $filesystem -> uploadFile('hcd_file');
                if (!($file instanceof EfrontFile)) {
                    $file = new EfrontFile($file);
                }
                // Check if csv or zip version
                if ($file['extension'] == "csv") {
                 $questionFilesDir = false;
                } else if ($file['extension'] == "zip") {
                 $zip = new ZipArchive;
                 if ($zip -> open($file['path']) === TRUE) {
                     $zip -> extractTo($uploadDir . "/importedQuestions/");
                     $zip -> close();
                     $fs = new FileSystemTree($uploadDir . "/importedQuestions/");
                     $allFiles = $fs->getFlatTree();
                     foreach ($allFiles as $candidateQuestionFile) {
                      if ($candidateQuestionFile['extension'] == "csv") {
                       $file = new EfrontFile($candidateQuestionFile['path']);
                      }
                     }
                  $questionFilesDir = $uploadDir . "/importedQuestions/";
                 }
                } else {
     $this ->setMessageVar(_MODULE_QUESTIONS_WRONGINPUTFILETYPE, "failure");
     break;
                }
                // Initial file parsing
                $fileContents = file_get_contents($file['path']);
                // Replace inner semicolon with ###TOKEN### to make correct explode later
    //$fileContents = $this->separatorReplace($fileContents);
                $fileContents = explode("\r", trim($fileContents));
                $size = sizeof($fileContents);
                // Take the first column and find the titles
                // Suppose , separator
                $separator = ";";
                //$regex 		  = '/(.*)"(.*),(.*)"(.*)/U';
                $fields = explode($separator, trim($fileContents[0]));
                if (sizeof($fields) == 1) {
                    // If it returned only one field suppose , separator
                    $separator = ",";
                    //$regex 		  = '/(.*)"(.*);(.*)"(.*)/U';
                    // Correct explosion
                    $fields = explode($separator, $fileContents[0]);
                    if (sizeof($fields) == 1) {
                        throw new Exception (_UNKNOWNSEPARATOR, EfrontSystemException::ILLEGAL_CSV);
                    }
                }
/*

                $lessons = EfrontLesson::getLessons();

                foreach ($lessons as $lesson) {

                	$this->lessonUnits[$lesson['id']]['lessonObj'] = new EfrontLesson($lesson['id']);

                	// initialize to false to acquire value later only if needed

                	$this->lessonUnits[$lesson['id']]['units'] = false;

                	$this->lessonUnits[$lesson['id']]['directory'] = false;

                }

 */
                if ($currentUser -> getType() == "administrator") {
                 $lesson_form = $_POST['educational_criteria_row'];
                 $lesson_form = explode("_", $lesson_form);
                 if ($lesson_form[0] != "lesson") {
                  eF_redirect("".$this->moduleBaseUrl . "&message=".urlencode(_MODULE_QUESTIONS_PLEASESELECTALESSON)."&message_type=failure");
                  exit;
                 } else {
                  $lessons_ID = $lesson_form[1];
                 }
                } else {
                 $lesson = $this ->getCurrentLesson();
                 $lessons_ID = $lesson -> lesson['id'];
                }
                $questions = eF_getTableDataFlat("questions", "text, id","lessons_ID = $lessons_ID");
                $current_questions = array_combine($questions['text'], $questions['id']);
                $questions_to_add = array();
//pr($this->lessonUnits);
//pr($fileContents);
                ////  MAIN LOOP
                $correct = 0;
$faulty_lines = array();
/*

$faulty_lines[] = 474;

$faulty_lines[] = 475;

$faulty_lines[] = 316;

$faulty_lines[] = 324;

*/
                // Skipping the first lines
                for ($i = 4; $i < $size; $i++) {
if (in_array($i, $faulty_lines)) {
    echo "****************$i************";
    echo $this ->separatorReplace($fileContents[$i], $separator);
}
                 $csvQuestion = explode($separator, $this ->separatorReplace($fileContents[$i], $separator));
                 foreach ($csvQuestion as $key=>$field) {
                  $csvQuestion[$key] = str_replace("###TOKEN###", $separator, str_replace("'", "&#39;s", $field));
                     $csvQuestion[$key] = str_replace("\"\"", "\"", $csvQuestion[$key]);
                 }
if (in_array($i, $faulty_lines)) {
    pr($csvQuestion);
}
                 $newQuestion = array();
                    // Check input
                    $newQuestion['type'] = $this -> checkQuestionType($csvQuestion[$this->questionType]);
                    if (!$newQuestion['type']) {
                     if ($csvQuestion[$this->questionType] != "") {
                      $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_QUESTIONTYPEISWRONG . " (" .$csvQuestion[$this->questionType]. ")";
                     }
                     continue;
                    }
                    $newQuestion['difficulty'] = $this -> checkQuestionDifficulty($csvQuestion[$this->questionDifficulty]);
                    if (!$newQuestion['difficulty']) {
                     $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_QUESTIONDIFFICULTYISWRONG . " (" .$csvQuestion[$this->questionDifficulty]. ")";
                     continue;
                    }
                   //*
                    $csvQuestion[$this->mainLesson] = str_replace("\n","", $csvQuestion[$this->mainLesson]);
                   // if ($this->checkLessonUnit($csvQuestion[$this->mainLesson], $csvQuestion[$this->mainUnit])) {
                        // NOTE: KEEPING HERE INTERNAL LESSON IDS TO POINT TO CORRECT DIRECTORIES
                     $newQuestion['lessons_ID'] = $csvQuestion[$this->mainLesson];
                     $newQuestion['content_ID'] = $csvQuestion[$this->mainUnit];
                    //} else {
           // 	$messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " .  _MODULE_QUESTIONS_QUESTIONLESSONUNITDOESNOTEXIST;
                    //	continue;
                    //}
                    $newQuestion['estimate'] = $this -> checkQuestionTime($csvQuestion[$this->questionAnswerTime]);
                    if (!$newQuestion['estimate']) {
                     $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_WRONGQUESTIONTIME;
                     continue;
                    }
                    $newQuestion['text'] = $this -> clearText($csvQuestion[$this->questionText]);
                    if ($newQuestion['text'] == "") {
                     $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_NOQUESTIONTEXT;
                     continue;
                    } else {
                        $newQuestion['text'] = trim(htmlspecialchars($newQuestion['text']));
                    }
                    // MULTIPLE CHOICE PARSING
                    if ($newQuestion['type'] == "multiple_one") {
                     $newQuestion['options'] = array();
                     foreach ($this ->choicesLimits as $choice) {
                      $questionChoice = $this -> clearText($csvQuestion[$choice]);
                      if ($questionChoice != "") {
                       $newQuestion['options'][] = $questionChoice;
                      }
                     }
                     if (sizeof($newQuestion['options']) == 0) {
                      $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_NOOPTIONSDEFINEDFORMULTIPLECHOICE;
                      continue;
                     } else {
                      $newQuestion['options'] = serialize($newQuestion['options']);
                     }
                        foreach ($this ->correctChoicesLimits as $answer) {
                      if ($csvQuestion[$answer] != "") {
                       $correctAnswer = $csvQuestion[$answer]-1;
                       $newQuestion['answer'][0] = (string)$correctAnswer;
                      }
                     }
                     if (sizeof($newQuestion['answer']) == 0) {
                      $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORMULTIPLECHOICE;
                      continue;
                     } else {
                      $newQuestion['answer'] = serialize($newQuestion['answer']);
                     }
                    } else if ($newQuestion['type'] == "multiple_many") {
                     $newQuestion['options'] = array();
                     foreach ($this ->choicesLimits as $choice) {
                      $questionChoice = $this -> clearText($csvQuestion[$choice]);
                      if ($questionChoice != "") {
                       $newQuestion['options'][] = $questionChoice;
                      }
                     }
                     if (sizeof($newQuestion['options']) == 0) {
                      $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_NOOPTIONSDEFINEDFORMULTIPLECHOICE;
                      continue;
                     } else {
                      $newQuestion['options'] = serialize($newQuestion['options']);
                     }
                        foreach ($this ->correctChoicesLimits as $answer) {
                      if ($csvQuestion[$answer] != "") {
                       $correctAnswer = $csvQuestion[$answer]-1;
                       $newQuestion['answer'][$correctAnswer] = 1;
                      }
                     }
                     if (sizeof($newQuestion['answer']) == 0) {
                      $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORMULTIPLECHOICE;
                      continue;
                     } else {
                      $newQuestion['answer'] = serialize($newQuestion['answer']);
                     }
                    // RAW TEXT
                    } else if ($newQuestion['type'] == "raw_text") {
                     $newQuestion['answer'] = $this -> clearText($csvQuestion[$sampleAnswer]);
                     if ($csvQuestion[$this->sampleAnswerFile] != "") {
                      if ($questionFilesDir) {
        $questionDir = $questionFilesDir . $csvQuestion[$this->questionOuterId];
                       if (is_dir($questionDir) && is_file($questionDir . "/" . $csvQuestion[$this->sampleAnswerFile])) {
                        $questionFile = new EfrontFile($questionDir . "/" . $csvQuestion[$this->sampleAnswerFile]);
                        $questionFile->copy(G_LESSONSPATH . $newQuestion['lessons_ID']); // should exist due to previous lesson check
                        $newQuestion['answer'] = "<a href=\"" . G_RELATIVELESSONSLINK . $newQuestion['lessons_ID'] . $questionFile['name'] . "\">" . $questionFile['name'] . "</a>";
                       } else {
                                $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_SAMPLEANSWERFILENOTFOUND;
                        continue;
                       }
                      } else {
                       $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_UPLOADAZIPFILEIFYOUWANTTOUPLOADQUESTIONFILES;
                       continue;
                      }
                     }
                    // TRUE OR FALSE QUESTIONS
                    } else if ($newQuestion['type'] == "true_false") {
                     if ($csvQuestion[$answer] != "") {
                      $newQuestion['answer'][] = $csvQuestion[$answer] - 1;
                     }
                     if (sizeof($newQuestion['answer']) == 0 || ($csvQuestion[$answer] != 1 && $csvQuestion[$answer] != 2)) {
                      $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORTRUEFALSE;
                      continue;
                     } else {
                      $newQuestion['answer'] = serialize($newQuestion['answer']);
                     }
                    } else if ($newQuestion['type'] == "empty_spaces") {
                     $newQuestion['text'] = preg_replace('/__+/','###', $newQuestion['text']);
                     $newQuestion['text'] = preg_replace('/--+/','###', $newQuestion['text']);
                     $spaces = substr_count($newQuestion['text'], "###");
                     if ($csvQuestion[$this->emptySpacesAnswers] != "") {
                      $newQuestion['answer'] = explode("," , $csvQuestion[$this->emptySpacesAnswers]);
                      if (sizeof($newQuestion['answer']) != $spaces) {
                       $newQuestion['answer'] = explode("/" , $csvQuestion[$this->emptySpacesAnswers]);
                       if (sizeof($newQuestion['answer']) != $spaces) {
//pr($newQuestion);
//echo $spaces ."<Br>";
                        $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_WRONGAMOUNTOFANSWERSINEMPTYSPACES;
                        continue;
                       } else {
                        $newQuestion['answer'] = serialize($newQuestion['answer']);
                       }
                      } else {
                       $newQuestion['answer'] = serialize($newQuestion['answer']);
                      }
                     } else {
//pr($newQuestion);
//echo $spaces ."<Br>";
                      $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_NOANSWERSDEFINEDFOREMPTYSPACES;
                      continue;
                     }
                    }
                    foreach($this->questionFileLimits as $questionFile) {
                     if ($csvQuestion[$questionFile] != "") {
                      if ($questionFilesDir) {
        $questionDir = $questionFilesDir . $csvQuestion[$this->questionOuterId];
                       if (is_dir($questionDir) && is_file($questionDir . "/" . $csvQuestion[$questionFile])) {
                        $questionFileObj = new EfrontFile($questionDir . "/" . $csvQuestion[$questionFile]);
                        $questionFileObj->copy(G_LESSONSPATH . $newQuestion['lessons_ID']); // should exist due to previous lesson check
                        $newQuestion['text'] .= "<br><br><a href=\"" . G_RELATIVELESSONSLINK . $newQuestion['lessons_ID'] . "/". $questionFileObj['name'] . "\">" . $questionFileObj['name'] . "</a>";
                       } else {
                                $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_SAMPLEANSWERFILENOTFOUND;
                        continue;
                       }
                      } else {
                       $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_UPLOADAZIPFILEIFYOUWANTTOUPLOADQUESTIONFILES;
                       continue;
                      }
                     }
                    }
                    //users login
                    $text = $newQuestion['text'];
if (in_array($i, $faulty_lines)) {
    pr($newQuestion);
    echo $newQuestion['text'] ."<BR>";
    echo ord($newQuestion['text']) ."<BR>";
    echo ord($newQuestion['text'][0]) ."<BR>";
    echo ord($newQuestion['text'][1]) ."<BR>";
    echo ord($newQuestion['text'][2]) ."<BR>";
}
                    $newQuestion['lessons_ID'] = $lessons_ID;
                    $newQuestion['content_ID'] = 0;
                    if ($text != "") {
                        $not_existing = 0;
                        if ($this->notExistingQuestion($newQuestion, $current_questions)) {
                            $not_existing = 1;
                            //$questions_to_add[$i] = implode("','",$newQuestion);
                            //pr($newQuestion);
                            if ($newId = eF_insertTableData("questions",$newQuestion)) {
                             $correct++;
                            }
                            $current_questions[$text] = $newId;
                        } else {
                         if ($form -> exportValue('hcd_ommit_users') == "1") {
                             eF_deleteTableData("questions", "id = ". $current_questions[$text]);
                             if ($newId = eF_insertTableData("questions",$newQuestion)) {
                              $correct++;
                             }
                          if ($form -> exportValue('report_existing') == "1") {
                        $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_QUESTIONREPLACEDPREVIOUSEXISTING;
                             }
                             $current_questions[$text] = $newId;
                            } else {
                             if ($form -> exportValue('report_existing') == "1") {
                        $messages[$i] = _MODULE_QUESTIONS_LINE . " " . ($i+1). ": " . _MODULE_QUESTIONS_QUESTIONEXISTSALREADY;
                             }
                       continue;
                            }
                        }
                    }
                }
//pr($questions_to_add);
//pr($messages);
                $file -> delete(); // delete questions file
      $uploadDir = new EfrontDirectory($questionFilesDir);
    $uploadDir -> delete();
                if ($correct) {
                 /*

                    if ($form -> exportValue('hcd_ommit_users') == "1" && sizeof($logins_to_add) > 0) {

//                    eF_execute("INSERT INTO questions (type, difficulty, lessons_ID, content_ID, estimate, text, options, answer) VALUES ('". implode("'),('", $questions_to_add)."')");

//echo "INSERT INTO questions (type, difficulty, lessons_ID, content_ID, estimate, text, options, answer) VALUES ('". implode("'),('", $questions_to_add) ."')";

*/
                    $message = _TOTALINSERTED.' '.$correct.' '._MODULE_QUESTIONS_HISTORYRECORDS;
                    $message_type = 'success';
                }
                if (sizeof($messages) > 0) {
                    if ($message != "") {
                        $message .= ".<BR>";
                    }
                    /*

                    if ($form -> exportValue('hcd_ommit_users') == "1") {

                        $message     .= _MODULE_QUESTIONS_THEFOLLOWINGUSERSHAVEBEENINSERT.":<br>".implode("<br>", $messages);

                    } else {

					*/
                    $message .= _MODULE_QUESTIONS_THERECORDSHAVEBEENOMMITED.":<br><table><tr><td align='left'>".implode("<br>", $messages)."</td></tr></table>";
                }
             //echo $message . "----------"   ."<BR>";
             //eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_questions_from_csv&message=".$message."&message_type=".$message_type);
    $this ->setMessageVar($message, $message_type);
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $this ->setMessageVar($e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>', 'failure');
            }
        }
       //pr($messages);
        $form -> setDefaults(array('login_column_title' => "Login",
                                   'date_column_title' => "Date"));
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_HISTORY_XLS_IMPORT_FORM', $renderer -> toArray());
    }
    /*

     * This function is used to define a smarty template for the main module pages

     *

     * Attention: DO NOT define this function if you do not want to use smarty (and want to just create html with the php

     * code of the getModule() function)

     *

     * Example implementation:

     *

     *    public function getSmartyTpl() {

     *         // It is a good idea to define the two following smarty variables for inclusion of module images, libraries etc

     *         $smarty = $this -> getSmartyVar();

     *         $smarty -> assign("T_MYMODULE_MODULE_BASEDIR" , $this -> moduleBaseDir);

     *         $smarty -> assign("T_MYMODULE_MODULE_BASEURL" , $this -> moduleBaseUrl);

     *         return $this -> moduleBaseDir . "module.tpl";

     *     }

     * @return false or the string of the filename of the smarty template file for the module main pages

     */
    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_XLS_HISTORY_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_XLS_HISTORY_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_XLS_HISTORY_MODULE_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
    }
}
?>
