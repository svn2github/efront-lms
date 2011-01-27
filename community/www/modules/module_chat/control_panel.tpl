{* chat module control panel template *}

<link href="{$T_CHAT_MODULE_BASELINK}css/control_panel.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$T_CHAT_MODULE_BASELINK}js/admin.js"></script>

<script type="text/javascript">
 var modulechatbaselink = '{$T_CHAT_MODULE_BASELINK}';
 var modulechatbasedir = '{$T_CHAT_MODULE_BASEDIR}';
 var modulechatbaseurl = '{$T_CHAT_MODULE_BASEURL}';
</script>
{if !isset($smarty.get.setChatHeartBeat) && !isset($smarty.get.setRefresh_rate) && !isset($smarty.get.createLog)}
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
  <a title = "Create Log File" href = "{$T_CHAT_MODULE_BASEURL}&createLog=1">{$smarty.const._CHAT_CREATE_LOG}</a>
 </td>
 <td>{$smarty.const._CHAT_CREATE_LOG_DESCR}</td>
 <td></td>
</tr>
<tr class = "evenRowColor">
 <td>
  <a href = "#" onclick="javascript:clearU2ULogs(); return false;">{$smarty.const._CHAT_CLEAR_HISTORY}</a>
 </td>
 <td>{$smarty.const._CHAT_CLEAR_HISTORY_DESCR}</td>
 <td><span class="caution">{$smarty.const._CHAT_CAUTION_IRREVERSIBLE}</span></td>
</tr>
<tr class = "oddRowColor">
 <td>
  <a href = "{$T_CHAT_MODULE_BASEURL}&setChatHeartBeat=1">{$smarty.const._CHAT_CHANGE_MSG_FREQUENCY}</a>
 </td>
 <td>{$smarty.const._CHAT_CHANGE_MSG_FREQUENCY_DESCR}</td>
 <td><span class="caution">{$smarty.const._CHAT_CAUTION_NOTCHANGE}</span></td>
</tr>
<tr class = "evenRowColor">
 <td>
  <a href = "{$T_CHAT_MODULE_BASEURL}&setRefresh_rate=1">{$smarty.const._CHAT_USERLIST_REFRESHRATE}</a>
 </td>
 <td>{$smarty.const._CHAT_USERLIST_REFRESHRATE_DESCR}</td>
 <td><span class="caution">{$smarty.const._CHAT_CAUTION_NOTCHANGE}</span></td>
</tr>
</table>

{/strip}
{/capture}

 {eF_template_printBlock title=$smarty.const._CHAT_CHAT data=$smarty.capture.t_chat_code image= $T_RSS_MODULE_BASELINK|cat:'img/chat.png' absoluteImagePath = 1 link = $T_CHAT_MODULE_BASEURL}
{/if}

<!----------------------------SET CHAT HEARTBEAT------------------------------------------------>
{if isset($smarty.get.setChatHeartBeat)}
  {capture name = 't_set_chatheartbeat'}
  {$T_CHAT_CHANGE_CHATHEARTBEAT_FORM.javascript}
<form {$T_CHAT_CHANGE_CHATHEARTBEAT_FORM.attributes}>
  <span id="currValue">{$smarty.const._CHAT_CURRENT_CHAT_ENGINE_RATE} {$T_CHAT_CURRENT_RATE} {$smarty.const._CHAT_SECONDS}</span>
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

{capture name = 't_chat_tab_code'}
<div class="tabber">
 <div class="tabbertab">

         <h3>{$smarty.const._CHAT_ENGINE_RATE}</h3>
  {eF_template_printBlock title=$smarty.const._CHAT_CHANGE_CHATHEARTBEAT data=$smarty.capture.t_set_chatheartbeat image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath=1}

 </div>
</div>
{/capture}
{eF_template_printBlock title=$smarty.const._CHAT_CHAT data=$smarty.capture.t_chat_tab_code image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath=1}
{/if}

<!---------------------------------------------------------------------------------------------------------------------------->
<!----------------------------SET USER LIST REFRESH RATE------------------------------------------------>
{if isset($smarty.get.setRefresh_rate)}
  {capture name = 't_set_refresh_rate'}
  {$T_CHAT_CHANGE_REFRESHRATE_FORM.javascript}
<form {$T_CHAT_CHANGE_REFRESHRATE_FORM.attributes}>
  <span id="currValue">{$smarty.const._CHAT_CURRENT_REFRESH_RATE} is {$T_CHAT_CURRENT_RATE} {$smarty.const._CHAT_SECONDS}</span>
        <table class="formElements">
  <tr>
   <td class="labelCell">{$smarty.const._CHAT_RATE}:&nbsp;</td>
   <td class="elementCell">{$T_CHAT_CHANGE_REFRESHRATE_FORM.rate.html}</td>
  </tr>
  <tr>
   <td></td>
   <td class="submitCell">{$T_CHAT_CHANGE_REFRESHRATE_FORM.submit.html}<span class="caution">{$smarty.const._CHAT_CAUTION_NOTCHANGE}</span></td>
  </tr>
 </table>
</form>
{/capture}

{capture name = 't_chat_tab_code'}
<div class="tabber">
 <div class="tabbertab">

         <h3>{$smarty.const._CHAT_USERLIST_REFRESH_RATE}</h3>
  {eF_template_printBlock title=$smarty.const._CHAT_CHANGE_REFRESHRATE data=$smarty.capture.t_set_refresh_rate image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath=1}

 </div>
</div>
{/capture}
{eF_template_printBlock title=$smarty.const._CHAT_CHAT data=$smarty.capture.t_chat_tab_code image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath=1}
{/if}
<!---------------------------------------------------------------------------------------------------------------------------->
<!----------------------------CREATE LOG FILE------------------------------------------------>
{if isset($smarty.get.createLog)}
  {capture name = 't_create_log'}
  {$T_CHAT_CREATE_LOG_FORM.javascript}
  {if isset($smarty.post.lesson)}
   {$T_CHAT_TEST}
  {/if}
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

{capture name = 't_chat_tab_code'}
<div class="tabber">
 <div class="tabbertab">

         <h3>{$smarty.const._CHAT_CREATE_LOG}</h3>
  {eF_template_printBlock title=$smarty.const._CHAT_LESSONS_CATALOGUE data=$smarty.capture.t_create_log image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath=1}

 </div>
</div>
{/capture}
{eF_template_printBlock title=$smarty.const._CHAT_CHAT data=$smarty.capture.t_chat_tab_code image=$T_CHAT_BASELINK|cat:'img/chat.png' absoluteImagePath=1}
{/if}
<!---------------------------------------------------------------------------------------------------------------------------->
