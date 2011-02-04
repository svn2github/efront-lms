<?php
/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */

/* Handle ajax request for lessons table */
if ($this->ajax) {

    $items = module_textme_getLessons();
    $items = eF_multiSort($items, $this->sort, $this->order);

    if ($this->filter) {
        $items = eF_filterData($items, $this->filter);
    }

    $items_count = sizeof($items);
    $items = array_slice($items, $this->offset, $this->limit);
    $this->smarty->assign('T_TEXTME_ITEMS', $items);
    $this->smarty->assign('T_TEXTME_ITEMS_COUNT', $items_count);

} else if ($this->subcategory == 'account') { /* Manage a lesson account */

    /* Item variable is required */
    if ($this->item == null) {
        eF_redirect($this->moduleBaseUrl . '&message_type=failure&message=' .
                rawurlencode(_TEXTME_ANERROROCCURED));
    }

    /* Get lessons settings and populate lesson account form with it */
    $settings = module_textme_getSingleRow('module_textme_lessons_settings',
                    '*', 'lessons_ID=' . $this->item);
    $form = module_textme_getLessonAccountForm($this->moduleBaseUrl .
                    '&cat=lessons&subcat=account&cmd=edit&item=' . $this->item, $settings);

    /* User has submitted data validate and store new values for lesson account */
    if ($form->isSubmitted() && $form->validate()) {

        if ($form->exportValue('credits') == '') {
            $settings['credits'] = null;
        } else {
            $settings['credits'] = $form->exportValue('credits');
        }

        eF_updateTableData('module_textme_lessons_settings',
                $settings, 'lessons_ID=' . $this->item);
        eF_redirect($this->moduleBaseUrl . '&cat=lessons&message_type=success&message=' .
                rawurlencode(_TEXTME_LESSONACCOUNUPDATED));
    }

    $lesson = module_textme_getSingleRow('lessons', 'name', 'id='.$this->item);
    $this->smarty->assign('T_TEXTME_LESSONNAME', $lesson['name']);
    $this->smarty->assign('T_TEXTME_LESSONALIAS', $settings['alias']);

    $renderer = module_textme_getFormRenderer($form, $this->smarty);
    $this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());
}

/**
 * Returns the form for a lesson account.
 *
 * @param string $url
 * @param array $account
 * @return HTML_QuickForm
 */
function module_textme_getLessonAccountForm($url, $account) {

    $form = new HTML_QuickForm('module_textme_form', 'post', $url, '', null, true);

    $form->addElement('text', 'credits', _TEXTME_CREDITS, 'class="textme-width"');
    $form->addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');

    $form->addRule('credits', _TEXTME_CREDITSFIELDBLANKORNUMBER, 'regex', '/^[0-9]{0,10}$/');

    $form->setDefaults(array('credits' => $account['credits']));

    return $form;
}


?>
