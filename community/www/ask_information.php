<?php
session_cache_limiter('none');
session_start();

$path = "../libraries/";

include_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

try {
 $languages = EfrontSystem::getLanguages(true);

 if (isset($_GET['lessons_ID']) && eF_checkParameter($_GET['lessons_ID'], 'id')) {
  $lesson = new EfrontLesson($_GET['lessons_ID']);
  $lessonInformation = $lesson -> getInformation();


  //$lessonInformation['language'] = $languages[$lesson -> lesson['languages_NAME']];
  if ($lessonInformation['professors']) {
   foreach ($lessonInformation['professors'] as $value) {
    $professorsString[] = formatLogin($value['login']);
   }
   $lessonInformation['professors'] = implode(", ", $professorsString);
  }
  $lesson -> lesson['price'] ? $priceString = formatPrice($lesson -> lesson['price'], array($lesson -> options['recurring'], $lesson -> options['recurring_duration']), true) : $priceString = false;
  $lessonInformation['price_string'] = $priceString;
  //    if (!$lessonInformation['price']) {
  //        unset($lessonInformation['price_string']);
  //    }

  try {
   if ($_GET['from_course'] && eF_checkParameter($_GET['from_course'], 'id')) {
    $course = new EfrontCourse($_GET['from_course']);
    $schedule = $course -> getLessonScheduleInCourse($lesson);
    $lessonInformation['from_timestamp'] = $schedule['start_date'];
    $lessonInformation['to_timestamp'] = $schedule['end_date'];
   }
  } catch (Exception $e) {};

  if ($lesson -> lesson['course_only']) {
   $lessonCourses = $lesson -> getCourses();
   if (!empty($lessonCourses)) {
    foreach ($lessonCourses as $value) {
     $lessonInformation['lesson_courses'][] = $value['name'];
    }
    $lessonInformation['lesson_courses'] = implode(", ", $lessonInformation['lesson_courses']);
   }
  }

  foreach ($lessonInformation as $key => $value) {
   if ($value) {
    $value = str_replace ("\n","<br />", $value);
    switch ($key) {
     case 'language' : $GLOBALS['configuration']['onelanguage'] OR $tooltipInfo[] = '<div class = "infoEntry"><span>'._LANGUAGE."</span><span>: $languages[$value]</span></div>"; break;
     case 'professors' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._PROFESSORS."</span><span>: $value</span></div>"; break;
     case 'content' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._CONTENTUNITS."</span><span>: $value</span></div>"; break;
     case 'tests' : $GLOBALS['configuration']['disable_tests'] != 1 ? $tooltipInfo[] = '<div class = "infoEntry"><span>'._TESTS."</span><span>: $value</span></div>" : null; break;
     case 'projects' : $GLOBALS['configuration']['disable_projects'] != 1 ? $tooltipInfo[] = '<div class = "infoEntry"><span>'._PROJECTS."</span><span>: $value</span></div>" : null; break;
     case 'course_dependency' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._DEPENDSON."</span><span>: $value</span></div>"; break;
     case 'from_timestamp' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._AVAILABLEFROM."</span><span>: ".formatTimestamp($value, 'time_nosec')."</span></div>";break;
     case 'to_timestamp' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._AVAILABLEUNTIL."</span><span>: ".formatTimestamp($value, 'time_nosec')."</span></div>"; break;
     case 'general_description': $tooltipInfo[] = '<div class = "infoEntry"><span>'._DESCRIPTION."</span><span>: $value</span></div>"; break;
     case 'assessment' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._ASSESSMENT."</span><span>: $value</span></div>"; break;
     case 'objectives' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._OBJECTIVES."</span><span>: $value</span></div>"; break;
     case 'lesson_topics' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._LESSONTOPICS."</span><span>: $value</span></div>"; break;
     case 'resources' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._RESOURCES."</span><span>: $value</span></div>"; break;
     case 'other_info' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._OTHERINFO."</span><span>: $value</span></div>"; break;
     case 'price_string' : !$lesson -> lesson['course_only'] ? $tooltipInfo[] = '<div class = "infoEntry"><span>'._PRICE."</span><span>: $value</span></div>" : null; break;
     case 'lesson_courses' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._PARTOFCOURSES."</span><span>: $value</span></div>"; break;
     default: break;
    }
   }
  }
  if ($string = implode("", $tooltipInfo)) {
   echo '<html '.($GLOBALS['rtl'] ? 'dir = "rtl"' : '').' >'.$string.'</html>';
  } else {
   echo _NODATAFOUND;
  }
 }


 if (isset($_GET['courses_ID']) && eF_checkParameter($_GET['courses_ID'], 'id')) {
  $course = new EfrontCourse($_GET['courses_ID']);
  $courseInformation = $course -> getInformation();

  if ($courseInformation['professors']) {
   foreach ($courseInformation['professors'] as $value) {
    $professorsString[] = formatLogin($value['login']);
   }
   $courseInformation['professors'] = implode(", ", $professorsString);
  }

  $course -> course['price'] ? $priceString = formatPrice($course -> course['price'], array($course -> options['recurring'], $course -> options['recurring_duration']), true) : $priceString = false;
  $courseInformation['price_string'] = $priceString;
  foreach ($courseInformation as $key => $value) {
   if ($value) {
    $value = str_replace ("\n","<br />", $value);
    switch ($key) {
     case 'language' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._LANGUAGE."</span><span>: $languages[$value]</span></div>"; break;
     case 'professors' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._PROFESSORS."</span><span>: $value</span></div>"; break;
     case 'lessons_number' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._LESSONS."</span><span>: $value</span></div>"; break;
     case 'instances' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._COURSEINSTANCES."</span><span>: $value</span></div>"; break;
     case 'general_description': $tooltipInfo[] = '<div class = "infoEntry"><span>'._DESCRIPTION."</span><span>: $value</span></div>"; break;
     case 'assessment' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._ASSESSMENT."</span><span>: $value</span></div>"; break;
     case 'objectives' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._OBJECTIVES."</span><span>: $value</span></div>"; break;
     case 'lesson_topics' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._COURSETOPICS."</span><span>: $value</span></div>"; break;
     case 'resources' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._RESOURCES."</span><span>: $value</span></div>"; break;
     case 'other_info' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._OTHERINFO."</span><span>: $value</span></div>"; break;
     case 'price_string' : $tooltipInfo[] = '<div class = "infoEntry"><span>'._PRICE."</span><span>: $value</span></div>"; break;
     default: break;
    }
   }
  }

  if ($course -> course['depends_on']) {
   try {
    $dependsOn = new EfrontCourse($course -> course['depends_on']);
    $tooltipInfo[] = '<div class = "infoEntry"><span>'._DEPENDSON."</span><span>: ".$dependsOn->course['name']."</span></div>";
   } catch (Exception $e) {}
  }

  if ($string = implode("", $tooltipInfo)) {
   echo $string;
  } else {
   echo _NODATAFOUND;
  }

 }

 // For eFront social
 if (isset($_GET['common_lessons']) && isset($_GET['user1']) && isset($_GET['user2']) && eF_checkParameter($_GET['user1'], 'login') && eF_checkParameter($_GET['user2'], 'login')) {
  $user1 = EfrontUserFactory::factory($_GET['user1']);
  if ($user1->getType() != "administrator") {
   $common_lessons = $user1 -> getCommonLessons($_GET['user2']);
   // pr($common_lessons);
   foreach ($common_lessons as $id => $lesson) {
    if (strlen($lesson['name'])>25) {
     $lesson['name'] = substr($lesson['name'],0,22) . "...";
    }
    $tooltipInfo[] = '<div class = "infoEntry"><span>'.$lesson['name']."</span><span></span></div>";
   }

   if ($string = implode("", $tooltipInfo)) {
    echo $string;
   } else {
    echo _NODATAFOUND;
   }
  } else {
   echo _NODATAFOUND;
  }
 }
} catch (Exception $e) {
 echo ($e -> getMessage().' ('.$e -> getCode().')'); //No ajax error handling here, since we want the info to appear in the popup
}
?>
