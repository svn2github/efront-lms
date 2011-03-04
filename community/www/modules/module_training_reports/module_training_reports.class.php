<?php

class module_training_reports extends EFrontModule {

    private $category;
    private $command;
    private $id;
    private $smarty;

    public function __construct($defined_moduleBaseUrl, $defined_moduleFolder) {
        parent::__construct($defined_moduleBaseUrl, $defined_moduleFolder);

        $this->category = isset($_GET['cat']) ? $_GET['cat'] : null;
        $this->command = isset($_GET['cmd']) ? $_GET['cmd'] : null;
        $this->id = isset($_POST['report']) ? $_POST['report'] : 0;
        $this->id = isset($_GET['id']) ? $_GET['id'] : $this->id;
    }

    public function getName() {
        return _TRAININGREPORTS;
    }

    public function getPermittedRoles() {
        return array('administrator');
    }

    public function getModuleJS() {

        switch ($this->category) {
            case 'edit':
                $javascript = $this->moduleBaseDir . 'assets/edit.js';
                break;
            case 'view':
            default:
                $javascript = $this->moduleBaseDir . 'assets/view.js';
                break;
        }

        return $javascript;
    }

    public function getLanguageFile($language) {

        $lang_dir = $this->moduleBaseDir . 'l10n/';
        $lang_file = $lang_dir . 'lang-' . $language . '.php';

        if (is_file($lang_file) == false) {
            $lang_file = $lang_dir . 'lang-english.php';
        }

        return $lang_file;
    }

    public function getSmartyTpl() {

        $this->smarty = $this->getSmartyVar();
        $role = $this->getCurrentUser()->getType();

        /* Only admins can access this module */
        if ($role != 'administrator') {
            return false;
        }

        switch ($this->category) {
            case 'excel':
                require_once('controllers/excel.php');
                break;
            case 'edit':
                require_once('controllers/edit.php');
                $template = 'views/edit.tpl';
                break;
            case 'view':
            default:
                require_once('controllers/view.php');
                $template = 'views/view.tpl';
                break;
        }

        $this->smarty->assign("T_MODULE_BASEDIR", $this->moduleBaseDir);
        $this->smarty->assign("T_MODULE_BASELINK", $this->moduleBaseLink);
        $this->smarty->assign("T_MODULE_BASEURL", $this->moduleBaseUrl);

        return $this->moduleBaseDir . $template;
    }

    public function getNavigationLinks() {

        require_once($this->moduleBaseDir . '/lib/TrainingReports_Report.php');

        $breadcrumbs = array();

        $role = $this->getCurrentUser()->getType();

        $this->category = isset($_GET['cat']) ? $_GET['cat'] : null;
        $this->command = isset($_GET['cmd']) ? $_GET['cmd'] : null;

        $breadcrumbs[] = array(
            'title' => _HOME,
            'link' => $role . '.php?ctg=control_panel');

        if ($role != 'administrator') {
            return $breadcrumbs;
        }

        $breadcrumbs[] = array(
            'title' => _TRAININGREPORTS,
            'link' => $this->moduleBaseUrl);

        if (TrainingReports_Report::isValid($this->id)) {

            $trainingReport = new TrainingReports_Report($this->id);

            $breadcrumbs[] = array(
                'title' => $trainingReport->getName(),
                'link' => $this->moduleBaseUrl . '&amp;id=' . $this->id);
        }

        return $breadcrumbs;
    }

    public function getCenterLinkInfo() {
        $link = array(
            'title' => _TRAININGREPORTS,
            'image' => $this->moduleBaseDir . 'assets/images/logo32.png',
            'link' => $this->moduleBaseUrl);

        return $link;
    }

    public function onDeleteCourse($courseId) {
        eF_deleteTableData('module_reports_courses', 'courses_ID=' . $courseId);
        return false;
    }

    public function onInstall() {

        global $_TIME_REPORTS_INSTALL_QUERIES;
        require_once('lib/schema.php');

        $result = true;

        // Create database tables for reports module
        foreach ($_TIME_REPORTS_INSTALL_QUERIES as $index => $query) {
            $result = $result && eF_executeNew($query);
        }

        return $result;
    }

    public function onUninstall() {

        global $_TIME_REPORTS_UNINSTALL_QUERIES;
        require_once('lib/schema.php');

        $result = true;

        // Drop all database tables for reports module
        foreach ($_TIME_REPORTS_UNINSTALL_QUERIES as $index => $query) {
            $result = $result && eF_executeNew($query);
        }

        return $result;
    }

}

?>
