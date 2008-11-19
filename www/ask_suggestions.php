<?php
/**
* Ask suggestions page
*
* This page implements the Ajax asking page for suggestions. 
*
* @package eFront
* @version 0.1
* @todo 
*/

session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

if (!eF_checkUser($_SESSION['s_login'],  $_SESSION['s_password'])) {                   //Only a valid user may access this page
    exit;
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header("Content-type: text/xml;charset=iso-8859-7");

$ie = isset($_GET['ie']) ? true : false ;

    $search_results_data  = array();
    $search_results_forum = array();
    $search_results_pmsgs = array();
    
    $results = EfrontSearch :: searchFull('');
    //$res     = eF_getTableData("users_to_lessons", "lessons_ID", "users_LOGIN='".$_SESSION['s_login']."'");
    $res     = eF_getTableData("users_to_lessons,lessons", "lessons_ID", "users_LOGIN='".$_SESSION['s_login']."' and lessons.active=1 and lessons.id=users_to_lessons.lessons_ID"); // na min emfanizontai ta deactivated lessons
    for ($i = 0; $i < sizeof($res); $i++) {
        $lessons_have[] = $res[$i]['lessons_ID'];
    }
    
    $have_results = false;
    if ($results) {
        for ($i = 0; $i < sizeof($results); $i++) {
            if ($results[$i]['table_name'] == "comments") {
                $res1     = eF_getTableData("content,comments", "content.name AS name,content.id AS id,content.lessons_ID AS lessons_ID", "comments.content_ID=content.id AND comments.id=".$results[$i]['foreign_ID']);
                $type_str = _COMMENTS;
            } elseif ($results[$i]['table_name'] == "news") {
                $res1     = eF_getTableData($results[$i]['table_name'], "id,title AS name,lessons_ID", "id=".$results[$i]['foreign_ID']);
                $type_str = _ANNOUNCEMENTS;
            } elseif ($results[$i]['table_name'] == "content") {
                $res1     = eF_getTableData($results[$i]['table_name'], "id,name,lessons_ID,ctg_type", "id=".$results[$i]['foreign_ID']);
                $type_str = _LESSONCONTENT;
            } elseif ($results[$i]['table_name'] == "f_messages") {
                $res1     = eF_getTableData("f_messages, f_topics, f_forums", "f_forums.id as category_id, f_forums.lessons_ID, f_messages.id, f_messages.title, f_messages.f_topics_ID, f_topics.title as topic_title", "f_topics_ID = f_topics.id and f_forums.id = f_forums_ID and f_messages.id=".$results[$i]['foreign_ID']);
                $type_str = _MESSAGESATFORUM;
            } elseif ($results[$i]['table_name'] == "f_personal_messages") {
                $res1     = eF_getTableData("f_personal_messages, f_folders", "f_personal_messages.id, f_personal_messages.title, f_personal_messages.users_LOGIN, f_folders.name, f_folders.id as folder_id", "f_personal_messages.f_folders_ID = f_folders.id and f_personal_messages.id=".$results[$i]['foreign_ID']);
                $type_str = _MESSAGESATFORUM;
            }
           elseif ($results[$i]['table_name'] == "lessons") {
                $res1     = eF_getTableData($results[$i]['table_name'], "id as lessons_ID,name", "id=".$results[$i]['foreign_ID']." and active=1"); 
                $type_str = _LESSON;
            }
            elseif ($results[$i]['table_name'] == "f_topics") {
                $res1     =  $res1     = eF_getTableData("f_messages, f_topics, f_forums", "f_forums.id as category_id, f_forums.lessons_ID, f_messages.id, f_messages.title, f_messages.f_topics_ID, f_topics.title as topic_title", "f_topics_ID = f_topics.id and f_forums.id = f_forums_ID and f_topics.id=".$results[$i]['foreign_ID']);
                $type_str = _MESSAGESATFORUM;
            }
//print_r($res1);
       
            if (sizeof($res1) > 0) {
                $results[$i]['position'] == "title" ? $position_str = _TITLE : $position_str = _TEXT;
                if (isset($res1[0]['lessons_ID']) && in_array($res1[0]['lessons_ID'], $lessons_have)) {
                    
                    $lesson = eF_getTableData("lessons", "name", "id=".$res1[0]['lessons_ID']);
                    if ($results[$i]['table_name'] != 'f_messages' && $results[$i]['table_name'] != 'f_topics') {             
                        
                        if($results[$i]['table_name'] == "lessons"){                            
                                $search_results_data[] = array('id'          => $res1[0]['id'],
                                                               'name'        => $res1[0]['name'],
                                                               'table_name'  => $results[$i]['table_name'],
                                                               'lessons_ID'  => $res1[0]['lessons_ID'],
                                                               'lesson_name' => $lesson[0]['name'],
                                                               'score'       => sprintf("%.0f %%", $results[$i]['score'] * 100),
                                                               'type'        => $type_str,
                                                               'position'    => $position_str);
                                                        
                        }elseif ($results[$i]['table_name'] != "lessons" && eF_isDoneContent($res1[0]['id'])) {
 //echo $res1[0]['id']."->".eF_isDoneContent($res1[0]['id']);                       
                                $search_results_data[] = array('id'          => $res1[0]['id'],
                                                               'name'        => $res1[0]['name'],
                                                               'table_name'  => $results[$i]['table_name'],
                                                               'lessons_ID'  => $res1[0]['lessons_ID'],
                                                               'lesson_name' => $lesson[0]['name'],
                                                               'ctg_type'    => $res1[0]['ctg_type'],
                                                               'score'       => sprintf("%.0f %%", $results[$i]['score'] * 100),
                                                               'type'        => $type_str,
                                                               'position'    => $position_str);
                        }                                      
                    } else {
                        $search_results_forum[] = array('category_id'     => $res1[0]['category_id'],
                                                        'lesson_name'     => $lesson[0]['name'],
                                                        'topic_subject'   => $res1[0]['topic_title'],
                                                        'topic_id'        => $res1[0]['f_topics_ID'],
                                                        'message_subject' => $res1[0]['title'],
                                                        'message_id'      => $res1[0]['id'],
                                                        'position'        => $position_str);
                    }
                } elseif ($results[$i]['table_name'] == 'f_personal_messages' && $_SESSION['s_login'] == $res1[0]['users_LOGIN']) {
                    $search_results_pmsgs[] = array('message_subject' => $res1[0]['title'],
                                                    'message_id'      => $res1[0]['id'],
                                                    'folder_name'     => $res1[0]['name'],
                                                    'folder_id'       => $res1[0]['folder_id'],
                                                    'position'        => $position_str);
                }
            }
        }
    }
echo "<?xml version=\"1.0\" ?>";
echo "<root>";
echo "<search_results_data>";
foreach($search_results_data as $key => $value)
{
    echo "<search_result_data>";
    echo "<id>".$value['id']."</id>";
    echo "<name>".$value['name']."</name>";
    echo "<table_name>".$value['table_name']."</table_name>";
    echo "<lessons_ID>".$value['lessons_ID']."</lessons_ID>";
    echo "<lesson_name>".$value['lesson_name']."</lesson_name>";
    echo "<score>".$value['score']."</score>";
    echo "<type>".$value['type']."</type>";
    echo "<position>".$value['position']."</position>";
    echo "</search_result_data>";
}
echo "</search_results_data>";
/*
    for($i=0;$i<sizeof($result);$i++)
    {
        $name = str_replace("&","&amp;",$result[$i]['name']);
        $url = str_replace("&","&amp;",$result[$i]['url']);
        $id = $result[$i]['id'];
        echo "<bookmark>";
        if($ie)
        {
            echo "<name>".$name."</name>";
            echo "<url>".$url."</url>";
            echo "<id>".$id."</id>";
        }
        else
        {
            echo "<name>".iconv("UTF-8","ISO-8859-7",$name)."</name>";
            echo "<url>".iconv("UTF-8","ISO-8859-7",$url)."</url>";
            echo "<id>".$id."</id>";
        }
        echo "</bookmark>";
    }
    echo "</bookmarks>";
}
*/
echo "</root>";
exit;

?>
