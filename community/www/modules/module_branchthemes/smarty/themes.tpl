<script type="text/javascript">
{literal}
function setBrowser(el,themeId,browser)
{
 var num=1;
 while(tp=document.getElementById('pin_'+browser+'_'+num++))
  moduleBranchThemesActivatePin(tp,false);
 {/literal}
 var url='{$T_BRANCH_THEME_MODULE_BASEURL}';
 var branchId={$T_BRANCHTHEMES_BRANCHID};
 {literal}
 parameters={themeId:themeId,branchId:branchId,browser:browser,method:'get'};
 ajaxRequest(el,url,parameters,moduleBranchThemesOnActivateNode);
}
function moduleBranchThemesOnActivateNode(el,response)
{
 moduleBranchThemesActivatePin(el,response==1);
}
function moduleBranchThemesActivatePin(el,flag)
{
 {/literal}
 var title=flag?'{$smarty.const._DEFAULTTHEME}':'{$smarty.const._USETHEME}';
 {literal}
 var imgName=flag?'green':'red';
 setImageSrc(el,16,"pin_"+imgName+".png");
 el.title=title;
 el.alt=title;
}
{/literal}
</script>
<table style="width: 100%" class="sortedTable" id="themesTable">
 <tr class="defaultRowHeight">
  <td class="topTitle">{$smarty.const._NAME}</td>
  <td class="topTitle">{$smarty.const._AUTHOR}</td>
  <td class="topTitle">{$smarty.const._VERSION}</td>
  <td class="topTitle centerAlign noSort">{$smarty.const._DEFAULTTHEME}</td>
  {foreach name='browsers_list' item="browser" key="key" from=$T_BROWSERS}
   <td class="topTitle centerAlign noSort"><img src="images/file_types/{$key}.png" alt="{$browser}" title="{$browser}"></td>
  {/foreach}
  <td class="topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
 </tr>
 {foreach name='users_list' key='key' item='theme' from=$T_THEMES}
  <tr class="{cycle name="themes" values="oddRowColor, evenRowColor"}">
   <td>{$theme.name}</td>
   <td>{$theme.author}</td>
   <td>{$theme.version}</td>
   <td class="centerAlign currentTheme">
    {if $theme.id==$T_BRANCHTHEMES_BRANCHTHEMEINFO.default}
     <img id='pin_default_{$key}' class="ajaxHandle default" src="images/16x16/pin_green.png" alt="{$smarty.const._DEFAULTTHEME}" title="{$smarty.const._DEFAULTTHEME}" onclick="setBrowser(this,'{$theme.id}','default')">
    {else}
     <img class="ajaxHandle default" id='pin_default_{$key}' src="images/16x16/pin_red.png" alt="{$smarty.const._USETHEME}" title="{$smarty.const._USETHEME}" onclick="setBrowser(this,'{$theme.id}','default')">
    {/if}
   </td>
   {foreach name='browsers_list' item="browserName" key="browserId" from=$T_BROWSERS}
    <td class="centerAlign {$browserId}">
     {if $T_BRANCHTHEMES_BRANCHTHEMEINFO[$browserId]==$theme.id}
      <img class="ajaxHandle browser_{$browserId}" id='pin_{$browserId}_{$key}' src="images/16x16/pin_green.png" alt="{$smarty.const._DEFAULTTHEME}" title="{$smarty.const._DEFAULTTHEME}" alttitle="{$smarty.const._USETHEMEBROWSER}" onclick="setBrowser(this,'{$theme.id}','{$browserId}')">
     {else}
      <img class="ajaxHandle browser_{$browserId}" id='pin_{$browserId}_{$key}' src="images/16x16/pin_red.png" alt="{$smarty.const._USETHEME}" title="{$smarty.const._USETHEME}" alttitle="{$smarty.const._ACTIVETHEMEBROWSER}" onclick="setBrowser(this,'{$theme.id}','{$browserId}')">
     {/if}
    </td>
   {/foreach}
   <td class="centerAlign">
    <img class="ajaxHandle" src="images/16x16/search.png" title="{$smarty.const._PREVIEW}" alt="{$smarty.const._PREVIEW}" onclick= "window.open('index.php?preview_theme={$theme.id}', 'preview_theme')" />
   </td>
  </tr>
 {/foreach}
</table>
