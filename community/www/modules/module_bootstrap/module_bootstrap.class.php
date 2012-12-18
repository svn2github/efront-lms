<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_bootstrap extends EfrontModule {
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
    public function getName() {
     //This is a language tag, defined in the file lang-<your language>.php
        return _MODULE_BOOTSTRAP_MODULEBOOTSTRAP;
    }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
    public function getPermittedRoles() {
        return array("administrator"); //This module will be available to administrators
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCenterLinkInfo()

     */
    public function getCenterLinkInfo() {
     return array('title' => $this -> getName(),
                     'image' => $this -> moduleBaseLink . 'img/logo.png',
                     'link' => $this -> moduleBaseUrl);
    }
    /**

     * The main functionality

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModule()

     */
    public function getModule() {
     $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASELINK" , $this -> moduleBaseLink);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
     $form = new HTML_QuickForm("bootstrap_form", "post", $this -> moduleBaseUrl, "", null, true);
     $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
  $form -> addElement('text', 'name', _NAME, 'class = "inputText"');
  $form -> addElement('static', '', _MODULE_BOOTSTRAP_WITHOUTTHECLASS, 'class = "inputText"');
  $form -> addRule('name', _THEFIELD.' '._NAME.' '._ISMANDATORY, 'required', null, 'client');
  $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'alnum');
  $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
  $form -> addElement('checkbox', 'administrator', _ADMINISTRATOR);
  $form -> addElement('checkbox', 'professor', _PROFESSOR);
  $form -> addElement('checkbox', 'student', _STUDENT);
  $form -> addElement('textarea', 'form', _MODULE_BOOTSTRAP_FORMFIELDS, 'class = "inputTextarea" style = "height:40px"');
  $form -> addElement('static', '', _MODULE_BOOTSTRAP_FORMINSTRUCTIONS, 'class = "inputText"');
  $form -> addElement('textarea', 'grid', _MODULE_BOOTSTRAP_GRIDFIELDS, 'class = "inputTextarea" style = "height:40px"');
  $form -> addElement('textarea', 'description', _DESCRIPTION, 'class = "inputTextarea" style = "height:80px"');
  //$form -> addElement('placing', 'student', _MODULE_BOOTSTRAP_NAME);
  $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
  if ($form -> isSubmitted() && $form -> validate()) {
   try {
    $values = $form -> exportValues();
    if (!$values['administrator'] && !$values['student'] && !$values['professor']) {
     throw new Exception(_MODULE_BOOTSTRAP_YOUMUSTSELECTATLEASTATYPE);
    }
    $module_name = $values['name'];
    $module_dir = $this -> moduleBaseDir.'module_'.$module_name;
    if (!is_dir($module_dir)) {
     mkdir($module_dir, 0755);
    }
    if (!is_dir("{$module_dir}/img")) {
     mkdir("{$module_dir}/img", 0755);
    }
    if ($values['administrator']) {
     $roles[] = 'administrator';
    }
    if ($values['professor']) {
     $roles[] = 'professor';
    }
    if ($values['student']) {
     $roles[] = 'student';
    }
    $roles = '"'.implode('","',$roles).'"';
    $fields = array();
    if ($values['form']) {
     foreach (explode(",", $values['form']) as $property) {
      $property = explode(":", $property);
      array_walk($property, create_function('&$v', '$v = trim($v);'));
      $fields[] = array('type' => $property[0], 'name' => $property[1], 'title' => $property[2], 'extra' => $property[3]);
     }
     $fields_content = '';
     foreach ($fields as $value) {
       $fields_content .= '$form -> addElement("'.$value["type"].'", "'.$value["name"].'", "'.$value["title"].'", "'.$value["extra"].'");
  ';
     }
    } else {
     $fields = "return false;";
    }
    $search = array("###NAME###","###TITLE###", "###ROLES###", "###FIELDS###");
    $replace = array($module_name, $values['title'], $roles, $fields_content);
    $contents = file_get_contents($this -> moduleBaseDir."template/module_.class.php");
    $contents = str_replace($search, $replace, $contents);
    file_put_contents("{$module_dir}/module_{$module_name}.class.php", $contents);
    $contents = '';
    if ($values['grid']) {
     $contents .= '';
    }
    if ($values['form']) {
     $contents .=
<<<FORM
{capture name = "t_form_block_code"}
 {eF_template_printForm form = \$T_FORM}
{/capture}
{eF_template_printBlock title = "{$values['title']}" data = \$smarty.capture.t_form_block_code}
FORM;
    }
    if (!$values['form'] && !$values['grid']) {
     $contents = file_get_contents($this -> moduleBaseDir."template/module.tpl");
    }

    file_put_contents("{$module_dir}/module.tpl", str_replace("###TITLE###", $values['title'], $contents));

    $date = date("Y m d");
    $author = $this->getCurrentUser()->user['name'].' '.$this->getCurrentUser()->user['surname'];

    $xml =
<<<XML
<?xml version="1.0" ?>
<module>
 <title>{$values['title']}</title>
 <author>{$author}</author>
 <date>{$date}</date>
 <version>1.0</version>
 <description>{$values['description']}</description>
 <className>module_{$module_name}</className>
 <requires>3.6.11</requires>
</module>
XML;
    file_put_contents("{$module_dir}/module.xml", $xml);

    copy($this -> moduleBaseDir."img/logo.png", "{$module_dir}/img/logo.png");
    copy($this -> moduleBaseDir."img/generic.png", "{$module_dir}/img/generic.png");

    $directory = new EfrontDirectory($module_dir);
    $file = $directory->compress(false, false);
    $this -> setMessageVar(_OPERATIONCOMPLETEDSUCCESSFULLY, 'success');

    $file->sendFile(true);

   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $this -> setMessageVar($message, 'failure');
   }
  }
  $smarty -> assign("T_FORM", $form -> toArray());

        return true;
    }

    /**

     * Specify which file to include for template

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSmartyTpl()

     */
    public function getSmartyTpl() {
     return $this -> moduleBaseDir."module.tpl";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getNavigationLinks()

     */
    public function getNavigationLinks() {
        return array (array ('title' => _HOME, 'link' => $_SERVER['PHP_SELF']),
                      array ('title' => $this -> getName(), 'link' => $this -> moduleBaseUrl));
    }
}
