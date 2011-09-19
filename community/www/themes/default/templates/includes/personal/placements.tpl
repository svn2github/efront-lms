{if $smarty.get.add_placement || $smarty.get.edit_placement}
 {capture name = 't_employee_jobs_form'}
  {eF_template_printForm form = $T_FORM}
 {/capture}
 {eF_template_printBlock title = $smarty.const._PLACEMENT data = $smarty.capture.t_employee_jobs_form image = '32x32/profile.png'}

 {if $T_MESSAGE_TYPE == 'success'}
    <script>parent.location = '{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=placements';</script>
 {/if}
{else}
 {capture name = 't_employee_jobs'}
  {if $_change_placements_}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWJOBPLACEMENT}" title = "{$smarty.const._NEWJOBPLACEMENT}"/>
     <a href="{$smarty.server.REQUEST_URI}&add_placement=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWJOBPLACEMENT}', 2)">{$smarty.const._NEWJOBPLACEMENT}</a>
    </span>
   </div>
  {/if}

  <table width = "100%" class = "sortedTable">
   <tr class = "topTitle">
    <td class = "topTitle">{$smarty.const._BRANCHNAME}</td>
    <td class = "topTitle">{$smarty.const._JOBDESCRIPTION}</td>
    <td class = "topTitle">{$smarty.const._EMPLOYEEPOSITION}</td>
  {if $_change_placements_}
    <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
  {/if}
   </tr>
   {foreach name = 'users_list' key = 'key' item = 'placement' from = $T_PLACEMENTS}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
    <td>
    {if $smarty.session.s_type == 'administrator' || in_array($placement.branch_ID, $T_SUPERVISES_BRANCHES)}
     <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=branches&edit_branch={$placement.branch_ID}" class = "editLink">{$T_BRANCHES_PATH[$placement.branch_ID]}</a>
    {else}
     {$T_BRANCHES_PATH[$placement.branch_ID]}
    {/if}
    </td>
    <td>
    {if $smarty.session.s_type == 'administrator' || in_array($placement.branch_ID, $T_SUPERVISES_BRANCHES)}
     <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions&edit_job_description={$placement.job_description_ID}" class = "editLink">{$placement.description}</a>
    {else}
     {$placement.description}
    {/if}
     </td>
    <td>{if $placement.supervisor == 0}{$smarty.const._EMPLOYEE}{else}{$smarty.const._SUPERVISOR}{/if}</td>
  {if $_change_placements_}
    <td class = "centerAlign">
    {if $smarty.session.s_type == 'administrator' || in_array($placement.branch_ID, $T_SUPERVISES_BRANCHES)}
     <a href = "{$smarty.server.REQUEST_URI}&edit_placement={$placement.job_description_ID}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._EDITJOBPLACEMENT}', 2)"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
     <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "deleteJob(this, '{$placement.job_description_ID}')"/>
    {/if}
    </td>
  {/if}
   </tr>
   {foreachelse}
   <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
  </table>
 {/capture}
 {eF_template_printBlock title = $smarty.const._PLACEMENTS data = $smarty.capture.t_employee_jobs image = '32x32/profile.png'}
{/if}
