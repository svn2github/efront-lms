<?php
class module_branchthemes extends EfrontModule
{
 public function getPermittedRoles()
 {
  return array("administrator", "professor", "student");
 }
    public function getName()
    {
        return _BRANCH_THEMES;
    }
 public function getLanguageFile ($language)
 {
  if(file_exists($this->moduleBaseDir.'language/'.$language.'.php'))
   return $this->moduleBaseDir.'language/'.$language.'.php';
  return $this->moduleBaseDir.'language/english.php';
 }
 //install the module
 public function onInstall()
 {
  eF_executeNew("DROP TABLE IF EXISTS module_branchthemes_branch ");
  $res1=eF_executeNew("
  CREATE TABLE if not exists module_branchthemes_branch
  (
     `branch_ID` int NOT NULL,
     `browser` varchar(9) NOT NULL,
     `themes_ID` int NOT NULL,
     PRIMARY KEY (`branch_ID`,`browser`)
   ) DEFAULT CHARSET=utf8");
  return ($res1);
 }
 //uninstall the module
 public function onUninstall()
 {
  return eF_executeNew("DROP TABLE IF EXISTS module_branchthemes_branch");
 }
 //set the theme for the user
 public function onSetTheme($theme)
 {
  if(!$theme=$this->getMyTheme())
   return false;
  $_SESSION['s_theme']=$theme;
  return $theme;
 }
 //Get the current users themeId
 private function getMyTheme()
 {
  $login=false;
  if($user=$this->getCurrentUser())
   $login=$user->login;
  if(!$login && isset($_POST['login']))
   $login=$_POST['login'];
  if(!$login)
   return false;
  $login=addslashes($login);
  $data=eF_getTableDataFlat('module_hcd_employee_works_at_branch','branch_ID', "assigned=1 AND users_login='$login'");
  return $this->getMyThemeHelper($data['branch_ID']);
 }
 //The recursive helper of getMyTheme
 private function getMyThemeHelper($branchId,$exclude=array(0))
 {
  if(!is_array($branchId))
   $branchId=array(intval($branchId));
  $browser=addslashes(detectBrowser());
  $data=eF_getTableData("module_branchthemes_branch","browser,themes_ID","branch_ID in (".implode(',',$branchId).") and (browser='$browser' or browser='default')");
  if(count($data))
  {
   foreach($data as $row)
   {
    if($row['browser']!='default')
     return $row['themes_ID'];
    $default=$row['themes_ID'];
   }
   return $default;
  }
  $data=eF_getTableDataFlat("module_hcd_branch","father_branch_ID as id",'branch_ID in ('.implode(',',$branchId).') and father_branch_ID not in ('.implode(',',$exclude).')');
  $newId=$data['id'];
  if(!count($newId))
   return false;
  return $this->getMyThemeHelper($newId,array_merge($exclude,$newId));
 }
 //return the name of the tab template
 public function getTabSmartyTpl($tabberIdentifier)
 {
  if($tabberIdentifier=='branches')
  {
    //load the branch settings
    $branchId=intval($_GET['edit_branch']);
   $settings=array();
   $branchThemes=eF_getTableData('module_branchthemes_branch','themes_ID, browser',"branch_ID=$branchId");
   foreach($branchThemes as $theme)
    $settings[$theme['browser']]=$theme['themes_ID'];
   //make data available to the template
   $smarty=$this->getSmartyVar();
   $smarty->assign("T_BRANCHTHEMES_BRANCHID",$branchId);
   $smarty->assign("T_BRANCHTHEMES_BRANCHTHEMEINFO",$settings);
   $smarty->assign("T_THEMES",themes::getAll("themes"));
   $smarty->assign('T_BROWSERS',themes::$browsers);
   $smarty->assign("T_BRANCH_THEME_MODULE_BASEURL",$this->moduleBaseUrl);
   return array('tab'=>'module_branchthemes','title'=>_BRANCH_THEMES,'file'=>$this->moduleBaseDir."smarty/themes.tpl");
  }
 }
 //This function is called every time a module page is loaded we are going to use to to handle the updates
 public function getModule()
 {
  if(isset($_GET['ajax']) && $_GET['ajax']=='ajax')
  {
   if(isset($_REQUEST['themeId']) && isset($_REQUEST['browser']) && isset($_REQUEST['branchId']))
   {
    $themeId=intval($_REQUEST['themeId']);
    $branchId=intval($_REQUEST['branchId']);
    $browser=$_REQUEST['browser'];
    if($this->updateBranchTheme($branchId,$browser,$themeId))
     die("1");
    die("0");
   }
  }
 }
 /*
	 * @param $branchId id of the branch
	 * @param $browser the browser short name
	 * @param $themeId id of the theme
	 * @return bool if this resulted in turing the setting on (true) or off (false)
	 * */
 public function updateBranchTheme($branch_ID,$browser,$themes_ID)
 {
  $browser=addslashes($browser);
  $branch_ID=intval($branch_ID);
  $themes_ID=intval($themes_ID);
  $check=eF_getTableData('module_branchthemes_branch','1',"branch_ID=$branch_ID and browser='$browser' and themes_ID=$themes_ID");
  if(count($check))
  {
   eF_deleteTableData('module_branchthemes_branch',"browser='$browser' AND branch_ID=$branch_ID");
   return false;
  }
  $sql="insert into module_branchthemes_branch (branch_ID,browser,themes_ID) values ($branch_ID,'$browser',$themes_ID) on duplicate key update themes_ID=values(themes_ID)";
  eF_executeNew($sql);
  return true;
 }
 //deletes references to a given theme
 public function onDeleteTheme($theme)
 {
  eF_deleteTableData('module_branchthemes_branch','themes_ID='.intval($theme));
 }


 public static function getBranchTheme($branchId) {
  if (eF_checkParameter($branchId, 'id')) {
   $browser=addslashes(detectBrowser());
   $result = eF_getTableData("module_branchthemes_branch", "themes_ID", "branch_ID={$branchId} and (browser='$browser' or browser='default')");
   return $result[0]['themes_ID'];
  } else return false;
 }
}
?>
