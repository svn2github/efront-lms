{* chat module control panel template *}

<link href="{$T_CHAT_MODULE_BASELINK}css/control_panel.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$T_CHAT_MODULE_BASELINK}js/admin.js"></script>

<script type="text/javascript">
 var modulechatbaselink = '{$T_CHAT_MODULE_BASELINK}';
 var modulechatbasedir = '{$T_CHAT_MODULE_BASEDIR}';
 var modulechatbaseurl = '{$T_CHAT_MODULE_BASEURL}';
</script>

<h1>Chat Module Administration Page</h1>


<table border="1px" align="center">
 <td>To clear user-to-user chat history press here:<br /><span class="caution">CAUTION! This action cannot be changed afterwards!</span></td>
 <td>
  <input type="submit" value="Clear Chat Logs" class="submit" onclick="javascript:clearU2ULogs()"/>
 </td>
</tr>
<tr>
 <td>To create lesson chat history file press here:</td>
 <td>
  <input type="submit" value="Create Log File" class="submit" onclick="javascript:createLogs()"/>
 </td>
</tr>
<tr>
 <td>Change Message Search Frequency<br /><span class="caution">CAUTION! If you are not sure do NOT change</span></td>
 <td>
  <input type="submit" value=" Set Frequency" class="submit" onclick="javascript:setChatheartBeat()"/>
 </td>
</tr>
<tr>
 <td>Change User List Refresh Rate<br /><span class="caution">CAUTION! If you are not sure do NOT change</span></td>
 <td>
  <input type="submit" value="Set Refresh rate" class="submit" onclick="javascript:setRefresh_rate()"/>
 </td>
</tr>
</table>
