<?php

/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */
$options = array();

$options[] = array(
    'text' => _TEXTME_GOTOPAGE,
    'image' => $this->moduleBaseLink . 'assets/images/16/go_into.png',
    'href' => $this->moduleBaseUrl);

$gateway = module_textme_getSingleRow('module_textme_gateways', '*', 'is_active=1');

$lessons = module_textme_getLessons();
$lessons_enabled = array_filter($lessons, 'module_textme_filterEnabledLessons');

$subscribers = eF_getTableDataFlat('module_textme_phonebook', '*', 'is_verified=1');

/* Workaround for a strange php bug */
if (count($subscribers) == 0) {
    $subscribers['users_LOGIN'] = array();
}

$this->smarty->assign('T_TEXTME_OPTIONS', $options);
$this->smarty->assign('T_TEXTME_GATEWAY', $gateway);
$this->smarty->assign('T_TEXTME_GATEWAYENABLED', sizeof($gateway) != 0);
$this->smarty->assign('T_TEXTME_LESSONSCOUNT', sizeof($lessons));
$this->smarty->assign('T_TEXTME_LESSONSENABLEDCOUNT', sizeof($lessons_enabled));
$this->smarty->assign('T_TEXTME_SUBSCRIBERSCOUNT', sizeof(array_unique($subscribers['users_LOGIN'])));
$this->smarty->assign('T_TEXTME_BASEDIR', $this->moduleBaseDir);
$this->smarty->assign('T_TEXTME_BASEURL', $this->moduleBaseUrl);
$this->smarty->assign("T_TEXTME_BASELINK", $this->moduleBaseLink);

/**
 * Filter function.
 * Returns whether a given lesson has enabled TextMe or not.
 *
 * @param array $lesson
 * @return boolean
 */
function module_textme_filterEnabledLessons($lesson) {
    return $lesson['module_textme'];
}

?>
