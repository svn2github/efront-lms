<?php

function addLanguagesDB() {
	$langsArray[] = array('name' => 'english', 'active' => (setlocale(LC_ALL, "english") == "") ? 0:1, 'translation' => 'english');
	$langsArray[] = array('name' => 'arabic',  'active' => (setlocale(LC_ALL, "arabic") == "") ? 0:1, 'translation' => 'العربية');
	$langsArray[] = array('name' => 'bulgarian',   'active' => (setlocale(LC_ALL, "bulgarian") == "") ? 0:1, 'translation' => 'български');
	$langsArray[] = array('name' => 'chinese_simplified',   'active' => (setlocale(LC_ALL, "chinese") == "") ? 0:1, 'translation' => '中国简化');
	$langsArray[] = array('name' => 'chinese_traditional',   'active' => (setlocale(LC_ALL, "chinese") == "") ? 0:1, 'translation' => '中國傳統');
	$langsArray[] = array('name' => 'croatian',   'active' => (setlocale(LC_ALL, "croatian") == "") ? 0:1, 'translation' => 'hrvatski');
	$langsArray[] = array('name' => 'czech',   'active' => (setlocale(LC_ALL, "czech") == "") ? 0:1, 'translation' => 'česky');
	$langsArray[] = array('name' => 'danish',   'active' => (setlocale(LC_ALL, "danish") == "") ? 0:1, 'translation' => 'dansk');
	$langsArray[] = array('name' => 'dutch',   'active' => (setlocale(LC_ALL, "dutch") == "") ? 0:1, 'translation' => 'nederlands');
	$langsArray[] =	array('name' => 'finnish',   'active' => (setlocale(LC_ALL, "finnish") == "") ? 0:1, 'translation' => 'suomi');
	$langsArray[] = array('name' => 'french',   'active' => (setlocale(LC_ALL, "french") == "") ? 0:1, 'translation' => 'français');
	$langsArray[] = array('name' => 'german',   'active' => (setlocale(LC_ALL, "german") == "") ? 0:1, 'translation' => 'deutsch');
	$langsArray[] = array('name' => 'greek',   'active' => (setlocale(LC_ALL, "greek") == "") ? 0:1, 'translation' => 'ελληνικά');
	$langsArray[] = array('name' => 'hindi',   'active' => (setlocale(LC_ALL, "hindi") == "") ? 0:1, 'translation' => 'हिन्दी');
	$langsArray[] = array('name' => 'italian',   'active' => (setlocale(LC_ALL, "italian") == "") ? 0:1, 'translation' => 'italiano');
	$langsArray[] = array('name' => 'japanese',   'active' => (setlocale(LC_ALL, "japanese") == "") ? 0:1, 'translation' => '日本語');
	$langsArray[] = array('name' => 'norwegian',   'active' => (setlocale(LC_ALL, "norwegian") == "") ? 0:1, 'translation' => 'norsk');
	$langsArray[] = array('name' => 'polish',   'active' => (setlocale(LC_ALL, "polish") == "") ? 0:1, 'translation' => 'polski');
	$langsArray[] = array('name' => 'portuguese',   'active' => (setlocale(LC_ALL, "portuguese") == "") ? 0:1, 'translation' => 'português');
	$langsArray[] = array('name' => 'romanian',   'active' => (setlocale(LC_ALL, "romanian") == "") ? 0:1, 'translation' =>'română');
	$langsArray[] = array('name' => 'russian',   'active' => (setlocale(LC_ALL, "russian") == "") ? 0:1, 'translation' => 'русский');
	$langsArray[] = array('name' => 'spanish',   'active' => (setlocale(LC_ALL, "spanish") == "") ? 0:1, 'translation' => 'español');
	$langsArray[] = array('name' => 'swedish',   'active' => (setlocale(LC_ALL, "swedish") == "") ? 0:1, 'translation' => 'svenska');
	
	foreach ($langsArray as $value) {
		try{
			eF_insertTableData("languages", $value);
		}
		catch (Exception $e) {}
			
	}	
}
?>