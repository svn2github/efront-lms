<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

function addLanguagesDB() {
 $langsArray[] = array('name' => 'albanian', 'active' => (setlocale(LC_ALL, "albanian") == "") ? 0:1, 'translation' => 'Shqipe');
 $langsArray[] = array('name' => 'arabic', 'active' => (setlocale(LC_ALL, "arabic") == "") ? 0:1, 'translation' => 'العربية');
 $langsArray[] = array('name' => 'brazilian', 'active' => (setlocale(LC_ALL, "portuguese") == "") ? 0:1, 'translation' => 'Brasileira');
 $langsArray[] = array('name' => 'bulgarian', 'active' => (setlocale(LC_ALL, "bulgarian") == "") ? 0:1, 'translation' => 'Български');
 $langsArray[] = array('name' => 'catalan', 'active' => (setlocale(LC_ALL, "catalan") == "") ? 0:1, 'translation' => 'Català');
 $langsArray[] = array('name' => 'chinese_simplified', 'active' => (setlocale(LC_ALL, "chinese") == "") ? 0:1, 'translation' => '中国简化');
 $langsArray[] = array('name' => 'chinese_traditional', 'active' => (setlocale(LC_ALL, "chinese") == "") ? 0:1, 'translation' => '中國傳統');
 $langsArray[] = array('name' => 'croatian', 'active' => (setlocale(LC_ALL, "croatian") == "") ? 0:1, 'translation' => 'Hrvatski');
 $langsArray[] = array('name' => 'czech', 'active' => (setlocale(LC_ALL, "czech") == "") ? 0:1, 'translation' => 'Česky');
 $langsArray[] = array('name' => 'danish', 'active' => (setlocale(LC_ALL, "danish") == "") ? 0:1, 'translation' => 'Dansk');
 $langsArray[] = array('name' => 'dutch', 'active' => (setlocale(LC_ALL, "dutch") == "") ? 0:1, 'translation' => 'Nederlands');
 $langsArray[] = array('name' => 'english', 'active' => 1, 'translation' => 'English');
 $langsArray[] = array('name' => 'filipino', 'active' => (setlocale(LC_ALL, "filipino") == "") ? 0:1, 'translation' => 'Filipino');
 $langsArray[] = array('name' => 'finnish', 'active' => (setlocale(LC_ALL, "finnish") == "") ? 0:1, 'translation' => 'Suomi');
 $langsArray[] = array('name' => 'french', 'active' => (setlocale(LC_ALL, "french") == "") ? 0:1, 'translation' => 'Français');
 $langsArray[] = array('name' => 'galician', 'active' => (setlocale(LC_ALL, "galician") == "") ? 0:1, 'translation' => 'Galego');
 $langsArray[] = array('name' => 'georgian', 'active' => (setlocale(LC_ALL, "georgian") == "") ? 0:1, 'translation' => 'ქართული');
 $langsArray[] = array('name' => 'german', 'active' => (setlocale(LC_ALL, "german") == "") ? 0:1, 'translation' => 'Deutsch');
 $langsArray[] = array('name' => 'greek', 'active' => (setlocale(LC_ALL, "greek") == "") ? 0:1, 'translation' => 'Eλληνικά');
 $langsArray[] = array('name' => 'hebrew', 'active' => (setlocale(LC_ALL, "hebrew") == "") ? 0:1, 'translation' => 'עברית');
 $langsArray[] = array('name' => 'hindi', 'active' => (setlocale(LC_ALL, "hindi") == "") ? 0:1, 'translation' => 'हिन्दी');
 $langsArray[] = array('name' => 'hungarian', 'active' => (setlocale(LC_ALL, "hungarian") == "") ? 0:1, 'translation' => 'Magyar');
 $langsArray[] = array('name' => 'indonesian', 'active' => (setlocale(LC_ALL, "indonesian") == "") ? 0:1, 'translation' => 'Indonesia');
 $langsArray[] = array('name' => 'italian', 'active' => (setlocale(LC_ALL, "italian") == "") ? 0:1, 'translation' => 'Italiano');
 $langsArray[] = array('name' => 'japanese', 'active' => (setlocale(LC_ALL, "japanese") == "") ? 0:1, 'translation' => '日本語');
 $langsArray[] = array('name' => 'latvian', 'active' => (setlocale(LC_ALL, "latvian") == "") ? 0:1, 'translation' => 'Latviešu');
 $langsArray[] = array('name' => 'lithuanian', 'active' => (setlocale(LC_ALL, "lithuanian") == "") ? 0:1, 'translation' => 'Lietuviškai');
 $langsArray[] = array('name' => 'norwegian', 'active' => (setlocale(LC_ALL, "norwegian") == "") ? 0:1, 'translation' => 'Norsk');
 $langsArray[] = array('name' => 'persian', 'active' => (setlocale(LC_ALL, "persian") == "") ? 0:1, 'translation' => 'فارسی');
 $langsArray[] = array('name' => 'polish', 'active' => (setlocale(LC_ALL, "polish") == "") ? 0:1, 'translation' => 'Polski');
 $langsArray[] = array('name' => 'portuguese', 'active' => (setlocale(LC_ALL, "portuguese") == "") ? 0:1, 'translation' => 'Português');
 $langsArray[] = array('name' => 'romanian', 'active' => (setlocale(LC_ALL, "romanian") == "") ? 0:1, 'translation' =>'Română');
 $langsArray[] = array('name' => 'russian', 'active' => (setlocale(LC_ALL, "russian") == "") ? 0:1, 'translation' => 'Pусский');
 $langsArray[] = array('name' => 'serbian', 'active' => (setlocale(LC_ALL, "serbian") == "") ? 0:1, 'translation' => 'Српски');
 $langsArray[] = array('name' => 'slovak', 'active' => (setlocale(LC_ALL, "slovak") == "") ? 0:1, 'translation' => 'Slovenčina');
 $langsArray[] = array('name' => 'slovenian', 'active' => (setlocale(LC_ALL, "slovenian") == "") ? 0:1, 'translation' => 'Slovenski');
 $langsArray[] = array('name' => 'spanish', 'active' => (setlocale(LC_ALL, "spanish") == "") ? 0:1, 'translation' => 'Español');
 $langsArray[] = array('name' => 'swedish', 'active' => (setlocale(LC_ALL, "swedish") == "") ? 0:1, 'translation' => 'Svenska');
 $langsArray[] = array('name' => 'thai', 'active' => (setlocale(LC_ALL, "thai") == "") ? 0:1, 'translation' => 'ไทย');
 $langsArray[] = array('name' => 'turkish', 'active' => (setlocale(LC_ALL, "english") == "") ? 0:1, 'translation' => 'Türkçe');
 $langsArray[] = array('name' => 'vietnamese', 'active' => (setlocale(LC_ALL, "vietnamese") == "") ? 0:1, 'translation' => 'Việt');


 try{
 foreach ($langsArray as $value) {
  eF_insertTableData("languages", $value);
 }
 eF_updateTableData("languages",array('name' => 'english'),"name='English'");
 }catch (Exception $e) {}
}
?>
