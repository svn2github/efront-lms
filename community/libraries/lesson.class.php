        }
        $cur_dir = getcwd();
        chdir(G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp);
        $filelist = eF_getDirContents();
        foreach ($filelist as $value) {
            copy($value, G_LESSONSPATH.$this->lesson['id']."/".$value);
        }
        chdir($cur_dir);
        foreach ($prerequisites as $key => $value) {
            foreach ($tagArray as $key2 => $value2) {
                if (isset($value2['attributes']['IDENTIFIER']) && $value2['attributes']['IDENTIFIER'] == $value) {
                    unset($fields_insert);
                    $fields_insert['users_LOGIN'] = "*";
                    $fields_insert['content_ID'] = $tagArray[$tagArray[$key]['parent_index']]['this_id'];
                    $fields_insert['rule_type'] = "hasnot_seen";
                    $fields_insert['rule_content_ID'] = $value2['this_id'];
                    $fields_insert['rule_option'] = 0;
                    //eF_insertTableData("rules", $fields_insert);
                }
            }
        }
        //read the special EfrontLesson.xml
        $xmlfile = G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp."/EfrontLesson.xml";
        $xml = simplexml_load_file($xmlfile);
        for ($i = 0; $i < sizeof($xml->conditions->condition); $i++){
            $condition = array();
            $condition['type'] = (string)$xml->conditions->condition[$i]->type;
            $condition['options'] = (string)$xml->conditions->condition[$i]->options;
            $condition['relation'] = (string)$xml->conditions->condition[$i]->relation;
            $condition['lessons_ID'] = $this->lesson['id'];
            $cid = ef_insertTableData("lesson_conditions", $condition);
        }
        for ($i = 0; $i < sizeof($xml->glossary->word); $i++){
            $glossary = array();
            $glossary['name'] = (string)$xml->glossary->word[$i]->name;
            $glossary['type'] = (string)$xml->glossary->word[$i]->type;
            $glossary['info'] = (string)$xml->glossary->word[$i]->info;
            $glossary['active'] = (string)$xml->glossary->word[$i]->active;
            $glossary['lessons_ID'] = $this->lesson['id'];
            $cid = ef_insertTableData("glossary", $glossary);
        }
        for ($i = 0; $i < sizeof($xml->questions->question); $i++){
            $update = array();
            $refid = (string)$xml->questions->question[$i]->refid;
            $qk = $questionsKeys[$refid];
            $qid = $questions[$qk]['id'];
            $update['type'] = (string)$xml->questions->question[$i]->type;
            $update['difficulty'] = (string)$xml->questions->question[$i]->difficulty;
            $update['options'] = (string)$xml->questions->question[$i]->options;
            $update['answer'] = (string)$xml->questions->question[$i]->answer;
            $update['explanation'] = (string)$xml->questions->question[$i]->explanation;
            ef_updateTableData("questions", $update, "id = $qid");
        }
        for ($i = 0; $i < sizeof($xml->tests->test); $i++){
            $update = array();
            $refid = (string)$xml->tests->test[$i]->refid;
            $tk = $testsKeys[$refid];
            $tid = $tests[$tk]['id'];
            $update['duration'] = (string)$xml->tests->test[$i]->duration;
            $update['redoable'] = (string)$xml->tests->test[$i]->redoable;
            $update['onebyone'] = (string)$xml->tests->test[$i]->onebyone;
            $update['answers'] = (string)$xml->tests->test[$i]->answers;
            $update['shuffle_questions'] = (string)$xml->tests->test[$i]->shuffle_questions;
            $update['shuffle_answers'] = (string)$xml->tests->test[$i]->shuffle_answers;
            $update['given_answers'] = (string)$xml->tests->test[$i]->given_answers;
            ef_updateTableData("tests", $update, "id=".$tid);
            for ($j = 0; $j < sizeof($xml->tests->test[$i]->questions->question); $j++){
                $testQuestions = array();
                $refid = (string)$xml->tests->test[$i]->questions->question[$j]->refid;
                $qk = $questionsKeys[$refid];
                $qid = $questions[$qk]['id'];
                $previd = (string)$xml->tests->test[$i]->questions->question[$j]->previous;
                if ($previd != "q0"){
                    $pk = $questionsKeys[$previd];
                    $pid = $questions[$pk]['id'];
                }
                else
                    $pid = 0;
                $testQuestions['tests_ID'] = $tid;
                $testQuestions['questions_ID'] = $qid;
                $testQuestions['previous_question_ID'] = $pid;
                $testQuestions['weight'] = (string)$xml->tests->test[$i]->questions->question[$j]->weight;
                ef_insertTableData("tests_to_questions", $testQuestions);
            }
        }
    }
    /**

     * Import lesson

     *

     * This function is used to import a lesson exported to a file

     * The first step is to optionally initialize the lesson, using initialize().

     * It then uncompresses the given file and proceeds to importing

     * <br/>Example:

     * <code>

     * try {

     *     $lesson = new EfrontLesson(32);                                             //32 is the lesson id

     *     $file = new EfrontFile($lesson -> getDirectory().'data.tar.gz');            //The file resides inside the lesson directory and is called 'data.tar.gz'

     *     $lesson -> import(array('content'), $file);

     * } catch (Exception $e) {

     *     echo $e -> getMessage();

     * }

     * </code><br/>

     *

     * @param EfrontFile $file The compressed lesson file object

     * @param array $deleteEntities The lesson aspects to initialize

     * @param boolean $lessonProperties Whether to import lesson properties as well

     * @param boolean $keepName Whether to keep the current (false) or the original name (true)

     * @return boolean True if the importing was successful

     * @since 3.5.0

     * @access public

     * @see EfrontLesson :: initialize()"

     */
    public function import($file, $deleteEntities = false, $lessonProperties = false, $keepName = false) {
        if ($deleteEntities) {
            $this -> initialize($deleteEntities); //Initialize the lesson aspects that the user specified
        }
        if (!($file instanceof EfrontFile)) {
            $file = new EfrontFile($file);
        }
        $fileList = $file -> uncompress();
        $file -> delete();
        $fileList = array_unique(array_reverse($fileList, true));
        $dataFile = new EfrontFile($this -> directory.'data.dat');
        $filedata = file_get_contents($dataFile['path']);
        $dataFile -> delete();
        $data = unserialize($filedata);
        $data['content'] = self :: eF_import_fixTree($data['content'], $last_current_node);
        for ($i = 0; $i < sizeof($data['files']); $i++) {
            if (isset($data['files'][$i]['file'])) {
                $newName = str_replace(G_ROOTPATH, '', dirname($data['files'][$i]['file']).'/'.EfrontFile :: encode(basename($data['files'][$i]['file'])));
                $newName = preg_replace("#(.*)www/content/lessons/#", "www/content/lessons/", $newName);
                $newName = preg_replace("#www/content/lessons/\d+/(.*)#", "www/content/lessons/".$this -> lesson['id']."/\$1", $newName);
                if ($data['files'][$i]['original_name'] != basename($data['files'][$i]['file'])) {
                    if (is_file(G_ROOTPATH.$newName)) {
                        $replaceString['/\/?(view_file.php\?file=)'.$data['files'][$i]['id'].'([^0-9])/'] = '${1}'.array_search(G_ROOTPATH.$newName, $fileList).'${2}'; //Replace old ids with new ids
                        //$mp[$data['files'][$i]['id']] = array_search(G_ROOTPATH.$newName, $fileList);
                        $file = new EfrontFile(G_ROOTPATH.$newName);
                        $file -> rename(G_ROOTPATH.dirname($newName).'/'.EfrontFile :: encode(rtrim($data['files'][$i]['original_name'], "/")));
                    }
                }
            } else {
                $newName = preg_replace("#www/content/lessons/\d+/(.*)#", "www/content/lessons/".$this -> lesson['id']."/\$1", $data['files'][$i]['path']);
                if (is_file(G_ROOTPATH.$newName)) {
                    $replaceString['/\/?(view_file.php\?file=)'.$data['files'][$i]['id'].'([^0-9])/'] = '${1}'.array_search(G_ROOTPATH.$newName, $fileList).'${2}'; //Replace old ids with new ids
                }
            }
        }
        for ($i = 0; $i < sizeof($data['files']); $i++) {
            if (isset($data['files'][$i]['file'])) {
                $newName = str_replace(G_ROOTPATH, '', dirname($data['files'][$i]['file']).'/'.EfrontFile :: encode(basename($data['files'][$i]['file'])));
                $newName = preg_replace("#(.*)www/content/lessons/#", "www/content/lessons/", $newName);
                $newName = preg_replace("#www/content/lessons/\d+/(.*)#", "www/content/lessons/".$this -> lesson['id']."/\$1", $newName);
                if ($data['files'][$i]['original_name'] != basename($data['files'][$i]['file'])) {
                    if (is_dir(G_ROOTPATH.$newName)) {
                        $file = new EfrontDirectory(G_ROOTPATH.$newName);
                        $file -> rename(G_ROOTPATH.dirname($newName).'/'.EfrontFile :: encode(rtrim($data['files'][$i]['original_name'], "/")));
                    }
                }
            }
        }
        unset($data['files']);
        $last_current_node = 0;
        $existing_tree = eF_getContentTree($nouse, $this -> lesson['id'], 0, false, false);
        if (sizeof($existing_tree) > 0) {
            $last_current_node = $existing_tree[sizeof($existing_tree) - 1]['id'];
            $first_node = self :: eF_import_getTreeFirstChild($data['content']);
            $data['content'][$first_node]['previous_content_ID'] = $last_current_node;
        }
        // MODULES - Import module data
        // Get all modules (NOT only the ones that have to do with the user type)
        $modules = eF_loadAllModules();
        foreach ($modules as $module) {
            if (isset($data[$module->className])) {
                $module -> onImportLesson($this -> lesson['id'], $data[$module->className]);
                unset($data[$module->className]);
            }
        }
        $dbtables = eF_showTables();
        //Skip tables that don't exist in current installation, such as modules' tables
        foreach (array_diff(array_keys($data), $dbtables) as $value) {
            unset($data[$value]);
        }
        //tests_to_questions table requires special handling	
  //$testsToQuestions = $data['tests_to_questions'];
  //unset($data['tests_to_questions']);
        if (!$data['questions'] && $data['tests_to_questions']) {
            unset($data['tests_to_questions']);
        }
        foreach ($data as $table => $tabledata) {
            if ($table == "glossary_words") {
                $table = "glossary";
            }
            if ($table == "lessons") { //from v3 lessons parameters also imported
                if ($lessonProperties) {
                    unset($data['lessons']['id']);
                    unset($data['lessons']['directions_ID']);
                    unset($data['lessons']['created']);
                    $this -> lesson = array_merge($this -> lesson, $data['lessons']);
                    $this -> persist();
                } else {
                 eF_updateTableData("lessons", array('info' => $data['lessons']['info'],
                                                     'metadata' => $data['lessons']['metadata'],
                                                     'options' => $data['lessons']['options']), "id=".$this -> lesson['id']);
                }
                if ($keepName) {
                    eF_updateTableData("lessons", array("name" => $data['lessons']['name']), "id=".$this -> lesson['id']);
                }
            } else {
                if ($table == "questions") {
                    foreach ($tabledata as $key => $value) {
                        unset($tabledata[$key]['timestamp']);
                        $tabledata[$key]['lessons_ID'] = $this -> lesson['id'];
      if ($tabledata[$key]['estimate'] == "") {
       unset($tabledata[$key]['estimate']);
      }
      if (isset($tabledata[$key]['code'])) { //code field removed in version 3.6
       unset($tabledata[$key]['code']);
      }
                    }
                }
                if ($table == "tests") {
                    for ($i = 0; $i < sizeof($tabledata); $i++) {
                        if (!isset($tabledata[$i]['options'])) {
                            $tabledata[$i]['options'] = serialize(array('duration' => $tabledata[$i]['duration'],
                                                                        'redoable' => $tabledata[$i]['redoable'],
                                                                        'onebyone' => $tabledata[$i]['onebyone'],
                                                                        'answers' => $tabledata[$i]['answers'],
                                                                        'given_answers' => $tabledata[$i]['given_answers'],
                                                                        'shuffle_questions' => $tabledata[$i]['shuffle_questions'],
                                                                        'shuffle_answers' => $tabledata[$i]['shuffle_answers']));
                            unset($tabledata[$i]['duration']);
                            unset($tabledata[$i]['redoable']);
                            unset($tabledata[$i]['onebyone']);
                            unset($tabledata[$i]['answers']);
                            unset($tabledata[$i]['given_answers']);
                            unset($tabledata[$i]['shuffle_questions']);
                            unset($tabledata[$i]['shuffle_answers']);
                        }
                    }
                }
                for ($i = 0; $i < sizeof($tabledata); $i++) {
                    if ($table == "tests") {
                        if (!isset($tabledata[$i]['lessons_ID'])) {
                            $tabledata[$i]['lessons_ID'] = $this -> lesson['id'];
                        }
                    }
                    if ($tabledata[$i]) {
                     $sql = "INSERT INTO ".G_DBPREFIX.$table." SET ";
                     $connector = "";
                     $fields = array();
                        foreach ($tabledata[$i] as $key => $value) {
                            if ($key == "id") {
                                $old_id = $value;
                            } else {
                                if (($table == "content" AND $key == "data") || ($table == "questions" AND $key == "text") || ($table == "tests" AND $key == "description")) {
                                 $value = str_replace("##SERVERNAME##", "", $value);
                                    //$value = str_replace("/##LESSONSLINK##", "content/lessons/".$this -> lesson['id'], $value);
                                    $value = str_replace("##LESSONSLINK##", "content/lessons/".$this -> lesson['id'], $value);
                                    $content_data = $value;
                                } elseif ($key == "lessons_ID") {
                                    $value = $this -> lesson['id'];
                                } elseif ($table == "lesson_conditions" AND $key == "options") {
                                    if (mb_strpos($data['lesson_conditions'][$i]['type'], "specific") === false){
                                    }else{
                                        $options = unserialize($data['lesson_conditions'][$i]['options']);
                                        $options[0] = $map['content'][$options[0]];
                                        $value = serialize($options);
                                    }
                                }
                                elseif ($table != "content" AND mb_substr($key, -3) == "_ID") {
                                    $from_table = mb_substr($key, 0, -3);
                                    if (isset($map[$from_table][$value])) {
                                        $value = $map[$from_table][$value];
                                    }
                                }
                                if ($table == 'scorm_sequencing_content_to_organization' && $key == 'organization_content_ID') {
                                    $value = $map['content'][$value];
                                }
                                if ($table == 'scorm_sequencing_maps_info' && $key == 'organization_content_ID') {
                                    $value = $map['content'][$value];
                                }
                                if ($table == "content" AND $key == 'previous_content_ID' AND !$value) {
                                    $value = 0;
                                }
                                if (!($table == "content" AND $key == "format")) {
                                    //$sql .= $connector.$key."='".str_replace("'","''",$value)."'";
                                    //$connector = ", ";
                                    $fields[$key] = $value;
                                }
                                if ($table == "content" AND $key == "name") {
                                    $content_name = $value;
                                }
                            }
                        }
                        $new_id = eF_insertTableData($table, $fields);
                     //eF_executeNew($sql);
                     //$new_id = mysql_insert_id();
                     //$GLOBALS['db']->debug=true;
                     if ($table == "content") {
                         EfrontSearch :: insertText($content_name, $new_id, "content", "title");
                         EfrontSearch :: insertText(strip_tags($content_data), $new_id, "content", "data");
                     }
                     $map[$table][$old_id] = $new_id;
                    }
                }
            }
        }
        if ($data['content']) {
   $map['content'] = array_reverse($map['content'], true);
            foreach($map['content'] as $old_id => $new_id) {
                eF_updateTableData("content", array('parent_content_ID' => $new_id), "parent_content_ID=$old_id AND lessons_ID=".$this -> lesson['id']);
                eF_updateTableData("content", array('previous_content_ID' => $new_id), "previous_content_ID=$old_id AND lessons_ID=".$this -> lesson['id']);
                //eF_updateTableData("questions", array('content_ID' => $new_id), "content_ID=$old_id");
            }
        }
        if ($data['rules']) {
            foreach($map['content'] as $old_id => $new_id) {
                eF_updateTableData("rules", array('rule_content_ID' => $new_id), "rule_content_ID=$old_id");
            }
        }
        // Update lesson skill
        $lessonSkillId = $this -> getLessonSkill();
        // The lesson offers skill record remains the same
        if ($lessonSkillId) {
            eF_updateTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFLESSON . " ". $this -> lesson['name'] , "categories_ID" => -1), "skill_ID = ". $lessonSkillId['skill_ID']);
        }
        if ($data['questions']) {
            foreach($map['questions'] as $old_id => $new_id) {
    eF_updateTableData("tests_to_questions", array('previous_question_ID' => $new_id), "previous_question_ID=$old_id and tests_ID in (select id from tests where lessons_ID=".$this -> lesson['id'].")");
                // Update all questions of not course_only lessons to offer the lessons skill
                if ($lessonSkillId) {
                    eF_insertTableData("questions_to_skills", array("questions_id" => $new_id, "skills_ID" => $lessonSkillId['skill_ID'], "relevance" => 2));
                }
                //eF_insertTableData("questions_to_skills", array("q
                //$questions = eF_getTableDataFlat("questions", "id", "lessons_ID = ". $this ->lesson['id']);
                //eF_deleteTableData("questions_to_skills", "questions_id IN ('".implode("','",$questions['id'])."')");
            }
        }
        foreach ($map['content'] as $old_id => $new_id) { //needs debugging
            $content_new_IDs[] = $new_id;
        }
        $content_new_IDs_list = implode(",",$content_new_IDs);
        if ($content_new_IDs_list) {
            $content_data = eF_getTableData("content", "data,id", "id IN ($content_new_IDs_list) AND lessons_ID=".$this -> lesson['id']);
        }
        if (isset($replaceString)) {
            for ($i = 0; $i < sizeof($content_data); $i++) {
                $replaced = preg_replace(array_keys($replaceString), array_values($replaceString), $content_data[$i]['data']);
                eF_updateTableData("content", array('data' => $replaced), "id=".$content_data[$i]['id']);
                EfrontSearch :: removeText('content', $content_data[$i]['id'], 'data'); //Refresh the search keywords
                EfrontSearch :: insertText($replaced, $content_data[$i]['id'], "content", "data");
            }
        }
        if ($content_new_IDs_list) {
            $content_data = eF_getTableData("content", "data,id", "id IN ($content_new_IDs_list) AND lessons_ID=".$this -> lesson['id']." AND data like '%##EFRONTINNERLINK##%'");
        }
        for($i =0; $i < sizeof($content_data); $i++) {
         preg_match_all("/##EFRONTINNERLINK##.php\?ctg=content&amp;view_unit=(\d+)/", $content_data[$i]['data'], $regs);
         foreach ($regs[1] as $value) {
             $replaced = str_replace("##EFRONTINNERLINK##.php?ctg=content&amp;view_unit=".$value,"##EFRONTINNERLINK##.php?ctg=content&amp;view_unit=".$map["content"][$value], $content_data[$i]['data']);
             eF_updateTableData("content", array('data' => $replaced), "id=".$content_data[$i]['id']);
             EfrontSearch :: removeText('content', $content_data[$i]['id'], 'data'); //Refresh the search keywords
             EfrontSearch :: insertText($replaced, $content_data[$i]['id'], "content", "data");
         }
        }
        $tests = eF_getTableData("tests t, content c", "t.id, t.name, c.name as c_name", "t.content_ID=c.id");
        foreach ($tests as $test) {
            if (!$test['name']) {
                eF_updateTableData("tests", array("name" => $test['c_name']), "id=".$test['id']);
            }
        }
//exit;        
        return true;
    }
    /**

     * Fix efront v1.x trees

     *

     * This function is used for fixing lessons from efront version 1 that

     * are beeing imported to the system

     *

     * @param array $tree The old content tree

     * @param int $last_current_node The last current node

     * @return array The fixed tree

     * @since 3.5.0

     * @access private

     */
    private function eF_import_fixTree($tree, $last_current_node = 0)
    {
        for ($i = 0; $i < sizeof($tree); $i++) {
            if ($tree[$i]['parent_content_ID'] == 0) {
                $roots[$i] = $tree[$i];
                $roots[$i]['idx'] = $i;
            }
        }
        foreach ($roots as $key => $node) {
            $roots[$key]['last_child'] = self :: eF_import_getLastChild($tree, $key);
            if ($node['previous_content_ID'] == 0 && $node['parent_content_ID'] == 0) {
                $eligible[] = $roots[$key];
            }
        }
        foreach ($eligible as $key => $node) {
            $timestamps[] = $node['timestamp'];
            $found = true;
            $temp = $roots;
            $eligible[$key]['final_child'] = $node['last_child'];
            while (sizeof($temp) > 0 && $found) {
                $found = false;
                foreach ($temp as $temp_key => $temp_node) {
                    if ($eligible[$key]['final_child'] == $temp_node['previous_content_ID']) {
                        $eligible[$key]['final_child'] = $temp_node['last_child'];
                        unset($temp[$temp_key]);
                        $found = true;
                    }
                }
            }
        }
        array_multisort($timestamps, SORT_ASC, $eligible);
        for ($i = 0; $i < sizeof($eligible); $i++) {
            if ($i < sizeof($eligible) - 1) {
                $eligible[$i + 1]['previous_content_ID'] = $eligible[$i]['final_child'];
            }
            unset($eligible[$i]['last_child']);
            unset($eligible[$i]['final_child']);
            $idx = $eligible[$i]['idx'];
            unset($eligible[$i]['idx']);
            $tree[$idx] = $eligible[$i];
        }
        return $tree;
    }
    /**

     * Get the tree last child

     *

     * This function is used for importing lessons and returns the tree's last child

     *

     * @param array $tree The content tree

     * @param int $idx The current index

     * @return int The last children index

     * @since 3.5.0

     * @access private

     */
    private function eF_import_getLastChild($tree, $idx) {
        $original_tree = $tree;
        $count = 0;
        $children[$idx] = $tree[$idx]['id'];
        $found = true;
        while (sizeof($tree) > 0 && $count++ < 1000 && $found) {
            $found = false;
            foreach ($tree as $key => $node) {
                if (in_array($node['parent_content_ID'], $children)) {
                    $children[$key] = $node['id'];
                    unset($tree[$key]);
                    $found = true;
                }
            }
        }
        foreach ($children as $key => $child) {
            $previous[] = $original_tree[$key]['previous_content_ID'];
        }
        $last = (array_diff($children, $previous));
        $last = array_values($last);
        return $last[0];
    }
    /**

     * Get the first child of the tree

     *

     * This function is used for importing lessons and returns the tree's first child

     *

     * @param array $tree

     * @return int the first child index

     * @since 3.5.0

     * @access private

     */
    private function eF_import_getTreeFirstChild($tree)
    {
        $count = 0;
        while ($tree[$count]['parent_content_ID'] != 0 || $tree[$count]['previous_content_ID'] != 0)
        {
            $count++;
        }
        $first_node = $count;
        return $first_node;
    }
    /**

     * Export lesson

     *

     * This function is used to export the current lesson's data to

     * a file, which can then be imported to other systems. Apart from

     * the lesson content, the user may optinally specify additional

     * information to export, using the $exportEntities array. If

     * $exportEntities is 'all', everything that can be exported, is

     * exported

     *

     * <br/>Example:

     * <code>

     * $exportedFile = $lesson -> export('all');

     * </code>

     *

     * @param array $exportEntities The additional data to export

     * @param boolean $rename Whether to rename the exported file with the same name as the lesson

     * @return EfrontFile The object of the exported data file

     * @since 3.5.0

     * @access public

     */
    public function export($exportEntities, $rename = true) {
        if (!$exportEntities) {
            $exportEntities = array('export_surveys' => 1, 'export_announcements' => 1, 'export_glossary' => 1,
                                    'export_calendar' => 1, 'export_comments' => 1, 'export_rules' => 1);
        }
        if (is_file($this -> directory.'data.tar.gz')) {
            try {
                $file = new EfrontFile($this -> directory.'data.tar.gz');
                $file -> delete();
            } catch (EfrontFileException $e) {
                unlink($this -> directory.'data.tar.gz');
            }
        }
        $data['lessons'] = $this -> lesson;
        unset($data['lessons']['shared_folder']);
        $content = eF_getTableData("content", "*", "lessons_ID=".$this -> lesson['id']);
        if (sizeof($content) > 0) {
            for ($i = 0; $i < sizeof($content); $i++) {
                $content[$i]['data'] = str_replace(G_SERVERNAME, "##SERVERNAME##", $content[$i]['data']);
                $content[$i]['data'] = str_replace("content/lessons/".$this -> lesson['id'], "##LESSONSLINK##", $content[$i]['data']);
            }
            $content_list = implode(",", array_keys($content));
            $data['content'] = $content;
            $questions = eF_getTableData("questions", "*", "lessons_ID=".$this -> lesson['id']);
            if (sizeof($questions) > 0) {
                for ($i = 0; $i < sizeof($questions); $i++) {
                    $questions[$i]['text'] = str_replace(G_SERVERNAME, "##SERVERNAME##", $questions[$i]['text']);
                    $questions[$i]['text'] = str_replace("content/lessons/".$this -> lesson['id'], "##LESSONSLINK##", $questions[$i]['text']);
                }
                $data['questions'] = $questions;
            }
            $tests = eF_getTableData("tests", "*", "lessons_ID=".$this -> lesson['id']);
            if (sizeof($tests)) {
    $testsIds = array();
    foreach ($tests as $key => $value) {
     $testsIds[] = $value['id'];
    }
                $tests_list = implode(",", array_values($testsIds));
                $tests_to_questions = eF_getTableData("tests_to_questions", "*", "tests_ID IN ($tests_list)");
                for ($i = 0; $i < sizeof($tests); $i++) {
                    $tests[$i]['description'] = str_replace(G_SERVERNAME, "##SERVERNAME##", $tests[$i]['description']);
                    $tests[$i]['description'] = str_replace("content/lessons/".$this -> lesson['id'], "##LESSONSLINK##", $tests[$i]['description']);
                }
                $data['tests'] = $tests;
                $data['tests_to_questions'] = $tests_to_questions;
            }
            if (isset($exportEntities['export_rules'])) {
                $rules = eF_getTableData("rules", "*", "lessons_ID=".$this -> lesson['id']);
                if (sizeof($rules) > 0) {
                    $data['rules'] = $rules;
                }
            }
            if (isset($exportEntities['export_comments'])) {
                $comments = eF_getTableData("comments", "*", "content_ID IN ($content_list)");
                if (sizeof($comments) > 0) {
                    $data['comments'] = $comments;
                }
            }
        }
        if (isset($exportEntities['export_calendar'])) {
            $calendar = eF_getTableData("calendar", "*", "lessons_ID=".$this -> lesson['id']);
            if (sizeof($calendar) > 0) {
                $data['calendar'] = $calendar;
            }
        }
        if (isset($exportEntities['export_glossary'])) {
            $glossary = eF_getTableData("glossary", "*", "lessons_ID = ".$this -> lesson['id']);
            if (sizeof($glossary) > 0) {
                $data['glossary'] = $glossary;
            }
        }
        if (isset($exportEntities['export_announcements'])) {
            $news = eF_getTableData("news", "*", "lessons_ID=".$this -> lesson['id']);
            if (sizeof($news) > 0) {
                $data['news'] = $news;
            }
        }
        if (isset($exportEntities['export_surveys'])) {
            $surveys = eF_getTableData("surveys", "*", "lessons_ID=".$this -> lesson['id']); //prepei na ginei to   lesson_ID -> lessons_ID sti basi (ayto isos to parampsoyme eykola)
            if (sizeof($surveys) > 0) {
                $data['surveys'] = $surveys;
                $surveys_list = implode(",", array_keys($surveys));
                $questions_to_surveys = eF_getTableData("questions_to_surveys", "*", "surveys_ID IN ($surveys_list)"); // oposipote omos to survey_ID -> surveys_ID sti basi
                if (sizeof($questions_to_surveys) > 0) {
                    $data['questions_to_surveys'] = $questions_to_surveys;
                }
            }
        }
        $lesson_conditions = eF_getTableData("lesson_conditions", "*", "lessons_ID=".$this -> lesson['id']);
        if (sizeof($lesson_conditions) > 0) {
            $data['lesson_conditions'] = $lesson_conditions;
        }
        $projects = eF_getTableData("projects", "*", "lessons_ID=".$this -> lesson['id']);
        if (sizeof($projects) > 0) {
            $data['projects'] = $projects;
        }
        $lesson_files = eF_getTableData("files", "*", "path like '".str_replace(G_ROOTPATH, '', EfrontDirectory :: normalize($this -> directory))."%'");
        if (sizeof($lesson_files) > 0) {
            $data['files'] = $lesson_files;
        }
        //'scorm_sequencing_rollup_rule', 'scorm_sequencing_rule',
        // MODULES - Export module data
        // Get all modules (NOT only the ones that have to do with the user type)
        $modules = eF_loadAllModules();
        foreach ($modules as $module) {
            if ($moduleData = $module -> onExportLesson($this -> lesson['id'])) {
                $data[$module -> className] = $moduleData;
            }
        }
        file_put_contents($this -> directory.'/'."data.dat", serialize($data)); //Create database dump file
        $lessonDirectory = new EfrontDirectory($this -> directory);
        $file = $lessonDirectory -> compress($this -> lesson['id'].'_exported.zip', false); //Compress the lesson files
        $newList = FileSystemTree :: importFiles($file['path']); //Import the file to the database, so we can download it
        $file = new EfrontFile(current($newList));
        $userTempDir = $GLOBALS['currentUser'] -> user['directory'].'/temp'; //The compressed file will be moved to the user's temp directory
        if (!is_dir($userTempDir)) { //If the user's temp directory does not exist, create it
            $userTempDir = EfrontDirectory :: createDirectory($userTempDir, false);
            $userTempDir = $userTempDir['path'];
        }
        try {
            $existingFile = new EfrontFile($userTempDir.'/'.$this -> lesson['name'].'.zip'); //Delete any previous exported files
            $existingFile -> delete();
        } catch (Exception $e) {}
        if ($rename) {
            $file -> rename($userTempDir.'/'.EfrontFile :: encode($this -> lesson['name']).'.zip', true);
        }
        unlink($this -> directory.'/'."data.dat"); //Delete database dump file
        return $file;
    }
    public function export2() {
        try {
         $dom = new DomDocument();
      $id = $dom -> createAttribute('id');// 
      $id -> appendChild($dom -> createTextNode($this -> lesson['id']));
      $lessonNode = $dom -> createElement("lesson");
      $lessonNode -> appendChild($id);
      $lessonNode = $dom -> appendChild($lessonNode);
      $parentNodes[0] = $lessonNode;
         $lessonContent = new EfrontContentTree($this -> lesson['id']);
         foreach ($iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $properties) {
             $result = eF_getTableData("content", "*", "id=$key");
          $contentNode = $dom -> appendChild($dom -> createElement("content")); //<content></content>
          $parentNodes[$iterator -> getDepth() + 1] = $contentNode;
          $attribute = $contentNode -> appendChild($dom -> createAttribute('id')); //<content id = ""></content>
          $attribute -> appendChild($dom -> createTextNode($key)); //<content id = "CONTENTID:32"></content>
          foreach ($result[0] as $element => $value) {
if ($element == 'data') $value = htmlentities($value);
              if ($element != 'id' && $element != 'previous_content_ID' && $element != 'parent_content_ID' && $element != 'lessons_ID') {
               $element = $contentNode -> appendChild($dom -> createElement($element));
               $element -> appendChild($dom -> createTextNode($value));
              }
          }
/*

		        if ($properties['ctg_type'] == 'tests') {

		            $result = eF_getTableData("tests", "*", "content_ID=$key");

		            foreach ($result[0] as $element => $value) {

		                

		            }

		        }

*/
             $parentNodes[$iterator -> getDepth()] -> appendChild($contentNode); //<lesson><content></content></lesson>
         }
         header("content-type:text/xml");
         echo (($dom -> saveXML()));
         //$content = eF_getTableData("content", "*", "lessons_ID=".$this -> lesson['id']);
        } catch (Exception $e) {
            pr($e);
        }
    }
    /**

     * Get system lessons

     *

     * This function is used used to return a list with all the system

     * lessons.

     * <br/>Example:

     * <code>

     * $lessons = EFrontLesson :: getLessons();

     * </code>

     *

     * @return array The lessons list

     * @since 3.5.0

     * @access public

     * @static

     */
    public static function getLessons($returnObjects = false) {
        $result = eF_getTableData("lessons l, directions d", "l.*, d.name as direction_name", "l.directions_ID=d.id and l.archive=0");
        foreach ($result as $value) {
            if ($returnObjects){
                $lessons[$value['id']] = new EfrontLesson($value);
            } else {
             $value['info'] = unserialize($value['info']);
             $lessons[$value['id']] = $value;
            }
        }
        return $lessons;
    }
    /**

     * Get system lessons that do not currently belong to a course

     *

     * This function is used used to return a list with all stand-alone 

     * system lessons.

     * <br/>Example:

     * <code>

     * $independent_lessons = EFrontLesson :: getStandAloneLessons();

     * </code>

     *

     * @param boolean $returnObjects whether to return objects

     * @return array The lessons list

     * @since 3.5.0

     * @access public

     * @static

     */
    public static function getStandAloneLessons($returnObjects = false) {
        $result = eF_getTableData("lessons l, directions d", "l.*, d.name as direction_name", "l.directions_ID=d.id AND l.course_only=0");
        foreach ($result as $value) {
            $value['info'] = unserialize($value['info']);
            if ($returnObjects) {
             $lessons[$value['id']] = new EfrontLesson($value);
            } else {
             $lessons[$value['id']] = $value;
            }
        }
        return $lessons;
    }
    /**

     * Get options

     *

     * This function is used to get the lesson specified options

     * <br/>Example:

     * <code>

     * $options = array('theory', 'tests');

     * $lesson -> getOptions($options);             //Get the values of 'theory' and 'tests' options

     * $lesson -> getOptions();                     //Get all options

     * </code>

     *

     * @param array $options An array of lesson options

     * @return array The values of the requested options

     * @since 3.5.0

     * @access public

     */
    public function getOptions($options) {
        if ($options && !is_array($options)) {
            $options = array($options);
        }
        if (sizeof($options) > 0) {
            $requestedOptions = array();
            foreach ($options as $value) {
                if (isset($this -> options[$value])) {
                    $requestedOptions[$value] = $this -> options[$value];
                }
            }
            return $requestedOptions;
        } else {
            return $this -> options;
        }
    }
    /**

     * Set options

     *

     * This function sets the lesson options, based on the array

     * specified

     * <br/>Example:

     * <code>

     * $options = array('theory' => 1, 'tests' => 0);

     * $lesson -> setOptions($options);

     * </code>

     *

     * @param array $options An array of lesson options

     * @since 3.5.0

     * @access public

     */
    public function setOptions($options) {
        foreach ($options as $key => $value) {
            if (isset($this -> options[$key])) {
                $this -> options[$key] = $value;
            }
        }
        $this -> lesson['options'] = serialize($this -> options);
        eF_updateTableData("lessons", array("options" => $this -> lesson['options']), "id=".$this -> lesson['id']);
    }
    /**

     * Store database values

     *

     * This function is used to store changed lesson properties

     * to the database.

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(4);           //Instantiate lesson with id 4

     * $lesson -> lesson['name'] = 'new name';  //Change a lesson's property, for example its name

     * $lesson -> persist();                    //Store any changed values to the database

     * </code>

     *

     * @since 3.5.0

     * @access public

     */
    public function persist() {
        $fields = array('name' => $this -> lesson['name'],
                        'directions_ID' => $this -> lesson['directions_ID'],
                        'info' => $this -> lesson['info'],
                        'price' => str_replace(array($GLOBALS['configuration']['decimal_point'], $GLOBALS['configuration']['thousands_sep']), array('.', ''), $this -> lesson['price']),
                        'active' => $this -> lesson['active'],
                        'duration' => $this -> lesson['duration'] ? $this -> lesson['duration'] : 0,
//                        'share_folder'    => $this -> lesson['share_folder'] ? $this -> lesson['share_folder'] : 0,
      'show_catalog' => $this -> lesson['show_catalog'],
                        'options' => serialize($this -> options),
                        'languages_NAME' => $this -> lesson['languages_NAME'],
                        'metadata' => $this -> lesson['metadata'],
                        'course_only' => $this -> lesson['course_only'],
                        'certificate' => $this -> lesson['certificate'],
//                        'auto_certificate'=> $this -> lesson['auto_certificate'],
//                        'auto_complete'   => $this -> lesson['auto_complete'],
//                        'publish'         => $this -> lesson['publish'],
                        'max_users' => $this -> lesson['max_users'] ? $this -> lesson['max_users'] : null,
                        'from_timestamp' => $this -> lesson['from_timestamp'] ? $this -> lesson['from_timestamp'] : 0,
                        'to_timestamp' => $this -> lesson['to_timestamp'] ? $this -> lesson['to_timestamp'] : 0,
                        'shift' => $this -> lesson['shift'],
                        'archive' => $this -> lesson['archive'],
            'created' => $this -> lesson['created']);
        if (!eF_updateTableData("lessons", $fields, "id=".$this -> lesson['id'])) {
            throw new EfrontUserException(_DATABASEERROR, EfrontUserException :: DATABASE_ERROR);
        }
        EfrontSearch :: removeText('lessons', $this -> lesson['id'], 'title'); //Refresh the search keywords
        EfrontSearch :: insertText($fields['name'], $this -> lesson['id'], "lessons", "title");
    }
    /**

     * Get lesson conditions

     *

     * This function can be used to retrieve the conditions set

     * for the current lesson

     * <br/>Example:

     * <code>

     * $lesson -> getConditions();                  //Returns an array with the lesson conditions

     * </code>

     *

     * @param array $conditions The conditions array, as a result query

     * @return array The lesson conditions

     * @since 3.5.0

     * @access public

     */
    public function getConditions($conditions = false) {
        if ($this -> conditions === false) {
         if (!$conditions) {
          $conditions = eF_getTableData("lesson_conditions", "*", "lessons_ID=".$this -> lesson['id']);
         }
            $this -> conditions = array();
            foreach ($conditions as $value) {
                $value['options'] = unserialize($value['options']);
                $this -> conditions[$value['id']] = $value;
            }
        }
        return $this -> conditions;
    }
    /**

     * Delete lesson conditions

     *

     * This function is used to delete one or more lesson conditions

     * <br/>Example:

     * <code>

     * $lesson -> deleteConditions(3);                                  //Delete condition with id 3

     * $lesson -> deleteConditions(array(3, 6, 34));                    //Delete conditions with ids 3,6 and 34

     * </code>

     *

     * @param mixed $conditions An id or an array of ids

     * @return array The remaining conditions

     * @since 3.5.0

     * @access public

     */
    public function deleteConditions($conditions) {
        if ($this -> conditions === false) { //Initialize conditions, if you haven't done so
            $this -> getConditions();
        }
        if (!is_array($conditions)) { //Convert single condition to array
            $conditions = array($conditions);
        }
        foreach ($conditions as $conditionId) {
            if (eF_checkParameter($conditionId, 'id') && in_array($conditionId, array_keys($this -> conditions))) {
                eF_deleteTableData("lesson_conditions", "id=$conditionId");
                unset($this -> rules[$conditionId]);
            }
        }
        return $this -> conditions;
    }
    /**

     * Get lesson projects

     *

     * This function is usd to retrieve the projects of this lesson

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(65);                          //Create new lesson object

     * $projectList = $lesson -> getProjects();                 //Get all projects for this lesson

     * $projectList = $lesson -> getProjects(true);             //Get all projects for this lesson as an EfrontProject objects list

     * $projectList = $lesson -> getProjects(true, 'jdoe');     //Get projects assigned to 'jdoe' for this lesson, as an EfrontProject objects list

     * </code>

     *

     * @param boolean $returnObjects Whether to return EfrontProject objects or just an array with projects properties

     * @param string $login If specified, return projects only assigned to this user

     * @return array an array of lesson projects

     * @since 3.5.0

     * @access public

     */
    public function getProjects($returnObjects = false, $login = false, $nonExpired = false) {
        if ($login instanceof EfrontUser) {
            $login = $login -> user['login'];
        }
        if ($login && eF_checkParameter($login, 'login')) {
            !$nonExpired ? $result = eF_getTableData("projects p, users_to_projects up", "p.*, up.grade, up.comments, up.filename", "up.users_LOGIN = '$login' and up.projects_ID = p.id and p.lessons_ID=".$this -> lesson['id']) : $result = eF_getTableData("projects p, users_to_projects up", "p.*, up.grade, up.comments, up.filename", "p.deadline > ".time()." and up.users_LOGIN = '$login' and up.projects_ID = p.id and p.lessons_ID=".$this -> lesson['id']);
        } else {
            !$nonExpired ? $result = eF_getTableData("projects", "*", "lessons_ID=".$this -> lesson['id']) : $result = eF_getTableData("projects", "*", "deadline > ".time()." and lessons_ID=".$this -> lesson['id']);
        }
        $projects = array();
        foreach ($result as $value) {
            $returnObjects ? $projects[$value['id']] = new EfrontProject($value) : $projects[$value['id']] = $value;
        }
        return $projects;
    }
    /**

     * Print a link with tooltip

     *

     * This function is used to print a lesson link with a popup tooltip

     * containing information on this lesson. The link must be provided

     * and optionally the information.

     * <br/>Example:

     * <code>

     * echo $lesson -> toHTMLTooltipLink('student.php?ctg=control_panel&lessons_ID=2');

     * </code>

     *

     * @param string $link The link to print

     * @param array $lessonInformation The information to display (According to the EfrontLesson :: getInformation() format)

     * @since 3.5.0

     * @access public

     */
    public function toHTMLTooltipLink($link, $lessonInformation = false) {
  if ($GLOBALS['configuration']['disable_tooltip'] != 1) {
   if (!$lessonInformation) {
    $lessonInformation = $this -> getInformation();
   }
   sizeof($lessonInformation['content']) > 0 || sizeof($lessonInformation['tests']) > 0 ? $classes[] = 'nonEmptyLesson' : $classes[] = 'emptyLesson'; //Display the link differently depending on whether it has content or not
   if (!$link) {
    $link = 'javascript:void(0)';
    $classes[] = 'inactiveLink';
   }
   if ($lessonInformation['professors']) {
    foreach ($lessonInformation['professors'] as $value) {
     $professorsString[] = $value['name'].' '.$value['surname'];
    }
    $lessonInformation['professors'] = implode(", ", $professorsString);
   }
   foreach ($lessonInformation as $key => $value) {
    if ($value) {
     switch ($key) {
      case 'professors' : $tooltipInfo[] = '<strong>'._PROFESSORS."</strong>: $value<br/>"; break;
      case 'content' : $tooltipInfo[] = '<strong>'._CONTENTUNITS."</strong>: $value<br/>"; break;
      case 'tests' : $tooltipInfo[] = '<strong>'._TESTS."</strong>: $value<br/>"; break;
      case 'projects' : $GLOBALS['configuration']['disable_projects'] != 1 ? $tooltipInfo[] = '<strong>'._PROJECTS."</strong>: $value<br/>" : null; break;
      case 'course_dependency' : $tooltipInfo[] = '<strong>'._DEPENDSON."</strong>: $value<br/>"; break;
      case 'from_timestamp' : $tooltipInfo[] = '<strong>'._AVAILABLEFROM."</strong>: ".formatTimestamp($value, 'time_nosec')."<br/>";break;
      case 'to_timestamp' : $tooltipInfo[] = '<strong>'._AVAILABLEUNTIL."</strong>: ".formatTimestamp($value, 'time_nosec')."<br/>"; break;
      case 'general_description': $tooltipInfo[] = '<strong>'._GENERALDESCRIPTION."</strong>: $value<br/>"; break;
      case 'assessment' : $tooltipInfo[] = '<strong>'._ASSESSMENT."</strong>: $value<br/>"; break;
      case 'objectives' : $tooltipInfo[] = '<strong>'._OBJECTIVES."</strong>: $value<br/>"; break;
      case 'lesson_topics' : $tooltipInfo[] = '<strong>'._LESSONTOPICS."</strong>: $value<br/>"; break;
      case 'resources' : $tooltipInfo[] = '<strong>'._RESOURCES."</strong>: $value<br/>"; break;
      case 'other_info' : $tooltipInfo[] = '<strong>'._OTHERINFO."</strong>: $value<br/>"; break;
      default: break;
     }
    }
   }
   if (sizeof($tooltipInfo) > 0) {
    $classes[] = 'info';
    $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> lesson['name'].'
      <img class = "tooltip" border = "0" src="images/others/tooltip_arrow.gif"/><span class = "tooltipSpan">
      '.implode("", $tooltipInfo).'</span></a>';
   } else {
    $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> lesson['name'].'</a>';
   }
  } else {
   $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> lesson['name'].'</a>';
  }
        return $tooltipString;
    }
   /**

     * Get all skills: for the skills this lesson offers the lesson_ID value will be filled

     * 

     * <br/>Example:

     * <code>

     * $skillsOffered = $lesson -> getSkills();

     * </code>

     *

     * @param $only_own set true if only the skills of this lesson are to be returned and not all skills

     * @return an array with skills where each record has the form [skill_ID] => [lesson_ID, description, specification,skill_ID, categories_ID]

     * @since 3.5.0

     * @access public

     */
    public function getSkills($only_own = false) {
        if (!isset($this -> skills) || !$this -> skills) {
            $this -> skills = false; //Initialize skills to something
            $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON (module_hcd_lesson_offers_skill.skill_ID = module_hcd_skills.skill_ID AND module_hcd_lesson_offers_skill.lesson_ID='".$this -> lesson['id']."')", "description,specification, module_hcd_skills.skill_ID,lesson_ID,categories_ID","");
            foreach ($skills as $key => $skill) {
                if ($only_own && $skill['lesson_ID'] != $this -> lesson['id']) {
                    unset($skills[$key]);
                } else {
                 $skID = $skill['skill_ID'];
                    $this -> skills[$skID] = $skill;
                }
            }
        }
        return $this -> skills;
    }
   /**

     * Get all branches: for the branches this lesson offers the lesson_ID value will be filled

     * 

     * <br/>Example:

     * <code>

     * $branchesOfLesson = $lesson -> getBranches();

     * </code>

     *

     * @param $only_own set true if only the branches of this lesson are to be returned and not all branches

     * @return an array with branches where each record has the form [branch_ID] => [lesson_ID]

     * @since 3.6.0

     * @access public

     */
    public function getBranches($only_own = false) {
        if (!isset($this -> branches) || !$this -> branches) {
            $this -> branches = false; //Initialize branches to something
            $branches = eF_getTableData("module_hcd_branch LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID LEFT OUTER JOIN module_hcd_lesson_to_branch ON (module_hcd_lesson_to_branch.branches_ID = module_hcd_branch.branch_ID AND module_hcd_lesson_to_branch.lessons_ID='".$this -> lesson['id']."')", "module_hcd_branch.*, module_hcd_branch.branch_ID as branches_ID, module_hcd_lesson_to_branch.lessons_ID, branch1.name as father","");
            foreach ($branches as $key => $branch) {
                if ($only_own && $branch['lessons_ID'] != $this -> lesson['id']) {
                    unset($branches[$key]);
                } else {
                 $bID = $branch['branches_ID'];
                    $this -> branches[$bID] = $branch;
                }
            }
        }
        return $this -> branches;
    }
   /**

     * Insert the skill corresponding to this lesson: Every lesson is mapped to a skill like "Knowledge of that lesson"

     * This insertion takes place when a lesson is changed from course_only to regular lesson

     *

     * <br/>Example:

     * <code>

     * $lesson -> insertLessonSkill();

     * </code>

     *

     * @return the id of the newly created record in the module_hcd_lesson_offers_skill table or false if something went wrong

     * @since 3.5.2

     * @access public

     */
    public function insertLessonSkill() {
        // If insertion of a self-contained lesson add the corresponding skill
        // Insert the corresponding lesson skill to the skill and lesson_offers_skill tables
        $lessonSkillId = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFLESSON . " ". $this -> lesson['name'], "categories_ID" => -1));
        // Insert question to lesson skill records for all lesson questions
        $questions = eF_getTableData("questions", "id", "lessons_ID = ". $this ->lesson['id']);
        $insert_string = "";
        foreach ($questions as $question) {
            if ($insert_string != "") {
                $insert_string .= ",('" . $question['id']. "','" . $lessonSkillId . "',2)";
            } else {
                $insert_string .= "('".$question['id']."','".$lessonSkillId."',2)";
            }
        }
        if ($insert_string != "") {
            eF_executeNew("INSERT INTO questions_to_skills VALUES " . $insert_string);
        }
        return eF_insertTableData("module_hcd_lesson_offers_skill", array("lesson_ID" => $this -> lesson['id'], "skill_ID" => $lessonSkillId));
    }
    /**

     * Function to remove all course inherited skills by all courses where this lesson belongs

     */
    public function removeCoursesInheritedSkills() {
        $courses = $this -> getCourses(true);
        foreach ($courses as $course) {
            $courseSkill = $course -> getCourseSkill();
            if ($courseSkill) {
                eF_deleteTableData("questions_to_skills", "skills_ID = " . $courseSkill['skill_ID']);
            }
        }
    }
   /**

     * Delete the skill corresponding to this lesson: Every lesson is mapped to a skill like "Knowledge of that lesson"

     * This deletion takes place when a lesson is changed from regular lesson to course_only

     *

     * <br/>Example:

     * <code>

     * $lesson -> deleteLessonSkill();

     * </code>

     *

     * @return the result of the table deletion

     * @since 3.5.2

     * @access public

     */
    public function deleteLessonSkill() {
        // Delete the corresponding lesson skill to the skill and lesson_offers_skill tables
        $lesson_skill = eF_getTableData("module_hcd_skills JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID","*", "lesson_ID = ". $this -> lesson['id'] . " AND module_hcd_skills.categories_ID = -1");
        eF_deleteTableData("module_hcd_skills", "skill_ID = ". $lesson_skill[0]['skill_ID']);
        // Delete all question-to-lesson specific skill assignments
        $questions = eF_getTableDataFlat("questions", "id", "lessons_ID = ". $this ->lesson['id']);
        eF_deleteTableData("questions_to_skills", "questions_id IN ('".implode("','",$questions['id'])."') AND skills_ID = " . $lesson_skill[0]['skill_ID']);
        return eF_deleteTableData("module_hcd_lesson_offers_skill", "lesson_ID = " . $this -> lesson['id'] . " AND skill_ID = " . $lesson_skill[0]['skill_ID']);
    }
   /**

     * Get the skill corresponding to this lesson: Every lesson that is not course_only is

     * mapped to a skill like "Knowledge of that lesson"

     * <br/>Example:

     * <code>

     * $lesson_skill = $lesson -> getLessonSkill();

     * </code>

     *

     * @return An array of the form [skill_ID] => [lesson_ID, description, specification,skill_ID, categories_ID]

     * @since 3.5.2

     * @access public

     */
    public function getLessonSkill() {
        return false;
    }
   /**

     * Assign a skill to this lesson or update an existing skill description

     *

     * This function is used to correlate a skill to the lesson - if the

     * lesson is completed then this skill is assigned to the user that completed it

     *

     * <br/>Example:

     * <code>

     * $lesson -> assignSkill(2, "Beginner PHP knowledge");   // The lesson will offer skill with id 2 and "Beginner PHP knowledge"

     * </code>

     *

     * @param $skill_ID the id of the skill to be assigned

     * @return boolean true/false

     * @since 3.5.0

     * @access public

     */
    public function assignSkill($skill_ID, $specification) {
        $this -> getSkills();
        // Check if the skill is not assigned as offered by this lesson
        if ($this -> skills[$skill_ID]['lesson_ID'] == "") {
            if ($ok = eF_insertTableData("module_hcd_lesson_offers_skill", array("skill_ID" => $skill_ID, "lesson_ID" => $this -> lesson['id'], "specification" => $specification))) {
                $this -> skills[$skill_ID]['lesson_ID'] = $this -> lesson['id'];
                $this -> skills[$skill_ID]['specification'] = $specification;
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        } else {
            if ($ok = eF_updateTableData("module_hcd_lesson_offers_skill", array("specification" => $specification), "skill_ID = '".$skill_ID."' AND lesson_ID = '". $this -> lesson['id'] ."'") ) {
                $this -> skills[$skill_ID]['specification'] = $specification;
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        }
        return true;
    }
   /**

     * Remove a skill that is offered from this lesson

     *

     * This function is used to stop the correlation of a skill to the lesson - if the

     * lesson is completed then this skill is assigned to the user that completed it

     *

     * <br/>Example:

     * <code>

     * $lesson -> removeSkill(2);   // The lesson will stop offering skill with id 2

     * </code>

     *

     * @param $skill_ID the id of the skill to be removed from the skills to be offered list

     * @return boolean true/false

     * @since 3.5.0

     * @access public

     */
    public function removeSkill($skill_ID) {
        $this -> getSkills();
        // Check if the skill is not assigned as offered by this lesson
        if ($this -> skills[$skill_ID]['lesson_ID'] == $this -> lesson['id']) {
            if ($ok = eF_deleteTableData("module_hcd_lesson_offers_skill", "skill_ID = '".$skill_ID."' AND lesson_ID = '". $this -> lesson['id'] ."'") ) {
                $this -> skills[$skill_ID]['specification'] = "";
                $this -> skills[$skill_ID]['lesson_ID'] = "";
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        }
        return true;
    }
   /**

     * Assign a branch to this lesson 

     *

     * This function is used to correlate a branch to the lesson

     * All users of the branch should be assigned to this lesson

     *

     * <br/>Example:

     * <code>

     * $lesson -> assignBranch(2);   // The lesson will be assigned to branch with id 2 

     * </code>

     *

     * @param $branch_ID the id of the branch to be assigned

     * @return boolean true/false

     * @since 3.6.0

     * @access public

     */
    public function assignBranch($branch_ID) {
        $this -> getBranches();
        // Check if the branch is not assigned as offered by this lesson
        if ($this -> branches[$branch_ID]['lessons_ID'] == "") {
            if ($ok = eF_insertTableData("module_hcd_lesson_to_branch", array("branches_ID" => $branch_ID, "lessons_ID" => $this -> lesson['id']))) {
                $this -> branches[$branch_ID]['lessons_ID'] = $this -> lesson['id'];
                $newBranch = new EfrontBranch($branch_ID);
                $employees = $newBranch ->getEmployees(false,true); //get data flat
                $this -> addUsers($employees['login'], $employees['user_type']);
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        }
        return true;
    }
   /**

     * Remove association of a branch with this lesson

     *

     * This function is used to stop the correlation of a branch to the lesson

     *

     * <br/>Example:

     * <code>

     * $lesson -> removeBranch(2);   // The lesson will stop offering branch with id 2

     * </code>

     *

     * @param $branch_ID the id of the branch to be removed from the lesson

     * @return boolean true/false

     * @since 3.6.0

     * @access public

     */
    public function removeBranch($branch_ID) {
        $this -> getBranches();
        // Check if the branch is not assigned as offered by this lesson
        if ($this -> branches[$branch_ID]['lessons_ID'] == $this -> lesson['id']) {
            if ($ok = eF_deleteTableData("module_hcd_lesson_to_branch", "branches_ID = '".$branch_ID."' AND lessons_ID = '". $this -> lesson['id'] ."'") ) {
                $this -> branches[$branch_ID]['lessons_ID'] = "";
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        }
        return true;
    }
   /**

     * Get all events related with this lesson

     *

     * This function is used to acquire all events related for this lesson,

     * according to a topical timeline

     *

     * <br/>Example:

     * <code>

     * $lesson -> getEvents();   // Get all events of this lessons from the most recent to the oldest

     * </code>

     *

     * @param $topic_ID the id of the topic to which the return events for the timeline should belong

     * @param $returnObjects whether to return event objects or not

     * @param $avatarSize the normalization size for the avatar images

     * @param $limit maximum number of events to return 

     * @return boolean true/false

     * @since 3.6.0

     * @access public

     */
    public function getEvents($topic_ID = false, $returnObjects = false, $avatarSize, $limit = false) {
  if (!($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_LESSON_TIMELINES)) {
      return array();
  }
     if ($topic_ID) {
      // only current lesson users
      $users = $this -> getUsers();
      $users_logins = array_keys($users); // don't mix with course events - with courses_ID = $this->lesson['id']		
      $related_events = eF_getTableData("events", "*", "type = '".EfrontEvent::NEW_POST_FOR_LESSON_TIMELINE_TOPIC. "' AND entity_ID = '".$topic_ID."' AND lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."') AND (type < 50 OR type >74)", "timestamp desc");
        } else {
      // only current lesson users
      $users = $this -> getUsers();
      $users_logins = array_keys($users);
//    		if ($limit) {
//    			$related_events = eF_getTableData("events", "*", "lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."')", "timestamp desc LIMIT " . $limit);
//    			
//    		} else {
      $related_events = eF_getTableData("events", "*", "lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."')  AND (type < 50 OR type >74)	", "timestamp desc");
//    		}
        }
     if (!isset($avatarSize) || $avatarSize <= 0) {
      $avatarSize = 25;
     }
     $prev_event = false;
     $count = 0;
     $filtered_related_events = array();
     foreach($related_events as $key => $event) {
   $user = $users[$event['users_LOGIN']];
   // Logical combination of events
   if ($prev_event) {
    // since we have decreasing chronological order we now that $event['timestamp'] < $prev_event['timestamp']
    if ($event['users_LOGIN'] == $prev_event['event']['users_LOGIN'] && $event['type'] == $prev_event['event']['type'] && $prev_event['event']['timestamp'] - $event['timestamp'] < EfrontEvent::SAME_USER_INTERVAL) {
     unset($filtered_related_events[$prev_event['key']]);
     $count--;
    }
   }
   $filtered_related_events[$key] = $event;
         try {
             $file = new EfrontFile($user['avatar']);
             $filtered_related_events[$key]['avatar'] = $user['avatar'];
             list($filtered_related_events[$key]['avatar_width'], $filtered_related_events[$key]['avatar_height']) = eF_getNormalizedDims($file['path'],$avatarSize, $avatarSize);
         } catch (EfrontfileException $e) {
             $filtered_related_events[$key]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
             $filtered_related_events[$key]['avatar_width'] = $avatarSize;
             $filtered_related_events[$key]['avatar_height'] = $avatarSize;
         }
         $prev_event = array("key"=>$key, "event"=>$event);
   if ($limit && ++$count == $limit) {
    break;
   }
     }
     if ($returnObjects) {
            $eventObjects = array();
            foreach ($filtered_related_events as $event) {
                $eventObjects[] = new EfrontEvent($event);
            }
            return $eventObjects;
        } else {
            return $filtered_related_events;
        }
    }
}
?>
