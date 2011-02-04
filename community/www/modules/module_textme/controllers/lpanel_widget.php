<?php
/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */

$options = array();

if( $this->role == 'professor' )
{
    $options[] = array(
        'text' => _TEXTME_COMPOSEMESSAGE,
        'image' => $this->moduleBaseLink . 'assets/images/16/add.png',
        'href' => $this->moduleBaseUrl.'&cat=inbox&subcat=compose');
}

$options[] = array(
    'text' => _SHOWALL,
    'id' => 'module_textme_show_all_messages',
    'image' => $this -> moduleBaseLink."assets/images/16/arrow_down_blue.png",
    'href' => "javascript:void(0)");
$options[] = array(
    'text' => _TEXTME_GOTOPAGE,
    'image' => $this->moduleBaseLink . 'assets/images/16/go_into.png',
    'href' => $this->moduleBaseUrl);

$items = module_textme_getInbox($this->user, $this->lesson);
$items = array_filter($items, 'module_textme_filterReadMessages');

$subscriber = module_textme_getSingleRow('module_textme_phonebook', '*',
                'users_LOGIN =\'' . $this->user->login . '\'');

$this->smarty->assign('T_TEXTME_ITEMS', $items);
$this->smarty->assign('T_TEXTME_ITEMSCOUNT', count($items));
$this->smarty->assign('T_TEXTME_OPTIONS', $options);
$this->smarty->assign('T_TEXTME_SUBSCRIBER', $subscriber);
$this->smarty->assign('T_TEXTME_BASEDIR', $this->moduleBaseDir);
$this->smarty->assign('T_TEXTME_BASEURL', $this->moduleBaseUrl);
$this->smarty->assign("T_TEXTME_BASELINK", $this->moduleBaseLink);

/**
 * Filter function.
 * Returns whether a given message is read by the user or not.
 *
 * @param array $message
 * @return boolean
 */
function module_textme_filterReadMessages($message)
{
    return $message['is_read'] == 0;
}
?>
