<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_security extends EfrontModule {
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
    public function getName() {
     //This is a language tag, defined in the file lang-<your language>.php
        return _MODULE_SECURITY_MODULESECURITY;
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
                     'image' => $this -> moduleBaseLink . 'img/security_agent.png',
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
  $feeds = $this->getRssFeeds(true, false);
  $smarty->assign("T_SECURITY_FEEDS", $feeds);
        return true;
    }
    private function checkLocalIssues() {
     $localIssues = array();
     if (is_dir("install/")) {
      $localIssues[] = _MODULE_SECURITY_INSTALLATIONFOLDERSTILLEXISTS;
     }
     if (ini_get("magic_quotes_gpc") == 1 || strtolower(ini_get("magic_quotes_gpc")) == "on") {
      $localIssues[] = _MODULE_SECURITY_MAGICQUOTESGPCISON;
     }
     $result = eF_getTableData("users", "login", "archive = 0 and active = 1 and ((login = 'student' and password = '04aed36b7da8d1b5d8c892cf91486cdb') or (login = 'professor' and password = 'da18be534843cf9f9edd60c89de6a8e7'))");
     if (!empty($result)) {
      $localIssues[] = _MODULE_SECURITY_DEFAULTACCOUNTSSTILLEXIST;
     }
     return $localIssues;
    }
    private function getRssFeeds($refresh = false, $limit = 10) {
     //session_write_close();
     $feedTitle = '';
     $feed = 'http://security.efrontlearning.net/feeds/posts/default';
     $str = '';
     if (!$refresh && $str = Cache::getCache('security_cache:'.$key)) {
      $rssString= $str;
     } else {
      $response = $this -> parseFeed($feed);
      !$limit OR $response = array_slice($response, 0, $limit);
      foreach ($response as $value) {
       $str .= '<li> '.formatTimestamp($value['timestamp']).' <a href = "'.$value['link'].'" target = "_NEW">'.$value['title'].'</a>'.$description.'</li>';
      }
      $rssString = $str;
      Cache::setCache('security_cache:'.$key, $str, 3600); //cache for one hour
     }
     return $rssString;
    }
    public function parseFeed($feed) {
     $context = stream_context_create(array('http' => array('timeout' => 3)));
     $xmlString = file_get_contents($feed, 0, $context);
        try {
            $iterator = new SimpleXMLIterator($xmlString);
            foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator :: SELF_FIRST) as $key => $value) {
                if ($key == 'item') {
                    $data = array('title' => (string)$value -> title, 'link' => (string)$value -> link, 'description' => (string)$value -> description);
                    if ($value -> pubDate) {
                     $data['timestamp'] = strtotime((string)$value -> pubDate);
                    } else if ($value -> date) {
                     $data['timestamp'] = (string)$value -> date;
                    }
                    $rss[] = $data;
                } else if ($key == 'entry') {
                 //pr(strtotime((string)$value -> updated));
                    $data = array('title' => (string)$value -> title, 'description' => (string)$value -> content, 'timestamp' => strtotime((string)$value -> updated));
                 foreach ($value->link as $link) {
                  if ($link['rel'] == 'alternate') {
                   $data['link'] = (string)$link['href'];
                  }
                 }
                    $rss[] = $data;
                }
            }
        } catch (Exception $e) {
            $rss[] = array('title' => '<span class = "emptyCategory">'._CONNECTIONERROR.'</span>', 'link' => 'javascript:void(0)');
        }
        return $rss;
    }
    /**

     * Specify which file to include for template

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSmartyTpl()

     */
    public function getSmartyTpl() {
     return $this -> moduleBaseDir."module_security_cpanel.tpl";
    }
    public function getControlPanelModule() {
     $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASELINK" , $this -> moduleBaseLink);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
  $feeds = $this->getRssFeeds();
  $smarty->assign("T_SECURITY_FEEDS", $feeds);
  $smarty -> assign("T_LOCAL_ISSUES", $this->checkLocalIssues());
  return true;
    }
    /**

     * Specify which file to include for template

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSmartyTpl()

     */
    public function getControlPanelSmartyTpl() {
     return $this -> moduleBaseDir."module_security_cpanel.tpl";
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
