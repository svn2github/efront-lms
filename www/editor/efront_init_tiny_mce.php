<?php
error_reporting(E_ERROR);
session_start();
include_once "../../libraries/language/lang-".$_SESSION['s_language'].".php.inc";

$str = '
tinyMCE.init(
    {
    mode : "specific_textareas",
    editor_selector : "simpleEditor",
    theme : "simple",
    language : "'._CURRENTLANGUAGESYMBOL.'",
    entity_encoding : "raw",
    force_p_newlines : false,
    force_br_newlines : true,
    convert_newlines_to_brs : true
    }
);

tinyMCE.init(
    {
    mode : "specific_textareas",
    language : "'._CURRENTLANGUAGESYMBOL.'",
    editor_selector : "mceEditor",
    theme : "advanced",
    theme_advanced_resizing : true,
    theme_advanced_resizing_use_cookie : false,
    entity_encoding : "raw",
    force_p_newlines : false,
    force_br_newlines : true,
    convert_newlines_to_brs : false,
    apply_source_formatting : true, 
    plugins : "table,save,advhr,advimage,advlink,emotions,iespell,preview,zoom,java,searchreplace,print,contextmenu,media,mathtype,paste,fullscreen",
    theme_advanced_buttons1_add_before : "save,separator",
    theme_advanced_buttons1_add : "fontselect,fontsizeselect,separator,bullist,separator,indent,outdent,separator,undo,redo,separator,link,unlink",
    theme_advanced_buttons2 : "zoom,separator,forecolor,backcolor",
    theme_advanced_buttons2_add_before : "table",
    theme_advanced_buttons2_add : "separator,code,charmap,separator,emotions,iespell,advhr,separator,sub,sup,separator,print,separator,image,java,media,separator,mathtype,pasteword,preview,fullscreen,separator,copy,paste,separator,search,anchor",
    theme_advanced_buttons3 : "",
    theme_advanced_disable : "formatselect,styleselect,help,cleanup,hr,removeformat,numlist",
    plugin_preview_width : "700",
    plugin_preview_height : "700",
    paste_create_paragraphs : false,
    paste_create_linebreaks : false,
    paste_use_dialog : true,
    paste_auto_cleanup_on_paste : true,
    paste_convert_middot_lists : false,
    paste_unindented_list_class : "unindentedList",
    paste_convert_headers_to_strong : true,
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    plugin_insertdate_dateFormat : "%Y-%m-%d",
    plugin_insertdate_timeFormat : "%H:%M:%S",
    extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],applet[code|codebase|width|height],embed[src|autostart|type],iframe[*],object[*]"
    }
);

tinyMCE.init(
    {
    mode : "specific_textareas",
    language : "'._CURRENTLANGUAGESYMBOL.'",
    editor_selector : "intermediateEditor",
    theme : "advanced",
    theme_advanced_resizing : true,
    theme_advanced_resizing_use_cookie : false,
    entity_encoding : "raw",
    force_p_newlines : false,
    force_br_newlines : true,
    convert_newlines_to_brs : false,
    apply_source_formatting : true, 
    plugins : "table,advhr,advimage,advlink,emotions,iespell,java,contextmenu,media,mathtype",
    theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,fontselect,fontsizeselect,separator,bullist,search,separator,link,unlink,separator,code,charmap,separator",
    theme_advanced_buttons1_add : "forecolor,separator,table,emotions,iespell,separator,image,java,media,separator,mathtype,sub,sup",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_disable : "formatselect,styleselect,strikethrough,cut,copy,paste,indent,outdent,help,cleanup,hr,anchor",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],applet[code|codebase|width|height],embed[src|autostart|type],object[*]"
    }
);

tinyMCE.init(
    {
    mode : "specific_textareas",
    language : "'._CURRENTLANGUAGESYMBOL.'",
    editor_selector : "templateEditor",
    theme : "advanced",
    theme_advanced_resizing : true,
    theme_advanced_resizing_use_cookie : false,
    entity_encoding : "raw",
    force_p_newlines : false,
    force_br_newlines : true,
    convert_newlines_to_brs : false,
    apply_source_formatting : true, 
    plugins : "table,save,advhr,advimage,advlink,emotions,iespell,preview,zoom,java,searchreplace,print,contextmenu,media,mathtype,paste,fullscreen,index_link,lessons_info",
    theme_advanced_buttons1_add_before : "save,separator",
    theme_advanced_buttons1_add : "fontselect,fontsizeselect,separator,bullist,separator,indent,outdent,separator,undo,redo,separator,link,unlink",
    theme_advanced_buttons2 : "zoom,separator,forecolor,backcolor",
    theme_advanced_buttons2_add_before : "table",
    theme_advanced_buttons2_add : "separator,code,charmap,separator,emotions,iespell,advhr,separator,sub,sup,separator,print,separator,image,java,media,separator,mathtype,pasteword,preview,fullscreen,separator,copy,paste,separator,search,anchor,index_link,lessons_info",
    theme_advanced_buttons3 : "",
    theme_advanced_disable : "formatselect,styleselect,help,cleanup,hr,removeformat,numlist",
    plugin_preview_width : "700",
    plugin_preview_height : "700",
    paste_create_paragraphs : false,
    paste_create_linebreaks : false,
    paste_use_dialog : true,
    paste_auto_cleanup_on_paste : true,
    paste_convert_middot_lists : false,
    paste_unindented_list_class : "unindentedList",
    paste_convert_headers_to_strong : true,
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    plugin_insertdate_dateFormat : "%Y-%m-%d",
    plugin_insertdate_timeFormat : "%H:%M:%S",
    extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],applet[code|codebase|width|height],embed[src|autostart|type],iframe[*],object[*]"
}
);

function mceAddControlDynamic(control_id, textarea_id, selector_class) {       
    var bControlAdded = false;                      
    for (c=0; c<tinyMCE.configs.length; c++) {            
         var configSettings = tinyMCE.configs[c];   
         if (configSettings.editor_selector && configSettings.editor_selector == selector_class)  {                   
               tinyMCE.settings = configSettings;                   
               tinyMCE.addMCEControl(document.getElementById(textarea_id), control_id);
               break;               
          }           
     }   
}
';
print $str;
?>