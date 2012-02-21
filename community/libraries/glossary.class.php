<?php
/**

* glossary Class file

*

* @package eFront

* @version 3.6

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 *

 * @author user

 *

 */
class glossary extends EfrontEntity
{
    /**

     * The glossary properties

     *

     * @since 3.6.0

     * @var array

     * @access public

     */
    public $glossary = array();
    /**

     * Create glossary

     *

     * This function is used to create glossary

     * <br>Example:

     * <code>

	 * $fields = array("title"       => $form -> exportValue('title'),

	 *       "data"        => $form -> exportValue('data'),

	 *       "timestamp"   => $from_timestamp,

	 *		 "expire"      => $to_timestamp,

	 *       "lessons_ID"  => isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID'] ? $_SESSION['s_lessons_ID'] : 0,

	 *       "users_LOGIN" => $_SESSION['s_login']);

	 *

	 * $glossary = glossary :: create($fields, 0));

	 *

     * </code>

     *

     * @param $fields An array of data

     * @return glossary The new object

     * @since 3.6.0

     * @access public

     * @static

     */
    public static function create($fields = array()) {
     // added to fix http://forum.efrontlearning.net/viewtopic.php?f=5&t=2851&p=14715
     if (mb_substr($fields['info'], 0, 3) == "<p>") {
      $fields['info'] = mb_substr($fields['info'], 3);
      if (mb_substr($fields['info'], -4, 4) == "</p>") {
       $fields['info'] = mb_substr($fields['info'], 0, -4);
      }
     }
        $fields = array("name" => $fields['name'],
                        "info" => $fields['info'],
                        "lessons_ID" => $fields['lessons_ID'],
                        "type" => isset($fields['type']) && $fields['type'] ? $fields['type'] : "general");
                    //    "active"     => isset($fields['active'])&& $fields['active'] ? 1 : 0);  //not implemented yet
        $newId = eF_insertTableData("glossary", $fields);
        $glossary = new glossary($newId);
  //pr($glossary);exit;
  EfrontSearch :: insertText($glossary -> glossary['name'], $glossary -> glossary['id'], "glossary", "title");
        EfrontSearch :: insertText($glossary -> glossary['info'], $glossary -> glossary['id'], "glossary", "data");
        return $glossary;
    }
 /**

     * Delete glossary terms

     *

     * This function is used to delete the current term.

     * <br/>Example:

     * <code>

     * $glossary = new glossary($newId);                 //Instantiate term with id $newId

     * $glossary  -> delete();                            //Delete term

     * </code>

     *

     * @since 3.6.0

     * @access public

     */
 public function delete() {
  parent :: delete();
  EfrontSearch :: removeText('glossary', $this -> glossary['id'], 'title');
     EfrontSearch :: removeText('glossary', $this -> glossary['id'], 'data');
 }
 /**

     * Persist glossary properties

     *

     * This function can be used to persist with the database

     * any changes made to the current glossary object.

     * <br/>Example:

     * <code>

     * $glossary -> glossary['name'] = 'new Title';              //Change the name

     * $glossary -> persist();                                   //Make the change permanent

     * </code>

     *

     * @since 3.6.0

     * @access public

     */
    public function persist() {
        // added to fix http://forum.efrontlearning.net/viewtopic.php?f=5&t=2851&p=14715
     if (mb_substr($this -> glossary['info'], 0, 3) == "<p>") {
      $this -> glossary['info'] = mb_substr($this -> glossary['info'], 3);
      if (mb_substr($this -> glossary['info'], -4, 4) == "</p>") {
       $this -> glossary['info'] = mb_substr($this -> glossary['info'], 0, -4);
      }
     }
        parent :: persist();
        EfrontSearch :: removeText('glossary', $this -> glossary['id'], 'data');
        EfrontSearch :: insertText($this -> glossary['info'], $this -> glossary['id'], "glossary", "data");
        EfrontSearch :: removeText('glossary', $this -> glossary['id'], 'title');
        EfrontSearch :: insertText($this -> glossary['title'], $this -> glossary['id'], "glossary", "title");
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     $form -> addElement('text', 'name', _TERM, 'id="termField" class = "inputText"');
     $form -> addRule('name', _THEFIELD.' '._TERM.' '._ISMANDATORY, 'required');
     $form -> addElement('textarea', 'info', _DEFINITION, 'class = "inputTextarea"');
     if ($GLOBALS['configuration']['disable_shared_glossary'] != 1) {
      $form -> addElement('select', 'lessons_ID', _LESSON, array( $_SESSION['s_lessons_ID']=> _FORTHISLESSON, 0 => _FORANYLESSON));
     }
     $form -> addElement('submit', 'submit', _SUBMITTERM, 'class = "flatButton"');
     $form -> addElement('submit', 'submit_term_add_another', _SUBMITANDADDANOTHER, 'class = "flatButton"');
     if ($GLOBALS['configuration']['disable_shared_glossary'] != 1) {
      $form -> setDefaults(array('name' => $this -> glossary['name'], 'info' => $this -> glossary['info'], 'lessons_ID' => $this -> glossary['lessons_ID']));
     } else {
      $form -> setDefaults(array('name' => $this -> glossary['name'], 'info' => $this -> glossary['info']));
     }
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
        if (isset($_GET['edit'])) {
            $this -> glossary["name"] = $form -> exportValue('name');
            $this -> glossary["info"] = $form -> exportValue('info');
            if ($GLOBALS['configuration']['disable_shared_glossary'] != 1) {
             $this -> glossary["lessons_ID"] = $form -> exportValue('lessons_ID');
            }
            $this -> persist();
        } else {
         if ($GLOBALS['configuration']['disable_shared_glossary'] != 1) {
           $fields = array("name" => $form -> exportValue('name'),
                         "info" => $form -> exportValue('info'),
                         "lessons_ID" => $form -> exportValue('lessons_ID'));
         } else {
           $fields = array("name" => $form -> exportValue('name'),
                         "info" => $form -> exportValue('info'),
                         "lessons_ID" => $_SESSION['s_lessons_ID']);
         }
            $glossary = self :: create($fields);
            $this -> glossary = $glossary;
        }
    }
    /**

	 * Get glossary words

	 *

	 * This function is used to return an array of words, divided per initial letter, given an array of

 	 * glossary entries.

     *

     * @return array An array of words, divided in subarrays per letter

     * @since 3.6.0

     * @access public

     * @static

     */
    public static function getGlossaryWords($words) {
        $initials = array();
  $returnValue = preg_match("/^\p{L}.*$/u", 'a');
        foreach($words as $key => $value) {
            $letter = mb_strtoupper(mb_substr($value['name'], 0, 1));
            //echo "LETTER: ".$letter." ASCII: ".ord($letter)."<br/>";
            if (preg_match("/[0-9]/", $letter)) {
                $initials["0-9"][$letter][] = $words[$key];
            } else if (!preg_match("/\p{L}/u", $letter) && $returnValue !== false) {
                $initials["Symbols"][$letter][] = $words[$key];
            } else if (!preg_match("/\w/", $letter) && $returnValue === false) {
                $initials["Symbols"][$letter][] = $words[$key];
            }
   else {
                $initials[$letter][] = $words[$key];
            }
        }
        //Added lines to sort terms in each tab (#1460)
        foreach ($initials as $key => $value) {
         $initials[$key] = eF_multiSort($value, 'name');
        }
        $setNum = isset($initials["0-9"]);
        $setSym = isset($initials["Symbols"]);
        if( $setNum || $setSym) {
            $tempNum = $initials["0-9"];
            $tempSym = $initials["Symbols"];
            unset($initials["0-9"]);
            unset($initials["Symbols"]);
            ksort($initials);
            if($setNum) {
                $initials["0-9"] = $tempNum;
            }
            if ($setSym) {
                $initials["Symbols"] = $tempSym;
            }
        } else {
            ksort($initials);
        }
        return $initials;
    }
    /**

     *

     * @param $text

     * @return unknown_type

     */
    public static function applyGlossary($text, $lessonId) {
     if ($GLOBALS['configuration']['disable_shared_glossary'] != 1) {
         $glossary_words = eF_getTableData("glossary", "name,info,lessons_ID", "lessons_ID=".$lessonId." OR lessons_ID=0"); //Get all the glossary words of this lesson
     } else {
      $glossary_words = eF_getTableData("glossary", "name,info,lessons_ID", "lessons_ID=".$lessonId); //Get all the glossary words of this lesson
     }
     //if a term is defined both for current lesson and all lessons, current lesson definition must be displayed
  $globalTerms = array();
      foreach ($glossary_words as $key => $value) {
   if ($value['lessons_ID'] == 0) {
    $globalTerms[$key] = $value['name'];
   }
   }
     foreach ($glossary_words as $key => $value) {
      if ($value['lessons_ID'] && $k = array_search($value['name'], $globalTerms)) {
          unset($glossary_words[$k]);
      }
  }
        $searchdata = array();
        $searchdatanext = array();
        $replacedata = array();
  $returnValue = preg_match("/^\p{L}.*$/u", 'a');
        foreach ($glossary_words as $key => $value) {
            $first_letter = mb_substr($value['name'], 0, 1);
            if ($first_letter != '<') {
                $value['name'] = preg_quote($value['name'], "/");
    if ($returnValue === false) {
     $searchdata[] = "/(\b)(".$value['name'].")(\b)/si";
    } else {
     $searchdata[] = "/(\P{L})(".$value['name'].")(\P{L})/usi";
    }
    $searchdatanext[] = "/(yty656hgh".$value['name'].")/usi";
    //Added 'UTF-8' because of #1661 and &rsquo;
                $replacedata[] = str_replace(array("\r\n", "\n"), '<br/>', rawurlencode(htmlentities(strip_tags($value['info']), ENT_QUOTES, "UTF-8")));
            }
        }
        $text = self :: highlightWords($text, $searchdata, $replacedata);
        $text = preg_replace("/encode\*\(\)\!768atyj/", "", $text);
        $text = preg_replace($searchdatanext, $replacedata, $text);
        return $text;
    }
    /**

     *

     * @param $text

     * @param $searchdata

     * @param $replacedata

     * @return unknown_type

     */
    public static function highlightWords ($text, $searchdata, $replacedata) {
        $word = $searchdata;
        $textPieces = preg_split("'(<a.*>.*</a>)|(<.+?>)'", $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $found = false;
        $info = $replacedata;
        foreach ($textPieces as $piece) {
            if ( (mb_strpos($piece, '<') === FALSE) && ($found == false) ) {
                if ($newPiece = preg_replace_callback($searchdata, array('glossary', 'encodeWords'), " ".$piece." ")) {
                    //because of EF-617
                 $piece = mb_substr(mb_substr($newPiece,1), 0, -1);
                }
            }
            $newTextPieces[] = $piece;
        }
        $text = implode('', $newTextPieces);
        return $text;
    }
    /**

     *

     * @param $matches

     * @return unknown_type

     */
    public static function encodeWords($matches)
    {
        $matching_text = $matches[2];
        $words = explode(" ", $matching_text);
        foreach($words as $key => $word) {
            $words[$key] = 'encode*()!768atyj'.$word;
        }
        $new_text = implode(' ',$words);
        return $matches[1]."<a class = 'glossary' href = 'javascript:void(0)' onmouseover = 'new Tip(this, decodeURIComponent(\"yty656hgh".self::encodeWordsInner($matching_text)."\"))'>".$new_text."</a>".$matches[3];
    }
    /**

     *

     * @param $text

     * @return unknown_type

     */
    public static function encodeWordsInner($text)
    {
        $words = explode(" ", $text);
        foreach($words as $key => $word) {
            $words[$key] = 'encode*()!768atyj'.$word;
        }
        $new_text = implode(' ',$words);
        return $new_text;
    }
    /**

     * Clear duplicate glossary terms

     *

     * There are times that the system may end up with duplicate glossary terms, like when

     * copying content. This function is used to effectively eliminate duplicates.

     * <br/>Example:

     * <code>

     * glossary :: clearDuplicates($currentLesson);

     * </code>

     *

     * @param mixed $lesson a lesson id or an EfrontLesson object

     * @access public

     * @static

     * @since 3.6.0

     */
    public static function clearDuplicates($lesson) {
     if ($lesson instanceOf EfrontLesson) {
      $lessonId = $lesson -> lesson['id'];
     } elseif (eF_checkParameter($lesson, 'id')) {
      $lessonId = $lesson;
     } else {
      throw new EfrontLessonException(_INVALIDID.": $lesson", EfrontLessonException :: INVALID_ID);
     }
     $result = eF_getTableData("glossary", "*", "lessons_ID=".$lessonId, "id");
     foreach ($result as $value) {
      $glossaryTerms[$value['id']] = $value;
         $id = $value['id'];
   unset($value['id']);
      $checksums[$id] = md5(serialize($value));
     }
     $uniques = array_unique($checksums);
     $duplicates = array_diff_key($checksums, $uniques);
     foreach ($duplicates as $key => $value) {
         $glossary = new glossary($glossaryTerms[$key]);
         $glossary -> delete();
     }
    }
}
