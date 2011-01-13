{* smarty template for chat module *}





<script type="text/javascript">
 var modulechatbaselink = '{$T_CHAT_MODULE_BASELINK}';
 var modulechatbasedir = '{$T_CHAT_MODULE_BASEDIR}';
 var modulechatbaseurl = '{$T_CHAT_MODULE_BASEURL}';
</script>
<link href="{$T_CHAT_MODULE_BASELINK}css/screen.css" rel="stylesheet" type="text/css">
<link href="{$T_CHAT_MODULE_BASELINK}css/chat.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$T_CHAT_MODULE_BASELINK}js/jquery.js"></script>
<script type="text/javascript" src="{$T_CHAT_MODULE_BASELINK}js/chat.js"></script>


<div id="chat_bar" class="chat_bar_open" onclick="javascript:toggle_users()">

 <div id="user_list" >
  <div id="content" >
   <!-- Online Users displayed here -->
  </div>
 </div>
 <table width="100%">
 <tr>
 <td id="first">
 <span id="status" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
 </td>
 <td align>
 <a href="javascript:void(0)" onClick="javascript:on_off()"><img id="statusimg" src="{$T_CHAT_MODULE_BASELINK}img/onoff18.png"/></a>

 </td>
 </tr>
 </table>
</div>


<script type="text/javascript">
 disableSelection(document.getElementById("chat_bar"))
</script>
