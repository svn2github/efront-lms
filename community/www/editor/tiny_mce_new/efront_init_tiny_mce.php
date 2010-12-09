<?php
session_start();

$path = '../../../libraries/';
require_once($path."configuration.php");
//include_once "../../libraries/language/lang-".$_SESSION['s_language'].".php.inc";
//include_once "../../libraries/configuration.php";

if (file_exists("langs/"._CURRENTLANGUAGESYMBOL.".js")) {
 $langFile = _CURRENTLANGUAGESYMBOL;
} else {
 $langFile = "en";
}

$str = '
var tinymceConfigs = new Array();
tinymceConfigs["simpleEditor"] = {
    mode : "specific_textareas",
    editor_selector : "simpleEditor",
    theme : "advanced",
    language : "'.$langFile.'",
 theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,link,unlink,separator,bullist,numlist,separator,undo,redo",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    entity_encoding : "raw",
    force_p_newlines : false,
 plugins : "media",
 convert_urls : false,
 extended_valid_elements : "*[*]"
    };

tinymceConfigs["digestEditor"] = {
    mode : "specific_textareas",
    editor_selector : "digestEditor",
    theme : "advanced",
    language : "'.$langFile.'",
 theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,link,unlink,separator,bullist,numlist,separator,undo,redo",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    entity_encoding : "raw",
    force_p_newlines : false,
 plugins : "media",
 convert_urls : false,
 content_css : "'.G_CURRENTTHEMEURL.'css/css_global.css",
 extended_valid_elements : "*[*]",
 handle_event_callback : "digestHandleEvent"
    };

tinymceConfigs["mceEditor"] = {
    mode : "specific_textareas",
    language : "'.$langFile.'",
    editor_selector : "mceEditor",
    theme : "advanced",
    theme_advanced_resizing : true,
    theme_advanced_resizing_use_cookie : false,
    entity_encoding : "raw",
    force_p_newlines : false,
 accessibility_warnings : false,
 verify_html : false,
 convert_urls : false,
    plugins : "java,asciimath,asciisvg,table,save,advhr,advimage,advlink,style,emotions,inlinepopups,iespell,preview,searchreplace,print,contextmenu,media,paste,directionality,fullscreen,template,save_template",
    theme_advanced_buttons1_add_before : "save,separator",
 theme_advanced_buttons1_add : "fontselect,fontsizeselect,separator,bullist,separator,indent,outdent,separator,undo,redo,separator,link,unlink",
    theme_advanced_buttons2 : "forecolor,backcolor",
    theme_advanced_buttons2_add_before : "table",
    theme_advanced_buttons2_add : "separator,emotions,iespell,advhr,separator,sub,sup,separator,print,separator,image,media,java,separator,pastetext,pasteword,selectall,preview,fullscreen,separator,copy,paste,separator,ltr,rtl,separator,search,anchor,separator,asciimath,asciimathcharmap,separator,template,save_template",
    theme_advanced_buttons3 : "",
    theme_advanced_disable : "help,cleanup,hr,removeformat,numlist",
    plugin_preview_width : "950",
    plugin_preview_height : "500",
 font_size_style_values : "10,13,16,18,24,32,48",
 theme_advanced_font_sizes : "10px,13px,16px,18px,24px,32px,48px",
    paste_use_dialog : true,
    paste_auto_cleanup_on_paste : true,
    paste_convert_middot_lists : false,
    paste_retain_style_properties : "all",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    plugin_insertdate_dateFormat : "%Y-%m-%d",
    plugin_insertdate_timeFormat : "%H:%M:%S",
 content_css : "'.G_CURRENTTHEMEURL.'css/css_global.css",
    extended_valid_elements : "a[name|href|target|title|onclick|class|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade|style],font[face|size|color|style],span[class|align|style],applet[code|codebase|width|height],embed[*],iframe[*],object[*]",
 template_external_list_url : "editor/templates_list.php"
 };

tinymceConfigs["templateEditor"] = {
    mode : "specific_textareas",
 translate_mode : true,
    language : "'.$langFile.'",
    editor_selector : "templateEditor",
    theme : "advanced",
    theme_advanced_resizing : true,
    theme_advanced_resizing_use_cookie : false,
    entity_encoding : "raw",
    force_p_newlines : false,
 accessibility_warnings : false,
 verify_html : false,
 convert_urls : false,
    plugins : "table,save,advhr,advimage,advlink,style,emotions,iespell,preview,zoom,searchreplace,print,contextmenu,media,paste,directionality,fullscreen,index_link,asciimath,asciisvg",
    theme_advanced_buttons1_add_before : "save,separator",
    theme_advanced_buttons1_add : "fontselect,fontsizeselect,separator,bullist,separator,indent,outdent,separator,undo,redo,separator,link,unlink",
    theme_advanced_buttons2 : "zoom,separator,forecolor,backcolor",
    theme_advanced_buttons2_add_before : "table",
    theme_advanced_buttons2_add : "separator,charmap,separator,emotions,iespell,advhr,separator,sub,sup,separator,print,separator,image,media,separator,pasteword,preview,fullscreen,separator,copy,paste,separator,ltr,rtl,separator,search,anchor,index_link,separator,asciimath,asciimathcharmap",
    theme_advanced_buttons3 : "",
    theme_advanced_disable : "formatselect,help,cleanup,hr,removeformat,numlist",
    plugin_preview_width : "950",
    plugin_preview_height : "500",
 font_size_style_values : "10,13,16,18,24,32,48",
 theme_advanced_font_sizes : "10px,13px,16px,18px,24px,32px,48px",
    paste_use_dialog : true,
    paste_auto_cleanup_on_paste : true,
    paste_convert_middot_lists : false,
    paste_retain_style_properties : "all",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    plugin_insertdate_dateFormat : "%Y-%m-%d",
    plugin_insertdate_timeFormat : "%H:%M:%S",
 content_css : "'.G_CURRENTTHEMEURL.'css/css_global.css",
    extended_valid_elements : "a[name|href|target|title|onclick|class|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade|style],font[face|size|color|style],span[class|align|style],applet[code|codebase|width|height],embed[*],iframe[*],object[*]"
};



tinyMCE.init(
tinymceConfigs["simpleEditor"]
);

tinyMCE.init(
tinymceConfigs["mceEditor"]
);

tinyMCE.init(
tinymceConfigs["templateEditor"]
);

tinyMCE.init(
tinymceConfigs["digestEditor"]
);

var tinyMCEmode = true;
function toggleEditor(id, editor_selector) {
 if (!tinyMCE.get(id)) {
  tinyMCE.settings = tinymceConfigs[editor_selector];
  tinyMCE.execCommand(\'mceAddControl\', false, id);
  tinyMCEmode = true;
 }
 else {
  //alert(tinyMCE.get(id).getContent());
  tinyMCE.execCommand(\'mceRemoveControl\', false, id);
  tinyMCEmode = false;
 }
}
function digestHandleEvent(e) {
       if (e.type == \'click\') {
           myActiveElement="";
       }
       return true;
}
';
print $str;
?>
