{capture name = "moduleComments"}
 <tr><td class = "moduleCell">
 {if $smarty.get.add || $smarty.get.edit}
     {capture name = 't_add_code'}
   {$T_ENTITY_FORM.javascript}
   <form {$T_ENTITY_FORM.attributes}>
       {$T_ENTITY_FORM.hidden}
       <table class = "formElements">
     <tr><td></td><td>
      <span>
       <img style="vertical-align:middle" onclick = "toggledInstanceEditor = 'data';javascript:toggleEditor('data','simpleEditor');" class = "handle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
       <a href = "javascript:void(0)" onclick = "toggledInstanceEditor = 'data';javascript:toggleEditor('data','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
      </span>
     </td></tr>
           <tr><td class = "labelCell">{$T_ENTITY_FORM.data.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ENTITY_FORM.data.html}</td></tr>
           <tr><td class = "labelCell">{$T_ENTITY_FORM.private.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ENTITY_FORM.private.html}</td></tr>
           <tr><td></td>
               <td class = "submitCell">{$T_ENTITY_FORM.submit.html}</td></tr>
       </table>
   </form>

  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location=parent.location;</script>
  {/if}

  {/capture}

  {eF_template_printBlock title = $smarty.const._COMMENTPROPERTIES data = $smarty.capture.t_add_code image = '32x32/note.png'}
 {/if}
 </td></tr>
{/capture}
