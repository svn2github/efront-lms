{* smarty template for chat module *}

{if $T_CHAT_MODULE_STATUS == "ON"}



<script type="text/javascript" >
 var modulechatbaselink = '{$T_CHAT_MODULE_BASELINK}';
 var modulechatbasedir = '{$T_CHAT_MODULE_BASEDIR}';
 var modulechatbaseurl = '{$T_CHAT_MODULE_BASEURL}';
 var ie = 0;
 var flashreload = true;
</script>

<link href="{$T_CHAT_MODULE_BASELINK}css/screen.css" rel="stylesheet" type="text/css">
<link href="{$T_CHAT_MODULE_BASELINK}css/chat.css" rel="stylesheet" type="text/css">
<!--[if IE ]>
<link type="text/css" rel="stylesheet" media="all" href="{$T_CHAT_MODULE_BASELINK}css/screen_ie.css" />
<![endif]-->

<div id="chat_module">
 <div id="windowspace">
  <div id="windows"></div>
 </div>
 <div id="chat_bar" onclick="javascript:toggle_users()">

  <div id="user_list" >
   <div id="content" >
    <!-- Online Users displayed here -->
   </div>
  </div>
  <table width="100%" cellpadding="0" cellspacing="0">
  <tr>
  <td id="first" >
   <span id="status" >

   </span>
  </td>
  <td align="right">
  <a href="javascript:void(0)" onClick="javascript:on_off()"><img id="statusimg" src="{$T_CHAT_MODULE_BASELINK}img/onoff18.png"/></a>

  </td>
  </tr>
  </table>
 </div>
</div>


<!--[if IE]>
<bgsound id="sound">
<script>var ie=1;</script>
<![endif]-->
<script type="text/javascript">
 var must_disable_selection = true;
</script>
{literal}
<script type="text/javascript">
try {
 //document.observe("dom:loaded", fix_flash);
 if ($('scormFrameID')) {
  Event.observe($('scormFrameID').contentWindow, 'load', applyFlashFrameFix);
 }
} catch (e) {
 //alert(e);
}
</script>

{/literal}

{/if}
