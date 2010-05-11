<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

try {
    /* Check permissions: manage only job descriptions of the branches you own */
 if (isset($_GET['edit_job_description'])) {
        $currentJob = new EfrontJob($_GET['edit_job_description']);

    }

    if ($currentUser -> getType() != "administrator" && !($currentEmployee -> getType() == _SUPERVISOR && ((isset($_GET['add_job_description']) || (isset($currentJob) && in_array($currentJob -> job['branch_ID'], $currentEmployee -> supervisesBranches)) || (!isset($currentJob) && !isset($_GET['add_job_description'])))))) {
        $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        eF_redirect("".$_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=".$message."&message_type=".$message_type);
        exit;
    }
    if (isset($_GET['postAjaxRequest'])) {
        try {
            if (isset($_GET['skill'])) {
                if ($_GET['insert'] == 'true') {
                    $currentJob -> assignSkill($_GET['add_skillID'], $_GET['apply_to_all_jd']);
                } else if ($_GET['insert'] == 'false') {
                    $currentJob -> removeSkill($_GET['add_skillID'], $_GET['apply_to_all_jd']);
                } else if (isset($_GET['addAll'] )) {
                    $skills = $currentJob -> getSkills();
                    isset($_GET['filter']) ? $skills = eF_filterData($skills,$_GET['filter']) : null;
                    foreach ($skills as $skill) {
                        if ($skill['job_description_ID'] == "") {
                            $currentJob -> assignSkill($skill['skill_ID'], $_GET['apply_to_all_jd']);
                        }
                    }
                } else if (isset($_GET['removeAll'] )) {
                    $skills = $currentJob -> getSkills();
                    isset($_GET['filter']) ? $skills = eF_filterData($skills,$_GET['filter']) : null;
                    foreach ($skills as $skill) {
                        if ($skill['job_description_ID'] != "") {
                            $currentJob -> removeSkill($skill['skill_ID'], $_GET['apply_to_all_jd']);
                        }
                    }
                }
            } else if (isset($_GET['lesson'])) {
                if ($_GET['insert'] == 'true') {
                    $currentJob -> associateLesson($_GET['add_lessonID'], $_GET['apply_to_all_jd']);
                } else if ($_GET['insert'] == 'false') {
                    $currentJob -> removeLesson($_GET['add_lessonID'], $_GET['apply_to_all_jd']);
                } else if (isset($_GET['addAll'] )) {
                    $lessons = $currentJob -> getLessons();
                    isset($_GET['filter']) ? $lessons = eF_filterData($lessons,$_GET['filter']) : null;

                    foreach ($lessons as $lesson) {
                        if ($lesson['job_description_ID'] == "") {
                            $currentJob -> associateLesson($lesson['id'], $_GET['apply_to_all_jd']);
                        }
                    }
                } else if (isset($_GET['removeAll'] )) {
                    $lessons = $currentJob -> getLessons();
                    isset($_GET['filter']) ? $lessons = eF_filterData($lessons,$_GET['filter']) : null;

                    foreach ($lessons as $lesson) {
                        if ($lesson['job_description_ID'] != "") {
                            $currentJob -> removeLesson($lesson['id'], $_GET['apply_to_all_jd']);
                        }
                    }
                }
            } else if (isset($_GET['course'])) {
                if ($_GET['insert'] == 'true') {
                    $currentJob -> associateCourse($_GET['add_courseID'], $_GET['apply_to_all_jd']);
                } else if ($_GET['insert'] == 'false') {
                    $currentJob -> removeCourse($_GET['add_courseID'], $_GET['apply_to_all_jd']);
                } else if (isset($_GET['addAll'] )) {
                    $courses = $currentJob -> getCourses();
                    isset($_GET['filter']) ? $courses = eF_filterData($courses,$_GET['filter']) : null;

                    foreach ($courses as $course) {
                        if ($course['job_description_ID'] == "") {
                            $currentJob -> associateCourse($course['id'], $_GET['apply_to_all_jd']);
                        }
                    }
                } else if (isset($_GET['removeAll'] )) {
                    $courses = $currentJob -> getCourses();
                    isset($_GET['filter']) ? $courses = eF_filterData($courses,$_GET['filter']) : null;

                    foreach ($courses as $course) {
                        if ($course['job_description_ID'] != "") {
                            $currentJob -> removeCourse($course['id'], $_GET['apply_to_all_jd']);
                        }
                    }
                }
            } else if (isset($_GET['training'])) {
             $currentJob -> setRequiredTraining($_GET['training'], $_GET['apply_to_all']);
            }

        } catch (Exception $e) {
            handleAjaxExceptions($e);
        }
            exit;

    }


    if (isset($_GET['delete_job_description'])) {
  try {
      $currentJob = new EfrontJob($_GET['delete_job_description']);
         $currentJob -> delete();
     } catch (Exception $e) {
      handleAjaxExceptions($e);
     }
     exit;
    } else if (isset($_GET['remove_user_job'])) {
  try {
   $editedUser = EfrontUserFactory :: factory($_GET['user']);
   $editedEmployee = $editedUser -> aspects['hcd'];
         $editedEmployee = $editedEmployee -> removeJob($_GET['remove_user_job']);
     } catch (Exception $e) {
      handleAjaxExceptions($e);
     }
     exit;
    } else if (isset($_GET['export_vacancies_for_job_description'])) {

        //TODO: well, export vacancies...
        /*

        if ($ok = eF_insertTableData("module_hcd_vacancies", array("job_description_ID" => $_GET['export_vacancies_for_job_description'], "available_placements" => $_GET['available_placements']))) {

            $message      = _JOBDESCRIPTIONDELETED;

            $message_type = 'success';

        } else {

            $message      = _THISJOBDESCRIPTIONSELEMENTSCOULDNOTBEDELETED;

            $message_type = 'failure';

        }

        eF_redirect("".$_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=".$message."&message_type=".$message_type);

         */
    /*****************************************************

     ON INSERTING OR EDITING A JOB DESCRIPTION

     **************************************************** */
    } else if (isset($_GET['add_job_description']) || isset($_GET['edit_job_description'])) {
        if (isset($_GET['add_job_description'])) {
            $form = new HTML_QuickForm("job_description_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&add_job_description=1", "", null, true);
        } else {
            $form = new HTML_QuickForm("job_description_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&edit_job_description=" . $_GET['edit_job_description'] , "", null, true);
        }
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
        $form -> addElement('text', 'job_description_name', _JOBDESCRIPTION, 'class = "inputText"');
        $form -> addElement('textarea', 'job_role_description', _JOBANALYTICALDESCRIPTION, 'class = "inputText"');
        $form -> addRule('job_description_name', _THEFIELD.' '._JOBDESCRIPTION.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('job_description_name', _INVALIDFIELDDATA, 'checkParameter', 'job_description_name'); /*mandatory me if*/
        $form -> addElement('text', 'placements', _VACANCIES, 'class = "inputText"');
        /* Create the branches drop down menu - all for admin, branches you can manage for supervisor */
        $branches = $currentEmployee -> getSupervisedBranches();
        if (!empty($branches)) {
            if (isset($_GET['edit_job_description'])) {
                $only_existing = 3;
            }
            $form -> addElement('select', 'branch' , _BRANCHNAME, eF_createBranchesTreeSelect($branches, 3) , 'class = "inputText"  id="branch" onchange="javascript:change_branch(\'branch\',\'details_link\')"');
        } else {
            $message = _NOBRANCHESHAVEBEENREGISTERED;
            $message_type = 'failure';
            if (isset($_GET['edit_branch'])) {
                unset($_GET['edit_job_description']);
            } else {
                unset($_GET['add_job_description']);
            }
            eF_redirect($_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=". $message . "&message_type=failure");
            exit;
        }
        /* Get job description data */
        if (isset($_GET['edit_job_description'])) {
            $smarty -> assign("T_JOB_DESCRIPTION_BRANCH_NAME", $currentJob -> job['name']);
            $smarty -> assign("T_JOB_DESCRIPTION_NAME", $currentJob -> job['job_description']);
            $employees = $currentJob -> getEmployees(false, true);
            if(!empty($employees)) {
                $smarty -> assign("T_EMPLOYEES", $employees);
            }
            /* Create the html code for the "view branch details" lense icon on the right of the branches drop down */
            if ($currentJob -> job['branch_ID'] != 0) {
                $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $currentJob -> job['branch_ID']. "\"";
            } else {
                $details_link = "";
            }
            /* Administrators can associate lessons or courses to job descriptions - every employee with that job description will have the lessons */
            if ($currentUser -> getType() == "administrator" || $currentEmployee -> getType() == _SUPERVISOR) {
                $lessons = $currentJob -> getLessons();
                // Remove all course_only lessons
                foreach ($lessons as $lid => $lesson) {
                    if ($lesson['course_only']) {
                         unset($lessons[$lid]);
                    }
                }
                $courses = $currentJob -> getCourses();
                $skills = $currentJob -> getSkills();
                // Get with ajax
                if (isset($_GET['ajax'])) {
                    if ($_GET['tab'] == 'lessons') {
                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $lessons = eF_filterData($lessons, $_GET['filter']);
                        }

                        $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $lessons = array_slice($lessons, $offset, $limit);
                        }
                        $smarty -> assign("T_LESSONS_DATA", $lessons);

                        $smarty -> display($_SESSION['s_type'].'.tpl');
                        exit;
                    }

                 $smarty -> assign("T_DATASOURCE_SORT_BY", 5);
                 $smarty -> assign("T_DATASOURCE_SORT_ORDER", 'desc');
                 $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'location', 'directions_name', 'num_lessons', 'num_skills', 'has_course'));
                 if ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable') {
                  try {
                   if ($_GET['ajax'] == 'coursesTable') {
                    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => false);
                   }
                   if ($_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
                    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => $_GET['instancesTable_source']);
                   }
                   $courses = $currentJob -> getJobCoursesIncludingUnassigned($constraints);
                   $dataSource = EfrontCourse :: convertCourseObjectsToArrays($courses);
                   $tableName = $_GET['ajax'];
                   $alreadySorted = 1;
                   include("sorted_table.php");
                  } catch (Exception $e) {
                   handleAjaxExceptions($e);
                  }
                 }
/*                    

                    if ($_GET['tab'] == 'courses') {

                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        if (isset($_GET['sort'])) {

                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';

                            $courses = eF_multiSort($courses, $_GET['sort'], $order);

                        }

                        if (isset($_GET['filter'])) {

                            $courses = eF_filterData($courses, $_GET['filter']);

                        }



                        $smarty -> assign("T_COURSES_SIZE", sizeof($courses));

                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {

                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;

                            $courses = array_slice($courses, $offset, $limit);

                        }

                        $smarty -> assign("T_COURSES_DATA", $courses);



                        $smarty -> display($_SESSION['s_type'].'.tpl');

                        exit;

                    }

*/
                    if ($_GET['tab'] == 'skills') {
                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $skills = eF_multiSort($skills, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $skills = eF_filterData($skills, $_GET['filter']);
                        }
                        $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $skills = array_slice($skills, $offset, $limit);
                        }
                        $smarty -> assign("T_SKILLS", $skills);
                        $smarty -> display($_SESSION['s_type'].'.tpl');
                        exit;
                    }
                } else {
                    // Conventional get
                    $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
                    if (!empty($lessons)) {
                        $smarty -> assign("T_LESSONS_DATA", $lessons);
                    }
                    $smarty -> assign("T_COURSES_SIZE", sizeof($courses));
                    if (!empty($courses)) {
                        $smarty -> assign("T_COURSES_DATA", $courses);
                    }
                    $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
                    if (!empty($skills)) {
                        $smarty -> assign("T_SKILLS", $skills);
                    }
                }
                // Job prerequisites handling
                $allCourses = EfrontCourse::getAllCourses(array("return_objects" => false, "instance" => false, "sort" => "name"));
                if (!empty($allCourses)) {
                 $trainingCourses = array();
                 foreach ($allCourses as $course) {
                  $trainingCourses[$course['id']] = $course['name'];
                 }
                 $form -> addElement('select', 'prerequisites_row_col', null, $trainingCourses, 'id = "prerequisites_row_col" class = "inputSelect" onChange="updateSelectedValue(this);ajaxPostRequiredTraining();"');
                 //    $form -> addElement('select', 'search_skill_template' , null, $skills_list ,'id="search_skill_row" class = "inputSelectMed"  onchange="javascript:refreshResults();"');
                }
                //$training_courses = $currentJob -> getRequiredTraining();
                $training_condition = $currentJob -> job['required_training'];
                if ($training_condition) {
                 $training_condition = explode(";", $training_condition);
                 $or_spans_index = array();
                 $row = 1;
                 $training_index = array();
                 foreach ($training_condition as $andCondition) {
                  $orCondition = explode(",", $andCondition);
                  $column = 0;
                  $training_index[$row] = array();
                  foreach ($orCondition as $condition) {
                   $form -> addElement('select', 'prerequisites_'.$row.'_'. $column, null, $trainingCourses, 'id = "prerequisites_'.$row.'_'.$column.'" class = "inputSelect" onChange="updateSelectedValue(this);ajaxPostRequiredTraining();"');
                   $form -> setDefaults(array('prerequisites_'.$row.'_'. $column => $condition));
                   $training_index[$row][] = 'prerequisites_'.$row.'_'. $column;
                   if ($column) {
                    $or_spans_index['prerequisites_'.$row.'_'. $column] = 1;
                   }
                   $column++;
                  }
                  $row++;
                 }
                 if (!empty($training_index[1])) {
                  $smarty -> assign ("T_PREREQUISITES", $training_index);
                  $smarty -> assign ("T_OR_SPANS", $or_spans_index);
                 }
                }
            }
        } else {
            $details_link = "";
        }
        /* The details link has the html code for the "view branch details" lense icon on the right of the branches drop down */
        $smarty -> assign("T_BRANCH_INFO", $details_link);
        $form -> addElement('submit', 'submit_job_description_details', _SUBMIT, 'class = "flatButton"');
        /* Set default values */
        if (isset($_GET['edit_job_description'])) {
            $form -> setDefaults(array( 'job_description_name' => $currentJob -> job['description'],
                                        'placements' => $currentJob -> job['employees_needed'],
                                        'job_role_description' => $currentJob -> job['job_role_description']));
        }
        $smarty -> assign("T_BRANCH_ID", $currentJob -> job['branch_ID']);
        /* If add_branch request coming from another branch subbranches menu, pre-enter the fatherBranch form */
        if (isset($_GET['add_job_description']) && isset($_GET['add_to_branch'])) {
            $form -> setDefaults(array( 'branch' => $_GET['add_to_branch']));
            $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['add_to_branch'] . "\"";
            $smarty -> assign("T_BRANCH_INFO", $details_link);
        }
        /* Hidden for maintaining the previous_url value, so that you can immediately return after the insertion of a new job description */
        $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
        $previous_url = getenv('HTTP_REFERER');
        if ($position = strpos($previous_url, "&message")) {
            $previous_url = substr($previous_url, 0, $position);
        }
        $form -> setDefaults(array( 'previous_url' => $previous_url));
        /* Needed for title */
        $smarty -> assign("T_JOB_DESCRIPTION_NAME", $currentJob -> job['description']);
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $renderer -> setRequiredTemplate(
            '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
        /*****************************************************

         JOB_DESCRIPTION DATA SUBMISSION

         **************************************************** */
        if ($form -> isSubmitted()) {
            if ($form -> validate()) {
                $job_description_content = array('description' => $form->exportValue('job_description_name'),
                                                 'branch_ID' => $form->exportValue('branch'),
                                                 'job_role_description' => $form->exportValue('job_role_description'),
                                                 'employees_needed' => $form->exportValue('placements'));
                if (isset($_GET['add_job_description'])) {
                    /* Either insert the job description to all branches or only to a single one */
                    EfrontJob :: createJob($job_description_content);
                    $message = _SUCCESSFULLYCREATEDJOBDESCRIPTION;
                    $message_type = 'success';
                    eF_redirect("".$_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=". $message . "&message_type=success");
                } elseif (isset($_GET['edit_job_description'])) {
                    $currentJob -> updateJobData($job_description_content);
                    $message = _JOBDESCRIPTIONDATAUPDATED;
                    $message_type = 'success';
                }
                /* Instead of going back to the branches go the previous link */
                eF_redirect("".basename($form->exportValue('previous_url'))."&message=". urlencode($message) . "&message_type=" . $message_type . "&tab=jobs");
                exit;
            }
        }

  $renderer = prepareFormRenderer($form);
        $smarty -> assign('T_JOB_DESCRIPTIONS_FORM', $renderer -> toArray());
    } else {
        // Create ajax enabled table for job descriptions
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'jobsTable') {
         $job_descriptions = EfrontJob :: getAllJobs();
         $dataSource = $job_descriptions;
   $tableName = $_GET['ajax'];
   include("sorted_table.php");
        }
    }
} catch (EfrontJobException $e) {
    $message = $e -> getMessage().' ('.$e -> getCode().')';
    $message_type = 'failure';
    eF_redirect("".basename($form->exportValue('previous_url'))."&message=". urlencode($message) . "&message_type=" . $message_type . "&tab=jobs");
    exit;
}

?>
