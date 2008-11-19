<?php
session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$message        = isset($_GET['message'])      ? $_GET['message']      : $message;
$message_type   = isset($_GET['message_type']) ? $_GET['message_type'] : $message_type;

$loadScripts = array('EfrontScripts', 'scriptaculous/prototype', 'scriptaculous/scriptaculous', 'scriptaculous/effects');
$smarty -> assign("T_CONFIGURATION", $configuration);           //Assign global configuration values to smarty
$smarty -> assign("T_CURRENCYSYMBOLS", $CURRENCYSYMBOLS);  



$loadScripts[] = 'drag-drop-folder-tree';
try {
    if (isset($_GET['lessons_ID'])) {
        $lesson     = new EfrontLesson($_GET['lessons_ID']);
        $smarty -> assign("T_LESSON", $lesson);
        
        $lessonInformation = $lesson -> getInformation();            
        $content    = new EfrontContentTree($lesson);    
        if (sizeof($content -> tree) > 0) {
            $smarty -> assign("T_CONTENT_TREE", $content -> toHTML(false, 'dhtml_content_tree', array('noclick' => 1)));
        }
        $lessonInfo = new LearningObjectInformation(unserialize($lesson -> lesson['info']));
        $smarty -> assign("T_LESSON_INFO", $lessonInfo);
        $additionalInfo = $lesson -> getInformation();
        $smarty -> assign("T_ADDITIONAL_LESSON_INFO", $additionalInfo);           
    } else if ($_GET['courses_ID']) {
        $course     = new EfrontCourse($_GET['courses_ID']);
        $smarty -> assign("T_COURSE", $course);

        $lessons = $course -> getLessons();
        $smarty -> assign("T_COURSE_LESSONS", $lessons);
        
        $courseInfo = new LearningObjectInformation(unserialize($course -> course['info']));
        $smarty -> assign("T_COURSE_INFO", $courseInfo);
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = failure;
}

$directionsTree = new EfrontDirectionsTree();
$options        = array('lessons_link' => basename($_SERVER['PHP_SELF']).'?lessons_ID=',
                        'courses_link' => basename($_SERVER['PHP_SELF']).'?courses_ID=',
                        'search'       => true);
if (isset($_GET['filter'])) {
    $result  = eF_getTableData("lessons", "*");
    foreach ($result as $value) {
        $lessonNames[$value['id']] = array('name' => $value['name']);
        $lessonData[$value['id']] = $value;
    }

    $lessons = eF_filterData($lessonNames, $_GET['filter']);

    foreach ($lessons as $id => $value) {
        $lessons[$id] = new EfrontLesson($lessonData[$id]);
    }

    $result  = eF_getTableData("courses", "*");
    foreach ($result as $value) {
        $courseNames[$value['id']] = array('name' => $value['name']);
        $courseData[$value['id']] = $value;
    }
    $courses = eF_filterData($courseNames, $_GET['filter']);
    foreach ($courses as $id => $value) {
        $courses[$id] = new EfrontCourse($courseData[$id]);
    }
    $options['tree_tools'] = false;
    echo $directionsTree -> toHTML(false, $lessons, $courses, false, $options);
    
    exit;
}
$smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, false, false, false, $options));


//If there is a valid session, try to instantiate current user
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
        $smarty -> assign("T_CURRENT_USER", $currentUser);
        if ($currentUser instanceOf EfrontLessonUser) {
            $userLessons = $currentUser -> getLessons();
            $userCourses = $currentUser -> getCourses();
        } else {
            $userLessons = $userCourses = null;
        }
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
    }
} 

/**
 *
 * MAIN PART
 *
 */

$fct        = eF_checkParameter($_GET['fct'], 'string') && isset($_GET['fct'])          ? $_GET['fct']          : null;
$direction  = eF_checkParameter($_GET['direction'], 'id') && isset($_GET['direction'])  ? $_GET['direction']    : null;
$course     = eF_checkParameter($_GET['course'], 'id') && isset($_GET['course'])        ? $_GET['course']       : null;
$lesson     = eF_checkParameter($_GET['lesson'], 'id') && isset($_GET['lesson'])        ? $_GET['lesson']       : null;
$id         = eF_checkParameter($_GET['id'], 'id') && isset($_GET['id'])                ? $_GET['id']           : null;

switch($_GET['fct']) {
    case 'cartPreview':
        $smarty -> assign("T_SESSION_DATA", sizeof($_SESSION['cart']['lesson']));

        if (sizeof($_SESSION['cart']['lesson']) > 0) {
            $p          = new paypal_class;                             //Load paypal class
            $pconfig    = $p -> configuration();                          //Load paypal configuration

            $finalPrice = $lessonsNames = $lessonsIds = null;           //Initiates some variables

            foreach ($_SESSION['cart']['lesson'] as $key => $value) {   //Calculate paypals variables
                $lessonsNames   .= $value['name'].", ";
                $lessonsIds     .= $value['id'].", ";
                $finalPrice     += $value['price'];
            }

            $lessonsNames   = mb_substr($lessonsNames, 0, -2);
            $lessonsIds     = mb_substr($lessonsIds, 0, -2);
            $config_data = eF_getTableData("paypal_configuration", "*", "");

            
            if ($finalPrice == 0) {
                $form = new HTML_QuickForm("order_lessons_form", "post", 'directory.php?fct=registerLessons', '', null, true);
                $form -> addElement('hidden', 'item_name', $lessonsNames);
                $form -> addElement('hidden', 'item_number', $lessonsIds);
                $form -> addElement('submit', 'order', _FREEREGISTRATION, 'class = "flatButton"');                
            } else {
                if (sizeof($config_data) > 0 && strlen($config_data[0]['paypalbusiness']) > 4 && $pconfig) {        //Paypal is setup
                    $transactionID = date('ymdHms') . substr(md5(G_MD5KEY . $_SESSION['s_login']), 0, 4);
                    $form = new HTML_QuickForm("order_lessons_form", "post", $p -> paypal_url, 'onsubmit="document.getElementById(\'savedata\').submit();"', null, true);
                    $form -> addElement('hidden', 'business', $config_data[0]['paypalbusiness']);
                    $form -> addElement('hidden', 'return', G_SERVERNAME."studentpage.php?message="._PAYPALORDERSUCCESS."&message_type=success");
                    $form -> addElement('hidden', 'cancel_return', G_SERVERNAME."studentpage.php?message="._PAYPALORDERFAILURE."&message_type=failure");
                    $form -> addElement('hidden', 'notify_url', G_SERVERNAME."ipn.php");
                    $form -> addElement('hidden', 'item_name', $lessonsNames);
                    $form -> addElement('hidden', 'rm', '2');       // Return method = POST
                    $form -> addElement('hidden', 'cmd', '_xclick');
                    //$form -> addElement('hidden', 'cmd', '_xclick-subscriptions');
                    $form -> addElement('hidden', 'currency_code', $configuration['currency']);
                    $form -> addElement('hidden', 'item_number', $lessonsIds);
                    $form -> addElement('hidden', 'amount', $finalPrice);                            //not used for subscribe
                    $form -> addElement('hidden', 'custom', $transactionID);
//for subscription instructions, see "Website Payments Standard Integration Guide"                    
                    //$form -> addElement('hidden', 'a3', '');    //subscription only: Regular subscription price
                    //$form -> addElement('hidden', 'p3', '');    //subscription only: Subscription duration. Specify an integer value in the allowable range for the units of duration that you specify with t3
                    //$form -> addElement('hidden', 't3', '');    //subscription only: D(1-90),W(1-52),M(1-24),Y(1-5)
                    //$form -> addElement('hidden', 'src', 1);    //subscription only: (0,1) Recurring payments. Subscription payments recur unless subscribers cancel their subscriptions before the end of the current billing cycle or you limit the number of times that payments recur with the value that you specify for srt.
                    
                    $form -> addElement('hidden', 'charset', 'utf-8');
                    $form -> addElement('submit', 'order', _PAYPALPAYNOW, 'class = "flatButton" onclick="document.getElementById(\'savedata\').submit();"');

                    //form to save data in the database
                    $formdata = new HTML_QuickForm("savedata", "post", "directory.php?fct=saveOrder", "id = 'savedata'", null, true);
                    $formdata -> addElement('hidden', 'business', $config_data[0]['paypalbusiness']);
                    $formdata -> addElement('hidden', 'item_name', $lessonsNames);
                    $formdata -> addElement('hidden', 'amount', $finalPrice);
                    $formdata -> addElement('hidden', 'item_number', $lessonsIds);
                    $formdata -> addElement('hidden', 'status', 'submitted');
                    $formdata -> addElement('hidden', 'currency_code', $configuration['currency']);
                    $formdata -> addElement('hidden', 'custom', $transactionID);

                    $rendererdata =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                    $formdata -> accept($rendererdata);

                    $smarty -> assign('T_ORDER_LESSONS_FORMDATA', $rendererdata -> toArray());
                } else {                                                                                    //Paypal is not setup
                    $form = new HTML_QuickForm("order_lessons_form", "post", 'directory.php?fct=registerLessons', '', null, true);
                    $form -> addElement('hidden', 'item_name', $lessonsNames);
                    $form -> addElement('hidden', 'item_number', $lessonsIds);
                    $form -> addElement('submit', 'order', _REGISTER, 'class = "flatButton"');                    
                }
            }
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
    
            $smarty -> assign('T_ORDER_LESSONS_FORM', $renderer -> toArray());
            $smarty -> assign("T_LESSONS_FINAL_PRICE", $finalPrice);
            $smarty -> assign("T_LESSONS_DATA", $_SESSION['cart']['lesson']);
        }
        break;
    case 'registerLessons':

        $lessons_id = explode(", ", $_POST['item_number']);
        foreach($lessons_id AS $id){
            if ($id < 100000) {
                $lesson = new EfrontLesson($id);
                $lesson -> addUsers($_SESSION['s_login'], 'student', $lesson -> lesson['price'] ? false : true);
            } else {
                $courseId   = $id - 100000;
                $editCourse = new EfrontCourse($courseId);
                $userType   = 'student';
                $courseUsers    = $editCourse-> getUsers();                         //Get all users that have this course
                $nonCourseUsers = $editCourse -> getNonUsers();                     //Get all the users that can, but don't, have this course
                $users          = array_merge($courseUsers, $nonCourseUsers);       //Merge users to a single array, which will be useful for displaying them

                if (in_array($_SESSION['s_login'], array_keys($nonCourseUsers))) {
                    $editCourse -> addUsers($_SESSION['s_login'], $userType, $editCourse -> course['price'] ? false : true);
                }
                if (in_array($_SESSION['s_login'], array_keys($courseUsers))) {
                    $userType != $courseUsers[$_SESSION['s_login']]['user_type'] ? $editCourse -> setRoles($_SESSION['s_login'], $userType) : $editCourse -> removeUsers($_SESSION['s_login']);
                }
            }
        }

        unset($_SESSION['cart']['lesson']);
        $message        = _PAYPALFREEORDERSUCCESS;
        $message_type   = 'success';
        header('location: student.php?ctg=lessons&message='.$message.'&message_type='.$message_type);
        break;
    case 'saveOrder':
        $log = fopen("ipn.log", "a");
        foreach ($_POST as $key => $value){
            $emailtext .= $key . " = " .$value ."\n";
        }
        $fields      = array('business'     => $_POST['business'],
                             'item_name'    => $_POST['item_name'],
                             'mc_gross'     => $_POST['amount'],
                             'item_number'  => $_POST['item_number'],
                             'mc_currency'  => $_POST['currency_code'],
                             'status'       => $_POST['status'],
                             'timestamp'    => time(),
                             'transactionID'=> $_POST['custom'],
                             'user'         => $_SESSION['s_login']);

        $result = eF_insertTableData("paypal_data", $fields);
        fwrite($log, "POST - " . gmstrftime ("%b %d %Y %H:%M:%S", time()) . "\n");
        fwrite($log, "POST DATA \n");
        fwrite($log, $emailtext."\n");
        if ($result) {
            unset($_SESSION['cart']['lesson']);
            $message        = _PAYPALORDERPROCESSING;
            $message_type   = 'success';
            header('location: directory.php?message='.$message.'&message_type='.$message_type);
        } else {
            $message        = _PAYPALORDERPROCESSINGERROR;
            $message_type   = 'failure';
            header('location: directory.php?message='.$message.'&message_type='.$message_type);
        }
        break;
    case 'addLessonToCart':
        if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id')) {
            $found = false;
            foreach ($_SESSION['cart']['lesson'] as $key => $value) {
                if ($value['id'] == $id) {
                    $found = true;
                }
            }
            if (!$found) {
                if ($id > 50000) {
                    $idReal = $id - 100000;
                    $course = new EfrontCourse($idReal);
                    $_SESSION['cart']['lesson'][] = array("id" => $id, "did" => $course -> course['directions_ID'], "price" => $course->course['price'], "name" => $course->course['name']);
                } else {
                    $lesson = new EfrontLesson($id);
                    $_SESSION['cart']['lesson'][] = array("id" => $id, "did" => $lesson -> lesson['directions_ID'], "price" => $lesson->lesson['price'], "name" => $lesson->lesson['name']);
                }
            }
        }

        printCart();
        exit;
        break;
    case 'removeLessonFromCart':
        if ($id) {
            foreach ($_SESSION['cart']['lesson'] as $key => $value) {
                if ($value['id'] == $id) {
                    unset($_SESSION['cart']['lesson'][$key]);
                }
            }
        }
        printCart();
        exit();
        break;
    case 'removeLessonAllFromCart':
        unset($_SESSION['cart']);
        printCart();
        exit();
        break;    
    default:
        $directionsTree = new EfrontDirectionsTree();                                                   //Load Direction Tree
        if (isset($direction)) {                                                                        //Directions in second or more level
            $fullPath           = array_reverse($directionsTree -> getNodeAncestors($direction));       //Find the path to the root direction
            //pr($fullPath);
            if (isset($lesson)) {
                $curLesson          = new EfrontLesson($lesson);
                $lessonContent      = new EFrontContentTree($lesson);
                $lessonContentTree  = $lessonContent -> toHTMLSelectOptions();
                $lessonInfo         = $curLesson -> getInformation();
                if (in_array($lesson, array_keys($userLessons))) {
                    $curLesson->lesson['my'] = '1';                                         //Add flag is student has already this lesson
                } else {
                    $curLesson->lesson['my'] = '0';
                }
                $smarty -> assign("T_CURRENT_LESSON", $curLesson);
                $smarty -> assign("T_CURRENT_LESSON_TREE", $lessonContentTree);
                $smarty -> assign("T_CURRENT_LESSON_INFO", $lessonInfo);
                $smarty -> assign("T_CURRENT_LESSON_INFO_NUM", sizeof($lessonInfo));
            } elseif (isset($course)) {
                $course     = new EfrontCourse($course);
                $courseInfo = $course -> getInformation();
                if (in_array($course -> course['id'], array_keys($userCourses))) {
                    $course -> course['my'] = '1';                                          //Add flag is student has already this course
                } else {
                    $course -> course['my'] = '0';
                }

                $smarty -> assign("T_CURRENT_COURSE", $course);
                $smarty -> assign("T_CURRENT_COURSE_INFO", $courseInfo);
                $smarty -> assign("T_CURRENT_COURSE_INFO_NUM", sizeof($courseInfo));
                //$smarty -> assign("T_CURRENT_COURSE", $course -> getInformation());
                //$smarty -> assign("T_CURRENT_COURSE_INFO_TIP", $course -> toHTMLTooltipLink());
            } else {
                $children           = $directionsTree -> getNodeChildren($direction);                       //Find children Directions and the selected Direction
                $currentDirection   = $children['name'];
                $direction          = new EfrontDirection($direction);                                      //Get the object of the current Direction
                $directionLessons   = $direction -> getLessons(true);                                       //Get Directions Lessons
                $directionCourses   = $direction -> getCourses(true);                                       //Get Directions Courses

                foreach ($directionLessons as $key => $value){
                    if (!$value -> lesson['active'] || $value -> lesson['course_only']) {
                        unset($directionLessons[$key]);
                    } else {
                        if (in_array($key, array_keys($userLessons))) {
                            $directionLessons[$key] -> lesson['my'] = '1';                                          //Add flag is student has already this lesson
                        } else {
                            $directionLessons[$key] -> lesson['my'] = '0';
                        }
                        //$directionLessons[$key] -> lesson['information'] = $directionLessons[$key] -> getInformation();
                        $directionLessons[$key] -> lesson['link'] = $directionLessons[$key] -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF'])."?direction=".$_GET['direction']."&lesson=".$directionLessons[$key] -> lesson['id']);
                        if (isset($currentUser) && $directionLessons[$key] -> lesson['languages_NAME'] != $currentUser -> user['languages_NAME']) {                 //If lesson is in other lang
                            unset($directionLessons[$key]);
                        }
                    }
                }
                //pr($directionLessons);
                //pr($userCourses);
                foreach ($directionCourses as $key => $value){
                    if (!$value->course['active']) {
                        unset($directionCourses[$key]);
                    } else {
                        if (in_array($key, array_keys($userCourses))) {
                            $directionCourses[$key] -> course['my'] = '1';                                          //Add flag is student has already this course
                        } else {
                            $directionCourses[$key] -> course['my'] = '0';
                        }
                        $directionCourses[$key] -> course['link'] = $directionCourses[$key] -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF'])."?direction=".$_GET['direction']."&course=".$directionCourses[$key] -> course['id']);
                        //$directionLessons = $value -> getLessons(true,true);
                        //foreach ($directionLessons as $keys => $values) {if (!$values->lesson['active']) unset($directionLessons[$keys]);}
                        //$directionCourses[$key]['lessons']    = sizeof($directionLessons);        //Find number of lessons in each Direction
                    }
                }
                //pr($directionCourses);
                $iterator           = (new EfrontNodeFilterIterator($children));            //Hold only childrens

                $smarty -> assign("T_COURSE_DATA", $directionCourses);                      //Assign the current directions courses
                $smarty -> assign("T_COURSE_DATA_NUM", sizeof($directionCourses));          //Assign the sizeof current directions courses
                $smarty -> assign("T_DIRECTIONS_LESSONS", $directionLessons);               //Assign the current lessons names
                $smarty -> assign("T_DIRECTIONS_LESSONS_NUM", sizeof($directionLessons));   //Assign the sizeof current lessons names
            }
            $smarty -> assign("T_DIRECTIONS_PATH", $fullPath);                          //Assign the path to the root direction if possible
        } else {
            $iterator   = new EfrontNodeFilterIterator($directionsTree -> tree);    //Find all root Directions
        }

        foreach ($iterator as $key => $value) {
            if ($value['active'] == "1") {                                          //Select only active Directions
                $directions[$key]               = $value;                           //Create array with these Directions
                $direction                      = new EfrontDirection($key);
                $directionLessons               = $direction -> getLessons(true,true);
                foreach ($directionLessons as $keys => $values) {if (!$values->lesson['active']) unset($directionLessons[$keys]);}
                $directions[$key]['lessons']    = sizeof($directionLessons);        //Find number of lessons in each Direction
            }
        }
        $smarty -> assign("T_DIRECTIONS_DATA", $directions);                //Assign the direction list
        $smarty -> assign("T_DIRECTIONS_DATA_NUM", sizeof($directions));    //Assign the sizeof direction list
}

$smarty -> assign("T_CART_LESSONS", $_SESSION['cart']['lesson']);
$smarty -> assign("T_CART_LESSONS_SIZE", sizeof($_SESSION['cart']['lesson']));
$smarty -> assign("T_INDEXPAGE", preg_match("/index.php/i",basename($_SERVER['PHP_SELF'])));

/**
 *
 * END OF MAIN PART
 *
 */
$smarty -> assign("T_LANGUAGES_DEFAULT", $GLOBALS['configuration']['default_language']);
$smarty -> assign("T_LANGUAGES", EfrontSystem :: getLanguages(true));
$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));                     //array_unique, so it doesn't send duplicate entries
$smarty -> assign("T_MESSAGE", $message);                                                   //Any messages generated during script execution
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_CONFIGURATION", $configuration);                                       //Assign global configuration values to smarty
$smarty -> load_filter('output', 'eF_template_formatTimestamp');                            //smartt filters, in case they are needed
$smarty -> load_filter('output', 'eF_template_formatLogins');

if ((isset($_SESSION['s_login']) && !preg_match('/index.php/i', $_SERVER['PHP_SELF'])) || ($configuration['lessons_directory'] == '2' && $configuration['interface_view'] == '1')) {
    $smarty -> display('directory.tpl');                                                        //Display template
}



/**
*
*/
function printCart() {
    if (sizeof($_SESSION['cart']['lesson']) > 0 ) {
        $finalPrice = 0;
        $str        = '';    
        foreach ($_SESSION['cart']['lesson'] as $key => $value) {
            $value['price'] == 0 ? $price = _FREEOFCHARGE : $price = $value['price']." ".$GLOBALS['CURRENCYSYMBOLS'][$GLOBALS['configuration']['currency']];
            
            $str .= '
            	<div class = "cartElement">
            		<div class = "cartTitle">'.$value['name'].'</div>
                    <div class = "cartDelete">
                        <span>'.$price.'</span>
                        <a href = "javascript:void(0)" onclick = "ajaxPostRemove(\''.$value['id'].'\', this);">
                            <img src = "images/16x16/delete.png" alt = "'._REMOVEFROMCART.'" title = "'._REMOVEFROMCART.'"></a>
                    </div>
                </div>';
            $finalPrice += $value['price']; 
        }
        $finalPrice == 0 ? $finalPrice = _FREEOFCHARGE : $finalPrice = $finalPrice." ".$GLOBALS['CURRENCYSYMBOLS'][$GLOBALS['configuration']['currency']];
        
        $str .= '
            <div id = "cart_total">
                <span>'._PAYPALFINALPRICE.': '.$finalPrice.'</span>
				<a href = "javascript:void(0)" onclick = "ajaxPostRemoveAll(\'\', this);">
                	<img src = "images/16x16/delete.png" alt = "'._REMOVEALLFROMCART.'" title = "'._REMOVEALLFROMCART.'"></a>							
            </div>
			<div id = "submit_cart"><input class = "flatButton" type = "submit" value = "'._CONTINUE.'&nbsp;&raquo;" onclick = "location = \''.($_SESSION['s_login'] ? 'directory.php?fct=cartPreview' : 'index.php?ctg=login&register_lessons=1&message='.urlencode(_PLEASELOGINTOCOMPLETEREGISTRATION)).'\'"></div>            ';
    } else {
        $str = '';
    }
    
    echo $str;
}


//Experimental/json
function printCart2() {
    foreach ($_SESSION['cart']['lesson'] as $key => $value) {
        if ($value['price'] == 0) {
            $_SESSION['cart']['lesson'][$key]['value_str'] = _FREEOFCHARGE;
        } else {
            $_SESSION['cart']['lesson'][$key]['value_str'] = $value['price']." ".$GLOBALS['CURRENCYSYMBOLS'][$GLOBALS['configuration']['currency']];
        }        
    }
    echo (json_encode($_SESSION['cart']['lesson']));
}


?>