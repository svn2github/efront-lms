<?php
    session_cache_limiter('none');          //Initialize session
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
    $actions[3] = "efrontlogin";
    $actions[4] = "create_user";
    $actions[5] = "update_user";
    $actions[6] = "activate_user";
    $actions[7] = "deactivate_user";
    $actions[8] = "remove_user";
    $actions[9] = "lesson_to_user";
    $actions[10] = "lesson_from_user";
    $actions[11] = "user_lessons";
    $actions[12] = "lesson_info";
    $actions[13] = "user_info";
    $actions[14] = "lessons";
    $actions[15] = "logout";
    
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
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   	   //Register this rule for checking user input with our function, eF_checkParameter    
    $form -> addElement('select', 'action', _ACTION, $actions, 'class = "inputSelect" id = "action" onchange = "window.location = \''.basename($_SERVER['PHP_SELF']).'?action=\'+this.options[this.selectedIndex].value"');     //Depending on user selection, changing the question type reloads the page with the corresponding form fields
    $form -> addRule('action', _THEFIELD.' '._QUESTIONTYPE.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('action', _INVALIDFIELDDATA, 'callback', 'text');        
    $output = "";
    switch ($action){
        case 'token':{
            break;
        }
        case 'login':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');    
            $form -> addElement('password', 'password', _PASSWORD, 'class = "inputText"');    
            break;   
        }
        case 'efrontlogin':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            break;
        }
        case 'create_user':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');    
            $form -> addElement('password', 'password', _PASSWORD, 'class = "inputText"');    
            $form -> addElement('text', 'name', _NAME, 'class = "inputText"');   
            $form -> addElement('text', 'surname', _SURNAME, 'class = "inputText"'); 
            $form -> addElement('text', 'email', _EMAIL, 'class = "inputText"');   
            $form -> addElement('text', 'language', _LANGUAGE, 'class = "inputText"'); 
            break;
        }
        case 'update_user':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');    
            $form -> addElement('password', 'password', _PASSWORD, 'class = "inputText"');    
            $form -> addElement('text', 'name', _NAME, 'class = "inputText"');   
            $form -> addElement('text', 'surname', _SURNAME, 'class = "inputText"'); 
            $form -> addElement('text', 'email', _EMAIL, 'class = "inputText"');   
            break;
        }
        case 'activate_user':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');    
            break;
        }
        case 'deactivate_user':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"');  
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');    
           break; 
        }
        case 'remove_user':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');    
            break;
        }
        case 'lesson_to_user':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"');  
            $form -> addElement('text', 'login', _LOGIN,   'class = "inputText"');    
            $form -> addElement('text', 'lesson', _LESSON, 'class = "inputText"');    
            break;
        }
        case 'lesson_from_user':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            $form -> addElement('text', 'login', _LOGIN,   'class = "inputText"');    
            $form -> addElement('text', 'lesson', _LESSON, 'class = "inputText"');    
            break;
        }
        case 'user_lessons':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"');  
            $form -> addElement('text', 'login', _LOGIN,   'class = "inputText"');    
            break;
        }
        case 'lesson_info':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            $form -> addElement('text', 'lesson', _LOGIN,   'class = "inputText"');    
            break;
        }
        case 'user_info':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"');  
            $form -> addElement('text', 'login', _LOGIN,   'class = "inputText"');  
            break;
        }
        case 'lessons':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
            break;
        }
        case 'logout':{
            $form -> addElement('text', 'token', _TOKEN,   'class = "inputText"'); 
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
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=token', 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'login':{
                    $login = $values['login'];
                    $pwd = $values['password'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=login&username='.$login.
                     '&password='.$pwd."&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;   
                }
                case 'efrontlogin':{
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=efrontlogin&token='.$token, 'r')) {
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
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=create_user&login='.$login.
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
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=update_user&login='.$login.
                     '&password='.$pwd.'&name='.$name.'&surname='.$surname.'&email='.$email.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;
                }
                case 'activate_user':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=activate_user&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;   
                }
                case 'deactivate_user':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=deactivate_user&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;    
                }
                case 'remove_user':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=remove_user&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;   
                }
                case 'lesson_to_user':{ 
                    $login = $values['login'];
                    $lesson = $values['lesson'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=lesson_to_user&login='.$login.
                    '&lesson='.$lesson.'&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;   
                }
                case 'lesson_from_user':{
                    $login = $values['login'];
                    $lesson = $values['lesson'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=lesson_to_user&login='.$login.
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
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=user_lessons&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;    
                }
                case 'lesson_info':{
                    $lesson = $values['lesson'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=lesson_info&lesson='.$lesson.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;    
                }
                case 'user_info':{
                    $login = $values['login'];
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=user_info&login='.$login.
                    "&token=".$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;    
                }
                case 'lessons':{
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=lessons&token='.$token, 'r')) {
                        $output = stream_get_contents($stream);
                        fclose($stream);
                    }
                    break;   
                    break;
                }
                case 'logout':{
                    $token = $values['token'];
                    if ($stream = fopen(G_SERVERNAME.'api.php?action=logout&token='.$token, 'r')) {
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
    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);
    $smarty -> assign('T_ACTION_FORM', $renderer -> toArray());         
    $smarty -> display('apidemo.tpl');
?>