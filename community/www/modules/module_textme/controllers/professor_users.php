<?php
/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */


if ($this->ajax) {

    if ($this->ajax == 'groups-table') {
        $items = module_textme_getGroups($this->lesson);
    } else if ($this->ajax == 'users-table') {
        $items = module_textme_getUsers($this->lesson);
    }

    $items = eF_multiSort($items, $this->sort, $this->order);

    if ($this->filter) {
        $items = eF_filterData($items, $this->filter);
    }

    $items_count = sizeof($items);
    $items = array_slice($items, $this->offset, $this->limit);

    $this->smarty->assign('T_TEXTME_ITEMS', $items);
    $this->smarty->assign('T_TEXTME_ITEMS_COUNT', $items_count);
} else if ($this->subcategory == 'group') {

    switch ($this->command) {

        case 'add':

            $form = module_textme_getUsersGroupForm($this->moduleBaseUrl .
                    '&cat=users&subcat=group&cmd=add', $this->lesson);

            if ($form->isSubmitted() && $form->validate()) {

                $group = array(
                    'lessons_ID' => $this->lesson->lesson['id'],
                    'name' => $form->exportValue('name'));

                $id = eF_insertTableData('module_textme_groups', $group);

                /* DO NOT use exportValue here!!! */
                $in_group = array_unique($form->getElementValue('in_group'));

                foreach ($in_group as $login) {

                    $subscriber = array(
                        'users_LOGIN' => $login,
                        'groups_ID' => $id);

                    eF_insertTableData('module_textme_group_has_subscribers', $subscriber);
                }

                eF_redirect($this->moduleBaseUrl .
                        '&cat=users&message_type=success&message=' .
                        rawurlencode(_TEXTME_GROUPADDED));
            }

            $renderer = module_textme_getFormRenderer($form, $this->smarty);
            $this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());

            break;
        case 'edit':

            if ($this->item == null) {
                eF_redirect($this->moduleBaseUrl . '&cat=users&message_type=failure&message=' .
                        rawurlencode(_TEXTME_ANERROROCCURED));
            }

            $form = module_textme_getUsersGroupForm($this->moduleBaseUrl . '&cat=users&subcat=group&cmd=edit&item=' . $this->item, $this->lesson, $this->item);

            if ($form->isSubmitted() && $form->validate()) {

                $group = array(
                    'lessons_ID' => $this->lesson->lesson['id'],
                    'name' => $form->exportValue('name'));

                eF_updateTableData('module_textme_groups', $group, 'id=' . $this->item);
                eF_deleteTableData('module_textme_group_has_subscribers', 'groups_ID=' . $this->item);

                /* DO NOT use exportValue here!!! */
                $in_group = $form->getElementValue('in_group');

                foreach ($in_group as $login) {

                    $subscriber = array(
                        'users_LOGIN' => $login,
                        'groups_ID' => $this->item);

                    eF_insertTableData('module_textme_group_has_subscribers', $subscriber);
                }

                eF_redirect($this->moduleBaseUrl .
                        '&cat=users&message_type=success&message=' .
                        rawurlencode(_TEXTME_GROUPUPDATED));
            }

            $renderer = module_textme_getFormRenderer($form, $this->smarty);
            $this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());

            break;
        case 'delete':

            if ($this->item == null) {
                eF_redirect($this->moduleBaseUrl . '&cat=users&message_type=failure&message=' .
                        rawurlencode(_TEXTME_ANERROROCCURED));
            }

            eF_deleteTableData('module_textme_group_has_subscribers', 'groups_ID=' . $this->item);
            eF_deleteTableData('module_textme_groups', 'id=' . $this->item);

            $this->setMessageVar(_TEXTME_GROUPDELETED, 'success');
            break;
    }
}

/**
 * Returns the form for adding or editing a group of users
 *
 * @param string $url
 * @param EFrontLesson $lesson
 * @param integer $group_id
 * @return HTML_QuickForm
 */
function module_textme_getUsersGroupForm($url, $lesson, $group_id = null) {

    $form = new HTML_QuickForm('module_textme_group_form', 'post', $url, '', null, true);

    $form->addElement('text', 'name', _NAME, 'class="textme-width"');

    if ($group_id) {
        $options = module_textme_getGroupUsers($lesson, $group_id, 'in');
    } else {
        $options = array();
    }

    $form->addElement('select', 'in_group', _TEXTME_INGROUPUSERS,
                    $options, 'id="module_textme_in_group" class="select-left"')
            ->setMultiple(true);

    if ($group_id) {
        $options = module_textme_getGroupUsers($lesson, $group_id, 'out');
    } else {
        $options = module_textme_getGroupUsers($lesson);
    }

    $form->addElement('select', 'out_group', _TEXTME_OUTOFGROUPUSERS,
                    $options, 'id="module_textme_out_group" class="select-right"')
            ->setMultiple(true);

    $form->addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');

    $form->addRule('name', _THEFIELD.' "'._NAME.'" '._ISMANDATORY, 'required');

    if ($group_id) {
        $group = module_textme_getSingleRow('module_textme_groups', '*', 'id='.$group_id);

        $defaults = array(
            'name' => $group['name']
        );
    } else {
        $defaults = array(
            'out_group' => module_textme_getGroupUsers($lesson)
        );
    }

    $form->setDefaults($defaults);

    return $form;
}

?>
