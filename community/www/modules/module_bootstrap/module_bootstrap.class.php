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
  $form -> addElement('text', 'creator', _MODULE_BOOTSTRAP_CREATOR, 'class = "inputText"');
  $form -> addElement('checkbox', 'administrator', _ADMINISTRATOR);
  $form -> addElement('checkbox', 'professor', _PROFESSOR);
  $form -> addElement('checkbox', 'student', _STUDENT);
  $form -> addElement('checkbox', 'supervisor', _MODULE_BOOTSTRAP_SUPERVISORONLY);
  $form -> addElement('checkbox', 'tabber', _MODULE_BOOTSTRAP_TABBER);
  $form -> addElement('textarea', 'form', _MODULE_BOOTSTRAP_FORMFIELDS, 'class = "inputTextarea" style = "height:40px"');
  $form -> addElement('static', '', _MODULE_BOOTSTRAP_FORMINSTRUCTIONS);
  $form -> addElement('textarea', 'grid', _MODULE_BOOTSTRAP_GRIDFIELDS, 'class = "inputTextarea" style = "height:40px"');
  $form -> addElement('static', '', _MODULE_BOOTSTRAP_GRIDINSTRUCTIONS);
  $form -> addElement('checkbox', 'filemanager', _MODULE_BOOTSTRAP_FILEMANAGER);
  $form -> addElement('textarea', 'description', _DESCRIPTION, 'class = "inputTextarea" style = "height:80px"');
  //$form -> addElement('placing', 'student', _MODULE_BOOTSTRAP_NAME);
  $form -> addElement('checkbox', 'overwrite', _MODULE_BOOTSTRAP_OVERWRITEIFEXISTS);
  $form -> addElement('submit', 'submit', _INSTALL, 'class = "flatButton"');
  $form -> setDefaults(array('creator' => $this->getCurrentUser()->user['name'].' '.$this->getCurrentUser()->user['surname']));
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
    if (!is_dir("{$module_dir}/assets")) {
     mkdir("{$module_dir}/assets", 0755);
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
    $contents = $tabber = $block = $grid_content = $fields_content = '';
    $fields = array();
    if ($values['form']) {
     foreach (explode(",", $values['form']) as $property) {
      $property = explode(":", $property);
      array_walk($property, create_function('&$v', '$v = trim($v);'));
      $fields[] = array('type' => $property[0], 'name' => $property[1], 'title' => $property[2], 'extra' => $property[3]);
     }
     foreach ($fields as $value) {
       $fields_content .= '$form -> addElement("'.$value["type"].'", "'.$value["name"].'", "'.$value["title"].'", "'.$value["extra"].'");';
     }
    } else {
     $fields = "return false;";
    }
    if ($values['filemanager']) {
     $file_manager = "true";
    } else {
     $file_manager = "false";
    }
    if ($values['grid']) {
     $headers = $rows = $grid_contents = array();
     $grid_content = "\$data = array(array(";
     foreach (explode(",", $values['grid']) as $property) {
      $property = trim($property);
      if ($property) {
       $property = explode(":", $property);
       array_walk($property, create_function('&$v', '$v = trim($v);'));
       $headers[] = "
        <td class = 'topTitle' name = '{$property[0]}'>{$property[1]}</td>";
       $rows[] = "
        <td>{\$item.{$property[0]}}</td>";
       $grid_content .= "'{$property[0]}' => 'sample value',";
      }
     }
     $grid_content .= "));";
     $headers = implode("\n", $headers);
     $rows = implode("\n", $rows);
     $contents .=
<<<GRID
{capture name = "t_grid_code"}
<!--ajax:{$module_name}Table-->
     <table style = "width:100%" class = "sortedTable" size = "{\$T_TABLE_SIZE}" sortBy = "0" id = "{$module_name}Table" useAjax = "1" rowsPerPage = "{\$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{\$T_MODULE_BASEURL}&">
      <tr class = "topTitle">
       {$headers}
      </tr>
 {foreach name = 'demo_data_list' key = 'key' item = 'item' from = \$T_DATA_SOURCE}
      <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
       {$rows}
      </tr>
 {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{\$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
    </table>
<!--/ajax:{$module_name}Table-->
{/capture}

GRID;
       if ($values['tabber']) {
        $tabber = "tabber = \"{$module_name}_grid\"";
       }
       $block .= '{eF_template_printBlock '.$tabber.' title = "'._MODULE_BOOTSTRAP_DATA.'" data = $smarty.capture.t_grid_code}'."\n";

    }
    if ($values['form']) {
     $contents .=
<<<FORM
{capture name = "t_form_block_code"}
 {eF_template_printForm form = \$T_FORM}
{/capture}
FORM;
     if ($values['tabber']) {
      $tabber = "tabber = \"{$module_name}_form\"";
     }
     $block .= '{eF_template_printBlock '.$tabber.' title = "'._MODULE_BOOTSTRAP_FORM.'" data = $smarty.capture.t_form_block_code}'."\n";
    }

    if ($values['filemanager']) {
     $contents .=
<<<FILEMANAGER
{capture name = "t_block_code"}
 {\$T_FILE_MANAGER}
{/capture}
FILEMANAGER;
     if ($values['tabber']) {
      $tabber = "tabber = \"{$module_name}_filemanager\"";
     }
     $block .= '{eF_template_printBlock '.$tabber.' title = "'._MODULE_BOOTSTRAP_FILES.'" data = $smarty.capture.t_block_code}';
    }

    if ($values['empty_page']) {
     $contents .=
<<<EMPTY
{capture name = "t_block_code"}
 Code here
{/capture}
EMPTY;
     if ($values['tabber']) {
      $tabber = "tabber = \"{$module_name}_page\"";
     }
     $block .= '{eF_template_printBlock '.$tabber.' title = "'._MODULE_BOOTSTRAP_PAGE.'" data = $smarty.capture.t_block_code}';
    }

    if ($values['tabber']) {
     $contents =
<<<CONTENTS
{$contents}
{capture name = "t_code"}
<div class = "tabber">
{$block}
</div>
{/capture}
{eF_template_printBlock title = "{$values["title"]}" data = \$smarty.capture.t_code}
CONTENTS;
    } else {
     $contents = $contents.$block;
    }

    file_put_contents("{$module_dir}/module.tpl", $contents);

    $search = array("###NAME###","###TITLE###", "###ROLES###", "###FIELDS###", "###FILE_MANAGER###", "###GRID_DATA###");
    $replace = array($module_name, $values['title'], $roles, $fields_content, $file_manager, $grid_content);

    $contents = file_get_contents($this -> moduleBaseDir."template/module_.class.php");
    $contents = str_replace($search, $replace, $contents);

    file_put_contents("{$module_dir}/module_{$module_name}.class.php", $contents);


    $date = date("Y m d");
    $xml =
<<<XML
<?xml version="1.0" ?>
<module>
 <title>{$values['title']}</title>
 <author>{$values['creator']}</author>
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
    if (is_dir(G_MODULESPATH.basename($module_dir))) {
     if ($values['overwrite']) {
      $file = $directory->copy(G_MODULESPATH.basename($module_dir), true);
     } else {
      throw new Exception(_MODULE_BOOTSTRAP_MODULEEXISTS);
     }
    } else {
     $file = $directory->copy(G_MODULESPATH.basename($module_dir));
    }
    $this -> setMessageVar(_MODULE_BOOTSTRAP_MODULEINSTALLED, 'success');

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
