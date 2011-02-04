<?php

/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */

/**
 * Gets all messages for a given user for a given lesson.
 * 
 * @param EFrontLessonUser $user
 * @param EFrontLesson $lesson
 * @return array
 */
function module_textme_getInbox($user, $lesson) {

    if ($user->getRole($lesson) == 'student') {
        $where = ' AND mtm.send_at < ' . mktime();
    }

    $data = eF_getTableData('module_textme_messages AS mtm ' .
                    ' INNER JOIN module_textme_recipients AS mtr ' .
                    ' ON mtm.id = mtr.messages_ID',
                    ' mtm.id, mtm.lessons_ID, mtm.users_LOGIN, mtm.text, ' .
                    ' mtm.credits, mtm.send_at, mtr.status, mtr.is_read',
                    ' mtr.is_deleted = 0 ' .
                    ' AND lessons_ID=' . $lesson->lesson['id'] .
                    ' AND mtr.users_LOGIN =\'' . $user->login . '\'' . $where);

    return $data;
}

/**
 * Returns all the recipients for a given message.
 * 
 * @param integer $message_id
 * @return return array
 */
function module_textme_getMessageRecipients($message_id) {
    $data = eF_getTableData('module_textme_recipients', '*',
                    ' messages_ID=' . $message_id);
    return $data;
}

/**
 * Returns all sms recipients for a given message
 * 
 * @param EfrontLesson $lesson
 * @param integer $message_id
 * @return array
 */
function module_textme_getSMSRecipients($lesson, $message_id) {
    $data = ef_getTableData('module_textme_recipients AS mtr' .
                    ' INNER JOIN module_textme_subscribers AS mts ' .
                    ' ON mtr.users_LOGIN = mts.users_LOGIN ' .
                    ' INNER JOIN module_textme_phonebook AS mtp ' .
                    ' ON mts.users_LOGIN = mtp.users_LOGIN',
                    ' mtp.users_LOGIN, identifier, status, mtp.mobile',
                    ' mtr.messages_ID=' . $message_id .
                    ' AND mts.lessons_ID=' . $lesson->lesson['id'] .
                    ' AND mtp.is_verified = 1');
    return $data;
}

/**
 * Returns whether or not a user can view a specific message.
 * 
 * @param EfrontLessonUser $user
 * @param EfrontLesson $lesson
 * @param integer $message_id
 * @return boolean
 */
function module_textme_canViewMessage($user, $lesson, $message_id) {

    if ($message_id == null) {
        return false;
    }

    $count = eF_countTableData('module_textme_recipients', '*',
                    ' messages_ID=' . $message_id .
                    ' AND users_LOGIN=\'' . $user->login . '\'' .
                    ' AND is_deleted=0');

    if ($count == 0) {
        return false;
    }

    $count = eF_countTableData('module_textme_messages', '*',
                    ' id=' . $message_id .
                    ' AND lessons_ID=' . $lesson->lesson['id']);

    if ($count == 0) {
        return false;
    }

    return true;
}

/**
 * Returns whether or not a user can view the recipients of a given message.
 * 
 * @param EfrontLessonUser $user
 * @param EfrontLesson $lesson
 * @param integer $message_id
 * @return boolean
 */
function module_textme_canViewMessageRecipients($user, $lesson, $message_id) {

    if ($user->getRole($lesson) != 'professor') {
        return false;
    }

    $count = eF_countTableData('module_textme_recipients', '*',
                    ' messages_ID=' . $message_id .
                    ' AND users_LOGIN=\'' . $user->login . '\'' .
                    ' AND is_deleted=0');

    if ($count == 0) {
        return false;
    }

    $count = eF_countTableData('module_textme_messages', '*',
                    ' id=' . $message_id .
                    ' AND lessons_ID=' . $lesson->lesson['id']);

    if ($count == 0) {
        return false;
    }

    return true;
}

/**
 * Returns whether or not a given user can delete a given message
 * 
 * @param EfrontLessonUser $user
 * @param integer $message_id
 * @return boolean
 */
function module_textme_canDeleteMessage($user, $message_id) {

    if ($message_id == null) {
        return false;
    }

    $count = eF_countTableData('module_textme_recipients', '*',
                    ' messages_ID=' . $message_id .
                    ' AND users_LOGIN=\'' . $user->login . '\'' .
                    ' AND is_deleted=0');

    if ($count == 0) {
        return false;
    }

    return true;
}

/**
 * Returns whether or not a given user can delete a message from all recipients's inboxes
 * 
 * @param EfrontLessonUser $user
 * @param EfrontLesson $lesson
 * @param integer $message_id
 * @return boolean
 */
function module_textme_canDeleteMessageFromRecipients($user, $lesson, $message_id) {
    if ($message_id == null) {
        return false;
    }

    if ($user->getRole($lesson) != 'professor') {
        return false;
    }

    $count = eF_countTableData('module_textme_messages', '*',
                    ' id=' . $message_id .
                    ' AND lessons_ID=' . $lesson->lesson['id']);

    if ($count == 0) {
        return false;
    }

    return true;
}

/**
 * Returns whether or not a given user can compose a message for a given lesson.
 * 
 * @param EfrontLessonUser $user
 * @param EfrontUser $lesson
 * @return integer
 */
function module_textme_canComposeMessage($user, $lesson) {
    return $user->getRole($lesson) == 'professor';
}

/**
 * Renders an HTML_QuickForm
 *
 * @param HTML_QuickForm $form
 * @param Smarty $smarty
 * @return HTML_QuickForm_Renderer_ArraySmarty
 */
function module_textme_getFormRenderer($form, $smarty) {

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $renderer->setRequiredTemplate('{$html}{if $required}&nbsp;<span class = "formRequired">*</span>{/if}');
    $renderer->setErrorTemplate('{$html}{if $error}<div class = "formError">{$error}</div>{/if}');
    $form->accept($renderer);

    return $renderer;
}

/**
 * Returns an array of all available lessons.
 * 
 * @return array
 */
function module_textme_getLessons() {

    $ef_lessons = EfrontLesson::getLessons(true);

    $lessons = array();

    foreach ($ef_lessons as $ef_lesson) {

        $settings = module_textme_getSingleRow('module_textme_lessons_settings', '*',
                        'lessons_ID=' . $ef_lesson->lesson['id']);

        if ($settings == null) {
            $settings = array(
                'lessons_ID' => $ef_lesson->lesson['id'],
                'alias' => 'EFRONT',
                'credits' => null,
                'credits_spent' => 0
            );

            eF_insertTableData('module_textme_lessons_settings', $settings);
        }

        $options = $ef_lesson->getOptions();
        $lesson['id'] = $ef_lesson->lesson['id'];
        $lesson['name'] = $ef_lesson->lesson['name'];
        $lesson['module_textme'] = ($options['module_textme'] == 1);
        $lesson['alias'] = $settings['alias'];
        $lesson['credits'] = $settings['credits'];
        $lesson['credits_spent'] = $settings['credits_spent'];

        $lessons[$lesson['id']] = $lesson;
    }

    return $lessons;
}

/**
 * Gets all the lessons that the user takes part in
 * 
 * @param EfrontLessonUser $user
 * @return array
 */
function module_textme_getUserLessons($user) {

    $ef_lessons = $user->getLessons(true);
    $subscriptions = eF_getTableDataFlat('module_textme_subscribers',
                    'lessons_ID',
                    'users_LOGIN=\'' . $user->login . '\'');

    $lessons = array();

    foreach ($ef_lessons as $ef_lesson) {

        $options = $ef_lesson->getOptions();

        if ($options['module_textme'] == 1) {

            $lesson['id'] = $ef_lesson->lesson['id'];
            $lesson['name'] = $ef_lesson->lesson['name'];
            $lesson['is_activated'] = in_array($lesson['id'], $subscriptions['lessons_ID']);
            $lessons[$lesson['id']] = $lesson;
        }
    }

    return $lessons;
}

/**
 * Gets all users for a given lesson
 * 
 * @param EfrontLesson $lesson
 * @param string $role
 * @param boolean $simple
 * @return array
 */
function module_textme_getUsers($lesson, $role = null, $simple = false) {

    $ef_users = $lesson->getUsers($role);

    $subscribers = eF_getTableDataFlat('module_textme_subscribers',
                    'users_LOGIN',
                    'lessons_ID=' . $lesson->lesson['id']);

    $users = array();

    foreach ($ef_users as $ef_user) {

        $user = array();

        if ($simple) {
            $user = formatLogin($ef_user['login']);
        } else {
            $user['login'] = $ef_user['login'];
            $user['role'] = $ef_user['role'];
            $user['login_formated'] = formatLogin($ef_user['login']);
            $user['is_subscribed'] = in_array($ef_user['login'], $subscribers['users_LOGIN']);
        }

        $users[$ef_user['login']] = $user;
    }

    return $users;
}

/**
 * Gets the users o a given group.
 * 
 * @param EfrontLesson $lesson
 * @param integer $group_id
 * @param string $choice
 * @return array
 */
function module_textme_getGroupUsers($lesson, $group_id = -1, $choice = null) {

    $users = module_textme_getUsers($lesson);

    $subscribers = eF_getTableDataFlat('module_textme_group_has_subscribers', '*', 'groups_ID=' . $group_id);

    $group_users = array();

    foreach ($users as $user) {

        $in = in_array($user['login'], $subscribers['users_LOGIN']);

        if ($choice == 'in' && $in == false) {
            continue;
        } else if ($choice == 'out' && $in == true) {
            continue;
        }

        $group_users[$user['login']] = formatLogin($user['login']);
    }

    return $group_users;
}

/**
 * Gets all groups for a given lesson.
 *
 * @param EFrontLesson $lesson
 * @param boolean $flat
 * @return array
 */
function module_textme_getGroups($lesson, $flat = false) {

    $id = $lesson->lesson['id'];

    if ($flat == true) {
        $data = eF_getTableDataFlat('module_textme_groups AS mtg LEFT JOIN module_textme_group_has_subscribers AS mtghs ON mtg.id = mtghs.groups_ID ',
                        'mtg.id, mtg.lessons_ID, mtg.name, COUNT(mtghs.groups_ID) AS count', 'mtg.lessons_ID=' . $id,
                        'mtg.name ASC',
                        'mtg.id');
        $data = array_combine($data['id'], $data['name']);
    } else {
        $data = eF_getTableData('module_textme_groups AS mtg LEFT JOIN module_textme_group_has_subscribers AS mtghs ON mtg.id = mtghs.groups_ID ',
                        'mtg.id, mtg.lessons_ID, mtg.name, COUNT(mtghs.groups_ID) AS count', 'mtg.lessons_ID=' . $id,
                        'mtg.name ASC',
                        'mtg.id');
    }

    return $data;
}

/**
 * This is a wrapper function for eFront's built in eF_getTableData function.
 * Fetches just one row of data rather than an array of rows.
 *
 * @param string $table
 * @param string $fields
 * @param string $where
 * @return array
 */
function module_textme_getSingleRow($table, $fields = '*', $where = '') {
    $results = eF_getTableData($table, $fields, $where, '', '', '1');

    return (count($results) > 0) ? $results[0] : null;
}

?>
