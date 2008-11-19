<?php
/*
This file lets you to make requests to eFront and get some useful information in xml format.
You need to be an administrator to use eFront API. First of all you need a token that must be used
in all the next requests for authentication. To get a token you have to call /api.php?action=token
The returned token must be used in the next calls. Action argument determnines the action that you want to do.
Foreach request you must provide the action argument, a token and possibly a set for other arguments.
Below are the available action arguments an the corresponding arguments needed (where <token> is the returned token).
/api.php?token=<token>&username=<login>&password=<password> 		logs <login> in eFront API (<login> must be an administrator account)
/api.php?token=<token>&action=efrontlogin&login=<login> 			logs <login> in eFront
/api.php?token=<token>&action=create_user&login=<login>&password=<password>&email=<email>&languages=<languages>&name=<name>&surname<surname> 	creates a new user with corresponding fields	
/api.php?token=<token>&action=update_user&login=<login>&password=<password>&email=<email>&name=<name>&surname<surname> 	updates a user profile with corresponding fields	
/api.php?token=<token>&action=deactivate_user&login=<login>			deactivates user <login>
/api.php?token=<token>&action=activate_user&login=<login>			activates user <login>
/api.php?token=<token>&action=remove_user&login=<login>				deletes user <login>
/api.php?token=<token>&action=lesson_to_user&login=<login>&lesson=<lesson_id>		assigns lesson with <lesson_id> to user <login>   
/api.php?token=<token>&action=lesson_from_user&login=<login>&lesson=<lesson_id>		undo assignment for lesson with <lesson_id> to user <login>  
/api.php?token=<token>&action=user_lessons&login=<login> 							returns the lessons that are assigned to the user <login>
/api.php?token=<token>&action=lesson_info&lesson=<lesson_id>						returns <lesson_id> information 
/api.php?token=<token>&action=user_info&login=<login>								returns <login> information 
/api.php?token=<token>&action=lessons												returns all lessons defined in eFront
/api.php?token=<token>&action=courses												returns all courses defined in eFront
/api.php?token=<token>&action=course_info&course=<course_id>						returns <course_id> information
/api.php?token=<token>&action=course_lessons&course=<course_id>						returns lessons containing in <course_id>
/api.php?token=<token>&action=course_to_user&course=<course_id>&login=<login>		assigns course with <course_id> to user <login>
/api.php?token=<token>&action=course_from_user&course=<course_id>&login=<login>		undo assignment for course with <course_id> to user <login>
/api.php?token=<token>&action=logout												logs out from eFront API

API returns xml corresponding to the action argument. For actions like efrontlogin, activate_user etc it returns a status entity ("ok" or "error").
In case of error it returns also a message entity with description of the error occured.
*/
	$path = "../libraries/"; 
	require_once $path."configuration.php";
    $data       = eF_getTableData("configuration", "value", "name='api'");                                        //Read current values
    $api = $data[0]['value'];
    if ($api == 1){
        if (isset($_GET['action'])){
            $action = $_GET['action'];
            switch($_GET['action']){
                case 'token':
                    $token = createToken(30);
                    if (strlen($token) ==30){
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
    
                        if (isset($_SESSION['s_login'])) {
							$user = EfrontUserFactory :: factory($_SESSION['s_login']);
							$user -> logout();
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
						$password = $user->user['password'];
						$ok = $user -> login($password, true);
                        if ($ok){
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
                 case 'login':{
					if (isset($_GET['username']) && isset($_GET['password']) && isset($_GET['token']) ){
						$user = EfrontUserFactory :: factory($_GET['username']); 
						if ($user ->user['user_type'] == "administrator") {		
							$login = $_GET['username'];
							$password = md5($_GET['password'].(G_MD5KEY));
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
						} else{
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
                }
                case 'create_user':{
                    if (isset($_GET['token']) && checkToken($_GET['token'])){
                        if (isset($_GET['login']) && isset($_GET['password']) && isset($_GET['email']) && isset($_GET['languages']) && isset($_GET['name']) && isset($_GET['surname'])){
                            $insert['login'] = $_GET['login'];
                            $insert['password'] = md5($_GET['password'].(G_MD5KEY));
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
                            $fields['password'] = md5($_GET['password'].(G_MD5KEY));
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
                            $update['active'] = '0';
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
                            $update['active'] = '1';
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
                            $user -> delete();\
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
                        $courses = eF_getTableData("courses","id, name, info, price, languages_NAME");
						echo "<xml>";
						echo "\n\t";
                        echo "<courses>";
						for ($i=0; $i < sizeof($courses);$i++){
							echo "\n\t\t";
							echo "<course>";
							echo "\n\t\t";
							echo "<id>".$courses[$i]['id']."</id>";
							echo "\n\t\t";
							echo "<name>".$courses[$i]['name']."</name>";
							echo "\n\t\t";
                            echo "</course>";
						}
						echo "\n\t";
						echo "</courses>";
						echo "\n\t";
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
								echo "\n\t";
								echo "<general_info>";
									echo "\n\t\t";
									echo "<id>".$course[0]['id']."</id>";
									echo "\n\t\t";
									echo "<name>".$course[0]['name']."</name>"; 
									echo "\n\t\t";
									echo "<info>".$course[0]['info']."</info>"; 
									echo "\n\t\t";
									echo "<price>".$course[0]['price']."</price>"; 
									echo "\n\t\t";
									echo "<active>".$course[0]['active']."</active>"; 
									echo "\n\t\t";
									echo "<languages_NAME>".$course[0]['languages_NAME']."</languages_NAME>"; 
								echo "\n\t";
								echo "<general_info>";	
							echo "\n";
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
							$lessons = $course->getLessons();						

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
									$course ->addUsers($_GET['login']);
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
?>

