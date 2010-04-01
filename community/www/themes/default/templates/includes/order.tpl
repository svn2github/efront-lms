
{*moduleUnitOrder: Content tree to change order*}
{capture name = "moduleUnitOrder"}
 <tr><td class = "moduleCell">
        {capture name = "content_tree"}
   {$T_UNIT_ORDER_TREE}
        {/capture}
        {eF_template_printBlock title = $smarty.const._DRAGAUNITTOCHANGEITSPOSITION data = $smarty.capture.content_tree image = "32x32/theory.png" alt = '<span class = "emptyCategory">'|cat:$smarty.const._NOCONTENTFOUND|cat:'</span>' options = $T_TABLE_OPTIONS help = 'Content_tree_management'}
        <input class = "flatButton" type = "button" onclick = "saveTree(this)" value = "{$smarty.const._SAVECHANGES}" />
        <input class = "flatButton" type = "button" onclick = "window.location.reload()" value = "{$smarty.const._UNDOCHANGES}" id = "reload_button"/>
 </td></tr>
{/capture}
