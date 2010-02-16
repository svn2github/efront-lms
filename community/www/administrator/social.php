<?php
    /*

     * eFront social administration

     * The social_module_activations is a bitmap, with each bit corresponding to a social functionality

     * 00000001: Events activated

     * 00000010: System timelines activated

     * 00000100: Lesson and topical timelines activated

     * 00001000: People activated

     * 00010000: People commenting activated

     * 00100000: User status deactivated

     * 01000000: Fb connect deactivated

     */
//pr($_GET);
//echo $GLOBALS['configuration']['social_modules_activated'] ."<BR>";
    if (!isset($GLOBALS['configuration']['social_modules_activated'])) {
            EfrontConfiguration :: setValue('social_modules_activated', pow(2,SOCIAL_MODULES_ALL)-1);
            $socialModulesActivated = pow(2,SOCIAL_MODULES_ALL)-1;
        } else {
            $socialModulesActivated = intval($GLOBALS['configuration']['social_modules_activated']);
        }
        if (isset($_GET['ajax'])) {
            $return_text = "1"; // default return value
            if (isset($_GET['activate']) && eF_checkSocialModuleExistance($_GET['activate'])) {
                $_GET['activate'] = intval($_GET['activate']);
                // This is sent to activate all modules
                if ($_GET['activate'] == 0) {
                    $socialModulesActivated = pow(2,SOCIAL_MODULES_ALL) -1;
                    $return_text = "4"; // to let JS know to change display of all icons to activated
                } else if (($socialModulesActivated & $_GET['activate']) == 0) {
                    // Check if this is the first module to be activated
                    if ($socialModulesActivated == 0) {
                        $return_text = "2"; // to let JS know to change the display of the "all options" icon to activated
                    }
                    $socialModulesActivated = $socialModulesActivated | $_GET['activate'];
                }
                $socialModule = $_GET['activate'];
            } elseif (isset($_GET['deactivate']) && eF_checkSocialModuleExistance($_GET['deactivate'])) {
                $_GET['deactivate'] = intval($_GET['deactivate']);
                // This is sent to deactivate all modules
                if ($_GET['deactivate'] == 0) {
                    $socialModulesActivated = 0;
                    $return_text = "5"; // to let JS know to change display of all icons to deactivated
                } else if (($socialModulesActivated & $_GET['deactivate']) > 0) {
                    $socialModulesActivated = $socialModulesActivated ^ $_GET['deactivate'];
                    // Check if after the module's deactivation no modules are enabled
                    if ($socialModulesActivated == 0) {
                        $return_text = "3"; // to let JS know to change the display of the "all options" icon to be deactivated
                    }
                }
                $socialModule = $_GET['deactivate'];
            }
            // Only a few options affect the sidebar - only they should trigger its reload
            if ($socialModule == 0 || $socialModule == SOCIAL_FUNC_SYSTEM_TIMELINES || $socialModule == SOCIAL_FUNC_PEOPLE || $socialModule == SOCIAL_FUNC_USERSTATUS) {
                $force_reload_flag = "!";
            } else {
                $force_reload_flag = "";
            }
            EfrontConfiguration::setValue("social_modules_activated", $socialModulesActivated);
            echo $return_text . $force_reload_flag;
            exit;
        }
        $socialSettings['all'] = array('text' => _ENTIRESOCIALMODULE, 'image' => ($socialModulesActivated > 0)? "32x32/theory.png" : "32x32/book_blue_gray.png", 'href' => 'javascript:void(0)', 'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \'0\')':'', 'style' => ($socialModulesActivated >0)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        $socialSettings['events'] = array('text' => _EVENTSLOGGING, 'image' => ($socialModulesActivated & SOCIAL_FUNC_EVENTS)? "32x32/theory.png" : "32x32/book_blue_gray.png", 'href' => 'javascript:void(0)', 'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''.SOCIAL_FUNC_EVENTS.'\')':'', 'style' => ($socialModulesActivated & SOCIAL_FUNC_EVENTS)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        $socialSettings['system_timelines'] = array('text' => _SYSTEMTIMELINES, 'image' => ($socialModulesActivated & SOCIAL_FUNC_SYSTEM_TIMELINES)? "32x32/theory.png" : "32x32/book_blue_gray.png", 'href' => 'javascript:void(0)', 'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''.SOCIAL_FUNC_SYSTEM_TIMELINES.'\')':'', 'style' => ($socialModulesActivated & SOCIAL_FUNC_SYSTEM_TIMELINES)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        $socialSettings['lesson_timelines'] = array('text' => _LESSONTIMELINES, 'image' => ($socialModulesActivated & SOCIAL_FUNC_LESSON_TIMELINES)? "32x32/theory.png" : "32x32/book_blue_gray.png", 'href' => 'javascript:void(0)', 'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''.SOCIAL_FUNC_LESSON_TIMELINES.'\')':'', 'style' => ($socialModulesActivated & SOCIAL_FUNC_LESSON_TIMELINES)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        $socialSettings['people'] = array('text' => _PEOPLE, 'image' => ($socialModulesActivated & SOCIAL_FUNC_PEOPLE)? "32x32/theory.png" : "32x32/book_blue_gray.png", 'href' => 'javascript:void(0)', 'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''.SOCIAL_FUNC_PEOPLE.'\')':'', 'style' => ($socialModulesActivated & SOCIAL_FUNC_PEOPLE)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        //$socialSettings['classmates']             = array('text' => _CLASSMATES, 'image' => ($socialModulesActivated & SOCIAL_FUNC_LESSON_PEOPLE)? "32x32/theory.png"     : "32x32/book_blue_gray.png",     'href' => 'javascript:void(0)',  'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''.SOCIAL_FUNC_LESSON_PEOPLE.'\')':'',          'style' => ($socialModulesActivated & SOCIAL_FUNC_LESSON_PEOPLE)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        $socialSettings['comments'] = array('text' => _COMMENTSWALL, 'image' => ($socialModulesActivated & SOCIAL_FUNC_COMMENTS)? "32x32/theory.png" : "32x32/book_blue_gray.png", 'href' => 'javascript:void(0)', 'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''.SOCIAL_FUNC_COMMENTS.'\')':'', 'style' => ($socialModulesActivated & SOCIAL_FUNC_COMMENTS)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        $socialSettings['userstatus'] = array('text' => _USERSTATUS, 'image' => ($socialModulesActivated & SOCIAL_FUNC_USERSTATUS)? "32x32/theory.png" : "32x32/book_blue_gray.png", 'href' => 'javascript:void(0)', 'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''.SOCIAL_FUNC_USERSTATUS.'\')':'', 'style' => ($socialModulesActivated & SOCIAL_FUNC_USERSTATUS)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        //$socialSettings['fbconnect']              = array('text' => _FBCONNECT , 'image' => ($socialModulesActivated & SOCIAL_FUNC_FBCONNECT)? "32x32/theory.png"     : "32x32/book_blue_gray.png",     'href' => 'javascript:void(0)',  'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''.SOCIAL_FUNC_FBCONNECT.'\')':'',          'style' => ($socialModulesActivated & SOCIAL_FUNC_FBCONNECT)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        //$socialSettings['']          = array('text' => , 'image' => ($socialModulesActivated & SOCIAL_FUNC_)? "32x32/theory.png"     : "32x32/book_blue_gray.png",     'href' => 'javascript:void(0)',  'onClick' => (!isset($currentUser -> coreAccess['social']) || (isset($currentUser -> coreAccess['social']) && $currentUser -> coreAccess['social'] == "change"))?'activate(this, \''..'\')':'',          'style' => ($socialModulesActivated & SOCIAL_FUNC_)?'color:inherit':'color:gray', 'title' => _CLICKTOTOGGLE);
        $smarty -> assign("T_SOCIAL_SETTINGS", $socialSettings);
?>
