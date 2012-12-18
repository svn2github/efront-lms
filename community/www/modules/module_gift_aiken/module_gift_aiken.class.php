<?php

/*

 * Class defining the new module

 * Its name should be the same as the one provided in the module.xml file

 */
class module_gift_aiken extends EfrontModule {
    /*

     * Mandatory function returning the name of the module

     * @return string the name of the module

     */
    public function getName() {
        return "GIFT/AIKEN";
    }
    /*

     * Mandatory function returning an array of permitted roles from the set {"administrator", "professor", "student"}

     *

     * @return array of eFront user roles that this module applies for

     */
    public function getPermittedRoles() {
        return array("professor");
    }
    public function isLessonModule() {
        return true;
    }
 public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {
            return array('title' => _GIFTAIKENQUESTIONS,
                     'image' => $this -> moduleBaseDir . 'images/transform32.png',
                     'link' => $this -> moduleBaseUrl);
        }
    }
    public function getSidebarLinkInfo() {
        $link_of_menu_clesson = array (array ('id' => 'other_link_gift_aiken',
                                              'title' => _GIFTAIKENQUESTIONS,
                                              'image' => $this -> moduleBaseDir . 'images/transform16',
                                              'eFrontExtensions' => '1',
                                              'link' => $this -> moduleBaseUrl));
        return array ( "current_lesson" => $link_of_menu_clesson);
    }
    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
  $currentLesson = $this -> getCurrentLesson();
     $smarty = $this -> getSmartyVar();
     $links[] = array ('title' => _HOME, 'link' => $smarty->get_template_vars('T_HOME_LINK'));
     if ($currentLesson) {
      $links[] = array ('title' => $currentLesson->lesson['name'], 'link' => $_SERVER['PHP_SELF']);
     }
     $links[] = array ('title' => _GIFTAIKENQUESTIONSTITLE, 'link' => $this -> moduleBaseUrl);
        return $links;
    }
 public function getModuleJs() {
  return $this->moduleBaseDir."gift_aiken.js";
 }
 public function getLinkToHighlight() {
        return 'other_link_gift_aiken';
    }

    private function scanAIKEN($text) {
        $lines = explode("\n", $text);

        $questions = array();
        $waiting_for_new_question = 1;
        foreach($lines as $line) {
            if ($waiting_for_new_question) {

                if (trim($line) != "") {
                    $waiting_for_new_question = 0;
                    $new_question = array();
                    $new_question['text'] = $line;
                    $new_question['type'] = "multiple_one"; //all AIKEN questions are of this type only
                    $new_question['options'] = array();
                    $new_question['option_tags'] = array();
                }
            } else {
                if (trim($line) != "") {
                    if (preg_match('/^ANSWER:[ ]*(?P<option_tag>[a-zA-Z])/i', $line, $matches)) {
                        $correct_answer = $matches['option_tag'];

                        // Find the count of the correct answer
                        foreach ($new_question['option_tags'] as $count => $option_tag) {
                            if ($option_tag == $correct_answer) {
                                // Only a single correct answer
                                $new_question['answer'] = array("0" => "$count");
                                break;
                            }
                        }

                        if(!isset($new_question['answer'])) {
                            $new_question['type'] = "no_answer_error";
                        }

                        unset($new_question['option_tags']);
                        $questions[] = $new_question;
                        $waiting_for_new_question = 1;
                    } else {
                        //preg_match('/\1/')
                        preg_match('/(?P<option_tag>\w+)[.)] (?P<option>.*)/', $line, $matches);
                        $new_question['options'][] = trim($matches['option']);
                        $new_question['option_tags'][] = $matches['option_tag'];
                    }
                }

            }

        }
        return $questions;
    }

    private function replaceSpecialGiftChars($line) {
        $result = $line;

        $result = str_replace("\'", "'", $result);
        $result = str_replace('\"', '\"', $result);
        $result = str_replace("\\\~", "\\\||hyphen||", $result);
        $result = str_replace("\\\=", "\\\||equal||", $result);
        $result = str_replace("\\\#", "\\\||sharp||", $result);
        $result = str_replace("\\\{", "\\\||left_bracket||", $result);
        $result = str_replace("\\\}", "\\\||right_bracket||", $result);

        return $result;
    }

    private function replaceBackSpecialGiftChars($line) {
        $result = $line;
        $result = str_replace("\\\||hyphen||", "~", $result);

        $result = str_replace("\\\||equal||", "=", $result);
        //echo $result ."<BR>";
        $result = str_replace("\\\||sharp||", "#", $result);
        $result = str_replace("\\\||left_bracket||", "{", $result);
        $result = str_replace("\\\||right_bracket||", "}", $result);

        return $result;
    }

    private function replaceBackQuestion($question) {
         $question['text'] = $this -> replaceBackSpecialGiftChars($question['text']);
         foreach ($question['options'] as $key => $option) {
            $question['options'][$key] = $this -> replaceBackSpecialGiftChars($option);
         }
         foreach ($question['answer'] as $key => $answer) {
            $question['answer'][$key] = $this -> replaceBackSpecialGiftChars($answer);
         }

         if (isset($question['explanation'])) {
             $question['explanation'] = $this -> replaceBackSpecialGiftChars($question['explanation']);
         }
         return $question;
    }

    // Returns true if question1 has the same text, options and answer as question2
    private function sameQuestions($question1, $question2) {
        // Check the text
        if ($question1['text'] != $question2['text']) {
            return false;
        }

        // The text is the same, check the options
        $options1 = $question1['options'];
        $options2 = $question2['options'];

        $options1_count = sizeof($options1);
        $options2_count = sizeof($options2);

        if ($options1_count != $options2_count) {
            return false;
        } else {
            // Sort options to be certain that different order does not matte
            array_multisort($options1);
            array_multisort($options2);

            for ($i = 0 ; $i < $options1_count; $i++) {
                if ($options1[$i] != $options2[$i]) {
                    return false;
                }
            }
        }

        // For true/false questions
        $answer1 = $question1['answer'];
        $answer2 = $question2['answer'];

        $answer1_count = sizeof($answer1);
        $answer2_count = sizeof($answer2);

        if ($answer1_count != $answer2_count) {
            return false;
        } else {

            array_multisort($answer1);
            array_multisort($answer2);
            for ($i = 0 ; $i < $answer1_count; $i++) {
                if ($answer1[$i] != $answer2[$i]) {
                    return false;
                }
            }
        }

        // If control reached here then everything is equal
        return true;

    }

    private function removeDuplicates($questions) {
        $questions_count = sizeof($questions);
        for ($i = $questions_count-1 ; $i >= 0; $i--) {
            for ($j = 0; $j < $i; $j++) {
                if ($this -> sameQuestions($questions[$i], $questions[$j])) {
                    $questions[$i]['type'] = "same";
                    $questions[$i]['options'] = $j+1; // keep in the options the count of the same question (should always be the first
                                                         // from a series of same answers
                }
            }
        }
        return $questions;
    }

    private function scanGIFT($text) {
        $lines = explode("\n", $text);
        $question_lines = array();
        $questions_count = 0;
        $waiting_for_new_question = 1;

        // Preprocessing: Removing comments and discriminating input to different questions
        foreach ($lines as $key => $line) {
            if (preg_match('/^\/\/.*/', $line)) {
                unset($lines[$key]);
            } else {
                if ($waiting_for_new_question) {
                    if ($line != "" && $line != " " && $line != "\r") {
                        $question_lines[$questions_count] = $this -> replaceSpecialGiftChars($line) ." ";
                        $waiting_for_new_question = 0;
                    }
                } else {
                    if ($line != "" && $line != " " && $line != "\r") {
                        $question_lines[$questions_count] .= $this -> replaceSpecialGiftChars($line) ." ";
                    } else {
                        $questions_count++;
                        $waiting_for_new_question = 1;
                    }
                }
            }
        }

        // Each question line relates to a different question which we will analyse
        $questions = array();

        foreach ($question_lines as $question_text) {
            preg_match('/(?P<text_before>.*){(?P<question_specs>.*)}(?P<text_after>.*)/' ,$question_text, $matches);

            // Create basic text title - each question type has different format for the final title text (GIFT title allows ::__::__ or just __)
            if (preg_match('/::(?P<text_before1>.*)::(?P<text_before2>.*)/', $matches['text_before'], $matches_text_before)) {
                $matches['text_before'] = $matches_text_before['text_before1'] . ": ". $matches_text_before['text_before2'];
            }

            // Get signs and options
            $specs_length = strlen($matches['question_specs']);
            $signs = array();
            $options = array();
            $temp = "";
            $found_sign = 0;
            $disregard = 0;
            for($i = 0; $i < $specs_length; $i++) {
                if ($matches['question_specs'][$i] == "=" || $matches['question_specs'][$i] == "~") {
                    if ($temp != "") {
                        $options[] = $temp;
                        $temp = "";
                    }
                    $signs[] = $matches['question_specs'][$i];
                    $found_sign = 1;
                } else if ($found_sign) {
                    $temp .= $matches['question_specs'][$i];
                }
            }

            if ($temp != "") {
                $options[] = $temp;
            }

            // Find any explanations in the option text
            $explanations = array();
            $options_length = sizeof($options);
            for($i = 0; $i < $options_length; $i++) {
                $temp = explode("#", $options[$i]);
                if ($temp[0] == "") { // numerical answer
                    $signs[$i] = "=";
                    $options[$i] = trim($temp[1]);
                } else if ($temp[1] != "") {
                    // both 1880:0 and %33%1880:1 are acceptable - all but the main one are considered false
                    if (preg_match('/^%[0-9]+%(?P<main_question>.*)/',$temp[0],$internal_match)) {
                        $temp[0] = $internal_match['main_question'];
                        $signs[$i] = "~";
                    }

                    if (preg_match('/(?P<main_question>[0-9]*):[0-9]+/',$temp[0],$internal_match)) {
                        $temp[0] = $internal_match['main_question'];
                    }

                    $options[$i] = $temp[0];

                    $explanations[$i] = $options[$i] . ": " . trim($temp[1]);
                }
            }

            // Create question according to signs and options
            $question = array();
            if (empty($signs)) {
                // Either {} or {T|TRUE|F|FALSE}
                if (trim($matches['question_specs']) == "") {
                    $question['type'] = "raw_text";
                    $question['text'] = $matches['text_before'] . " " . $matches['text_after'];
                } else {
                    $question['type'] = "true_false";

                    // The explanation part for true false questions is extracted here
                    $temp = explode("#", $matches['question_specs']);
                    $matches['question_specs'] = trim($temp[0]);

                    if ($matches['question_specs'] == "T" || $matches['question_specs'] == "TRUE") {
                        $question['answer'] = "1";
                        $is_true = 1;
                    } else if ($matches['question_specs'] == "F" || $matches['question_specs'] == "FALSE") {
                        $question['answer'] = "0";
                        $is_true = 0;
                    } else {
                        $question['type'] = "error";
                    }

                    // Create the explanation (if such exists) - the format is always first expl. for true the second for false
                    if (isset($temp[1])) {
                        $explanations[0] = _TRUE. ": " . $temp[1] . "\n";
                        if (isset($temp[2])) {
                            $explanations[0] .= _FALSE . ": " . $temp[2] . "\n";
                        }
                    }

                    $question['text'] = $matches['text_before'] . " " . $matches['text_after'];
                }
            } else {
                // Multiple choice answers have at least one wrong answer
                if (in_array("~" , $signs)) {
                    $question['options'] = array();
                    foreach ($options as $option) {
                        $question['options'][] = trim($option);
                    }
                    $correct_answers = 0;
                    $signs_length = sizeof($signs);
                    $question['answer'] = array();
                    for ($i =0 ; $i < $signs_length; $i++) {
                        if ($signs[$i] == "=") {
                            $correct_answers++;
                            $question['answer'][] = $i; // this works because signs match exactly to options order
                        }
                    }

                    if ($correct_answers == 0) {
                        $question['type'] = "error";
                    } else if ($correct_answers == 1) {
                        $question['type'] = "multiple_one";
                    } else {
                        $question['type'] = "multiple_many";
                    }

                    if (trim($matches['text_after']) != "") {
                        $question['text'] = $matches['text_before'] . " __________ " . $matches['text_after'];
                    } else {
                        $question['text'] = $matches['text_before'];
                    }

                } else {
                    // All answers are =... (empty_spaces) or =..->... match questions
                    // Check if this is a matching questions, i.e. all options are of the form A->B
                    // We assume it is a matching question and behave as if it is until the preg_match condition returns false
                    $is_matching_question = 1;
                    $question['options'] = array();
                    $question['answer'] = array();

                    foreach ($options as $option) {
                        if (preg_match('/(?P<option1>.*)->(?P<option2>.*)/', $option, $option_matches)) {
                           $question['options'][] = $option_matches['option1'];
                           $question['answer'][] = $option_matches['option2'];
                        } else {
                            $is_matching_question = 0;
                            break;
                        }
                    }

                    if ($is_matching_question) {
                        $question['type'] = "match";
                        if (trim($matches['text_after']) != "") {
                            $question['text'] = $matches['text_before'] . " __________ " . $matches['text_after'];
                        } else {
                            $question['text'] = $matches['text_before'];
                        }
                    } else {
                        // Replacing any previous values in these arrays
                        unset($question['options']);
                        foreach ($options as $key => $option) {
                            $options[$key] = trim($option);
                        }
                        $question['answer'] = array("0" => implode("|",$options));
                        $question['type'] = "empty_spaces";

                        // The first explanation has already been extracted in multiple lines we need to create a single one
                        $temp_expl = implode($explanations, ", ");

                        $explanations = array();
                        if ($temp_expl != "") {
                            $explanations[] = $temp_expl;
                        }
                        // We will be traversing the string backwards to find multiple blank spaces - in the end we will reverse the answers
                        $temp_question_text = " ### " . $matches['text_after'];
                        $remaining_string = $matches['text_before'];

                        while (preg_match('/(?P<text_before>.*){(?P<question_specs>.*)}(?P<text_after>.*)/' , $remaining_string, $previous_matches)) {

                            // Assumption that the syntax is correct - if no "=" is used the error below will emerge
                            $options = explode("=", $previous_matches['question_specs']);

                            unset($options[0]); // the first one is blank
                            if (empty($options)) {
                                $error = 1;
                                break;
                            } else {

                                $temp_expl = array();
                                $options_length = sizeof($options);
                                for($i = 1; $i <= $options_length; $i++) {
                                    $temp = explode("#", $options[$i]);
                                    if ($temp[1] != "") {
                                        $options[$i] = trim($temp[0]);
                                        $temp_expl[$i] = $options[$i] . ": " . trim($temp[1]);
                                    }
                                }

                                if (sizeof($temp_expl) > 0) {
                                    $explanations[] = implode($temp_expl, ", ");
                                }
                                foreach ($options as $key => $option) {
                                    $options_exploded = explode("#", $option);
                                    $options[$key] = trim($options_exploded[0]);
                                }
                                $question['answer'][] = implode("|", $options);
                            }
                            $temp_question_text = " ### " . $previous_matches['text_after'] . $temp_question_text;
                            $remaining_string = $previous_matches['text_before'];
                        }

                        $question['answer'] = array_reverse($question['answer']);
                        $explanations= array_reverse($explanations);
                        $question['text'] = $remaining_string . $temp_question_text;
                        if ($error) {
                            $question['type'] = "error";
                        }
                    }
                }

            }

            if (!empty($explanations)) {
                $question['explanation'] = implode("\n", $explanations);
            }
            // Replace back special characters
            $questions[] = $this -> replaceBackQuestion($question);
        }

        $questions = $this -> removeDuplicates($questions);

        return $questions;
    }

    public function createPreviewHTML($questions) {
        $questionString = '<table class = "unsolvedQuestion">';
        foreach ($questions as $count => $question) {

            $pos = strpos($question['text'], "###");
            // Replace ### empty spaces with ____1____ , ____2____ , ..., ____N____ corresponding to N answer groups
            if ($pos) {
                $temp = $question['text'];
                $size = strlen($question['text']);
                $label = 1;
                $question['text'] = "";
                for ($i = 0; $i < $size-2; $i++) {
                    if ($temp[$i] == "#" && $temp[$i+1] == "#" && $temp[$i+2] == "#") {
                        $question['text'] .= "<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[" . ($label++) . "]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>";
                        $i = $i+2; //skip characters
                    } else {
                        $question['text'] .= $temp[$i];
                    }
                }
                $question['text'] .= $temp[$size-2];
                $question['text'] .= $temp[$size-1];
            }

            $questionString .= '<tr><td><b>'.($count+1).". ". $question['text'] .'</b></td></tr><tr><td>';

            if ($question['type'] == "true_false") {
                if ($question['answer'] == 1) {
                    $questionString .= "<span class = 'orderedList' style='color:green;font-weight:bold;'>". _TRUE. "</span><BR><span class = 'orderedList'>"._FALSE. "</span><BR>";
                } else {
                    $questionString .= "<span class = 'orderedList'>". _TRUE. "</span><BR><span class = 'orderedList' style='color:green;font-weight:bold;'>"._FALSE. "</span><BR>";
                }
            } else if ($question['type'] == "empty_spaces") {
                $label = 1;
                foreach ($question['answer'] as $answer) {
                    $questionString .= "[".($label++)."] ". str_replace("|", ", ", $answer) . "<br>";
                }

            } else if ($question['type'] == "match") {

                $options_length = sizeof($question['options']);
                $questionString .= "<table width='40%'>";
                for($i = 0; $i < $options_length; $i++) {
                    $questionString .= "<tr><td>".$question['options'][$i] . "</td><td>&raquo;&raquo;</td><td>" . $question['answer'][$i] . "</td></tr>";
                }
                $questionString .= "</table>";

            } else if ($question['type'] == "raw_text") {

                $questionString .= "(" . _RAWTEXT . ")<BR>";

            } else if ($question['type'] == "no_answer_error") {
                $questionString .= "<span class = 'orderedList' style='color:red;font-weight:bold;'>". _GIFTAIKEN_NOCORRECTANSWERGIVENORCORRECTANSWERDOESNOTEXIST. "</span><BR>";
            } else if ($question['type'] == "error") {
                $questionString .= "<span class = 'orderedList' style='color:red;font-weight:bold;'>". _GIFTAIKEN_ERRORINQUESTIONSYNTAX. "</span><BR>";
            } else if ($question['type'] == "same") {
                $questionString .= "<span class = 'orderedList' style='color:red;font-weight:bold;'>". _GIFTAIKEN_QUESTIONISALREADYDEFINEDIN. " " . $question['options'] . ".</span><BR>";
            } else {
                foreach ($question['options'] as $key => $option) {
                    $questionString .= "<span class = 'orderedList' ";
                    if (in_array($key,$question['answer'])) {
                        $questionString .= "style='color:green;font-weight:bold;'>";
                    } else {
                        $questionString .= ">";
                    }
                    $questionString .= "[".($key+1)."]&nbsp;".$option. "</span><BR>";
                }
            }

            if (isset($question['explanation'])) {
                $questionString .= '</td></tr><tr><td class="questionExplanation">'.str_replace("\n","<br>",$question['explanation']);
            }
            $questionString .= '</td></tr><tr><td>&nbsp;</td></tr>';
        }
        $questionString .= '</table>';
        return $questionString;
    }

    public function getModule() {

        $smarty = $this -> getSmartyVar();

        // Always show the preview of the data
        if ($_POST['preview'] || $_POST['submit']) {
            if ($_POST['questions_format'] == "gift") {
                $questions = $this -> scanGIFT($_POST['imported_data']);
            } else {
                $questions = $this -> scanAIKEN(str_replace('\"','"',(str_replace("\'","'",$_POST['imported_data']))));
            }
            if (sizeof($questions)) {
                $smarty -> assign ("T_PREVIEW_DIV", $this -> createPreviewHTML($questions));
            }
        }

        // Submit the data the data
        if ($_POST['submit']) {
            if ($_POST['select_unit'] == -1 || $_POST['select_unit'] == -2) {
                $content_ID = 0;
            } else {
                $content_ID = $_POST['select_unit'];
            }
            $currentLesson = $this -> getCurrentLesson();
            $lessons_ID = $currentLesson -> lesson['id'];

            $count = 0;
            foreach($questions as $key => $question) {
                if ($question['type'] != "same" && $question['type'] != "error" && $question['type'] != "no_answer_error") {
                    $question['content_ID'] = $content_ID;
                    $question['lessons_ID'] = $lessons_ID;
                    $question['difficulty'] = "medium";

                    if (sizeof($question['options'])) {
                        $question['options'] = serialize($question['options']);
                        //$question['options'] = str_replace("'", "&#39;", $question['options']);
                        //$question['options'] = str_replace("\r", "", $question['options']);
                    }
                    if (sizeof($question['answer'])) {

                        // Different accounting for answers of multiple many type
                        if ($question['type'] == "multiple_many") {
                            $answers = array();
                            foreach ($question['answer'] as $answer) {
                                $answers[$answer] = "1";
                            }

                            $question['answer'] = $answers;
                        }
                        $question['answer'] = serialize($question['answer']);
                        //$question['answer'] = str_replace("'", "&#39;", $question['answer']);
                        //$question['answer'] = str_replace("\r", "", $question['answer']);
                    }

                    //$question['text'] = str_replace("'", "&#39;", $question['text']);
                    if (isset($question['explanation'])) {
                        //$question['explanation'] = str_replace("'", "&#39;", $question['explanation']);
                        //$question['explanation'] = str_replace("\r", "", $question['explanation']);
                    }

                    if (Question :: createQuestion($question)) {
                        $count++;
                    }
                }
            }

            if ($count) {
                $this -> setMessageVar($count." "._GIFTAIKEN_QUESTIONSIMPORTEDSUCCESSFULLY, "success");
            } else {
                $this -> setMessageVar(_GIFTAIKEN_NOQUESTIONCOULDBEIMPORTED, "failure");
            }

        }

        $pos = strpos($_SERVER['REQUEST_URI'], "&message");
        if ($pos) {
            $postUrl = substr($_SERVER['REQUEST_URI'], 0,$pos);
        } else {
            $postUrl = $_SERVER['REQUEST_URI'];
        }
        $importForm = new HTML_QuickForm("import_users_form", "post", $postUrl, "", null, true);
        $importForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

        $importForm -> addElement('radio', 'questions_format', _GIFTAIKEN_GIFT, null, 'gift', 'id="gift_selection"');
        $importForm -> addElement('radio', 'questions_format', _GIFTAIKEN_AIKEN, null, 'aiken', 'id="aiken_selection"');

        $currentLesson = $this ->getCurrentLesson();
        $currentContent = new EfrontContentTree($currentLesson -> lesson['id']);
        $smarty -> assign("T_UNITS", $currentContent -> toHTMLSelectOptions());

        $importForm -> addElement('textarea', 'imported_data', _GIFTAIKEN_QUESTIONDATATOIMPORT, 'class = "inputProjectTextarea" id="imported_data"');

        $importForm -> addElement('submit', 'preview', _PREVIEW, 'class=flatButton onclick="$(\'import_users_form\').action += \'&preview=1\'"');
        $importForm -> addElement('submit', 'submit', _SUBMIT, 'class=flatButton');

        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $importForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $importForm -> setRequiredNote(_REQUIREDNOTE);
        $importForm -> accept($renderer);
        $smarty -> assign('T_GIFTAIKENQUESTIONS_FORM', $renderer -> toArray());

        return true;
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_GIFTAIKENQUESTIONS_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_GIFTAIKENQUESTIONS_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_GIFTAIKENQUESTIONS_MODULE_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
    }

    public function getModuleIcon() {
        return $this -> moduleBaseLink.'images/transform32.png';
    }
}
?>
