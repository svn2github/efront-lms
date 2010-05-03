{if !$smarty.get.popup && !$T_POPUP_MODE}
 <table class = "pageLayout {if isset($T_MAXIMIZE_VIEWPORT)}centerFull hideBoth{else}{$layoutClass}{/if}" id = "pageLayout">
  <tr><td style = "vertical-align:top">
   <table style = "width:100%;">
   {if $smarty.server.PHP_SELF|basename == 'index.php'}
    {if $T_THEME_SETTINGS->options.show_header != 0}
     <tr><td class = "header" colspan = "3">{include file = "includes/header_code.tpl"}</td></tr>
    {/if}
   {elseif $T_THEME_SETTINGS->options.show_header == 2}
    <tr><td id ="horizontalBarRow" class = "{if isset($T_HEADER_CLASS)}{$T_HEADER_CLASS}{else}header{/if}" colspan = "3">{include file = "includes/header_code.tpl"}</td></tr>
    {include file = "includes/horizontal_sidebar.tpl"}
   {else}
    <tr><td class = "topTitle defaultRowHeight" colspan = "3">
     {$smarty.capture.t_path_additional_code}
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
