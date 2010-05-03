<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
    <meta http-equiv = "Content-Type" content = "text/html; charset = utf-8"/>
    <link rel="shortcut icon" href="{$T_FAVICON}">
</head>
<script>
var global_sideframe_width = '{$T_SIDEFRAME_WIDTH}';
</script>

{if $T_SIDEBAR_MODE == 0}
 <frameset framespacing = "0" frameborder = "0" border="no" id = "framesetId" cols = "{$T_SIDEFRAME_WIDTH}, *">
    {if isset($T_SIDEBAR_URL)}
     <frame name = "sideframe" src ="{$T_SIDEBAR_URL}" scrolling="no"/>
    {else}
     <frame name = "sideframe" src ="new_sidebar.php" scrolling="no"/>
    {/if}

  {if isset($T_MAIN_URL)}
   <frame name = "mainframe" src ="{$T_MAIN_URL}"/>
  {else}
   {if $smarty.get.message && $smarty.get.message_type}
    <frame name = "mainframe" src ="student.php?message={$smarty.get.message}&message_type={$smarty.get.message_type}"/>
   {else}
    <frame name = "mainframe" src ="student.php"/>
   {/if}
  {/if}
 </frameset>
{else}

 <frameset framespacing = "0" frameborder = "0" border="no" id = "framesetId" cols = "0, *">
    <frame name = "sideframe" src ="" scrolling="no"/>
  {if isset($T_MAIN_URL)}
   <frame name = "mainframe" src ="{$T_MAIN_URL}"/>
  {else}
   {if $smarty.get.message && $smarty.get.message_type}
    <frame name = "mainframe" src ="student.php?message={$smarty.get.message}&message_type={$smarty.get.message_type}"/>
   {else}
    <frame name = "mainframe" src ="student.php"/>
   {/if}
  {/if}
 </frameset>
{/if}
</html>
