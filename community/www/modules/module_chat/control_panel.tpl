{* chat module control panel template *}

<link href="{$T_CHAT_MODULE_BASELINK}css/control_panel.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$T_CHAT_MODULE_BASELINK}js/admin.js"></script>

<script type="text/javascript">
 var modulechatbaselink = '{$T_CHAT_MODULE_BASELINK}';
 var modulechatbasedir = '{$T_CHAT_MODULE_BASEDIR}';
 var modulechatbaseurl = '{$T_CHAT_MODULE_BASEURL}';
</script>
{capture name = "t_chat_code"}
{strip}
<table style="width:100%" >
<tr>
 <td class="topTitle" width="15%">{$smarty.const._CHAT_ACTION}</td>
 <td class="topTitle" width="27%">{$smarty.const._CHAT_DESCRIPTION}</td>
 <td class="topTitle">{$smarty.const._CHAT_NOTES}</td>
</tr>
<tr class = "oddRowColor">
 <td >
  <a class="action" title = "Lessons Catalogue" href = "{$T_CHAT_MODULE_BASELINK}admin.php?force=createLogs" onclick = "eF_js_showDivPopup('Lessons Catalogue', 0)" target = "POPUP_FRAME">{$smarty.const._CHAT_CREATE_LOG}</a>
 </td>
 <td>{$smarty.const._CHAT_CREATE_LOG_DESCR}</td>
 <td></td>
</tr>
<tr class = "evenRowColor">
 <td>
  <a class="action" href = "#" onclick="javascript:clearU2ULogs(); return false;">{$smarty.const._CHAT_CLEAR_HISTORY}</a>
 </td>
 <td>{$smarty.const._CHAT_CLEAR_HISTORY_DESCR}</td>
 <td><span class="caution">{$smarty.const._CHAT_CAUTION_IRREVERSIBLE}</span></td>
</tr>
<tr class = "oddRowColor">
 <td>
  <a class="action" href = "#" onclick="javascript:setChatheartBeat(); return false;">{$smarty.const._CHAT_CHANGE_MSG_FREQUENCY}</a>
 </td>
 <td>{$smarty.const._CHAT_CHANGE_MSG_FREQUENCY_DESCR}</td>
 <td><span class="caution">{$smarty.const._CHAT_CAUTION_NOTCHANGE}</span></td>
</tr>
<tr id="heartbeatrate">
 <td colspan="3">
  {capture name="t_chat_heartbeat"}
   <div id="heartbeatrateDiv"></div>
  {/capture}
  {eF_template_printBlock title=$smarty.const._CHAT_HEARTBEAT data=$smarty.capture.t_chat_heartbeat image= $T_RSS_MODULE_BASELINK|cat:'img/chat.png' absoluteImagePath = 1 link = $T_CHAT_MODULE_BASEURL}
 </td>
</tr>
<tr class = "evenRowColor">
 <td>
  <a class="action" href = "#" onclick="javascript:setRefresh_rate(); return false;">{$smarty.const._CHAT_USERLIST_REFRESHRATE}</a>
 </td>
 <td>{$smarty.const._CHAT_USERLIST_REFRESHRATE_DESCR}</td>
 <td><span class="caution">{$smarty.const._CHAT_CAUTION_NOTCHANGE}</span></td>
</tr>
</table>

{/strip}
{/capture}

 {eF_template_printBlock title=$smarty.const._CHAT_CHAT data=$smarty.capture.t_chat_code image= $T_RSS_MODULE_BASELINK|cat:'img/chat.png' absoluteImagePath = 1 link = $T_CHAT_MODULE_BASEURL}
