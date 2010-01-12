<?php
class module_flashcards extends EfrontModule {

	public function getName() {
        return _FLASHCARDS_FLASHCARDS;
    }

    public function getPermittedRoles() {
        return array("student","professor","administrator");
    }

	public function getModule() {
		$smarty = $this -> getSmartyVar();
		$currentLesson = $this -> getCurrentLesson();
        $currentUser   = $this -> getCurrentUser();		
			try {
				$currentContent = new EfrontContentTree($_SESSION['s_lessons_ID']);           //Initialize content
			} catch (Exception $e) {
				$smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
				$message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
			}
		//pr($currentUser);exit;
		$roles = EfrontUser :: getRoles();
		//pr($roles);
		if ($roles[$currentUser ->lessons[$_SESSION['s_lessons_ID']]] == "professor") {
			if (isset($_GET['view_deck']) && eF_checkParameter($_GET['view_deck'],'id')) {
				$deck				= $currentContent -> seekNode($_GET['view_deck']);
				$questions 			= $deck -> getQuestions(true);
				$cards 				= array();
				$possibleCardsIds 	= array();
				foreach ($questions as $key => $value) {
					if ($value -> question['type'] == 'empty_spaces'){
						$cards[] 			= $value;
						$possibleCardsIds[] = $value->question['id'];	
					}
				}
				$questions = $cards;
		//pr($questions);
				foreach ($questions as $qid => $question) {
					$questions[$qid]->question['text']             = strip_tags($question->question['text']);        //If we ommit this line, then the questions list is html formatted, images are displayed etc, which is *not* the intended behaviour
					//$questions[$qid]->question['answer']           = unserialize($question->question['answer']);
				}
				$res = eF_getTableData("module_flashcards_decks", "cards,options", "content_ID=".$_GET['view_deck']);
				$resCards = unserialize($res[0]['cards']); 	
				$smarty -> assign("T_FLASHCARDS_DECK_CARDS", $resCards);  
			
				$post_target =  $this -> moduleBaseUrl.'&view_deck='.$_GET['view_deck']."&tab=options";
				//Create form elements
				$form = new HTML_QuickForm("deck_options", "post", $post_target, "", null, true);
				$form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
				$form -> addElement('advcheckbox', 'active',_FLASHCARDS_ACTIVE, null, 'class = "inputCheckbox"', array(0, 1));
				$form -> addElement("text", "low", _LOW,'size = "5"');
				$form -> addElement("text", "medium", _MEDIUM,'size = "5"');
				$form -> addElement("text", "hard", _HIGH,'size = "5"');
				$form -> addElement("text", "very_hard", _VERYHIGH,'size = "5"');
				$form  -> addRule('low', _INVALIDFIELDDATA.":"._LOW, 'checkParameter', 'id');
				$form  -> addRule('medium', _INVALIDFIELDDATA.":"._MEDIUM, 'checkParameter', 'id');
				$form  -> addRule('hard', _INVALIDFIELDDATA.":"._HIGH, 'checkParameter', 'id');
				$form  -> addRule('very_hard', _INVALIDFIELDDATA.":"._VERYHIGH, 'checkParameter', 'id');
				$form -> addElement('advcheckbox', 'answer_first',_FLASHCARDS_SHOWANSWERFIRST, null, 'class = "inputCheckbox"', array(0, 1));
				$form -> addElement('advcheckbox', 'shuffle', _FLASHCARDS_SHUFFLECARDS, null, 'class = "inputCheckbox"', array(0, 1));
				$form -> addElement('advcheckbox', 'display_mastery', _FLASHCARDS_DISPLAYMASTERY, null, 'class = "inputCheckbox"', array(0, 1));
				$form -> addElement('advcheckbox', 'wrong', _FLASHCARDS_WRONGREDUCES, null, 'class = "inputCheckbox"', array(0, 1));
				$form -> addElement('advcheckbox', 'show_count', _FLASHCARDS_SHOWSUCCESSCOUNT, null, 'class = "inputCheckbox"', array(0, 1));
				$form -> addElement('advcheckbox', 'show_explanation', _FLASHCARDS_SHOWEXPLANATION, null, 'class = "inputCheckbox"', array(0, 1));
				$form -> addElement('submit', 'submit_options', _SAVECHANGES, 'class = "flatButton"');       //The submit content button
	
				$options = unserialize($res[0]['options']); 		
				
				$form -> setDefaults(array(	'active' 			=> $options['active'],
											'answer_first' 		=> $options['answer_first'],
											'shuffle' 			=> $options['shuffle'],
											'display_mastery' 	=> $options['display_mastery'],
											'wrong' 			=> $options['wrong'],
											'show_count' 		=> $options['show_count'],
											'show_explanation' 	=> $options['show_explanation'],
											'low' 				=> ($options['low'] == "" ? 1 : $options['low']),
											'medium' 			=> ($options['medium'] == "" ? 2 : $options['medium']),
											'hard' 				=> ($options['hard'] == "" ? 4 : $options['hard']),
											'very_hard' 		=> ($options['very_hard'] == "" ? 6 : $options['very_hard'])));
			
				if ($form -> isSubmitted() && $form -> validate()) {                                                              //If the form is submitted and validated
					$values = $form -> exportValues();
					unset($values['submit_options']);
					$options = serialize($values);
					if (sizeof($res) != 0) {
						$ok = eF_updateTableData("module_flashcards_decks", array('options' => $options),"content_ID=".$_GET['view_deck']);
					} else {
						$fields = array ('content_ID' 	=> 	$_GET['view_deck'],
										'options'		=>	$options);
						$ok = eF_insertTableData("module_flashcards_decks", $fields);
					}
					if ($ok !== false) {
						$message      = _FLASHCARDS_SUCCESSFULLY;
						$message_type = 'success';
					} else {
						$message      = _FLASHCARDS_PROBLEMOCCURED;
						$message_type = 'failure';
					}
					eF_redirect("".$this -> moduleBaseUrl."&view_deck=".$_GET['view_deck']."&tab=options&message=".$message."&message_type=".$message_type);
				}
				
				$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer

				$form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
				$form -> setRequiredNote(_REQUIREDNOTE);
				$form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created
				$smarty -> assign('T_FLASHCARDS_OPTIONS', $renderer -> toArray());                     //Assign the form to the template
			
				if (isset($_GET['postAjaxRequest'])) {
					try {
						$result = eF_getTableData("module_flashcards_decks", "cards", "content_ID=".$_GET['view_deck']);
					//pr($result);exit;
						$cardsArray 	= unserialize($result[0]['cards']);
						if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id')) {	
							if (!in_array($_GET['id'], array_values($cardsArray))) {
								$cardsArray[] 	= $_GET['id'];
								$cards = serialize($cardsArray);		
								if (sizeof($result) != 0) {
									$fields = array('cards'			=>	$cards);
									eF_updateTableData("module_flashcards_decks", $fields, "content_ID=".$_GET['view_deck']);
								} else {
									$fields = array ('content_ID' 	=> 	$_GET['view_deck'],
													'cards'			=>	$cards);
									eF_insertTableData("module_flashcards_decks", $fields);
								}
							}
							elseif (in_array($_GET['id'], array_values($cardsArray))) {
								unset($cardsArray[array_search($_GET['id'], $cardsArray)]);
								if (!empty($cardsArray)) {
									$cards = serialize($cardsArray);	
									$fields = array('cards'			=>	$cards);
									eF_updateTableData("module_flashcards_decks", $fields,"content_ID=".$_GET['view_deck']);
								} else {
									eF_deleteTableData("module_flashcards_decks", "content_ID=".$_GET['view_deck']);
								}
							}
						} else if (isset($_GET['addAll'])) {
							$cards = serialize($possibleCardsIds);
							if (sizeof($result) != 0) { 
								$fields = array('cards'			=>	$cards);
								eF_updateTableData("module_flashcards_decks", $fields,"content_ID=".$_GET['view_deck']);
							} else {
								$fields = array ('content_ID' 	=> 	$_GET['view_deck'],
												'cards'			=>	$cards);
								eF_insertTableData("module_flashcards_decks", $fields);
							}
						} else if (isset($_GET['removeAll'])) {
							$fields = array('cards'			=>	"");
							eF_updateTableData("module_flashcards_decks", $fields,"content_ID=".$_GET['view_deck']);
						
						}
					} catch (Exception $e) {
						header("HTTP/1.0 500 ");
						echo $e -> getMessage().' ('.$e -> getCode().')';
					}
					exit;
				}

				$smarty -> assign("T_FLASHCARDS_CARDS", $cards);
				$smarty -> assign("T_FLASHCARDS_CARDS_SIZE", sizeof($cards));
			} else { 
				$decksArray 	= array();
				$iterator = new EfrontTheoryFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($currentContent -> tree, RecursiveIteratorIterator :: SELF_FIRST)));  
				foreach ($iterator as $key => $value) {	
					$questions 	= $value -> getQuestions(true);
					$cards 		= array();
					foreach ($questions as $key2 => $value2) {
						if ($value2 -> question['type'] == 'empty_spaces'){
							$cards[] 			= $value2;
						}
					}

					if (sizeof($cards) > 0) {
						$decksArray[$value['id']] = array('id' 		=> $value['id'],
														'name' 		=> $value['name'],
														'questions' => sizeof($cards));
					}
				}
				if (!empty($decksArray)) {
					$str = implode(",",array_keys($decksArray));
					$decks = eF_getTableDataFlat("module_flashcards_decks","*","content_ID IN (".$str.")");
					$decksTemp 	= array_combine(array_values($decks['content_ID']) , array_values($decks['options']));
					$decksTemp2 = array_combine(array_values($decks['content_ID']) , array_values($decks['cards']));
					foreach ($decksArray as $key => $value) {
						$decksArray[$value['id']]['options'] = unserialize($decksTemp[$key]);
						$cardsTemp = unserialize($decksTemp2[$key]);
						$decksArray[$value['id']]['num_cards'] = empty($cardsTemp) ? 0 : sizeof($cardsTemp);
					}
				}
				//pr($decksArray);
				$smarty -> assign("T_FLASHCARDS_DECKS", $decksArray);
			}
		} elseif ($roles[$currentUser->lessons[$_SESSION['s_lessons_ID']]] == "student") {
			if (isset($_GET['restart_deck']) && eF_checkParameter($_GET['restart_deck'],'id')) {
				eF_deleteTableData("module_flashcards_users_to_cards", "users_LOGIN='".$_SESSION['s_login']."' AND content_ID=".$_GET['restart_deck']);
			}
			if (isset($_GET['restart_decks'])) {
				eF_deleteTableData("module_flashcards_users_to_cards", "users_LOGIN='".$_SESSION['s_login']."'");
			}
			if ($_GET['answer'] == "true") {
				$resUserCard = eF_getTableData("module_flashcards_users_to_cards", "*", "cards_ID=".$_GET['view_card']." and users_LOGIN='".$_SESSION['s_login']."'");
				if (sizeof($resUserCard) == 0) {
					$fields = array('users_LOGIN'	=> 	$_SESSION['s_login'],
									'content_ID' 	=>	$_GET['view_deck'],
									'cards_ID'		=>	$_GET['view_card'],
									'success'		=>	'1');
					eF_insertTableData("module_flashcards_users_to_cards", $fields);
				} else {
					$success = $resUserCard[0]['success'] + 1; 
					eF_updateTableData("module_flashcards_users_to_cards",array('success' => $success),"cards_ID=".$_GET['view_card']." and users_LOGIN='".$_SESSION['s_login']."'");
				}
			} elseif($_GET['answer'] == "false") {
				$resUserCard		= eF_getTableData("module_flashcards_users_to_cards", "*", "cards_ID=".$_GET['view_card']." and users_LOGIN='".$_SESSION['s_login']."'");
				$currentDeckTemp	= eF_getTableData("module_flashcards_decks","options","content_ID=".$_GET['view_deck']);
				$deckTemp =  unserialize($currentDeckTemp[0]['options']);
				if ($deckTemp['wrong'] == 1 && sizeof($resUserCard) != 0 && $resUserCard[0]['success'] != 0) {
					$success = $resUserCard[0]['success'] - 1; 
					eF_updateTableData("module_flashcards_users_to_cards",array('success' => $success),"cards_ID=".$_GET['view_card']." and users_LOGIN='".$_SESSION['s_login']."'");			
				}
			}
				$decksArray 	= array();
				$iterator = new EfrontTheoryFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($currentContent -> tree, RecursiveIteratorIterator :: SELF_FIRST)));  
				foreach ($iterator as $key => $value) {	
					$decksArray[$value['id']] = array(	'id' 		 => $value['id'],
														'name' 		 => $value['name']);
				}
				if (empty($decksArray)) {
					$smarty -> assign("T_FLASHCARDS_DECKSNAMES", $decksArray);
					return true;
				}
				$str = implode(",",array_keys($decksArray));
				$decks = eF_getTableData("module_flashcards_decks","*","content_ID IN (".$str.")");
				$mastery = eF_getTableDataFlat("module_flashcards_users_to_cards","*","content_ID IN (".$str.")");
				$masteryArray = array_combine(array_values($mastery['cards_ID']) , array_values($mastery['success']));
				$questionsDiff = eF_getTableDataFlat("questions","*","content_ID IN (".$str.")");
				$questionsDiffArray = array_combine(array_values($questionsDiff['id']) , array_values($questionsDiff['difficulty']));
				$validDecks = array();
				foreach ($decks as $key => $value) {
					$opt   = unserialize($value['options']);
					$cards = unserialize($value['cards']);
					if ($opt['active'] == 1 && !empty($cards)) {
						$value['number_cards'] = (empty($cards) ? 0 : sizeof($cards));
						$validDecks[$value['content_ID']] = $value;
						$validDecks[$value['content_ID']]['cards'] = $cards;
						$validDecks[$value['content_ID']]['options'] = $opt;
						$finishedCards = 0;
						foreach ($cards as $index => $item) {
							if($masteryArray[$item] == $opt[$questionsDiffArray[$item]]) {
								$finishedCards++;
							}
						}
						$validDecks[$value['content_ID']]['non_finished'] 	= $value['number_cards'] - $finishedCards;
						$validDecks[$value['content_ID']]['mastery'] 		= ((float)$finishedCards/sizeof($cards)*100);
						
					}
				}
				//pr($masteryArray);
				//pr($validDecks);
				//pr($decksArray);
				$smarty -> assign("T_FLASHCARDS_DECKS", $validDecks);
				$smarty -> assign("T_FLASHCARDS_DECKSNAMES", $decksArray);
				
				if(isset($_GET['view_deck'])) {	
					$currentDeck = $validDecks[$_GET['view_deck']];
					$resUserSuccess = eF_getTableDataFlat("module_flashcards_users_to_cards", "*", "content_ID=".$_GET['view_deck']." and users_LOGIN='".$_SESSION['s_login']."'");
					$successArray = array_combine(array_values($resUserSuccess['cards_ID']) , array_values($resUserSuccess['success']));
					//pr($successArray);
					foreach ($currentDeck['cards'] as $key => $value) {
						$questionTemp = new EmptySpacesQuestion($value); 
						$limit = $currentDeck['options'][$questionTemp->question['difficulty']];
						if ($successArray[$value] == $limit && $value != $_GET['view_card']) {
								unset($currentDeck['cards'][$key]);
						}	
					}
					$currentDeck['cards'] = array_values($currentDeck['cards']);
					if ($currentDeck['options']['shuffle'] == 1) {
						shuffle($currentDeck['cards']);
					}
					if (!empty($currentDeck['cards'])) {
						if (isset($_GET['view_card'])) {
							while ((current($currentDeck['cards']) != $_GET['view_card']) & next($currentDeck['cards']) !== false);
							if (current($currentDeck['cards']) === false) {
								reset($currentDeck['cards']);
							} 
							$_GET['view_card'] = current($currentDeck['cards']);
						} else {
							$_GET['view_card'] = $currentDeck['cards'][0];	
						}
						//echo $_GET['view_card'];
						$question 	= new EmptySpacesQuestion($_GET['view_card']); 				
						$limit = $currentDeck['options'][$question->question['difficulty']];
						if ($successArray[$_GET['view_card']] == $limit) {
							$message      = _FLASHCARDS_SUCCESSFULLYCOMPLETEDDECK;
							$message_type = 'success';
							eF_redirect($this -> moduleBaseUrl."&reset_popup=1&message=".urlencode($message)."&message_type=".$message_type, true, 'parent');	
						} else {
							//$form = new HTML_QuickForm("questionForm", "post", "", "", null, true);
							$form = new HTML_QuickForm();
							$question -> toHTMLQuickForm($form);

							foreach ($question -> answer as $key => $value) {
							    $form -> setDefaults(array("question[".$question -> question['id']."][$key]" => "________"));
							}
							$form -> freeze();

							$smarty -> assign("T_FLASHCARDS_CURRENTCARD_PREVIEW", $question -> toHTML($form));
							//$smarty -> assign("T_FLASHCARDS_CURRENTCARD_PREVIEW_ANSWERED", $question -> toHTMLSolved(new HTML_QuickForm(), true, false, false));
							$smarty -> assign("T_FLASHCARDS_CURRENTCARD_PREVIEW_ANSWERED", implode("<br/>", $question -> answer));
						}
					} else {
						$message      = _FLASHCARDS_SUCCESSFULLYCOMPLETEDDECK;
						//$message_type = 'success';
						eF_redirect("".$this -> moduleBaseUrl."&popup=1&finish=1&message=".$message."&message_type=".$message_type);
					}
					//pr($question);
					//pr($currentDeck);
					$smarty -> assign("T_FLASHCARDS_CURRENTDECK", $currentDeck);
					$smarty -> assign("T_FLASHCARDS_CURRENTCARD", $question);
//pr($currentDeck);					
					$smarty -> assign("T_FLASHCARDS_SUCCESSARRAY", $successArray);
					$smarty -> assign ("T_FLASHCARDS_LESSONNAME",$currentLesson -> lesson['name']);
				}
		}
		return true;
	}
	
	public function getSmartyTpl(){
		$smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_FLASHCARDS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_FLASHCARDS_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_FLASHCARDS_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
	} 
	
	public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
            return array('title' => _FLASHCARDS_FLASHCARDS,
                         'image' => $this -> moduleBaseDir.'images/flashcard32.png',
                         'link'  => $this -> moduleBaseUrl);
    }

    public function getSidebarLinkInfo() {

    	$currentUser = $this -> getCurrentUser();
        $link_of_menu_system = array (array ('id' => 'flashcards_link_id1',
                                              	'title' => _FLASHCARDS_FLASHCARDS,
                                              	'image' => $this -> moduleBaseDir.'images/flashcard16',
                                              	'eFrontExtensions' => '1',
                                              	'link'  => $this -> moduleBaseUrl));

		return array ("current_lesson" => $link_of_menu_system);
    }
	
	public function getNavigationLinks() {
        $currentUser 	= $this -> getCurrentUser();
		$currentLesson 	= $this -> getCurrentLesson();
		if (isset($_GET['view_deck'])){
			$res = eF_getTableData("content","name","id=".$_GET['view_deck']);
            return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
							array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
							array ('title' => _FLASHCARDS_FLASHCARDS, 'link'  => $this -> moduleBaseUrl),
							array ('title' => $res[0]['name'], 'link'  => $this -> moduleBaseUrl."&view_deck=".$_GET['view_deck']));    
        } else{
			return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
							array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getRole($currentLesson).".php?ctg=control_panel"),
							array ('title' => _FLASHCARDS_FLASHCARDS, 'link'  => $this -> moduleBaseUrl));   
		}
        
    }

	public function getLinkToHighlight() {
        return 'flashcards_link_id1';
    }
	
	public function onInstall() {		
		$res1 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_flashcards_decks` (
								`content_ID` int(10) unsigned NOT NULL,
								`cards` text,
								`options` text
								) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
	
		$res2 = eF_executeNew("CREATE TABLE `module_flashcards_users_to_cards` (
								`users_LOGIN` VARCHAR( 100 ) NOT NULL ,
								`content_ID` MEDIUMINT( 11 ) NOT NULL ,
								`cards_ID` MEDIUMINT( 11 ) NOT NULL ,
								`success` MEDIUMINT( 11 ) NOT NULL DEFAULT '0'
								) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
		return ($res1 && $res2);
	}

	
	public function onUninstall() {
        $res1 = eF_executeNew("DROP TABLE module_flashcards_users_to_cards;");
        $res2 = eF_executeNew("DROP TABLE module_flashcards_decks;");
        return ($res1 && $res2 && $res3 && $res4);
    }
	
	public function getModuleCSS (){
		return $this->moduleBaseDir.'flashcards_custom.css';
	}

	
	public function isLessonModule() {
		return true;
	}	
}
?>