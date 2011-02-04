<?php
/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */

/* Get subscriber data if any */
$subscriber = module_textme_getSingleRow('module_textme_phonebook', '*',
                'users_LOGIN =\'' . $this->user->login . '\'');

/* Handle ajax request for lessons table */
if ($this->ajax) {

    $items = module_textme_getUserLessons($this->user);
    $items = eF_multiSort($items, $this->sort, $this->order);

    if ($this->filter) {
        $items = eF_filterData($items, $this->filter);
    }

    $items_count = sizeof($items);
    $items = array_slice($items, $this->offset, $this->limit);

    $this->smarty->assign('T_TEXTME_ITEMS', $items);
    $this->smarty->assign('T_TEXTME_ITEMS_COUNT', $items_count);
    $this->smarty->assign('T_TEXTME_SUBSCRIBER', $subscriber);
} else {

    if (isset($_POST['submit'])) {

        switch ($this->command) {
            case 'unsubscribe':
                eF_deleteTableData('module_textme_subscribers', 'users_LOGIN =\'' . $this->user->login . '\'');
                eF_deleteTableData('module_textme_phonebook', 'users_LOGIN =\'' . $this->user->login . '\'');
                $subscriber = null;
                break;
            case 'mobile':
                eF_deleteTableData('module_textme_phonebook', 'users_LOGIN =\'' . $this->user->login . '\'');
                $subscriber = null;
                break;
            case 'vcode':
                if (module_textme_sendVerificationCode($subscriber, $user)) {
                    $this->setMessageVar(sprintf(_TEXTME_VERIFICATIONCODEWASSENT, $subscriber['mobile']) , 'success');
                } else {
                    $this->setMessageVar(_TEXTME_VERIFICATIONCODEWASNOTSENT, 'failure');
                }
                break;
        }
    }

    if ($subscriber == null) { /* User has not subscribed to service yet */

        /* User has to enter his mobile number */
        $form = module_textme_getAddMobileForm($this->moduleBaseUrl.'&cat=subscribe');

        if ($form->isSubmitted() && $form->validate()) {

            $subscriber = array(
                'users_LOGIN' => $this->user->login,
                'mobile' => $form->exportValue('mobile'),
                'vcode' => strtoupper(substr(md5('' . mktime()), 0, 5)),
                'is_verified' => 0);

            eF_insertTableData('module_textme_phonebook', $subscriber);

            /* Send verification code */
            if (module_textme_sendVerificationCode($subscriber, $user)) {
                $this->setMessageVar(sprintf(_TEXTME_VERIFICATIONCODEWASSENT, $subscriber['mobile']) , 'success');
            } else {
                $this->setMessageVar(_TEXTME_VERIFICATIONCODEWASNOTSENT, 'failure');
            }

            $form = module_textme_getVerifyMobileForm($this->moduleBaseUrl.'&cat=subscribe', $this->user);
        }

        /* Get all lessons that user attends and have activated textme */
        $lessons = module_textme_getUserLessons($this->user);
        $this->smarty->assign('T_TEXTME_LESSONS', $lessons);

        $renderer = module_textme_getFormRenderer($form, $this->smarty);
        $this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());
    } else if ($subscriber['is_verified'] == 0) { /* User has not verified his mobile number yet */

        $form = module_textme_getVerifyMobileForm($this->moduleBaseUrl.'&cat=subscribe', $this->user);

        if ($form->isSubmitted() && $form->validate()) {

            $subscriber['is_verified'] = 1;

            eF_updateTableData('module_textme_phonebook', $subscriber,
                    'users_LOGIN =\'' . $this->user->login . '\'');
        }

        $renderer = module_textme_getFormRenderer($form, $this->smarty);
        $this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());
    } else if ($this->subcategory == 'lessons') { /* User can subscribe or unsubscribe from lesson's notifications */

        switch ($this->command) {
            case 'activate':

                if ($this->item == null) {
                    eF_redirect($this->moduleBaseUrl.
                            '&cat=subscribe&message_type=failure&message=' .
                            rawurlencode(_TEXTME_ANERROROCCURED));
                }

                $lesson_subscriber = array(
                    'users_LOGIN' => $this->user->login,
                    'lessons_ID' => $this->item);

                eF_insertTableData('module_textme_subscribers', $lesson_subscriber);
                break;
            case 'deactivate':
                if ($this->item == null) {
                    eF_redirect($this->moduleBaseUrl.
                            '&cat=subscribe&message_type=failure&message=' .
                            rawurlencode(_TEXTME_ANERROROCCURED));
                }

                eF_deleteTableData('module_textme_subscribers',
                        'users_LOGIN=\'' . $this->user->login . '\' AND lessons_ID = \'' . $this->item . '\'');
                break;
        }
    }

    $this->smarty->assign('T_TEXTME_SUBSCRIBER', $subscriber);
}

/**
 * Send an sms with a verification code to a user.
 * Returns true upon success of false otherwise.
 *
 * @param array $subscriber
 * @param EfrontUser $user
 * @return boolean
 */
function module_textme_sendVerificationCode($subscriber, $user) {

    $data = module_textme_getSingleRow('module_textme_gateways', '*', 'is_active=1');
    $gateway = Module_TextMe_SmsGatewayFactory::getGateway($data);

    if ($gateway) {

        try {

            $text = 'Your verification code for TextMe is: ' . $subscriber['vcode'];

            $recipients = array(
                array('users_LOGIN' => $user->login,
                    'mobile' => $subscriber['mobile']));

            $gateway->send('EFRONT', $text, $recipients, mktime());

            return true;
        } catch (Exception $exc) {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Returns the form for adding a mobile number.
 *
 * @param string $url
 * @return HTML_QuickForm 
 */
function module_textme_getAddMobileForm($url) {
    /* Create form object */
    $form = new HTML_QuickForm('module_textme_mobile_form', 'post',
                    $url . '&subcat=mobile', '', null, true);

    /* Add input fields */
    $form->addElement('text', 'mobile', _TEXTME_MOBILE, 'class="textme-width"');
    $form->addElement('submit', 'submit', _NEXT, 'class = "flatButton"');

    /* Attach validators */
    $form->addRule('mobile', _THEFIELD.' "'._TEXTME_MOBILE.'" '._ISMANDATORY, 'required');
    $form->addRule('mobile', _INVALIDDATA, 'numeric');
    $form->addRule('mobile', _INVALIDDATA, 'minlength', 8);

    return $form;
}

/**
 * Returns the verification code form.
 *
 * @param string $url
 * @param EfrontUser $user
 * @return HTML_QuickForm 
 */
function module_textme_getVerifyMobileForm($url, $user) {

    /* Create form objct */
    $form = new HTML_QuickForm('module_textme_vcode_form', 'post',
                    $url, '', null, true);

    /* Register custom validators */
    $form->registerRule('isValidVerificationCode', 'callback', 'module_textme_isValidVerificationCode');

    /* Add input fields */
    $form->addElement('text', 'vcode', _TEXTME_VERIFICATIONCODE, 'class="textme-width"');
    $form->addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');

    /* Attach validators */
    $form->addRule('vcode', _THEFIELD.' "'._TEXTME_VERIFICATIONCODE.'" '._ISMANDATORY, 'required');
    $form->addRule('vcode', _TEXTME_INVALIDVERIFICATIONCODE, 'isValidVerificationCode', $user);

    return $form;
}

/**
 * Checks if a mobile number verification code for a given user is valid.
 *
 * @param string $code the input verification code
 * @param EfrontUser $user the user
 * @return boolean
 */
function module_textme_isValidVerificationCode($code, $user) {

    $subscriber = module_textme_getSingleRow('module_textme_phonebook', '*',
                    'users_LOGIN=\'' . $user->login . '\'');

    return (isset($subscriber['vcode'])) && ($subscriber['vcode'] == $code);
}

?>
