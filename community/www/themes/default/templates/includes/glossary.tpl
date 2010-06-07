<script>
var tabberLoadingConst = "{$smarty.const._LOADINGDATA}";
</script>

{capture name = "moduleGlossary"}
 <tr><td class = "moduleCell">
  {* Format T_GLOSSARY into html code *}

  {if $smarty.get.add || $smarty.get.edit}
  {capture name='t_glossary_add_code'}
   {if $T_MESSAGE_TYPE == 'success' && !$smarty.post.submit_term_add_another}
      <script>
          re = /\?/;
          !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
      </script>
   {/if}
   {$T_ENTITY_FORM.javascript}
   <form {$T_ENTITY_FORM.attributes}>
       {$T_ENTITY_FORM.hidden}
       <table class = "formElements">
           <tr><td class = "labelCell">{$T_ENTITY_FORM.name.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ENTITY_FORM.name.html}</td></tr>
           {if $T_ENTITY_FORM.name.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.name.error}</td></tr>{/if}
     <tr><td></td><td><span>
       <img style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
       <a href = "javascript:toggleEditor('info','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
      </span></td></tr>
           <tr><td class = "labelCell">{$T_ENTITY_FORM.info.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ENTITY_FORM.info.html}</td></tr>
           {if $T_ENTITY_FORM.info.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.info.error}</td></tr>{/if}
           <tr><td></td>
            <td class = "submitCell">{$T_ENTITY_FORM.submit.html} {if $smarty.get.add}{$T_ENTITY_FORM.submit_term_add_another.html}{/if}</td></tr>
       </table>
   </form>
  {/capture}
  {eF_template_printBlock title = $smarty.const._GLOSSARY data = $smarty.capture.t_glossary_add_code image = '32x32/glossary.png' help = 'Glossary'}
 {else}
  {capture name='t_glossary_code'}
   {if !$_student_ && $_change_}
             <div class = "headerTools">
                 <img src = "images/16x16/add.png" title = "{$smarty.const._ANNOUNCEMENTADD}" alt = "{$smarty.const._ANNOUNCEMENTADD}"/>
                 <a href = "{$smarty.server.PHP_SELF}?ctg=glossary&add=1&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._ADDDEFINITION}', 1)" title = "{$smarty.const._ADDDEFINITION}" target = "POPUP_FRAME">{$smarty.const._ADDDEFINITION}</a>
             </div>
            {/if}

   {counter start = 0 assign = "count"}

            {if $smarty.foreach.glossary_list.first}
    <div class = "tabber" >
            {/if}
            {foreach name = 'glossary_list' item = "item" key = "key" from = $T_GLOSSARY}

             <div class = "tabbertab {if $smarty.get.tab|@mb_strtolower == $key|@mb_strtolower}tabbertabdefault{/if} useAjax" id = "tabbertab{$count}" title="{$key}">
<!--tabberajax:tabbertab{$count}-->

{if $count == $smarty.get.tabberajax}
 {capture name = "t_term_code"}
  <table class = "glossary" id = "tabbertab_table{$count}">
    {if $key == '0-9' || $key == 'Symbols'}
     {foreach name = 'symbols_list' item = 'inner_item' key = "inner_key" from = $item}
                        <tr class = "defaultRowHeight">
                         <td colspan = "3" class = "boldFont">{$inner_key|@htmlentities} :</td></tr>
                        <tr class = "defaultRowHeight">
                         <td class = "topTitle" style="width:15%">{$smarty.const._TERM}</td>
                         <td class = "topTitle">{$smarty.const._EXPLANATION}</td>
                        {if !$_student_ && $_change_}
       <td class = "centerAlign topTitle">{$smarty.const._OPERATIONS}</td></tr>
      {/if}
      {foreach name = 'terms_list' item = 'term' key = "notused" from = $inner_item}
                        <tr class = "defaultRowHeight {cycle values = "oddRowColor,evenRowColor"}">
                            <td class = "boldFont">{$term.name}</td>
                            <td>{$term.info}</td>
       {if !$_student_ && $_change_}
                            <td class = "centerAlign" class = "nowrap">
                               <a href = "{$smarty.server.PHP_SELF}?ctg=glossary&edit={$term.id}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDITDEFINITION}', 1)" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITDEFINITION}" title = "{$smarty.const._EDITDEFINITION}" /></a>
                               <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEntity(this, '{$term.id}');"/>
                            </td>
       {/if}
      </tr>
      {/foreach}
     {/foreach}
    {else}
                        <tr class = "defaultRowHeight">
                         <td class = "topTitle" style="width:15%">{$smarty.const._TERM}</td>
                         <td class = "topTitle">{$smarty.const._EXPLANATION}</td>
                        {if !$_student_ && $_change_}
       <td class = "centerAlign topTitle">{$smarty.const._OPERATIONS}</td></tr>
      {/if}
      {foreach name = 'terms_list' item = 'term' key = "notused" from = $item}
                        <tr class = "defaultRowHeight {cycle values = "oddRowColor,evenRowColor"}">
                            <td class = "boldFont">{$term.name}</td>
                            <td>{$term.info}</td>
       {if !$_student_ && $_change_}
                            <td class = "centerAlign" class = "nowrap">
                               <a href = "{$smarty.server.PHP_SELF}?ctg=glossary&edit={$term.id}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDITDEFINITION}', 1)" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITDEFINITION}" title = "{$smarty.const._EDITDEFINITION}" /></a>
                               <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEntity(this, '{$term.id}');"/>
                            </td>
       {/if}
      </tr>
      {/foreach}
    {/if}
     </table>
 {/capture}
 {eF_template_printBlock title=$key data=$smarty.capture.t_term_code image='32x32/glossary.png'}

{/if}

<!--/tabberajax:tabbertab{$count}-->
             </div>
             {counter}

            {foreachelse}
            <table class = "glossary"><tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr></table>
            {/foreach}
  {/capture}
             {if $smarty.foreach.glossary_list.first}
             </div>
             {/if}
  {eF_template_printBlock title = $smarty.const._GLOSSARY data = $smarty.capture.t_glossary_code image = '32x32/glossary.png' help = 'Glossary'}
  {* Hidden used for the add info popup to define whether this page should be reloaded on popup close or not *}
  <input type = "hidden" id = "reloadHidden" value = "" />
 {/if}

 </td></tr>
{/capture}
