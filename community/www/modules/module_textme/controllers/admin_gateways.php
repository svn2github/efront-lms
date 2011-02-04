<?php

/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */
if ($this->ajax) {

    $items = eF_getTableData('module_textme_gateways', '*');
    $items = eF_multiSort($items, $this->sort, $this->order);

    if ($this->filter) {
        $items = eF_filterData($items, $this->filter);
    }

    $items_count = sizeof($items);
    $items = array_slice($items, $this->offset, $this->limit);

    $this->smarty->assign('T_TEXTME_ITEMS', $items);
    $this->smarty->assign('T_TEXTME_ITEMS_COUNT', $items_count);
} else if ($this->subcategory == 'account') {

    switch ($this->command) {

        /* Activate an sms gateway */
        case 'activate':

            /* Item variable is required */
            if ($this->item == null) {
                eF_redirect($this->moduleBaseUrl .
                        '&cat=gateways&message_type=failure&message=' .
                        rawurlencode(_TEXTME_ANERROROCCURED));
            }

            /* Disable currently enabled gateway and activate selected */
            eF_updateTableData('module_textme_gateways',
                    array('is_active' => 0), '1');
            eF_updateTableData('module_textme_gateways',
                    array('is_active' => 1), 'id=' . $this->item);
            break;

        /* Dectivate an sms gateway */
        case 'deactivate':
            eF_updateTableData('module_textme_gateways',
                    array('is_active' => 0), 'id=' . $this->item);
            break;

        /* Add an sms gateway */
        case 'add':

            $form = module_textme_getGatewayForm($this->moduleBaseUrl . '&cat=gateways&subcat=account&cmd=add',
                            Module_TextMe_SmsGatewayFactory::getGateways());

            if ($form->isSubmitted() && $form->validate()) {
                $gateway = array(
                    'type' => $form->exportValue('type'),
                    'name' => $form->exportValue('name'),
                    'data' => $form->exportValue('data'),
                    'is_active' => 0
                );

                eF_insertTableData('module_textme_gateways', $gateway);
                eF_redirect($this->moduleBaseUrl .
                        '&cat=gateways&message_type=success&message=' .
                        rawurlencode(_TEXTME_SMSGATEWAYADDED));
            }

            $renderer = module_textme_getFormRenderer($form, $this->smarty);
            $this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());
            break;

        /* Edit an sms gateway */
        case 'edit':
            /* Item variable is required */
            if ($this->item == null) {
                eF_redirect($this->moduleBaseUrl .
                        '&cat=gateways&message_type=failure&message=' .
                        rawurlencode(_TEXTME_ANERROROCCURED));
            }

            $form = module_textme_getGatewayForm($this->moduleBaseUrl .
                            '&cat=gateways&subcat=account&cmd=edit&item=' . $this->item,
                            Module_TextMe_SmsGatewayFactory::getGateways(), $this->item);

            if ($form->isSubmitted() && $form->validate()) {

                $gateway = array(
                    'type' => $form->exportValue('type'),
                    'name' => $form->exportValue('name'),
                    'data' => $form->exportValue('data'),
                );

                eF_updateTableData('module_textme_gateways', $gateway, 'id=' . $this->item);

                eF_redirect($this->moduleBaseUrl .
                        '&cat=gateways&message_type=success&message=' .
                        rawurlencode(_TEXTME_SMSGATEWAYUPDATED));
            }

            $renderer = module_textme_getFormRenderer($form, $this->smarty);
            $this->smarty->assign('T_TEXTME_FORM', $renderer->toArray());
            break;

        /* Delete an sms gateway */
        case 'delete':
            /* Item variable is required */
            if ($this->item == null) {
                eF_redirect($this->moduleBaseUrl .
                        '&cat=gateways&message_type=failure&message=' .
                        rawurlencode(_TEXTME_ANERROROCCURED));
            }

            eF_deleteTableData('module_textme_gateways', 'id=' . $this->item);
            $this->setMessageVar(_TEXTME_SMSGATEWAYDELETED, 'success');
            break;

        /* Test an sms gateway */
        case 'test':

            /* Item variable is required */
            if ($this->item == null) {
                eF_redirect($this->moduleBaseUrl .
                        '&cat=gateways&message_type=failure&message=' .
                        rawurlencode(_TEXTME_ANERROROCCURED));
            }

            $data = module_textme_getSingleRow('module_textme_gateways', '*', 'id=' . $this->item);
            $gateway = Module_TextMe_SmsGatewayFactory::getGateway($data);

            /* Check if mobile parameter is set for this provider else test message cannot be sent */
            if (isset($gateway->mobile)) {
                try {
                    $gateway->test();
                    $this->setMessageVar(sprintf(_TEXTME_TESTMESSAGESENT, $gateway->mobile), 'success');
                } catch (Exception $exc) {
                    $this->setMessageVar(sprintf(_TEXTME_GATEWAYERROR, $exc->getMessage()), 'failure');
                }
            } else {
                $this->setMessageVar(_TEXTME_NOMOBILEPARAMETERDEFINED, 'failure');
            }
            break;
    }
}

/**
 * Prepares and returns the add/edit form for an Sms Gateway.
 *
 * @param string $url The url to submit to
 * @param array $gateways an array of all the available gateways
 * @param integer $gateway_id the id on the database of the gateway (edit mode only)
 * @return HTML_QuickForm
 */
function module_textme_getGatewayForm($url, $gateways, $gateway_id = null) {

    $form = new HTML_QuickForm('module_textme_form', 'post', $url, '', null, true);
    $form->registerRule('checkMandatoryParameters', 'callback', 'module_textme_hasValidMandatoryParameters');

    $form->addElement('select', 'type', _TEXTME_SMSGATEWAYURL, $gateways, 'class="textme-width"');
    $form->addElement('text', 'name', _NAME, 'class="textme-width"');
    $form->addElement('textarea', 'data', _TEXTME_PARAMETERS, 'class="textme-width"');

    $form->addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');

    $form->addRule('name', _THEFIELD . ' "' . _NAME . '" ' . _ISMANDATORY, 'required');
    $form->addRule('data', _INVALIDFIELDDATA, 'regex', '/^((?<name>[a-zA-Z]\w+):(?<value>\S*)(\s*))*$/');

    if ($form->isSubmitted()) {
        $gateway_data = array(
            'type' => $form->exportValue('type'),
            'data' => $form->exportValue('data'));

        $gateway = Module_TextMe_SmsGatewayFactory::getGateway($gateway_data);
        $mandatory_params = $gateway->getMandatoryParameters(true);

        $form->addRule('data',
                sprintf(_TEXTME_THEFOLLOWINGPARAMETERSAREMANDATORYFORGATEWAY, $mandatory_params),
                'checkMandatoryParameters', $gateway);
    }

    if ($gateway_id != null) {
        $defaults = module_textme_getSingleRow('module_textme_gateways', '*', 'id=' . $gateway_id);
        $form->setDefaults($defaults);
    }

    return $form;
}

/**
 * Form validation function function.
 * Checks whether mandatory parameters are set for a given Sms Gateway.
 *
 * @param string $value
 * @param Module_TextMe_SmsGateway $gateway
 * @return boolean
 */
function module_textme_hasValidMandatoryParameters($value, $gateway) {
    foreach ($gateway->getMandatoryParameters() as $parameter) {
        if (isset($gateway->$parameter) == false) {
            return false;
        }
    }
    return true;
}

?>
