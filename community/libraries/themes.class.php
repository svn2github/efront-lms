<?php
/**

 *

 */
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 *

 * @author user

 *

 */
class EfrontThemesException extends Exception
{
    const ALREADY_EXISTS = 1201;
    const THEME_LOCKED = 1202;
 const DEFAULT_IMPORTED = 1203;
}
/**

 *

 * @author user

 *

 */
class themes extends EfrontEntity
{
    /**

     *

     * @var unknown_type

     */
    public $options = array();
    /**

     *

     * @var unknown_type

     */
    public $layout = array();
    /**

     *

     * @var unknown_type

     */
    public $directory = false;
    public static $browsers = array('ie' => 'Internet Explorer',
                              'ie6' => 'Internet Explorer 6',
                              'firefox'=> 'Mozilla Firefox',
                              'safari' => 'Apple Safari',
                              'chrome' => 'Google Chrome',
                              'opera' => 'Opera',
                              'mobile' => _MOBILECLIENT);
    /**

     *

     * @param $param

     * @return unknown_type

     */
    public function __construct($param) {
        //Special handling in case we are instantiating with string (name) instead of id
        if (!eF_checkParameter($param, 'id') && eF_checkParameter($param, 'alnum_general')) {
         $result = eF_getTableData("themes", "id", "name='".$param."'");
         if (sizeof($result) == 0) {
             throw new EfrontEntityException(_ENTITYNOTFOUND.': '.htmlspecialchars($param), EfrontEntityException :: ENTITY_NOT_EXIST);
         }
         $param = $result[0]['id'];
        }
        parent :: __construct($param);
        if (strpos($this -> {$this -> entity}['path'], 'http') === 0) {
            $this -> remote = 1;
        }
/*

        //Check whether this is a remote theme

        if (!is_dir($this -> {$this -> entity}['path']) && !is_file($this -> {$this -> entity}['path'])) {

            if (!fopen($this -> {$this -> entity}['path'].'theme.xml', 'r')) {

                throw new EfrontEntityException(_ENTITYNOTFOUND.': '.htmlspecialchars($this -> {$this -> entity}['path']), EfrontEntityException :: ENTITY_NOT_EXIST);

            } else {

                $this -> remote = 1;

            }

        }

*/
        $this -> options = unserialize($this -> {$this -> entity}['options']);
        if (!$this -> options) {
            $this -> options = array();
        }
        $this -> layout = unserialize($this -> {$this -> entity}['layout']);
        if (!$this -> layout) {
            $this -> layout = array();
        }
        //Check validity of current logo
        try {
            if (isset($this -> options['logo'])) {
                new EfrontFile($this -> options['logo']);
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            $this -> options['logo'] = false;
        }
        //Check validity of current favicon
        try {
            if (isset($this -> options['favicon'])) {
                new EfrontFile($this -> options['favicon']);
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            $this -> options['favicon'] = false;
        }
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
        //$system_form = new HTML_QuickForm("customization_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=themes&tab=customization", "", null, true);
  $form -> addElement('file', 'theme_file', _UPLOADTHEMEFILEZIPFORMAT);
  $form -> addElement('text', 'remote_theme', _ORSPECIFYREMOTETHEME, 'class = "inputText"');
  $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);
  $form -> addElement("submit", "submit_theme", _INSTALL, 'class = "flatButton"');
  return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
        if ($form -> exportValue('remote_theme')) {
            $file = $form -> exportValue('remote_theme');
            if (!fopen($file, 'r')) {
                throw new EfrontFileException(_FILEDOESNOTEXIST.': '.$file, EfrontFileException :: FILE_NOT_EXIST);
            }
        } else {
        try {
   $filesystem = new FileSystemTree(G_THEMESPATH, true);
   $themeFile = $filesystem -> uploadFile('theme_file', G_THEMESPATH);
   $filesList = $themeFile -> listContents();
   if (mb_substr($filesList[0], 0, mb_strpos($filesList[0] , "/")) == "default") {
     throw new Exception(_DEFAULTTHEMECANNOTBEIMPORTED, EfrontThemesException :: DEFAULT_IMPORTED);
   }
   $themeFile -> uncompress(false);
   //$pathInfo = pathinfo($themeFile['path']);
   //copy(G_DEFAULTTHEMEPATH.'css/css_global.php', $pathInfo['dirname'].'/'.$pathInfo['filename'].'/css/css_global.php');
   $themeFile -> delete();
   $file = new EfrontFile($themeFile['directory'].'/'.str_replace('.zip', '', $themeFile['name'])."/theme.xml");
        } catch (EfrontFileException $e) {
             //Don't halt if no file was uploaded (errcode = 4). Otherwise, throw the exception
             if ($e -> getCode() != 4) {
                 throw $e;
             }
         }
        }
        if ($file) {
         $xmlValues = themes::parseFile($file);
         if ($_GET['add']) {
             $theme = self :: create($xmlValues);
         }
          /*

	        else {

	            $this -> options = array_merge($this -> options, $xmlValues);

	            $this -> layout  =

	            $this -> persist();

	        }

	        */
        }
        //$smarty -> assign("T_RELOAD_ALL", 1);
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#persist()

     */
    public function persist() {
     unset($this->layout['positions']['comment']);
        $this -> {$this -> entity}['options'] = serialize($this -> options);
        $this -> {$this -> entity}['layout'] = serialize($this -> layout);
        parent :: persist();
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#delete()

     */
    public function delete() {
     if (!$this -> remote) {
         $directory = new EfrontDirectory(G_THEMESPATH.$this -> {$this -> entity}['path']);
         $directory -> delete();
     }
        eF_deleteTableData($this -> entity, "id=".$this -> {$this -> entity}['id']);
     $modules = eF_loadAllModules();
  foreach($modules as $key => $module) {
   $module -> onDeleteTheme($this -> {$this -> entity}['id']);
  }
    }
    /**

     *

     * @return unknown_type

     */
    public function applySettings($mode = false) {
        $file = G_THEMESPATH.$this -> {$this -> entity}['path'].'theme.xml';
        $fields = self :: parseFile($file);
        if (is_file($fields['path'].'images/logo.png')) {
            $fields['options']['logo'] = $fields['path'].'images/logo.png';
        }
        if (is_file($fields['path'].'images/favicon.png')) {
            $fields['options']['favicon'] = $fields['path'].'images/favicon.png';
        }
        $fields = self :: validateFields($fields);
        if ($mode == 'layout') {
            eF_updateTableData($this -> entity, array('layout' => $fields['layout']), "id=".$this -> {$this -> entity}['id']);
        } else {
            eF_updateTableData($this -> entity, $fields, "id=".$this -> {$this -> entity}['id']);
        }
    }
    /**

     *

     * @return unknown_type

     */
    public function export() {
        if (!$this -> remote) {
         global $currentUser;
            $directory = new EfrontDirectory(G_THEMESPATH.$this -> {$this -> entity}['path']);
            $file = $directory -> compress();
            $file = $file -> copy($currentUser->getDirectory().$file['name']);
            return $file;
        }
    }
    /**

     *

     * @param $fields

     * @return unknown_type

     */
    public static function validateFields($fields) {
        //Check validity of parameters
        if (!isset($fields['name']) || !eF_checkParameter($fields['name'], 'alnum_general')) {
            throw new Exception(_INVALIDNAME, EfrontEntityException :: INVALID_PARAMETER);
        }
        if (!isset($fields['options'])) {
            $fields['options'] = array();
        }
        //!isset($fields['active']) ? $fields['active'] = 1 : null;
        if (!isset($fields['options']['sidebar_width']) || $fields['options']['sidebar_width'] < 50 || $fields['options']['sidebar_width'] > 500) {
            $fields['options']['sidebar_width'] = 175;
        }
        if (!isset($fields['options']['sidebar_interface']) || !in_array($fields['options']['sidebar_interface'], array(0, 1, 2))) {
            $fields['options']['sidebar_interface'] = 0;
        }
        if (!isset($fields['options']['show_header']) || !in_array($fields['options']['show_footer'], array(0, 1, 2))) {
            $fields['options']['show_header'] = 1;
        }
        if (!isset($fields['options']['show_footer']) || !in_array($fields['options']['show_footer'], array(0, 1, 2))) {
            $fields['options']['show_footer'] = 1;
        }
        if (!isset($fields['options']['images_displaying']) || !in_array($fields['options']['images_displaying'], array(0, 1, 2))) {
            $fields['options']['images_displaying'] = 0;
        }
        if (!isset($fields['layout'])) {
            $fields['layout'] = array();
        }
        if (!isset($fields['layout']['positions'])) {
            $fields['positions'] = array();
        }
        if (!isset($fields['layout']['positions']['layout']) || !in_array($fields['layout']['positions']['layout'], array('simple', 'left', 'right', 'three'))) {
            $fields['layout']['positions']['layout'] = 'three';
        }
        if (!isset($fields['layout']['positions']['leftList'])) {
            $fields['layout']['positions']['leftList'] = array();
        } else if (isset($fields['layout']['positions']['leftList']) && !is_array($fields['layout']['positions']['leftList'])) {
            $fields['layout']['positions']['leftList'] = array($fields['layout']['positions']['leftList']);
        }
        if (!isset($fields['layout']['positions']['centerList'])) {
            $fields['layout']['positions']['centerList'] = array();
        } else if (isset($fields['layout']['positions']['centerList']) && !is_array($fields['layout']['positions']['centerList'])) {
            $fields['layout']['positions']['centerList'] = array($fields['layout']['positions']['centerList']);
        }
        if (!isset($fields['layout']['positions']['rightList'])) {
            $fields['layout']['positions']['rightList'] = array();
        } else if (isset($fields['layout']['positions']['rightList']) && !is_array($fields['layout']['positions']['rightList'])) {
            $fields['layout']['positions']['rightList'] = array($fields['layout']['positions']['rightList']);
        }
        //$fields['layout']['positions']['layout'] = $fields['layout']['selected_layout'];
//pr($fields);exit;
        $fields['options'] = serialize($fields['options']);
        $fields['layout'] = serialize($fields['layout']);
        return $fields;
    }
    /**

     *

     * @param $fields

     * @return unknown_type

     */
    public static function create($fields = array()) {
        if (is_file(G_THEMESPATH.$fields['path'].'images/logo.png')) {
            $fields['options']['logo'] = $fields['path'].'images/logo.png';
        }
        if (is_file(G_THEMESPATH.$fields['path'].'images/favicon.png')) {
            $fields['options']['favicon'] = $fields['path'].'images/favicon.png';
        }
        $fields = self :: validateFields($fields);
        $result = eF_getTableDataFlat("themes", "name");
        if (!in_array($fields['name'], $result['name'])) {
            $id = eF_insertTableData("themes", $fields);
        } else {
   $idx = array_search($fields['name'], $result['name']);
   $id = $result['name'][$idx];
  }
        $newTheme = new themes($id);
        return $newTheme;
    }
    /**

     *

     * @param $file

     * @return unknown_type

     */
    public static function parseFile($file) {
        if ($file instanceof EfrontFile) {
            $file = $file['path'];
        }
        $xml = new SimpleXMLIterator(file_get_contents($file));
        //Remove comment nodes
        foreach (new RecursiveIteratorIterator($xml, RecursiveIteratorIterator :: SELF_FIRST) as $key => $value) {
            unset($value->comment);
        }
        $fields = array('name' => (string)$xml -> name ? (string)$xml -> name: basename($file),
                  'title' => (string)$xml -> title,
            'version' => (string)$xml -> version,
               'author' => (string)$xml -> author,
                        'path' => str_replace(G_THEMESPATH, "", str_replace("\\", "/", dirname($file)).'/'),
               'description' => (string)$xml -> description,
         'options' => (array)$xml -> options,
                        'layout' => array('positions' => (array)$xml -> layout -> positions));
        return $fields;
    }
    /**

     *

     * @return unknown_type

     */
    public static function getAll() {
        $themes = parent :: getAll("themes", false);
        foreach ($themes as $key => $value) {
         unserialize($value['options']) ? $themes[$key]['options'] = unserialize($value['options']) : $themes[$key]['options'] = array();
            if (strpos($value['path'], 'http') === 0) {
             $themes[$key]['remote'] = 1;
         }
        }
        return $themes;
    }
}
