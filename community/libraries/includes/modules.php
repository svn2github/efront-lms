<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$loadScripts[] = 'includes/modules';

if (isset($currentUser -> coreAccess['modules']) && $currentUser -> coreAccess['modules'] == 'hidden') {
    eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}


try {
    if (isset($_GET['delete_module']) && eF_checkParameter($_GET['delete_module'], 'filename')) {
        if (isset($currentUser -> coreAccess['modules']) && $currentUser -> coreAccess['modules'] != 'change') {
            throw new EfrontSystemException(_UNAUTHORIZEDACCESS, EfrontSystemException::UNAUTHORIZED_ACCESS);
        }
        $smarty -> assign("T_REFRESH_SIDE", true);
        $lesson_options = eF_getTableData("lessons", "options, id");
        foreach ($lesson_options as $value) {
            if ($value['options']) {
                $options = unserialize($value['options']);
                if (in_array($_GET['delete_module'], array_keys($options))) {
                    unset($options[$_GET['delete_module']]);
                    eF_updateTableData("lessons", array('options' => serialize($options)), "id=".$value['id']);
                }
            }
        }

        $className = $_GET['delete_module'];
        $module_folder_position = eF_getTableData("modules", "position", "className='". $className."'");

        $folder = $module_folder_position[0]['position'];
        require_once G_MODULESPATH.$folder."/".$className.".class.php";

        if (class_exists($className)) {
            $module = new $className("administrator.php?ctg=module&op=".$className, $folder);
            try {
             $module -> onUninstall();
            } catch (Exception $e) {/*Do nothing*/}
        }

        // PROBLEM: if the folder is open and cannot be deleted then the module cannot be reinstalled
        $folder = new EfrontDirectory(G_MODULESPATH.$folder.'/');
        $folder -> delete();
        eF_deleteTableData("modules", "className='".$className."'");

        exit;
    } elseif(isset($_GET['activate_module']) && eF_checkParameter($_GET['activate_module'], 'filename')) {
        if (isset($currentUser -> coreAccess['modules']) && $currentUser -> coreAccess['modules'] != 'change') {
            throw new EfrontSystemException(_UNAUTHORIZEDACCESS, EfrontSystemException::UNAUTHORIZED_ACCESS);
        }
        eF_updateTableData("modules", array("active" => 1), "className = '".$_GET['activate_module']."'");
        echo "1";
        exit;
    } elseif(isset($_GET['deactivate_module']) && eF_checkParameter($_GET['deactivate_module'], 'filename')) {
        if (isset($currentUser -> coreAccess['modules']) && $currentUser -> coreAccess['modules'] != 'change') {
            throw new EfrontSystemException(_UNAUTHORIZEDACCESS, EfrontSystemException::UNAUTHORIZED_ACCESS);
        }
        eF_updateTableData("modules", array("active" => 0), "className = '".$_GET['deactivate_module']."'");
        echo "0";
        exit;
    } elseif(isset($_GET['install_module']) && eF_checkParameter($_GET['install_module'], 'filename')) {
        $module_folder = $_GET['install_module'];
        $module_position = $module_folder;

        if (is_file(G_MODULESPATH.$module_folder.'/module.xml')) {

         $xml = simplexml_load_file(G_MODULESPATH.$module_folder.'/module.xml');

         $className = (string)$xml -> className;
         $className = str_replace(" ", "", $className);
         $database_file = (string)$xml -> database;
         if (is_file(G_MODULESPATH.$module_folder.'/'.$className. ".class.php")) {
          $module_exists = 0;

          // Do not check for module existence if the module is to be upgraded
          if (!isset($_GET['upgrade'])) {
           foreach ($modulesList as $module) {
            if ($module['className'] == $className) {
             $module_exists = 1;
            }
           }
          }

          if ($module_exists == 0) {

           require_once G_MODULESPATH.$module_folder."/".$className.".class.php";

           if (class_exists($className)) {
            $module = new $className("administrator.php?ctg=module&op=".$className, $className);

            // Check whether the roles defined are acceptable
            $roles = $module -> getPermittedRoles();
            $roles_failure = 0;
            if (sizeof($roles) == 0) {
             $message = _NOMODULEPERMITTEDROLESDEFINED;
             $message_type = 'failure';
             $roles_failure = 1;
            } else {
             foreach ($roles as $role) {
              if ($role != 'administrator' && $role != 'student' && $role != 'professor') {
               $message = _PERMITTEDROLESMODULEERROR;
               $message_type = 'failure';
               $roles_failure = 1;
              }
             }
            }

            if ($roles_failure) {
             $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
             $dir -> delete();
            } else {

             $fields = array('className' => $className,
                                                             'db_file' => $database_file,
                                                             'name' => $className,
                                                             'active' => 1,
                                                             'title' => ((string)$xml -> title)?(string)$xml -> title:" ",
                                                             'author' => (string)$xml -> author,
                                                             'version' => (string)$xml -> version,
                                                             'description' => (string)$xml -> description,
                                                             'position' => $module_position,
                                                             'permissions' => implode(",", $module -> getPermittedRoles()));


             if (!isset($_GET['upgrade'])) {
              // Install module database
              if ($module -> onInstall()) {
               if (eF_insertTableData("modules", $fields)) {
                $message = _MODULESUCCESFULLYINSTALLED;
                $message_type = 'success';
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=modules&message=".urlencode($message)."&message_type=".$message_type."&refresh_side=1");
               } else {
                $module -> onUninstall();
                $message = _PROBLEMINSERTINGPARSEDXMLVALUESORMODULEEXISTS;
                $message_type = 'failure';

                $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
                $dir -> delete();
                //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
               }
              } else {
               $message = _MODULEDBERRORONINSTALL;
               $message_type = 'failure';

               $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
               $dir -> delete();
               //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
              }
             } else {

              // If the module is to be installed to a different than the existing folder that
              // already exists (like the directory name of another module) then the upgrade should
              // be aborted

              // If everything went ok, then upgrade the module
              if ($module -> onUpgrade()) {

               // If the upgrade is successful, then update the modules table
               if (eF_updateTableData("modules", $fields, "className ='".$_GET['upgrade']."'")) {

                // Delete the existing module folder
                $message = _MODULESUCCESFULLYUPGRADED;
                $message_type = 'success';
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=modules&message=".urlencode($message)."&message_type=".$message_type);
               } else {
                $message = _PROBLEMINSERTINGPARSEDXMLVALUESORMODULEEXISTS;
                $message_type = 'failure';
               }

              } else {
               $message = _MODULEDBERRORONUPGRADECHECKUPGRADEFUNCTION;
               $message_type = 'failure';
              }
             }
            }
           } else {
            $message = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$module_folder.'/'.$className.'.class.php';
            $message_type = 'failure';
            $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
            $dir -> delete();
            //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
           }
          } else {
           $message = '"'.$className .'": '. _MODULEISALREADYINSTALLED;
           $message_type = 'failure';
           //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
           $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
           $dir -> delete();
          }
         } else {
          $message = _NOMODULECLASSFOUND . ' "'. $className .'" : '.G_MODULESPATH.$module_folder;
          $message_type = 'failure';
          //$dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
          //$dir -> delete();
          //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
         }
        }
    }
} catch (Exception $e) {
 handleAjaxExceptions($e);
}

$modulesList = eF_getTableData("modules", "*");

// Check for errors in modules
foreach ($modulesList as $key => $module) {
    $folder = $module['position'];
    $className = $module['className'];
    $permissions = explode(",", $module['permissions']);

    // Check if module folder exists
    $modulesList[$key]['folder_exists'] = is_dir(G_MODULESPATH.$folder);
    if (!$modulesList[$key]['folder_exists']) {
        $modulesList[$key]['errors'] = _THISFOLDERDOESNOTEXIT . ": " . G_MODULESPATH . $folder;
    } else {
        // Check if module class exists
        $modulesList[$key]['class_exists'] = is_file(G_MODULESPATH. $folder.'/'.$className. ".class.php");
        if (!$modulesList[$key]['class_exists']) {
            $modulesList[$key]['errors'] = _NOMODULECLASSFOUND . ' "'. $className .'" : '.G_MODULESPATH. $folder .'/'.$className. ".class.php";
        } else {
            // The module class can be instantiated if the module is not to be upgraded now
            if ($_GET['upgrade'] != $className) {
                if (!isset($loadedModules[$className])) {
                    // Include module definition file if it hasn't been included yet
                    require_once G_MODULESPATH.$folder."/".$className.".class.php";
                }

                if (class_exists($className)) {
                    try {
                     $moduleInstance = new $className($user_type.".php?ctg=module&op=".$className, $folder);
                     if (!$moduleInstance -> diagnose($error)) {
                      $modulesList[$key]['errors'] = $error;
                     }
                    } catch (Exception $e) {
                     $modulesList[$key]['errors'] = $e -> getMessage();
                    }
                } else {
                    $message = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$folder.'/'.$className.'.class.php';
                    $message_type = 'failure';
                }
            }
        }
    }
    $existingModules[] = $folder;
}


//THESE LINES ARE HERE FOR FUTURE SUPPORT OF AUTOMATICALLY INSTALLING MODULES WHICH FOLDERS EXIST
$modulesFolder = new FilesystemTree(G_MODULESPATH, true);
foreach (new EfrontDirectoryOnlyFilterIterator($modulesFolder->tree) as $value) {
 if (!in_array($value['name'], $existingModules)) {
  $modulesList[] = array('className' => $value['name'], 'not_installed' => 1, 'errors' => _MODULEFILESPRESENTNOTINSTALLED);
 }
}


$smarty -> assign("T_MODULES", $modulesList);

$upload_form = new HTML_QuickForm("upload_file_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=modules', "", null, true);
$upload_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
$upload_form -> addElement('file', 'file_upload[0]', null, 'class = "inputText"');
$upload_form -> addElement('checkbox', 'overwrite', _OVERWRITEIFFOLDEREXISTS);
$upload_form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB
$upload_form -> addElement('submit', 'submit_upload_file', _UPLOAD, 'class = "flatButton"');

if ($upload_form -> isSubmitted() && $upload_form -> validate()) {
    $filesystem = new FileSystemTree(G_MODULESPATH);
    $uploadedFile = $filesystem -> uploadFile('file_upload', G_MODULESPATH, 0);

    if (isset($_GET['upgrade'])) {
        $prev_module_version = eF_getTableData("modules", "position", "className = '".$_GET['upgrade']."'");
        $prev_module_folder = $prev_module_version[0]['position'];

        // The name of the temp folder to extract the new version of the module
        $module_folder = $prev_module_folder; //basename($filename[0], '.zip') . time();
        $module_position = $prev_module_folder;//basename($filename[0], '.zip');

    } else {
        $module_folder = basename($uploadedFile['path'], '.zip');
        $module_position = $module_folder;
    }

    if (is_dir(G_MODULESPATH.$module_folder) && !isset($_GET['upgrade']) && !isset($_POST['overwrite'])) {
        $message = _FOLDERWITHMODULENAMEEXISTSIN . G_MODULESPATH;
        $message_type = 'failure';
    } else {
        $zip = new ZipArchive;
        if ($zip -> open($uploadedFile['path']) === TRUE) {
            $zip -> extractTo(G_MODULESPATH.$module_folder);
            $zip -> close();

            if (is_file(G_MODULESPATH.$module_folder.'/module.xml')) {

                $xml = simplexml_load_file(G_MODULESPATH.$module_folder.'/module.xml');

                $className = (string)$xml -> className;
                $className = str_replace(" ", "", $className);
                $database_file = (string)$xml -> database;
                if (is_file(G_MODULESPATH.$module_folder.'/'.$className. ".class.php")) {
                    $module_exists = 0;

                    // Do not check for module existence if the module is to be upgraded
                    if (!isset($_GET['upgrade'])) {
                        foreach ($modulesList as $module) {
                            if ($module['className'] == $className && in_array($module_folder, $existingModules)) {
                                $module_exists = 1;
                            }
                        }
                    }

                    if ($module_exists == 0) {

                        require_once G_MODULESPATH.$module_folder."/".$className.".class.php";

                        if (class_exists($className)) {
                            $module = new $className("administrator.php?ctg=module&op=".$className, $className);

                            // Check whether the roles defined are acceptable
                            $roles = $module -> getPermittedRoles();
                            $roles_failure = 0;
                            if (sizeof($roles) == 0) {
                                $message = _NOMODULEPERMITTEDROLESDEFINED;
                                $message_type = 'failure';
                                $roles_failure = 1;
                            } else {
                                foreach ($roles as $role) {
                                    if ($role != 'administrator' && $role != 'student' && $role != 'professor') {
                                        $message = _PERMITTEDROLESMODULEERROR;
                                        $message_type = 'failure';
                                        $roles_failure = 1;
                                    }
                                }
                            }

                            if ($roles_failure) {
                             $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
        $dir -> delete();
                            } else {

                                $fields = array('className' => $className,
                                                             'db_file' => $database_file,
                                                             'name' => $className,
                                                             'active' => 1,
                                                             'title' => ((string)$xml -> title)?(string)$xml -> title:" ",
                                                             'author' => (string)$xml -> author,
                                                             'version' => (string)$xml -> version,
                                                             'description' => (string)$xml -> description,
                                                             'position' => $module_position,
                                                             'permissions' => implode(",", $module -> getPermittedRoles()));


                                if (!isset($_GET['upgrade'])) {
                                    // Install module database
                                    if ($module -> onInstall()) {
                                        if (eF_insertTableData("modules", $fields)) {
                                            $message = _MODULESUCCESFULLYINSTALLED;
                                            $message_type = 'success';
                                            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=modules&message=".urlencode($message)."&message_type=".$message_type."&refresh_side=1");
                                        } else {
                                            $module -> onUninstall();
                                            $message = _PROBLEMINSERTINGPARSEDXMLVALUESORMODULEEXISTS;
                                            $message_type = 'failure';

                                $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
           $dir -> delete();
                                            //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                                        }
                                    } else {
                                        $message = _MODULEDBERRORONINSTALL;
                                        $message_type = 'failure';

                               $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
          $dir -> delete();
                                        //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                                    }
                                } else {

                                    // If the module is to be installed to a different than the existing folder that
                                    // already exists (like the directory name of another module) then the upgrade should
                                    // be aborted

                                    // If everything went ok, then upgrade the module
                                    if ($module -> onUpgrade()) {

                                        // If the upgrade is successful, then update the modules table
                                        if (eF_updateTableData("modules", $fields, "className ='".$_GET['upgrade']."'")) {

                                            // Delete the existing module folder
                                            $message = _MODULESUCCESFULLYUPGRADED;
                                            $message_type = 'success';
                                            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=modules&message=".urlencode($message)."&message_type=".$message_type);
                                        } else {
                                            $message = _PROBLEMINSERTINGPARSEDXMLVALUESORMODULEEXISTS;
                                            $message_type = 'failure';
                                        }

                                    } else {
                                        $message = _MODULEDBERRORONUPGRADECHECKUPGRADEFUNCTION;
                                        $message_type = 'failure';
                                    }
                                }
                            }
                        } else {
                            $message = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$module_folder.'/'.$className.'.class.php';
                            $message_type = 'failure';
                            $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
       $dir -> delete();
                            //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                        }
                    } else {
                        $message = '"'.$className .'": '. _MODULEISALREADYINSTALLED;
                        $message_type = 'failure';
                        //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                        $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
      $dir -> delete();
                    }
                } else {
                    $message = _NOMODULECLASSFOUND . ' "'. $className .'" : '.G_MODULESPATH.$module_folder;
                    $message_type = 'failure';
                    //$dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
     //$dir -> delete();
                    //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                }
            } else if (!is_dir(G_MODULESPATH.$module_folder)) {
                $message = _THISFOLDERDOESNOTEXIT.': '.G_MODULESPATH.$module_folder;
                $message_type = 'failure';
            } else {
                $message = _DESCRIPTIONFILECOULDNOTBEFOUND;
                $message_type = 'failure';
                $dir = new EfrontDirectory(G_MODULESPATH.$module_folder.'/');
    $dir -> delete();
                //eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
            }
        } else {
            $message = _COULDNOTOPENZIPFILE;
            $message_type = 'failure';
        }
    }

}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$upload_form -> accept($renderer);

$smarty -> assign('T_UPLOAD_FILE_FORM', $renderer -> toArray());
