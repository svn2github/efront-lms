<?php
/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */


$settings = module_textme_getSingleRow('module_textme_lessons_settings', '*',
            'lessons_ID=' . $this->lesson->lesson['id']);

$form = module_textme_getSenderIDForm($this->moduleBaseUrl.
        '&cat=configuration&subcat=alias', $settings);

if ($form->isSubmitted() && $form->validate()) { /* Change lesson's alias*/

    $settings['alias'] = $form->exportValue('alias');

    eF_updateTableData('module_textme_lessons_settings', $settings,
            'lessons_ID=' . $this->lesson->lesson['id']);

    $this->setMessageVar(_TEXTME_SENDERIDUPDATED, 'success');
}

$renderer = module_textme_getFormRenderer($form, $this->smarty);
$this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());


/**
 * Returns the form for editing a lessons sender ID
 *
 * @param string $url
 * @param array $settings
 * @return HTML_QuickForm 
 */
function module_textme_getSenderIDForm($url, $settings) {

    $form = new HTML_QuickForm('module_textme_alias_form', 'post', $url, '', null, true);

    $form->addElement('text', 'alias', _TEXTME_SENDERID, 'class="textme-width"');
    $form->addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');

    $form->addRule('alias', _THEFIELD.' "'._TEXTME_SENDERID.'" '._ISMANDATORY, 'required');
    $form->addRule('alias', _TEXTME_SENDERIDINVALIDCHARACTERS, 'regex', '/^[A-Z][A-Z0-9]{1,10}$/');

    $defaults = array('alias' => $settings['alias']);

    $form->setDefaults($defaults);

    return $form;
}

?>
