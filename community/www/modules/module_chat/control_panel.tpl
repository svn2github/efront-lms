{* chat module control panel template *}

<link href="{$T_CHAT_MODULE_BASELINK}css/control_panel.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$T_CHAT_MODULE_BASELINK}js/admin.js"></script>

<script type="text/javascript">
 var modulechatbaselink = '{$T_CHAT_MODULE_BASELINK}';
 var modulechatbasedir = '{$T_CHAT_MODULE_BASEDIR}';
 var modulechatbaseurl = '{$T_CHAT_MODULE_BASEURL}';
</script>



{capture name = 't_set_chatheartbeat'}
{$T_CHAT_CHANGE_CHATHEARTBEAT_FORM.javascript}
<form {$T_CHAT_CHANGE_CHATHEARTBEAT_FORM.attributes}>
  <!--<span id="currValue">{$smarty.const._CHAT_CURRENT_CHAT_ENGINE_RATE} {$T_CHAT_CURRENT_RATE} {$smarty.const._CHAT_SECONDS}</span>-->
        <table class="formElements">
  <tr>
   <td class="labelCell">{$smarty.const._CHAT_RATE}:&nbsp;</td>
   <td class="elementCell">{$T_CHAT_CHANGE_CHATHEARTBEAT_FORM.rate.html}</td>
  </tr>
  <tr>
   <td></td>
   <td class="submitCell">{$T_CHAT_CHANGE_CHATHEARTBEAT_FORM.submit.html}<span class="caution">{$smarty.const._CHAT_CAUTION_NOTCHANGE}</span></td>
  </tr>
 </table>
</form>
{/capture}
{capture name = 't_set_refresh_rate'}
{$T_CHAT_CHANGE_REFRESHRATE_FORM.javascript}
<form {$T_CHAT_CHANGE_REFRESHRATE_FORM.attributes}>
  <!--<span id="currValue">{$smarty.const._CHAT_CURRENT_REFRESH_RATE} {$T_CHAT_CURRENT_REFRESH_RATE} {$smarty.const._CHAT_SECONDS}</span>-->
        <table class="formElements">
  <tr>
   <td class="labelCell">{$smarty.const._CHAT_RATE}:&nbsp;</td>
   <td class="elementCell">{$T_CHAT_CHANGE_REFRESHRATE_FORM.rate2.html}</td>
  </tr>
  <tr>
   <td></td>
   <td class="submitCell">{$T_CHAT_CHANGE_REFRESHRATE_FORM.submit.html}<span class="caution">{$smarty.const._CHAT_CAUTION_NOTCHANGE}</span></td>
  </tr>
 </table>
</form>
{/capture}
{capture name = 't_create_log'}
  {$T_CHAT_CREATE_LOG_FORM.javascript}

<form {$T_CHAT_CREATE_LOG_FORM.attributes}>
        <table class="formElements">
  <tr>
   <td>From Date:</td>
   <td class="elementCell">{$T_CHAT_CREATE_LOG_FORM.from.html}</td>
  </tr>
  <tr>
   <td>Until Date:</td>
   <td class="elementCell">{$T_CHAT_CREATE_LOG_FORM.until.html}</td>
  </tr>
  <tr>
   <td></td>
   <td class="elementCell">{$T_CHAT_CREATE_LOG_FORM.lesson.html}</td>
  </tr>
  <tr>
   <td></td>
   <td class="submitCell">{$T_CHAT_CREATE_LOG_FORM.submit.html}</td>
  </tr>
 </table>
</form>
{/capture}
{capture name = 't_clear_logs'}
 <table class="sortedTable" width="100%">
 <tr>
  <td class = "topTitle">
   {$smarty.const._CHAT_TITLE}
  </td>
  <td class = "topTitle">
   {$smarty.const._CHAT_DESCRIPTION}
  </td>
  <td class="topTitle centerAlign noSort">
   {$smarty.const._CHAT_OPERATIONS}
  </td>
 </tr>
 <tr class="oddRowColor">
  <td>
  {$smarty.const._CHAT_CLEAR_HISTORY}
  </td>
  <td>
   {$smarty.const._CHAT_CLEAR_HISTORY_DESCR}
  </td>
  <td class="centerAlign">
   <a href = "#" onclick="javascript:clearU2ULogs(); return false;"><img src="{$T_CHAT_MODULE_BASELINK}img/error_delete.png" alt="deleteImg"/></a>
  </td>
 </tr>
 </table>
{/capture}
{capture name = 't_chat_tab_code'}
<div class="tabber">

{if $smarty.get.setRefresh_rate==1}
  <div class="tabbertab">
  {eF_template_printBlock tabber = "chat_engine_rate" title=$smarty.const._CHAT_ENGINE_RATE data=$smarty.capture.t_set_chatheartbeat image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath = 1}
  </div>
  <div class="tabbertab tabbertabdefault">
  {eF_template_printBlock tabber = "user_list_refresh_rate" title=$smarty.const._CHAT_USERLIST_REFRESH_RATE data=$smarty.capture.t_set_refresh_rate image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath = 1}
  </div>
  <div class="tabbertab">
  {eF_template_printBlock tabber = "create_log" title=$smarty.const._CHAT_CREATE_LOG data=$smarty.capture.t_create_log image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath = 1}
  </div>
  <div class="tabbertab">
  {eF_template_printBlock tabber = "clear_logs" title=$smarty.const._CHAT_CLEAR_HISTORY data=$smarty.capture.t_clear_logs image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath = 1}
  </div>
{else}
  <div class="tabbertab">
  {eF_template_printBlock tabber = "chat_engine_rate" title=$smarty.const._CHAT_ENGINE_RATE data=$smarty.capture.t_set_chatheartbeat image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath = 1}
  </div>
  <div class="tabbertab">
  {eF_template_printBlock tabber = "user_list_refresh_rate" title=$smarty.const._CHAT_USERLIST_REFRESH_RATE data=$smarty.capture.t_set_refresh_rate image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath = 1}
  </div>
  <div class="tabbertab">
  {eF_template_printBlock tabber = "create_log" title=$smarty.const._CHAT_CREATE_LOG data=$smarty.capture.t_create_log image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath = 1}
  </div>
  <div class="tabbertab">
  {eF_template_printBlock tabber = "clear_logs" title=$smarty.const._CHAT_CLEAR_HISTORY data=$smarty.capture.t_clear_logs image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath = 1}
  </div>
{/if}

</div>

{/capture}
{eF_template_printBlock title=$smarty.const._CHAT_CHAT data=$smarty.capture.t_chat_tab_code image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath=1}
