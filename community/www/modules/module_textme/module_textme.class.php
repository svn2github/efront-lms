<?php

/**
 * TextMe module for eFront
 *
 * @author Manos Dimitrakis <manos@dimitrakis.gr>
 * @version 2.0
 */
require_once('library/utilities.php');
require_once('library/gateways/gateway.class.php');

class module_textme extends EfrontModule {

    public function getName() {
        return _TEXTME;
    }

    public function getPermittedRoles() {
        return array('administrator', 'professor', 'student');
    }

    public function getLanguageFile($language) {
        $lang_dir = $this->moduleBaseDir . 'i18n/';
        $lang_file = $lang_dir . 'english.inc';

        if (file_exists($lang_dir . $language . '.inc'))
            $lang_file = $lang_dir . $language . '.inc';

        return $lang_file;
    }

    public function getModuleJS() {
        return $this->moduleBaseDir . 'assets/script.js';
    }

    public function getModuleCSS() {
        return $this->moduleBaseDir . 'assets/style.css';
    }

    public function getModule() {
        return true;
    }

    public function getSmartyTpl() {

        $tabs = array();
        $default_tab = null;

        if ($this->role == 'administrator') {
            $tabs['lessons'] = _TEXTME_LESSONSACCOUNTSTAB;
            $tabs['gateways'] = _TEXTME_SMSGATEWAYSTAB;
            $default_tab = 'lessons';

            switch ($this->category) {
                case 'help':
                    $template = 'views/admin_help.tpl';
                    break;
                case 'gateways':
                    require_once('controllers/admin_gateways.php');
                    $template = 'views/admin_gateways.tpl';
                    break;
                case 'lessons':
                default:
                    require_once('controllers/admin_lessons.php');
                    $template = 'views/admin_lessons.tpl';
                    break;
            }

            $gateway = module_textme_getSingleRow('module_textme_gateways', '*', 'is_active=1');

            if ($gateway == null) {
                $this->setMessageVar(_TEXTME_NOTIFICATIONSONLYLOCALLY, 'failure');
            }
        } else if ($this->role == 'professor') {
            $tabs['inbox'] = _TEXTME_INBOX;
            $tabs['users'] = _TEXTME_USERSANDGROUPS;
            $tabs['configuration'] = _TEXTME_CONFIGURATION;
            $tabs['subscribe'] = _TEXTME_SUBSCRIBE;
            $default_tab = 'inbox';

            switch ($this->category) {
                case 'help':
                    $template = 'views/professor_help.tpl';
                    break;
                case 'subscribe':
                    require_once('controllers/subscribe.php');
                    $template = 'views/subscribe.tpl';
                    break;
                case 'configuration':
                    require_once('controllers/professor_configuration.php');
                    $template = 'views/professor_configuration.tpl';
                    break;
                case 'users':
                    require_once('controllers/professor_users.php');
                    $template = 'views/professor_users.tpl';
                    break;
                case 'inbox':
                default:
                    require_once('controllers/inbox.php');
                    $template = 'views/inbox.tpl';
                    break;
            }
        } else if ($this->role == 'student') {
            $tabs['inbox'] = _TEXTME_INBOX;
            $tabs['subscribe'] = _TEXTME_SUBSCRIBE;
            $default_tab = 'inbox';

            switch ($this->category) {
                case 'help':
                    $template = 'views/student_help.tpl';
                    break;
                case 'subscribe':
                    require_once('controllers/subscribe.php');
                    $template = 'views/subscribe.tpl';
                    break;
                case 'inbox':
                default:
                    require_once('controllers/inbox.php');
                    $template = 'views/inbox.tpl';
                    break;
            }
        }

        $lessons = module_textme_getLessons();

        $options[] = array(
            'text' => _HELP,
            'image' => $this->moduleBaseLink.'assets/images/16/help.png',
            'href' => "javascript:void(0)",
            'onclick' => 'PopupCenter(\'' . $this->moduleBaseUrl . '&cat=help#' . $this->category . '\', \'helpwindow\', \'800\', \'500\');'
        );

        $this->smarty->assign('T_TEXTME_OPTIONS', $options);


        $this->smarty->assign('T_TEXTME_CURRENTUSER', $this->user);
        $this->smarty->assign('T_TEXTME_CURRENTLESSON', $this->lesson);
        $this->smarty->assign('T_TEXTME_CURRENTROLE', $this->role);
        $this->smarty->assign('T_TEXTME_BASEDIR', $this->moduleBaseDir);
        $this->smarty->assign('T_TEXTME_BASEURL', $this->moduleBaseUrl);
        $this->smarty->assign("T_TEXTME_BASELINK", $this->moduleBaseLink);
        $this->smarty->assign('T_TEXTME_TABS', $tabs);
        $this->smarty->assign('T_TEXTME_TAB_DEFAULT', $default_tab);

        return $this->moduleBaseDir . $template;
    }

    public function isLessonModule() {
        return true;
    }

    public function getLessonModule() {
        return true;
    }

    public function getLessonSmartyTpl() {

        /* We need this to suppress some odd behaviour of eFront */
        if ($this->role == 'administrator') {
            return false;
        }
        require_once('controllers/lpanel_widget.php');
        return $this->moduleBaseDir . 'views/lpanel_widget.tpl';
    }

    public function getControlPanelModule() {
        return true;
    }

    public function getControlPanelSmartyTpl() {

        require_once('controllers/cpanel_widget.php');
        return $this->moduleBaseDir . 'views/cpanel_widget.tpl';
    }

    public function getNavigationLinks() {

        $links = array();

        if ($this->category == 'help') {
            $links[] = array(
                'title' => _HELP,
                'link' => $this->moduleBaseUrl.'&cat=help');
        } else if ($this->role == 'administrator') {
            $links[] = array(
                'title' => _HOME,
                'link' => $this->user->getType() . ".php?ctg=control_panel");
            $links[] = array(
                'title' => _TEXTME,
                'link' => $this->moduleBaseUrl);
        } else {
            $links[] = array(
                'title' => _MYLESSONS,
                'onclick' => "location='" . $this->role
                . ".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();");
            $links[] = array(
                'title' => $this->lesson->lesson['name'],
                'link' => $this->user->getType() . ".php?ctg=control_panel");
            $links[] = array(
                'title' => _TEXTME,
                'link' => $this->moduleBaseUrl);
        }

        return $links;
    }

    public function getCenterLinkInfo() {

        $links = array(
            'title' => _TEXTME,
            'image' => $this->moduleBaseDir . 'assets/images/32/logo.png',
            'link' => $this->moduleBaseUrl);

        return $links;
    }

    public function getLessonCenterLinkInfo() {

        $links = array(
            'title' => _TEXTME,
            'image' => $this->moduleBaseDir . 'assets/images/32/logo.png',
            'link' => $this->moduleBaseUrl);

        return $links;
    }

    public function onDeleteUser($login) {
        $login_condition = 'users_LOGIN=\'' . $login . '\'';

        /* Get all messages that the user cas composed */
        $messages = eF_getTableData('module_textme_messages', '*', $login_condition);

        /* Remove each message for every recipient's box */
        foreach ($messages as $message) {
            eF_deleteTableData('module_textme_recipients', 'messages_ID=' . $message['id']);
        }
        /* Delete all messages composed by the user */
        eF_deleteTableData('module_textme_messages', $login_condition);
        /* Delete account, inbox and subscriptions for user */
        eF_deleteTableData('module_textme_phonebook', $login_condition);
        eF_deleteTableData('module_textme_recipients', $login_condition);
        eF_deleteTableData('module_textme_subscribers', $login_condition);

        return false;
    }

    public function onNewLesson($lessonId) {

        $settings = array(
            'lessons_ID' => $ef_lesson->lesson['id'],
            'alias' => 'EFRONT',
            'credits' => null,
            'credits_spent' => 0
        );

        eF_insertTableData('module_textme_lessons_settings', $settings);

        return false;
    }

    public function onDeleteLesson($lessonId) {

        $lesson_condition = 'lessons_ID=' . $lessonId;

        /* Get all messages that the user cas composed */
        $messages = eF_getTableData('module_textme_messages', '*', $lesson_condition);

        /* Remove each message for every recipient's box */
        foreach ($messages as $message) {
            eF_deleteTableData('module_textme_recipients', 'messages_ID=' . $message['id']);
        }

        eF_deleteTableData('module_textme_messages', $lesson_condition);
        eF_deleteTableData('module_textme_lessons_settings', $lesson_condition);
        eF_deleteTableData('module_textme_subscribers', $lesson_condition);


        return false;
    }

    public function onImportLesson($lessonId, $data) {

        $settings = array(
            'lessons_ID' => $ef_lesson->lesson['id'],
            'alias' => 'EFRONT',
            'credits' => null,
            'credits_spent' => 0
        );

        eF_insertTableData('module_textme_lessons_settings', $settings);

        return false;
    }

    public function onInstall() {

        global $_TEXTME_INSTALL_QUERIES;
        global $_TEXTME_UNINSTALL_QUERIES;

        require_once('includes/install_queries.inc');
        require_once('includes/uninstall_queries.inc');

        $result1 = true;

        // Drop database tables
        foreach ($_TEXTME_UNINSTALL_QUERIES as $index => $query)
            if (eF_executeNew($query) == false)
                $result1 = false;

        $result2 = true;

        // Create database tables
        foreach ($_TEXTME_INSTALL_QUERIES as $index => $query)
            if (eF_executeNew($query) == false)
                $result2 = false;

        return $result1 && $result2;
    }

    public function onUninstall() {

        global $_TEXTME_UNINSTALL_QUERIES;
        require_once('includes/uninstall_queries.inc');

        $result = true;

        // Drop database tables
        foreach ($_TEXTME_UNINSTALL_QUERIES as $index => $query)
            if (eF_executeNew($query) == false)
                $result = false;

        return $result;
    }

    private function sanitizeContextVariables() {

        $this->user = $this->getCurrentUser();
        $this->lesson = $this->getCurrentLesson();
        $this->smarty = $this->getSmartyVar();

        if ($this->lesson == null) {
            $this->role = $this->user->getType();
        } else {
            $this->role = $this->user->getRole($this->lesson);
        }

        $this->category = isset($_GET['cat']) ? $_GET['cat'] : null;
        $this->subcategory = isset($_GET['subcat']) ? $_GET['subcat'] : null;
        $this->command = isset($_GET['cmd']) ? $_GET['cmd'] : null;
        $this->ajax = isset($_GET['ajax']) ? $_GET['ajax'] : null;
        $this->sort = isset($_GET['sort']) ? $_GET['sort'] : null;
        $this->filter = isset($_GET['filter']) ? $_GET['filter'] : null;

        $f = fopen('manos', 'w+');
        fwrite($f, print_r($_GET, true));
        fclose($f);

        if (isset($_GET['order']) &&
                ($_GET['order'] == 'asc' || $_GET['order'] == 'desc')) {
            $this->order = $_GET['order'];
        } else {
            $this->order = $_GET['asc'];
        }

        if (isset($_GET['offset'])
                && is_numeric($_GET['offset']) && $_GET['offset'] >= 0) {
            $this->offset = (int) $_GET['offset'];
        } else {
            $this->offset = 0;
        }

        if (isset($_GET['limit'])
                && is_numeric($_GET['limit']) && $_GET['limit'] >= 0) {
            $this->limit = (int) $_GET['limit'];
        } else {
            $this->limit = 20;
        }

        if (isset($_GET['item'])
                && is_numeric($_GET['item']) && $_GET['item'] >= 0) {
            $this->item = (int) $_GET['item'];
        } else {
            $this->item = null;
        }
    }

    public function __construct($defined_moduleBaseUrl, $defined_moduleFolder) {
        parent::__construct($defined_moduleBaseUrl, $defined_moduleFolder);
        if ($this->getCurrentUser()) {
         $this->sanitizeContextVariables();
        }
    }

    private $user;
    private $lesson;
    private $role;
    private $category;
    private $subcategory;
    private $command;
    private $item;
    private $ajax;
    private $sort;
    private $order;
    private $offset;
    private $limit;
    private $filter;
    private $smarty;

}

?>
