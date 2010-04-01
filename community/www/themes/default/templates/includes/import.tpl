{capture name = 'moduleImport'}
 <tr><td class = "moduleCell">
 {capture name = "t_import_code"}
    {$T_ENTITY_FORM.javascript}
    <form {$T_ENTITY_FORM.attributes}>
        {$T_ENTITY_FORM.hidden}
  <table class = "formElements">
   <tr><td class = "labelCell">{$T_ENTITY_FORM.folders_to_hierarchy.label}:&nbsp;</td>
    <td class = "elementCell">{$T_ENTITY_FORM.folders_to_hierarchy.html}</td></tr>
   <tr><td class = "labelCell">{$T_ENTITY_FORM.uncompress_recursive.label}:&nbsp;</td>
    <td class = "elementCell">{$T_ENTITY_FORM.uncompress_recursive.html}</td></tr>

{* <tr><td class = "labelCell">{$T_ENTITY_FORM.import_type.label}:&nbsp;</td>
    <td class = "elementCell">{$T_ENTITY_FORM.import_type.html}</td></tr>
   <tr><td class = "labelCell">{$T_ENTITY_FORM.import_method.label}:&nbsp;</td>
    <td class = "elementCell">{$T_ENTITY_FORM.import_method.html}</td></tr>*}
   <tr><td class = "labelCell">{$T_ENTITY_FORM.import_file[0].label}:&nbsp;</td>
    <td class = "elementCell">{$T_ENTITY_FORM.import_file[0].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "addBox(this)"></td></tr>
  {foreach name = 'file_upload_list' item = "item" key = "key" from = $T_ENTITY_FORM.import_file}
   {if $key > 0}
   <tr style = "display:none"><td class = "labelCell"></td>
    <td class = "elementCell">{$T_ENTITY_FORM.import_file[$key].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "addBox(this)"></td></tr>
   {/if}
  {/foreach}
   <tr><td class = "labelCell">{$T_ENTITY_FORM.import_url[0].label}:&nbsp;</td>
    <td class = "elementCell">{$T_ENTITY_FORM.import_url[0].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "addBox(this)"></td></tr>
  {foreach name = 'url_upload_list' item = "item" key = "key" from = $T_ENTITY_FORM.import_url}
   {if $key > 0}
   <tr style = "display:none"><td class = "labelCell"></td>
    <td class = "elementCell">{$T_ENTITY_FORM.import_url[$key].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "addBox(this)"></td></tr>
   {/if}
  {/foreach}
   <tr><td class = "labelCell">{$T_ENTITY_FORM.import_path[0].label}:&nbsp;</td>
    <td class = "elementCell">{$T_ENTITY_FORM.import_path[0].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "addBox(this)"></td></tr>
  {foreach name = 'url_upload_list' item = "item" key = "key" from = $T_ENTITY_FORM.import_path}
   {if $key > 0}
   <tr style = "display:none"><td class = "labelCell"></td>
    <td class = "elementCell">{$T_ENTITY_FORM.import_path[$key].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "addBox(this)"></td></tr>
   {/if}
  {/foreach}
   <tr><td></td>
    <td class = "submitCell">{$T_ENTITY_FORM.import_submit.html}</td></tr>
  </table>
 {/capture}
 {eF_template_printBlock title=$smarty.const._IMPORT data=$smarty.capture.t_import_code image='32x32/import.png' help = 'Smart_content'}
 </td></tr>
{/capture}
