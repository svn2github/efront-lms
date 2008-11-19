<?php

$path = "../../../../libraries/";  
//include_once $path."configuration.php"; // not include this one, because this includes session lang file.....
//include_once $path."database.php";
include_once $path."tools.php";



 
include_once $path."language/lang-".$_GET['langname'].".php.inc";  
$lang_file =_CURRENTLANGUAGESYMBOL;

$language_javascript = "
tinyMCE.addToLang('',{
bold_desc : '"._EDITOR_BOLD_DESC."',
italic_desc : '"._EDITOR_ITALIC_DESC."',
underline_desc : '"._EDITOR_UNDERLINE_DESC."',
striketrough_desc : '"._EDITOR_STRIKETROUGH_DESC."',
justifyleft_desc : '"._EDITOR_JUSTIFYLEFT_DESC."',
justifycenter_desc : '"._EDITOR_JUSTIFYCENTER_DESC."',
justifyright_desc : '"._EDITOR_JUSTIFYRIGHT_DESC."',
justifyfull_desc : '"._EDITOR_JUSTIFYFULL_DESC."',
bullist_desc : '"._EDITOR_BULLIST_DESC."',
numlist_desc : '"._EDITOR_NUMLIST_DESC."',
outdent_desc : '"._EDITOR_OUTDENT_DESC."',
indent_desc : '"._EDITOR_INDENT_DESC."',
undo_desc : '"._EDITOR_UNDO_DESC."',
redo_desc : '"._EDITOR_REDO_DESC."',
link_desc : '"._EDITOR_LINK_DESC."',
unlink_desc : '"._EDITOR_UNLINK_DESC."',
image_desc : '"._EDITOR_IMAGE_DESC."',
cleanup_desc : '"._EDITOR_CLEANUP_DESC."',
focus_alert : '"._EDITOR_FOCUS_ALERT."',
edit_confirm : '"._EDITOR_EDIT_CONFIRM."',
insert_link_title : '"._EDITOR_INSERT_LINK_TITLE."',
insert : '"._EDITOR_INSERT."',
update : '"._EDITOR_UPDATE."',
cancel : '"._EDITOR_CANCEL."',
insert_link_url : '"._EDITOR_INSERT_LINK_URL."',
insert_link_target : '"._EDITOR_INSERT_LINK_TARGET."',
insert_link_target_same : '"._EDITOR_INSERT_LINK_TARGET_SAME."',
insert_link_target_blank : '"._EDITOR_INSERT_LINK_TARGET_BLANK."',
insert_image_title : '"._EDITOR_INSERT_IMAGE_TITLE."',
insert_image_src : '"._EDITOR_INSERT_IMAGE_SRC."',
insert_image_alt : '"._EDITOR_INSERT_IMAGE_ALT."',
help_desc : '"._EDITOR_HELP_DESC."',
bold_img : \""._EDITOR_BOLD_IMG."\",
italic_img : \""._EDITOR_ITALIC_IMG."\",
underline_img : \""._EDITOR_UNDERLINE_IMG."\",
clipboard_msg : '"._EDITOR_CLIPBOARD_MSG."',
popup_blocked : '"._EDITOR_POPUP_BLOCKED."'
});";
  
    $fp = fopen($lang_file.".js", "w");
    fwrite($fp, $language_javascript, mb_strlen($language_javascript));
    fclose($fp);

$language_javascript_java = "
tinyMCE.addToLang('java',{
title : '"._EDITOR_JAVA_TITLE."',
desc : '"._EDITOR_JAVA_DESC."',
file : '"._EDITOR_JAVA_FILE."',
size : '"._EDITOR_JAVA_SIZE."',
list : '"._EDITOR_JAVA_LIST."',
props : '"._EDITOR_JAVA_PROPS."',
general : '"._EDITOR_JAVA_GENERAL."',
codebase : '"._EDITOR_JAVA_CODEBASE."'
});";

    $fp = fopen("../plugins/java/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_java, mb_strlen($language_javascript_java));
    fclose($fp);


$language_javascript_advhr =" 
tinyMCE.addToLang('',{
insert_advhr_desc : '"._EDITOR_ADVHR_INSERT_ADVHR_DESC."',
insert_advhr_width : '"._EDITOR_ADVHR_INSERT_ADVHR_WIDTH."',
insert_advhr_size : '"._EDITOR_ADVHR_INSERT_ADVHR_SIZE."',
insert_advhr_noshade : '"._EDITOR_ADVHR_INSERT_ADVHR_NOSHADE."'
});";

    $fp = fopen("../plugins/advhr/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_advhr, mb_strlen($language_javascript_advhr));
    fclose($fp);


$language_javascript_advimage =" 
tinyMCE.addToLang('advimage',{
tab_general : '"._EDITOR_ADVIMAGE_TAB_GENERAL."',
tab_appearance : '"._EDITOR_ADVIMAGE_TAB_APPEARANCE."',
tab_advanced : '"._EDITOR_ADVIMAGE_TAB_ADVANCED."',
general : '"._EDITOR_ADVIMAGE_GENERAL."',
title : '"._EDITOR_ADVIMAGE_TITLE."',
preview : '"._EDITOR_ADVIMAGE_PREVIEW."',
constrain_proportions : '"._EDITOR_ADVIMAGE_CONSTRAIN_PROPORTIONS."',
langdir : '"._EDITOR_ADVIMAGE_LANGDIR."',
langcode : '"._EDITOR_ADVIMAGE_LANGCODE."',
long_desc : '"._EDITOR_ADVIMAGE_LONG_DESC."',
style : '"._EDITOR_ADVIMAGE_STYLE."',
classes : '"._EDITOR_ADVIMAGE_CLASSES."',
ltr : '"._EDITOR_ADVIMAGE_LTR."',
rtl : '"._EDITOR_ADVIMAGE_RTL."',
id : '"._EDITOR_ADVIMAGE_ID."',
image_map : '"._EDITOR_ADVIMAGE_IMAGE_MAP."',
swap_image : '"._EDITOR_ADVIMAGE_SWAP_IMAGE."',
alt_image : '"._EDITOR_ADVIMAGE_ALT_IMAGE."',
mouseover : '"._EDITOR_ADVIMAGE_MOUSEOVER."',
mouseout : '"._EDITOR_ADVIMAGE_MOUSEOUT."',
misc : '"._EDITOR_ADVIMAGE_MISC."',
example_img : '"._EDITOR_ADVIMAGE_EXAMPLE_IMG."',
missing_alt : '"._EDITOR_ADVIMAGE_MISSING_ALT."'
});";

    $fp = fopen("../plugins/advimage/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_advimage, mb_strlen($language_javascript_advimage));
    fclose($fp);


$language_javascript_advlink =" 
tinyMCE.addToLang('advlink',{
general_tab : '"._EDITOR_ADVLINK_GENERAL_TAB."',
popup_tab : '"._EDITOR_ADVLINK_POPUP_TAB."',
events_tab : '"._EDITOR_ADVLINK_EVENT_PROPS."',
advanced_tab : '"._EDITOR_ADVLINK_ADVANCED_TAB."',
general_props : '"._EDITOR_ADVLINK_GENERAL_PROPS."',
popup_props : '"._EDITOR_ADVLINK_POPUP_PROPS."',
event_props : '"._EDITOR_ADVLINK_EVENT_PROPS."',
advanced_props : '"._EDITOR_ADVLINK_ADVANCED_PROPS."',
popup_opts : '"._EDITOR_ADVLINK_POPUP_OPTS."',
anchor_names : '"._EDITOR_ADVLINK_ANCHOR_NAMES."',
target_same : '"._EDITOR_ADVLINK_TARGET_SAME."',
target_parent : '"._EDITOR_ADVLINK_TARGET_PARENT."',
target_top : '"._EDITOR_ADVLINK_TARGET_TOP."',
target_blank : '"._EDITOR_ADVLINK_TARGET_BLANK."',
popup : '"._EDITOR_ADVLINK_POPUP."',
popup_url : '"._EDITOR_ADVLINK_POPUP_URL."',
popup_name : '"._EDITOR_ADVLINK_POPUP_NAME."',
popup_return : '"._EDITOR_ADVLINK_POPUP_RETURN."',
popup_scrollbars : '"._EDITOR_ADVLINK_POPUP_SCROLLBARS."',
popup_statusbar : '"._EDITOR_ADVLINK_POPUP_STATUSBAR."',
popup_toolbar : '"._EDITOR_ADVLINK_POPUP_TOOLBAR."',
popup_menubar : '"._EDITOR_ADVLINK_POPUP_MENUBAR."',
popup_location : '"._EDITOR_ADVLINK_POPUP_LOCATION."',
popup_resizable : '"._EDITOR_ADVLINK_POPUP_RESIZABLE."',
popup_dependent : '"._EDITOR_ADVLINK_POPUP_DEPENDENT."',
popup_size : '"._EDITOR_ADVLINK_POPUP_SIZE."',
popup_position : '"._EDITOR_ADVLINK_POPUP_POSITION."',
id : '"._EDITOR_ADVLINK_ID."',
style: '"._EDITOR_ADVLINK_STYLE."',
classes : '"._EDITOR_ADVLINK_CLASSES."',
target_name : '"._EDITOR_ADVLINK_TARGET_NAME."',
langdir : '"._EDITOR_ADVLINK_LANGDIR."',
target_langcode : '"._EDITOR_ADVLINK_TARGET_LANGCODE."',
langcode : '"._EDITOR_ADVLINK_LANGCODE."',
encoding : '"._EDITOR_ADVLINK_ENCODING."',
mime : '"._EDITOR_ADVLINK_MIME."',
rel : '"._EDITOR_ADVLINK_REL."',
rev : '"._EDITOR_ADVLINK_REV."',
tabindex : '"._EDITOR_ADVLINK_TABINDEX."',
accesskey : '"._EDITOR_ADVLINK_ACCESSKEY."',
ltr : '"._EDITOR_ADVLINK_LTR."',
rtl : '"._EDITOR_ADVLINK_RTL."'
});";

    $fp = fopen("../plugins/advlink/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_advlink, mb_strlen($language_javascript_advlink));
    fclose($fp);


$language_javascript_emotions =" 
tinyMCE.addToLang('emotions',{
title : '"._EDITOR_EMOTIONS_TITLE."',
desc : '"._EDITOR_EMOTIONS_DESC."',
cool : '"._EDITOR_EMOTIONS_COOL."',
cry : '"._EDITOR_EMOTIONS_CRY."',
embarassed : '"._EDITOR_EMOTIONS_EMBARASSED."',
foot_in_mouth : '"._EDITOR_EMOTIONS_FOOT_IN_MOUTH."',
frown : '"._EDITOR_EMOTIONS_FROWN."',
innocent : '"._EDITOR_EMOTIONS_INNOCENT."',
kiss : '"._EDITOR_EMOTIONS_KISS."',
laughing : '"._EDITOR_EMOTIONS_LAUGHING."',
money_mouth : '"._EDITOR_EMOTIONS_MONEY_MOUTH."',
sealed : '"._EDITOR_EMOTIONS_SEALED."',
smile : '"._EDITOR_EMOTIONS_SMILE."',
surprised : '"._EDITOR_EMOTIONS_SURPRISED."',
tongue_out : '"._EDITOR_EMOTIONS_TONGUE_OUT."',
undecided : '"._EDITOR_EMOTIONS_UNDECIDED."',
wink : '"._EDITOR_EMOTIONS_WINK."',
yell : '"._EDITOR_EMOTIONS_YELL."'
});";

    $fp = fopen("../plugins/emotions/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_emotions, mb_strlen($language_javascript_emotions));
    fclose($fp);

$language_javascript_iespell =" 
tinyMCE.addToLang('',{
iespell_desc : '"._EDITOR_IESPELL_IESPELL_DESC."',
iespell_download : '"._EDITOR_IESPELL_IESPELL_DOWNLOAD."'
});";

    $fp = fopen("../plugins/iespell/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_iespell, mb_strlen($language_javascript_iespell));
    fclose($fp);


$language_javascript_insertdatetime =" 
tinyMCE.addToLang('',{
insertdate_def_fmt : '"._EDITOR_INSERTDATETIME_INSERTDATE_DEF_FMT."',
inserttime_def_fmt : '"._EDITOR_INSERTDATETIME_INSERTTIME_DEF_FMT."',
insertdate_desc : '"._EDITOR_INSERTDATETIME_INSERTDATE_DESC."',
inserttime_desc : '"._EDITOR_INSERTDATETIME_INSERTTIME_DESC."',
inserttime_months_long : "._EDITOR_INSERTDATETIME_INSERTTIME_MONTHS_LONG.",
inserttime_months_short : "._EDITOR_INSERTDATETIME_INSERTTIME_MONTHS_SHORT.",
inserttime_day_long : "._EDITOR_INSERTDATETIME_INSERTTIME_DAY_LONG.",
inserttime_day_short : "._EDITOR_INSERTDATETIME_INSERTTIME_DAY_SHORT."
});";

    $fp = fopen("../plugins/insertdatetime/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_insertdatetime, mb_strlen($language_javascript_insertdatetime));
    fclose($fp);


$language_javascript_mathtype =" 
tinyMCE.addToLang('mathtype',{
insert_mathtype_title : '"._EDITOR_MATHTYPE_INSERT_MATHTYPE_TITLE."',
title : '"._EDITOR_MATHTYPE_TITLE."',
mathtype_desc : '"._EDITOR_MATHTYPE_MATHTYPE_DESC."',
desc : '"._EDITOR_MATHTYPE_DESC."'
});";


    $fp = fopen("../plugins/mathtype/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_mathtype, mb_strlen($language_javascript_mathtype));
    fclose($fp);


$language_javascript_media =" 
tinyMCE.addToLang('media',{
title : '"._EDITOR_MEDIA_TITLE."',
desc : '"._EDITOR_MEDIA_DESC."',
general : '"._EDITOR_MEDIA_GENERAL."',
advanced : '"._EDITOR_MEDIA_ADVANCED."',
file : '"._EDITOR_MEDIA_FILE."',
list : '"._EDITOR_MEDIA_LIST."',
size : '"._EDITOR_MEDIA_SIZE."',
preview : '"._EDITOR_MEDIA_PREVIEW."',
constrain_proportions : '"._EDITOR_MEDIA_CONSTRAIN_PROPORTIONS."',
type : '"._EDITOR_MEDIA_TYPE."',
id : '"._EDITOR_MEDIA_ID."',
name : '"._EDITOR_MEDIA_NAME."',
class_name : '"._EDITOR_MEDIA_CLASS_NAME."',
vspace : '"._EDITOR_MEDIA_VSPACE."',
hspace : '"._EDITOR_MEDIA_HSPACE."',
play : '"._EDITOR_MEDIA_PLAY."',
loop : '"._EDITOR_MEDIA_LOOP."',
menu : '"._EDITOR_MEDIA_MENU."',
quality : '"._EDITOR_MEDIA_QUALITY."',
scale : '"._EDITOR_MEDIA_SCALE."',
align : '"._EDITOR_MEDIA_ALIGN."',
salign : '"._EDITOR_MEDIA_SALIGN."',
wmode : '"._EDITOR_MEDIA_WMODE."',
bgcolor : '"._EDITOR_MEDIA_BGCOLOR."',
base : '"._EDITOR_MEDIA_BASE."',
flashvars : '"._EDITOR_MEDIA_FLASHVARS."',
liveconnect : '"._EDITOR_MEDIA_LIVECONNECT."',
autohref : '"._EDITOR_MEDIA_AUTOHREF."',
cache : '"._EDITOR_MEDIA_CACHE."',
hidden : '"._EDITOR_MEDIA_HIDDEN."',
controller : '"._EDITOR_MEDIA_CONTROLLER."',
kioskmode : '"._EDITOR_MEDIA_KIOSKMODE."',
playeveryframe : '"._EDITOR_MEDIA_PLAYEVERYFRAME."',
targetcache : '"._EDITOR_MEDIA_TARGETCACHE."',
correction : '"._EDITOR_MEDIA_CORRECTION."',
enablejavascript : '"._EDITOR_MEDIA_ENABLEJAVASCRIPT."',
starttime : '"._EDITOR_MEDIA_STARTTIME."',
endtime : '"._EDITOR_MEDIA_ENDTIME."',
href : '"._EDITOR_MEDIA_HREF."',
qtsrcchokespeed : '"._EDITOR_MEDIA_QTSRCCHOKESPEED."',
target : '"._EDITOR_MEDIA_TARGET."',
volume : '"._EDITOR_MEDIA_VOLUME."',
autostart : '"._EDITOR_MEDIA_AUTOSTART."',
enabled : '"._EDITOR_MEDIA_ENABLED."',
fullscreen : '"._EDITOR_MEDIA_FULLSCREEN."',
invokeurls : '"._EDITOR_MEDIA_INVOKEURLS."',
mute : '"._EDITOR_MEDIA_MUTE."',
stretchtofit : '"._EDITOR_MEDIA_STRETCHTOFIT."',
windowlessvideo : '"._EDITOR_MEDIA_WINDOWLESSVIDEO."',
balance : '"._EDITOR_MEDIA_BALANCE."',
baseurl : '"._EDITOR_MEDIA_BASEURL."',
captioningid : '"._EDITOR_MEDIA_CAPTIONINGID."',
currentmarker : '"._EDITOR_MEDIA_CURRENTMARKER."',
currentposition : '"._EDITOR_MEDIA_CURRENTPOSITION."',
defaultframe : '"._EDITOR_MEDIA_DEFAULTFRAME."',
playcount : '"._EDITOR_MEDIA_PLAYCOUNT."',
rate : '"._EDITOR_MEDIA_RATE."',
uimode : '"._EDITOR_MEDIA_UIMODE."',
flash_options : '"._EDITOR_MEDIA_FLASH_OPTIONS."',
qt_options : '"._EDITOR_MEDIA_QT_OPTIONS."',
wmp_options : '"._EDITOR_MEDIA_WMP_OPTIONS."',
rmp_options : '"._EDITOR_MEDIA_RMP_OPTIONS."',
shockwave_options : '"._EDITOR_MEDIA_SHOCKWAVE_OPTIONS."',
autogotourl : '"._EDITOR_MEDIA_AUTOGOTOURL."',
center : '"._EDITOR_MEDIA_CENTER."',
imagestatus : '"._EDITOR_MEDIA_IMAGESTATUS."',
maintainaspect : '"._EDITOR_MEDIA_MAINTAINASPECT."',
nojava : '"._EDITOR_MEDIA_NOJAVA."',
prefetch : '"._EDITOR_MEDIA_PREFETCH."',
shuffle : '"._EDITOR_MEDIA_SHUFFLE."',
console : '"._EDITOR_MEDIA_CONSOLE."',
numloop : '"._EDITOR_MEDIA_NUMLOOP."',
controls : '"._EDITOR_MEDIA_CONTROLS."',
scriptcallbacks : '"._EDITOR_MEDIA_SCRIPTCALLBACKS."',
swstretchstyle : '"._EDITOR_MEDIA_SWSTRETCHSTYLE."',
swstretchhalign : '"._EDITOR_MEDIA_SWSTRETCHHALIGN."',
swstretchvalign : '"._EDITOR_MEDIA_SWSTRETCHVALIGN."',
sound : '"._EDITOR_MEDIA_SOUND."',
progress : '"._EDITOR_MEDIA_PROGRESS."',
qtsrc : '"._EDITOR_MEDIA_QTSRC."',
qt_stream_warn : '"._EDITOR_MEDIA_QT_STREAM_WARN."'
});";


    $fp = fopen("../plugins/media/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_media, mb_strlen($language_javascript_media));
    fclose($fp);

$language_javascript_preview =" 
tinyMCE.addToLang('',{
preview_desc : '"._EDITOR_PREVIEW_PREVIEW_DESC."'
});";

    $fp = fopen("../plugins/preview/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_preview, mb_strlen($language_javascript_preview));
    fclose($fp);

$language_javascript_print =" 
tinyMCE.addToLang('',{
print_desc : '"._EDITOR_PRINT_PRINT_DESC."'
});";

    $fp = fopen("../plugins/print/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_print, mb_strlen($language_javascript_print));
    fclose($fp);

$language_javascript_save =" 
tinyMCE.addToLang('',{
save_desc : '"._EDITOR_SAVE_SAVE_DESC."'
});";

    $fp = fopen("../plugins/save/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_save, mb_strlen($language_javascript_save));
    fclose($fp);


$language_javascript_searchreplace =" 
tinyMCE.addToLang('',{
searchreplace_search_desc : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_SEARCH_DESC."',
searchreplace_searchnext_desc : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_SEARCHNEXT_DESC."',
searchreplace_replace_desc : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_REPLACE_DESC."',
searchreplace_notfound : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_NOTFOUND."',
searchreplace_search_title : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_SEARCH_TITLE."',
searchreplace_replace_title : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_REPLACE_TITLE."',
searchreplace_allreplaced : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_ALLREPLACED."',
searchreplace_findwhat : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_FINDWHAT."',
searchreplace_replacewith : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_REPLACEWITH."',
searchreplace_direction : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_DIRECTION."',
searchreplace_up : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_UP."',
searchreplace_down : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_DOWN."',
searchreplace_case : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_CASE."',
searchreplace_findnext : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_FINDNEXT."',
searchreplace_replace : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_REPLACE."',
searchreplace_replaceall : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_REPLACEALL."',
searchreplace_cancel : '"._EDITOR_SEARCHREPLACE_SEARCHREPLACE_CANCEL."'
});";

    $fp = fopen("../plugins/searchreplace/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_searchreplace, mb_strlen($language_javascript_searchreplace));
    fclose($fp);


$language_javascript_table =" 
tinyMCE.addToLang('table',{
general_tab : '"._EDITOR_TABLE_GENERAL_TAB."',
advanced_tab : '"._EDITOR_TABLE_ADVANCED_TAB."',
general_props : '"._EDITOR_TABLE_GENERAL_PROPS."',
advanced_props : '"._EDITOR_TABLE_ADVANCED_PROPS."',
desc : '"._EDITOR_TABLE_DESC."',
row_before_desc : '"._EDITOR_TABLE_ROW_BEFORE_DESC."',
row_after_desc : '"._EDITOR_TABLE_ROW_AFTER_DESC."',
delete_row_desc : '"._EDITOR_TABLE_DELETE_ROW_DESC."',
col_before_desc : '"._EDITOR_TABLE_COL_BEFORE_DESC."',
col_after_desc : '"._EDITOR_TABLE_COL_AFTER_DESC."',
delete_col_desc : '"._EDITOR_TABLE_DELETE_COL_DESC."',
rowtype : '"._EDITOR_TABLE_ROWTYPE."',
title : '"._EDITOR_TABLE_TITLE."',
width : '"._EDITOR_TABLE_WIDTH."',
height : '"._EDITOR_TABLE_HEIGHT."',
cols : '"._EDITOR_TABLE_COLS."',
rows : '"._EDITOR_TABLE_ROWS."',
cellspacing : '"._EDITOR_TABLE_CELLSPACING."',
cellpadding : '"._EDITOR_TABLE_CELLPADDING."',
border : '"._EDITOR_TABLE_BORDER."',
align : '"._EDITOR_TABLE_ALIGN."',
align_default : '"._EDITOR_TABLE_ALIGN_DEFAULT."',
align_left : '"._EDITOR_TABLE_ALIGN_LEFT."',
align_right : '"._EDITOR_TABLE_ALIGN_RIGHT."',
align_middle : '"._EDITOR_TABLE_ALIGN_MIDDLE."',
row_title : '"._EDITOR_TABLE_ROW_TITLE."',
cell_title : '"._EDITOR_TABLE_CELL_TITLE."',
cell_type : '"._EDITOR_TABLE_CELL_TYPE."',
row_desc : '"._EDITOR_TABLE_ROW_DESC."',
cell_desc : '"._EDITOR_TABLE_CELL_DESC."',
valign : '"._EDITOR_TABLE_VALIGN."',
align_top : '"._EDITOR_TABLE_ALIGN_TOP."',
align_bottom : '"._EDITOR_TABLE_ALIGN_BOTTOM."',
props_desc : '"._EDITOR_TABLE_PROPS_DESC."',
bordercolor : '"._EDITOR_TABLE_BORDERCOLOR."',
bgcolor : '"._EDITOR_TABLE_BGCOLOR."',
merge_cells_title : '"._EDITOR_TABLE_MERGE_CELLS_TITLE."',
split_cells_desc : '"._EDITOR_TABLE_SPLIT_CELLS_DESC."',
merge_cells_desc : '"._EDITOR_TABLE_MERGE_CELLS_DESC."',
cut_row_desc : '"._EDITOR_TABLE_CUT_ROW_DESC."',
copy_row_desc : '"._EDITOR_TABLE_COPY_ROW_DESC."',
paste_row_before_desc : '"._EDITOR_TABLE_PASTE_ROW_BEFORE_DESC."',
paste_row_after_desc : '"._EDITOR_TABLE_PASTE_ROW_AFTER_DESC."',
id : '"._EDITOR_TABLE_ID."',
style: '"._EDITOR_TABLE_STYLE."',
langdir : '"._EDITOR_TABLE_LANGDIR."',
langcode : '"._EDITOR_TABLE_LANGCODE."',
mime : '"._EDITOR_TABLE_MIME."',
ltr : '"._EDITOR_TABLE_LTR."',
rtl : '"._EDITOR_TABLE_RTL."',
bgimage : '"._EDITOR_TABLE_BGIMAGE."',
summary : '"._EDITOR_TABLE_SUMMARY."',
td : '"._EDITOR_TABLE_TD."',
th : '"._EDITOR_TABLE_TH."',
cell_cell : '"._EDITOR_TABLE_CELL_CELL."',
cell_row : '"._EDITOR_TABLE_CELL_ROW."',
cell_all : '"._EDITOR_TABLE_CELL_ALL."',
row_row : '"._EDITOR_TABLE_ROW_ROW."',
row_odd : '"._EDITOR_TABLE_ROW_ODD."',
row_even : '"._EDITOR_TABLE_ROW_EVEN."',
row_all : '"._EDITOR_TABLE_ROW_ALL."',
thead : '"._EDITOR_TABLE_THEAD."',
tbody : '"._EDITOR_TABLE_TBODY."',
tfoot : '"._EDITOR_TABLE_TFOOT."',
del : '"._EDITOR_TABLE_DEL."',
scope : '"._EDITOR_TABLE_SCOPE."',
row : '"._EDITOR_TABLE_ROW."',
col : '"._EDITOR_TABLE_COL."',
rowgroup : '"._EDITOR_TABLE_ROWGROUP."',
colgroup : '"._EDITOR_TABLE_COLGROUP."',
col_limit : '"._EDITOR_TABLE_COL_LIMIT."',
row_limit : '"._EDITOR_TABLE_ROW_LIMIT."',
cell_limit : '"._EDITOR_TABLE_CELL_LIMIT."',
missing_scope: '"._EDITOR_TABLE_MISSING_SCOPE."'
});";


    $fp = fopen("../plugins/table/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_table, mb_strlen($language_javascript_table));
    fclose($fp);


$language_javascript_autosave =" 
tinyMCE.addToLang('',{
autosave_unload_msg : '"._EDITOR_AUTOSAVE_AUTOSAVE_UNLOAD_MSG."'
});";

    $fp = fopen("../plugins/autosave/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_autosave, mb_strlen($language_javascript_autosave));
    fclose($fp);


$language_javascript_devkit =" 
tinyMCE.addToLang('devkit',{
title : '"._EDITOR_DEVKIT_TITLE."',
info_tab : '"._EDITOR_DEVKIT_INFO_TAB."',
settings_tab : '"._EDITOR_DEVKIT_SETTINGS_TAB."',
log_tab : '"._EDITOR_DEVKIT_LOG_TAB."',
content_tab : '"._EDITOR_DEVKIT_CONTENT_TAB."',
command_states_tab : '"._EDITOR_DEVKIT_COMMAND_STATES_TAB."',
undo_redo_tab : '"._EDITOR_DEVKIT_UNDO_REDO_TAB."',
misc_tab : '"._EDITOR_DEVKIT_MISC_TAB."',
filter : '"._EDITOR_DEVKIT_FILTER."',
clear_log : '"._EDITOR_DEVKIT_CLEAR_LOG."',
refresh : '"._EDITOR_DEVKIT_REFRESH."',
info_help : '"._EDITOR_DEVKIT_INFO_HELP."',
settings_help : '"._EDITOR_DEVKIT_SETTINGS_HELP."',
content_help : '"._EDITOR_DEVKIT_CONTENT_HELP."',
command_states_help : '"._EDITOR_DEVKIT_COMMAND_STATES_HELP."',
undo_redo_help : '"._EDITOR_DEVKIT_UNDO_REDO_HELP."',
misc_help : '"._EDITOR_DEVKIT_MISC_HELP."',
debug_events : '"._EDITOR_DEVKIT_DEBUG_EVENTS."',
undo_diff : '"._EDITOR_DEVKIT_UNDO_DIFF."'
});";

    $fp = fopen("../plugins/devkit/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_devkit, mb_strlen($language_javascript_devkit));
    fclose($fp);


$language_javascript_directionality =" 
tinyMCE.addToLang('',{
directionality_ltr_desc : '"._EDITOR_DIRECTIONALITY_DIRECTIONALITY_LTR_DESC."',
directionality_rtl_desc : '"._EDITOR_DIRECTIONALITY_DIRECTIONALITY_RTL_DESC."'
});";

    $fp = fopen("../plugins/directionality/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_directionality, mb_strlen($language_javascript_directionality));
    fclose($fp);

$language_javascript_layer =" 
tinyMCE.addToLang('layer',{
insertlayer_desc : '"._EDITOR_LAYER_INSERTLAYER_DESC."',
forward_desc : '"._EDITOR_LAYER_FORWARD_DESC."',
backward_desc : '"._EDITOR_LAYER_BACKWARD_DESC."',
absolute_desc : '"._EDITOR_LAYER_ABSOLUTE_DESC."',
content : '"._EDITOR_LAYER_CONTENT."'
});";

    $fp = fopen("../plugins/layer/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_layer, mb_strlen($language_javascript_layer));
    fclose($fp);
    
    
$language_javascript_nonbreaking =" 
tinyMCE.addToLang('nonbreaking',{
desc : '"._EDITOR_NONBREAKING_DESC."'
});";

    $fp = fopen("../plugins/nonbreaking/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_nonbreaking, mb_strlen($language_javascript_nonbreaking));
    fclose($fp);

$language_javascript_paste =" 
tinyMCE.addToLang('',{
paste_text_desc : '"._EDITOR_PASTE_PASTE_TEXT_DESC."',
paste_text_title : '"._EDITOR_PASTE_PASTE_TEXT_TITLE."',
paste_text_linebreaks : '"._EDITOR_PASTE_PASTE_TEXT_LINEBREAKS."',
paste_word_desc : '"._EDITOR_PASTE_PASTE_WORD_DESC."',
paste_word_title : '"._EDITOR_PASTE_PASTE_WORD_TITLE."',
selectall_desc : '"._EDITOR_PASTE_SELECTALL_DESC."'
});";

    $fp = fopen("../plugins/paste/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_paste, mb_strlen($language_javascript_paste));
    fclose($fp);


$language_javascript_theme ="
tinyMCE.addToLang('',{
theme_style_select : '"._EDITOR_THEME_THEME_STYLE_SELECT."',
theme_code_desc : '"._EDITOR_THEME_THEME_CODE_DESC."',
theme_code_title : '"._EDITOR_THEME_THEME_CODE_TITLE."',
theme_code_wordwrap : '"._EDITOR_THEME_THEME_CODE_WORDWRAP."',
theme_sub_desc : '"._EDITOR_THEME_THEME_SUB_DESC."',
theme_sup_desc : '"._EDITOR_THEME_THEME_SUP_DESC."',
theme_hr_desc : '"._EDITOR_THEME_THEME_HR_DESC."',
theme_removeformat_desc : '"._EDITOR_THEME_THEME_REMOVEFORMAT_DESC."',
theme_custom1_desc : '"._EDITOR_THEME_THEME_CUSTOM1_DESC."',
insert_image_border : '"._EDITOR_THEME_INSERT_IMAGE_BORDER."',
insert_image_dimensions : '"._EDITOR_THEME_INSERT_IMAGE_DIMENSIONS."',
insert_image_vspace : '"._EDITOR_THEME_INSERT_IMAGE_VSPACE."',
insert_image_hspace : '"._EDITOR_THEME_INSERT_IMAGE_HSPACE."',
insert_image_align : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN."',
insert_image_align_default : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_DEFAULT."',
insert_image_align_baseline : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_BASELINE."',
insert_image_align_top : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_TOP."',
insert_image_align_middle : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_MIDDLE."',
insert_image_align_bottom : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_BOTTOM."',
insert_image_align_texttop : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_TEXTTOP."',
insert_image_align_absmiddle : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_ABSMIDDLE."',
insert_image_align_absbottom : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_ABSBOTTOM."',
insert_image_align_left : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_LEFT."',
insert_image_align_right : '"._EDITOR_THEME_INSERT_IMAGE_ALIGN_RIGHT."',
theme_font_size : '"._EDITOR_THEME_THEME_FONT_SIZE."',
theme_fontdefault : '"._EDITOR_THEME_THEME_FONTDEFAULT."',
theme_block : '"._EDITOR_THEME_THEME_BLOCK."',
theme_paragraph : '"._EDITOR_THEME_THEME_PARAGRAPH."',
theme_div : '"._EDITOR_THEME_THEME_DIV."',
theme_address : '"._EDITOR_THEME_THEME_ADDRESS."',
theme_pre : '"._EDITOR_THEME_THEME_PRE."',
theme_h1 : '"._EDITOR_THEME_THEME_H1."',
theme_h2 : '"._EDITOR_THEME_THEME_H2."',
theme_h3 : '"._EDITOR_THEME_THEME_H3."',
theme_h4 : '"._EDITOR_THEME_THEME_H4."',
theme_h5 : '"._EDITOR_THEME_THEME_H5."',
theme_h6 : '"._EDITOR_THEME_THEME_H6."',
theme_blockquote : '"._EDITOR_THEME_THEME_BLOCKQUOTE."',
theme_code : '"._EDITOR_THEME_THEME_CODE."',
theme_samp : '"._EDITOR_THEME_THEME_SAMP."',
theme_dt : '"._EDITOR_THEME_THEME_DT."',
theme_dd : '"._EDITOR_THEME_THEME_DD."',
theme_colorpicker_title : '"._EDITOR_THEME_THEME_COLORPICKER_TITLE."',
theme_colorpicker_apply : '"._EDITOR_THEME_THEME_COLORPICKER_APPLY."',
theme_forecolor_desc : '"._EDITOR_THEME_THEME_FORECOLOR_DESC."',
theme_backcolor_desc : '"._EDITOR_THEME_THEME_BACKCOLOR_DESC."',
theme_charmap_title : '"._EDITOR_THEME_THEME_CHARMAP_TITLE."',
theme_charmap_desc : '"._EDITOR_THEME_THEME_CHARMAP_DESC."',
theme_visualaid_desc : '"._EDITOR_THEME_THEME_VISUALAID_DESC."',
insert_anchor_title : '"._EDITOR_THEME_INSERT_ANCHOR_TITLE."',
insert_anchor_name : '"._EDITOR_THEME_INSERT_ANCHOR_NAME."',
theme_anchor_desc : '"._EDITOR_THEME_THEME_ANCHOR_DESC."',
theme_insert_link_titlefield : '"._EDITOR_THEME_THEME_INSERT_LINK_TITLEFIELD."',
theme_clipboard_msg : '"._EDITOR_THEME_THEME_CLIPBOARD_MSG."',
theme_path : '"._EDITOR_THEME_THEME_PATH."',
cut_desc : '"._EDITOR_THEME_CUT_DESC."',
copy_desc : '"._EDITOR_THEME_COPY_DESC."',
paste_desc : '"._EDITOR_THEME_PASTE_DESC."',
link_list : '"._EDITOR_THEME_LINK_LIST."',
image_list : '"._EDITOR_THEME_IMAGE_LIST."',
browse : '"._EDITOR_THEME_BROWSE."',
image_props_desc : '"._EDITOR_THEME_IMAGE_PROPS_DESC."',
newdocument_desc : '"._EDITOR_THEME_NEWDOCUMENT_DESC."',
class_name : '"._EDITOR_THEME_CLASS_NAME."',
newdocument : '"._EDITOR_THEME_NEWDOCUMENT."',
about_title : '"._EDITOR_THEME_ABOUT_TITLE."',
about : '"._EDITOR_THEME_ABOUT."',
license : '"._EDITOR_THEME_LICENSE."',
plugins : '"._EDITOR_THEME_PLUGINS."',
plugin : '"._EDITOR_THEME_PLUGIN."',
author : '"._EDITOR_THEME_AUTHOR."',
version : '"._EDITOR_THEME_VERSION."',
loaded_plugins : '"._EDITOR_THEME_LOADED_PLUGINS."',
help : '"._EDITOR_THEME_HELP."',
not_set : '"._EDITOR_THEME_NOT_SET."',
close : '"._EDITOR_THEME_CLOSE."',
toolbar_focus : '"._EDITOR_THEME_TOOLBAR_FOCUS."',
invalid_data : '"._EDITOR_THEME_INVALID_DATA."'
});";


    $fp = fopen("../themes/advanced/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_theme, mb_strlen($language_javascript_theme));
    fclose($fp);

$language_javascript_indexlink =" 
tinyMCE.addToLang('',{
lang_index_link_linkdescription : '"._EDITOR_INDEXLINK_LANG_INDEX_LINK_LINKDESCRIPTION."',
lang_index_link_desc : '"._EDITOR_INDEXLINK_LANG_INDEX_LINK_DESC."'
});";

    $fp = fopen("../plugins/index_link/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_indexlink, mb_strlen($language_javascript_indexlink));
    fclose($fp);

$language_javascript_lessonsinfo =" 
tinyMCE.addToLang('',{
lang_lessons_info_linkdescription : '"._EDITOR_LESSONSINFO_LANG_LESSONS_INFO_DESC."',
lang_lessons_info_desc : '"._EDITOR_INDEXLINK_LANG_INDEX_LINK_LINKDESCRIPTION."'
});";

    $fp = fopen("../plugins/lessons_info/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_lessonsinfo, mb_strlen($language_javascript_lessonsinfo));
    fclose($fp);

$language_javascript_fullscreen =" 
tinyMCE.addToLang('',{
fullscreen_desc : '"._EDITOR_FULLSCREEN_FULLSCREEN_DESC."'
});";

    $fp = fopen("../plugins/fullscreen/langs/".$lang_file.".js", "w");
    fwrite($fp, $language_javascript_fullscreen, mb_strlen($language_javascript_fullscreen));
    fclose($fp);
?>