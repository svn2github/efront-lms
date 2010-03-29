<?php
/**

 * This page is for copying content and other entities between lessons

 * 

 */
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change' ? $_change_ = 1 : $_change_ = 0;
$smarty -> assign("_change_", $_change_);
if (!$_change_) {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}
$loadScripts[] = 'includes/copy';
try {
    //Get the user's lessons list, so that he can pick a lesson to copy from
    $lessons = $currentUser -> getLessons(true);

    unset($lessons[$currentLesson -> lesson['id']]);
    $direction_lessons = array();
    foreach ($lessons as $lesson){
        $direction = $lesson -> getDirection();
        $direction_lessons[$direction['name']][] = array('id' => $lesson -> lesson['id'], 'name' => $lesson -> lesson['name']);
    }
    $smarty -> assign("T_USER_LESSONS", $direction_lessons);

    if (isset($_GET['from']) && in_array($_GET['from'], array_keys($userLessons))) {
        //We asked to copy the glossary
        if (isset($_GET['entity']) && $_GET['entity'] == 'glossary') {
            try {
             $result = eF_getTableData("glossary", "name, info, type, active", "lessons_ID = ".$_GET['from']);
             foreach ($result as $key => $value) {
                 $result[$key]['lessons_ID'] = $currentLesson -> lesson['id'];
             }
             eF_insertTableDataMultiple("glossary", $result);
             glossary :: clearDuplicates($currentLesson);
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        //We asked to copy the questions
        } else if (isset($_GET['entity']) && $_GET['entity'] == 'questions') {
            try {
             $result = eF_getTableData("questions", "*", "lessons_ID = ".$_GET['from']);
             foreach ($result as $key => $value) {
                 $result[$key]['lessons_ID'] = $currentLesson -> lesson['id'];
                 unset($result[$key]['content_ID']);
                 unset($result[$key]['id']);
             }

             eF_insertTableDataMultiple("questions", $result);
             glossary :: clearDuplicates($currentLesson);
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        //We asked to copy the surveys
        } else if (isset($_GET['entity']) && $_GET['entity'] == 'surveys') {
            try {
             $result = eF_getTableData("surveys", "*", "lessons_ID = ".$_GET['from']);
             foreach ($result as $key => $value) {
                 $result[$key]['lessons_ID'] = $currentLesson -> lesson['id'];
                 unset($result[$key]['id']);
             }
             eF_insertTableDataMultiple("surveys", $result);
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        //We asked to copy content
        } else {
            $currentContent = new EfrontContentTree($currentLesson, true);
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST));
            if (sizeof($currentContent -> tree) == 0) {
                $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, 'dhtmlTargetTree', array('noclick' => true, 'drag' => false, 'tree_root' => true)));
            } else {
                $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, 'dhtmlTargetTree', array('noclick' => true, 'drag' => false, 'expand' => true)));
            }
            $sourceContent = new EfrontContentTree($_GET['from'], true);
            $sourceIterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($sourceContent -> tree), RecursiveIteratorIterator :: SELF_FIRST));
            $smarty -> assign("T_SOURCE_TREE", $sourceContent -> toHTML($sourceIterator, 'dhtmlSourceTree', array('noclick' => true, 'drag' => true, 'expand' => true)));

            $currentIds[] = 0; //0 is a valid parent node
            foreach ($iterator as $key => $value) {
                $currentIds[] = $value['id'];
            }
            foreach ($sourceIterator as $key => $value) {
                $sourceIds[] = $value['id'];
            }

            try {
                if (isset($_GET['node_orders'])) { //Save new order through AJAX call
                    $nodeOrders = explode(",", $_GET['node_orders']);
                    $nodeOrders = array_unique($nodeOrders);
                    $previousContentId = 0;
                    $transferedNodes = array();
                    $transferedNodesCheck = array();
                    if ($_GET['transfered']) {
                        $transferedNodesCheck = unserialize($_GET['transfered']);
                    }

                    $copiedTests = array();
                    $copiedUnits = array();
                    foreach ($nodeOrders as $value) {
                        list($id, $parentContentId) = explode("-", $value);
                        if (!in_array($id, $transferedNodesCheck)) {
                            if (eF_checkParameter($id, 'id') !== false && eF_checkParameter($parentContentId, 'id') !== false && in_array($id, $sourceIds) && in_array($parentContentId, $currentIds)) {
                                //echo "Copying $id to parent $parentContentId with previous $previousContentId\n";
                                try {
                                    $createdUnit = $currentContent -> copyUnit($id, $parentContentId, $previousContentId);
                                    $transferedNodes[] = intval($id);
                                } catch (Exception $e) {
                                    $errorMessages[] = $e -> getMessage().' '.$e -> getCode();
                                }
                            }
                            $previousContentId = $id;
                        }
                    }

                    Question :: clearDuplicates($currentLesson);
                    glossary :: clearDuplicates($currentLesson);

                    if (isset($errorMessages) && $errorMessages) {
                        header("HTTP/1.0 500 ");
                        echo _ERRORSAVINGTREE."\n".implode("\n", $errorMessages);
                    } else {
                        echo serialize($transferedNodes);
                    }
                    exit;
                }
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
                exit;
            }
        }
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
?>
