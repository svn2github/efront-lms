{if !$smarty.get.popup && !$T_POPUP_MODE}
 {if $smarty.session.s_login}
 <script>
     // Translations used in the updater script
     var translations = new Array();
     translations['lessons'] = '{$smarty.const._LESSONS}';
     translations['servername'] = '{$smarty.const.G_SERVERNAME}';
     translations['onlineusers'] = '{$smarty.const._ONLINEUSERS}';
     translations['nousersinroom'] = '{$smarty.const._THEREARENOOTHERUSERSRIGHTNOWINTHISROOM}';
     translations['redirectedtomain']= '{$smarty.const._REDIRECTEDTOEFRONTMAIN}';
     translations['s_type'] = '{$smarty.session.s_type}';
     translations['s_login'] = '{$smarty.session.s_login}';
     translations['clicktochange'] = '{$smarty.const._CLICKTOCHANGESTATUS}';
     translations['userisonline'] = '{$smarty.const._USERISONLINE}';
     translations['and'] = '{$smarty.const._AND}';
     translations['hours'] = '{$smarty.const._HOURS}';
     translations['minutes'] = '{$smarty.const._MINUTES}';
     translations['userjustloggedin']= '{$smarty.const._USERJUSTLOGGEDIN}';
     translations['user'] = '{$smarty.const._USER}';
     translations['sendmessage'] = '{$smarty.const._SENDMESSAGE}';
     translations['web'] = '{$smarty.const._WEB}';
  translations['user_stats'] = '{$smarty.const._USERSTATISTICS}';
  translations['user_settings'] = '{$smarty.const._USERPROFILE}';
  translations['logout_user'] = '{$smarty.const._LOGOUTUSER}';
  translations['_ADMINISTRATOR'] = '{$smarty.const._ADMINISTRATOR}';
  translations['_PROFESSOR'] = '{$smarty.const._PROFESSOR}';
  translations['_STUDENT'] = '{$smarty.const._STUDENT}';

  var startUpdater = true;
  var updaterPeriod = '{$T_CONFIGURATION.updater_period}';
 </script>
 {/if}
 <table class = "pageLayout {if isset($T_MAXIMIZE_VIEWPORT)}centerFull hideBoth{else}{$layoutClass}{/if}" id = "pageLayout">
  <tr><td style = "vertical-align:top">
   <table style = "width:100%;">
   {if $smarty.server.PHP_SELF|basename == 'index.php'}
    {if $T_THEME_SETTINGS->options.show_header != 0}
     <tr><td class = "header" colspan = "3">{include file = "includes/header_code.tpl"}</td></tr>
    {/if}
   {elseif $T_THEME_SETTINGS->options.show_header == 2}
    <tr><td id ="horizontalBarRow" class = "{if isset($T_HEADER_CLASS)}{$T_HEADER_CLASS}{else}header{/if}" colspan = "3">{include file = "includes/header_code.tpl"}</td></tr>
   {else}
    <tr><td class = "topTitle defaultRowHeight" colspan = "3">
     <div style = "float:right;">{$smarty.capture.t_path_additional_code}</div>
     {$title|eF_formatTitlePath}
    </td></tr>
   {/if}
   <tr><td class = "layoutColumn left">
     {if !$layoutClass || strpos($layoutClass, 'hideRight') !== false}{$smarty.capture.left_code}{/if}
    </td>
    <td class = "layoutColumn center">
     {$smarty.capture.center_code}
    </td>
    <td class = "layoutColumn right">
     {if !$layoutClass || strpos($layoutClass, 'hideLeft') !== false}{$smarty.capture.right_code}{/if}
    </td></tr>
   </table>
  </td></tr>
 {if $T_THEME_SETTINGS->options.show_footer > 0 && !$smarty.get.popup && !$T_POPUP_MODE}
  <tr><td style = "vertical-align:bottom">
   <table style = "width:100%">
    <tr><td class = "footer {if $smarty.server.PHP_SELF|basename == 'index.php'}indexFooter{/if}" colspan = "3">{include file = "includes/footer_code.tpl"}</td></tr>
   </table>
  </td></tr>
 {/if}
 </table>
{else}
 {$smarty.capture.center_code}
{/if}
