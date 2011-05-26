
{*Closing template functions*}
<script>

{if $T_UNIT}var currentUnit = document.getElementById('node{$T_UNIT.id}');{else}var currentUnit = '';{/if}
  var g_servername = '{$smarty.const.G_SERVERNAME}';
</script>
<script>var BOOKMARKTRANSLATION = '{$smarty.const._BOOKMARKS}';var NODATAFOUND = '{$smarty.const._NODATAFOUND}';</script>

<script type = "text/javascript" src = "js/scripts.php?build={$smarty.const.G_BUILD}&load={$T_HEADER_MAIN_SCRIPTS}"> </script> {*Main scripts, such as prototype*}

{if $T_HEADER_EDITOR}
 {if $T_CONFIGURATION.editor_type == 'tinymce_new'}
  {include file = "includes/editor_new.tpl"}
 {elseif $T_CONFIGURATION.editor_type == 'tinymce'}
  {include file = "includes/editor.tpl"}
 {/if}
{/if}


{if $T_HEADER_LOAD_SCRIPTS}<script type = "text/javascript" src = "js/scripts.php?build={$smarty.const.G_BUILD}&load={$T_HEADER_LOAD_SCRIPTS}"> </script>{/if}

{foreach name = 'module_scripts_list' item = item key = key from = $T_MODULE_JS}
<script type = "text/javascript" src = "{$item}"> </script> {*///MODULES LINK JAVASCRIPT CODE*}
{/foreach}

{*
<script>
$$('div.block').ancestors().each(function (s) {
 if (s.readAttribute('collapsed')) {
  $(t.id+"_content").hide();
  $(t.id+"t_image").removeClassName("open");
  $(t.id+"t_image").addClassName("close");
  $(t.id+"t_image").src = "themes/default/images/16x16/navigate_down.png";
 }
});
</script>
*}
<div id = "user_table" style = "display:none">
{capture name = "t_users_table_code"}
    <table width = "100%">
        <tr><td align = "left" id = "user_box" style = "padding:3px 3px 4px 5px;"></td></tr>
    </table>
{/capture}
{eF_template_printBlock title = $smarty.const._INFO data = $smarty.capture.t_users_table_code image = '32x32/user.png'}
</div>
{*This table is used to display popups*}

<table id = "popup_table" class = "divPopup" style = "display:none;">
    <tr class = "defaultRowHeight">
        <td class = "topTitle" id = "popup_title"></td>
        <td class = "topTitle" id = "popup_close_cell"><img src = "images/16x16/close.png" alt = "{$smarty.const._CLOSE}" name = "" id = "popup_close" title = "{$smarty.const._CLOSE}" onclick = "if (document.getElementById('reloadHidden') && document.getElementById('reloadHidden').value == '1')  {ldelim}parent.frames[1].location = parent.frames[1].location{rdelim};eF_js_showDivPopup('', '', this.name);"/>
    </td></tr>
    <tr><td colspan = "2" id = "popup_data" style = ""></td></tr>
    <tr><td colspan = "2" id = "frame_data" style = "display:none;">
   <iframe name = "POPUP_FRAME" id = "popup_frame" src = "about:blank" >Sorry, but your browser needs to support iframes to see this</iframe>
    </td></tr>
</table>
<div id = "error_details" style = "display:none">{eF_template_printBlock title=$smarty.const._ERRORDETAILS data="<pre>`$T_EXCEPTION_TRACE`</pre>" image='32x32/error_delete.png'}</div>
<div id = 'showMessageDiv' style = "display:none"></div>
<div id="dimmer" class = "dimmerDiv" style = "display:none;"></div>
<div id = "defaultExceptionHandlerDiv" style = "color:#ffffff;display:none"></div>

{foreach name = "module_closing_list" item = "module_close_code" key = "key" from=$T_PAGE_FINISH_MODULES}
 {include file = $module_close_code}
{/foreach}


<script>

{if $T_ADD_ANOTHER}
 document.getElementById('add_new_event_link').onclick();
 document.getElementById('popup_frame').src ="{$smarty.session.s_type}.php?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&add_calendar=1{$T_CALENDAR_TYPE_LINK}&message={$smarty.get.pmessage}&message_type={$smarty.get.pmessage_type}";
{/if}
{if isset($div_error)}
 eF_js_showDivPopup('{$div_error}');
{/if}



{* Let outputfilter.eF_template_includeScripts.php know to send notifications, after sending for ajax tables *}
{if ($T_TRIGGER_NEXT_NOTIFICATIONS_SEND == 1)}
    var __shouldTriggerNextNotifications = true;
{else}
    var __shouldTriggerNextNotifications = false;
{/if}

{if $T_THEME_SETTINGS->options.sidebar_interface == 1}

 // Code used for appearance fixing of the horizontal menus
 var leftDist;
 {foreach name = 'outer_menu' key = 'menu_key' item = 'menu' from = $T_MENU}
     if (document.getElementById("listmenu{$menu_key}")) {ldelim}
   leftDist = document.getElementById("listmenu{$menu_key}").getStyle("width");
   resArray = leftDist.split("px");
   leftDist = (parseInt(resArray[0])+1) + "px";
   document.getElementById("listmenu{$menu_key}").setStyle({ldelim}left: "-"+leftDist{rdelim}); //= "-" + leftDist; // + "px";
   document.getElementById("listmenu{$menu_key}").style.display = "none";
  {rdelim}
 {/foreach}
 if (document.getElementById("horizontal_menu"))
  document.getElementById("horizontal_menu").style.display = "none";
{/if}

{if $T_FACEBOOK_ACCOUNT_MERGE_POPUP}
 {if $T_FACEBOOK_EXTERNAL_LOGIN}
 eF_js_showDivPopup('{$smarty.const._FACEBOOKMERGEACCOUNT}', 2, 'facebook_login');
 {else}
 eF_js_showDivPopup('{$smarty.const._FACEBOOKMERGEACCOUNT}', 0, 'facebook_login');
 {/if}
{/if}



{if $T_FACEBOOK_API_KEY != ""}
//If facebook enabled prompt for permissions
 FB.init("{$T_FACEBOOK_API_KEY}", "facebook/xd_receiver.htm");
 {if isset($T_FACEBOOK_SHOULD_UPDATE_STATUS) && $T_FACEBOOK_SHOULD_UPDATE_STATUS != 1}
  {literal}
  function onUpdateDone() {
   top.location='{/literal}{$smarty.session.s_type}{literal}page.php?fb_authenticated=1';
  }
  FB.ensureInit(function() { FB.Connect.showPermissionDialog("status_update", onUpdateDone); });
  {/literal}
 {/if}
 {if isset($T_FACEBOOK_LOGOUT)}
  {literal}
  FB.ensureInit(function() { FB.Connect.logout(); });
  {/literal}

 {/if}

{/if}

{literal}
if (!usingHorizontalInterface) {
 if (top.sideframe && top.sideframe.document.getElementById('current_location')) {
  top.sideframe.document.getElementById('current_location').value = top.mainframe.location.toString();
 }
} else {
 // $('current_location') caused js error in browse.php
 if (document.getElementById('current_location')) {
  document.getElementById('current_location').value = document.location.toString();
 }
}
{/literal}
{*{if !$smarty.get.popup && !$T_POPUP_MODE}closePopup();{/if}*}

{*{if $smarty.get.popup || $T_POPUP_MODE}parent.displayPopup(document.body);{/if}*}

 translations['_COUPON'] = '{$smarty.const._COUPON}';
 translations['_CLICKTOENTERDISCOUNTCOUPON'] = '{$smarty.const._CLICKTOENTERDISCOUNTCOUPON}';
 {if $smarty.session.s_login}
  {if $smarty.server.PHP_SELF|basename|replace:'.php':'' == 'index'}
   redirectLocation ='index.php?ctg=checkout&checkout=1&register_lessons=1';
  {else}
   redirectLocation ='{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1&checkout=1';
  {/if}
 {else}
  redirectLocation ='index.php?ctg=login&register_lessons=1';
 {/if}

 {* Moved here because of EF-567*}
 {* If this creates problems in the future "Permission denied to get property Window.document" check "security.fileuri.strict_origin_policy: true" *}
 if (parent.frames[0].document.getElementById('dimmer'))
  parent.frames[0].document.getElementById('dimmer').style.display = 'none';

 {* Make the loading div disappear (again most of the times) once for lesson changing *}
 if (top.sideframe && top.sideframe.document && top.sideframe.document.getElementById('loading_sidebar'))
     top.sideframe.document.getElementById('loading_sidebar').style.display = 'none'; //no prototype here please

</script>
