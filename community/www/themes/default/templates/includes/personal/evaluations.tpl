{if (isset($smarty.get.add_evaluation) || isset($smarty.get.edit_evaluation))}
 {capture name = 't_evaluations_code'}
 {eF_template_printForm form = $T_EVALUATIONS_FORM}
 {if $T_MESSAGE_TYPE == 'success'}
    <script>parent.location = '{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=evaluations';</script>
 {/if}

 {/capture}
 {eF_template_printBlock title = $smarty.const._EVALUATION data = $smarty.capture.t_evaluations_code image = '32x32/generic.png'}
{else}
 {capture name = 't_evaluations_code'}
  {if $_change_evaluations_}
  <div class = "headerTools">
   <span>
    <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWEVALUATION}" title = "{$smarty.const._NEWEVALUATION}">
    <a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=evaluations&add_evaluation=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', 1)">{$smarty.const._NEWEVALUATION}</a>
   </span>
  </div>
  {/if}
  <table width = "100%" class = "sortedTable">
   <tr class = "topTitle">
    <td class = "topTitle">{$smarty.const._DATE}</td>
    <td class = "topTitle">{$smarty.const._SUBJECT}</td>
    <td class = "topTitle">{$smarty.const._AUTHOR}</td>
   {if $_change_evaluations_}
    <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
   {/if}
   </tr>
   {foreach name = 'users_list' key = 'key' item = 'evaluation' from = $T_EVALUATIONS}
   <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
    <td style = "white-space:nowrap"><span style="display:none">{$evaluation.timestamp}</span>#filter:timestamp_time-{$evaluation.timestamp}#</td>
    <td>{$evaluation.specification}</td>
    <td style = "white-space:nowrap">
     {if $smarty.session.s_type == 'administrator'}
     <a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$evaluation.author}&op=profile" class = "editLink">#filter:login-{$evaluation.author}#</a>
     {else}
     #filter:login-{$evaluation.author}#
     {/if}
    </td>
   {if $_change_evaluations_}
    <td class = "centerAlign">
     <a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=evaluations&edit_evaluation={$evaluation.event_ID}&popup=1" target = "POPUP_FRAME" class = "editLink" onclick = "eF_js_showDivPopup('{$smarty.const._EVALUATION}', 1)"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
     <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEEVALUATION}')) deleteEvaluation(this, '{$evaluation.event_ID}');" />
    </td>
   {/if}
   </tr>
   {foreachelse}
   <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}

  </table>
 {/capture}
 {eF_template_printBlock title = $smarty.const._EVALUATIONS data = $smarty.capture.t_evaluations_code image = '32x32/note.png'}
{/if}
