<?php
class module_translate extends EfrontModule {

    public function getName() {
        return _TRANSLATE_TRANSLATE;
    }

    public function getPermittedRoles() {
        return array("administrator","student","professor");
    }

 public function getModuleJs() {
  return $this->moduleBaseDir."translate_jsapi.js";
 }

 public function getModuleCSS() {
  return $this->moduleBaseDir."translate_css.css";

 }

 public function getModule() {
  $smarty = $this -> getSmartyVar();
  return true;
}

 public function getSmartyTpl(){
  $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
 }

 public function getLessonModule() {
  $smarty = $this -> getSmartyVar();
  $inner_table_options = array(array('text' => _TRANSLATE_GOTOTRANSLATEPAGE,
           'image' => "16x16/go_into.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_TRANSLATE_INNERTABLE_OPTIONS", $inner_table_options);
        return true;
 }

    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }

    public function getSidebarLinkInfo() {

     $currentUser = $this -> getCurrentUser();
        $link_of_menu_system = array (array ('id' => 'translate_link_id1',
                                               'title' => _TRANSLATE_TRANSLATE,
                                               'image' => $this -> moduleBaseDir.'images/planet16',
                                               'eFrontExtensions' => '1',
                                               'link' => $this -> moduleBaseUrl));
  if ($currentUser -> getType() == "administrator") {
         return array ( "system" => $link_of_menu_system);
        }else {
   return array ( "tools" => $link_of_menu_system);
  }
    }

    public function getCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "administrator") {
            return array('title' => _TRANSLATE_TRANSLATE,
                         'image' => $this -> moduleBaseDir.'images/planet32.png',
                         'link' => $this -> moduleBaseUrl);
        }
    }

    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
  if ($currentUser -> getType() == "administrator") {
   return array ( array ('title' => _HOME, 'link' => $currentUser -> getType() . ".php?ctg=control_panel"),
       array ('title' => _TRANSLATE_TRANSLATE, 'link' => $this -> moduleBaseUrl));
  } else {
   if (isset($_SESSION['s_lessons_ID'])) {
    $currentLesson = $this -> getCurrentLesson();
    return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
        array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getType() . ".php?ctg=control_panel"),
        array ('title' => _TRANSLATE_TRANSLATE, 'link' => $this -> moduleBaseUrl));
   } else {
    return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$_SESSION['s_type'].".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
        array ('title' => _TRANSLATE_TRANSLATE, 'link' => $this -> moduleBaseUrl));

   }
  }
    }

    public function getLinkToHighlight() {
        return 'translate_link_id1';
    }

    public function getModuleIcon() {
        return $this -> moduleBaseLink.'images/planet32.png';
    }

}
?>
