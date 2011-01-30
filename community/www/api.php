<?php
/*

This file lets you to make requests to eFront and get some useful information in xml format.

You need to be an administrator to use eFront API. First of all you need a token that must be used

in all the next requests for authentication. To get a token you have to call /api.php?action=token

The returned token must be used in the next calls. Action argument determnines the action that you want to do.

Foreach request you must provide the action argument, a token and possibly a set for other arguments.

Below are the available action arguments an the corresponding arguments needed (where <token> is the returned token).

/api.php?token=<token>&action=login&username=<login>&password=<password> 		logs <login> in eFront API (<login> must be an administrator account)

/api.php?token=<token>&action=efrontlogin&login=<login> 			logs <login> in eFront

/api.php?token=<token>&action=create_lesson&name=<name>&category=<category_id>&course_only=<course_only>&language=<language>&price=<price>	creates a new lesson with corresponding fields

/api.php?token=<token>&action=create_user&login=<login>&password=<password>&email=<email>&languages=<languages>&name=<name>&surname<surname> 	creates a new user with corresponding fields

/api.php?token=<token>&action=update_user&login=<login>&password=<password>&email=<email>&name=<name>&surname<surname> 	updates a user profile with corresponding fields

/api.php?token=<token>&action=deactivate_user&login=<login>			deactivates user <login>

/api.php?token=<token>&action=activate_user&login=<login>			activates user <login>

/api.php?token=<token>&action=remove_user&login=<login>				deletes user <login>

/api.php?token=<token>&action=groups                                                returns all groups defined in eFront

/api.php?token=<token>&action=group_info&group=<group_id>                           returns <group_id> information

/api.php?token=<token>&action=group_to_user&login=<login>&group=<group_id>          assigns group with <group_id> to user <login>

/api.php?token=<token>&action=group_from_user&login=<login>&lesson=<group_id>       undo assignment for group with <group_id> to user <login>

/api.php?token=<token>&action=lesson_to_user&login=<login>&lesson=<lesson_id>		assigns lesson with <lesson_id> to user <login>

/api.php?token=<token>&action=activate_user_lesson&login=<login>&lesson=<lesson_id> activate assignment for lesson with <lesson_id> to user <login>

/api.php?token=<token>&action=deactivate_user_lesson&login=<login>&lesson=<lesson_id> deactivate assignment for lesson with <lesson_id> to user <login>

/api.php?token=<token>&action=lesson_from_user&login=<login>&lesson=<lesson_id>		undo assignment for lesson with <lesson_id> to user <login>

/api.php?token=<token>&action=course_to_user&login=<login>&course=<course_id>		assigns course with <course_id> to user <login>

/api.php?token=<token>&action=course_from_user&login=<login>&courses=<course_id>	undo assignment for course with <course_id> to user <login>

/api.php?token=<token>&action=user_lessons&login=<login> 							returns the lessons that are assigned to the user <login>

/api.php?token=<token>&action=user_courses&login=<login> 							returns the courses that are assigned to the user <login>

/api.php?token=<token>&action=lesson_info&lesson=<lesson_id>						returns <lesson_id> information

/api.php?token=<token>&action=user_info&login=<login>								returns <login> information

/api.php?token=<token>&action=lessons												returns all lessons defined in eFront

/api.php?token=<token>&action=courses												returns all courses defined in eFront

/api.php?token=<token>&action=course_info&course=<course_id>						returns <course_id> information

/api.php?token=<token>&action=course_lessons&course=<course_id>						returns lessons containing in <course_id>

/api.php?token=<token>&action=course_to_user&course=<course_id>&login=<login>		assigns course with <course_id> to user <login>

/api.php?token=<token>&action=activate_user_course&login=<login>&course=<course_id> activate assignment for all lessons within <course_id> to user <login>

/api.php?token=<token>&action=deactivate_user_course&login=<login>&course=<course_id> deactivate assignment for all lessons within <course_id> to user <login>

/api.php?token=<token>&action=course_from_user&course=<course_id>&login=<login>		undo assignment for course with <course_id> to user <login>

/api.php?token=<token>&action=catalog												returns the list with all courses and lessons of the system

/api.php?token=<token>&action=logout												logs out from eFront API



API returns xml corresponding to the action argument. For actions like efrontlogin, activate_user etc it returns a status entity ("ok" or "error").

In case of error it returns also a message entity with description of the error occured.

*/
 $path = "../libraries/";
 require_once $path."configuration.php";
    $data = eF_getTableData("configuration", "value", "name='api'"); //Read current values
    $api = $data[0]['value'];
    if ($api == 1){
        if (isset($_GET['action'])){
            $action = $_GET['action'];
            switch($_GET['action']){
                case 'token':
                    $token = createToken(30);
                    if (strlen($token) == 30){
                        $insert['token'] = $token;
                        $insert['status'] = "unlogged";
                        $insert['expired'] = 0;
                        $insert['create_timestamp'] = time();
                        eF_insertTableData("tokens", $insert);
                        echo "<xml>";
      echo "<token>".$token."</token>";
      echo "</xml>";
                    }
                    break;
                 case 'efrontlogin':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        $token = $_GET['token'];
                        $creds = eF_getTableData("tokens t, users u", "u.login, u.password, u.user_type", "t.users_LOGIN = u.LOGIN and t.token='$token'");
                        if (sizeof($creds) == 0){
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Invalid username</message>";
       echo "</xml>";
                            break;
                        }
                        try {
       $user = EfrontUserFactory :: factory($_GET['login']);
      } catch (Exception $e) {
       echo "<xml>";
       echo "<status>error</status>";
                            echo "<message>This user does not exist</message>";
       echo "</xml>";
       break;
      }
      $password = $user -> user['password'];
      session_start();//Otherwise it won't store session data
      $ok = $user -> login($password, true);
                        if ($ok) {
       echo "<xml>";
                            echo "<status>ok</status>";
       echo "</xml>";
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Unable to update log</message>";
       echo "</xml>";
                        }
                        break;
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                        break;
                    }
                 }
     case 'efrontlogout':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])) {
                        $token = $_GET['token'];
      if (isset($_GET['login'])) {
       try {
        $user = EfrontUserFactory :: factory($_GET['login']);
        try{
         $user -> logout();
         echo "<xml>";
         echo "<status>ok</status>";
         echo "</xml>";
         break;
        } catch (Exception $e) {
         echo "<xml>";
         echo "<status>error</status>";
         echo "<message>User can not logout</message>";
         echo "</xml>";
         break;
        }
       } catch (Exception $e) {
        echo "<xml>";
        echo "<status>error</status>";
        echo "<message>This user does not exist</message>";
        echo "</xml>";
        break;
       }
      } else {
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
       break;
      }
     } else {
      echo "<xml>";
      echo "<status>error</status>";
      echo "<message>Invalid token</message>";
      echo "</xml>";
      break;
     }
    }
                 case 'login':{
     if (isset($_GET['username']) && isset($_GET['password']) && isset($_GET['token']) ){
      $user = EfrontUserFactory :: factory($_GET['username']);
      if ($user -> user['user_type'] == "administrator") {
       $login = $_GET['username'];
       $password = EfrontUser::createPassword($_GET['password']);
       $token = $_GET['token'];
       $tmp = eF_getTableData("tokens","token","status='unlogged'");
       if (sizeof($tmp) > 0){
        if (eF_checkParameter($login, 'login')) {
         $tmp = eF_getTableData("users", "password","login='$login'");
         $pwd = $tmp[0]['password'];
         if ($pwd == $password){
          $update['status'] = "logged";
          $update['users_LOGIN'] = $login;
          eF_updateTableData("tokens",$update,"token='$token'");
          echo "<xml>";
          echo "<status>ok</status>";
          echo "</xml>";
         } else{
          echo "<xml>";
          echo "<status>error</status>";
          echo "<message>Invalid password</message>";
          echo "</xml>";
         }
        } else {
         echo "<xml>";
         echo "<status>error</status>";
         echo "<message>Invalid username</message>";
         echo "</xml>";
        }
       } else{
        echo "<xml>";
        echo "<status>error</status>";
        echo "<message>Invalid token</message>";
        echo "</xml>";
       }
      } else {
       echo "<xml>";
       echo "<status>error</status>";
       echo "<message>You must have an administrator account to login to eFront API</message>";
       echo "</xml>";
      }
     } else {
      echo "<xml>";
      echo "<status>error</status>";
      echo "<message>No username/password/token provided</message>";
      echo "</xml>";
     }
                    break;
                } case 'create_lesson':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['name']) && isset($_GET['category']) && isset($_GET['course_only']) && isset($_GET['language'])){
       if (!eF_checkParameter($_GET['category'], 'uint')) {
        echo "<xml>";
                                echo "<status>Invalid category</status>";
        echo "</xml>";
        exit;
       }
                            $insert['name'] = $_GET['name'];
                            $insert['directions_ID'] = $_GET['category'];
                            $insert['course_only'] = $_GET['course_only'];
                            $insert['languages_NAME'] = $_GET['language'];
       $insert['created'] = time();
       if (isset($_GET['price']) && eF_checkParameter($_GET['price'], 'uint')) {
        $insert['price'] = $_GET['price'];
       }
                            if (eF_insertTableData("lessons", $insert))
                            {
        echo "<xml>";
                                echo "<status>ok</status>";
        echo "</xml>";
                            }
                            else
                            {
        echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>Some problem occured</message>";
        echo "</xml>";
                            }
                        }
                        else
                        {
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                } case 'create_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['password']) && isset($_GET['email']) && isset($_GET['languages']) && isset($_GET['name']) && isset($_GET['surname'])){
                            $insert['login'] = $_GET['login'];
                            $insert['password'] = EfrontUser :: createPassword($_GET['password']);
                            $insert['email'] = $_GET['email'];
                            $insert['languages_NAME'] = $_GET['languages'];
                            $insert['name'] = $_GET['name'];
                            $insert['surname'] = $_GET['surname'];
                            if (eF_insertTableData("users", $insert))
                            {
        echo "<xml>";
                                echo "<status>ok</status>";
        echo "</xml>";
                            }
                            else
                            {
        echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>User exists</message>";
        echo "</xml>";
                            }
                        }
                        else
                        {
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'update_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['password']) && isset($_GET['email']) && isset($_GET['name']) && isset($_GET['surname'])){
                            $fields['password'] = EfrontUser::createPassword($_GET['password']);
                            $fields['email'] = $_GET['email'];
                            $fields['name'] = $_GET['name'];
                            $fields['surname'] = $_GET['surname'];
                            if (eF_updateTableData("users", $fields, "login='".$_GET['login']."'"))
                            {
        echo "<xml>";
                                echo "<status>ok</status>";
        echo "</xml>";
                            }
                            else
                            {
        echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>User exists</message>";
        echo "</xml>";
                            }
                        }
                        else
                        {
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'deactivate_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login'])){
       $login = $_GET['login'];
                            $update['active'] = 0;
                            if (eF_updateTableData("users",$update, "login='$login'")){
        echo "<xml>";
                                echo "<status>ok</status>";
        echo "</xml>";
                            }
                            else{
        echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>User doesn't exist</message>";
        echo "</xml>";
                            }
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'activate_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login'])){
       $login = $_GET['login'];
                            $update['active'] = 1;
                            if (eF_updateTableData("users",$update, "login='$login'")){
        echo "<xml>";
                                echo "<status>ok</status>";
        echo "</xml>";
                            }
                            else{
        echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>User doesn't exist</message>";
        echo "</xml>";
                            }
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'remove_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login'])){
                            $user = EfrontUserFactory :: factory($_GET['login']);
                            $user -> delete();
       echo "<xml>";
                            echo "<status>ok</status>";
       echo "</xml>";
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'groups':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        $groups = eF_getTableData("groups","id, name");
                        echo "<xml>";
                        echo "<groups>";
                        for ($i=0; $i < sizeof($groups);$i++){
                            echo "<group>";
                            echo "<id>".$groups[$i]['id']."</id>";
                            echo "<name>".$groups[$i]['name']."</name>";
                            echo "</group>";
                        }
                        echo "</groups>";
                        echo "</xml>";
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'group_info':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['group'])){
                            try{
                               $group = eF_getTableData("groups","name, description, languages_NAME,unique_key");
                                echo "<xml>";
                                echo "<general_info>";
                                echo "<name>".$group[0]['name']."</name>";
                                echo "<description>".$group[0]['description']."</description>";
                                echo "<language>".$group[0]['languages_NAME']."</language>";
                                echo "<unique_key>".$group[0]['unique_key']."</unique_key>";
                                echo "</general_info>";
                                echo "</xml>";
                            }
                            catch (Exception $e){
                                echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>Group doesn't exist</message>";
                                echo "</xml>";
                            }
                        }
                        else{
                            echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'group_to_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['group'])){
                            $insert['users_LOGIN'] = $_GET['login'];
                            $insert['groups_ID'] = $_GET['group'];
                            $res = eF_getTableData("users_to_groups", "*", "users_LOGIN='".$_GET['login']."' and groups_ID=".$_GET['group']);
                            if (sizeof($res) == 0){
                                eF_insertTableData("users_to_groups",$insert);
                                echo "<xml>";
                                echo "<status>ok</status>";
                                echo "</xml>";
                            }
                            else{
                                echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>Assignment already exists</message>";
                                echo "</xml>";
                            }
                        }
                        else{
                            echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'group_from_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['group'])){
                            $res = eF_deleteTableData("users_to_groups", "users_LOGIN='".$_GET['login']."' and groups_ID=".$_GET['group']);
                            echo "<xml>";
                            echo "<status>ok</status>";
                            echo "</xml>";
                        }
                        else{
                            echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'lesson_to_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['lesson'])){
                            $insert['users_LOGIN'] = $_GET['login'];
                            $insert['lessons_ID'] = $_GET['lesson'];
                            $insert['active'] = '1';
                            $insert['from_timestamp'] = time();
                            $res = eF_getTableData("users_to_lessons", "*", "users_LOGIN='".$_GET['login']."' and lessons_ID=".$_GET['lesson']);
                            if (sizeof($res) == 0){
                                eF_insertTableData("users_to_lessons",$insert);
        echo "<xml>";
                                echo "<status>ok</status>";
        echo "</xml>";
                            }
                            else{
        echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>Assignment already exists</message>";
        echo "</xml>";
                            }
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'deactivate_user_lesson':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['lesson'])){
                            $update['from_timestamp'] = 0;
                            if (eF_updateTableData("users_to_lessons",$update, "users_LOGIN='".$_GET['login']."' and lessons_ID=".$_GET['lesson'])){
                                $cacheKey = "user_lesson_status:lesson:".$_GET['lesson']."user:".$_GET['login'];
                                Cache::resetCache($cacheKey);
                                echo "<xml>";
                                echo "<status>ok</status>";
                                echo "</xml>";
                            }
                            else{
                                echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>User doesn't exist</message>";
                                echo "</xml>";
                            }
                        }
                        else{
                            echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'activate_user_lesson':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['lesson'])){
                            $update['from_timestamp'] = time();
                            if (eF_updateTableData("users_to_lessons",$update, "users_LOGIN='".$_GET['login']."' and lessons_ID=".$_GET['lesson'])){
                                $cacheKey = "user_lesson_status:lesson:".$_GET['lesson']."user:".$_GET['login'];
                                Cache::resetCache($cacheKey);
                                echo "<xml>";
                                echo "<status>ok</status>";
                                echo "</xml>";
                            }
                            else{
                                echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>User doesn't exist</message>";
                                echo "</xml>";
                            }
                        }
                        else{
                            echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'lesson_from_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['lesson'])){
                            $res = eF_deleteTableData("users_to_lessons", "users_LOGIN='".$_GET['login']."' and lessons_ID=".$_GET['lesson']);
       echo "<xml>";
                            echo "<status>ok</status>";
       echo "</xml>";
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'user_lessons':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login'])){
                            $lessons = eF_getTableData("users_to_lessons ul, lessons l", "l.name", "ul.lessons_ID = l.ID and ul.users_LOGIN='".$_GET['login']."'");
                            echo "<xml>";
                            for ($i=0; $i<sizeof($lessons); $i++){
                                echo "<lesson>".$lessons[$i]['name']."</lesson>";
                            }
                            echo "</xml>";
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'lesson_info':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['lesson'])){
                            try{
                                $lesson = new EfrontLesson($_GET['lesson']);
                                $info = $lesson -> getStatisticInformation();
                                echo "<xml>";
                                echo "<general_info>";
                                echo "<name>".$lesson -> lesson['name']."</name>";
                                echo "<direction>".$info['direction']."</direction>";
                                echo "<price>".$info['price_string']."</price>";
                                echo "<language>".$info['language']."</language>";
                                echo "</general_info>";
                                echo "</xml>";
                            }
                            catch (Exception $e){
        echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>Lesson doesn't exist</message>";
        echo "</xml>";
                            }
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'user_courses':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login'])){
                            $courses = eF_getTableData("users_to_courses ul, courses l", "l.name", "ul.courses_ID = l.ID and ul.users_LOGIN='".$_GET['login']."'");
                            echo "<xml>";
                            for ($i=0; $i<sizeof($courses); $i++){
                                echo "<course>".$courses[$i]['name']."</course>";
                            }
                            echo "</xml>";
                        }
                        else{
                            echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'user_info':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login'])){
                            try{
                                $user = EfrontUserFactory :: factory($_GET['login']);
                                echo "<xml>";
                                echo "<general_info>";
                                echo "<name>".$user -> user['name']." ".$user -> user['surname']."</name>";
                                echo "<active>".$user -> user['active']."</active>";
                                echo "<user_type>".$user -> user['user_type']."</user_type>";
                                echo "</general_info>";
                                echo "</xml>";
                            }
                            catch (Exception $e){
        echo "<xml>";
                                echo "<status>error</status>";
                                echo "<message>User doesn't exist</message>";
        echo "</xml>";
                            }
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'catalog':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        $courses = eF_getTableData("courses","id, name");
                        $lessons = eF_getTableData("lessons","id, name");
                        echo "<xml>";
                        echo "<catalog>";
                        echo "<courses>";
                        for ($i=0; $i < sizeof($courses);$i++){
                            echo "<course>";
                            echo "<id>".$courses[$i]['id']."</id>";
                            echo "<name>".$courses[$i]['name']."</name>";
                            echo "</course>";
                        }
                        echo "</courses>";
                        echo "<lessons>";
                        for ($i=0; $i < sizeof($lessons);$i++){
                            echo "<lesson>";
                            echo "<id>".$lessons[$i]['id']."</id>";
                            echo "<name>".$lessons[$i]['name']."</name>";
                            echo "</lesson>";
                        }
                        echo "</lessons>";
                        echo "</catalog>";
                        echo "</xml>";
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'lessons':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        $lessons = eF_getTableData("lessons","id, name");
                        echo "<xml>";
                        echo "<lessons>";
                        for ($i=0; $i < sizeof($lessons);$i++){
                            echo "<lesson>";
                            echo "<id>".$lessons[$i]['id']."</id>";
                            echo "<name>".$lessons[$i]['name']."</name>";
                            echo "</lesson>";
                        }
                        echo "</lessons>";
                        echo "</xml>";
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
    }
                case 'courses':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        $courses = eF_getTableData("courses","id, name");
                        echo "<xml>";
                        echo "<courses>";
                        for ($i=0; $i < sizeof($courses);$i++){
                            echo "<course>";
                            echo "<id>".$courses[$i]['id']."</id>";
                            echo "<name>".$courses[$i]['name']."</name>";
                            echo "</course>";
                        }
                        echo "</courses>";
                        echo "</xml>";
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'course_info':{
                 if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['course'])){
       $course = eF_getTableData("courses", "id, name, info, price, active, languages_NAME", "id ='".$_GET['course']."'");
       echo "<xml>";
        echo "<general_info>";
         echo "<id>".$course[0]['id']."</id>";
         echo "<name>".$course[0]['name']."</name>";
         echo "<info>".$course[0]['info']."</info>";
         echo "<price>".$course[0]['price']."</price>";
         echo "<active>".$course[0]['active']."</active>";
         echo "<languages_NAME>".$course[0]['languages_NAME']."</languages_NAME>";
        echo "<general_info>";
                            echo "</xml>";
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
    }
    case 'course_lessons':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
      if (isset($_GET['course'])){
       $course = new EfrontCourse($_GET['course']);
       $lessons = EfrontCourse::convertLessonObjectsToArrays($course->getCourseLessons());
       echo "<xml>";
        echo "\n\t";
        echo "<lessons>";
        echo "\n\t\t";
        foreach ($lessons as $key=>$values ){
         echo "<lesson>";
          echo "\n\t\t\t";
          echo "<id>".$lessons[$key]['id']."</id>";
          echo "\n\t\t\t";
          echo "<name>".$lessons[$key]['name']."</name>";
          echo "\n\t\t\t";
          echo "<previous_lessons_ID>".$lessons[$key]['previous_lessons_ID']."</previous_lessons_ID>";
          echo "\n\t\t";
         echo "</lesson>";
      }
        echo "\n\t";
        echo "<lessons>";
       echo "\n";
       echo "</xml>";
       /*

							$lessons = eF_getTableData("lessons_to_courses", "courses_ID, lessons_ID, previous_lessons_ID", "courses_ID ='".$_GET['course']."'");

							echo "<xml>";

								echo "\n\t";

								echo "<lessons>";

								echo "\n\t\t";

								for ($i=0; $i < sizeof($lessons);$i++){

									echo "<lesson>";

										echo "\n\t\t\t";

										echo "<courses_ID>".$lessons[$i]['courses_ID']."</courses_ID>";

										echo "\n\t\t\t";

										echo "<lessons_ID>".$lessons[$i]['lessons_ID']."</lessons_ID>";

										echo "\n\t\t\t";

										echo "<previous_lessons_ID>".$lessons[$i]['previous_lessons_ID']."</previous_lessons_ID>";

										echo "\n\t\t";

									echo "</lesson>";

						}

								echo "\n\t";

								echo "<lessons>";

							echo "\n";

							echo "</xml>";

							*/
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
    }
    case 'course_to_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
       if (isset($_GET['login']) && isset($_GET['course'])){
        try{
         $course = new EfrontCourse($_GET['course']);
         if (isset($_GET['type'])) {
          $course ->addUsers($_GET['login'], $_GET['type']);
         } else {
          $course ->addUsers($_GET['login']);
         }
         echo "<xml>";
         echo "<status>ok</status>";
         echo "</xml>";
        }
         catch (Exception $e){
         echo "<xml>";
         echo "<status>error</status>";
         echo "<message>Invalid course/username or user already enrolled into course</message>";
         echo "</xml>";
                            }
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
    }
                case 'activate_user_course':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['course'])){
                            $update['from_timestamp'] = time();
                            $courses = eF_getTableData("lessons_to_courses","lessons_id", "courses_ID=".$_GET['course']);
                            for ($i=0; $i < sizeof($courses);$i++){
                                if (eF_updateTableData("users_to_lessons",$update, "users_LOGIN='".$_GET['login']."' and lessons_ID=".$courses[$i]['lessons_id'])){
                                 $cacheKey = "user_lesson_status:lesson:".$courses[$i]['lessons_id']."user:".$_GET['login'];
                                 Cache::resetCache($cacheKey);
                                }
                            }
                            echo "<xml>";
                            echo "<status>ok</status>";
                            echo "</xml>";
                        }
                        else{
                            echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
                case 'deactivate_user_course':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['course'])){
                            $update['from_timestamp'] = 0;
                            $courses = eF_getTableData("lessons_to_courses","lessons_id", "courses_ID=".$_GET['course']);
                            for ($i=0; $i < sizeof($courses);$i++){
                                if (eF_updateTableData("users_to_lessons",$update, "users_LOGIN='".$_GET['login']."' and lessons_ID=".$courses[$i]['lessons_id'])){
                                 $cacheKey = "user_lesson_status:lesson:".$courses[$i]['lessons_id']."user:".$_GET['login'];
                                 Cache::resetCache($cacheKey);
                                }
                            }
                            echo "<xml>";
                            echo "<status>ok</status>";
                            echo "</xml>";
                        }
                        else{
                            echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
                            echo "</xml>";
                        }
                    }
                    else{
                        echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
                        echo "</xml>";
                    }
                    break;
                }
    case 'course_from_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
       if (isset($_GET['login']) && isset($_GET['course'])){
        try{
         $course = new EfrontCourse($_GET['course']);
         $course ->removeUsers($_GET['login']);
         echo "<xml>";
         echo "<status>ok</status>";
         echo "</xml>";
        }
         catch (Exception $e){
         echo "<xml>";
         echo "<status>error</status>";
         echo "<message>Invalid course/username or user not enrolled into course</message>";
         echo "</xml>";
                            }
                        }
                        else{
       echo "<xml>";
                            echo "<status>error</status>";
                            echo "<message>Incomplete arguments</message>";
       echo "</xml>";
                        }
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>Invalid token</message>";
      echo "</xml>";
                    }
                    break;
                }
                case 'logout':{
                    if (isset($_GET['token'])){
                        $token = $_GET['token'];
                        eF_deleteTableData("tokens","token='$token'");
      echo "<xml>";
                        echo "<status>ok</status>";
      echo "</xml>";
                    }
                    else{
      echo "<xml>";
                        echo "<status>error</status>";
                        echo "<message>No token provided</message>";
      echo "</xml>";
                    }
                    break;
                }
    default: {
     echo "<xml>";
     echo "<status>error</status>";
                    echo "<message>Invalid action argument</message>";
     echo "</xml>";
     break;
    }
            }
  } else {
   echo "<xml>";
   echo "<status>error</status>";
            echo "<message>There is no action argument</message>";
   echo "</xml>";
  }
    }
    else{
  echo "<xml>";
        echo "<status>error</status>";
        echo "<message>The api module is disabled</message>";
  echo "</xml>";
    }
    function createToken($length){
        $salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789"; // salt to select chars from
        srand((double)microtime()*1000000); // start the random generator
        $token=""; // set the inital variable
        for ($i=0;$i<$length;$i++) // loop and create password
        $token = $token . substr ($salt, rand() % strlen($salt), 1);
        return $token;
    }
    function checkToken($token){
     if (eF_checkParameter($token, 'alnum')) {
      $tmp = ef_getTableData("tokens","status","token='$token'");
      $token = $tmp[0]['status'];
      if ($token == 'logged'){
       return true;
      }
     }
     return false;
    }
?>
