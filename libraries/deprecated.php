<?php


/**
* Presents a comma separated list of units related to a lesson
*
* This function accepts a results table, of the same structure as returned by the eF_getTableData() function and
* optionally a $key string. It searches through the table for $key, and appends results in the list.
* <br/>Example:
* <code>
* $content       = eF_getTableData("content", "*", "lessons_ID=3");
* $content_list  = eF_makeListIDs($content);
* echo $content_list;
* //prints: 2952,2956,2958,2960,2961,2962,2963,2979,2980,2981,2982,2985,3023,3022,3020
* </code>
*
* @param array $data The table to search for results
* @param string $key The key to search for
* @return string The comma-separated list of results
* @version 1.0
* @deprecated. Should use getTableDataFlat() with implode() instead
*/
function eF_makeListIDs($data, $key = "id")
{
    if ($data) {
        for ($i = 0; $i < sizeof($data); $i++) {
            $IDs[] = $data[$i][$key];
        }
        $list = implode(",", $IDs);
        return $list;
    } else {
        return false;
    }
}

/**
* Create content structure
*
* This function creates the content structure in its primitive form. This form consists
* of a multidimensional array, where each value represents a unit. If a unit has children,
* these children exist as sub-arrays of the unit. This way, the array created closely matches 
* the tree structure
* 
* @param int $content_ID The content id to build the tree for
* @param int $lessons_ID The lesson to build the tree for, defaults to session value
* @param bool $only_active Whether it should return only active values, defaults to true\
* 
* @deprecated 
*
*/
function eF_createContentStructure($content_ID = 0, $lessons_ID = '', $only_active = true) {

    if (!$lessons_ID || !eF_checkParameter($lessons_ID, 'id')) {
        $lessons_ID = $_SESSION['s_lessons_ID'];
    }


    $units = eF_getTableData("content", "id, name, parent_content_ID, ctg_type, active, previous_content_ID, data", "lessons_ID=".$lessons_ID, "parent_content_ID, previous_content_ID");        //Get all the lesson units

    for ($i = 0; $i < sizeof($units); $i++) {                                                       //Initialize the nodes array
        $units[$i]['data'] == '' ? $units[$i]['data'] = false : $units[$i]['data'] = true;          //Set data bit, if the node has data.        
        
        $nodes[$units[$i]['id']]             = $units[$i];
        $nodes[$units[$i]['id']]['children'] = array();
    }

    $q = 0;
    while (sizeof($units) > 0 && $q++ < 1000 && !isset($wanted)) {                                   //$q is put here to prevent an infinite loop
        $leaves = eF_findLeaves($units);                                                            //Get the leaf nodes of the tree
        foreach ($leaves as $leaf) {
            $nodes[$leaf['parent_content_ID']]['children'][$nodes[$leaf['id']]['id']] = $nodes[$leaf['id']];

            if ($leaf['id'] == $content_ID) {                                                       //If the user asked for the tree below a specified content id, then put it in $wanted
                $wanted = $nodes[$leaf['id']];
            }
            unset($nodes[$leaf['id']]);
        }
    }
    isset($wanted) ? $nodes = array(0 => $wanted) : $nodes = $nodes[0]['children'];                 //$nodes now has either the whole tree (which was put in $nodes[0]['children'] from the loop above), or the branch under the specified content_ID (which was put in $wanted)

    $tree = array();
    $tree = eF_makeCompatibleTree($tree, $nodes, 0);                                                //Convert the tree from its multidimensional form to its flat form, which is used throughout eFront

    if (sizeof($tree) > 0) {
        $tree = eF_putInCorrectOrder($tree, $only_active);                                          //Reorder the units to match the content series
    }


    
    return $tree;    
}


/**
* Convert primitive content tree to eFront-compatible tree
*
* This recursive function will convert the given (multidimensional) units array to the corresponding (flat) 
* efront content tree. It starts with an empty array as input.
*
* @param array $tree The efront-compatible content tree array (empty on initial call)
* @param array $nodes The primitive content array
* @param int $level The current tree level (depth) to consider
* @return array The new, efront-compatible content tree
* @deprecated
*/
function eF_makeCompatibleTree(&$tree, $nodes, $level = 0) {
    foreach($nodes as $node) {
        $new_node = array('id'        => $node['id'], 
                          'name'      => $node['name'],
                          'ctg_type'  => $node['ctg_type'],
                          'parent_id' => $node['parent_content_ID'],
                          'level'     => $level,
                          'active'    => $node['active'],
                          'data'      => $node['data']);

        if (!isset($tree[$node['previous_content_ID']])) {
            $tree[$node['previous_content_ID']] = $new_node;
        } 

        eF_makeCompatibleTree($tree, $node['children'], $level + 1);
    }
    
    return $tree;
}


/**
* Reorder tree according to content sequence
*
* This function is used to reorder the tree array, so that the array values succession 
* match the content sucession. The reordering is done based on array indexes, as returned
* by eF_makeCompatibleTree
*
* @param array $tree The content tree, as returned by eF_makeCompatibleTree
* @param bool $only_active Whether the tree should contain only active units
* @return array The correct content tree
* @version 1.1
* @deprecated 
* Changes from version 1.0 to 1.1 (2007/06/18 - venakis):
* - Added the check if the first unit is active
*/
function eF_putInCorrectOrder($tree, $only_active) {
    $current_unit    = $tree[0];
    if ($current_unit['active'] || !$only_active) {
        $correct_tree[0] = $tree[0];
    }
    unset($tree[0]);
    foreach ($tree as $node) {
        $current_unit = $tree[$current_unit['id']];
        if ($current_unit['active'] == 1 || !$only_active) {
            $correct_tree[] = $current_unit;
        }
    }
    return $correct_tree;
}



/**
* Get the student seen content
*
* This function gets the units that the student has set as seen
* A unit is considered seen when the student clicks the corresponding icon
* below the unit content. The same way he may deignate a unit as unseen (except
* for test units).
*
* @param string $login The student login
* @param int $lessons_ID The current lesson id
* @return array An array holding the seen unit ids (including tests)
* @version 1.0
* @deprecated 
*/
function eF_getSeenContent($login, $lessons_ID)
{
    $done_content = eF_getTableDataFlat("users_to_lessons", "done_content", "users_LOGIN='".$login."' and lessons_ID=".$lessons_ID);
    $done_tests   = eF_getTableDataFlat("done_tests as dt,tests as t, content as c", "c.id", "dt.users_LOGIN='".$login."' AND t.content_ID = c.id AND c.lessons_ID = ".$lessons_ID." AND t.id=dt.tests_ID"); 
    (sizeof($done_content['done_content'][0]) > 0 && $done_content['done_content'][0]) ? $done_content = unserialize($done_content['done_content'][0])       : $done_content = array();
    sizeof($done_tests['id'][0])             > 0 ? $done_tests   = array_combine($done_tests['id'], $done_tests['id']) : $done_tests   = array();

    return ($done_content + $done_tests);
}



/**
* Calculates the lesson tree
*
* This function is used to create an array with all the lesson's units, sorted the right way, so that 
* parent-children and previous-next relationships are preserved. Each element of the array is another array,
* whose elements are holding unit information: Specifically, these elements are:
* - 'id'        : the unit id
* - 'name'      : the name of the unit
* - 'ctg_type'  : the unit type
* - 'parent_id' : the id of the unit's parent
* - 'level'     : The level of the unit
* - 'active'    : whether it is an active unit
* - 'data'      : Whether the unit has data.
* If a content id is specified, then only the tree under and including the specified unit is returned. content id
* defaults to 0, which means the entire lesson tree. We may also specify the content type, using the $ctg parameter. 
* This will result in returning a tree containing only units of the specified type. The $ctg_in_tree variable is 
* only used when $only_current or $only_done variables are set to true. It is an array that, after the function
* returns, will contain the unit types present in the list. So, if only theory and exercise units are present in
* the tree, then this variable will be equivalent to: array('theory'=>'theory', 'exercise'=>'exercise');
* <br/>Example:
* <code>
* // We name the first variable $nouse because we will not be using it
* // This call returns the full lesson tree, only active and done units.
* eF_getContentTree($nouse, $lessons_ID, 0, false, true, false, true, false);
* </code>
* 
* @param array $ctg_in_tree Lists the unit types present in the list.
* @param int $lessons_ID The lesson id
* @param int $content_ID The content id
* @param string $ctg The content type
* @param bool $only_active Shows only active units
* @param bool $only_current Shows only current units
* @param bool $only_done Shows only units done so far
* @param mixed $only_period If true, then it shows only units that belong to a currently active period. Otherwise, 
*                            if set to a number, then it shows only units assigned to the period with this id
* @param string $only_unseen Gets only units that the student with the specified login hasn't seen
* @deprecated 
* @return array The content tree
* @version 1.2.1
* Changes from version 1.2 to 1.2.1 (20/11/2005):
* - Replaced > with >= in the $only_period part, since, without it, it was displaying the first unit as clickable in the student tree, even if it wasn't eligible (i.e. out of period)
* Changes from version 1.1 to 1.2 (18/11/2005):
* - Moved the part that examines the $ctg at the end of the function body, in order to exclude units from the student theory / examples etc tree. 
*   Before that, parent units were appearing, of units not in the current period
* - Replaced > with >= in the $ctg check part
* Changes from version 1.0 to 1.1 (3/11/2005):
* - Replaced >= with > in many places
*
*/
function eF_getContentTree(&$ctg_in_tree, $lessons_ID = false, $content_ID = 0, $ctg = false, $only_active = true, $only_current = false, $only_done = false, $only_period = false, $only_unseen = false)
{
    if (!$lessons_ID) {
        return "";
    }
    if ($content_ID == "" OR $content_ID === false) {                                                   //defaults to 0
        $content_ID = 0;
    } 
    $counter = 0;
    $level   = 0;

    $tree = eF_createContentStructure($content_ID, $lessons_ID, $only_active);           //New way of getting the tree, at least 5 times faster (and many lines of code less)!

    $tree = eF_checkContentInTree($tree);                                                //Get done and current units (see function definition for more information on what "done" and "current" means)

    if ($_SESSION['s_type'] == 'student') {
        $seen_content = eF_getSeenContent($_SESSION['s_login'], $lessons_ID);
        //print_r($seen_content);
        foreach ($tree as $key => $value)
        {
            if (in_array($value['id'], $seen_content)) {
                $tree[$key]['seen']='yes';
            }
        }
    }    
/*
    foreach ($tree as $key => $value)
    {
        foreach ($seen_content as $key1 => $value1)
        {
            if ($value['id']==$value1['id'])
                $tree[$key]['seen']='yes';
        }
    }
*/
    $keep = array();

    if ($only_current) {
        $counter = 0;
        for ($i = sizeof($tree) - 1; $i >= 0; $i--) {
            //if (!eF_isCurrentContent($tree[$i]['id'])) {
            if (!$tree[$i]['isCurrentContent']) {
                $parent_keep = false;
                for ($k = 0; $k < sizeof($keep) AND !$parent_keep; $k++) {
                    if ($tree[$i]['id'] == $keep[$k]) {
                        $parent_keep    = true;
                        $keep[$counter] = $tree[$i]['parent_id'];
                        $counter++;
                    }
                }
                if (!$parent_keep) {
                    $tree = array_merge(array_slice($tree, 0, $i), array_slice($tree, $i + 1, sizeof($tree) - 1));
                }
            } else {
                $ctg_in_tree[$tree[$i]['ctg_type']] = $tree[$i]['ctg_type'];
                $keep[$counter] = $tree[$i]['parent_id'];
                $counter++;
            }
        }
    }
//print_r($tree);    
    $keep = array();

    if ($only_done) {
        $counter = 0;
        for ($i = sizeof($tree) - 1; $i >= 0; $i--) {
            //if (!eF_isDoneContent($tree[$i]['id'])) {
            if (!($tree[$i]['isDoneContent'])) {
                $parent_keep = false;
                for($k = 0; $k < sizeof($keep) AND !$parent_keep; $k++) {
                    if($tree[$i]['id'] == $keep[$k]) {
                        $parent_keep    = true;
                        $keep[$counter] = $tree[$i]['parent_id'];
                        $counter++;
                    }
                }
                if(!$parent_keep)
                    $tree = array_merge(array_slice($tree, 0, $i), array_slice($tree, $i + 1, sizeof($tree) - 1));
            } else {
                $ctg_in_tree[$tree[$i]['ctg_type']] = $tree[$i]['ctg_type'];
                $keep[$counter] = $tree[$i]['parent_id'];
                $counter++;
            }
        }
    }
    
    $keep = array();
    
    if ($only_active) {
        $counter = 0;
        for ($i = sizeof($tree) - 1; $i >= 0; $i--) {
            //if (!eF_isDoneContent($tree[$i]['id'])) {
            if (!$tree[$i]['isDoneContent']) {
                $parent_keep = false;
                for ($k = 0; $k < sizeof($keep) AND !$parent_keep; $k++) {
                    if ($tree[$i]['id'] == $keep[$k]) {
                        $parent_keep    = true;
                        $keep[$counter] = $tree[$i]['parent_id'];
                        $counter++;
                    }
                }
                if (!$parent_keep) {
                    $tree[$i]['active'] = false;
                }
            } else {
                $keep[$counter] = $tree[$i]['parent_id'];
                $counter++;
            }
        }
    }
    
    $keep = array();
    
    if ($only_period AND $only_period === true) {                                     
        $result = eF_getTableData("current_content", "content_ID");
        for ($i = 0; $i < sizeof($result); $i++) {
            $currentContent[$result[$i]['content_ID']] = $result[$i]['content_ID'];
        }

        $counter = 0;
        for ($i = sizeof($tree) - 1; $i >= 0; $i--) {
            //$res = eF_getTableData("current_content", "content_ID", "content_ID=".$tree[$i]['id']);
            //if (sizeof($res) == 0) {
            if (!isset($currentContent[$tree[$i]['id']])) {
                $parent_keep = false;
                for ($k = 0; $k < sizeof($keep) AND !$parent_keep; $k++) {
                    if ($tree[$i]['id'] == $keep[$k]) {
                        $parent_keep    = true;
                        $keep[$counter] = $tree[$i]['parent_id'];
                        $counter++;
                    }
                }
                if (!$parent_keep) {
                    $tree = array_merge(array_slice($tree, 0, $i), array_slice($tree, $i + 1, sizeof($tree) - 1));
                } else {
                    $tree[$i]['active'] = false;
                }
            } else {
                $keep[$counter] = $tree[$i]['parent_id'];
                $counter++;
            }
        }
    } elseif ($only_period) {
        $counter = 0;
        for ($i = sizeof($tree) - 1; $i >= 0; $i--) {
            $res = eF_getTableData("current_content", "content_ID", "content_ID=".$tree[$i]['id']." AND periods_ID=$only_period");
            if (sizeof($res) == 0) {
                $parent_keep = false;
                $keep        = array();
                for ($k = 0; $k < sizeof($keep) AND !$parent_keep; $k++) {
                    if ($tree[$i]['id'] == $keep[$k]) {
                        $parent_keep    = true;
                        $keep[$counter] = $tree[$i]['parent_id'];
                        $counter++;
                    }
                }
                if (!$parent_keep) {
                    $tree = array_merge(array_slice($tree, 0, $i), array_slice($tree, $i + 1, sizeof($tree) - 1));
                } else {
                    $tree[$i]['active'] = false;
                }
            } else {
                $keep[$counter] = $tree[$i]['parent_id'];
                $counter++;
            }
        }
    }
    
    $keep = array();
/*
    if ($only_unseen) {
        $counter = 0;
        for ($i = sizeof($tree) - 1; $i >= 0; $i--) {
            $res = eF_getTableData("logs","count(*) as times","(action='content' OR action='lessons' OR action = 'tests') AND users_LOGIN='".$only_unseen."' AND comments=".$tree[$i]['id']);
            if ($res[0]['times'] > 0 OR ($tree[$i]['data'] == false && $tree[$i]['ctg_type'] != 'tests')) {
                $parent_keep = false;
                for ($k = 0; $k < sizeof($keep) AND !$parent_keep; $k++) {
                    if ($tree[$i]['id'] == $keep[$k]) {
                        $parent_keep    = true;
                        $keep[$counter] = $tree[$i]['parent_id'];
                        $counter++;
                    }
                }
                if (!isset($tree[$i]['seen']) && $tree[$i]['ctg_type'] == 'tests') {            //This line checks against 'seen' attribute for tests, so that a test is considered 'unseen' not if it hasn't been visited, but if it hasn't been completed
                    //do nothing
                } else if (!$parent_keep) {
                    $tree = array_merge(array_slice($tree, 0, $i), array_slice($tree, $i + 1, sizeof($tree) - 1));
                } else {
                    $tree[$i]['active'] = false;
                }
            } else {
                $keep[$counter] = $tree[$i]['parent_id'];
                $counter++;
            }
        }
    }

    $keep = array();
*/
    if ($only_unseen) {
        $counter = 0;
        for ($i = sizeof($tree) - 1; $i >= 0; $i--) {
            if ($tree[$i]['seen'] == 'yes') {
                $parent_keep = false;
                for ($k = 0; $k < sizeof($keep) AND !$parent_keep; $k++) {
                    if ($tree[$i]['id'] == $keep[$k]) {
                        $parent_keep    = true;
                        $keep[$counter] = $tree[$i]['parent_id'];
                        $counter++;
                    }
                }
                if (!isset($tree[$i]['seen']) && $tree[$i]['ctg_type'] == 'tests') {            //This line checks against 'seen' attribute for tests, so that a test is considered 'unseen' not if it hasn't been visited, but if it hasn't been completed
                    //do nothing
                } else if (!$parent_keep) {
                    $tree = array_merge(array_slice($tree, 0, $i), array_slice($tree, $i + 1, sizeof($tree) - 1));
                } else {
                    $tree[$i]['active'] = false;
                }
            } else {
                $keep[$counter] = $tree[$i]['parent_id'];
                $counter++;
            }
        }
    }

    $keep = array();


    if ($ctg) {                                                                                         // ????? ???? ????????? ??????? ?? ???? ?? ctg
        $counter = 0;
        for ($i = sizeof($tree) - 1; $i >= 0; $i--) {
            if ($tree[$i]['ctg_type'] != $ctg) {
                $parent_keep = false;
                if (!isset($keep)) {
                    echo $keep = null;
                }
                for ($k = 0; $k < sizeof($keep) AND !$parent_keep; $k++) {
                    if ($tree[$i]['id'] == $keep[$k]) {
                        $parent_keep    = true;
                        $keep[$counter] = $tree[$i]['parent_id'];
                        $counter++;
                    }
                }
                if (!$parent_keep) {
                    $tree = array_merge(array_slice($tree, 0, $i), array_slice($tree, $i + 1, sizeof($tree) - 1));
                }
            } else {
                $keep[$counter] = $tree[$i]['parent_id'];
                $counter++;
            }
        }        
    }    
//    echo "<br>";print_r($tree);

    return $tree;
}


/**
* Parents of a unit
*
* This function returns an array containing the parents of a unit. 
* For example, for the unit 3.2.1, the array will hold the ids of the units 3.2.1,
* 3.2, 3 and the 0.
* <br/>Example:
* <code>
* $parents = eF_getParents(213);
* print_r($parents);
* //Returns:
* //Array
* //(
* //    [0] => 213
* //    [1] => 211
* //    [2] => 206
* //    [3] => 0
* //)
* </code>
* 
* @param int $content_ID The id of the unit
* @param array $tree The content tree
* @return array The unit's parents ids
* @version 2.0
* @deprecated 
* Changes from version 1.0 to version 2.0 (2007/05/15 - venakis):
* - Rewritten so that if $tree is specified, it doesn't perform any database queries
*/

function eF_getParents($content_ID, $tree = null, $tree_indexes = null) 
{
    if ($tree) {
        if (!$tree_indexes) {
            for ($i = 0; $i < sizeof($tree); $i++) {
                $tree_indexes[$tree[$i]['id']] = $i;
            }
        }

        $parents['id'][]   = $content_ID;
        $parents['name'][] = $tree[$tree_indexes[$content_ID]]['name'];
        
        while ($tree[$tree_indexes[$content_ID]]['parent_id'] != 0) {
            $content_ID = $tree[$tree_indexes[$content_ID]]['parent_id'];
            $parents['id'][]   = $content_ID;
            $parents['name'][] = $tree[$tree_indexes[$content_ID]]['name'];
        }
        
    } else {
        $now            = 0;
        $count          = 0;
        $parents['id'][0] = $content_ID;

        while ($now <= $count) {
            $child = $parents['id'][$now];
            $res   = eF_getTableData("content", "parent_content_ID, name", "id=$child");
            for ($i = 0; $i < sizeof($res) && $res[$i]['parent_content_ID'] != 0; $i++) {
                $parents['name'][$count] = $res[$i]['name'];
                $count++;
                $parents['id'][$count] = $res[$i]['parent_content_ID'];
            }
            $parents['name'][$count] = $res[$i]['name'];
            $now++;
        }
    }
    
    return $parents;
}

/**
* Checks if a user is eligible to access a unit, based on rules
*
* This function checks of the currently logged-in student is allowed to view a unit's content,
* based on access rules.
*
* @param int $content_ID the d of the current unit
* @return mixed true if the student is allowed to access the unit, or an explanatory message otherwise
* @version 2.0
* @deprecated
* @Changes from version 1.0 to 2.0 (2007/09/29 - venakis):
* - Rewritten so that it supports the new way of checking whether a unit is seen (through the explicit student action)
*/
function eF_checkRules($content_ID)
{
    $seen_content = eF_getSeenContent($_SESSION['s_login'], $_SESSION['s_lessons_ID']);

    $message = "";
    $parents = eF_getParents($content_ID);
    $list    = implode(",", $parents['id']);
    $rules   = eF_getTableData("rules", "rule_type, rule_content_ID, rule_option", "content_ID IN ($list) AND (users_LOGIN='*' OR users_LOGIN='".$_SESSION['s_login']."')");
    $allow   = true;

    for ($i = 0; $i < sizeof($rules); $i++) {
        if ($rules[$i]['rule_type'] == "always") {
            $allow    = false;
            $message .= _YOUHAVEBEENEXCLUDEDBYPROFESSOR.'<BR>';
        } elseif ($rules[$i]['rule_type'] == "hasnot_seen") {
            if (!in_array($rules[$i]['rule_content_ID'], $seen_content)) {
                $content  = eF_getTableData("content", "name", "id = ".$rules[$i]['rule_content_ID']);
                $allow    = false;
                $message .= _MUSTFIRSTREADUNIT.' <a href="student.php?ctg=content&view_unit='.$rules[$i]['rule_content_ID'].'">'.$content[0]['name'].'</a><br/>';
            }
        } elseif ($rules[$i]['rule_type'] == "hasnot_passed") {
            $res = eF_getTableData("tests, done_tests", "done_tests.id", "users_LOGIN='".$_SESSION['s_login']."' AND tests.content_ID=".$rules[$i]['rule_content_ID']." AND done_tests.tests_ID=tests.id AND score > ".$rules[$i]['rule_option']);
            if (!$res) {
                $content  = eF_getTableData("content", "name", "content.id=".$rules[$i]['rule_content_ID']);
                $allow    = false;
                $message .= _MUSTFIRSTTAKEATLEAST.' '.($rules[$i]['rule_option'] * 100).' % '._ATTEST.' <a href="student.php?ctg=tests&view_unit='.$rules[$i]['rule_content_ID'].'">'.$content[0]['name'].'</a><br/>';
            }
        }
    }

    if ($allow === true) {
        return ($allow);
    } else {
        return ($message);
    }
}



/**
* Convert backward slashes to forward slashes
*
* Thi function converts all the backslashes contained in a string to forward slashes.
* This is used to ensure cross-platform compatibility of the code (which, in general, uses
* forward slashes, '/').
*
* @param string $str The string to convert
* @return string The converted string
* @version 1.0
* @deprecated
*/
function eF_revertSlashes($str)
{
    $str = str_replace('\\', '/', $str);
    return $str;
}

/**
* Register a user with the system
*
* This function takes an array containing user information and stores it to the users
* database table. It also accepts two boolean parameters, corresponding to system configuration
* options. It returns a string with a success or failure message.
*
* @param array $values The user information array
* @param bool $is_automatic_activation Specifies whether new users are activated automatically.
* @param bool $is_ldap_user Specifies whether the user is ldap-based
* @return string An error or success message
* @version 1.2
* @todo Check for permissions in folder
* @todo return other than true
* @deprecated
* Changes from version 1.1 to 1.2:
* - Added if clauses and delete user on failure.
* Changes from version 1.0 to 1.1:
* - Got rid of forum
*/
function eF_registerUser($values, $is_automatic_activation, $is_ldap_user = false, $type="student")
{
    //$db -> debug = true;
    foreach ($values as $key => $value) {
        $fields_insert[$key] = $value;                                                                      //The values that will be inserted in the database
    }
    if ($type == ''){
        $fields_insert['user_type'] = "student";
    }else{
        $fields_insert['user_type'] = $type;
    }
    if($fields_insert['login'] == ''){
        $fields_insert['login'] = 'student';
    }

    if ($fields_insert['password'] == '') {
        $fields_insert['password']  = md5($fields_insert['login'].G_MD5KEY);
    } else {
        $fields_insert['password']  = md5($fields_insert['password'].G_MD5KEY);
    }

    if($fields_insert['languages_NAME'] == ''){
        $fields_insert['languages_NAME'] = $_SESSION['s_language'];
    }

    if ($is_ldap_user) {
        $fields_insert['password'] = 'ldap';                //For an LDAP user, the password always is 'ldap', so we can tell by looking at the password field that this is an LDAP user that authenticates to the LDAP server
    }

    $fields_insert['timestamp'] = time();
    if (!isset($fields_insert['active'])) {     //If the active field is not already set in the values, derive it
        if (!$is_automatic_activation) {      //Does the system permit automatic activation of new users?
            $fields_insert['pending'] = 1;
            $fields_insert['active']  = 0;
        } else {
            $fields_insert['pending'] = 0;
            $fields_insert['active']  = 1;
        }
    }
    //$fields_insert['valid_until_timestamp'] = time() + 365 * 86400;                                         //1 year

    //default avatar for new user
    $target_filename = time().'_prefix_unknown_small.png';
    copy (G_ROOTPATH.'www/images/avatars/system_avatars/unknown_small.png', G_ROOTPATH.'www/images/avatars/'.$target_filename);
    $fields_insert['avatar'] = $target_filename;

    if (eF_insertTableData("users", $fields_insert)) {                                                       //Create the new user by inserting a new entry in the users table
        if (is_dir(G_ROOTPATH.'upload/'.$values['login']) || mkdir(G_ROOTPATH.'upload/'.$values['login'])) {
            mkdir(G_ROOTPATH.'upload/'.$values['login'].'/message_attachments/');
            mkdir(G_ROOTPATH.'upload/'.$values['login'].'/message_attachments/Incoming');
            mkdir(G_ROOTPATH.'upload/'.$values['login'].'/message_attachments/Sent');
            mkdir(G_ROOTPATH.'upload/'.$values['login'].'/message_attachments/Drafts');
            mkdir(G_ROOTPATH.'upload/'.$values['login'].'/projects');
            $fields_insert = array('name'        => 'Incoming',
                                   'users_LOGIN' => $values['login'],
                                   'parent_id'   => 0);
            eF_insertTableData("f_folders", $fields_insert);


            $fields_insert = array('name'        => 'Sent',
                                   'users_LOGIN' => $values['login'],
                                   'parent_id'   => 0);
            eF_insertTableData("f_folders", $fields_insert);


            $fields_insert = array('name'        => 'Drafts',
                                   'users_LOGIN' => $values['login'],
                                   'parent_id'   => 0);
            eF_insertTableData("f_folders", $fields_insert);

            return true;
        } else {
            eF_deleteTableData("users", "login='".$values['login']."'");            //Delete inserted user in case of failure
            return false;
        }
    } else {
        return false;
    }

}

/**
* Delete user
*
* This function deletes the designated user
* @param string $login The user to delete
* @return bool true if the user was deleted
* @todo Ask if to delete forum data
* @version 1.0
*/
function eF_deleteUser($login) {
    if (eF_checkParameter($login, 'login')) {
        $user = eF_getTableData("users", "user_type, avatar", "login='".$login."'");

        eF_deleteTableData("users", "login='".$login."'");
        eF_deleteTableData("comments", "users_LOGIN='".$login."'");
        eF_deleteTableData("logs", "users_LOGIN='".$login."'");
        eF_deleteTableData("rules", "users_LOGIN='".$login."'");
        eF_deleteTableData("users_to_lessons", "users_LOGIN='".$login."'");
        eF_deleteTableData("scorm_data", "users_LOGIN='".$login."'");

        $result          = eF_getTableData("done_tests", "id", "users_LOGIN='".$login."'");
        $done_tests_list = eF_makeListIDs($result);
        if ($done_tests_list) {
            eF_deleteTableData("done_questions", "done_tests_ID IN ($done_tests_list)");
        }
        eF_deleteTableData("done_tests", "users_LOGIN='".$login."'");

        eF_deleteTableData("f_folders", "users_LOGIN='".$login."'");
        eF_deleteTableData("f_messages", "users_LOGIN='".$login."'");
        eF_deleteTableData("f_personal_messages", "users_LOGIN='".$login."'");
        eF_deleteTableData("chatmessages", "users_LOGIN='".$login."'");

        if ($user[0]['user_type'] != 'administrator') {                    //Delete chat and some other data, only if the users is not an administrator, otherwise you risk loosing much useful data
            eF_deleteTableData("groups", "users_LOGIN='".$login."'");
            eF_deleteTableData("chatrooms", "users_LOGIN='".$login."'");
        }

        if (is_dir(G_UPLOADPATH.$login)) {                            //Delete personal messages folder from system directory
            eF_deleteFolder(G_UPLOADPATH.$login);
        }

        if (is_file(G_AVATARSPATH.$user[0]['avatar'])) {                    //Delete user avatar
            unlink(G_AVATARSPATH.$user[0]['avatar']);
        }

        return true;
    } else {
        return false;
    }
}


/**
* Delete a personal message
*
* This function is used to delete a message, including any attachments it may have
*
* @param int $msg_id The message id
* @return bool True if the deletion was succesful
* @version 0.1
* @deprecated
*/
function eF_deletePersonalMessage($msg_id) {
    if (eF_checkParameter($msg_id, 'id')) {
        $res = eF_getTableData("f_personal_messages", "users_LOGIN, attachments, f_folders_ID", "id=".$msg_id);

        if ($_SESSION['s_login'] == $res[0]['users_LOGIN'] || $_SESSION['s_type'] == 'administrator') {
            eF_deleteTableData("f_personal_messages", "id=".$msg_id);

            if ($res[0]['attachments'] != '') {
                $attached_file = new EfrontFile($res[0]['attachments']);
                $attached_file -> delete();
            }

            return true;
        } else {
            $message = 'You cannot delete this message';
            return $message;
        }
    } else {
        $message = _INVALIDID;
        return $message;
    }
}

/**
* Move a personal message
*
* This function is used to move a message, including any attachments it may have
*
* @param int $msg_id The message id
* @param int $target_folder_id the target folder id
* @return bool True if the move was succesful
* @version 0.1
* @deprecated
*/
function eF_movePersonalMessage($msg_id, $target_folder_id) {
    if (eF_checkParameter($msg_id, 'id') && eF_checkParameter($target_folder_id, 'id')) {
        $res = eF_getTableData("f_personal_messages", "users_LOGIN, attachments, f_folders_ID", "id=".$msg_id);
        $folder_name = eF_getTableData("f_folders", "name", "users_LOGIN='".$_SESSION['s_login']."' and id=".$target_folder_id);

        if ($_SESSION['s_login'] == $res[0]['users_LOGIN'] || $_SESSION['s_type'] == 'administrator') {
            eF_updateTableData("f_personal_messages", array("f_folders_ID" => $target_folder_id), "id=".$msg_id);

            if ($res[0]['attachments'] && sizeof($folder_name) > 0) {
                $attachment = new EfrontFile($res[0]['attachments']);
                $attachment -> move(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$folder_name[0]['name'].'/');


/*
                $attachments = unserialize($res[0]['attachments']);
                foreach($attachments as $attach) {
                    rename($attach, G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$folder_name[0]['name'].'/'.basename($attach));
                    $new_attachments[] = G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$folder_name[0]['name'].'/'.basename($attach);
                }

                eF_updateTableData("f_personal_messages", array("attachments" => serialize($new_attachments)), "id=".$msg_id);
*/
            }
            return true;
        } else {
            $message = 'You cannot move this message';
            return $message;
        }
    } else {
        $message = _INVALIDID;
        return $message;
    }
}

/**
* Get recent forum messages
*
* This function reads recent forum messages from the forum category with the same name as the current lesson.
* It returns an array with the forum title and information about the message, which can be used to access it.
* If a lesson is not specified, the current (session) is considered.
* <br/>Example:
* <code>
* $messages = eF_getForumMessages($lessons_ID);
* print_r($messages);
* //Returns:
*Array
*(
*    [0] => Array
*        (
*            [title] => Some message
*            [id] => 53
*            [topic_id] => 27
*            [users_LOGIN] => periklis
*            [timestamp] => 1125765345
*        )
*
*    [1] => Array
*        (
*            [title] => Another message
*            [id] => 54
*            [topic_id] => 27
*            [users_LOGIN] => periklis
*            [timestamp] => 1125752345
*        )
*
*    [2] => Array
*        (
*            [title] => This is a large mess...
*            [id] => 55
*            [topic_id] => 27
*            [users_LOGIN] => admin
*            [timestamp] => 1125751543
*        )
*
*)
* </code>
*
* @param int $lessons_ID The lesson id
* @param int $limit The results limit
* @return array The messages array
* @version 1.1
* Changes from 1.0 to 1.1 (15/11/2005):
* - Added $limit parameter
*/
function eF_getForumMessages($lessons_ID = false, $limit = false)
{
    if (!$lessons_ID) {
        $lessons_ID = $_SESSION['s_lessons_ID'];
    }

    if ($limit && eF_checkParameter($limit, 'uint')) {
        $limit_str = ' limit '.$limit;
    } else {
        $limit_str = '';
    }

    $messages_array = array();
    if ($lessons_ID) {
        $messages_array = eF_getTableData("f_messages m, f_topics t, f_forums c", "m.title, m.id, t.id as topic_id, m.users_LOGIN, m.timestamp", "c.lessons_ID=".$lessons_ID." AND t.f_forums_ID=c.id AND m.f_topics_ID=t.id", "m.timestamp desc".$limit_str);
    }
    return $messages_array;
}

/**
* Get unread personal messages
*
* This function gets the unread personal messages from the user's inbox. It returns an array with
* the message title and information about the message, which can be used to access it. If $login
* is not set, then messages for the current (session) user are returned.
* <br/>Example:
* <code>
* $messages = eF_getPrivateMessages();
* print_r($messages);
* //Returns:
*Array
*(
*    [0] => Array
*        (
*            [title] => Hello!
*            [id] => 26
*        )
*
*    [1] => Array
*        (
*            [title] => It's me again!
*            [id] => 24
*        )
*
*    [2] => Array
*        (
*            [title] => This is a large mess...
*            [id] => 23
*        )
*
*)
* </code>
*
* @param string $login The user login name
* @param int $limit The results limit
* @return array The messages array
* @version 1.1
* Changes from 1.0 to 1.1 (15/11/2005):
* - Added $limit parameter
*/
function eF_getPersonalMessages($login = false, $limit = false) {

    if (!$login) {
        $login = $_SESSION['s_login'];
    } elseif (sizeof(eF_getTableData("users", "login", "login=$login")) == 0) {                             //This user does not exist
        return array();
    }

    if ($limit && eF_checkParameter($limit, 'uint')) {
        $limit_str = ' limit '.$limit;
    } else {
        $limit_str = '';
    }

    $messages_array = eF_getTableData("f_personal_messages pm, f_folders", "pm.title, pm.id, pm.timestamp, pm.sender", "pm.users_LOGIN='".$login."' and f_folders_ID=f_folders.id and f_folders.name='Incoming' and viewed='no'", "pm.timestamp desc".$limit_str);         //Get unseen messages in Incoming folder

    return $messages_array;
}



/**
* Gets dates offset for a student
*
* This functions is used to add an offset to all dates concerning a student that has 
* enrolled late to the system. The amount of time he enrolled after the first period 
* started is added to the current time, in order to normalize the content flow per period
*
* @return int The date offset is an integer to be added to a timestamp
* @version 1.0.1
* Changes from version 1.0 to 1.0.1:
* - Fixed bug: it always reported offset.
* @deprecated 
*/
function eF_getOffset()
{
    if ($GLOBALS['currentLesson'] -> options['dynamic_periods']) {
        $res           = eF_getTableData("users_to_lessons", "from_timestamp", "lessons_ID=".$_SESSION['s_lessons_ID']." AND users_LOGIN='".$_SESSION['s_login']."'");
        $new_timestamp = $res[0]['from_timestamp'];
        if ($new_timestamp > 0) {
            $periods       = eF_getTableData("periods", "id, name, from_timestamp, to_timestamp", "lessons_ID=".$_SESSION['s_lessons_ID'], "from_timestamp ASC");
            if (sizeof ($periods) > 0) {
                $old_timestamp = $periods[0]['from_timestamp'];
                $temp          = $new_timestamp - $old_timestamp;
                if ($new_timestamp > $old_timestamp) {
                    $offset = $new_timestamp - $old_timestamp;
                }
            }
        }
    }
    if (!isset($offset)) {
        $offset = 0;
    }
    return $offset;
}

/**
* Checks if a unit is current and active
*
* This function will check if a unit is both active and current. This is how it works: If a lesson does not have any periods defined, then 
* all units are considered active and current, so this function returns true for every unit. If a lesson has periods defined, then the funtion 
* returns true if the specified unit is part of at least one of the *current* periods; otherwise, it returns false, meaning that this unit does 
* not belong to any current period (even if it belongs to a period that is not current)
* Note: as of eFront 3.0 this function is deprecated and you should use eF_checkCurrentContentInTree() instead
*
* @param int $id The unit id
* @return bool true if the unit is currently active 
* @version 1.0
* @see eF_checkCurrentContentInTree()
* @deprecated
*/
function eF_isDoneContent($id)
{

    $time   = time();
    $offset = eF_getOffset();
    $res    = eF_getTableData("current_content,periods,content", "content_ID", "content_ID=$id AND current_content.periods_ID=periods.id AND periods.from_timestamp+$offset<=$time AND content.id=current_content.content_ID AND content.active=1");
    if (isset($res[0]['content_ID'])) {
        return true;
    } else { 
        $res1 = eF_getTableData("periods,content", "count(*)", "periods.lessons_ID=content.lessons_ID AND content.id=$id"); 
        if ($res1[0]['count(*)'] == 0) {
            return true;
        } else {
            return false;
        }
    }
}





/**
* Check if a unit is linked form another lesson
*
* When we copy units from one lesson to another, then a special strin is appended to the data, '<:link:>' plus the content_ID.
* This function checks the data to see if it is linked to another unit.
*
* @param string $data the unit content
* @return bool True if the unit is linked to another.
* @version 1.0
* @deprecated 
*/
function eF_isLinked($data)
{
    if(mb_substr($data, 0, 8) == "<:link:>") {
        return true;
    } else {
        return false;
    }
}


/**
* Converts a timestamp interval to time interval
*
* This function is used to convert the interval specified into a human - readable format.
* <br/> Example:
* <code>
* $timestamp_from = mktime(10, 34, 27, 10, 7, 2005);
* $timestamp_to = mktime(11, 47, 4, 10, 7, 2005);
* $interval = $timestamp_to - $timestamp_from;
* print_r(eF_convertIntervalToTimeFull($interval));
* </code>
* Returns:
* <code>
*Array
*(
*    [weeks] => 0
*    [days] => 0
*    [hours] => 1
*    [minutes] => 12
*    [seconds] => 37
*)
* </code>
* @deprecated 
*/
function eF_convertIntervalToTimeFull($interval)
{
    $seconds = $interval % 60;
    $minutes = (($interval - $seconds) / 60) % 60;
    $hours   = ($interval - $seconds - ($minutes * 60)) / 3600 % 24;
    $days    = floor(($interval - $seconds - ($minutes * 60) - ($hours*24))/ 86400 % 7);
    $weeks   = floor(($interval - $seconds - ($minutes * 60) - 7*($hours*24)) / 604800);
    //return "$interval $string:$weeks:$days:$hours:$minutes:$seconds";
    $string = null;
    if($weeks == 1)         $string .= ' '.$weeks.' '._WEEKs.',';
    elseif($weeks > 1)      $string .= ' '.$weeks.' '._WEEKS.',';
    if($days == 1)          $string .= ' '.$days.' '._DAY.',';
    elseif($days > 1)       $string .= ' '.$days.' '._DAYS.',';
    if($hours == 1)         $string .= ' '.$hours.' '._HOUR.',';
    elseif($hours > 1)      $string .= ' '.$hours.' '._HOURS.',';
    if($minutes == 1)       $string .= ' '.$minutes.' '._MINUTE.',';
    elseif($minutes > 1)    $string .= ' '.$minutes.' '._MINUTES.',';
    if($seconds > 0)        $string .= ' '.$seconds.' '._SECONDS;

    return array('string' => $string,'weeks' => $weeks, 'days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds);
}


/**
* 18_1_2007 add case for periods...makriria.. now, in lessons_list op, it appears only current content numbers
* @deprecated 
*/
function getLessonContentUnits($lessons_ID = false, $nonempty = false) {
    if (!$lessons_ID) {
        $lessons_ID = $_SESSION['s_lessons_ID'];
    }
    ($nonempty) ? $nonempty = " AND content.data != '' " : $nonempty = "";

    $time = time();
    $offset = eF_getOffset();
    $result = eF_getTableData("periods", "id", "lessons_ID=".$lessons_ID);
    if (sizeof($result) > 0) {
        $units = eF_getTableData("current_content,periods,content", "count(*)", "ctg_type != 'tests' AND periods.lessons_ID = ".$lessons_ID." AND current_content.periods_ID=periods.id AND periods.from_timestamp+$offset<=$time AND periods.to_timestamp+$offset>$time AND content.id=current_content.content_ID AND content.active=1".$nonempty);

        $tests = eF_getTableData("current_content,periods,content", "count(*)", "ctg_type = 'tests' AND periods.lessons_ID = ".$lessons_ID." AND current_content.periods_ID=periods.id AND periods.from_timestamp+$offset<=$time AND periods.to_timestamp+$offset>$time AND content.id=current_content.content_ID AND content.active=1");

    } else {
        $units = eF_getTableData("content", "count(*)", "ctg_type != 'tests' and lessons_ID = $lessons_ID and active = 1".$nonempty);
        $tests = eF_getTableData("content", "count(*)", "ctg_type = 'tests' and lessons_ID = $lessons_ID and active = 1");
    }


    return array('units' => $units[0]['count(*)'], 'tests' => $tests[0]['count(*)']);
}



/**
* Get number of unread messages
*
* This function returns the number of a user's unread messages.
* If $login is not set, the current user is assumed.
*
* @param string $login The user to check messages for
* @return int The number of unread messages
* @version 1.0 (9/12/2005)
* @deprecated 
*/
function eF_getUnreadMessagesNumber($login = false) {
    if (!$login) {
        $login = $_SESSION['s_login'];
    }

    $messages = eF_getTableData("f_personal_messages pm, f_folders", "count(*)", "pm.users_LOGIN='".$login."' and viewed='no' and f_folders_ID=f_folders.id and f_folders.name='Incoming'");

    return $messages[0]['count(*)'];
}




/**
 * 
 * @deprecated 
 * @param unknown_type $password
 * @return unknown
 */
function eF_passwdCheck($password){
    if( mb_strlen($password) < 6){
        return false;
    }
    return true;
}


/**
* Get student Progress
*
* This function can be used to get the progress information for every student lesson
* Example:<br>
* <code>
* $progress = eF_getStudentProgress('jdoe');        //$progress now holds an array of the form 'lessons_ID' => 'progress' for every student lesson
* $progress = eF_getStudentProgress('jdoe', 13);    //$progress now holds a 1 element array of the form 'lessons_ID' => 'progress', for the lesson with id 13
* $progress = eF_getStudentProgress('jdoe', array(13,15,17));    //$progress now holds a 3-elements array of the form 'lessons_ID' => 'progress', for the lessons specified
* </code>
*
* @param string $login The student login
* @param string $lessons The array of lessons id (may be a single value for just 1 lesson)
* @return array The progress array
* @version 1.0
* @deprecated 
*/
function eF_getStudentProgress($login, $lessons = false) {
    if (!$lessons) {                                                                                    //If $lessons is not specified, all lessons are assumed
        $lessons = eF_getTableDataFlat("users_to_lessons", "lessons_ID", "users_LOGIN='".$login."'");   //Get all user lessons
    } elseif (!is_array($lessons)) {                                                                    //If $lessons is a single value, convert it to 1-element array
        $lessons = array('lessons_ID' => array($lessons));
    }

    $progress = array();                                                                                //Initialize variable, so it never returns undefined value
    foreach ($lessons['lessons_ID'] as $lessons_ID) {                                                   //Get the progress for each lesson id
        $content      = getLessonContentUnits($lessons_ID, true);                                             //Get the content units
        $seen_content = eF_getSeenContent($login, $lessons_ID);                                         //Get the seen content
        array_sum($content) > 0 ? $progress[$lessons_ID] = round(100 - 100 * (array_sum($content) - sizeof($seen_content)) / array_sum($content)) : $progress[$lessons_ID] = '0';   //Calculate the progress percentage
    }

    return $progress;
}


/**
* Get basic type of user  ...which is enum(student,professor,administrator)
*
* This function returns the basic user type  for the given lesson
*
* @param login $login, defaults to corresponding session variable
* @param int $lesson_id the current lesson id, defaults to corresponding session variable
* @return basic user_type of login
* @version 2.6
* @from now on, basic user_type is lesson specific.if value in users_to_lesson is NULL (because of an import), we take default basic user type from table users
* @deprecated 
*/

function eF_getUserBasicType($login = false, $lessons_ID = false){

    if($login == false){
        $login = $_SESSION['s_login'];
    }
    if($lessons_ID == false){
        if (isset($_SESSION['s_lessons_ID']))
            $lessons_ID = $_SESSION['s_lessons_ID'];
    }
    $user = EfrontUserFactory :: factory($login);
    try{
        $lesson = new EfrontLesson($lessons_ID);
        $role = $user -> getRole($lesson -> lesson['id']);
    }
    catch (Exception $e){
        $role = $user -> user['user_type'];
    }

    if ($role != "student" && $role != "professor" && $role != "administrator" ){
        $res2 = eF_getTableData("user_types","basic_user_type","user_type='".$role."'");
        $user_type = $res2[0]['basic_user_type'];
    }else{
        $user_type = $role;
    }

    return $user_type;
}

/**
* prints header - why this is not a smarty template?
*
* @version 1.0
* @DEPRECATED!!!
*/

function eF_printHeader($editor = false)
{
    global $path, $COLOR, $ctg;

    //$marginstr = isset($_POST['standalone'])? "" : " style = 'margin-left: -5px;'"; 
    //$marginstr = "style = 'margin-left: -5px;'";
    $marginstr = "style = 'margin-left:0px; margin-right:2px; margin-top:4px; margin-bottom:0px'";
    print ' 
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 transitional//EN">
      <HTML><HEAD>
      <meta http-equiv="Content-Language" content="'._HEADERLANGUAGETAG.'" />
      <TITLE>eFront - '._THENEWFORMOFADDITIVELEARNING.'</TITLE>
      <link rel="shortcut icon" href="images/favicon.ico" >
      <link rel="icon" href="images/favicon.gif" type="image/gif" >
      <META http-equiv="keywords" content="education" />
      <META http-equiv="description" content="Collaborative Elearning Platform" />
      <META http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <META http-equiv="Cache-Control" content="no-cache" />
      <META http-equiv="Pragma" content="no-cache" />
      <META http-equiv="Expires" content="0" />
    <LINK rel="stylesheet" type="text/css" href="css/css_global.css" />   
    <LINK rel="stylesheet" type="text/css" href="css/css_global_temp.css" />   
    ';
    $css = eF_getTableData("configuration", "value", "name='css'");
    $css = $css[0]['value'];
    if ($css && eF_checkParameter($css, 'filename') && is_file(G_ROOTPATH.'www/css/custom_css/'.$css)) {
        print '<LINK rel="stylesheet" type="text/css" href="css/custom_css/'.$css.'" />';
    } else {
        print '<LINK rel="stylesheet" type="text/css" href="css/custom_css/normal.css" />';
    }    

    print '<LINK rel="stylesheet" type="text/css" href="slashfiles/menu.css" />
    <LINK rel="stylesheet" type="text/css" href="css/drag-drop-folder-tree.css" />
    <LINK rel="stylesheet" type="text/css" href="css/context-menu.css" />
    <LINK rel="stylesheet" type="text/css" href="css/tabber.css" />';
    
    if($editor == true) {
     
        print '
        <script language="javascript" type="text/javascript" src="editor/tiny_mce/tiny_mce.js"></script>
        <script language="javascript" type="text/javascript">
    
        tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "simpleEditor",
        theme : "simple",
        ';
    
        switch ($_SESSION['s_language']) {
            case "greek":
                print 'language : \'el\',';
                break;
            case "english":
                print 'language : \'en\',';
                break;
        }
    
        print '
        entity_encoding : "raw",
        force_p_newlines : false,
        force_br_newlines : true,
        convert_newlines_to_brs : true
            });

        tinyMCE.init({
        //mode : "exact",
        mode : "specific_textareas",
        ';
    
    
        switch ($_SESSION['s_language']) {
            case "greek":
                print 'language : \'el\',';
                break;
            case "english":
                print 'language : \'en\',';
                break;
        }
    
        print ' 
        editor_selector : "mceEditor",
        theme : "advanced",
        //height : "550",
        theme_advanced_resizing : true,
        theme_advanced_resizing_use_cookie : false,
        entity_encoding : "raw",
        force_p_newlines : false,
        force_br_newlines : true,
        convert_newlines_to_brs : false,
        apply_source_formatting : true, 
        plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,java,searchreplace,print,contextmenu,media,mathtype,paste,fullscreen",
        theme_advanced_buttons1_add_before : "save,separator",
        theme_advanced_buttons1_add : "fontselect,fontsizeselect",
        theme_advanced_buttons2_add : "separator,insertdate,inserttime,zoom,separator,forecolor,backcolor",
        theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
        theme_advanced_buttons3_add_before : "tablecontrols,separator",
        theme_advanced_buttons3_add : "emotions,iespell,advhr,separator,print,java,media,separator,mathtype,pastetext,pasteword,selectall,preview,fullscreen",
        plugin_preview_width : "700",
        plugin_preview_height : "700",
        paste_create_paragraphs : false,
        paste_create_linebreaks : false,
        paste_use_dialog : true,
        paste_auto_cleanup_on_paste : true,
        paste_convert_middot_lists : false,
        paste_unindented_list_class : "unindentedList",
        paste_convert_headers_to_strong : true,
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_path_location : "bottom",
        plugin_insertdate_dateFormat : "%Y-%m-%d",
        plugin_insertdate_timeFormat : "%H:%M:%S",
        extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],applet[code|codebase|width|height],embed[src|autostart|type]",';
        
        //file_browser_callback : "myCustomFileBrowser",
        print '
        external_link_list_url : "example_data/example_link_list.js",
        external_image_list_url : "myexternallist.js",
        flash_external_list_url : "example_data/example_flash_list.js"
        
        });
        

    function myCustomFileBrowser(field_name, url, type, win) {

        var fileBrowserWindow = new Array();

        fileBrowserWindow["file"] = "editor/popups/insert_image.php" + "?type=" + type + "&lessons_ID='.$_SESSION['s_lessons_ID'].'"; 
        fileBrowserWindow["title"] = "File Browser";
        fileBrowserWindow["width"] = "800";
        fileBrowserWindow["height"] = "600";
        fileBrowserWindow["close_previous"] = "no";
    
        tinyMCE.openWindow(fileBrowserWindow, {
          window : win,
          input : field_name,
          resizable : "yes",
          inline : "yes"
        });


        win.tinyMCE.setWindowArg(\'editor_id\',\'mce_editor_0\');
        

    }       
        
    
        tinyMCE.init({
        mode : "specific_textareas",
        ';
    
    
        switch ($_SESSION['s_language']) {
            case "greek":
                print 'language : \'el\',';
                break;
            case "english":
                print 'language : \'en\',';
                break;
        }
    
        print ' 
        editor_selector : "intermediateEditor",
        theme : "advanced",
        theme_advanced_resizing : true,
        theme_advanced_resizing_use_cookie : false,
        entity_encoding : "raw",
        force_p_newlines : false,
        force_br_newlines : true,
        convert_newlines_to_brs : false,
        apply_source_formatting : true, 
        plugins : "table,advhr,advimage,advlink,emotions,iespell,java,contextmenu,media,mathtype",
        theme_advanced_buttons1_add : "fontselect,fontsizeselect,removeformat",
        theme_advanced_buttons2_add_before : "forecolor,backcolor,separator,table",
        theme_advanced_buttons2_add : "emotions,iespell,separator,advimage,java,media,separator,mathtype,sub,sup",
        theme_advanced_buttons3 : "",
        theme_advanced_disable : "formatselect,styleselect,strikethrough,cut,copy,paste,indent,outdent,help,cleanup,hr,anchor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_path_location : "bottom",
        extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],applet[code|codebase|width|height],embed[src|autostart|type]"
        });
        </script>';
    }    
    print '<script type="text/javascript" src="js/mathml.js"> </script>
        <script type="text/javascript" src="js/ASCIIMathML.js"></script>
    <script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/context-menu.js"></script>
    <script type="text/javascript" src="js/drag-drop-folder-tree.js"></script>
    <script type="text/javascript" src="js/statists_tabber.js"></script>
    <script type="text/javascript" src="js/calendar3.js"></script>
    <script type="text/javascript">
    //--------------------------------
    // Save functions
    //--------------------------------
    var ajaxObjects = new Array();
    
    // Use something like this if you want to save data by Ajax.
    function saveMyTree()
    {
            saveString = treeObj.getNodeOrders();
            var ajaxIndex = ajaxObjects.length;
            ajaxObjects[ajaxIndex] = new sack();
            var url = "saveNodes.php?saveString=" + saveString;
            ajaxObjects[ajaxIndex].requestFile = url;   // Specifying which file to get
            ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); };// Specify function that will be executed after file has been found
            ajaxObjects[ajaxIndex].runAJAX();       // Execute AJAX function            
        
    }
    function saveComplete(index)
    {
        alert(ajaxObjects[index].response);         
    }

    
    // Call this function if you want to save it by a form.
    function saveMyTree_byForm()
    {
        document.myForm.elements["saveString"].value = treeObj.getNodeOrders();
        document.myForm.submit();       
    }
    

        </script>';
//echo $ctg." ".$COLOR[$ctg];
    //include_once $path."css.php";

    if (!$ctg) {
        $ctg = "lessons";
    }
    
    if (!$COLOR[$ctg] OR $COLOR[$ctg] == "") {
        //$DEF_COLOR   = eF_getDefaultColors();
        $COLOR[$ctg] = $DEF_COLOR["lessons"];
    }
    $COLOR['messages'] = $COLOR['forum'];  
    print '<style type="text/css">
            TH {
                BACKGROUND-COLOR: '.$COLOR[$ctg].';
            }

            .top_title {
                BACKGROUND-COLOR: '.$COLOR[$ctg].';
            }
            </style>';           


print'        </HEAD>
            <BODY id = "body_'.$ctg.'" onload = "jeF_initialize()" '.$marginstr.'>
        <script type="text/javascript" src="js/PieNG.js"></script>
        <script type="text/javascript" src="js/tabber.js"></script>
        <script type="text/javascript" LANGUAGE="JavaScript">


            if(window.location==top.location)
            {
            //if(document.body.style && document.body.style.marginLeft)
                    //  document.body.style.marginLeft = "10px";
            }
        </script>
        <script type="text/javascript">
            top.document.title = "eFront - '._THENEWFORMOFADDITIVELEARNING.'";
        </script>
        <SCRIPT type="text/javascript" LANGUAGE="JavaScript" src="js/print-script.js"></SCRIPT>
            ';
        
 
        //echo $ctg." ".$COLOR[$ctg];
        
    print " <SCRIPT LANGUAGE=\"JavaScript\">
      
            function jeF_initialize() 
            {
                if (el = document.getElementById('main_table'))
                    el.style.display = '';
                if (el = document.getElementById('loading_table')) 
                    el.style.display = 'none';
                if (window._editor_url) initEditor();
                
                setCorrectIframeSize();

                if(changeImages)
                        changeImages();
            }
            
            function popUp(URL, width, height, resize) 
            {
                var left = (screen.width - width) / 2
                var top  = (screen.height-height) / 2
                var resizeable = 0;
                if (resize == 1) {
                    resizeable = 1;
                }
                popup = window.open(URL, '', 'toolbar = 0, scrollbars = 1, location = 0, statusbar = 1, menubar = 0, resizable = '+resizeable+', width = '+width+', height = '+height+', left = '+left+', top = '+top);
                return popup;
            }           

            //Αυτό το κομμάτι javascript φέρνει το iframe του scorm στο σωστό μέγεθος, ώστε να πιάνει όλη την εικόνα
            function setCorrectIframeSize()
            {
                if (frame = window.document.getElementById('scormFrameID'))
                {
                    innerDoc    = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;
                    objToResize = (frame.style) ? frame.style : frame;
                    if (frame.document) {
                        //alert(frame.contentWindow.document.body.clientHeight);
                        objToResize.height = Math.max(innerDoc.body.scrollHeight, frame.document.body.scrollHeight) + 50;
                        //alert(frame.contentWindow.document.body.clientHeight);
                        
                    } else {
                        objToResize.height = innerDoc.body.scrollHeight + 50;
                    }
                }
            }

            </SCRIPT>";
}



/**
* Prints a warning or error message
*
* This function prints a message in a yellow box with an exclamation mark. It is used when
* an important message must be displayed, such as a confirmation or a warning. If the $print
* variable is set, then the message is printed, otherwise it is returned in a string
*
* @param string $str The message to be printed
* @param bool $print If the message will be directly displayed, or returned in a string
* @return string The string with the message
* @version 1.0
*/
function eF_printMessage($str, $print = true, $message_type = '')
{
    if ($str) {
        if ($message_type == 'success') {
            $message = '
                <table border = "1" width = "100%" align = "center" bgcolor = "gray" rules = "none" style = "border-color:black">
                    <tr><td class = "message_success">
                            <img src = "images/32x32/check2.png" title="'.$str.'" alt="'.$str.'">
                        </td><td width = "99%" class = "message_success" align = "center">
                            '.$str.'
                    </td></tr>
                </table><br/>';            
        } else {
            $message = '
                <table border = "1" width = "100%" align = "center" bgcolor = "gray" rules = "none" style = "border-color:black">
                    <tr><td class="message">
                            <img src = "images/32x32/warning.png" title="'.$str.'" alt="'.$str.'">
                        </td><td width = "99%" class="message" align="center">
                            '.$str.'
                    </td></tr>
                </table><br/>';
        }
        
        if ($print) {
            print $message;
        } else {
            return $message;
        }
    }
}


function eF_checkContentInTree($tree)
{
    $time   = time();
    $offset = eF_getOffset();       //Get the user offset, if such exists
//pr($tree);    
    if (sizeof($tree) == 0) {
        $ids[] = 0;
    } else {
        foreach ($tree as $branch) {
            if ($branch['id']) {
                $ids[] = $branch['id'];     //Make a list of ids, that will be used in the query
            }
        }
    }
    
//pr($ids);
    $result_done    = eF_getTableDataFlat("current_content,periods,content", "current_content.content_ID", "current_content.content_ID in (".implode(",",$ids).") AND current_content.periods_ID=periods.id AND periods.from_timestamp+$offset<=$time AND content.id=current_content.content_ID AND content.active=1");
    $result_current = eF_getTableDataFlat("current_content,periods,content", "current_content.content_ID", "current_content.content_ID in (".implode(",",$ids).") AND current_content.periods_ID=periods.id AND periods.from_timestamp+$offset<=$time AND periods.to_timestamp+$offset>$time AND content.id=current_content.content_ID AND content.active=1");

    $done_ids       = array_combine($result_done['content_ID'], $result_done['content_ID']);                //Copy the array values to keys
    $current_ids    = array_combine($result_current['content_ID'], $result_current['content_ID']);          //Copy the array values to keys

    if (sizeof($tree) > 0) {
        $lesson_periods = eF_getTableData("periods,content", "count(*)", "periods.lessons_ID=content.lessons_ID AND content.id=".$tree[0]['id']);       //check if this lesson has periods
    }
    $lesson_periods[0]['count(*)'] == 0 ? $lesson_periods = false : $lesson_periods = true;           //Make $lesson_periods a boolean value, based on whether this lesson has periods or not
    
    for ($i = 0; $i < sizeof($tree); $i++) {
        if (isset($done_ids[$tree[$i]['id']])) {                    //If the unit is in a current or past period, set the isDoneContent field in the $tree array to true
            $tree[$i]['isDoneContent'] = true;
        } else {
            if ($lesson_periods) {
                $tree[$i]['isDoneContent'] = false;                 //If the lesson has periods and the unit is not part of a current or past one, set isDoneContent to false
            } else {
                $tree[$i]['isDoneContent'] = true;                  //If the lesson does not have any periods, set isDoneContent to true
            }
        }

        if (isset($current_ids[$tree[$i]['id']])) {                 //If the unit is in a current period, set the isCurrentContent field in the $tree array to true
            $tree[$i]['isCurrentContent'] = true;
        } else {
            if ($lesson_periods) {
                $tree[$i]['isCurrentContent'] = false;              //If the lesson has periods and the unit is not part of a current one of them, set isCurrentContent to false
            } else {
                $tree[$i]['isCurrentContent'] = true;               //If the lesson does not have any periods, set isCurrentContent to true
            }
        }
    }
//pr($tree);
    return $tree;
}

/**
* Get the leaf nodes of the tree
*
* Ths function is used to traverse through the primitive content tree array,
* to find all units that do not have children (leaf units). It also removes
* these leaves from the array.
*
* @param array $units The array of units
* @return array The tree leaves
*/
function eF_findLeaves(&$units) {
    
    for ($i = 0; $i < sizeof($units); $i++) {
        $ok = true;
        for ($j = 0; $j < sizeof($units); $j++) {
            if ($units[$j]['parent_content_ID'] == $units[$i]['id']) {
                $ok = false;
            }
        }
        if ($ok) {
            $leaves[]      = $units[$i];
            $unset_array[] = $i;
        }
    }
    
    for ($i = 0; $i < sizeof($unset_array); $i++) {
        unset($units[$unset_array[$i]]);
    }
    
    $units = array_values($units);
    return $leaves;
}

/**
 * @deprecated 
 *
 * @param unknown_type $login
 * @return unknown
 */
function ef_getProfessorStudents($login){
    $sql = "select distinct(ul.users_LOGIN) from users_to_lessons ul, users u where ul.users_LOGIN = u.login and u.user_type = 'student' and exists (select l.lessons_ID from users_to_lessons l where ul.lessons_ID=l.lessons_ID and l.users_LOGIN='$login') order by ul.users_LOGIN";
    $res = ef_execute($sql);   
    $users = array();
    while ($k = mysql_fetch_row($res)){
        $user = array();
        $user['login']= $k[0];
        $users[] = $user;
    }    
    return $users;
}


/**
* Returns the extension of the designated filename
*
* This simple function is used to find the extension of a file. Its input is the file name
* and it returns a string containing the extension.
* <br/>Example:
* <code>
* $ext = eF_getFileExtension("test.txt");
* //$ext now contains "txt"
* </code>
* If the file doesn't have any extension, the string returned is empty
*
* @param string $filename The file name
* @return string The file extension
* @version 1.0
* @todo filesystem
*/
function eF_getFileExtension($filename) 
{
    return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
* Returns the contents of a directory
*
* This function accepts a directory name and returns an array where the elements are 
* the full paths to every file in it, recursively. If the second parameter is specified, 
* then only files of the specified type are returned. If no argument is specified, it searches 
* the current directory and returns every file in it.
* <br/>Example:
* <code>
* $file_list = eF_getDirContents();                 //return current directory contents
* $file_list = eF_getDirContents('/tmp');           //return /tmp directory contents
* $file_list = eF_getDirContents(false, 'php');     //return files with extension php in the current directory and subdirectories
* $file_list = eF_getDirContents(false, array('php', 'html'));     //return files with extension php or html in the current directory and subdirectories
* </code>
*
* @param string $dir The directory to recurse into
* @param mixed $ext Return only files with extension $ext, or in array $ext
* @param bool $get_dir If false, do not append directory information to files
* @param bool $recurse Whether to recurse into subdirectories
* @return array An array with every file and directory inside the directory specified
* @version 1.8
* Changes from version 1.7 to 1.8 (2007/08/10 - peris):
* - Exclude .svn folder from return list
* Changes from version 1.6 to 1.7 (2007/07/30 - peris):
* - Now, it returns directory names along with file names
* Changes from version 1.5 to 1.6 (2007/07/26 - peris):
* - Added $recurse parameter
* Changes from version 1.3 to 1.4 (2007/03/26 - peris):
* - Added $get_dir parameter
* Changes from version 1.2 to 1.3 (2007/03/25 - peris):
* - Changed data type of $ext from string to mixed. Now, $ext can be an array of possible extensions. Also, minor bug fix, in $ext and directories handling
* Changes from version 1.1 to 1.2 (2007/03/05 - peris):
* - Fixed recursion bug (Added $ext parameter to recurse call)
* Changes from version 1.0 to 1.1 (22/12/2005):
* - Added $ext parameter
*/
function eF_getDirContents($dir = false, $ext = false, $get_dir = true, $recurse = true)
{
    if ($dir) {
        $handle = opendir($dir);
    } else {
        $handle = opendir(getcwd());
    }
    
    $filelist = array();
    while (false !== ($file = readdir($handle))) {
        if ($file != "." AND $file != ".." AND $file != '.svn') {
            if (is_dir($dir.$file) && $recurse) {//echo "!$dir . $file@<br>";
                $temp = eF_getDirContents($dir.$file.'/', $ext, $get_dir);
                $get_dir ? $filelist[] = $dir.$file.'/' : $filelist[] = $file.'/';
                if (!$ext) {                      //It is put here for empty directories (when $ext is not specified), or, if $ext is specified, to not return directories
                    $filelist = array_merge($filelist, $temp);
                }
            } else {
                if ($ext) {
                    if (is_array($ext)) {
                        if (in_array(pathinfo($file, PATHINFO_EXTENSION), $ext)) {
                            $get_dir ? $filelist[] = $dir.$file : $filelist[] = $file;
                        }
                    } else {
                        if (pathinfo($file, PATHINFO_EXTENSION) == $ext) {
                            $get_dir ? $filelist[] = $dir.$file : $filelist[] = $file;
                        }
                    }
                } else {
                    $get_dir ? $filelist[] = $dir.$file : $filelist[] = $file;
                }
            }
        }
    }   
    return $filelist;
}

/**
* Recursively delete directory contents
*
* This function deletes all the contents of the designated folder, including subfolders, and the folder itself
* <br/>Example:
* <code>
* eF_deleteFolder('/tmp/useless_dir/');
* </code>
*
* @param string $folder The full pathname of the directory to be deleted
* @return bool true if everythin is ok.
* @see eF_getDirContents
* @todo return other than true
*/
function eF_deleteFolder($folder)
{
    $folder = $folder.'/';
    $filelist = eF_getDirContents($folder);
    for ($i = 0; $i < sizeof($filelist); $i++) {
        unlink($filelist[$i]);
    }
    
    $folders[] = $folder;
    
    for ($k = 0; $k < sizeof($folders); $k++) {
        $handle = opendir($folders[$k]);
        while (false !== ($file = readdir($handle))) {
            if ($file != "." AND $file != "..") {
                $folders[] = $folders[$k].'/'.$file;
            }
        }
        closedir($handle);
    }
    
    for ($k = sizeof($folders) - 1; $k >= 0; $k--) {
        rmdir($folders[$k]);
    }
    
    if (is_dir($folder)) {
        return false;
    } else {
        return true;
    }
}





/**
* Handles any file uploading
*
* This function is used to simplify handling and error reporting when we are uploading files.
* <br/>Example:
* <code>
* $timestamp = time();
* list($ok, $upload_messages, $upload_messages_type, $filename) = eF_handleUploads("file_upload", "uploads/", $timestamp."_");  //This will upload all the files specified in the "file_upload" form field, move them to the "uploads" directory and append to their name the current timestamp. 
* //$uploaded_messages is an array with the error or succes message corresponding to each of the uploaded files 
* //$upload_messages_type is an array holding the correspnding message types
* //$filename is an array holding the uploaded files filenames 
* </code>
*
* @param string $field_name The upload file form field name
* @param string $target_dir The directory to put uploaded files into
* @param string $prefix A prefix that the uploaded files will be prepended with
* @param string $ext The extension that is only allowed for the files. If it is false, then we allow all the allowed_extensions
* @param string $target_filename The filename that the uploaded file will have (doesn't work if multiple files uploaded)
* @return array The results array.
* @todo handle better single uploads
* @version 0.9
*/
function eF_handleUploads($field_name, $target_dir, $prefix = '', $target_filename = '', $ext=false) {

    $ok = false;
    $upload_messages = array();
    
    if ($target_dir[mb_strlen($target_dir) - 1] != '/') {
        $target_dir = $target_dir.'/';
    }
    
    if ($prefix) {
        $prefix = $prefix.'_prefix_';
    }
    
    if ($target_filename && sizeof($_FILES[$field_name]['name']) > 1) {
        $target_filename = '';
    }
    
    $allowed_extensions    = eF_getTableData("configuration", "value", "name='allowed_extensions'");
    $disallowed_extensions = eF_getTableData("configuration", "value", "name='disallowed_extensions'");
    if (sizeof($allowed_extensions) == 0 || $allowed_extensions[0]['value'] == '') {
        unset ($allowed_extensions);
    }
    if (sizeof($disallowed_extensions) == 0 || $disallowed_extensions[0]['value'] == '') {
        unset ($disallowed_extensions);
    }    
    if ($ext == false){
        unset($ext);
    }
    
    foreach ($_FILES[$field_name]['name'] as $count => $value) {
        $message_type = 'failure';
        
        $file['tmp_name'] = $_FILES[$field_name]['tmp_name'][$count];
        $file['name']     = $_FILES[$field_name]['name'][$count];
        $file['error']    = $_FILES[$field_name]['error'][$count];
        $file['size']     = $_FILES[$field_name]['size'][$count];

        if ($file['error']) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE : 
                    $upload_messages[$count] = _THEFILE." ".($count + 1)." "._MUSTBESMALLERTHAN." ".ini_get('upload_max_filesize')."<br/>";
                    break;
                case UPLOAD_ERR_FORM_SIZE :
                    $upload_messages[$count] = _THEFILE." ".($count + 1)." "._MUSTBESMALLERTHAN." ".sprintf("%.0f", $_POST['MAX_FILE_SIZE']/1024)." "._KILOBYTES."<br/>";
                    break;
                case UPLOAD_ERR_PARTIAL :
                    $upload_messages[$count] = _PROBLEMUPLOADINGFILE." ".($count + 1);
                    break;
                case UPLOAD_ERR_NO_FILE :
                    //$upload_messages[$count] = _PROBLEMUPLOADINGFILE." ".($count + 1);
                    break;
                case UPLOAD_ERR_NO_TMP_DIR :
                    $upload_messages[$count] = _PROBLEMUPLOADINGFILE." ".($count + 1);
                    break;
                default:
                    $upload_messages[$count] = _PROBLEMUPLOADINGFILE." ".($count + 1);
                    break;
            }
        } else {
            $path_parts = pathinfo($file['name']);
            if ($file['size'] == 0) {
                $upload_messages[] = _FILEDOESNOTEXIST;
            } elseif ((isset($disallowed_extensions) && in_array($path_parts['extension'], explode(",", preg_replace("/\s+/", "", $disallowed_extensions[0]['value'])))) || $path_parts['extension'] == 'php') {           //php files NEVER upload!!!
                $upload_messages[$count] = _YOUCANNOTUPLOADFILESWITHTHISEXTENSION.': .'.$path_parts['extension'].' ('.$file['name'].')<br/>';
            } elseif (isset($allowed_extensions) && $path_parts && !in_array($path_parts['extension'], explode(",", preg_replace("/\s+/", "", $allowed_extensions[0]['value'])))) {
                $upload_messages[$count] = _YOUMAYONLYUPLOADFILESWITHEXTENSION.': '.$allowed_extensions[0]['value'].'<br/>';
            } elseif (!eF_checkParameter($file['name'], 'filename')) {
                $upload_messages[$count] = _INVALIDFILENAME;
            } else if ( isset($ext) && $path_parts && !in_array($path_parts['extension'], explode(",", preg_replace("/\s+/", "", $ext)))){
                $upload_messages[$count] = _YOUMAYONLYUPLOADFILESWITHEXTENSION.': '.$ext.'<br/>';
            } else {
                $new_name    = explode('.', $path_parts['basename']);                                                           //These 3 lines translate greek characters to greeklish characters
                if (!$target_filename) {
                    $new_name[0] = $prefix.$new_name[0];
                    //$new_name[0] = $prefix.iconv('UTF-8', 'ISO-8859-7', $new_name[0]);
                } else {
                    $new_name[0] = $prefix.$target_filename;
                }
                $new_name    = implode('.', $new_name);
                if (move_uploaded_file($file['tmp_name'], $target_dir.$new_name)) {
                    $upload_messages[$count]      = _THEFILE." ".($count + 1)." "._HASBEENSEND."<br/>";
                    $upload_messages_type[$count] = 'success';
                    $ok = true;
                } else {
                    $upload_messages[$count]      = _THEFILE." ".($count + 1)." "._COULDNOTBESEND."<br/>";
                    $upload_messages_type[$count] = 'failure';
                    $ok = false;
                }
            }
            $filename[$count] = $target_dir.$new_name;
        }
    }

    if ($ok) {
        return array($ok, $upload_messages, $upload_messages_type, $filename);
    } else {
        return array($ok, $upload_messages, $upload_messages_type, false);
    }
}

?>