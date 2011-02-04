<?php
/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */

if ($this->ajax) { /* Ajax request for table data */

    if ($this->ajax == 'inbox-table') {
        $items = module_textme_getInbox($this->user, $this->lesson);
    } else if ($this->ajax == 'recipients-table' &&
            module_textme_canViewMessageRecipients($this->user, $this->lesson, $this->item)) {
        $items = module_textme_getMessageRecipients($this->item);
    }

    $items_count = sizeof($items);

    if ($this->filter) {
        $items = eF_filterData($items, $this->filter);
    }

    $items = eF_multiSort($items, $this->sort, $this->order);
    $items = array_slice($items, $this->offset, $this->limit);

    $this->smarty->assign('T_TEXTME_ITEMS', $items);
    $this->smarty->assign('T_TEXTME_ITEMS_COUNT', $items_count);

} else if ($this->subcategory == 'view') { /* View a single notification */

    /* Check if user is eligible to view message */
    if (module_textme_canViewMessage($this->user, $this->lesson, $this->item) == false) {
        eF_redirect($this->moduleBaseUrl .
                '&cat=inbox&message_type=failure&message=' .
                rawurlencode(_TEXTME_ANERROROCCURED));
    }

    /* We need to retrieve delivery statuses for message recipients */
    $gateway_params = module_textme_getSingleRow('module_textme_gateways', '*', 'is_active=1');
    $gateway = Module_TextMe_SmsGatewayFactory::getGateway($gateway_params);

    if ($gateway && $gateway->supportsDeliveryReports() &&
            module_textme_canViewMessageRecipients($this->user, $this->lesson, $this->item)) {
        /* Get only those recipients that have subscribed to sms notifications */
        $recipients = module_textme_getSMSRecipients($this->lesson, $this->item);
    }

    /* Query gateway for delivery statuses */
    foreach ($recipients as $recipient) {
        if ($recipient['status'] != 'pending') {
            continue;
        }

        try {
            /* Query gateway for delivery status of given recipient */
            $status = $gateway->query($recipient['identifier'],
                            $recipient['mobile']);
            eF_updateTableData('module_textme_recipients',
                    array('status' => $status),
                    ' messages_ID=' . $this->item .
                    ' AND users_LOGIN=\'' . $recipient['users_LOGIN'] . '\'');
        } catch (Exception $exc) {
            /* No need to do anything. Just fail with grace! */
        }
    }

    /* Mark message as read */
    eF_updateTableData('module_textme_recipients',
            array('is_read' => 1),
            ' messages_ID=' . $this->item .
            ' AND users_LOGIN=\'' . $this->user->login . '\'');

    /* Get requested message */
    $message = module_textme_getSingleRow('module_textme_messages', '*',
                    'id=' . $this->item);
    $this->smarty->assign('T_TEXTME_MESSAGE', $message);
} else if ($this->subcategory == 'compose') { /* Compose a new notification */

    if (module_textme_canComposeMessage($this->user, $this->lesson) == false) {
        eF_redirect($this->moduleBaseUrl . '&cat=inbox&message_type=failure&message=' .
                rawurlencode(_TEXTME_ANERROROCCURED));
    }

    /* Get active gateway if any */
    $params = module_textme_getSingleRow('module_textme_gateways', '*', 'is_active=1');
    $gateway = Module_TextMe_SmsGatewayFactory::getGateway($params);

    if ($gateway && $gateway->supportsScheduledMessages()) {
        $this->smarty->assign('T_TEXTME_SCHEDULEDSMS', true);
    }

    $settings = module_textme_getSingleRow('module_textme_lessons_settings',
                    '*', 'lessons_ID=' . $this->lesson->lesson['id']);
    $this->smarty->assign('T_TEXTME_SETTINGS', $settings);

    $form = module_textme_AddSmsAlertForm($this->moduleBaseUrl . '&cat=inbox&subcat=compose', $this->lesson);

    if ($form->isSubmitted() && $form->validate()) {

        $recipients = module_textme_getRecipients(
                        $form->exportValue('recipients'),
                        $this->user->login,
                        $this->lesson,
                        $form->getElementValue('users'),
                        $form->getElementValue('groups'));

        $schedule_choice = $form->exportValue('schedule');
        $date = $form->exportValue('date');

        if ($schedule_choice == 'now') {
            $send_at = mktime();
        } else {
            $send_at = mktime($date['H'], $date['i'], 0,
                            $date['M'], $date['d'], $date['Y']);
        }

        $text = $form->exportValue('message');

        /* Store message to database */
        $message = array(
            'lessons_ID' => $this->lesson->lesson['id'],
            'users_LOGIN' => $this->user->login,
            'text' => $text,
            'credits' => 0,
            'send_at' => $send_at,
        );

        $id = eF_insertTableData('module_textme_messages', $message);

        /* Store message recipients to database */
        foreach ($recipients as $index => $login) {
            $recipient = array(
                'messages_ID' => $id,
                'users_LOGIN' => $login,
                'identifier' => '',
                'status' => 'local',
                'is_read' => 0,
                'is_deleted' => 0
            );
            eF_insertTableData('module_textme_recipients', $recipient);
        }

        /* Try to send messages to recipients or fail gracefully */
        if ($gateway) {

            $recipients = module_textme_getSMSRecipients($this->lesson, $id);
            try {
                $reports = $gateway->send($settings['alias'], $message['text'], $recipients, $message['send_at']);
            } catch (Exception $exc) {
                $failure = true;
            }
        } else {
            $failure = true;
        }

        /* Update recipients with the data returned by gateway */
        foreach ($reports as $report) {
            eF_updateTableData('module_textme_recipients',
                    array('status' => 'pending', 'identifier' => $report['id']),
                    ' messages_ID=' . $id .
                    ' AND users_LOGIN=\'' . $report['users_LOGIN'] . '\'');
        }

        /* Calculate credits spent and update intrested tables */
        $credits = module_textme_calculateCredits($text, count($reports));
        if ($settings['credits'] != null) {
            $settings['credits'] = $settings['credits'] - $credits;
        }
        $settings['credits_spent'] = $settings['credits_spent'] + $credits;
        eF_updateTableData('module_textme_lessons_settings', $settings,
                'lessons_ID=' . $this->lesson->lesson['id']);
        eF_updateTableData('module_textme_messages',
                array('credits' => $credits), 'id=' . $id);

        if (isset($failure) && $failure == true) {
            eF_redirect($this->moduleBaseUrl . '&cat=inbox&message_type=failure&message=' .
                    rawurlencode(_TEXTME_MESSAGESENTPROBLEM));
        } else {
            eF_redirect($this->moduleBaseUrl . '&cat=inbox&message_type=success&message=' .
                    rawurlencode(_TEXTME_MESSAGESENT));
        }
    }

    $renderer = module_textme_getFormRenderer($form, $this->smarty);
    $this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());
} else {

    switch ($this->command) {
        case 'delete':

            if (module_textme_canDeleteMessage($this->user, $this->item) == false) {
                eF_redirect($this->moduleBaseUrl . '&cat=inbox&message_type=failure&message=' .
                        rawurlencode(_TEXTME_ANERROROCCURED));
            }

            eF_updateTableData('module_textme_recipients', array('is_deleted' => 1),
                    'messages_ID=' . $this->item . ' AND users_LOGIN=\'' . $this->user->login . '\'');

            $this->setMessageVar(_TEXTME_MESSAGEDELETED, 'success');

            break;
        case 'delete-all':

            if (module_textme_canDeleteMessageFromRecipients($this->user, $this->lesson, $this->item) == false) {
                eF_redirect($this->moduleBaseUrl . '&cat=inbox&message_type=failure&message=' .
                        rawurlencode(_TEXTME_ANERROROCCURED));
            }

            eF_updateTableData('module_textme_recipients',
                    array('is_deleted' => 1),
                    'messages_ID=' . $this->item);

            $this->setMessageVar(_TEXTME_MESSAGEDELETED, 'success');

            break;
    }
}

/**
 * Returns the form for composing a new TextMe alert.
 * 
 * @param string $url
 * @param EFrontLesson $lesson
 * @return HTML_QuickForm
 */
function module_textme_AddSmsAlertForm($url, $lesson) {

    /* Create the new form */
    $form = new HTML_QuickForm('module_textme_compose_form', 'post', $url, '', null, true);

    /* Register custom validators */
    $form->registerRule('isValidDate', 'callback', 'module_textme_isValidDate');
    $form->registerRule('isDateInFuture', 'callback', 'module_textme_isDateInFuture');
    $form->registerRule('hasEnoughCredits', 'callback', 'module_textme_hasEnoughCredits');

    /* Create recipients field */
    $options = array(
        'all' => _TEXTME_EVERYONE,
        'professors' => _TEXTME_PROFESSORSONLY,
        'students' => _TEXTME_STUDENTSONLY,
        'select' => _TEXTME_SELECT);
    $form->addElement('select', 'recipients', _TEXTME_RECIPIENTS,
            $options, 'id="module_textme_recipients" class = "textme-width"');

    /* Create users field */
    $options = module_textme_getUsers($lesson, null, true);
    $form->addElement('select', 'users', _TEXTME_USERS, $options, 'class = "textme-width"')
            ->setMultiple(true);

    /* Create groups field */
    $options = module_textme_getGroups($lesson, true);
    $form->addElement('select', 'groups', _TEXTME_GROUPS, $options, 'class = "textme-width"')
            ->setMultiple(true);

    /* Create message field */
    $form->addElement('textarea', 'message', _TEXTME_MESSAGE, 'class = "textme-width" rows="6"');

    /* Create schedule field */
    $options = array('now' => _TEXTME_NOW, 'later' => _TEXTME_SELECTDATE);
    $form->addElement('select', 'schedule', _SEND, $options,
            'id="module_textme_schedule" class = "textme-width"');

    /* Create date field */
    $options = array('format' => 'dMYHi', 'minYear' => date('Y'), 'maxYear' => date('Y') + 1);
    $form->addElement('date', 'date', _DATE, $options);

    /* Create submit button */
    $form->addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');

    /* Set default values for fields */
    $defaults = array('date' => mktime());
    $form->setDefaults($defaults);

    /* Attach validators for fields */
    $form->addRule('recipients', _TEXTME_NOCREDITSLEFT, 'hasEnoughCredits', $lesson);
    $form->addRule('message', _THEFIELD.' "'._TEXTME_MESSAGE.'" '._ISMANDATORY, 'required');
    $form->applyFilter('message', 'trim');

    if ($form->exportValue('schedule') == 'later') {
        $form->addRule('date', _TEXTME_INVALIDDATE, 'isValidDate');
        $form->addRule('date', _TEXTME_INVALIDDATE, 'isDateInFuture');
    }

    return $form;
}

/**
 * Gets the recipients of a message.
 *
 * @param string $choice
 * @param string $lesson
 * @param array $users
 * @param array $groups
 * @return array
 */
function module_textme_getRecipients($choice, $sender, $lesson, $users = array(), $groups = array()) {
    $recipients = array();

    switch ($choice) {
        case 'all':
            $recipients = array_keys(module_textme_getUsers($lesson));
            break;
        case 'professors':
            $recipients = array_keys(module_textme_getUsers($lesson, 'professor'));
            break;
        case 'students':
            $recipients = array_keys(module_textme_getUsers($lesson, 'student'));
            break;
        case 'select':
            $users = ($users == null) ? array(): $users;
            $groups = ($groups == null) ? array() : $groups;

            if (count($groups) > 0) {
                $subscribers = eF_getTableDataFlat(
                                'module_textme_group_has_subscribers',
                                'users_LOGIN',
                                'groups_ID IN (' . implode(',', $groups) . ')');

                $group_users = $subscribers['users_LOGIN'];
            }
            else {
                $group_users = array();
            }

            $recipients = array_merge($users, $group_users);
            break;
    }

    $recipients[] = $sender;
    $recipients = array_unique($recipients);

    return $recipients;
}

/**
 * Checks whether lesson has enough credits to send a message
 *
 * @param array $recipients
 * @param EfrontLesson $lesson
 * @return boolean
 */
function module_textme_hasEnoughCredits($recipients, $lesson) {
    $settings = module_textme_getSingleRow('module_textme_lessons_settings', '*',
                    'lessons_ID=' . $lesson->lesson['id']);

    if ($settings['credits'] == null) {
        return true;
    } else if ($settings['credits'] <= 0) {
        return false;
    }
    return true;
}

/**
 * Checks whether a given date is valid.
 *
 * @param array $value
 * @return boolean
 */
function module_textme_isValidDate($value) {
    return checkdate($value['M'], $value['d'], $value['Y']);
}

/**
 * Checks whether a given date is in the past.
 *
 * @param array $value
 * @return boolean
 */
function module_textme_isDateInFuture($value) {

    $current_timestamp = mktime();
    $input_timestamp = mktime($value['H'], $value['i'], 0,
                    $value['M'], $value['d'], $value['Y']);

    return $current_timestamp <= $input_timestamp;
}

/**
 * Calculates the credits for sending a message
 *
 * @param string $message
 * @param array $recipients
 * @return float
 */
function module_textme_calculateCredits($message, $recipients) {

    $message_length = mb_strlen($message, 'UTF-8');
    $credits = ( ceil($message_length / 160) ) * count($recipients);

    return $credits;
}

?>
