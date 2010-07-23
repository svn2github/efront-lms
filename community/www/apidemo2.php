<?php
    session_cache_limiter('none'); //Initialize session
    session_start();
    $path = "../libraries/";
    require_once $path."configuration.php";
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    $css = $GLOBALS['configuration']['css'];
    if (strlen($css) > 0 && is_file(G_CUSTOMCSSPATH.$css)){
        $smarty->assign("T_CUSTOM_CSS", $css);
    }
    $loadScripts = array_merge($loadScripts, array('scriptaculous/prototype','scriptaculous/scriptaculous','scriptaculous/effects','scriptaculous/controls'));

    $actions = array();
    $actions[0] = "token";
    $actions[1] = "login";
    $actions[2] = "efrontlogin";
 $actions[3] = "create_lesson";
    $actions[4] = "create_user";
    $actions[5] = "user_info";
    $actions[6] = "user_lessons";
    $actions[7] = "user_courses";
    $actions[8] = "update_user";
    $actions[9] = "activate_user";
    $actions[10] = "deactivate_user";
    $actions[11] = "remove_user";
    $actions[12] = "groups";
    $actions[13] = "group_info";
    $actions[14] = "group_to_user";
    $actions[15] = "group_from_user";
    $actions[16] = "catalog";
    $actions[17] = "lessons";
    $actions[18] = "lesson_info";
    $actions[19] = "lesson_to_user";
    $actions[20] = "lesson_from_user";
    $actions[21] = "courses";
    $actions[22] = "course_info";
    $actions[23] = "course_to_user";
    $actions[24] = "course_from_user";
 $actions[25] = "course_lessons";
 $actions[26] = "curriculum_to_user";
 $actions[27] = "efrontlogout";
    $actions[28] = "logout";

    $smarty -> assign("T_ACTIONS", $actions);

    if (isset($_GET['action'])){
        $action = $actions[$_GET['action']];
        $action_id = $_GET['action'];
    }
    else if (isset($_POST['action'])){
        $action = $actions[$_POST['action']];
        $action_id = $_POST['action'];
    }
    else{
        $action = "token";
        $action_id = 0;
    }
    $smarty -> assign("T_ACTION", $action);

    $postTarget = basename($_SERVER['PHP_SELF']);
    $form = new HTML_QuickForm("action_form", "post", $postTarget, "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter    
    $form -> addElement('select', 'action', _ACTION, $actions, 'class = "inputSelect" id = "action" onchange = "window.location = \''.basename($_SERVER['PHP_SELF']).'?action=\'+this.options[this.selectedIndex].value"'); //Depending on user selection, changing the question type reloads the page with the corresponding form fields
    $form -> addRule('action', _THEFIELD.' '._QUESTIONTYPE.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('action', _INVALIDFIELDDATA, 'callback', 'text');
    $output = "";
    switch ($action){
        case 'token':{
            break;
        }
        case 'login':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('password', 'password', _PASSWORD, 'class = "inputText"');
            break;
        }
        case 'efrontlogin':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
   $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            break;
        }
  case 'efrontlogout':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
   $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            break;
        }
  case 'create_lesson':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'name', _LESSONNAME, 'class = "inputText"');
            $form -> addElement('text', 'category', _CATEGORY, 'class = "inputText"');
            $form -> addElement('select', 'course_only', _COURSEONLY, array(0 => _NO, 1 => _YES));
            $form -> addElement('text', 'language', _LANGUAGE, 'class = "inputText"');
   $form -> addElement('text', 'price', _PRICE, 'class = "inputText"');
            break;
        } case 'create_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('password', 'password', _PASSWORD, 'class = "inputText"');
            $form -> addElement('text', 'name', _FIRSTNAME, 'class = "inputText"');
            $form -> addElement('text', 'surname', _SURNAME, 'class = "inputText"');
            $form -> addElement('text', 'email', _EMAIL, 'class = "inputText"');
            $form -> addElement('text', 'language', _LANGUAGE, 'class = "inputText"');
            break;
        }
        case 'update_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('password', 'password', _PASSWORD, 'class = "inputText"');
            $form -> addElement('text', 'name', _FIRSTNAME, 'class = "inputText"');
            $form -> addElement('text', 'surname', _SURNAME, 'class = "inputText"');
            $form -> addElement('text', 'email', _EMAIL, 'class = "inputText"');
            $form -> addElement('text', 'language', _LANGUAGE, 'class = "inputText"');
            break;
        }
        case 'activate_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            break;
        }
        case 'deactivate_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
           break;
        }
        case 'remove_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            break;
        }
        case 'groups':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            break;
        }
        case 'group_info':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'group', _GROUP, 'class = "inputText"');
            break;
        }
        case 'group_to_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('text', 'group', _GROUP, 'class = "inputText"');
            break;
        }
        case 'group_from_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('text', 'group', _GROUP, 'class = "inputText"');
            break;
        }
        case 'lesson_to_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('text', 'lesson', _LESSON, 'class = "inputText"');
   $form -> addElement("select", "type", _USERTYPE, array("student"=>_STUDENT, "professor"=>_PROFESSOR), 'class = "inputText"');
            break;
        }
        case 'lesson_from_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('text', 'lesson', _LESSON, 'class = "inputText"');
            break;
        }
        case 'user_lessons':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            break;
        }
        case 'course_to_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('text', 'course', _COURSE, 'class = "inputText"');
   $form -> addElement("select", "type", _USERTYPE, array("student"=>_STUDENT, "professor"=>_PROFESSOR), 'class = "inputText"');
            break;
        }
  case 'curriculum_to_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('text', 'curriculum', _CURRICULUM, 'class = "inputText"');
            break;
        }
        case 'course_from_user':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            $form -> addElement('text', 'course', _COURSE, 'class = "inputText"');
            break;
        }

        case 'user_courses':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            break;
        }
        case 'lesson_info':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'lesson', _LESSON, 'class = "inputText"');
            break;
        }
        case 'course_info':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'course', _COURSE, 'class = "inputText"');
            break;
        }
  case 'course_lessons':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'course', _COURSE, 'class = "inputText"');
            break;
        }
        case 'user_info':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
            break;
        }
        case 'catalog':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            break;
        }
        case 'lessons':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            break;
        }
        case 'courses':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            break;
        }
        case 'logout':{
            $form -> addElement('text', 'token', _TOKEN, 'class = "inputText"');
            break;
        }
    }
    $form -> addElement('textarea', 'output', _OUTPUT, 'class = "simpleEditor inputTextarea" style = "disabled:true;width:60%;height:120px"');
    $form -> addElement('submit', 'submit_action', _SUBMIT, 'class = "flatButton"');

    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            $values = $form -> exportValues();
            switch ($action){
                case 'token':{
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=token', 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'login':{
                    $login = $values['login'];
                    $pwd = $values['password'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=login&username='.$login.
                     '&password='.$pwd."&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'efrontlogin':{
                    $token = $values['token'];
     $login = $values['login'];
     /*

					 * WARNING: This will not work as expected: It will simply register the user as being login, without actually logging 

					 * in the browser to the system, due to the inability to set session variables through fopen() (and streams in general).

					 * If we need to login the current browser to the system, we need to open an actual connection FROM the browser to the

					 * api2.php page, using the same URL query string. For example, this can be done using header(), an iframe, or a javascript

					 * popup window. For example:

					 * echo "<script>var mine = window.open('api2.php?action=efrontlogin&token=".$token."&login=".$login."', 'api', 'width=1,height=1,left=0,top=0,scrollbars=no');</script>";

					 * -OR- using AJAX query:

					 * 		echo '

					 *			<script type = "text/javascript" src = "js/scriptaculous/prototype.php"> </script>

					 *			<script>new Ajax.Request("api2.php?action=efrontlogin&token='.$token.'&login=professor")</script>';

					 */
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=efrontlogin&token='.$token.'&login='.$login, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    //echo "<script>var mine = window.open('api2.php?action=efrontlogin&token=".$token."&login=".$login."', 'api', 'width=1,height=1,left=0,top=0,scrollbars=no');</script>";
     /*

					echo '

						<script type = "text/javascript" src = "js/scriptaculous/prototype.php"> </script>

						<script>new Ajax.Request("api2.php?action=efrontlogin&token='.$token.'&login=professor")</script>';					

					 */
                    break;
                }
    case 'efrontlogout':{
                    $token = $values['token'];
     $login = $values['login'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=efrontlogout&token='.$token.'&login='.$login, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
    case 'create_lesson':{
                    $name = $values['name'];
                    $category = $values['category'];
                    $token = $values['token'];
                    $course_only = $values['course_only'];
                    $price = $values['price'];
                    $language = $values['language'];
     $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=create_lesson&name='.urlencode($name).
                     '&category='.$category.'&course_only='.$course_only.'&price='.$price.'&language='.$language.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
    }
                case 'create_user':{
                    $login = $values['login'];
                    $pwd = $values['password'];
                    $token = $values['token'];
                    $name = $values['name'];
                    $surname = $values['surname'];
                    $language = $values['language'];
                    $email = $values['email'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=create_user&login='.$login.
                     '&password='.$pwd.'&name='.$name.'&surname='.$surname.'&email='.$email.'&languages='.$language.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'update_user':{
                    $login = $values['login'];
                    $pwd = $values['password'];
                    $token = $values['token'];
                    $name = $values['name'];
                    $surname = $values['surname'];
                    $email = $values['email'];
                    $token = $values['token'];
     $language = $values['language'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=update_user&login='.$login.
                     '&password='.$pwd.'&name='.$name.'&surname='.$surname.'&email='.$email.'&token='.$token.'&language='.$language, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'activate_user':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=activate_user&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'deactivate_user':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=deactivate_user&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'remove_user':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=remove_user&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'groups':{
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=groups&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                    break;
                }
                case 'group_info':{
                    $group = $values['group'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=group_info&group='.$group.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'group_to_user':{
                    $login = $values['login'];
                    $group = $values['group'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=group_to_user&login='.$login.
                    '&group='.$group.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'group_from_user':{
                    $login = $values['login'];
                    $group = $values['group'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=group_from_user&login='.$login.
                    '&group='.$group.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                    break;
                }
                case 'lesson_to_user':{
                    $login = $values['login'];
                    $lesson = $values['lesson'];
     $type = $values['type'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=lesson_to_user&login='.$login.
                    '&lesson='.$lesson.'&type='.$type.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'lesson_from_user':{
                    $login = $values['login'];
                    $lesson = $values['lesson'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=lesson_from_user&login='.$login.
                    '&lesson='.$lesson.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                    break;
                }
                case 'user_lessons':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=user_lessons&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'lesson_info':{
                    $lesson = $values['lesson'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=lesson_info&lesson='.$lesson.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'course_to_user':{
                    $login = $values['login'];
                    $course = $values['course'];
     $type = $values['type'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=course_to_user&login='.$login.
                    '&course='.$course.'&type='.$type.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'course_from_user':{
                    $login = $values['login'];
                    $course = $values['course'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=course_from_user&login='.$login.
                    '&course='.$course.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                    break;
                }
                case 'user_courses':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=user_courses&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'course_info':{
                    $course = $values['course'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=course_info&course='.$course.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
    case 'course_lessons':{
                    $course = $values['course'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=course_lessons&course='.$course.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'user_info':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=user_info&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'catalog':{
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=catalog&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                    break;
                }
                case 'lessons':{
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=lessons&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                    break;
                }
                case 'courses':{
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=courses&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                    break;
                }
    case 'curriculum_to_user':{
                    $login = $values['login'];
                    $curriculum = $values['curriculum'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=curriculum_to_user&login='.$login.
                    '&curriculum='.$curriculum.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'logout':{
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api2.php?action=logout&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
            }
        }
    }
    $form -> setDefaults(array('action' => $action_id));
    $element = & $form->getElement('output');
    $element -> setValue($output);
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);
    $smarty -> assign('T_ACTION_FORM', $renderer -> toArray());
    $smarty -> display('apidemo2.tpl');
?>
