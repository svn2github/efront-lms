<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

$loadScripts[] = 'includes/rules';
$currentContent = new EfrontContentTree($currentLesson);
$rules = $currentContent -> getRules();
$units = array();
foreach ($iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
    $units['id'][] = $value['id'];
    $units['name'][] = $value['name'];
    $units['active'][] = $value['active'];
}

$smarty -> assign("T_TREE_ACTIVE", array_combine($units['id'], $units['active']));
$smarty -> assign("T_TREE_NAMES", array_combine($units['id'], $units['name']));
$smarty -> assign("T_RULES", $rules);

$conditions = $currentLesson -> getConditions();
$condition_types = array('all_units' => _PASSEDALLUNITS,
                         'percentage_units' => _PERCENTAGEUNITS,
                         'specific_unit' => _SPECIFICUNIT,
                         'all_tests' => _PASSEDALLTESTS,
                         'specific_test' => _SPECIFICTEST
                         );
$smarty -> assign("T_LESSON_CONDITIONS", $conditions);
$smarty -> assign("T_CONDITION_TYPES", $condition_types);


//$legalValues = array_keys($rules);
//include "entity.php";

if (isset($_GET['action']) && $_GET['action'] == 'auto_complete') {
    try {
        $currentLesson -> options['auto_complete'] ? $currentLesson -> setOptions(array('auto_complete' => 0)) : $currentLesson -> setOptions(array('auto_complete' => 1));
        echo $currentLesson -> options['auto_complete'] ? 1 : 0;
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo $e -> getMessage().' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['delete_rule']) && in_array($_GET['delete_rule'], array_keys($rules))) {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    try {
        $currentContent -> deleteRules($_GET['delete_rule']);
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo $e -> getMessage().' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['add_rule']) || (isset($_GET['edit_rule']) && eF_checkParameter($_GET['edit_rule'], 'id'))) {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    isset($_GET['add_rule']) ? $post_target = 'add_rule=1' : $post_target = 'edit_rule='.$_GET['edit_rule'];

    $form = new HTML_QuickForm("add_rule_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=rules&".$post_target, "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
    $form -> registerRule('in_array', 'callback', 'in_array');

    $users = $currentLesson -> getUsers('student');
    sizeof($users) > 0 ? $users = array('*' => _ALLOFTHEM) + array_combine(array_keys($users), array_keys($users)) : $users = array('*' => _ALLOFTHEM);

    $form -> addElement('select', 'scope', null, $users, 'class = "inputSelect"');
    $form -> addRule('scope', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('scope', _INVALIDLOGIN, 'checkParameter', 'text');

    $testsIterator = new EfrontTestsFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));
    $testUnits = $currentContent -> toHTMLSelectOptions($testsIterator);

    $contentIterator = (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))); //Get active units that are anything but tests (false negates both rules)
    $noTestUnits = $currentContent -> toHTMLSelectOptions($contentIterator);

    $form -> addElement('select', 'exclusion_unit', null, $currentContent -> toHTMLSelectOptions(), 'class = "inputSelect"');
    $form -> addRule('exclusion_unit', _INVALIDID, null, 'numeric');

    $rule_type = array('always' => _ALWAYS, 'hasnot_seen' => _HASNOTSEENTHEUNIT);
    $form -> addElement('select', 'rule_type', null, $rule_type, 'class = "inputSelect" onchange = "selectRule(this)"');
    $form -> addRule('rule_type', _INVALIDRULE, 'in_array', array_keys($rule_type));

    $form -> addElement('select', 'rule_unit', null, $noTestUnits, 'class = "inputSelect"');
    $form -> addRule('rule_unit', _INVALIDID, 'numeric', null, 'client');

    $form -> addElement('select', 'test_unit', null, $testUnits, 'class = "inputSelect"');
    $form -> addRule('test_unit', _INVALIDID, 'numeric', null, 'client');

    $form -> addElement('text', 'score', null, 'style = "width:5em"');
    $form -> addRule('score', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('score', _INVALIDSCORE, 'numeric');

    $form -> addElement('submit', 'submit_rule', _SUBMIT, 'class = "flatButton"');

    if ($_GET['edit_rule']) {
        $form -> setDefaults(array('scope' => $rules[$_GET['edit_rule']]['users_LOGIN'],
                                   'exclusion_unit' => $rules[$_GET['edit_rule']]['content_ID'],
                                   'rule_type' => $rules[$_GET['edit_rule']]['rule_type'],
                                   'rule_unit' => $rules[$_GET['edit_rule']]['rule_content_ID'],
                                   'test_unit' => $rules[$_GET['edit_rule']]['rule_content_ID'],
                                   'score' => $rules[$_GET['edit_rule']]['rule_option'] * 100));
        $smarty -> assign("T_CURRENT_RULE", $rules[$_GET['edit_rule']]['rule_type']);
    } else {
        $form -> setDefaults(array('score' => 50));
    }

    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            $fields = array('users_LOGIN' => $form -> exportValue('scope'),
                            'content_ID' => $form -> exportValue('exclusion_unit'),
                            'lessons_ID' => $currentLesson -> lesson['id']);

            switch ($form -> exportValue('rule_type')) {
                case 'always':
                    $fields['rule_type'] = 'always';
                    break;
                case 'hasnot_seen':
                    $fields['rule_type'] = 'hasnot_seen';
                    $fields['rule_content_ID'] = $form -> exportValue('rule_unit');
                    break;
                case 'hasnot_passed':
                    $fields['rule_type'] = 'hasnot_passed';
                    $fields['rule_content_ID'] = $form -> exportValue('test_unit');
                   $fields['rule_option'] = round($form -> exportValue('score') / 100, 2);
                  break;
                default:
                    break;
            }

            if (isset($_GET['edit_rule'])) {
                if (eF_updateTableData("rules", $fields, "id=".$_GET['edit_rule'])) {
                    $message = _SUCCESFULLYUPDATEDRULE;
                    $message_type = 'success';
                    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=rules&message=".$message."&message_type=".$message_type);
                } else {
                    $message = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            } else {
                if (eF_insertTableData("rules", $fields)) {
                    $message = _SUCCESFULLYINSERTEDRULE;
                    $message_type = 'success';
                    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=rules&message=".$message."&message_type=".$message_type);
                } else {
                    $message = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            }
        }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_ADD_RULE_FORM', $renderer -> toArray());

    $form = new HTML_QuickForm("add_ready_rule_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=rules&add_rule=1", "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
    $form -> addElement('radio', 'ready_rule', _SERIALRULE, null, 'serial', "checked");
    $form -> addElement('radio', 'ready_rule', _TREERULE, null, 'tree');
    $form -> addElement('submit', 'submit_ready_rule', _SUBMIT, 'class=flatButton');

    if ($form -> isSubmitted() && $form -> validate()) {
        $fields = array('users_LOGIN' => '*',
                        'content_ID' => 0,
                        'lessons_ID' => $currentLesson -> lesson['id']);
        switch ($form -> exportValue('ready_rule')) {
            case 'tree':
                $fields['rule_type'] = 'tree';
                break;
            case 'serial': default:
                $fields['rule_type'] = 'serial';
                break;
        }
        if (eF_insertTableData("rules", $fields)) {
            $message = _SUCCESFULLYINSERTEDRULE;
            $message_type = 'success';
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=rules&message=".$message."&message_type=".$message_type);
        } else {
            $message = _SOMEPROBLEMEMERGED;
            $message_type = 'failure';
        }

    }
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> accept($renderer);
    $smarty -> assign('T_ADD_READY_RULE_FORM', $renderer -> toArray());

} elseif (isset($_GET['delete_condition']) && in_array($_GET['delete_condition'], array_keys($conditions))) {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    try {
        $currentLesson -> deleteConditions($_GET['delete_condition']);
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo $e -> getMessage().' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['add_condition']) || (isset($_GET['edit_condition']) && eF_checkParameter($_GET['edit_condition'], 'id'))) {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    isset($_GET['add_condition']) ? $post_target = 'add_condition=1' : $post_target = 'edit_condition='.$_GET['edit_condition'];

    $form = new HTML_QuickForm("complete_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=rules&tab=conditions&'.$post_target, "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
    $form -> registerRule('in_array', 'callback', 'in_array');

    $testsIterator = new EfrontTestsFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));
    $testUnits = $currentContent -> toHTMLSelectOptions($testsIterator);

    $contentIterator = new EfrontContentFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))); //Get active units that are anything but tests (false negates both rules)
    $noTestUnits = $currentContent -> toHTMLSelectOptions($contentIterator);

    if (!empty($noTestUnits)) {
     $form -> addElement('select', 'specific_unit', null, $noTestUnits, 'class = "inputSelect"');
     $form -> addRule('specific_unit', _INVALIDID, 'numeric', null, 'client');
    } else {
        unset($condition_types['specific_unit']);
    }

    if (!empty($testUnits) && $GLOBALS['configuration']['disable_tests'] != 1) {
     $form -> addElement('select', 'specific_test', null, $testUnits, 'class = "inputSelect"');
     $form -> addRule('specific_test', _INVALIDID, 'numeric', null, 'client');
    } else {
        unset($condition_types['specific_test']);
    }

    $form -> addElement('select', 'condition_types', null, $condition_types, 'class = "inputSelect" onchange = "selectCondition(this)"');
    $form -> addRule('condition_types', _INVALIDCONDITION, 'in_array', array_keys($condition_types));

    $form -> addElement('text', 'percentage_units', null, 'style = "width:2.5em"');
    $form -> setDefaults(array('percentage_units' => 50));
    $form -> addRule('percentage_units', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('percentage_units', _INVALIDPERCENTAGE, 'numeric');

    $form -> addElement('select', 'relation', null, array('and' => _AND, 'or' => _OR));

    $form -> addElement('submit', 'submit_complete_lesson_condition', _SUBMIT, 'class = "flatButton"');

    if (isset($_GET['edit_condition'])) {
        $smarty -> assign('T_CURRENT_CONDITION', $conditions[$_GET['edit_condition']]);
        $form -> setDefaults(array('condition_types' => $conditions[$_GET['edit_condition']]['type'], 'relation' => $conditions[$_GET['edit_condition']]['relation']));
        $form -> freeze('condition_types');

        $options = $conditions[$_GET['edit_condition']]['options'];
        switch ($conditions[$_GET['edit_condition']]['type']) {
            case 'percentage_units': $defaults = array('percentage_units' => $options[0]); break;
            case 'specific_unit': $defaults = array('specific_unit' => $options[0]); break;
            //case 'all_tests':        $defaults = array('all_tests'        => $options[0]); break;
            //case 'specific_test':    $defaults = array('specific_test'    => $options[0]); break;
            default: break;
        }
        $form -> setDefaults($defaults);
    }

    if ($form -> isSubmitted()) {
        if ($form -> exportValue('condition_types') == 'percentage_units' && ($form -> exportValue('percentage_units') < 1 || $form -> exportValue('percentage_units') > 100)) {
            $message = _PERCENTAGEMUSTBEBETWEEN1100;
            $message_type = 'failure';
        } elseif ($form -> validate()) {
            $fields = array('lessons_ID' => $_SESSION['s_lessons_ID'],
                            'type' => $form -> exportValue('condition_types'),
                            'relation' => $form -> exportValue('relation'));

            switch ($form -> exportValue('condition_types')) {
                case 'percentage_units': $fields['options'] = serialize(array(0 => $form -> exportValue('percentage_units'))); break;
                case 'specific_unit': $fields['options'] = serialize(array(0 => $form -> exportValue('specific_unit'))); break;
                //case 'all_tests':        $fields['options'] = serialize(array(0 => $form -> exportValue('all_tests')));           break;
                case 'specific_test': $fields['options'] = serialize(array(0 => $form -> exportValue('specific_test'))); break;
                default: break;
            }

            if (isset($_GET['add_condition'])) {
                if (eF_insertTableData('lesson_conditions', $fields)) {
                    $message = _SUCCESFULLYADDEDCONDITION;
                    $message_type = 'success';
                    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=rules&tab=conditions&message=".$message."&message_type=".$message_type);
                } else {
                    $message = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            } else {
                if (eF_updateTableData('lesson_conditions', $fields, "id=".$_GET['edit_condition'])) {
                    $message = _SUCCESFULLYUPDATEDCONDITION;
                    $message_type = 'success';
                    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=rules&tab=conditions&message=".$message."&message_type=".$message_type);
                } else {
                    $message = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            }
        }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_COMPLETE_LESSON_FORM', $renderer -> toArray());
}

$moduleTabs = array();
foreach (eF_loadAllModules(true, true) as $module) {
 if ($moduleTab = $module -> getTabSmartyTpl('rules')) {
  $moduleTabs[] = $moduleTab;
 }
}
$smarty -> assign("T_MODULE_RULES_TABS", $moduleTabs);
