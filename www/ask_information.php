<?php
session_cache_limiter('none');
session_start();

$path = "../libraries/";

include_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if (isset($_GET['lessons_ID']) && eF_checkParameter($_GET['lessons_ID'], 'id')) {
    $lesson            = new EfrontLesson($_GET['lessons_ID']);
    $lessonInformation = ($lesson -> getInformation());
    
    if ($lessonInformation['professors']) {
        foreach ($lessonInformation['professors'] as $value) {
            $professorsString[] = $value['name'].' '.$value['surname'];
        }
        $lessonInformation['professors'] = implode(", ", $professorsString);
    }
    if (!$lessonInformation['price']) {
        unset($lessonInformation['price_string']);
    }
    foreach ($lessonInformation as $key => $value) {
        if ($value) {
            switch ($key) {
                case 'professors'         : $tooltipInfo[] = '<strong>'._PROFESSORS."</strong>: $value<br/>";         break;
                case 'content'            : $tooltipInfo[] = '<strong>'._CONTENTUNITS."</strong>: $value<br/>";       break;
                case 'tests'              : $tooltipInfo[] = '<strong>'._TESTS."</strong>: $value<br/>";              break;
                case 'projects'           : $tooltipInfo[] = '<strong>'._PROJECTS."</strong>: $value<br/>";           break;
                case 'course_dependency'  : $tooltipInfo[] = '<strong>'._DEPENDSON."</strong>: $value<br/>";          break;
                case 'from_timestamp'     : $tooltipInfo[] = '<strong>'._AVAILABLEFROM."</strong>: ".formatTimestamp($value, 'time_nosec')."<br/>";break;
                case 'to_timestamp'       : $tooltipInfo[] = '<strong>'._AVAILABLEUNTIL."</strong>: ".formatTimestamp($value, 'time_nosec')."<br/>"; break;
                case 'general_description': $tooltipInfo[] = '<strong>'._GENERALDESCRIPTION."</strong>: $value<br/>"; break;
                case 'assessment'         : $tooltipInfo[] = '<strong>'._ASSESSMENT."</strong>: $value<br/>";         break;
                case 'objectives'         : $tooltipInfo[] = '<strong>'._OBJECTIVES."</strong>: $value<br/>";         break;
                case 'lesson_topics'      : $tooltipInfo[] = '<strong>'._LESSONTOPICS."</strong>: $value<br/>";       break;
                case 'resources'          : $tooltipInfo[] = '<strong>'._RESOURCES."</strong>: $value<br/>";          break;
                case 'other_info'         : $tooltipInfo[] = '<strong>'._OTHERINFO."</strong>: $value<br/>";          break;
                case 'price_string'       : $tooltipInfo[] = '<strong>'._PRICE."</strong>: $value<br/>";              break;
                default: break;
            }
        }
    }
    if ($string = implode("", $tooltipInfo)) {
        echo $string;
    } else {
        echo _NODATAFOUND;
    }
} if (isset($_GET['courses_ID']) && eF_checkParameter($_GET['courses_ID'], 'id')) {
    $course            = new EfrontCourse($_GET['courses_ID']);
    $courseInformation = ($course -> getInformation());

    if ($courseInformation['professors']) {
        foreach ($courseInformation['professors'] as $value) {
            $professorsString[] = $value['name'].' '.$value['surname'];
        }
        $courseInformation['professors'] = implode(", ", $professorsString);
    }
    
    foreach ($courseInformation as $key => $value) {
        if ($value) {
            switch ($key) {
                case 'professors'         : $tooltipInfo[] = '<strong>'._PROFESSORS."</strong>: $value<br/>";         break;
                case 'lessons_number'     : $tooltipInfo[] = '<strong>'._LESSONS."</strong>: $value<br/>";            break;
                case 'general_description': $tooltipInfo[] = '<strong>'._GENERALDESCRIPTION."</strong>: $value<br/>"; break;
                case 'assessment'         : $tooltipInfo[] = '<strong>'._ASSESSMENT."</strong>: $value<br/>";         break;
                case 'objectives'         : $tooltipInfo[] = '<strong>'._OBJECTIVES."</strong>: $value<br/>";         break;
                case 'lesson_topics'      : $tooltipInfo[] = '<strong>'._LESSONTOPICS."</strong>: $value<br/>";       break;
                case 'resources'          : $tooltipInfo[] = '<strong>'._RESOURCES."</strong>: $value<br/>";          break;
                case 'other_info'         : $tooltipInfo[] = '<strong>'._OTHERINFO."</strong>: $value<br/>";          break;
                default: break;
            }
        }
    }

    if ($string = implode("", $tooltipInfo)) {
        echo $string;
    } else {
        echo _NODATAFOUND;
    }
    
}

?>