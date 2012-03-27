<?php

/**
 * Abstract class for any Import class - serves as an interface for subsequent
 * developed importers
 *
 * @package eFront
 * @abstract
 */
abstract class EfrontImport
{
 /**
	 * The contents of the file to be imported
	 *
	 * @since 3.6.1
	 * @var string
	 * @access private
	 */
 protected $fileContents;

 /**
	 * Various options like duplicates handling are stored in the options array
	 *
	 * @since 3.6.1
	 * @var array
	 * @access private
	 */
 protected $options;


 /**
	 * Log where the results of the import are stored
	 *
	 * @since 3.6.1
	 * @var array
	 * @access private
	 */
 protected $log = array();

    /**
     * Import the data from the file following the designated options
     *
     * <br/>Example:
     * <code>
     * $importer -> import(); //returns something like /var/www/efront/upload/admin/
     * $logMessages = $importer -> getLogMessages();
     * </code>
     *
     * @return void
     * @since 3.6.1
     * @access public
     */


    /**
     * Get the log of the import operations
     *
     * <br/>Example:
     * <code>
     * $importer -> import(); //returns something like /var/www/efront/upload/admin/
     * $logMessages = $importer -> getLogMessages();
     * </code>
     *
     * @return array with subarrays "success" and "failure" each with corresponding messages
     * @since 3.6.1
     * @access public
     */
 public function getLogMessages() {
  return $this -> log;
 }


 protected static $datatypes = false;
 public static function getImportTypes() {
  if (!self::$datatypes) {
   self::$datatypes = array("anything" => _IMPORTANYTHING,
          "users" => _USERS,
          "users_to_courses" => _USERSTOCOURSES);
  }
  return self::$datatypes;
 }
 public function getImportTypeName($import_type) {
  if (!$datatypes) {
   $datatypes = EfrontImport::getImportTypes();
  }
  return $datatypes[$import_type];
 }
 public function __construct($filename, $_options) {
  $this -> fileContents = file_get_contents($filename);
  $this -> options = $_options;
 }
 /*
	 * All following functions cache arrays of type "entity_name" => array("entity_ids of entities with name=entity_name")
	 */
 protected $courseNamesToIds = false;
 protected function getCourseByName($courses_name) {
  if (!$this -> courseNamesToIds) {
   $constraints = array ('return_objects' => false);
   $courses = EfrontCourse::getAllCourses($constraints);
   foreach($courses as $course) {
    if (!isset($this -> courseNamesToIds[$course['name']])) {
     $this -> courseNamesToIds[$course['name']] = array($course['id']);
    } else {
     $this -> courseNamesToIds[$course['name']][] = $course['id'];
    }
   }
  }
  return $this -> courseNamesToIds[$courses_name];
 }
 private $groupNamesToIds = false;
 protected function getGroupByName($group_name) {
  if (!$this -> groupNamesToIds) {
   $groups = EfrontGroup::getGroups();
   foreach($groups as $group) {
    if (!isset($this -> groupNamesToIds[$group['name']])) {
     $this -> groupNamesToIds[$group['name']] = array($group['id']);
    } else {
     $this -> groupNamesToIds[$group['name']][] = $group['id'];
    }
   }
  }
  return $this -> groupNamesToIds[$group_name];
 }
 /*
	 * Convert dates of the form dd/mm/yy to timestamps
	 */
    protected function createTimestampFromDate($date_field) {
        // date of event if existing, else current time
        if ($date_field != "") {
         $date_field = trim($date_field);
         // Assuming dd/mm/yy or dd-mm-yy
            $dateParts = explode("/", $date_field);
            if (sizeof($dateParts) == 1) {
             $dateParts = explode("-", $date_field);
            }
            if ($this -> options['date_format'] == "MM/DD/YYYY") {
             $timestamp = mktime(0,0,0,$dateParts[0],$dateParts[1],$dateParts[2]);
            } else if ($this -> options['date_format'] == "YYYY/MM/DD") {
             $timestamp = mktime(0,0,0,$dateParts[2],$dateParts[0],$dateParts[1]);
            } else {
             $timestamp = mktime(0,0,0,$dateParts[1],$dateParts[0],$dateParts[2]);
            }
            return $timestamp;
        } else {
         return "";
        }
    }
 /*
	 * Create the mappings between csv columns and db attributes
	 */
 public static function getTypes($type) {
  switch($type) {
   case "users":
    $users_info = array("users_login" => "login",
           "password" => "password",
           "users_email" => "email",
           "language" => "languages_NAME",
           "users_name" => "name",
           "users_surname" => "surname",
           "active" => "active",
           "user_type" => "user_type",
           "registration_date" => "timestamp",
           "timezone" => "timezone",
           "archive" => "archive");
    return $users_info;
   case "users_to_courses":
    return array("users_login" => "users_login",
         "courses_name" => "course_name",
         "course_start_date" => "from_timestamp",
         "course_user_type" => "user_type",
         "course_completed" => "completed",
         "course_comments" => "comments",
         "course_score" => "score",
         "course_active" => "active",
         "course_end_date" => "to_timestamp");
   case "users_to_groups":
    return array("users_login" => "users_login",
        "group_name" => "groups.name");
  }
 }
    /*
     * Get array of fields that are mandatory to be defined for a successfull import according to the type of import
     */
 public static function getMandatoryFields($type) {
  switch($type) {
   case "users":
    return array("login" => "users_login");
   case "users_to_courses":
    return array("users_login" => "users_login",
        "course_name"=> "courses_name");
   case "users_to_groups":
    return array("users_login" => "users_login",
        "groups.name" => "group_name");
  }
 }
 public static function getOptionalFields($type) {
  $all = EfrontImport::getTypes($type);
  $mandatory = EfrontImport::getMandatoryFields($type);
  foreach ($mandatory as $type_name => $column) {
   unset($all[$column]);
  }
  return array_keys($all);
 }
}
/****************************************************
 * Class used to import data from csv files
 *
 */
class EfrontImportCsv extends EfrontImport
{
 /*
	 * The separator between the file's fields
	 */
 private $separator = false;
 /*
	 * Array containing metadata about the imported data type (db attribute names, db tables, import-file accepted column names)
	 */
 protected $types = false;
 /*
	 * Number of lines of the imported file
	 */
 protected $lines = false;
 /*
	 * Array with the mappings between db fields and columns
	 */
 protected $mappings = array();
 /*
	 * Used for initialization of data (to contain all fields)
	 */
 protected $empty_data = false;
 /*
	 * Find the separator - either "," or ";"
	 */
 protected function getSeparator() {
  if (!$this -> separator) {
   $this -> separator = ",";
   $test_line = explode($this -> separator, $this -> fileContents[0]);
   if (sizeof($test_line) > 1) {
    return $this -> separator;
   }
   $this -> separator = ";";
   $test_line = explode($this -> separator, $this -> fileContents[0]);
   if (sizeof($test_line) > 1) {
    return $this -> separator;
   }
   $this -> separator = false;
  }
  return $this -> separator;
 }
 /*
	 * Get empty data - used for caching of initialization array
	 */
 protected function getEmptyData() {
  if (!$this -> empty_data) {
   $this -> empty_data = array();
   foreach ($this -> types as $key) {
    $this -> empty_data[$key] = "";
   }
   unset($this -> empty_data['user_type']); // the default value should never be set to ""
   unset($this -> empty_data['password']); // the default value should never be set to ""
  }
  return $this -> empty_data;
 }
 /*
	 * Split a line to its different strings as they are determined by the separator
	 */
 protected function explodeBySeparator($line) {
  if ($this -> separator) {
   return str_getcsv($this -> fileContents[$line], $this -> separator);
  } else {
   return $this -> fileContents[$line];
  }
 }
 /*
	 * Find the header line - the first non zero line of the csv that contains at least one of the import $type's column headers
	 * @param: the line of the header
	 */
 protected function parseHeaderLine(&$headerLine) {
  $this -> mappings = array();
  $this -> separator = $this -> getSeparator();
  $legitimate_column_names = array_keys($this -> types);
  //pr($legitimate_column_names);
  $found_header = false;
  for ($line = 0; $line < $this -> lines; ++$line) {
   $candidate_header = $this -> explodeBySeparator($line);
   $size_of_header = sizeof($candidate_header);
   for ($header_record = 0; $header_record < $size_of_header; ++$header_record) {
    $candidate_header[$header_record] = trim($candidate_header[$header_record], "\"\r\n ");
    if ($candidate_header[$header_record] != "" && in_array($candidate_header[$header_record], $legitimate_column_names)) {
     $this -> mappings[$this -> types[$candidate_header[$header_record]]] = $header_record;
     $found_header = true;
    }
   }
   if ($found_header) {
    $headerLine = $line;
    return $this -> mappings;
   }
  }
  return false;
 }
 /*
	 * Utility function to initialize the $log array
	 */
 protected function clearLog() {
  $this -> log = array();
  $this -> log["success"] = array();
  $this -> log["failure"] = array();
 }
 protected function clear() {
  $this -> clearLog();
  $this -> mappings = array();
  $this -> empty_data = false;
  $this -> types = array();
 }
 /*
	 * Get existence exception and compare it against the "already exists" exception of for each different import type
	 */
 protected function isAlreadyExistsException($exception_code, $type) {
  switch ($type) {
   case "users":
    if ($exception_code == EfrontUserException::USER_EXISTS) { return true; }
    break;
   default:
    return false;
  }
  return false;
 }
 protected function cleanUpEmptyValues(&$data) {
  foreach ($data as $key => $info) {
   if ($info == "") {
    unset($data[$key]);
   }
  }
 }
 /*
	 * Update the data of an existing record
	 */
 protected function updateExistingData($line, $type, $data) {
  $this -> cleanUpEmptyValues(&$data);
  try {
   switch($type) {
    case "users":
     if (isset($data['password']) && $data['password'] != "" && $data['password'] != "ldap") {
      $data['password'] = EfrontUser::createPassword($data['password']);
     }
     eF_updateTableData("users", $data, "login='".$data['login']."'"); $this -> log["success"][] = _LINE . " $line: " . _REPLACEDUSER . " " . $data['login'];
     if (function_exists('apc_delete')) {
      apc_delete(G_DBNAME.':_usernames');
     }
     break;
    case "users_to_courses":
     $where = "users_login='".$data['users_login']."' AND courses_ID = " . $data['courses_ID'];
     EfrontCourse::persistCourseUsers($data, $where, $data['courses_ID'], $data['users_login']);
     $this -> log["success"][] = _LINE . " $line: " . _REPLACEDEXISTINGASSIGNMENT;
     break;
    case "users_to_groups":
     break;
   }
  } catch (Exception $e) {
   $this -> log["failure"][] = _LINE . " $line: " . $e -> getMessage();
  }
 }
 protected function importDataMultiple($type, $data) {
  try {
   switch($type) {
    case "users_to_groups":
     foreach ($data as $value) {
      $groups_ID = current($this -> getGroupByName($value['groups.name']));
      $groups[$groups_ID][] = $value['users_login'];
     }
     foreach ($groups as $id => $groupUsers) {
      try {
       $group = new EfrontGroup($id);
       $this -> log["success"][] = _NEWGROUPASSIGNMENT . " " . $group -> group['name'];
       $group -> addUsers($groupUsers);
      } catch (Exception $e) {
       $this -> log["failure"][] = _LINE . " ".($key+2).": " . $e -> getMessage();// ." ". str_replace("\n", "<BR>", $e->getTraceAsString());
      }
     }
     break;
    case "users":
     $existingUsers = eF_getTableDataFlat("users", "login, active, archive");
     $roles = EfrontUser::getRoles();
     $rolesTypes = EfrontUser::getRoles(true);
     $languages = EfrontSystem :: getLanguages();
     $addedUsers = array();
     foreach ($data as $key => $value) {
      try {
       $newUser = EfrontUser::createUser($value, $existingUsers, false);
       $existingUsers['login'][] = $newUser -> user['login'];
       $existingUsers['active'][] = $newUser -> user['active'];
       $existingUsers['archive'][] = $newUser -> user['archive'];
       $addedUsers[] = $newUser -> user['login'];
       $this -> log["success"][] = _IMPORTEDUSER . " " . $newUser -> user['login'];
      } catch (Exception $e) {
       if ($this -> options['replace_existing']) {
        if ($this -> isAlreadyExistsException($e->getCode(), $type)) {
         if (!in_array($value['login'], $existingUsers['login'])) { //For case-insensitive matches
          foreach ($existingUsers['login'] as $login) {
           if (mb_strtolower($value['login']) == mb_strtolower($login)) {
            $value['login'] = $login;
           }
          }
         }
         if (!isset($value['user_type'])) {
          $value['user_type'] = 'student';
         } else {
          if (in_array(mb_strtolower($value['user_type']), $roles)) {
           $value['user_type'] = mb_strtolower($value['user_type']);
          } else if ($k=array_search($value['user_type'], $rolesTypes)) {
           $value['user_types_ID'] = $k;
           $value['user_type'] = $roles[$k];
          } else {
           $value['user_type'] = 'student';
          }
         }
         if (!in_array($value['user_type'], EFrontUser::$basicUserTypes)) {
          $value['user_type'] = 'student';
          $value['user_types_ID'] = 0;
         }
         if (in_array($value['languages_NAME'], array_keys($languages)) === false) {
          $value['languages_NAME'] = $GLOBALS['configuration']['default_language'];
         }
         $this -> updateExistingData($key+2, $type, $value);
        } else {
         $this -> log["failure"][] = _LINE . " ".($key+2).": " . $e -> getMessage();// ." ". str_replace("\n", "<BR>", $e->getTraceAsString());
        }
       } else {
        $this -> log["failure"][] = _LINE . " ".($key+2).": " . $e -> getMessage();// ." ". str_replace("\n", "<BR>", $e->getTraceAsString());
       }
      }
     }
     $defaultGroup = eF_getTableData("groups", "id", "is_default = 1 AND active = 1");
     if (!empty($defaultGroup) && !empty($addedUsers)) {
      $defaultGroup = new EfrontGroup($defaultGroup[0]['id']);
      $defaultGroup -> addUsers($addedUsers);
     }
     break;
    case "users_to_jobs":
     $jobDescriptions = $userJobs = $userBranchesAssigned = $userBranchesUnassigned = array();
     $result = eF_getTableData("module_hcd_job_description", "job_description_ID, branch_ID, description");
     foreach ($result as $value) {
      $jobDescriptions[$value['job_description_ID']] = $value;
     }
     $result = eF_getTableData("module_hcd_employee_has_job_description", "*");
     foreach ($result as $value) {
      $userJobs[$value['users_login']][$value['job_description_ID']] = $value['job_description_ID'];
     }
     $result = eF_getTableData("module_hcd_employee_works_at_branch", "*");
     foreach ($result as $value) {
      if ($value['assigned']) {
       $userBranchesAssigned[$value['users_login']][$value['branch_ID']] = $value;
      } else {
       $userBranchesUnassigned[$value['users_login']][$value['branch_ID']] = $value;
      }
     }
     $allBranches = eF_getTableData("module_hcd_branch", "branch_ID, father_branch_ID", "");
     $addedJobs = $addedBranches = array();
     foreach ($data as $key => $value) {
      try {
       if (!$value['description']) {
        throw new EfrontJobException(_MISSING_JOB_DESCRIPTION, EfrontJobException::MISSING_JOB_DESCRIPTION);
       }
       $branchId = $this -> getBranchByName($value['branch_name']); //Executes only once
       if ($branchId[0]) {
        if (sizeof($branchId) == 1) {
         $branchId = $branchId[0];
        } else {
         throw new EfrontBranchException(_BRANCHNAMEAMBIGUOUS.': '.$value['branch_name'], EfrontBranchException :: BRANCH_AMBIGUOUS);
        }
       } else {
        throw new EfrontBranchException(_BRANCHDOESNOTEXIST.': '.$value['branch_name'], EfrontBranchException::BRANCH_NOT_EXISTS);
       }
       $jobId = false;
       foreach ($jobDescriptions as $job) {
        if ($job['description'] == $value['description'] && $job['branch_ID'] == $branchId) {
         $jobId = $job['job_description_ID'];
        }
       }
       if (!$jobId) {
        $jobId = eF_insertTableData("module_hcd_job_description", array('description' => $value['description'], 'branch_ID' => $branchId));
        $jobDescriptions[$jobId] = array('job_description_ID' => $jobId, 'description' => $value['description'], 'branch_ID' => $branchId);
       }
       $user = EfrontUserFactory::factory($value["users_login"]);
       $value['users_login'] = $user -> user['login'];
       if (isset($userJobs[$value['users_login']]) && $this -> options['replace_assignments']) {
        $unset = false;
        foreach ($userJobs[$value['users_login']] as $key => $v) {
         if (!isset($addedJobs[$v][$value['users_login']])) {
          $user->aspects['hcd']->removeJob($v);
          unset($userJobs[$value['users_login']][$v]);
          $unset = true;
         }
        }
        if ($unset) {
         unset($userBranchesAssigned[$value['users_login']]);
        }
       }
       if (isset($userJobs[$value['users_login']][$jobId]) && $this -> options['replace_existing']) {
        eF_deleteTableData("module_hcd_employee_has_job_description", "users_login='".$value['users_login']."' AND job_description_ID ='".$jobId."'");
        unset($userJobs[$value['users_login']][$jobId]);
       }
       // Check if this job description is already assigned
       if (!isset($userJobs[$value['users_login']][$jobId])) {
        if (!isset($userBranchesAssigned[$value['users_login']][$branchId])) {
         // Write to the database the new branch assignment: employee to branch (if such an assignment is not already true)
         if (isset($userBranchesUnassigned[$value['users_login']][$branchId])) {
          eF_updateTableData("module_hcd_employee_works_at_branch", array("assigned" => 1), "users_login='".$value['users_login']."' and branch_ID=$branchId");
          unset($userBranchesUnassigned[$value['users_login']][$branchId]);
         } else {
          $fields = array('users_login' => $value['users_login'],
              'supervisor' => $value['supervisor'],
              'assigned' => '1',
              'branch_ID' => $branchId);
          eF_insertTableData("module_hcd_employee_works_at_branch", $fields);
          if ($value['supervisor']) {
           //Iterate through sub branches
           foreach (eF_subBranches($branchId, $allBranches) as $subBranchId) {
            //If this subranch is not associated with the user, associate it
            if (!isset($userBranchesAssigned[$value['users_login']][$subBranchId]) && !isset($userBranchesUnassigned[$value['users_login']][$subBranchId])) {
             $fields = array('users_login' => $value['users_login'],
                 'supervisor' => 1,
                 'assigned' => '0',
                 'branch_ID' => $subBranchId);
             eF_insertTableData("module_hcd_employee_works_at_branch", $fields);
             $userBranchesUnassigned[$value['users_login']][$branchId] = array('branch_ID' => $branchId, 'supervisor' => $value['supervisor'], 'assigned' => 0);
            } elseif (isset($userBranchesAssigned[$value['users_login']][$subBranchId]) && $userBranchesAssigned[$value['users_login']][$subBranchId]['supervisor'] == 0) {
             eF_updateTableData("module_hcd_employee_works_at_branch", array("supervisor" => 1), "users_login='".$value['users_login']."' and branch_ID=$subBranchId");
             $userBranchesAssigned[$value['users_login']][$subBranchId]['supervisor'] = 1;
            } elseif (isset($userBranchesUnassigned[$value['users_login']][$subBranchId]) && $userBranchesUnassigned[$value['users_login']][$subBranchId]['supervisor'] == 0) {
             eF_updateTableData("module_hcd_employee_works_at_branch", array("supervisor" => 1), "users_login='".$value['users_login']."' and branch_ID=$subBranchId");
             $userBranchesUnassigned[$value['users_login']][$subBranchId]['supervisor'] = 1;
            }
           }
          }
         }
         $userBranchesAssigned[$value['users_login']][$branchId] = array('branch_ID' => $branchId, 'supervisor' => $value['supervisor'], 'assigned' => 1);
         $addedBranches[$branchId][$value['users_login']] = $value['users_login'];
        } elseif (!$userBranchesAssigned[$value['users_login']][$branchId]['supervisor'] && $value['supervisor']) {
         eF_updateTableData("module_hcd_employee_works_at_branch", array("supervisor" => 1), "users_login='".$value['users_login']."' and branch_ID=$branchId");
         //Iterate through sub branches
         foreach (eF_subBranches($branchId, $allBranches) as $subBranchId) {
          //If this subranch is not associated with the user, associate it
          if (!isset($userBranchesAssigned[$value['users_login']][$subBranchId]) && !isset($userBranchesUnassigned[$value['users_login']][$subBranchId])) {
           $fields = array('users_login' => $value['users_login'],
               'supervisor' => 1,
               'assigned' => '0',
               'branch_ID' => $subBranchId);
           eF_insertTableData("module_hcd_employee_works_at_branch", $fields);
           $userBranchesUnassigned[$value['users_login']][$branchId] = array('branch_ID' => $branchId, 'supervisor' => $value['supervisor'], 'assigned' => 0);
          } elseif (isset($userBranchesAssigned[$value['users_login']][$subBranchId]) && $userBranchesAssigned[$value['users_login']][$subBranchId]['supervisor'] == 0) {
           eF_updateTableData("module_hcd_employee_works_at_branch", array("supervisor" => 1), "users_login='".$value['users_login']."' and branch_ID=$subBranchId");
           $userBranchesAssigned[$value['users_login']][$subBranchId]['supervisor'] = 1;
          } elseif (isset($userBranchesUnassigned[$value['users_login']][$subBranchId]) && $userBranchesUnassigned[$value['users_login']][$subBranchId]['supervisor'] == 0) {
           eF_updateTableData("module_hcd_employee_works_at_branch", array("supervisor" => 1), "users_login='".$value['users_login']."' and branch_ID=$subBranchId");
           $userBranchesUnassigned[$value['users_login']][$subBranchId]['supervisor'] = 1;
          }
         }
        } elseif ($userBranchesAssigned[$value['users_login']][$branchId]['supervisor'] && !$value['supervisor']) {
         eF_updateTableData("module_hcd_employee_works_at_branch", array("supervisor" => 0), "users_login='".$value['users_login']."' and branch_ID=$branchId");
        }
        // Write to database the new job assignment: employee to job description
        $fields = array('users_login' => $value['users_login'],
            'job_description_ID' => $jobId);
        eF_insertTableData("module_hcd_employee_has_job_description", $fields);
        $userJobs[$value['users_login']][$jobId] = $jobId;
        $addedJobs[$jobId][$value['users_login']] = $value['users_login'];
/*
									if ($event_info) {
										EfrontEvent::triggerEvent(array("type" => EfrontEvent::HCD_NEW_JOB_ASSIGNMENT, "users_LOGIN" => $this -> login, "lessons_ID" => $branchID, "lessons_name" => $bname[0]['name'], "entity_ID" => $jobID, "entity_name" => $job_description, "timestamp" => $event_info['timestamp'], "explicitly_selected" => $event_info['manager']));
									} else {
										EfrontEvent::triggerEvent(array("type" => EfrontEvent::HCD_NEW_JOB_ASSIGNMENT, "users_LOGIN" => $this -> login, "lessons_ID" => $branchID, "lessons_name" => $bname[0]['name'], "entity_ID" => $jobID, "entity_name" => $job_description));
									}
*/
       } else {
        throw new EfrontUserException(_JOBALREADYASSIGNED . ": ".$value['users_login'], EfrontUserException :: WRONG_INPUT_TYPE);
       }
       $this -> log["success"][] = _LINE." ".($key+2)." : "._NEWJOBASSIGNMENT . " " . $value["users_login"] . " - (" .$value['branch_name'] . " - " .$value['description'] . ") ";
      } catch (Exception $e) {
       $this -> log["failure"][] = _LINE." ".($key+2)." : ".$e -> getMessage().' ('.$e -> getCode().')';
      }
     }
     $courseAssignmentsToUsers = $lessonAssignmentsToUsers = array();
     $result = eF_getTableData("module_hcd_course_to_job_description", "*");
     foreach ($result as $value) {
      foreach ($addedJobs[$value['job_description_ID']] as $user) {
       $courseAssignmentsToUsers[$value['courses_ID']][] = $user;
      }
     }
     $result = eF_getTableData("module_hcd_lesson_to_job_description", "*");
     foreach ($result as $value) {
      foreach ($addedJobs[$value['job_description_ID']] as $user) {
       $lessonAssignmentsToUsers[$value['lessons_ID']][] = $user;
      }
     }
     $result = eF_getTableData("module_hcd_course_to_branch", "*");
     foreach ($result as $value) {
      foreach ($addedBranches[$value['branches_ID']] as $user) {
       $courseAssignmentsToUsers[$value['courses_ID']][] = $user;
      }
     }
     foreach ($courseAssignmentsToUsers as $courseId => $users) {
      $course = new EfrontCourse($courseId);
      $course -> addUsers($users);
     }
     foreach ($lessonAssignmentsToUsers as $lessonId => $users) {
      $course = new EfrontLesson($lessonId);
      $course -> addUsers($users);
     }
     break;
   }
  } catch (Exception $e) {
   $this -> log["failure"][] = $e -> getMessage().' ('.$e -> getCode().')';// ." ". str_replace("\n", "<BR>", $e->getTraceAsString());
  }
 }
 /*
	 * Use eFront classes according to the type of import to store the data used
	 * @param line: the line of the imported file
	 * @param type: the import type
	 * @param type: the data of this line, formatted to be put directly into the eFront db
	 */
 //TODO: this should be moved to the EfrontImport base class - and be used by all - the $line should probably leave though
 protected function importData($line, $type, $data) {
//pr($line);exit;		
  try {
   switch($type) {
    case "users":
     $newUser = EfrontUser::createUser($data);
     $this -> log["success"][] = _LINE . " $line: " . _IMPORTEDUSER . " " . $newUser -> login;
     break;
    case "users_to_courses":
     //Check if a user exists and whether it has the same case
     $userFound = false;
     if (!in_array($data['users_login'], $this->allUserLogins)) { //For case-insensitive matches
      foreach ($this->allUserLogins as $login) {
       if (mb_strtolower($data['users_login']) == mb_strtolower($login)) {
        $data['users_login'] = $login;
        $userFound = true;
       }
      }
     } else {
      $userFound = true;
     }
     if ($userFound) {
      $courses_name = trim($data['course_name']);
      $courses_ID = $this -> getCourseByName($courses_name);
      unset($data['course_name']);
      if ($courses_ID) {
       foreach($courses_ID as $course_ID) {
        $data['courses_ID'] = $course_ID;
        $course = new EfrontCourse($course_ID);
        //$course -> addUsers($data['users_login'], (isset($data['user_type']) && $data['user_type']?$data['user_type']:"student"));
        $course -> addUsers($data['users_login'], (isset($data['user_type'])?$data['user_type']:"student"));
        $where = "users_login = '" .$data['users_login']. "' AND courses_ID = " . $data['courses_ID'];
        $data['completed'] ? $data['completed'] = 1 : $data['completed'] = 0;
        EfrontCourse::persistCourseUsers($data, $where, $data['courses_ID'], $data['users_login']);
        if ($data['active']) {
         $course -> confirm($data['users_login']);
        } else {
         $course -> unconfirm($data['users_login']);
        }
        $this -> log["success"][] = _LINE . " $line: " . _NEWCOURSEASSIGNMENT . " " . $courses_name . " - " . $data['users_login'];
       }
      } else if ($courses_name != "") {
       $course = EfrontCourse::createCourse(array("name" => $courses_name));
       $this -> log["success"][] = _LINE . " $line: " . _NEWCOURSE . " " . $courses_name;
       $course -> addUsers($data['users_login'], (isset($data['user_type'])?$data['user_type']:"student"));
       $courses_ID = $course -> course['id'];
       $this -> courseNamesToIds[$courses_name] = array($courses_ID);
       $where = "users_login = '" .$data['users_login']. "' AND courses_ID = " . $courses_ID;
       EfrontCourse::persistCourseUsers($data, $where, $courses_ID, $data['users_login']);
       if ($data['active']) {
        $course -> confirm($data['users_login']);
       } else {
        $course -> unconfirm($data['users_login']);
       }
       $this -> log["success"][] = _LINE . " $line: " . _NEWCOURSEASSIGNMENT . " " . $courses_name . " - " . $data['users_login'];
      } else {
       $this -> log["failure"][] = _LINE . " $line: " . _COULDNOTFINDCOURSE . " " . $courses_name;
      }
     } else {
      $this -> log["failure"][] = _LINE . " $line: " . _USERDOESNOTEXIST. ": " . $data['users_login'];
     }
     break;
    case "users_to_groups":
     //debug();
     $groups_ID = $this -> getGroupByName($data['groups.name']);
     $group_name = $data['groups.name'];
     unset($data['groups.name']);
     foreach($groups_ID as $group_ID) {
      $data['groups_ID'] = $group_ID;
      $group = new EfrontGroup($group_ID);
      $group -> addUsers(array($data['users_login']));
      $this -> log["success"][] = _LINE . " $line: " . _NEWGROUPASSIGNMENT . " " . $group_name . " - " . $data['users_login'];
     }
     break;
     //debug(false);
   }
  } catch (Exception $e) {
   if ($this -> options['replace_existing']) {
    if ($this -> isAlreadyExistsException($e->getCode(), $type)) {
     $this -> updateExistingData($line, $type, $data);
    } else {
     $this -> log["failure"][] = _LINE . " $line: " . $e -> getMessage();// ." ". str_replace("\n", "<BR>", $e->getTraceAsString());
    }
   } else {
    $this -> log["failure"][] = _LINE . " $line: " . $e -> getMessage();// ." ". str_replace("\n", "<BR>", $e->getTraceAsString());
   }
  }
 }
 /*
	 * Check whether the file contains the columns that are necessary for this import type
	 */
 protected function checkImportEssentialField($type) {
  $mandatoryFields = EfrontImport::getMandatoryFields($type);
  $not_found = false;
  foreach ($mandatoryFields as $dbField => $columnName) {
   if (!isset($this -> mappings[$dbField])) {
    $not_found = true;
    break;
   }
  }
  if ($not_found) {
   $this -> log["failure"]["headerproblem"] = _HEADERDOESNOTINCLUDEESSENTIALCOLUMN . ": " . implode(",", $mandatoryFields);
   return false;
  } else {
   return true;
  }
 }
 /*
	 * Parse line data
	 */
 protected function parseDataLine($line) {
  $lineContents = $this -> explodeBySeparator($line);
  array_walk($lineContents, create_function('&$val', '$val = trim($val);'));
  $data = $this -> getEmptyData();
  foreach ($this -> mappings as $dbAttribute => $fileInfo) {
   if (strpos($dbAttribute, "timestamp") === false && $dbAttribute != "hired_on" && $dbAttribute != "left_on" && !in_array($dbAttribute, $this->dateFields)) {
    $data[$dbAttribute] = trim($lineContents[$fileInfo], "\r\n");
   } else {
    $data[$dbAttribute] = $this -> createTimestampFromDate(trim($lineContents[$fileInfo], "\r\n"));
   }
  }
  //pr($data);pr(date("Y/m/d", $data['to_timestamp']));exit;
  return $data;
 }
 /*
	 * Main importing function
	 */
 public function import($type) {
  $this -> clear();
  if ($this -> lines == "") {
   $this -> log["failure"]["missingheader"] = _NOHEADERROWISDEFINEDORHEADERROWNOTCOMPATIBLEWITHIMPORTTYPE;
  } else if ($this -> lines == 1) {
   $this -> log["failure"]["missingdata"] = _THEFILEAPPEARSEMPTYPERHAPSITISNOTFORMATTEDCORRECTLY;
  } else {
   // Pairs of values <Csv column header> => <eFront DB field>
   $this -> types = EfrontImport::getTypes($type);
   // Pairs of values <eFront DB field> => <import file column>
   $this -> mappings = $this -> parseHeaderLine(&$headerLine);
   $result = eF_getTableDataFlat("user_profile", "name", "active=1 AND type ='date'"); //Get admin-defined form fields for user registration
   if (!empty($result)) {
    $this->dateFields = $result['name'];
   } else {
    $this->dateFields = array();
   }
   if ($this -> mappings) {
    if ($this -> checkImportEssentialField($type)) {
     if ($type == 'users_to_groups' || $type == 'users' || $type == 'users_to_jobs') {
      $data = array();
      for ($line = $headerLine+1; $line < $this -> lines; ++$line) {
       $data[] = $this -> parseDataLine($line);
      }
      $this -> importDataMultiple($type, $data);
     } else {
      $result = eF_getTableDataFlat("users", "login");
      $this->allUserLogins = $result['login'];
      for ($line = $headerLine+1; $line < $this -> lines; ++$line) {
       $data = $this -> parseDataLine($line);
       $this -> importData($line+1, $type, $data);
      }
     }
    }
   } else {
    $this -> log["failure"]["missingheader"] = _NOHEADERROWISDEFINEDORHEADERROWNOTCOMPATIBLEWITHIMPORTTYPE;
   }
  }
  return $this -> log;
 }
 /*
	 * Set the memory and time limits for an import according to the number of lines to be imported
	 */
 protected function setLimits($factor = false) {
  if (!$factor) {
   $factor = $this->lines / 500;
  }
  if ($factor < 1) {
   return;
  }
  if ($factor > 20) {
   $factor = 20;
  }
  $maxmemory = 128 * $factor;
  $maxtime = 300 * $factor;
  //ini_set("memory_limit", max($maxmemory,$GLOBALS['configuration']['memory_limit'])  . "M");
        //ini_set("max_execution_time", max($maxtime, $GLOBALS['configuration']['max_execution_time']));
 }
 public function __construct($filename, $_options) {
  $this -> fileContents = file_get_contents($filename);
  $this -> fileContents = explode("\n", trim($this -> fileContents));
  $this -> lines = sizeof($this -> fileContents);
  $this -> setLimits();
  $this -> options = $_options;
 }
}
/**
 * Import Factory class
 *
 * This class is used as a factory for import objects
 * <br/>Example
 * <code>
 * $importer = EfrontImportFactory :: factory('csv', $file, $options);
 * $importer -> import();
 * $importer -> import('users');
 * </code>
 *
 * @package eFront
 * @version 3.6.1
 */
class EfrontImportFactory
{
    /**
     * Construct import object
     *
     * This function is used to construct an import object which can be
     * of any type: EfrontCsvImport
     *
     * <br/>Example :
     * <code>
     * $file = $filesystem -> uploadFile('upload_file');
     * $user = EfrontImportFactory :: factory('csv', $file, array("keep_duplicates" => 1);            //Use factory function to instantiate user object with login 'jdoe'
     * </code>
     *
     * @param string the type the importer: currently only 'csv' is supported $importerType
     * @param file $filename
     * @param options $various import options
     * @return EfrontImport an object of a class extending EfrontImport
     * @since 3.6.1
     * @access public
     * @static
     */
    public static function factory($importerType, $file, $options = false) {
     if (!($file instanceof EfrontFile)) {
      $file = new EfrontFile($file);
     }
     switch ($importerType) {
      case 'csv' : $factory = new EfrontImportCsv($file['path'], $options); break;
     }
        return $factory;
    }
}
// -----------------------------------------------------------------------------------------------------------------
/**
 * Abstract class for any Export class - serves as an interface for subsequent
 * developed exporters
 *
 * @package eFront
 * @abstract
 */
abstract class EfrontExport
{
 /**
	 * Various options like duplicates handling are stored in the options array
	 *
	 * @since 3.6.1
	 * @var array
	 * @access protected
	 */
 protected $options;
 /**
	 * The lines that should finally be exported are written in this array
	 *
	 * @since 3.6.1
	 * @var array
	 * @access protected
	 */
 protected $lines = array();
    /**
     * Export the data from the file following the designated options
     *
     * <br/>Example:
     * <code>
     * $exporter -> export(); //returns something like /var/www/efront/upload/admin/
     * $logMessages = $exporter -> getLogMessages();
     * </code>
     *
     * @return void
     * @since 3.6.1
     * @access public
     */
 public abstract function export($type);
 public static function getExportTypes() {
  $datatypes = array("users" => _USERS,
         "users_to_courses" => _USERSTOCOURSES);
  return $datatypes;
 }
 /*
	 * Create the mappings between csv columns and db attributes
	 */
 public static function getTypes($type) {
  switch($type) {
   case "users":
    $users_info = array("users_login" => "login",
           "password" => "password",
           "users_email" => "email",
           "language" => "languages_NAME",
           "users_name" => "name",
           "users_surname" => "surname",
           "active" => "active",
           "user_type" => "user_type",
           "registration_date" => "timestamp");
    return $users_info;
   case "users_to_courses":
    return array("users_login" => "users_login",
         "courses_name" => "courses.name",
         "course_start_date" => "users_to_courses.from_timestamp",
         "course_user_type" => "users_to_courses.user_type",
         "course_completed" => "users_to_courses.completed",
         "course_comments" => "users_to_courses.comments",
         "course_score" => "users_to_courses.score",
         "course_active" => "users_to_courses.active",
         "course_end_date" => "users_to_courses.to_timestamp");
   case "users_to_groups":
    return array("users_login" => "users_login",
        "group_name" => "groups.name");
  }
 }
 public function __construct($_options) {
  $this -> options = $_options;
  if ($this -> options['date_format'] == "MM/DD/YYYY") {
   $this -> options['date_new_format'] = "m/d/Y";
  } else if ($this -> options['date_format'] == "YYYY/MM/DD") {
   $this -> options['date_new_format'] = "Y/m/d";
  } else {
   $this -> options['date_new_format'] = "d/m/Y";
  }
 }
 private $courseNamesToIds = false;
 protected function getCourseByName($courses_name) {
  if (!$courseNamesToIds) {
   $courses = EfrontCourse::getCourses();
   foreach($courses as $course) {
    if (!isset($courseNamesToIds[$course['name']])) {
     $courseNamesToIds[$course['name']] = array($course['id']);
    } else {
     $courseNamesToIds[$course['name']][] = $course['id'];
    }
   }
  }
  return $courseNamesToIds[$courses_name];
 }
 private $groupNamesToIds = false;
 protected function getGroupByName($group_name) {
  if (!$groupNamesToIds) {
   $groups = EfrontGroup::getGroups();
   foreach($groups as $group) {
    if (!isset($groupNamesToIds[$group['name']])) {
     $groupNamesToIds[$group['name']] = array($group['id']);
    } else {
     $groupNamesToIds[$group['name']][] = $group['id'];
    }
   }
  }
  return $groupNamesToIds[$group_name];
 }
 /*
	 * Convert dates of the form dd/mm/yy to timestamps
	 */
    protected function createDatesFromTimestamp($timestamp) {
        // date of event if existing, else current time
        if ($timestamp != "" && $timestamp != 0) {
   return date($this -> options['date_new_format'], $timestamp);
        } else {
         return "";
        }
    }
}
/****************************************************
 * Class used to export data from csv files
 *
 */
class EfrontExportCsv extends EfrontExport
{
 /*
	 * The separator between the file's fields
	 */
 protected $separator = false;
 /*
	 * Array containing metadata about the exported data type (db attribute names, db tables, export-file accepted column names)
	 */
 protected $types = false;
 /*
	 * Find the header line - the first non zero line of the csv that contains at least one of the export $type's column headers
	 * @param: the line of the header
	 */
 protected function setHeaderLine($type) {
  $this -> types = EfrontExport::getTypes($type);
  if ($type == "users") {
   unset($this -> types['password']);
  }
  $this -> lines[] = implode($this -> separator, array_keys($this -> types));
 }
 protected function clear() {
  $this -> lines = array();
 }
 /*
	 * Use eFront classes according to the type of export to store the data used
	 * @param line: the line of the exported file
	 * @param type: the export type
	 * @param type: the data of this line, formatted to be put directly into the eFront db
	 */
 protected function exportData($data) {
  $result = eF_getTableDataFlat("user_profile", "name", "active=1 AND type ='date'"); //Get admin-defined form fields for user registration
  $dateFields = array();
  if (!empty($result)) {
   $dateFields = $result['name'];
  }
  foreach ($data as $info) {
         unset($info['password']);
         foreach ($info as $field => $value) {
          if (!(strpos($field, "timestamp") === false) || $field=="hired_on" || $field=="left_on" || in_array($field, $dateFields)) {
           $info[$field] = $this -> createDatesFromTimestamp($value);
          }
         }
         $this -> lines[] = implode($this -> separator, $info);
     }
 }
 /*
	 * Get data to be exported
	 */
 protected function getData($type) {
  switch($type) {
   case "users":
     return eF_getTableData($type, implode(",", $this -> types), "archive = 0");
   case "users_to_courses":
     return eF_getTableData("users_to_courses JOIN courses ON courses.id = users_to_courses.courses_ID", implode(",", $this -> types), "");
   case "users_to_groups":
     return eF_getTableData("users_to_groups JOIN groups ON groups.id = users_to_groups.groups_ID", implode(",", $this -> types), "");
   return eF_getTableData($type, implode(",", $this -> types), "archive = 0");
  }
 }
 /*
	 * Write the exported file
	 */
 protected function writeFile($type) {
     if (!is_dir($GLOBALS['currentUser'] -> user['directory']."/temp")) {
         mkdir($GLOBALS['currentUser'] -> user['directory']."/temp", 0755);
     }
     file_put_contents($GLOBALS['currentUser'] -> user['directory']."/temp/efront_".$type.".csv", implode("\n", $this -> lines));
     $file = new EfrontFile($GLOBALS['currentUser'] -> user['directory']."/temp/efront_".$type.".csv");
     return $file;
 }
 /*
	 * Main exporting function
	 */
 public function export($type) {
  $this -> clear();
  $this -> setHeaderLine($type);
  $data = $this -> getData($type);
  $this -> exportData($data);
  return $this -> writeFile($type);
 }
 public function __construct($_options) {
  $this -> options = $_options;
  if ($this -> options['date_format'] == "MM/DD/YYYY") {
   $this -> options['date_new_format'] = "m/d/Y";
  } else if ($this -> options['date_format'] == "YYYY/MM/DD") {
   $this -> options['date_new_format'] = "Y/m/d";
  } else {
   $this -> options['date_new_format'] = "d/m/Y";
  }
  if (isset($this -> options['separator'])) {
   $this -> separator = $this -> options['separator'];
  } else {
   $this -> separator = ",";
  }
 }
}
/**
 * Export Factory class
 *
 * This class is used as a factory for export objects
 * <br/>Example
 * <code>
 * $exporter = EfrontExportFactory :: factory('csv', $file, $options);
 * $exporter -> export();
 * $exporter -> export('users');
 * </code>
 *
 * @package eFront
 * @version 3.6.1
 */
class EfrontExportFactory
{
    /**
     * Construct export object
     *
     * This function is used to construct an export object which can be
     * of any type: EfrontCsvExport
     *
     * <br/>Example :
     * <code>
     * $file = $filesystem -> uploadFile('upload_file');
     * $user = EfrontExportFactory :: factory('csv', $file, array("keep_duplicates" => 1);            //Use factory function to instantiate user object with login 'jdoe'
     * </code>
     *
     * @param string the type the exporter: currently only 'csv' is supported $exporterType
     * @param file $filename
     * @param options $various export options
     * @return EfrontExport an object of a class extending EfrontExport
     * @since 3.6.1
     * @access public
     * @static
     */
    public static function factory($exporterType, $options = false) {
     switch ($exporterType) {
      case 'csv' : $factory = new EfrontExportCsv($options); break;
     }
        return $factory;
    }
}
