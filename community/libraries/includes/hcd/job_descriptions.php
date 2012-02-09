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


    $_change_ = true;
    if ((isset($currentUser -> coreAccess['organization']) && $currentUser -> coreAccess['organization'] == 'view') || (!$currentEmployee->isSupervisor() && $currentUser -> getType() != "administrator")) {
     $_change_ = false;
    }
    $smarty -> assign("_change_", $_change_);

    if (isset($_GET['postAjaxRequest']) && $_change_) {
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
                    $currentJob -> associateLessonsToJob($_GET['add_lessonID'], $_GET['apply_to_all_jd']);
                } else if ($_GET['insert'] == 'false') {
                    $currentJob -> removeLessonsFromJob($_GET['add_lessonID'], $_GET['apply_to_all_jd']);
                } else if (isset($_GET['addAll'] )) {
                 $constraints = array('archive' => false, 'active' => true, 'condition' => 'r.lessons_ID is null') + createConstraintsFromSortedTable();
                    $lessons = $currentJob -> getJobLessonsIncludingUnassigned($constraints);
                    isset($_GET['filter']) ? $lessons = eF_filterData($lessons,$_GET['filter']) : null;

                    $currentJob -> associateLessonsToJob($lessons, $_GET['apply_to_all_jd']);
                } else if (isset($_GET['removeAll'] )) {
                 $constraints = array('archive' => false, 'active' => true) + createConstraintsFromSortedTable();
                    $lessons = $currentJob -> getJobLessons($constraints);
                    isset($_GET['filter']) ? $lessons = eF_filterData($lessons,$_GET['filter']) : null;
                    $currentJob -> removeLessonsFromJob($lessons, $_GET['apply_to_all_jd']);
                }
            } else if (isset($_GET['course'])) {
                if ($_GET['insert'] == 'true') {
                    $currentJob -> associateCoursesToJob($_GET['add_courseID'], $_GET['apply_to_all_jd']);
                } else if ($_GET['insert'] == 'false') {
                    $currentJob -> removeCoursesFromJob($_GET['add_courseID'], $_GET['apply_to_all_jd']);
                } else if (isset($_GET['addAll'] )) {
                 $constraints = array('archive' => false, 'active' => true, 'condition' => 'r.courses_ID is null') + createConstraintsFromSortedTable();
                    $courses = $currentJob -> getJobCoursesIncludingUnassigned($constraints);
                    isset($_GET['filter']) ? $courses = eF_filterData($courses,$_GET['filter']) : null;
                    $currentJob -> associateCoursesToJob($courses, $_GET['apply_to_all_jd']);

                } else if (isset($_GET['removeAll'] )) {
                 $constraints = array('archive' => false, 'active' => true) + createConstraintsFromSortedTable();
                    $courses = $currentJob -> getJobCourses($constraints);
                    isset($_GET['filter']) ? $courses = eF_filterData($courses,$_GET['filter']) : null;
                    $currentJob -> removeCoursesFromJob($courses, $_GET['apply_to_all_jd']);
                }
            } else if (isset($_GET['training'])) {
             $currentJob -> setRequiredTraining($_GET['training'], $_GET['apply_to_all']);
            }

        } catch (Exception $e) {
            handleAjaxExceptions($e);
        }
            exit;

    }


    if (isset($_GET['delete_job_description']) && $_change_) {
  try {
      $currentJob = new EfrontJob($_GET['delete_job_description']);
         $currentJob -> delete();
     } catch (Exception $e) {
      handleAjaxExceptions($e);
     }
     exit;
    } else if (isset($_GET['remove_user_job']) && $_change_) {
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
        $form -> addRule('job_description_name', _INVALIDFIELDDATA, 'checkParameter', 'text');
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
            if ($currentEmployee -> isSupervisor()) {
             $supervisedEmployees = $currentEmployee -> getSupervisedEmployees();
             $supervisedEmployees[] = $currentEmployee -> login;
             $smarty -> assign("T_SUPERVISED_EMPLOYEES", $supervisedEmployees);
            }
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
            if (($currentUser -> getType() == "administrator" || $currentEmployee -> getType() == _SUPERVISOR)) {
                $skills = $currentJob -> getSkills();

                // Get with ajax
                if (isset($_GET['ajax'])) {
              if (isset($_GET['applytoallusers']) && $_change_) {
            try {
                switch ($_GET['applytoallusers']) {
                 case 'course':
                  $result = eF_getTableDataFlat("module_hcd_course_to_job_description", "*", "job_description_ID=".$_GET['edit_job_description']);
                  $jobcourses = $result['courses_ID'];
                  $resultUsers = eF_getTableDataFlat("module_hcd_employee_has_job_description as jb,users", "jb.*, users.user_type, users.user_types_ID", "users.login=jb.users_login and jb.job_description_ID=".$_GET['edit_job_description']);
                  $jobusers = $resultUsers['users_login'];
                  //Take the default types for users
                  $jobtypes = array ();
                  foreach ($resultUsers['user_types_ID'] as $key => $value) {
                   $resultUsers['user_types_ID'][$key] == 0 ? $jobtypes[$key] = $resultUsers['user_type'][$key] : $jobtypes[$key] = $resultUsers['user_types_ID'][$key];
                  }

                  $result2 = eF_getTableData("users_to_courses", "users_LOGIN,courses_ID,user_type");
                  $usersTocourses = array();
                  foreach ($result2 as $value) {
                   $usersTocourses[$value['courses_ID']][$value['users_LOGIN']] = $value['user_type'];
                  }

                  foreach ($jobcourses as $value) {
          $coursetypes = $jobtypes;
          $course = new EfrontCourse($value);
          foreach ($jobusers as $user) {
           $flag = in_array($user, array_keys($usersTocourses[$value]));
           if ($flag !== false) {
            $index = array_search($user,$jobusers);
            if ($index !== false) {
             unset($jobusers[$index]);
             unset($coursetypes[$index]);
            }
           }
          }
          if (!empty($jobusers)) {
           $course -> addUsers($jobusers, $coursetypes);
          }
         }
                  break;
                 case 'lesson':
                  $result = eF_getTableDataFlat("module_hcd_lesson_to_job_description", "*", "job_description_ID=".$_GET['edit_job_description']);
                  $joblessons = $result['lessons_ID'];
         $resultUsers = eF_getTableDataFlat("module_hcd_employee_has_job_description as jb,users", "jb.*, users.user_type, users.user_types_ID", "users.login=jb.users_login and jb.job_description_ID=".$_GET['edit_job_description']);
                  $jobusers = $resultUsers['users_login'];
                  //Take the default types for users
                  $jobtypes = array ();
                  foreach ($resultUsers['user_types_ID'] as $key => $value) {
                   $resultUsers['user_types_ID'][$key] == 0 ? $jobtypes[$key] = $resultUsers['user_type'][$key] : $jobtypes[$key] = $resultUsers['user_types_ID'][$key];
                  }

                  $result2 = eF_getTableData("users_to_lessons", "users_LOGIN,lessons_ID,user_type");
                  $usersTolessons = array();
                  foreach ($result2 as $value) {
                   $usersTolessons[$value['lessons_ID']][$value['users_LOGIN']] = $value['user_type'];
                  }

                  foreach ($joblessons as $value) {
                   $lessontypes = $jobtypes;
                   $lesson = new EfrontLesson($value);
                   foreach ($jobusers as $user) {
                    $flag = in_array($user, array_keys($usersTolessons[$value]));
                    if ($flag !== false) {
                     $index = array_search($user,$jobusers);
                     if ($index !== false) {
                      unset($jobusers[$index]);
                      unset($lessontypes[$index]);
                     }
                    }
                   }
                   if (!empty($jobusers)) {
                    $lesson -> addUsers($jobusers, $lessontypes);
                   }
                  }
                  break;
                }

            } catch (Exception $e) {
                handleAjaxExceptions($e);
            }
            exit;
           }

           if ($_GET['ajax'] == 'lessonsTable') {
                  try {
                   $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true);
                   $lessons = $currentJob -> getJobLessonsIncludingUnassigned($constraints);
       $totalEntries = $currentJob -> countJobLessonsIncludingUnassigned($constraints);
       $smarty -> assign("T_TABLE_SIZE", $totalEntries);
                   $dataSource = EfrontLesson :: convertLessonObjectsToArrays($lessons);
                   $tableName = $_GET['ajax'];
                   $alreadySorted = 1;
                   include("sorted_table.php");
                  } catch (Exception $e) {
                   handleAjaxExceptions($e);
                  }
                 }
                 $smarty -> assign("T_DATASOURCE_SORT_BY", 5);
                 $smarty -> assign("T_DATASOURCE_SORT_ORDER", 'desc');
                 $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'location', 'directions_name', 'num_lessons', 'has_course'));
                 if ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable') {
                  try {
                   if ($_GET['ajax'] == 'coursesTable') {
                    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => false);
                   }
                   if ($_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
                    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => $_GET['instancesTable_source']);
                   }
                   $courses = $currentJob -> getJobCoursesIncludingUnassigned($constraints);
       $totalEntries = $currentJob -> countJobCoursesIncludingUnassigned($constraints);
       $smarty -> assign("T_TABLE_SIZE", $totalEntries);
                   $dataSource = EfrontCourse :: convertCourseObjectsToArrays($courses);
                   $tableName = $_GET['ajax'];
                   $alreadySorted = 1;
                   include("sorted_table.php");
                  } catch (Exception $e) {
                   handleAjaxExceptions($e);
                  }
                 }

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

        if ($_change_) {
         $form -> addElement('submit', 'submit_job_description_details', _SUBMIT, 'class = "flatButton"');
        } else {
         $form -> freeze();
        }
        /* Set default values */
        if (isset($_GET['edit_job_description'])) {
            $form -> setDefaults(array( 'job_description_name' => $currentJob -> job['description'],
                                        'placements' => $currentJob -> job['employees_needed'],
                                        'job_role_description' => $currentJob -> job['job_role_description']));
        } else {
         $details_link = "";
        }

        $smarty -> assign("T_BRANCH_ID", $currentJob -> job['branch_ID']);

        /* If add_branch request coming from another branch subbranches menu, pre-enter the fatherBranch form */
        if (isset($_GET['add_job_description'])) {

         if (isset($_GET['add_to_branch'])) {
             $form -> setDefaults(array( 'branch' => $_GET['add_to_branch']));
             $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['add_to_branch'] . "\"";
             $smarty -> assign("T_BRANCH_INFO", $details_link);
         } else {
          if (!empty($branches)) {
           $defaultBranch = $branches[0]['branch_ID'];
           $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $defaultBranch . "\"";
           $form -> setDefaults(array('branch' => $defaultBranch));
           $smarty -> assign("T_BRANCH_INFO", $details_link);
          }
         }
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
      $branchesTree = new EfrontBranchesTree();
      $branchPaths = $branchesTree -> toPathString();

      $job_descriptions = EfrontJob :: getAllJobs();
      foreach ($job_descriptions as $key => $value) {
       $job_descriptions[$key]['branch_path'] = eF_truncatePath($branchPaths[$value['branch_ID']], 10);
      }
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
