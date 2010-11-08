
  <table>
   <tr>
    <td>
     <img src={$MOD_JOBS_MANAGER_BASELINK|cat:'images/add.png'} alt="{$smarty.const._JOBS_MANAGER_ADD_JOB}" title="{$smarty.const._JOBS_MANAGER_ADD_JOB}" style="vertical-align:middle">
     <a href="{$MOD_JOBS_MANAGER_BASEURL}&action=new_job">{$smarty.const._JOBS_MANAGER_ADD_JOB}</a>
    </td>
   </tr>
  </table>
  <table class="sortedTable" style="width:100%;">
   <tr>
    <td class="topTitle" style="width:120px;">{$smarty.const._JOBS_MANAGER_JOB_CODE}</td>
    <td class="topTitle">{$smarty.const._JOBS_MANAGER_JOB_TITLE}</td>
    <td class="topTitle centerAlign" style="width:120px;">{$smarty.const._JOBS_MANAGER_JOB_CREATED_ON}</td>
    <td class="topTitle centerAlign" style="width:120px;">{$smarty.const._JOBS_MANAGER_JOB_STATUS}</td>
    <td class="topTitle centerAlign" style="width:120px;">{$smarty.const._JOBS_MANAGER_JOB_APP_COUNT}</td>
    <td class="topTitle centerAlign noSort" style="width:120px;">{$smarty.const._JOBS_MANAGER_JOB_TOOLS}</td>
   </tr>
   {foreach name='jobs_loop' key='job_id' item="job" from=$_JOB_MANAGER_JOBS}
    <tr id="row_{$job.id}" class="{cycle values = "oddRowColor, evenRowColor"}">
     <td><a href="{$MOD_JOBS_MANAGER_BASEURL}&action=show_job&job_id={$job.id}">{$job.code}</a></td>
     <td><a href="{$MOD_JOBS_MANAGER_BASEURL}&action=show_job&job_id={$job.id}">{$job.title}</a></td>
     <td class="centerAlign"><a href="{$MOD_JOBS_MANAGER_BASEURL}&action=show_job&job_id={$job.id}">{$job.date_added|date_format:"%a, %d %b %Y"}</a></td>
     <td class="centerAlign">
      {if $job.active}
       <img src="images/16x16/trafficlight_green.png" style="width:16px;cursor:pointer;" onclick="javascript:toggleJob('{$job.id}');" id="status_image_{$job_id}">
      {else}
       <img src="images/16x16/trafficlight_red.png" style="width:16px;cursor:pointer;" onclick="javascript:toggleJob('{$job.id}');" id="status_image_{$job_id}">
      {/if}
     </td>
     <td class="centerAlign"><a href="{$MOD_JOBS_MANAGER_BASEURL}&action=show_job&job_id={$job.id}">{$job.app_count}</a></td>
     <td class="centerAlign">
      <a href="{$MOD_JOBS_MANAGER_BASEURL}&action=edit_job&job_id={$job.id}"><img src="{$MOD_JOBS_MANAGER_BASELINK}images/milky_pencil.png" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}" border="0"></a>
      <a href="javascript:void(0);" onclick="if(confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) window.location='{$MOD_JOBS_MANAGER_BASEURL}&action=remove_job&job_id={$job.id}';"><img src="{$MOD_JOBS_MANAGER_BASELINK}images/milky_delete.png" alt="{$smarty.const._DELETE}" title="{$smarty.const._DELETE}" border="0"></a>
     </td>
    </tr>
   {foreachelse}
    <tr class="defaultRowHeight oddRowColor">
     <td class="emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td>
    </tr>
   {/foreach}
  </table>
