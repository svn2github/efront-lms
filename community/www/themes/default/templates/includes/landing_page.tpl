   {*Inner table modules *}
 {foreach name = 'module_inner_tables_list' key = key item = moduleItem from = $T_INNERTABLE_MODULE}
     {capture name = "moduleLandingPage"}
   <tr><td class = "moduleCell">
    {if $moduleItem.smarty_file}
     {include file = $moduleItem.smarty_file}
    {else}
     {$moduleItem.html_code}
    {/if}
   </td></tr>
   {/capture}
 {/foreach}
