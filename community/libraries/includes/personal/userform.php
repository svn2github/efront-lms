<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
session_write_close();







if ($editedUser -> user['user_type'] != 'administrator') {
 $directionsTree = new EfrontDirectionsTree();
 $directionsArray = $directionsTree -> getFlatTree();
 $smarty -> assign("T_DIRECTIONS_TREE", $directionsPathStrings = $directionsTree -> toPathString());

 $studentRoles = EfrontLessonUser :: getRoles();
 foreach ($studentRoles as $key => $value) {
  if ($value != 'student') {
   unset($studentRoles[$key]);
  }
 }

 $constraints = array('archive' => false);
 if ($_COOKIE['setUserFormSelectedSort']) {
  preg_match("/\d_(\w+)--(\w+)/", $_COOKIE['setUserFormSelectedSort'], $matches);
  in_array($matches[1], array('name', 'directions_ID', 'active_in_course', 'completed', 'score')) ? $constraints['sort'] = $matches[1] : $constraints['sort'] = 'name';
  $matches[2] == 'desc' ? $constraints['order'] = 'asc' : $constraints['order'] = 'desc';
 }
 $userCourses = $editedUser -> getUserCourses($constraints);
 foreach ($userCourses as $key => $value) {
  if (!in_array($value -> course['user_type'], $studentRoles)) {
   unset($userCourses[$key]);
  }
 }

 $constraints = array('archive' => false, 'active' => true, 'return_objects' => false);
 foreach ($userCourses as $key => $course) {
  $courseLessons[$key] = $course -> getCourseLessons($constraints);
  $userCourses[$key] = $course -> course; //strip object, we don't need it
  $coursesScores[] = $course -> course['score'];
 }

 $smarty -> assign("T_USER_COURSES", $userCourses);

 $userLessons = $editedUser -> getUserStatusInLessons();
 foreach ($userLessons as $key => $value) {
  if (!in_array($value -> lesson['user_type'], $studentRoles)) {
   unset($userLessons[$key]);
  }
 }

 $result = EfrontStats :: getStudentsDoneTests($userLessons, $editedUser -> user['login']);
 foreach ($result[$editedUser -> user['login']] as $value) {
  $userDoneTests[$value['lessons_ID']][] = $value;
 }

 $smarty -> assign("T_USER_TESTS", $userDoneTests);
 foreach ($userLessons as $key => $lesson) {
  if ($lesson -> lesson['course_only']) {
   foreach($courseLessons as $courseId => $foo) {
    if (isset($courseLessons[$courseId][$key])) {
     $courseLessons[$courseId][$key] = $lesson -> lesson;
    } elseif ($foo -> lesson) {
     $courseLessons[$courseId][$key] = $foo -> lesson;
    }
   }
   unset($userLessons[$key]); //Remove course lesson from lessons list
  } else {
   $lessonsScores[] = $lesson -> lesson['score'];
   $userLessons[$key] = $lesson -> lesson; //strip object, we don't need it
  }
 }


 $smarty -> assign("T_USER_LESSONS", $userLessons);
 $smarty -> assign("T_COURSE_LESSONS", $courseLessons);

 if (sizeof($userCourses) > 0) {
  $averages['courses'] = formatScore(round(array_sum($coursesScores) / sizeof($coursesScores), 2));
 }
 if (sizeof($userLessons) > 0) {
  $averages['lessons'] = formatScore(round(array_sum($lessonsScores) / sizeof($lessonsScores), 2));
 }
 $smarty -> assign("T_AVERAGES", $averages);
}
$smarty -> assign("T_EMPLOYEE_FORM_CAPTION", _USERFORM.": " . formatLogin($editedUser -> user['login']));




if (isset($_GET['pdf']) && $currentUser -> user['login'] != $editedUser -> user['login']) {
/*
	$pdf = new EfrontPdf(_EMPLOYEEFORM . ": " . formatLogin($editedUser -> user['login']));
	try {
		$avatarFile = new EfrontFile($infoUser -> user['avatar']);
	} catch(Exception $e) {
		$avatarFile = new EfrontFile(G_SYSTEMAVATARSPATH."unknown_small.png");
	}
	$info = array(_NAME		=> formatLogin($editedUser -> user['login']),
				  _BIRTHDAY	=> formatTimestamp($editedEmployee -> employee['birthday']),
				  _ADDRESS	=> $editedEmployee -> employee['address'],
				  _CITY		=> $editedEmployee -> employee['city'],
				  _HIREDON  => formatTimestamp($editedEmployee -> employee['hired_on']),
				  _LEFTON   => formatTimestamp($editedEmployee -> employee['left_on']));
	$pdf -> printInformationSection(_GENERALUSERINFO, $info, $avatarFile);


	$pdf -> OutputPdf('user_form_'.$editedUser -> user['login'].'.pdf');
	exit;
*/
 $heightForHeaders = 10;
 $headerFont = 10;
 $smallHeaderFont = 8;
 $contentFont = 6;
 $defaultFont = 'dejavusans';

 $logoFile = EfrontSystem::getSystemLogo();
/*
	try {
		list($halfPageWidth, $height) = getimagesize($logoFile['path']);
	} catch (Exception $e) {
		list($halfPageWidth, $height) = array(200, 150);
	}
*/
 $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 $pdf->setFontSubsetting(false);
 $pdf->SetCreator(PDF_CREATOR);

 $pdf->setPrintHeader(false);
 $pdf->setPrintFooter(false);

 $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
 $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
 $pdf->setFontSubsetting(false);

 $pdf->SetFont($defaultFont, 'B', $headerFont, '', true);
 $pdf->SetAuthor(formatLogin($currentUser -> user['login']));
 $pdf->SetTitle(_EMPLOYEEFORM . ": " . formatLogin($editedUser -> user['login']));
 $pdf->SetSubject(_EMPLOYEEFORM);
 $pdf->SetKeywords('pdf, '._EMPLOYEEFORM);
 $pdf->AddPage();

 $pdf->Image($logoFile['path'], '', '', 0, 0, '', '', 'T');

 $pdf->SetFont($defaultFont, 'B', 16, '', true);
 $pdf->Cell(0, 0, $GLOBALS['configuration']['site_name'], 0, 2);
 $pdf->SetFont($defaultFont, '', $headerFont, '', true);
 $pdf->Cell(0, 0, $GLOBALS['configuration']['site_motto'], 0, 2);

 $pdf->Ln(12); //separator
 $pdf->SetFont($defaultFont, 'B', $headerFont, '', true);
 $pdf->SetFillColor(220, 220, 220);
 $pdf->Cell(0, $heightForHeaders, _EMPLOYEEFORM.": ".formatLogin($editedUser -> user['login']), 'BT', 1, 'C', true);
 $pdf->SetFillColor(240, 240, 240);
 try {
  $avatarFile = new EfrontFile($editedUser -> user['avatar']);
 } catch(Exception $e) {
  $avatarFile = new EfrontFile(G_SYSTEMAVATARSPATH."unknown_small.png");
 }

 $pdf->Ln(6); //separator
 $pdf->SetFont($defaultFont, 'B', $headerFont, '', true);
 $pdf->Cell(0, $heightForHeaders, _GENERALUSERINFO, 0, 1);

 $pdf->SetFont($defaultFont, '', $contentFont, '', true);
 $pdf->Image($avatarFile['path'], '', '', 0, 0, '', '', 'T');
 $pdf->Cell(0, 0, _NAME.": ".formatLogin($editedUser -> user['login']), 0, 2);
 !$editedEmployee -> employee['birthday'] OR $pdf->Cell(0, 0, _BIRTHDAY.": ".formatTimestamp($editedEmployee -> employee['birthday']), 0, 2);
 !$editedEmployee -> employee['address'] OR $pdf->Cell(0, 0, _ADDRESS.": ".$editedEmployee -> employee['address'], 0, 2);
 !$editedEmployee -> employee['city'] OR $pdf->Cell(0, 0, _CITY.": ".$editedEmployee -> employee['city'], 0, 2);
 !$editedEmployee -> employee['hired_on'] OR $pdf->Cell(0, 0, _HIREDON.": ".formatTimestamp($editedEmployee -> employee['hired_on']), 0, 2);
 !$editedEmployee -> employee['left_on'] OR $pdf->Cell(0, 0, _LEFTON.": ".formatTimestamp($editedEmployee -> employee['left_on']), 0, 1);
 $pdf->Ln(10); //separator

 $halfPageWidth = round($pdf -> GetPageWidth()/2)-5;
 if (isset($jobs) && !empty($jobs)) {
  $pdf->Ln(6); //separator
  $pdf->SetFont($defaultFont, 'B', $headerFont, '', true);
  $pdf->Cell(0, $heightForHeaders, _PLACEMENTS, 0, 1);
  $pdf->SetFont($defaultFont, '', $contentFont, '', true);
  foreach ($jobs as $value) {
   $column1 = $value['name'].': ';
   $column2 = strip_tags($value['description']).(!$value['supervisor'] OR _SUPERVISOR);
   $pdf->MultiCell(min($halfPageWidth, $pdf->GetStringWidth($column1)+5), 0, $column1, 0, 'L', 0, 0);
   $pdf->MultiCell(min($halfPageWidth, $pdf->GetStringWidth($column2)+5), 0, $column2, 0, 'L', 0, 1);
  }
 }

 if (isset($evaluations) && !empty($evaluations)) {
  $pdf->Ln(6); //separator
  $pdf->SetFont($defaultFont, 'B', $headerFont, '', true);
  $pdf->Cell(0, $heightForHeaders, _EVALUATIONS, 0, 1);
  $pdf->SetFont($defaultFont, '', $contentFont, '', true);
  foreach ($evaluations as $value) {
   $column1 = formatLogin($value['author']).' '.formatTimestamp($value['timestamp']).': ';
   $column2 = strip_tags($value['specification']);
   $pdf->MultiCell(min($halfPageWidth, $pdf->GetStringWidth($column1)+5), 0, $column1, 0, 'L', 0, 0);
   $pdf->MultiCell(min($halfPageWidth, $pdf->GetStringWidth($column2)+5), 0, $column2, 0, 'L', 0, 1);
  }
 }

 if (isset($skills) && !empty($skills)) {
  $pdf->Ln(6); //separator
  $pdf->SetFont($defaultFont, 'B', $headerFont, '', true);
  $pdf->Cell(0, $heightForHeaders, _SKILLS, 0, 1);
  $pdf->SetFont($defaultFont, '', $contentFont, '', true);
  foreach ($skills as $value) {
   $column1 = strip_tags($value['description']).': ';
   $column2 = strip_tags($value['specification']);
   $pdf->MultiCell(min($halfPageWidth, $pdf->GetStringWidth($column1)+5), 0, $column1, 0, 'L', 0, 0);
   $pdf->MultiCell(min($halfPageWidth, $pdf->GetStringWidth($column2)+5), 0, $column2, 0, 'L', 0, 1);
  }
 }

 if ($editedUser -> user['user_type'] != 'administrator' && (!empty($userCourses) || !empty($userLessons))) {
  $pdf->Ln(6); //separator
  $pdf->SetFont($defaultFont, 'B', $headerFont, '', true);
  $pdf->Cell(0, $heightForHeaders, _TRAINING, 0, 1);

  $pageWidth = $pdf -> GetPageWidth();
  $columnWidth1 = ($pageWidth-20)*0.40;
  $columnWidth2 = ($pageWidth-20)*0.25;
  $columnWidth3 = ($pageWidth-20)*0.13;
  $columnWidth4 = ($pageWidth-20)*0.13;
  $columnWidth5 = ($pageWidth-20)*0.09;

  $columnWidth6 = ($pageWidth-20)*0.78;
  //$columnWidth7 = ($pageWidth-20)*0.38;
  $columnWidth8 = ($pageWidth-20)*0.13;
  $columnWidth9 = ($pageWidth-20)*0.09;

  $columnWidth10 = ($pageWidth-20)*0.78;
  $columnWidth11 = ($pageWidth-20)*0.13;
  $columnWidth12 = ($pageWidth-20)*0.09;

  if (sizeof($userCourses) > 0) {
   $pdf->SetFont($defaultFont, 'B', $smallHeaderFont, '', true);
   $pdf->MultiCell(0, 0, _COURSES, 0, 'L', 0, 1);

   $pdf->SetFont($defaultFont, 'B', $contentFont, '', true);
   $pdf->MultiCell($columnWidth1, 0, _NAME, 0, 'L', 0, 0);
   $pdf->MultiCell($columnWidth2, 0, _CATEGORY, 0, 'L', 0, 0);
   $pdf->MultiCell($columnWidth3, 0, _REGISTRATIONDATE, 0, 'C', 0, 0);
   $pdf->MultiCell($columnWidth4, 0, _COMPLETED, 0, 'C', 0, 0);
   $pdf->MultiCell($columnWidth5, 0, _SCORE, 0, 'R', 0, 1);

   $pdf->SetFont($defaultFont, '', $contentFont, '', true);
   $count = 0;

   foreach ($userCourses as $value) {
    $printedLessons ? $border = 'T' : $border = ''; //Used to draw a bottom border where applicable
    $printedLessons = false;
    if ($value['active']) {
     ($count++%2) ? $pdf -> setTextColor(18,52,86) : $pdf -> setTextColor(101,67,33);
    } else {
     $pdf -> setTextColor(255,0,0);
    }
    $pdf->MultiCell($columnWidth1, 0, $value['name'], $border, 'L', 0, 0);
    $pdf->MultiCell($columnWidth2, 0, str_replace("&nbsp;&rarr;&nbsp;", " -> ", $directionsPathStrings[$value['directions_ID']]), $border, 'L', 0, 0);
    $pdf->MultiCell($columnWidth3, 0, formatTimestamp($value['active_in_course']), $border, 'C', 0, 0);
    $pdf->MultiCell($columnWidth4, 0, $value['to_timestamp']? formatTimestamp($value['to_timestamp']) : '-', $border, 'C', 0, 0);
    $pdf->MultiCell($columnWidth5, 0, formatScore($value['score']).'%', $border, 'R', 0, 1);

    if (isset($courseLessons[$value['id']]) && !empty($courseLessons[$value['id']])) {
     $pdf->setTextColor(0,0,0);
     $pdf->SetFont($defaultFont, 'B', $contentFont, '', true);
     $pdf->MultiCell(0, 0, _LESSONSFORCOURSE.' '.$value['name'], 'T', 'L', true, 1);

     $pdf->SetFont($defaultFont, 'B', $contentFont, '', true);
     $pdf->MultiCell($columnWidth6, 0, _NAME, 0, 'L', true, 0);
     //$pdf->MultiCell($columnWidth7, 0, _CATEGORY, 0, 'L', true, 0);
     $pdf->MultiCell($columnWidth8, 0, _COMPLETED, 0, 'C', true, 0);
     $pdf->MultiCell($columnWidth9, 0, _SCORE, 0, 'R', true, 1);

     $pdf->SetFont($defaultFont, '', $contentFont, '', true);

     foreach ($courseLessons[$value['id']] as $courseLesson) {
      ($count++%2) ? $pdf -> setTextColor(18,52,86) : $pdf -> setTextColor(101,67,33);

      $pdf->MultiCell($columnWidth6, 0, $courseLesson['name'], 0, 'L', true, 0);
      //$pdf->MultiCell($columnWidth7, 0, str_replace("&nbsp;&rarr;&nbsp;", " -> ", $directionsPathStrings[$courseLesson['directions_ID']]), 0, 'L', true, 0);
      $pdf->MultiCell($columnWidth8, 0, $courseLesson['timestamp_completed'] ? formatTimestamp($courseLesson['timestamp_completed']) : '-', 0, 'C', true, 0);
      $pdf->MultiCell($columnWidth9, 0, formatScore($value['score']).'%', 0, 'R', true, 1);
      $printedLessons = true;
/*
						if (isset($userDoneTests[$courseLesson['id']])) {
							$pdf->SetFillColor(220, 220, 220);
							$pdf->setTextColor(0,0,0);
							$pdf->SetFont($defaultFont, 'B', $contentFont, '', true);
							$pdf->MultiCell(0, 0, _TESTSFORLESSON.' '.$courseLesson['name'], 0, 'L', true, 1);

							$pdf->SetFont($defaultFont, 'B', $contentFont, '', true);
							$pdf->MultiCell($columnWidth10, 0, _TESTNAME, 0, 'L', true, 0);
							$pdf->MultiCell($columnWidth11, 0, _STATUS, 0, 'C', true, 0);
							$pdf->MultiCell($columnWidth12, 0, _SCORE, 0, 'R', true, 1);

							$pdf->SetFont($defaultFont, '', $contentFont, '', true);
							foreach ($userDoneTests[$courseLesson['id']] as $test) {
								($count++%2) ? $pdf -> setTextColor(18,52,86) : $pdf -> setTextColor(101,67,33);
								$pdf->MultiCell($columnWidth10, 0, $test['name'], 0, 'L', true, 0);
								$pdf->MultiCell($columnWidth11, 0, $test['status'], 0, 'C', true, 0);
								$pdf->MultiCell($columnWidth12, 0, formatScore($test['score']).'%', 0, 'R', true, 1);
							}
							$pdf->SetFillColor(240, 240, 240);
						}
*/

     }
    }

   }

  }


  if (sizeof($userLessons) > 0) {
   $pdf -> setTextColor(0,0,0);
   $pdf->Ln(6); //separator
   $pdf->SetFont($defaultFont, 'B', $smallHeaderFont, '', true);
   $pdf->MultiCell(0, 0, _LESSONS, 0, 'L', 0, 1);

   $pdf->SetFont($defaultFont, 'B', $contentFont, '', true);
   $pdf->MultiCell($columnWidth1, 0, _NAME, 0, 'L', 0, 0);
   $pdf->MultiCell($columnWidth2, 0, _CATEGORY, 0, 'L', 0, 0);
   $pdf->MultiCell($columnWidth3, 0, _REGISTRATIONDATE, 0, 'C', 0, 0);
   $pdf->MultiCell($columnWidth4, 0, _COMPLETED, 0, 'C', 0, 0);
   $pdf->MultiCell($columnWidth5, 0, _SCORE, 0, 'R', 0, 1);

   $pdf->SetFont($defaultFont, '', $contentFont, '', true);
   foreach ($userLessons as $value) {
    if ($value['active']) {
     ($count++%2) ? $pdf -> setTextColor(18,52,86) : $pdf -> setTextColor(101,67,33);
    } else {
     $pdf -> setTextColor(255,0,0);
    }
    $pdf->MultiCell($columnWidth1, 0, $value['name'], 0, 'L', 0, 0);
    $pdf->MultiCell($columnWidth2, 0, str_replace("&nbsp;&rarr;&nbsp;", " -> ", $directionsPathStrings[$value['directions_ID']]), 0, 'L', 0, 0);
    $pdf->MultiCell($columnWidth3, 0, formatTimestamp($value['active_in_lesson']), 0, 'C', 0, 0);
    $pdf->MultiCell($columnWidth4, 0, $value['timestamp_completed'] ? formatTimestamp($value['timestamp_completed']) : '-', 0, 'C', 0, 0);
    $pdf->MultiCell($columnWidth5, 0, formatScore($value['score']).'%', 0, 'R', 0, 1);
/*
				if (isset($userDoneTests[$value['id']])) {
					$pdf -> setTextColor(0,0,0);
					$pdf->SetFont($defaultFont, 'B', $contentFont, '', true);
					$pdf->MultiCell(0, 0, _TESTSFORLESSON.' '.$value['name'], 0, 'L', true, 1);

					$pdf->SetFont($defaultFont, 'B', $contentFont, '', true);
					$pdf->MultiCell($columnWidth10, 0, _TESTNAME, 0, 'L', true, 0);
					$pdf->MultiCell($columnWidth11, 0, _STATUS, 0, 'L', true, 0);
					$pdf->MultiCell($columnWidth12, 0, _SCORE, 0, 'L', true, 1);

					$pdf->SetFont($defaultFont, '', $contentFont, '', true);
					foreach ($userDoneTests[$value['id']] as $test) {
						($count++%2) ? $pdf -> setTextColor(18,52,86) : $pdf -> setTextColor(101,67,33);
						$pdf->MultiCell($columnWidth10, 0, $test['name'], 0, 'L', true, 0);
						$pdf->MultiCell($columnWidth11, 0, $test['status'], 0, 'L', true, 0);
						$pdf->MultiCell($columnWidth12, 0, formatScore($test['score']).'%', 0, 'L', true, 1);
					}
				}
*/
   }
  }
  $pdf -> setTextColor(0,0,0);

  $pdf->Ln(6); //separator
  $pdf->SetFont($defaultFont, 'B', $smallHeaderFont, '', true);
  $pdf->MultiCell(0, 0, _OVERALL, 0, 'L', 0, 1);

  $pdf->SetFont($defaultFont, '', $contentFont, '', true);
   $column1 = _COURSESAVERAGE.': ';
   $column2 = $averages['courses'].'%';
   $column3 = _LESSONSAVERAGE.': ';
   $column4 = $averages['lessons'].'%';
   $maxStringWidth = max($pdf->GetStringWidth($column1),
          $pdf->GetStringWidth($column2),
          $pdf->GetStringWidth($column3),
          $pdf->GetStringWidth($column4)) + 3;
   if ($averages['courses']) {
    $pdf->MultiCell(min($halfPageWidth, $maxStringWidth), 0, $column1, 0, 'L', 0, 0);
    $pdf->MultiCell(min($halfPageWidth, $maxStringWidth), 0, $column2, 0, 'L', 0, 1);
   }
   if ($averages['courses']) {
    $pdf->MultiCell(min($halfPageWidth, $maxStringWidth), 0, $column3, 0, 'L', 0, 0);
    $pdf->MultiCell(min($halfPageWidth, $maxStringWidth), 0, $column4, 0, 'L', 0, 1);
   }


 }

 $pdf->Output('user_form_'.$editedUser -> user['login'].'.pdf', 'D');
 exit;
}
