<?php
$path = "../libraries/";                //Define default path
require_once $path."configuration.php";
require_once 'charts/php-ofc-library/open-flash-chart.php';

$id = $_GET['id'];
switch ($id){
    case 2:  createTestQuestionTypesPie($_GET['test_id']); break;
    case 3:  createTestQuestionDifficultyPie($_GET['test_id']); break;
    case 4:  createUserTypeChart(); break;
    case 5:  createMostActiveLessonUsersChart($_GET['lesson_id'], $_GET['from'], $_GET['to']); break;
    case 6:  createMostActiveUsersChart($_GET['logins'], $_GET['seconds']); break;
    case 8:  createLessonLoginAccessChart($_GET['lesson_id'], $_GET['from'], $_GET['to']);break;
    case 9:  createLoginAccessChart($_GET['from'], $_GET['to']); break;
    case 10: createUserLessonLoginAccessChart($_GET['from'], $_GET['to'], $_GET['login'], $_GET['lesson_id']); break;
    case 11: createUserLoginAccessChart($_GET['from'], $_GET['to'], $_GET['login']); break;
}

function createMostActiveLessonUsersChart($lesson_id, $from, $to){
    $traffic = EfrontStats :: getUsersTime($lesson_id, false, $from, $to);

    foreach ($traffic as $key => $value) {
        $times[] = ceil($value['total_seconds'] / 60);
    }
    $logins = array_keys($traffic);        

    $title = new title(_MOSTACTIVEUSERS);
    $hbar  = new hbar( '#86BBEF' );
    $max   = 0;
    
    for ($i = sizeof($times) - 1; $i >= 0; $i--){
        $hbar->append_value( new hbar_value(0, $times[$i]) );
        if ($times[$i] > $max){
            $max = $times[$i];
        }
    }    
    $chart = new open_flash_chart();
    $chart -> set_title( $title );
    $chart -> add_element( $hbar );

    $x = new x_axis();
    $x -> set_offset(false);
    $x -> set_range(0, $max);
    $x -> set_steps(ceil($max/5));
    $chart -> set_x_axis( $x );
    
    $y = new y_axis();
    $y -> set_offset( true );
    $y -> set_labels($logins);
    $chart -> add_y_axis( $y );
    
    echo $chart -> toPrettyString();
}


function createTestQuestionTypesPie($test_id){
	$title = new title(_QUESTIONS);
    $pie   = new pie();
    $pie -> set_start_angle(35);
    $pie -> set_animate(true);
    $pie -> set_tooltip( '#val# of #total#<br>#percent# of 100%' );
    
    $stats  = EfrontStats :: getTestInfo($test_id);
    $values = array();
	foreach (Question :: $questionTypes as $questionType => $typeText) {
	    if ($stats[$test_id]['questions'][$questionType] > 0) {
            $values[] = new pie_value($stats[$test_id]['questions'][$questionType], $typeText);    
        }
	}
	$pie -> set_values($values);
    $chart = new open_flash_chart();
    $chart -> set_title($title);
    $chart -> add_element($pie);
    
    $chart -> x_axis = null;
    
    echo $chart->toPrettyString();
}

function createLessonLoginAccessChart($lesson_id, $from, $to){
    $traffic  = EfrontStats :: getUsersTime($lesson_id, false, $from, $to);
    $max      = 0;
    $users    = array();
    $accesses = array();
    foreach ($traffic as $key => $value) {
        $users[]    = $key;
        $accesses[] = (int)$value['accesses'];
        if ($value['accesses'] > $max) {
            $max = (int) $value['accesses'];
        }   
    }

    $title  = new title(_ACCESSNUMBER);
    
    $line_1 = new line_dot();
    $line_1 -> set_values($accesses);
    $line_1 -> set_halo_size( 0 );
    $line_1 -> set_width( 2 );
    $line_1 -> set_dot_size( 4 );
    
    $y = new y_axis();
    $y -> set_range(0, $max + 1);
    
    $x = new x_axis();
    $x -> set_labels_from_array($users);
        
    $chart = new open_flash_chart();
    $chart -> set_title( $title );
    $chart -> add_element( $line_1 );
    $chart -> set_y_axis( $y );
    $chart -> set_x_axis( $x );
    echo $chart -> toPrettyString();   
}

function createTestQuestionDifficultyPie($test_id){
    
}

function createUserLessonLoginAccessChart($from, $to, $login, $lesson_id){
    $traffic = EfrontStats :: getUsersTime($lesson_id, false, $from, $to);

    $result  = eF_getTableData("logs", "id, users_LOGIN, action, timestamp", "timestamp between $from and $to and lessons_id=".$lesson_id." and users_LOGIN = '".$login."' order by timestamp");
    $labels  = array();
    $count   = array();
    //Assign each day of the week an empty slot
    for ($i = $from; $i <= $to; $i = $i + 86400) {
        $labels[] = date('Y/m/d', $i);
        $count[]  = 0;
    }
    //Assign the number of accesses to each week day
    $max = 0;
    foreach ($result as $value) {
        $cnt = 0;
        for ($i = $from; $i <= $to; $i = $i + 86400) {
            if ($i <= $value['timestamp'] && $value['timestamp'] < $i + 86400) {
                $count[$cnt]++;
                if ($count[$cnt] > $max) {
                    $max = $count[$cnt];
                }
            }
            $cnt++;
        }
    }
    $title = new title(_USERACCESSESINLESSON);
    
    $line_1 = new line_dot();
    $line_1 -> set_values($count);
    $line_1 -> set_halo_size( 0 );
    $line_1 -> set_width( 2 );
    $line_1 -> set_dot_size( 4 );
    
    $y = new y_axis();
    $y -> set_range(0, $max + 1);
    
    $x = new x_axis();
    $x -> set_labels_from_array($labels);
        
    $chart = new open_flash_chart();
    $chart -> set_title( $title );
    $chart -> add_element( $line_1 );
    $chart -> set_y_axis( $y );
    $chart -> set_x_axis( $x );
    echo $chart -> toPrettyString();   
}

function createUserLoginAccessChart($from, $to, $login){
    $result = eF_getTableData("logs", "id, users_LOGIN, action, timestamp", "timestamp between $from and $to and action = 'login' and users_LOGIN = '".$login."' order by timestamp");

    //Assign each day of the week an empty slot
    $labels = array();
    $count = array();
    for ($i = $from; $i <= $to; $i = $i + 86400) {
        $labels[] = date('Y/m/d', $i);
        $count[]  = 0;
    }

    //Assign the number of accesses to each week day
    $max = 0;
    foreach ($result as $value) {
        $cnt = 0;
        for ($i = $from; $i <= $to; $i = $i + 86400) {
            if ($i <= $value['timestamp'] && $value['timestamp'] < $i + 86400) {
                $count[$cnt]++;
                if ($count[$cnt] > $max){
                    $max = $count[$cnt];
                }
            }
            $cnt++;
        }
    }
     
    $title = new title(_LOGINS);
    
    $line_1 = new line_dot();
    $line_1 -> set_values($count);
    $line_1 -> set_halo_size( 0 );
    $line_1 -> set_width( 2 );
    $line_1 -> set_dot_size( 4 );
    
    $y = new y_axis();
    $y -> set_range(0, $max + 1);
    
    $x = new x_axis();
    $x -> set_labels_from_array($labels);
        
    $chart = new open_flash_chart();
    $chart -> set_title( $title );
    $chart -> add_element( $line_1 );
    $chart -> set_y_axis( $y );
    $chart -> set_x_axis( $x );
    echo $chart -> toPrettyString();   
}

function createLoginAccessChart($from, $to){
    $result = eF_getTableData("logs", "*", "timestamp between $from and $to and action = 'login' order by timestamp");

    $labels = array();
    $count = array();
    //Assign each day of the week an empty slot
    for ($i = $from; $i <= $to; $i = $i + 86400) {
        $labels[] = date('Y/m/d', $i);
        $count[]  = 0;
    }
    
    $max = 0;
    //Assign the number of accesses to each week day
    foreach ($result as $value) {
        $cnt = 0;
        for ($i = $from; $i <= $to; $i = $i + 86400) {
            if ($i <= $value['timestamp'] && $value['timestamp'] < $i + 86400) {
                $count[$cnt]++;
                if ($count[$cnt] > $max){
                    $max = $count[$cnt];
                }
            }
            $cnt++;
        }
    }
    
    $title = new title(_LOGINS);
    
    $line_1 = new line_dot();
    $line_1 -> set_values($count);
    $line_1 -> set_halo_size( 0 );
    $line_1 -> set_width( 2 );
    $line_1 -> set_dot_size( 4 );
    
    $y = new y_axis();
    $y -> set_range(0, $max + 1);
    
    $x = new x_axis();
    $x -> set_labels_from_array($labels);
        
    $chart = new open_flash_chart();
    $chart -> set_title( $title );
    $chart -> add_element( $line_1 );
    $chart -> set_y_axis( $y );
    $chart -> set_x_axis( $x );
    echo $chart -> toPrettyString();
}

function createUserTypeChart(){
    $result    = eF_getTableDataFlat("users", "user_type, count(user_type) as num", "", "", "user_type");
    $userTypes = $result['user_type'];
    $count     = $result['num'];
    $title     = new title(_USERTYPES);
    $hbar      = new hbar( '#86BBEF' );
    $max       = 0;
    for ($i = sizeof($count) - 1; $i >=0 ; $i--) {
        $hbar->append_value( new hbar_value(0, $count[$i]));
        if ($count[$i] > $max) {
            $max = $count[$i];
        }
    }    
    $chart = new open_flash_chart();
    $chart -> set_title( $title );
    $chart -> add_element( $hbar );

    $x = new x_axis();
    $x -> set_offset( false );
    $x -> set_range(0, $max + 1);
    $chart -> set_x_axis( $x );
    
    $y = new y_axis();
    $y -> set_offset( true );
    $y -> set_labels($userTypes);
    $chart -> add_y_axis( $y );
    
    echo $chart -> toPrettyString();
}



function createMostActiveUsersChart($logins, $seconds) {
    $logins  = explode(",", $logins);
    $seconds =  explode(",", $seconds);
    foreach ($seconds as $value) {
        $minutes[] = ceil($value / 60);
    }
    $title = new title(_MOSTACTIVEUSERS);
    $hbar  = new hbar( '#86BBEF' );
    $max   = 0;
    
    for ($i = sizeof($minutes) - 1; $i >= 0; $i--){
        $hbar->append_value( new hbar_value(0, $minutes[$i]) );
        if ($minutes[$i] > $max){
            $max = $minutes[$i];
        }
    }    
    $chart = new open_flash_chart();
    $chart -> set_title( $title );
    $chart -> add_element( $hbar );

    $x = new x_axis();
    $x -> set_offset(false);
    $x -> set_range(0, $max);
    $x -> set_steps(ceil($max/5));
    $chart -> set_x_axis( $x );
    
    $y = new y_axis();
    $y -> set_offset( true );
    $y -> set_labels($logins);
    $chart -> add_y_axis( $y );
    
    echo $chart -> toPrettyString();
}



?>