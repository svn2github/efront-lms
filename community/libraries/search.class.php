<?php
/**

* EfrontSearch Class file

*

* @package eFront

* @version 3.5.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * EfrontSearch class

 *

 * This class manipulates search

 * @author Tsirakis Nikos <tsirakis@efront.gr>

 * @package eFront

 * @version 1.0

 */
class EfrontSearch
{
 /**

     * Array containing the correspondence between table names and integers in db table search keywords

     * @since 3.6

     * @var array

     * @access private

     */
  private static $tableAssoc = array(
        'content' => 0,
        'lessons' => 1,
        'courses' => 2,
        'f_forums' => 3,
        'f_topics' => 4,
        'f_messages' => 5,
        'f_personal_messages' => 6,
        'news' => 7,
        'files' => 8,
  'f_poll' => 9,
  'questions' => 10,
  'glossary' => 11);
 /**

	 * Function insertText()

	 *

	 * This function registers a new keyword to the search table. Input arguments are:

	 * - The keyword to be commited

	 * - The id of the database entry in the keyword original table

	 * - This table's name.

	 * - Whether the keyword lies on the title or the body of the containing text

	 *

	 * @param string $text The search keyword

	 * @param int $foreignID The keyword's original entry id

	 * @param string $tableName The keyword's original table name

	 * @param string $position The keyword's original text position, either 'title' or 'data'.

     * @since 3.5.0

     * @access public

	 */
 public static function insertText($text, $foreignID, $tableName, $position) {
     $fields['foreign_ID'] = $foreignID;
  $fields['table_name'] = EfrontSearch :: $tableAssoc[$tableName]; //from 3.6 there is a corespondence between tables and numbers
  ($position == "title") ? $fields['position'] = 0 : $fields['position'] = 1; //from 3.6  1 means 'data' and 0 means 'title'
  //todo : remove also some special chars like [ ] & * etc
  if($text == "") {return true;}
  $replace = array("(", "{", "}", ")","]", "[","@", "#", "$", "%", "^", "&","*", ".", ",");
  $querywords = mb_strtolower(strip_tags(str_replace("&nbsp;"," ", str_replace($replace, " ", $text))));
  $eachword = explode(" ", $querywords);
  $eachword = array_unique($eachword); //Remove duplicate values from search table
  $terms = array();
  foreach ($eachword AS $key => $value) {
   $len = mb_strlen($value);
   if ($len > 3 AND $len < 100) { //Only words with length more than 3 and less than 100 characters long.
    $terms[] = $value;
   } else{
   }
  }
  //Querying for all values may be very slow; this is why we added this 20 values limit
   if (sizeof($terms) > 20) {
    $result = eF_getTableDataFlat("search_invertedindex", "id,keyword");
   } else {
    $result = eF_getTableDataFlat("search_invertedindex", "id,keyword", "keyword in ('".implode("','", array_walk($terms, eF_addSlashes))."')");
   }
      $result["keyword"] ? $allTerms = $result["keyword"] : $allTerms = array();
      if (! empty($terms)) {
          foreach ($terms as $key => $value) {
              $position = array_search( $value, $allTerms); //array_search may also return null!
              if ($position === false && !is_null($position)) {
                  $newId = eF_insertTableData("search_invertedindex", array("keyword" => $value));
                  //$fields['keyword'] = $newId;
                  $allFields[] = $fields + array('keyword' => $newId);
                  //$rows[] = "('".implode("','",$fields)."')";
              } else {
                  //$fields['keyword'] = $result["id"][$position];
                  $allFields[] = $fields + array('keyword' => $result["id"][$position]);
                  //$rows[] = "('".implode("','",$fields)."')";
              }
          }
          //$res = eF_executeNew("insert into search_keywords (".implode(",", array_keys($fields)).") values ".implode(",",$rows)."");
          eF_insertTableDataMultiple("search_keywords", $allFields);
      }
  return true;
 }
 /**

	 * Function removeText()

	 *

	 * This function removes keywords on the search table. Input arguments are:

	 * - The id of the database entry in the keyword original table

	 * - This table's name.

	 * - Whether the keyword lies on the title or the body of the containing text

	 *

	 * @param string $tableName The keyword's original table name

	 * @param string $position The keyword's position

	 * @param boolean $morefIds If foreign Id is a list or not

	 * @param int $foreignID The keyword's original entry id

	 * @since 3.5.0

     * @access public

	 */
 public static function removeText($tableName, $foreignID, $position, $morefIds = false) {
  if (strlen($position) > 2) {
   ($position == "title") ? $position_str = "position = 0 and " : $position_str = "position = 1 and ";
  }
  if ($morefIds == true) {
   eF_deleteTableData("search_keywords", $position_str."foreign_ID in (".$foreignID.") and table_name = '". EfrontSearch :: $tableAssoc[$tableName]."'");
  } else {
   eF_deleteTableData("search_keywords", $position_str."foreign_ID = '".$foreignID."' and table_name = '". EfrontSearch :: $tableAssoc[$tableName]."'");
  }
 }
 /**

	 * Function searchForOneWord()

	 *

	 * @param string $word The word to search for

	 * @param int $maxres The total number of words it should return

	 * @param string $table_name ?

	 * @param string $position The position of the string we are looking for

     * @since 3.5.0

     * @access public

	 */
 public static function searchForOneWord($word, $maxres, $tableName, $position) {
  ($position == "title") ? $position_str = 0 : $position_str = 1;
  $res = eF_getTableDataFlat("search_invertedindex","id","keyword like '%$word%'");
  if (sizeof($res) > 0) {
      $idsList = implode(",", $res['id']);
      $result = eF_getTableDataFlat("search_keywords", "foreign_ID, count(keyword) AS score, table_name, position", "keyword IN ($idsList)".($position ? ' and position='.$position_str : '').($tableName ? ' and table_name='. EfrontSearch :: $tableAssoc[$tableName] : '')."", "", "foreign_ID,table_name limit $maxres");
  }
  return $result;
 }
 /**

	 * Function searchFull()

	 *

	 * This function performs a search to the search table for a given string. The query string

     * is first transformed into greeklish. The results are returned as an array, whose each element

	 * is another array holding results data:

	 * - 'foreign_ID' : The id of the entry that includes the search string, in the original table.

	 * - 'table_name' : The original table name, where the search term came from

	 * - 'position' : The posirion of the text, 'title' or 'data'

	 * - 'score' : The relevance score

	 *

	 * @param string $text The text to search for

	 * @param bool $tableName The database table to search into

	 * @param string $position The position of the text, could be 'title' or 'data'

	 * @param int $number The number of results to display

     * @since 3.5.0

     * @access public

	 */
 public static function searchFull ($text, $tableName = false, $position = false, $number = 20) {
  global $debugMessages;
  $debugMessages.="Got into search";
  $eachword = explode(" ", $text);
  $maxres = 10000;
  $score_ids = array();
  for ($i = 0; $i < count($eachword); $i++) {
   if (mb_strlen($eachword[$i]) > 3) {
    $results[$i] = EfrontSearch :: searchForOneWord($eachword[$i], $maxres, $tableName, $position);
    $score_ids[$i] = array_combine($results[$i]['foreign_ID'], $results[$i]['score']);
    $maxres = $maxres / 2;
   }
  }
  //These lines are used for removing empty subarrays because of words with mb_strlen < 3
  foreach($results as $key => $value) {
   if (!empty($value)) {
     $resultsTemp[] = $results[$key];
     $score_idsTemp[] = $score_ids[$key];
    }
  }
  $results = $resultsTemp;
  $score_ids = $score_idsTemp;
  $common_keys = $score_ids[0];
  for ($i = 1; $i < sizeof($score_ids); $i++) {
   $common_keys = array_intersect_key($common_keys, $score_ids[$i]);
   foreach ($common_keys as $key => $value) {
    $common_keys[$key] += $score_ids[$i][$key] - 1;
   }
  }
  $field_data = array();
  $max_score = max($common_keys);
  for ($i = 0; $i < sizeof($results[0]['foreign_ID']); $i++) {
   if (in_array($results[0]['foreign_ID'][$i], array_keys($common_keys))) {
    $field_data[] = array('foreign_ID' => $results[0]['foreign_ID'][$i],
          'table_name' => array_search($results[0]['table_name'][$i], EfrontSearch :: $tableAssoc),
          'position' => $results[0]['position'][$i],
          'score' => $common_keys[$results[0]['foreign_ID'][$i]] / $max_score);
   }
  }
  return $field_data;
 }
 /**

	 * Function searchUsers()

	 *

	 * This function performs a search to the users table for a given string. The query string

	 * is first transformed into upper case. The results are returned as an array, whose each element

	 * is another array holding results data.

	 *

	 * @param string $text The text to search for

     * @since 3.5.0

     * @access public

	 */
 public static function searchUsers ($text) {
  $split_stemmed = split(" ",$text);
  while (list($key,$val)= each($split_stemmed)) {
   if($val <> " " && strlen($val) > 2){
    $val = strtoupper($val);
    $where .= "(upper(login) LIKE '%$val%' OR upper(email) LIKE '%$val%' OR upper(name) LIKE '%$val%' OR upper(surname) LIKE '%$val%') OR";
   }
  }
  $where = substr($where,0,(strLen($where)-4)); //this will eat the last OR
  $where .= ") ORDER BY login DESC";
  $result = eF_getTableData("users", "*", $where);
  return $result;
 }
 /**

	 * Function wordLimiter()

	 *

	 * This function performs reduction of words in a given string.

	 *

	 * @param string $str The text to limit

	 * @param int $n The number of words

	 * @param string	$startChar The start characters

	 * @param string	$endChar The end characters

     * @since 3.5.0

     * @access public

	 */
 public static function wordLimiter ($str, $n = 100, $startChar = '...',$endChar = '...') {
  if (mb_strlen($str) < $n) {
   return $str;
  }
  $words = explode(' ', preg_replace("/\s+/", ' ', preg_replace("/(\r\n|\r|\n)/", " ", $str)));
  if (count($words) <= $n) {
   return $str;
  }
  $str = '';
  for ($i = 0; $i < $n; $i++) {
   $str .= $words[$i].' ';
  }
  return $startChar.trim($str).$endChar;
 }
 /**

	 * Function highlightText()

	 *

	 * This function performs highlighting of words in a given string.

	 *

	 * @param string $searchText The search results to be examined

	 * @param string $searchCriteria String with the words that will be highlighted

	 * @param string $style The style the highlighter

     * @since 3.5.0

     * @access public

	 */
 public static function highlightText ($searchText, $searchCriteria, $style) {
  if (!is_array($searchCriteria)) {
   $searchCriteria = trim($searchCriteria);
   $searchCriteria = explode(" ", $searchCriteria); //create an array of keywords if it does not exists
  }
  for ($i=0; $i < count($searchCriteria); $i++) {
   if (mb_strlen($searchCriteria[$i])>1) {
    if (!preg_match("/^.*[$\/\'\"]+.*$/", $searchCriteria[$i])) { //checks if keyword is text
     $searchText = preg_replace('/(' . $searchCriteria[$i] . ')/iu', '<span class="'.$style.'">$1</span>', $searchText);
    }
   }
  }
  return $searchText;
 }
    /**

     * Function resultsTextLimit()

     *

     * This function is used to find keywords in search result content

     *

     * @param data $data The resulted to be added applied

     * @param searchCriteria $searchCriteria The criteria of selecting terms

     * @param style $style The style to be applied

     * @param limitText $limitText Limitation of word length

     * @param start $start The starting characters

     * @param end $end The ending characters

     * @since 3.5.0

     * @access public

     */
 public static function resultsTextLimit ($data, $searchCriteria, $style, $limitText = "150", $start = "...", $end = "...") {
  $data = strip_tags($data);
  $dataLength = mb_strlen($data);
  if (!is_array($searchCriteria)) {
   $searchCriteria = explode(" ", trim($searchCriteria)); //create an array of keywords if it does not exists
  }
  foreach ($searchCriteria as $key => $value) {
   $value = mb_strtolower($value);
   if (mb_strpos(mb_strtolower($data), $value) !== false) {
    $positions[] = mb_strpos(mb_strtolower($data), $value);
   }
  }
  $head = 0;
  $foot = $dataLength;
  $startC = $endC = null;
  if (sizeof($positions) > 0) {
   if($positions[0] > floor($limitText/2)) {
    $head = $positions[0] - floor($limitText/2);
    $startC = $start;
   }
   if(($positions['0'] + floor($limitText/2)) < $dataLength) {
    $foot = $positions[0] + floor($limitText/2);
    $endC = $end;
   }
   return $startC . EfrontSearch :: highlightText(mb_substr($data, $head, ($foot - $head)), $searchCriteria, $style) . $endC;
  } else {
   return EfrontSearch :: highlightText(mb_substr($data, 0, $limitText), $searchCriteria, $style) . $end;
  }
 }
    /**

     * Function reBuiltIndex()

     *

     * This function is used to built the keywords from scratch

     *

     * @since 3.5.0

     * @access public

     */
 public static function reBuiltIndex () {
     eF_deleteTableData("search_keywords"); //Delete old search terms
//		eF_deleteTableData("search");
  $GLOBALS['db'] -> Execute("truncate table search_invertedindex");
  //Courses Data
  $courses = eF_getTableData("courses", "id,name");
  for ($i = 0; $i < sizeof($courses); $i++) {
   EfrontSearch :: insertText($courses[$i]['name'], $courses[$i]['id'], "courses", "title");
  }
  //Lesson Data
  $lessons = eF_getTableData("lessons", "id,name");
  for ($i = 0; $i < sizeof($lessons); $i++) {
   EfrontSearch :: insertText($lessons[$i]['name'], $lessons[$i]['id'], "lessons", "title");
  }
  //Content Data
  $content = eF_getTableData("content", "id,name,data");
  for ($i = 0; $i < sizeof($content); $i++) {
   EfrontSearch :: insertText($content[$i]['name'], $content[$i]['id'], "content", "title");
   EfrontSearch :: insertText(strip_tags($content[$i]['data']), $content[$i]['id'], "content", "data");
  }
  //Forum Messages
  $forum_messages = eF_getTableData("f_messages", "id, title, body");
  for ($i = 0; $i < sizeof($forum_messages); $i++) {
   EfrontSearch :: insertText(strip_tags($forum_messages[$i]['body']), $forum_messages[$i]['id'], "f_messages", "data");
   EfrontSearch :: insertText($forum_messages[$i]['title'], $forum_messages[$i]['id'], "f_messages", "title");
  }
  //Forums
  $forums = eF_getTableData("f_forums", "id, title, comments");
  for ($i = 0; $i < sizeof($forums); $i++) {
   EfrontSearch :: insertText($forums[$i]['title'], $forums[$i]['id'], "f_forums", "title");
   if(strlen($forums[$i]['comments'])>3){
    EfrontSearch :: insertText(strip_tags($forums[$i]['comments']), $forums[$i]['id'], "f_forums","data");
   }
  }
  //Forums Topics
  $f_topics = eF_getTableData("f_topics", "id, title, comments");
  for ($i = 0; $i < sizeof($f_topics); $i++) {
   EfrontSearch :: insertText($f_topics[$i]['title'], $f_topics[$i]['id'], "f_topics", "title");
   if(strlen($f_topics[$i]['comments'])>3){
    EfrontSearch :: insertText(strip_tags($f_topics[$i]['comments']), $f_topics[$i]['id'], "f_topics","data");
   }
  }
  //Forums Polls
  $f_poll = eF_getTableData("f_poll", "id, title, question");
  for ($i = 0; $i < sizeof($f_poll); $i++) {
   EfrontSearch :: insertText($f_poll[$i]['title'], $f_poll[$i]['id'], "f_poll", "title");
   if(strlen($f_poll[$i]['question'])>3){
    EfrontSearch :: insertText(strip_tags($f_poll[$i]['question']), $f_poll[$i]['id'], "f_poll","data");
   }
  }
  //Personal Messages
  $personal_messages = eF_getTableData("f_personal_messages", "id, title, body"); //Get all the personal messages
  for ($i = 0; $i < sizeof($personal_messages); $i++) {
   EfrontSearch :: insertText($personal_messages[$i]['body'], $personal_messages[$i]['id'], "f_personal_messages", "data");
   EfrontSearch :: insertText($personal_messages[$i]['title'], $personal_messages[$i]['id'], "f_personal_messages", "title");
  }
  //Questions
  $questions = eF_getTableData("questions", "id, text");
  for ($i = 0; $i < sizeof($questions); $i++) {
   EfrontSearch :: insertText(strip_tags($questions[$i]['data']), $questions[$i]['id'], "questions", "data");
  }
  //Glossary terms
  $glossary = eF_getTableData("glossary", "id, name, info");
  for ($i = 0; $i < sizeof($glossary); $i++) {
   EfrontSearch :: insertText(strip_tags($glossary[$i]['info']), $glossary[$i]['id'], "glossary", "data");
   EfrontSearch :: insertText($glossary[$i]['name'], $glossary[$i]['id'], "glossary", "title");
  }
 }
}
?>
