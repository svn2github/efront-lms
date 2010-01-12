<?php
class module_wiki extends EfrontModule {

	public function getName() {
        return _WIKI_WIKI;
    }

    public function getPermittedRoles() {
        return array("student","professor");
    }
	
	public function getModuleJs() {
		return $this->moduleBaseDir."module_wiki.js";
	}

	public function getModule() {
			$languageSymbols = array(   "arabic"    => "Ar",
                                        "bulgarian" => "Bg",
                                        "czech"     => "Cs",
                                        "danizh"    => "Da",
                                        "german"    => "De",
                                        "greek"     => "El",
                                        "spanish"   => "Es",
                                        "english"   => "En",
                                        "finnish"   => "Fi",
                                        "french"    => "Fr",
                                        "hindi"     => "Hi",
                                        "croatian"  => "Hr",
                                        "italian"   => "It",
                                        "japanese"  => "Ja",
                                        "dutch"     => "Nl",
                                        "norwegian" => "No",
                                        "polish"    => "Pl",
                                        "portuguese" => "Pt",
                                        "romanian"   => "Ro",
                                        "russian"   => "Ru",
                                        "swedish"   => "Sv",
                                        "chinese_traditional" => "Zh_Cn",
                                        "chinese_simplified" => "Zh_Cn");	
		$_SESSION['servername'] = G_SERVERNAME;
		$configFile = file($this -> moduleBaseDir.'/local/config.php');
		$symbolCap 	= $languageSymbols[$_SESSION['s_language']];
		$symbol 	= strtolower($symbolCap);
		$configFile[sizeof($configFile)-1] = "XLPage('".$symbol."','PmWiki".$symbolCap.".XLPage');";
		file_put_contents($this -> moduleBaseDir.'/local/config.php', $configFile);
		if (isset($_GET['n'])) {
			return '<iframe id="wiki_frame" frameborder="0" src ="'.G_LESSONSLINK.$_SESSION['s_lessons_ID'].'/wiki/index.php?n='.$_GET['n'].'" width="100%"></iframe>';
		} else {
			return '<iframe id="wiki_frame" frameborder="0" src ="'.G_LESSONSLINK.$_SESSION['s_lessons_ID'].'/wiki/index.php" width="100%"></iframe>';
		}
		//return '<iframe id="wiki_frame" frameborder="0" src ="'.G_LESSONSLINK.$_SESSION['s_lessons_ID'].'/wiki/index.php" width="100%"></iframe>';
		//<script>window.document.getElementById("wiki_frame").style.height=parseInt(top.sideframe.document.documentElement.scrollHeight-60)+ "px";</script>';
		//return '<script>document.location.href="'.G_LESSONSLINK.$_SESSION['s_lessons_ID'].'/wiki/index.php"</script>';
	
	}

	public function getLessonModule() {
		$smarty = $this -> getSmartyVar();
		$inner_table_options = array(array('text' => _WIKI_GOTOWIKI,
		         'image' => $this -> moduleBaseLink."images/go_into.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_WIKI_INNERTABLE_OPTIONS", $inner_table_options);
		$recentChanges = file(G_LESSONSPATH.$_SESSION['s_lessons_ID'].'/wiki/wiki.d/Site.AllRecentChanges');
		$recentChangesAssoc = array();
		for ($k = 0; $k < sizeof($recentChanges); $k++) {
		$key = mb_substr($recentChanges[$k], 0, mb_strpos($recentChanges[$k], "="));
		$recentChangesAssoc[$key] = mb_substr($recentChanges[$k], mb_strpos($recentChanges[$k],"=")+1);
		}
		$pagesArray = explode("*",$recentChangesAssoc['text']);
		$wikiPages 	= array();
		$dateArray  = array();
		for($k = 1; $k < sizeof($pagesArray); $k++) {
			$end 		=   mb_strrpos($pagesArray[$k],"]]");
			$author		= 	mb_substr($pagesArray[$k],mb_strpos($pagesArray[$k],"~")+1, $end-mb_strpos($pagesArray[$k],"~")-1);
			
			$tempValue 	= mb_substr($pagesArray[$k], mb_strpos($pagesArray[$k], ". . .")+5, 20);
			$value 		= str_replace(array('[[',']]'), "", $tempValue);
			$key		= mb_substr($pagesArray[$k], mb_strpos($pagesArray[$k], "[[")+2, mb_strpos($pagesArray[$k], "]]")-3);

			$value 		= str_replace(array("-",":"), " ", $value);
			$dateArray 	= explode(" ", $value);
			$timestamp 	= mktime($dateArray[4], $dateArray[5], $dateArray[6], $dateArray[1], $dateArray[2], $dateArray[3]);
			$wikiPages[$key] = trim($author).", ".eF_convertIntervalToTime(time() - $timestamp, true).' '._AGO;
		}
		$smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_WIKI_WIKIPAGES" , $wikiPages);
        return true;
	}

    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_WIKI_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_WIKI_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_WIKI_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }

    public function getSidebarLinkInfo() {

    	$currentUser = $this -> getCurrentUser();
        $link_of_menu_system = array (array ('id' => 'wiki_link_id1',
                                              	'title' => _WIKI_WIKI,
                                              	'image' => $this -> moduleBaseDir.'images/eFrontWiki16',
                                              	'eFrontExtensions' => '1',
                                              	'link'  => $this -> moduleBaseUrl));

		return array ("current_lesson" => $link_of_menu_system);
    }
	
	public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
		$currentLesson = $this -> getCurrentLesson();
            return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
							array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getRole($currentLesson).".php?ctg=control_panel"),
							array ('title' => _WIKI_WIKI, 'link'  => $this -> moduleBaseUrl));    
         
        
    }
	
	public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
            return array('title' => _WIKI_WIKI,
                         'image' => $this -> moduleBaseDir.'images/eFrontWiki32.png',
                         'link'  => $this -> moduleBaseUrl);
        }
    }

	public function getLinkToHighlight() {
        return 'wiki_link_id1';
    }
	
	public function onInstall() {
		$FarmConfig = "<?php if (!defined('PmWiki')) exit();\r\n \$EnableLocalConfig = 0;\r\n\$FarmPubDirUrl ='".G_SERVERNAME."modules/module_wiki/pub/';";
		file_put_contents(G_ROOTPATH.'www/modules/module_wiki/local/farmconfig.php', $FarmConfig);
		
		$result 	= eF_getTableDataFlat("lessons","id");
		$lesson_ids = $result['id']; 
		$config = '<?php include(\''.$this -> moduleBaseDir.'pmwiki.php\');';
		foreach ($lesson_ids as $value) {
			if (!file_exists(G_LESSONSPATH.$value.'/wiki/')) {
				EfrontDirectory :: createDirectory(G_LESSONSPATH.$value.'/wiki/');
			}
			if(!file_exists(G_LESSONSPATH.$value.'/wiki/wiki.d/')) {
				EfrontDirectory :: createDirectory(G_LESSONSPATH.$value.'/wiki/wiki.d/');
			}
			file_put_contents(G_LESSONSPATH.$value.'/wiki/index.php', $config);
		}
		return true;
	}
	
	
	public function onNewLesson ($lessonId) {		
		if (!file_exists(G_LESSONSPATH.$lessonId.'/')) {
			EfrontDirectory :: createDirectory(G_LESSONSPATH.$lessonId.'/');
		}	
		if (!file_exists(G_LESSONSPATH.$lessonId.'/wiki/')) {
			EfrontDirectory :: createDirectory(G_LESSONSPATH.$lessonId.'/wiki/');
		}
		if (!file_exists(G_LESSONSPATH.$lessonId.'/wiki/wiki.d/')){
			EfrontDirectory :: createDirectory(G_LESSONSPATH.$lessonId.'/wiki/wiki.d/');
		}
		$config = '<?php include(\''.$this -> moduleBaseDir.'pmwiki.php\');';
		file_put_contents(G_LESSONSPATH.$lessonId.'/wiki/index.php', $config);
	}
	
	public function isLessonModule() {
		return true;
	}	
}
?>